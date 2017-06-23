<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
