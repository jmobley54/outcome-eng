<?php

// Load main class
require_once(dirname(dirname(__FILE__)).'/admin/admin.php');

/**
 * WP Link Status Demo Admin class
 *
 * @package WP Link Status Demo
 * @subpackage WP Link Status Demo Admin
 */
class WPLNST_Admin_Demo extends WPLNST_Admin {



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates a singleton object
	 */
	public static function instantiate($args = null) {
		return self::get_instance(get_class(), $args);
	}



	/**
	 * Enqueue specific versions scripts
	 */
	protected function admin_enqueue_version() {
		
		// Version admin styles
		wp_enqueue_style('wplnst-admin-pro-css', plugins_url('assets-demo/css/admin-pro.css', WPLNST_FILE), array(), $this->script_version);
		wp_enqueue_style('wplnst-admin-demo-css', plugins_url('assets-demo/css/admin-demo.css', WPLNST_FILE), array(), $this->script_version);
		
		// Admin script version
		wp_enqueue_script('wplnst-admin-demo-script', plugins_url('assets-demo/js/admin-demo.js', WPLNST_FILE), array('jquery'), $this->script_version, true);
		wp_enqueue_script('wplnst-admin-demo-paywall', plugins_url('assets-demo/js/admin-paywall.js', WPLNST_FILE), array('jquery'), $this->script_version, true);
		
		// URL tools scripts
		if (WPLNST_Core_Demo_Plugin::slug.'-tools-url' == $_GET['page'])
			wp_enqueue_script('wplnst-admin-demo-tools-url', plugins_url('assets-demo/js/admin-tools-url.js', WPLNST_FILE), array('jquery', 'json2'), $this->script_version, true);
	}



	// Menu hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Admin menu utilities
	 */
	protected function admin_menu_utilities() {
		add_submenu_page(WPLNST_Core_Demo_Plugin::slug, __('URL Tools', 'wplnst'), __('URL Tools', 'wplnst'), WPLNST_Core_Demo_Plugin::capability, WPLNST_Core_Demo_Plugin::slug.'-tools-url', array(&$this, 'admin_menu_tools_url'));
	}



	/**
	 * Admin menu addons
	 */
	protected function admin_menu_addons() {
		add_submenu_page(WPLNST_Core_Demo_Plugin::slug, __('Extensions', 'wplnst'), '<span style="color:#f18500">'.__('Extensions', 'wplnst').'</span>', WPLNST_Core_Demo_Plugin::capability, WPLNST_Core_Demo_Plugin::slug.'-extensions', array(&$this, 'admin_menu_extensions'));
	}



	/*
	 * New or edit scan page
	 */
	public function admin_menu_scans() {
		wplnst_require('admin-demo', 'scans-demo');
		new WPLNST_Admin_Demo_Scans($this, 'context');
	}



	/**
	 * Section for URL tools
	 */
	public function admin_menu_tools_url() {
		wplnst_require('admin-demo', 'tools-url');
		new WPLNST_Admin_Demo_Tools_URL($this);
	}



	/**
	 * Open or close advanced panel
	 */
	public function ajax_results_advanced_display() {
		
		// Check input
		if (!isset($_POST['display']) || !in_array($_POST['display'], array('off', 'on')))
			return;
		
		// Check nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wplnst-results-advanced-display'))
			return;
		
		// Update
		update_user_meta(get_current_user_id(), 'wplnst_advanced_search', $_POST['display']);
	}



	/**
	 * Handler for tools URL
	 */
	public function ajax_tools_url() {
		
		// Load dependencies
		wplnst_require('core-demo', 'tools-url');
		
		// Instantiate and self start start processes
		$tools = WPLNST_Core_Pro_Tools_URL::instantiate();
	}



	// Custom views
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates the HTML for the paywall screen
	 */
	protected function screen_view_before() {
		?><div id="wplnst-paywall" class="wplnst-display-none">
			<div id="wplnst-only-pro">
				<div id="wplnst-only-pro-top" class="wplnst-clearfix"><p>WP Link Status Pro</p><a href="#" id="wplnst-only-pro-remove">&nbsp;</a></div>
				<div id="wplnst-only-pro-content">
					<h3>Take control with premium features</h3>
					<ul>
						<li>Update link URLs, anchor texts, apply redirections, unlink, etc.</li>
						<li>Create nofollow links or remove existing nofollow attributes</li>
						<li>Recheck link status and show original status headers</li>
						<li>Full advanced filters with string URL search or anchor texts</li>
						<li>Extra URL tools to detect and perform massive link changes</li>
					</ul>
					<p id="wplnst-only-pro-link-container"><a href="http://seedplugins.com/wp-link-status/" id="wplnst-only-pro-link" target="_blank">Get started now</a></p>
				</div>
			</div>
		</div><?php
	}



	// Utilities
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Return plugin title for screen view
	 */
	protected function get_plugin_title() {
		return 'WP Link Status Demo';
	}



}