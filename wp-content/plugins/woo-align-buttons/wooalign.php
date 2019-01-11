<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://320up.com
 * @since             3.1.0
 * @package           Wooalign
 *
 * @wordpress-plugin
 * Plugin Name:          Woo Align Buttons
 * Plugin URI:           https://wordpress.org/plugins/woo-align-buttons
 * Description:          A lightweight plugin to align WooCommerce "Add to cart" buttons.
 * Version:              3.5.3
 * WC requires at least: 3.0.0
 * WC tested up to:      3.5.3
 * Author:               320up
 * Author URI:           https://320up.com
 * License:              GPL-2.0+
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          wooalign
 * Domain Path:          /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_VERSION', '3.5.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wooalign-activator.php
 */
function activate_wooalign() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wooalign-activator.php';
	Wooalign_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wooalign-deactivator.php
 */
function deactivate_wooalign() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wooalign-deactivator.php';
	Wooalign_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wooalign' );
register_deactivation_hook( __FILE__, 'deactivate_wooalign' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wooalign.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    3.1.0
 */
function run_wooalign() {

	$plugin = new Wooalign();
	$plugin->run();

}
run_wooalign();
