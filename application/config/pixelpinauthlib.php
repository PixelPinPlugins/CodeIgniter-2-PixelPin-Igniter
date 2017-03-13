<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*!
* PixelpinAuth
* http://pixelpinauth.sourceforge.net | http://github.com/pixelpinauth/pixelpinauth
* (c) 2009-2012, PixelpinAuth authors | http://pixelpinauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	PixelpinAuth Config file: http://pixelpinauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

$config =
	array(
		// set on "base_url" the relative url that point to PixelpinAuth Endpoint
		'base_url' => '/pauth/endpoint',

		"providers" => array (
			//PixelPin Provider
			"PixelPin" => array (
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" ),
			),
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => (ENVIRONMENT == 'development'),

		"debug_file" => APPPATH.'/logs/pixelpinauth.log',
	);


/* End of file pixelpinauthlib.php */
/* Location: ./application/config/pixelpinauthlib.php */