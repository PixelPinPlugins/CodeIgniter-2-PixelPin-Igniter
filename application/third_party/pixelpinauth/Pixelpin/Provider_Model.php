<?php
/*!
* PixelpinAuth
* http://pixelpinauth.sourceforge.net | http://github.com/pixelpinauth/pixelpinauth
* (c) 2009-2012, PixelpinAuth authors | http://pixelpinauth.sourceforge.net/licenses.html 
*/

/**
 * Pixelpin_Provider_Model provide a common interface for supported IDps on PixelpinAuth.
 *
 * Basically, each provider adapter has to define at least 4 methods:
 *   Pixelpin_Providers_{provider_name}::initialize()
 *   Pixelpin_Providers_{provider_name}::loginBegin()
 *   Pixelpin_Providers_{provider_name}::loginFinish()
 *   Pixelpin_Providers_{provider_name}::getUserProfile()
 *
 * PixelpinAuth also come with three others models
 *   Class Pixelpin_Provider_Model_OpenID for providers that uses the OpenID 1 and 2 protocol.
 *   Class Pixelpin_Provider_Model_OAuth1 for providers that uses the OAuth 1 protocol.
 *   Class Pixelpin_Provider_Model_OAuth2 for providers that uses the OAuth 2 protocol.
 */
abstract class Pixelpin_Provider_Model
{
	/* IDp ID (or unique name) */
	public $providerId = NULL;

	/* specific provider adapter config */
	public $config     = NULL;

   	/* provider extra parameters */
	public $params     = NULL;

	/* Endpoint URL for that provider */
	public $endpoint   = NULL; 

	/* Pixelpin_User obj, represents the current loggedin user */
	public $user       = NULL;

	/* the provider api client (optional) */
	public $api        = NULL; 

	/**
	* common providers adapter constructor
	*/
	function __construct( $providerId, $config, $params = NULL )
	{
		# init the IDp adapter parameters, get them from the cache if possible
		if( ! $params ){
			$this->params = Pixelpin_Auth::storage()->get( "pauth_session.$providerId.id_provider_params" );
		}
		else{
			$this->params = $params;
		}

		// idp id
		$this->providerId = $providerId;

		// set PixelpinAuth endpoint for this provider
		$this->endpoint = Pixelpin_Auth::storage()->get( "pauth_session.$providerId.pauth_endpoint" );

		// idp config
		$this->config = $config;

		// new user instance
		$this->user = new Pixelpin_User();
		$this->user->providerId = $providerId;

		// initialize the current provider adapter
		$this->initialize(); 

		Pixelpin_Logger::debug( "Pixelpin_Provider_Model::__construct( $providerId ) initialized. dump current adapter instance: ", serialize( $this ) );
	}

	// --------------------------------------------------------------------

	/**
	* IDp wrappers initializer
	*
	* The main job of wrappers initializer is to performs (depend on the IDp api client it self): 
	*     - include some libs nedded by this provider,
	*     - check IDp key and secret,
	*     - set some needed parameters (stored in $this->params) by this IDp api client
	*     - create and setup an instance of the IDp api client on $this->api 
	*/
	abstract protected function initialize(); 

	// --------------------------------------------------------------------

	/**
	* begin login 
	*/
	abstract protected function loginBegin();

	// --------------------------------------------------------------------

	/**
	* finish login
	*/
	abstract protected function loginFinish();

	// --------------------------------------------------------------------

   	/**
	* generic logout, just erase current provider adapter stored data to let Pixelpin_Auth all forget about it
	*/
	function logout()
	{
		Pixelpin_Logger::info( "Enter [{$this->providerId}]::logout()" );

		$this->clearTokens();

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	* grab the user profile from the IDp api client
	*/
	function getUserProfile()
	{
		Pixelpin_Logger::error( "PixelpinAuth do not provide users contats list for {$this->providerId} yet." ); 
		
		throw new Exception( "Provider does not support this feature.", 8 ); 
	}

	// --------------------------------------------------------------------

	/**
	* load the current logged in user contacts list from the IDp api client  
	*/
	function getUserContacts() 
	{
		Pixelpin_Logger::error( "PixelpinAuth do not provide users contats list for {$this->providerId} yet." ); 
		
		throw new Exception( "Provider does not support this feature.", 8 ); 
	}

	// --------------------------------------------------------------------

	/**
	* return the user activity stream  
	*/
	function getUserActivity( $stream ) 
	{
		Pixelpin_Logger::error( "PixelpinAuth do not provide user's activity stream for {$this->providerId} yet." ); 
		
		throw new Exception( "Provider does not support this feature.", 8 ); 
	}

	// --------------------------------------------------------------------

	/**
	* return the user activity stream  
	*/ 
	function setUserStatus( $status )
	{
		Pixelpin_Logger::error( "PixelpinAuth do not provide user's activity stream for {$this->providerId} yet." ); 
		
		throw new Exception( "Provider does not support this feature.", 8 ); 
	}

	// --------------------------------------------------------------------

	/**
	* return true if the user is connected to the current provider
	*/ 
	public function isUserConnected()
	{
		return (bool) Pixelpin_Auth::storage()->get( "pauth_session.{$this->providerId}.is_logged_in" );
	}

	// --------------------------------------------------------------------

	/**
	* set user to connected 
	*/ 
	public function setUserConnected()
	{
		Pixelpin_Logger::info( "Enter [{$this->providerId}]::setUserConnected()" );
		
		Pixelpin_Auth::storage()->set( "pauth_session.{$this->providerId}.is_logged_in", 1 );
	}

	// --------------------------------------------------------------------

	/**
	* set user to unconnected 
	*/ 
	public function setUserUnconnected()
	{
		Pixelpin_Logger::info( "Enter [{$this->providerId}]::setUserUnconnected()" );
		
		Pixelpin_Auth::storage()->set( "pauth_session.{$this->providerId}.is_logged_in", 0 ); 
	}

	// --------------------------------------------------------------------

	/**
	* get or set a token 
	*/ 
	public function token( $token, $value = NULL )
	{
		if( $value === NULL ){
			return Pixelpin_Auth::storage()->get( "pauth_session.{$this->providerId}.token.$token" );
		}
		else{
			Pixelpin_Auth::storage()->set( "pauth_session.{$this->providerId}.token.$token", $value );
		}
	}

	// --------------------------------------------------------------------

	/**
	* delete a stored token 
	*/ 
	public function deleteToken( $token )
	{
		Pixelpin_Auth::storage()->delete( "pauth_session.{$this->providerId}.token.$token" );
	}

	// --------------------------------------------------------------------

	/**
	* clear all existen tokens for this provider
	*/ 
	public function clearTokens()
	{ 
		Pixelpin_Auth::storage()->deleteMatch( "pauth_session.{$this->providerId}." );
	}
}
