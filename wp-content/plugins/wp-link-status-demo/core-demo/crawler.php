<?php

// Load main class
require_once(dirname(dirname(__FILE__)).'/core/crawler.php');

/**
 * WP Link Status Demo Core Crawler class
 *
 * @package WP Link Status Demo
 * @subpackage WP Link Status Demo Core
 */
class WPLNST_Core_Demo_Crawler extends WPLNST_Core_Crawler {



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates a singleton object
	 */
	public static function instantiate($args = null) {
		return self::get_instance(get_class(), $args);
	}



	// Override methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Start again the crawler
	 */
	protected function restart() {
		WPLNST_Core_Demo_Alive::run($this->scan->id, $this->scan->hash, $this->thread_id);
	}



	/**
	 * Calls alive activity method to check scans activity
	 */
	protected function activity() {
		WPLNST_Core_Demo_Alive::activity(true);
	}



}