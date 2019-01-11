<?php

// Load main class
require_once(dirname(dirname(__FILE__)).'/core/plugin.php');

/**
 * WP Link Status Demo Core Plugin class
 *
 * @package WP Link Status Demo
 * @subpackage WP Link Status Demo Core
 */
class WPLNST_Core_Demo_Plugin extends WPLNST_Core_Plugin {



	/**
	 * URL to the tools section
	 */
	public static function get_url_tools_url() {
		return self::get_url_scans().'-tools-url';
	}



}