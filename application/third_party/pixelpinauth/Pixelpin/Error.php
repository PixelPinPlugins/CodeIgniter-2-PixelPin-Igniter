<?php
/*!
* PixelpinAuth
* http://pixelpinauth.sourceforge.net | http://github.com/pixelpinauth/pixelpinauth
* (c) 2009-2012, PixelpinAuth authors | http://pixelpinauth.sourceforge.net/licenses.html
*/

/**
 * Errors manager
 * 
 * PixelpinAuth errors are stored in Pixelpin::storage() and not displayed directly to the end user 
 */
class Pixelpin_Error
{
	/**
	* store error in session
	*/
	public static function setError( $message, $code = NULL, $trace = NULL, $previous = NULL )
	{
		Pixelpin_Logger::info( "Enter Pixelpin_Error::setError( $message )" );

		Pixelpin_Auth::storage()->set( "pauth_session.error.status"  , 1         );
		Pixelpin_Auth::storage()->set( "pauth_session.error.message" , $message  );
		Pixelpin_Auth::storage()->set( "pauth_session.error.code"    , $code     );
		Pixelpin_Auth::storage()->set( "pauth_session.error.trace"   , $trace    );
		Pixelpin_Auth::storage()->set( "pauth_session.error.previous", $previous );
	}

	/**
	* clear the last error
	*/
	public static function clearError()
	{ 
		Pixelpin_Logger::info( "Enter Pixelpin_Error::clearError()" );

		Pixelpin_Auth::storage()->delete( "pauth_session.error.status"   );
		Pixelpin_Auth::storage()->delete( "pauth_session.error.message"  );
		Pixelpin_Auth::storage()->delete( "pauth_session.error.code"     );
		Pixelpin_Auth::storage()->delete( "pauth_session.error.trace"    );
		Pixelpin_Auth::storage()->delete( "pauth_session.error.previous" );
	}

	/**
	* Checks to see if there is a an error. 
	* 
	* @return boolean True if there is an error.
	*/
	public static function hasError()
	{ 
		return (bool) Pixelpin_Auth::storage()->get( "pauth_session.error.status" );
	}

	/**
	* return error message 
	*/
	public static function getErrorMessage()
	{ 
		return Pixelpin_Auth::storage()->get( "pauth_session.error.message" );
	}

	/**
	* return error code  
	*/
	public static function getErrorCode()
	{ 
		return Pixelpin_Auth::storage()->get( "pauth_session.error.code" );
	}

	/**
	* return string detailled error backtrace as string.
	*/
	public static function getErrorTrace()
	{ 
		return Pixelpin_Auth::storage()->get( "pauth_session.error.trace" );
	}

	/**
	* @return string detailled error backtrace as string.
	*/
	public static function getErrorPrevious()
	{ 
		return Pixelpin_Auth::storage()->get( "pauth_session.error.previous" );
	}
}
