<?php
/**
 * WC_CSP_MultiCurrency_Compatibility class
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
 * WooCommerce MultiCurrency Compatibility.
 *
 * @since  1.4.0
 */
class WC_CSP_MultiCurrency_Compatibility {

	/**
	 * Initialization.
	 */
	public static function init() {
		self::load_conditions();
	}

	/**
	 * Load additional conditions by adding to the global conditions array.
	 *
	 * @return void
	 */
	public static function load_conditions() {

		$load_conditions = array(
			'WC_CSP_Condition_Currency'
		);

		if ( is_array( WC_CSP()->conditions->conditions ) ) {

			foreach ( $load_conditions as $condition ) {

				$condition = new $condition();
				WC_CSP()->conditions->conditions[ $condition->id ] = $condition;
			}
		}
	}
}

WC_CSP_MultiCurrency_Compatibility::init();
