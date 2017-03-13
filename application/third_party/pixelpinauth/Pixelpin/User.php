<?php
/*!
* PixelpinAuth
* http://pixelpinauth.sourceforge.net | http://github.com/pixelpinauth/pixelpinauth
* (c) 2009-2012, PixelpinAuth authors | http://pixelpinauth.sourceforge.net/licenses.html 
*/

/**
 * The Pixelpin_User class represents the current loggedin user 
 */
class Pixelpin_User 
{
	/* The ID (name) of the connected provider */
	public $providerId = NULL;

	/* timestamp connection to the provider */
	public $timestamp = NULL; 

	/* user profile, containts the list of fields available in the normalized user profile structure used by PixelpinAuth. */
	public $profile = NULL;

	/**
	* inisialize the user object,
	*/
	function __construct()
	{
		$this->timestamp = time(); 

		$this->profile   = new Pixelpin_User_Profile(); 
	}
}
