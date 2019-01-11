<?php

// Load main class
require_once(dirname(dirname(__FILE__)).'/core/alive.php');

/**
 * WP Link Status Demo Core Alive class
 *
 * @package WP Link Status Demo
 * @subpackage WP Link Status Demo Core
 */
class WPLNST_Core_Demo_Alive extends WPLNST_Core_Alive {



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Start check
	 */
	public static function check() {
		self::start(get_class());
	}



	/**
	 * Specific version components at start
	 */
	protected static function start_version() {
		
		// Plugin definitions
		wplnst_require('core-demo', 'plugin');
	}



	// Override methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Load a crawler instance
	 */
	protected static function instantiate_crawler($scan_row, $thread_id) {
		
		// Load dependencies
		wplnst_require('core-demo', 'crawler');
		
		// Instance
		WPLNST_Core_Demo_Crawler::instantiate(array(
			'scan_id' 		=> $scan_row->scan_id,
			'thread_id' 	=> $thread_id,
			'crawler_url' 	=> parent::get_crawler_url($scan_row->scan_id, $scan_row->hash, $thread_id),
		));
	}



}