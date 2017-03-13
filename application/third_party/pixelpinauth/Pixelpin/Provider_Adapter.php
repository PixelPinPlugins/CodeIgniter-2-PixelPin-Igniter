<?php
/*!
* PixelpinAuth
* http://pixelpinauth.sourceforge.net | http://github.com/pixelpinauth/pixelpinauth
* (c) 2009-2012, PixelpinAuth authors | http://pixelpinauth.sourceforge.net/licenses.html
*/

/**
 * Pixelpin_Provider_Adapter is the basic class which Pixelpin_Auth will use
 * to connect users to a given provider. 
 * 
 * Basically Pixelpin_Provider_Adapterwill create a bridge from your php 
 * application to the provider api.
 * 
 * Pixelpin_Auth will automatically load Pixelpin_Provider_Adapter and create
 * an instance of it for each authenticated provider.
 */
class Pixelpin_Provider_Adapter
{
	/* Provider ID (or unique name) */
	public $id       = NULL ;

	/* Provider adapter specific config */
	public $config   = NULL ;

	/* Provider adapter extra parameters */
	public $params   = NULL ; 

	/* Provider adapter wrapper path */
	public $wrapper  = NULL ;

	/* Provider adapter instance */
	public $adapter  = NULL ;

	// --------------------------------------------------------------------

	/**
	* create a new adapter switch IDp name or ID
	*
	* @param string  $id      The id or name of the IDp
	* @param array   $params  (optional) required parameters by the adapter 
	*/
	function factory( $id, $params = NULL )
	{
		Pixelpin_Logger::info( "Enter Pixelpin_Provider_Adapter::factory( $id )" );

		# init the adapter config and params
		$this->id     = $id;
		$this->params = $params;
		$this->id     = $this->getProviderCiId( $this->id );
		$this->config = $this->getConfigById( $this->id );

		# check the IDp id
		if( ! $this->id ){
			throw new Exception( "No provider ID specified.", 2 ); 
		}

		# check the IDp config
		if( ! $this->config ){
			throw new Exception( "Unknown Provider ID, check your configuration file.", 3 ); 
		}

		# check the IDp adapter is enabled
		if( ! $this->config["enabled"] ){
			throw new Exception( "The provider '{$this->id}' is not enabled.", 3 );
		}

		# include the adapter wrapper
		if( isset( $this->config["wrapper"] ) && is_array( $this->config["wrapper"] ) ){
			require_once $this->config["wrapper"]["path"];

			if( ! class_exists( $this->config["wrapper"]["class"] ) ){
				throw new Exception( "Unable to load the adapter class.", 3 );
			}

			$this->wrapper = $this->config["wrapper"]["class"];
		}
		else{ 
			require_once Pixelpin_Auth::$config["path_providers"] . $this->id . ".php" ;

			$this->wrapper = "Pixelpin_Providers_" . $this->id; 
		}

		# create the adapter instance, and pass the current params and config
		$this->adapter = new $this->wrapper( $this->id, $this->config, $this->params );

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	* Pixelpin_Provider_Adapter::login(), prepare the user session and the authentication request
	* for index.php
	*/
	function login()
	{
		Pixelpin_Logger::info( "Enter Pixelpin_Provider_Adapter::login( {$this->id} ) " );

		if( ! $this->adapter ){
			throw new Exception( "Pixelpin_Provider_Adapter::login() should not directly used." );
		}

		// clear all unneeded params
		foreach( Pixelpin_Auth::$config["providers"] as $idpid => $params ){
			Pixelpin_Auth::storage()->delete( "pauth_session.{$idpid}.pauth_return_to"    );
			Pixelpin_Auth::storage()->delete( "pauth_session.{$idpid}.pauth_endpoint"     );
			Pixelpin_Auth::storage()->delete( "pauth_session.{$idpid}.id_provider_params" );
		}

		// make a fresh start
		$this->logout();

		# get pixelpinauth base url
		$HYBRID_AUTH_URL_BASE = Pixelpin_Auth::$config["base_url"];

		# we make use of session_id() as storage hash to identify the current user
		# using session_regenerate_id() will be a problem, but ..
		$this->params["pauth_token"] = session_id();

		# set request timestamp
		$this->params["pauth_time"]  = time();

		# for default PixelpinAuth endpoint url pauth_login_start_url
		# 	auth.start  required  the IDp ID
		# 	auth.time   optional  login request timestamp
		$this->params["login_start"] = $HYBRID_AUTH_URL_BASE . ( strpos( $HYBRID_AUTH_URL_BASE, '?' ) ? '&' : '?' ) . "pauth.start={$this->id}&pauth.time={$this->params["pauth_time"]}";

		# for default PixelpinAuth endpoint url pauth_login_done_url
		# 	auth.done   required  the IDp ID
		$this->params["login_done"]  = $HYBRID_AUTH_URL_BASE . ( strpos( $HYBRID_AUTH_URL_BASE, '?' ) ? '&' : '?' ) . "pauth.done={$this->id}";

		Pixelpin_Auth::storage()->set( "pauth_session.{$this->id}.pauth_return_to"    , $this->params["pauth_return_to"] );
		Pixelpin_Auth::storage()->set( "pauth_session.{$this->id}.pauth_endpoint"     , $this->params["login_done"] ); 
		Pixelpin_Auth::storage()->set( "pauth_session.{$this->id}.id_provider_params" , $this->params );

		// store config to be used by the end point 
		Pixelpin_Auth::storage()->config( "CONFIG", Pixelpin_Auth::$config );

		// move on
		Pixelpin_Logger::debug( "Pixelpin_Provider_Adapter::login( {$this->id} ), redirect the user to login_start URL." );

		Pixelpin_Auth::redirect( $this->params["login_start"] );
	}

	// --------------------------------------------------------------------

	/**
	* let pixelpinauth forget all about the user for the current provider
	*/
	function logout()
	{
		$this->adapter->logout();
	}

	// --------------------------------------------------------------------

	/**
	* return true if the user is connected to the current provider
	*/ 
	public function isUserConnected()
	{
		return $this->adapter->isUserConnected();
	}

	// --------------------------------------------------------------------

	/**
	* handle :
	*   getUserProfile()
	*   getUserContacts()
	*   getUserActivity() 
	*   setUserStatus() 
	*/ 
	public function __call( $name, $arguments ) 
	{
		Pixelpin_Logger::info( "Enter Pixelpin_Provider_Adapter::$name(), Provider: {$this->id}" );

		if ( ! $this->isUserConnected() ){
			throw new Exception( "User not connected to the provider {$this->id}.", 7 );
		} 

		if ( ! method_exists( $this->adapter, $name ) ){
			throw new Exception( "Call to undefined function Pixelpin_Providers_{$this->id}::$name()." );
		}

		if( count( $arguments ) ){
			return $this->adapter->$name( $arguments[0] ); 
		} 
		else{
			return $this->adapter->$name(); 
		}
	}

	// --------------------------------------------------------------------

	/**
	* If the user is connected, then return the access_token and access_token_secret
	* if the provider api use oauth
	*/
	public function getAccessToken()
	{
		if( ! $this->adapter->isUserConnected() ){
			Pixelpin_Logger::error( "User not connected to the provider." );

			throw new Exception( "User not connected to the provider.", 7 );
		}

		return
			ARRAY(
				"access_token"        => $this->adapter->token( "access_token" )       , // OAuth access token
				"access_token_secret" => $this->adapter->token( "access_token_secret" ), // OAuth access token secret
				"refresh_token"       => $this->adapter->token( "refresh_token" )      , // OAuth refresh token
				"expires_in"          => $this->adapter->token( "expires_in" )         , // OPTIONAL. The duration in seconds of the access token lifetime
				"expires_at"          => $this->adapter->token( "expires_at" )         , // OPTIONAL. Timestamp when the access_token expire. if not provided by the social api, then it should be calculated: expires_at = now + expires_in
			);
	}

	// --------------------------------------------------------------------

	/**
	* Naive getter of the current connected IDp API client
	*/
	function api()
	{
		if( ! $this->adapter->isUserConnected() ){
			Pixelpin_Logger::error( "User not connected to the provider." );

			throw new Exception( "User not connected to the provider.", 7 );
		}

		return $this->adapter->api;
	}

	// --------------------------------------------------------------------

	/**
	* redirect the user to pauth_return_to (the callback url)
	*/
	function returnToCallbackUrl()
	{ 
		// get the stored callback url
		$callback_url = Pixelpin_Auth::storage()->get( "pauth_session.{$this->id}.pauth_return_to" );

		// remove some unneed'd stored data 
		Pixelpin_Auth::storage()->delete( "pauth_session.{$this->id}.pauth_return_to"    );
		Pixelpin_Auth::storage()->delete( "pauth_session.{$this->id}.pauth_endpoint"     );
		Pixelpin_Auth::storage()->delete( "pauth_session.{$this->id}.id_provider_params" );

		// back to home
		Pixelpin_Auth::redirect( $callback_url );
	}

	// --------------------------------------------------------------------

	/**
	* return the provider config by id
	*/
	function getConfigById( $id )
	{ 
		if( isset( Pixelpin_Auth::$config["providers"][$id] ) ){
			return Pixelpin_Auth::$config["providers"][$id];
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	* return the provider config by id; insensitive
	*/
	function getProviderCiId( $id )
	{
		foreach( Pixelpin_Auth::$config["providers"] as $idpid => $params ){
			if( strtolower( $idpid ) == strtolower( $id ) ){
				return $idpid;
			}
		}

		return NULL;
	}
}
