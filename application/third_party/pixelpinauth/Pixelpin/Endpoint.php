<?php
/*!
* PixelpinAuth
* http://pixelpinauth.sourceforge.net | http://github.com/pixelpinauth/pixelpinauth
* (c) 2009-2012, PixelpinAuth authors | http://pixelpinauth.sourceforge.net/licenses.html
*/

/**
 * Pixelpin_Endpoint class
 * 
 * Pixelpin_Endpoint class provides a simple way to handle the OpenID and OAuth endpoint.
 */
class Pixelpin_Endpoint {
	public static $request = NULL;
	public static $initDone = FALSE;

	/**
	* Process the current request
	*
	* $request - The current request parameters. Leave as NULL to default to use $_REQUEST.
	*/
	public static function process( $request = NULL )
	{
		// Setup request variable
		Pixelpin_Endpoint::$request = $request;

		if ( is_null(Pixelpin_Endpoint::$request) ){
			// Fix a strange behavior when some provider call back ha endpoint
			// with /index.php?pauth.done={provider}?{args}... 
			// >here we need to recreate the $_REQUEST
			if ( strrpos( $_SERVER["QUERY_STRING"], '?' ) ) {
				$_SERVER["QUERY_STRING"] = str_replace( "?", "&", $_SERVER["QUERY_STRING"] );

				parse_str( $_SERVER["QUERY_STRING"], $_REQUEST );
			}

			Pixelpin_Endpoint::$request = $_REQUEST;
		}

		// If openid_policy requested, we return our policy document
		if ( isset( Pixelpin_Endpoint::$request["get"] ) && Pixelpin_Endpoint::$request["get"] == "openid_policy" ) {
			Pixelpin_Endpoint::processOpenidPolicy();
		}

		// If openid_xrds requested, we return our XRDS document
		if ( isset( Pixelpin_Endpoint::$request["get"] ) && Pixelpin_Endpoint::$request["get"] == "openid_xrds" ) {
			Pixelpin_Endpoint::processOpenidXRDS();
		}

		// If we get a pauth.start
		if ( isset( Pixelpin_Endpoint::$request["pauth_start"] ) && Pixelpin_Endpoint::$request["pauth_start"] ) {
			Pixelpin_Endpoint::processAuthStart();
		}
		// Else if pauth.done
		elseif ( isset( Pixelpin_Endpoint::$request["pauth_done"] ) && Pixelpin_Endpoint::$request["pauth_done"] ) {
			Pixelpin_Endpoint::processAuthDone();
		}
		// Else we advertise our XRDS document, something supposed to be done from the Realm URL page
		else {
			Pixelpin_Endpoint::processOpenidRealm();
		}
	}

	/**
	* Process OpenID policy request
	*/
	public static function processOpenidPolicy()
	{
		$output = file_get_contents( dirname(__FILE__) . "/resources/openid_policy.html" ); 
		print $output;
		die();
	}

	/**
	* Process OpenID XRDS request
	*/
	public static function processOpenidXRDS()
	{
		header("Content-Type: application/xrds+xml");

		$output = str_replace
		(
			"{RETURN_TO_URL}",
			str_replace(
				array("<", ">", "\"", "'", "&"), array("&lt;", "&gt;", "&quot;", "&apos;", "&amp;"), 
				Pixelpin_Auth::getCurrentUrl( false )
			),
			file_get_contents( dirname(__FILE__) . "/resources/openid_xrds.xml" )
		);
		print $output;
		die();
	}

	/**
	* Process OpenID realm request
	*/
	public static function processOpenidRealm()
	{
		$output = str_replace
		(
			"{X_XRDS_LOCATION}",
			htmlentities( Pixelpin_Auth::getCurrentUrl( false ), ENT_QUOTES, 'UTF-8' ) . "?get=openid_xrds&v=" . Pixelpin_Auth::$version,
			file_get_contents( dirname(__FILE__) . "/resources/openid_realm.html" )
		); 
		print $output;
		die();
	}

	/**
	* define:endpoint step 3.
	*/
	public static function processAuthStart()
	{
		Pixelpin_Endpoint::authInit();

		$provider_id = trim( strip_tags( Pixelpin_Endpoint::$request["pauth_start"] ) );

		# check if page accessed directly
		if( ! Pixelpin_Auth::storage()->get( "pauth_session.$provider_id.pauth_endpoint" ) ) {
			Pixelpin_Logger::error( "Endpoint: pauth_endpoint parameter is not defined on pauth_start, halt login process!" );

			header( "HTTP/1.0 404 Not Found" );
			die( "You cannot access this page directly." );
		}

		# define:pixelpin.endpoint.php step 2.
		$pauth = Pixelpin_Auth::setup( $provider_id );

		# if REQUESTed pauth_idprovider is wrong, session not created, etc. 
		if( ! $pauth ) {
			Pixelpin_Logger::error( "Endpoint: Invalid parameter on pauth_start!" );

			header( "HTTP/1.0 404 Not Found" );
			die( "Invalid parameter! Please return to the login page and try again." );
		}

		try {
			Pixelpin_Logger::info( "Endpoint: call adapter [{$provider_id}] loginBegin()" );

			$pauth->adapter->loginBegin();
		}
		catch ( Exception $e ) {
			Pixelpin_Logger::error( "Exception:" . $e->getMessage(), $e );
			Pixelpin_Error::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString(), $e );

			$pauth->returnToCallbackUrl();
		}

		die();
	}

	/**
	* define:endpoint step 3.1 and 3.2
	*/
	public static function processAuthDone()
	{
		Pixelpin_Endpoint::authInit();

		$provider_id = trim( strip_tags( Pixelpin_Endpoint::$request["pauth_done"] ) );

		$pauth = Pixelpin_Auth::setup( $provider_id );

		if( ! $pauth ) {
			Pixelpin_Logger::error( "Endpoint: Invalid parameter on pauth_done!" ); 

			$pauth->adapter->setUserUnconnected();

			header("HTTP/1.0 404 Not Found"); 
			die( "Invalid parameter! Please return to the login page and try again." );
		}

		try {
			Pixelpin_Logger::info( "Endpoint: call adapter [{$provider_id}] loginFinish() " );

			$pauth->adapter->loginFinish(); 
		}
		catch( Exception $e ){
			Pixelpin_Logger::error( "Exception:" . $e->getMessage(), $e );
			Pixelpin_Error::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString(), $e );

			$pauth->adapter->setUserUnconnected(); 
		}

		Pixelpin_Logger::info( "Endpoint: job done. retrun to callback url." );

		$pauth->returnToCallbackUrl();
		die();
	}

	public static function authInit()
	{
		if ( ! Pixelpin_Endpoint::$initDone) {
			Pixelpin_Endpoint::$initDone = TRUE;

			# Init Pixelpin_Auth
			try {
				require_once realpath( dirname( __FILE__ ) )  . "/Storage.php";
				
				$storage = new Pixelpin_Storage(); 

				// Check if Pixelpin_Auth session already exist
				if ( ! $storage->config( "CONFIG" ) ) { 
					header( "HTTP/1.0 404 Not Found" );
					die( "You cannot access this page directly." );
				}

				Pixelpin_Auth::initialize( $storage->config( "CONFIG" ) ); 
			}
			catch ( Exception $e ){
				Pixelpin_Logger::error( "Endpoint: Error while trying to init Pixelpin_Auth" ); 

				header( "HTTP/1.0 404 Not Found" );
				die( "Oophs. Error!" );
			}
		}
	}
}
