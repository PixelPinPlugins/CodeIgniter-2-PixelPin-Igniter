<?php
/*!
* PixelpinAuth
* http://pixelpinauth.sourceforge.net | http://github.com/pixelpinauth/pixelpinauth
* (c) 2009-2012, PixelpinAuth authors | http://pixelpinauth.sourceforge.net/licenses.html
*/
 
/**
 * Debugging and Logging manager
 */
class Pixelpin_Logger
{
	function __construct()
	{
		// if debug mode is set to true, then check for the writable log file
		if ( Pixelpin_Auth::$config["debug_mode"] ){
			if ( ! file_exists( Pixelpin_Auth::$config["debug_file"] ) ){
				throw new Exception( "'debug_mode' is set to 'true', but the file " . Pixelpin_Auth::$config['debug_file'] . " in 'debug_file' does not exit.", 1 );
			}

			if ( ! is_writable( Pixelpin_Auth::$config["debug_file"] ) ){
				throw new Exception( "'debug_mode' is set to 'true', but the given log file path 'debug_file' is not a writable file.", 1 );
			}
		} 
	}

	public static function debug( $message, $object = NULL )
	{
		if( Pixelpin_Auth::$config["debug_mode"] ){
			$datetime = new DateTime();
			$datetime =  $datetime->format(DATE_ATOM);

			file_put_contents( 
				Pixelpin_Auth::$config["debug_file"], 
				"DEBUG -- " . $_SERVER['REMOTE_ADDR'] . " -- " . $datetime . " -- " . $message . " -- " . print_r($object, true) . "\n", 
				FILE_APPEND
			);
		}
	}

	public static function info( $message )
	{ 
		if( Pixelpin_Auth::$config["debug_mode"] ){
			$datetime = new DateTime();
			$datetime =  $datetime->format(DATE_ATOM);

			file_put_contents( 
				Pixelpin_Auth::$config["debug_file"], 
				"INFO -- " . $_SERVER['REMOTE_ADDR'] . " -- " . $datetime . " -- " . $message . "\n", 
				FILE_APPEND
			);
		}
	}

	public static function error($message, $object = NULL)
	{ 
		if( Pixelpin_Auth::$config["debug_mode"] ){
			$datetime = new DateTime();
			$datetime =  $datetime->format(DATE_ATOM);

			file_put_contents( 
				Pixelpin_Auth::$config["debug_file"], 
				"ERROR -- " . $_SERVER['REMOTE_ADDR'] . " -- " . $datetime . " -- " . $message . " -- " . print_r($object, true) . "\n", 
				FILE_APPEND
			);
		}
	}
}
