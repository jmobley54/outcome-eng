<?php
/**
 * WooCommerce Authorize.Net AIM Gateway
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net AIM Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net AIM Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/authorize-net-aim/
 *
 * @package   WC-Gateway-Authorize-Net-AIM/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2019, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_3_0 as Framework;

/**
 * Authorize.Net AIM Payment Gateway Parent Class
 *
 * Functionality which is shared between the credit card and echeck gateways
 *
 * @since 3.0
 */
class WC_Gateway_Authorize_Net_AIM extends Framework\SV_WC_Payment_Gateway_Direct {


	/** @var string authorize.net API login ID */
	public $api_login_id;

	/** @var string authorize.net API transaction key */
	public $api_transaction_key;

	/** @var string authorize.net test API login ID */
	public $test_api_login_id;

	/** @var string authorize.net test API transaction key */
	public $test_api_transaction_key;

	/** @var WC_Authorize_Net_AIM_API instance */
	protected $api;

	/** @var array shared settings names */
	protected $shared_settings_names = array( 'api_login_id', 'api_transaction_key', 'test_api_login_id', 'test_api_transaction_key' );


	/**
	 * Returns an array of form fields specific for this method.
	 *
	 * @see Framework\SV_WC_Payment_Gateway::get_method_form_fields()
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function get_method_form_fields() {

		return array(

			'api_login_id' => array(
				'title'    => __( 'API Login ID', 'woocommerce-gateway-authorize-net-aim' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Authorize.Net API Login ID', 'woocommerce-gateway-authorize-net-aim' ),
			),

			'api_transaction_key' => array(
				'title'    => __( 'API Transaction Key', 'woocommerce-gateway-authorize-net-aim' ),
				'type'     => 'password',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Authorize.Net API Transaction Key', 'woocommerce-gateway-authorize-net-aim' ),
			),

			'test_api_login_id' => array(
				'title'    => __( 'Test API Login ID', 'woocommerce-gateway-authorize-net-aim' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your test Authorize.Net API Login ID', 'woocommerce-gateway-authorize-net-aim' ),
			),

			'test_api_transaction_key' => array(
				'title'    => __( 'Test API Transaction Key', 'woocommerce-gateway-authorize-net-aim' ),
				'type'     => 'password',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your test Authorize.Net API Transaction Key', 'woocommerce-gateway-authorize-net-aim' ),
			),
		);
	}


	/**
	 * Add any Authorize.Net AIM specific transaction information as
	 * class members of WC_Order instance.  Added members can include:
	 *
	 * auth_net_aim_merchant_defined_fields - custom fields added to the transaction in format array( name => value )
	 *
	 * @since 3.0
	 * @see WC_Gateway_Authorize_Net_AIM::get_order()
	 * @param int $order_id order ID being processed
	 * @return WC_Order object with payment and transaction information attached
	 */
	public function get_order( $order_id ) {

		// add common order members
		$order = parent::get_order( $order_id );

		/**
		 * Filter the order description
		 *
		 * @since 3.3.2
		 * @param string $description The order description
		 * @param int $order_id The order ID being processed
		 * @param WC_Gateway_Authorize_Net_AIM $aim The gateway class instance
		 */
		$order->description = apply_filters( 'wc_authorize_net_aim_transaction_description', sprintf( _x( '%s - Order %s', 'Order description', 'woocommerce-gateway-authorize-net-aim' ), wp_specialchars_decode( get_bloginfo( 'name' ) ), $order->get_order_number() ), $order_id, $this );

		return $order;
	}


	/**
	 * Determines if the gateway is properly configured to perform transactions.
	 *
	 * Authorize.Net AIM requires: API Login ID & API Transaction Key.
	 *
	 * @see Framework\SV_WC_Payment_Gateway::is_configured()
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	protected function is_configured() {

		$is_configured = parent::is_configured();

		// missing configuration
		if ( ! $this->get_api_login_id() || ! $this->get_api_transaction_key() ) {
			$is_configured = false;
		}

		return $is_configured;
	}


	/** Getter methods ******************************************************/


	/**
	 * Gets the API handler instance.
	 *
	 * @see Framework\SV_WC_Payment_Gateway::get_api()
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_Authorize_Net_AIM_API
	 */
	public function get_api() {

		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		$plugin_path = $this->get_plugin()->get_plugin_path();

		// main API class responsible for communication with AIM API
		require_once( $plugin_path . '/includes/api/class-wc-authorize-net-aim-api.php' );

		// API request
		require_once( $plugin_path . '/includes/api/class-wc-authorize-net-aim-api-request.php' );

		// API response
		require_once( $plugin_path . '/includes/api/class-wc-authorize-net-aim-api-response.php' );

		require_once( $plugin_path . '/includes/api/class-wc-authorize-net-aim-api-transaction-details-request.php' );
		require_once( $plugin_path . '/includes/api/class-wc-authorize-net-aim-api-transaction-details-response.php' );

		// API response user message helper
		require_once( $plugin_path . '/includes/api/class-wc-authorize-net-aim-api-response-message-helper.php' );

		return $this->api = new WC_Authorize_Net_AIM_API( $this->get_id(), $this->get_environment(), $this->get_api_login_id(), $this->get_api_transaction_key() );
	}


	/**
	 * Returns the API Login ID based on the current environment
	 *
	 * @since 3.0
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string the API login ID to use
	 */
	public function get_api_login_id( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		return 'production' == $environment_id ? $this->api_login_id : $this->test_api_login_id;
	}


	/**
	 * Returns the API Transaction Key based on the current environment
	 *
	 * @since 3.0
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string the API transaction key to use
	 */
	public function get_api_transaction_key( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		return 'production' == $environment_id ? $this->api_transaction_key : $this->test_api_transaction_key;
	}


	/**
	 * Gets the customer ID user meta key.
	 *
	 * Authorize.Net AIM does not support customer IDs, so this will always
	 * return false.
	 *
	 * @see Framework\SV_WC_Payment_Gateway::get_customer_id_user_meta_name()
	 *
	 * @since 3.0.0
	 *
	 * @param string $environment_id desired environment
	 * @return false
	 */
	public function get_customer_id_user_meta_name( $environment_id = null ) {

		return false;
	}


	/**
	 * Gets a guest customer ID.
	 *
	 * Authorize.Net AIM does not support customer IDs so this will always
	 * return false.
	 *
	 * @see Framework\SV_WC_Payment_Gateway::get_guest_customer_id()
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return false
	 */
	public function get_guest_customer_id( WC_Order $order ) {

		return false;
	}


	/**
	 * Authorize.Net AIM does not support customer IDs
	 *
	 * @see Framework\SV_WC_Payment_Gateway::get_customer_id()
	 *
	 * @since 3.0.0
	 *
	 * @param int $user_id WordPress user ID
	 * @param array $args optional additional arguments
	 * @return false
	 */
	public function get_customer_id( $user_id, $args = array() ) {

		return false;
	}


}
