<?php
/*!
* PixelpinAuth
* http://pixelpinauth.sourceforge.net | http://github.com/pixelpinauth/pixelpinauth
* (c) 2009-2012, PixelpinAuth authors | http://pixelpinauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	PixelpinAuth Config file: http://pixelpinauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return 
	array(
		"base_url" => "http://localhost/pixelpinauth-git/pixelpinauth/", 

		"providers" => array (
			//PixelPin Provider
			"PixelPin" => array (
				"enabled" => true,
				"keys"    => array ( "id" => "", "secret" => '' ),
			),
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => "",
	);
