<?php

// Load pro class
require_once(dirname(__FILE__).'/scans.php');

/**
 * WP Link Status Demo Admin Scans class
 *
 * @package WP Link Status
 * @subpackage WP Link Status Demo Admin
 */
class WPLNST_Admin_Demo_Scans extends WPLNST_Admin_Pro_Scans {



	// Scan results
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Show a list table for scan results
	 */
	protected function scans_results_views_table($args) {
		wplnst_require('views-demo', 'scans-results');
		$list = new WPLNST_Views_Pro_Scans_Results($args['results']);
		$list->prepare_items();
		$list->display();
	}



	// Scan crawler
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Wrapper function to run scan from Alive class
	 */
	protected function scans_crawler_run($scan_id, $hash) {
		WPLNST_Core_Alive::run($scan_id, $hash);
	}



}