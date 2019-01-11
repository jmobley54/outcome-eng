<?php
/**
 * Plugin Name: WP OAuth Server - Pro
 * Plugin URI: http://wp-oauth.com
 * Version: 3.5.7
 * Description: Full OAuth2 Server for WordPress. User Authorization Management Systems For WordPress.
 * Author: WP OAuth Server
 * Author URI: http://wp-oauth.com
 * Text Domain: wp-oauth
 *
 * @author  Justin Greer <justin@justin-greer.com>
 * @package WP OAuth Server
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! defined( 'WPOAUTH_FILE' ) ) {
	define( 'WPOAUTH_FILE', __FILE__ );
}

if ( ! defined( 'WPOAUTH_VERSION' ) ) {
	define( 'WPOAUTH_VERSION', '3.5.7' );
}

// localize
add_action( 'plugins_loaded', 'wo_load_textdomain', 99 );
function wo_load_textdomain() {
	load_plugin_textdomain( 'wp-oauth', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * 5.4 Strict Mode Temp Patch
 *
 * Since PHP 5.4, WP will through notices due to the way WP calls statically
 */
function _wo_server_register_files() {
	wp_register_style( 'wo_admin', plugins_url( '/assets/css/admin.css', __FILE__ ) );
	wp_register_script( 'wo_admin', plugins_url( '/assets/js/admin.js', __FILE__ ), array( 'jquery-ui-tabs' ) );

	// Notices JS
	wp_register_script( 'wo_admin_notices', plugins_url( '/assets/js/notices.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'wo_admin_notices' );
}

add_action( 'admin_enqueue_scripts', '_wo_server_register_files' );

require_once( dirname( __FILE__ ) . '/includes/functions.php' );
require_once( dirname( __FILE__ ) . '/wp-oauth-main.php' );

/**
 * Adds/registers query vars
 *
 * @return void
 */
function _wo_server_register_query_vars() {
	_wo_server_register_rewrites();

	global $wp;
	$wp->add_query_var( 'oauth' );
	$wp->add_query_var( 'well-known' );
	$wp->add_query_var( 'wpoauthincludes' );
}

add_action( 'init', '_wo_server_register_query_vars' );

/**
 * Registers rewrites for OAuth2 Server
 *
 * - authorize
 * - token
 * - .well-known
 * - wpoauthincludes
 *
 * @return void
 */
function _wo_server_register_rewrites() {
	add_rewrite_rule( '^oauth/(.+)', 'index.php?oauth=$matches[1]', 'top' );
	add_rewrite_rule( '^.well-known/(.+)', 'index.php?well-known=$matches[1]', 'top' );
	add_rewrite_rule( '^wpoauthincludes/(.+)', 'index.php?wpoauthincludes=$matches[1]', 'top' );
}

/**
 * [template_redirect_intercept description]
 *
 * @return [type] [description]
 */
function _wo_server_template_redirect_intercept( $template ) {
	global $wp_query;

	if ( $wp_query->get( 'oauth' ) || $wp_query->get( 'well-known' ) ) {
		require_once dirname( __FILE__ ) . '/library/class-wo-api.php';
		exit;
	}

	return $template;
}

add_filter( 'template_include', '_wo_server_template_redirect_intercept', 100 );

/**
 * OAuth2 Server Activation
 *
 * @param  [type] $network_wide [description]
 *
 * @return [type]               [description]
 */
function _wo_server_activation( $network_wide ) {
	if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide ) {
		$mu_blogs = wp_get_sites();
		foreach ( $mu_blogs as $mu_blog ) {
			switch_to_blog( $mu_blog['blog_id'] );
			_wo_server_register_rewrites();
			flush_rewrite_rules();
		}
		restore_current_blog();
	} else {
		_wo_server_register_rewrites();
		flush_rewrite_rules();
	}
}

register_activation_hook( __FILE__, '_wo_server_activation' );

/**
 * OAuth Server Deactivation
 *
 * @param  [type] $network_wide [description]
 *
 * @return [type]               [description]
 */
function _wo_server_deactivation( $network_wide ) {
	if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide ) {
		$mu_blogs = wp_get_sites();
		foreach ( $mu_blogs as $mu_blog ) {
			switch_to_blog( $mu_blog['blog_id'] );
			flush_rewrite_rules();
		}
		restore_current_blog();
	} else {
		flush_rewrite_rules();
	}
}

register_deactivation_hook( __FILE__, '_wo_server_deactivation' );

/**
 * Initialize updater
 *
 * @since 3.4.0 Filter is used to control updater
 */
$wo_updater = apply_filters( 'wo_updater', true );
if ( $wo_updater ) {

	if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		require_once( dirname( __FILE__ ) . '/includes/updater.php' );
	}

	$edd_updater = new EDD_SL_Plugin_Updater( 'https://wp-oauth.com', __FILE__, array(
			'version'   => WPOAUTH_VERSION,
			'license'   => wo_license_key(),
			'item_name' => 'WP OAuth Server',
			'author'    => 'Justin Greer Interactive, LLC',
			'url'       => home_url(),
		)
	);
}

/**
 * License Notice
 */
if ( ! wo_is_licensed() ) {
	function wo_license_notice() {
		?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'WP OAuth Server\'s Updates are disabled. Please enter a valid license on the', 'wp-oauth' ); ?>
                <a
                        href="<?php echo admin_url( 'admin.php?page=wo_server_status' ); ?>">Server Status</a> Page.</p>
        </div>
		<?php
	}

	add_action( 'admin_notices', 'wo_license_notice' );
}

global $wp_version;
if ( $wp_version <= 4.3 ) {
	function wo_incompatibility_with_wp_version() {
		?>
        <div class="notice notice-error">
            <p><?php _e( 'WP OAuth Server requires that WordPress 4.4 or greater be used. Update to the latest WordPress version.', 'wp-oauth' ); ?>
                <a href="<?php echo admin_url( 'update-core.php' ); ?>"><?php _e( 'Update Now', 'wp-oauth' ); ?></a></p>
        </div>
		<?php
	}

	add_action( 'admin_notices', 'wo_incompatibility_with_wp_version' );
}

register_activation_hook( __FILE__, array( new WO_Server, 'setup' ) );
register_activation_hook( __FILE__, array( new WO_Server, 'upgrade' ) );
