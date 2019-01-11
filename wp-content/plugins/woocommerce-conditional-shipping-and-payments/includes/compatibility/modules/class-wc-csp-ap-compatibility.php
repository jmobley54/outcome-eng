<?php
/**
 * WC_CSP_AP_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Amazon Pay Compatibility.
 *
 * @since  1.4.0
 */
class WC_CSP_AP_Compatibility {

	public static function init() {
		// Support Amazon Payments.
		add_filter( 'woocommerce_amazon_payments_init', array( __CLASS__, 'init_amazon_payments' ) );
	}

	/**
	 * Prevent Amazon Payments from initializing.
	 *
	 * @param  boolean  $enable
	 * @return boolean
	 */
	public static function init_amazon_payments( $enable ) {

		if ( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) ) {
			if ( WC_CSP_Compatibility::is_gateway_restricted( 'amazon_payments_advanced' ) ) {
				$enable = false;
			}
		}

		return $enable;
	}
}

WC_CSP_AP_Compatibility::init();
