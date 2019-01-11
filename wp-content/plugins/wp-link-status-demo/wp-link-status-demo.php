<?php
/*
Plugin Name: WP Link Status Demo
Plugin URI: http://seedplugins.com/wp-link-status/
Description: A demo version to try WP Link Status Pro plugin only for testing purposes.
Version: 1.0.4
Author: Pau Iglesias, SeedPlugins
License: GPLv2 or later
Text Domain: wplnst
Domain Path: /languages
*/

// Avoid script calls via plugin URL
if (!function_exists('add_action'))
	die;

// Boot checks
require(dirname(__FILE__).'/core/boot.php');

// This plugin constants
define('WPLNST_FILE', __FILE__);
define('WPLNST_PATH', dirname(WPLNST_FILE));
define('WPLNST_VERSION', '1.0.4');

// Check scan crawling action
require_once(WPLNST_PATH.'/core-demo/alive.php');
WPLNST_Core_Demo_Alive::check();

// Check context
if (is_admin()) {
	
	// Admin area
	require_once(WPLNST_PATH.'/admin-demo/admin.php');
	WPLNST_Admin_Demo::instantiate();
	
	// Plugin activation
	register_activation_hook(WPLNST_FILE, 'wplnst_plugin_activation');
	function wplnst_plugin_activation() {
		wplnst_require('core', 'register');
		WPLNST_Core_Register::activation();
	}
	
	// Plugin deactivation
	register_deactivation_hook(WPLNST_FILE, 'wplnst_plugin_deactivation');
	function wplnst_plugin_deactivation() {
		wplnst_require('core', 'register');
		WPLNST_Core_Register::deactivation();
	}
	
	// Plugin uninstall
	register_uninstall_hook(WPLNST_FILE, 'wplnst_plugin_uninstall');
	function wplnst_plugin_uninstall() {
		wplnst_require('core', 'register');
		WPLNST_Core_Register::uninstall();
	}
}