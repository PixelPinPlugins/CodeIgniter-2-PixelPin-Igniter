<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'/third_party/pixelpinauth/Pixelpin/Auth.php';

class PixelpinAuthLib extends Pixelpin_Auth
{
	function __construct($config = array())
	{
		$ci =& get_instance();
		$ci->load->helper('url_helper');

		$config['base_url'] = site_url((config_item('index_page') == '' ? SELF : '').$config['base_url']);

		parent::__construct($config);

		log_message('debug', 'PixelpinAuthLib Class Initalized');
	}

	/**
	 * @deprecated
	 */
	public static function serviceEnabled($service)
	{
		return self::providerEnabled($service);
	}

	public static function providerEnabled($provider)
	{
		return isset(parent::$config['providers'][$provider]) && parent::$config['providers'][$provider]['enabled'];
	}
}

/* End of file PixelpinAuthLib.php */
/* Location: ./application/libraries/PixelpinAuthLib.php */