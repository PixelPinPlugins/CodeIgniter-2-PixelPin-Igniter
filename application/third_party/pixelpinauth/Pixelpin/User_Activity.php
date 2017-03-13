<?php
/*!
* PixelpinAuth
* http://pixelpinauth.sourceforge.net | http://github.com/pixelpinauth/pixelpinauth
* (c) 2009-2012, PixelpinAuth authors | http://pixelpinauth.sourceforge.net/licenses.html 
*/

/**
 * Pixelpin_User_Activity 
 * 
 * used to provider the connected user activity stream on a standardized structure across supported social apis.
 * 
 * http://pixelpinauth.sourceforge.net/userguide/Profile_Data_User_Activity.html
 */
class Pixelpin_User_Activity
{
	/* activity id on the provider side, usually given as integer */
	public $id = NULL;

	/* activity date of creation */ 
	public $date = NULL;

	/* activity content as a string */ 
	public $text = NULL;

	/* user who created the activity */
	public $user = NULL;

	public function __construct()
	{
		$this->user = new stdClass();

		// typically, we should have a few information about the user who created the event from social apis
		$this->user->identifier  = NULL;
		$this->user->displayName = NULL;
		$this->user->profileURL  = NULL;
		$this->user->photoURL    = NULL; 
	}
}
