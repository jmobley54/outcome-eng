<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              mandsconsulting.com
 * @since             5.0.0
 * @package           Email_Before_Download
 *
 * @wordpress-plugin
 * Plugin Name:       Email Before Download
 * Plugin URI:        mandsconsulting.com
 * Version:           5.1.9
 * Author:            M&S Consulting
 * Author URI:        mandsconsulting.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       email-before-download
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_NAME_VERSION', '5.1.9' );

function activate_email_before_download() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-email-before-download-activator.php';
	Email_Before_Download_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_email_before_download' );
require plugin_dir_path( __FILE__ ) . 'includes/class-email-before-download.php';

function run_email_before_download() {
    $plugin = new Email_Before_Download();
	$plugin->run();

}
run_email_before_download();
