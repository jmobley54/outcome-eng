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
 * @package   WC-Gateway-Authorize-Net-AIM/Plugin
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2019, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_3_0 as Framework;

/**
 * WooCommerce Authorize.Net AIM Gateway Main Plugin Class
 *
 * This plugin adds Authorize.Net AIM as a payment gateway.  This class handles all the
 * non-gateway tasks such as verifying dependencies are met, loading the text
 * domain, etc.
 *
 * @since 3.0.0
 */
class WC_Authorize_Net_AIM extends Framework\SV_WC_Payment_Gateway_Plugin {


	/** @var WC_Authorize_Net_AIM single instance of this plugin */
	protected static $instance;


	/** string version number */
	const VERSION = '3.14.5';

	/** string the plugin id */
	const PLUGIN_ID = 'authorize_net_aim';

	/** string credit card gateway class name */
	const CREDIT_CARD_GATEWAY_CLASS_NAME = 'WC_Gateway_Authorize_Net_AIM_Credit_Card';

	/** string credit card gateway id */
	const CREDIT_CARD_GATEWAY_ID = 'authorize_net_aim';

	/** string eCheck gateway class name */
	const ECHECK_GATEWAY_CLASS_NAME = 'WC_Gateway_Authorize_Net_AIM_eCheck';

	/** string eCheck gateway id */
	const ECHECK_GATEWAY_ID = 'authorize_net_aim_echeck';

	/** string emulation gateway class name */
	const EMULATION_GATEWAY_CLASS_NAME = 'WC_Gateway_Authorize_Net_AIM_Emulation';

	/** string emulation gateway ID */
	const EMULATION_GATEWAY_ID = 'authorize_net_aim_emulation';


	/**
	 * Setup main plugin class
	 *
	 * @since 3.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'  => 'woocommerce-gateway-authorize-net-aim',
				'gateways'     => $this->get_enabled_gateways(),
				'dependencies' => array(
					'php_extensions' => array( 'SimpleXML', 'xmlwriter', 'dom' ),
				),
				'require_ssl' => true,
				'supports'    => array(
					self::FEATURE_CAPTURE_CHARGE,
				),
			)
		);

		// load gateway files
		$this->includes();

		if ( is_admin() && ! is_ajax() ) {

			// handle activating/deactivating emulation gateway
			add_action( 'admin_action_wc_authorize_net_aim_emulation', array( $this, 'toggle_emulation' ) );
		}
	}


	/**
	 * Loads any required files
	 *
	 * @since 3.0
	 */
	public function includes() {

		$plugin_path = $this->get_plugin_path();

		// gateway classes
		require_once( $plugin_path . '/includes/class-wc-gateway-authorize-net-aim.php' );
		require_once( $plugin_path . '/includes/class-wc-gateway-authorize-net-aim-credit-card.php' );
		require_once( $plugin_path . '/includes/class-wc-gateway-authorize-net-aim-echeck.php' );

		// get the store's base location
		$store_location = wc_get_base_location();

		// require checkout billing fields for non-US stores, as all European card processors require the billing fields
		// in order to successfully process transactions
		if ( ! is_admin() && 'US' !== $store_location['country'] ) {

			// remove blank arrays from the state fields, otherwise it's hidden
			add_action( 'woocommerce_states', array( $this, 'tweak_states' ), 1 );

			//  require the billing fields
			add_filter( 'woocommerce_get_country_locale', array( $this, 'require_billing_fields' ), 100 );
		}

		// load the emulation gateway if enabled
		if ( $this->is_emulation_enabled() ) {

			require_once( $plugin_path . '/includes/class-wc-gateway-authorize-net-aim-emulation.php' );
		}
	}


	/**
	 * Return the enabled gateways, AIM credit card/eCheck by default, with
	 * AIM emulation included when enabled
	 *
	 * @since 3.8.0
	 * @return array
	 */
	protected function get_enabled_gateways() {

		// default gateways
		$gateways = array(
			self::CREDIT_CARD_GATEWAY_ID => self::CREDIT_CARD_GATEWAY_CLASS_NAME,
			self::ECHECK_GATEWAY_ID      => self::ECHECK_GATEWAY_CLASS_NAME,
		);

		// add emulation gateway if enabled
		if ( $this->is_emulation_enabled() ) {
			$gateways[ self::EMULATION_GATEWAY_ID ] = self::EMULATION_GATEWAY_CLASS_NAME;
		}

		return $gateways;
	}


	/** Frontend methods ******************************************************/


	/**
	 * Before requiring all billing fields, the state array has to be removed of blank arrays, otherwise
	 * the field is hidden
	 *
	 * @see WC_Countries::__construct()
	 *
	 * @since 3.0
	 * @param array $countries the available countries
	 * @return array the available countries
	 */
	public function tweak_states( $countries ) {

		foreach ( $countries as $country_code => $states ) {

			if ( is_array( $countries[ $country_code ] ) && empty( $countries[ $country_code ] ) ) {
				$countries[ $country_code ] = null;
			}
		}

		return $countries;
	}


	/**
	 * Require all billing fields to be entered when the merchant is using a European payment processor
	 *
	 * @since 3.0
	 * @param array $locales array of countries and locale-specific address field info
	 * @return array the locales array with billing info required
	 */
	public function require_billing_fields( $locales ) {

		foreach ( $locales as $country_code => $fields ) {

			if ( isset( $locales[ $country_code ]['state']['required'] ) ) {
				$locales[ $country_code ]['state']['required'] = true;
				$locales[ $country_code ]['state']['label']    = $this->get_state_label( $country_code );
			}
		}

		return $locales;
	}


	/**
	 * Gets a label for states that don't have one set by WooCommerce.
	 *
	 * @since 3.11.1
	 *
	 * @param string $country_code the 2-letter country code for the billing country
	 * @return string the label for the "billing state" field at checkout
	 */
	protected function get_state_label( $country_code ) {

		switch( $country_code ) {

			case 'AF':
			case 'AT':
			case 'BI':
			case 'KR':
			case 'PL':
			case 'PT':
			case 'LK':
			case 'SE':
			case 'VN':
				$label = __( 'Province', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'AX':
			case 'YT':
				$label = __( 'Island', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'DE':
				$label = __( 'State', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'EE':
			case 'NO':
				$label = __( 'County', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'FI':
			case 'IL':
			case 'LB':
				$label = __( 'District', 'woocommerce-gateway-authorize-net-cim' );
			break;

			default:
				$label = __( 'Region', 'woocommerce-gateway-authorize-net-cim' );
		}

		return $label;
	}


	/** Admin methods ******************************************************/


	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @since 3.0
	 * @param array $actions associative array of action names to anchor tags
	 * @return array associative array of plugin action links
	 */
	public function plugin_action_links( $actions ) {

		// get the standard action links
		$actions = parent::plugin_action_links( $actions );

		// enable/disable emulation link
		$params = array(
			'action' => 'wc_authorize_net_aim_emulation',
			'toggle' => $this->is_emulation_enabled() ? 'disable' : 'enable'
		);

		$url = wp_nonce_url( add_query_arg( $params, 'admin.php' ), $this->get_file() );
		$title  = $this->is_emulation_enabled()
			? esc_html__( 'Disable Emulation Gateway', 'woocommerce-gateway-authorize-net-aim' )
			: esc_html__( 'Enable Emulation Gateway', 'woocommerce-gateway-authorize-net-aim' );

		$actions['emulation'] = sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', esc_url( $url ), $title );

		return $actions;
	}


	/**
	 * Returns the "Configure Credit Cards" or "Configure eCheck" plugin action links that go
	 * directly to the gateway settings page
	 *
	 * @since 3.4.0
	 * @see Framework\SV_WC_Payment_Gateway_Plugin::get_settings_url()
	 * @param string $gateway_id the gateway identifier
	 * @return string plugin configure link
	 */
	public function get_settings_link( $gateway_id = null ) {

		switch ( $gateway_id ) {

			case self::EMULATION_GATEWAY_ID:
				$label = __( 'Configure Emulator', 'woocommerce-gateway-authorize-net-aim' );
			break;

			case self::ECHECK_GATEWAY_ID:
				$label = __( 'Configure eChecks', 'woocommerce-gateway-authorize-net-aim' );
			break;

			default:
				$label = __( 'Configure Credit Cards', 'woocommerce-gateway-authorize-net-aim' );
		}

		return sprintf( '<a href="%s">%s</a>',
			$this->get_settings_url( $gateway_id ),
			$label
		);
	}


	/**
	 * Handles enabling/disabling the emulation gateway
	 *
	 * @since 3.8.0
	 */
	public function toggle_emulation() {

		// security check
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], $this->get_file() ) || ! current_user_can( 'manage_woocommerce' ) ) {
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// sanity check
		if ( empty( $_GET['toggle'] ) || ! in_array( $_GET['toggle'], array( 'enable', 'disable' ), true ) ) {
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// enable/disable the emulation gateway
		update_option( 'wc_authorize_net_aim_emulation_enabled', 'enable' === $_GET['toggle'] );

		$return_url = add_query_arg( array( 'wc_authorize_net_aim_emulation' => $_GET['toggle'] ), 'plugins.php' );

		// back to whence we came
		wp_safe_redirect( $return_url );
		exit;
	}


	/**
	 * Renders an admin notices, along with displaying a message on the plugins list table
	 * when activating/deactivating legacy SIM gateway
	 *
	 * @since 3.2.0
	 * @see Framework\SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		parent::add_admin_notices();

		$credit_card_gateway = $this->get_gateway( self::CREDIT_CARD_GATEWAY_ID );

		if ( $credit_card_gateway->is_enabled() && $credit_card_gateway->is_accept_js_enabled() && isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] ) {

			$message = '';

			if ( ! $credit_card_gateway->get_client_key() ) {
				$message = sprintf( __( "%s: A valid Client Key is required to use Accept.js at checkout.", 'woocommerce-gateway-authorize-net-aim' ), '<strong>' . $this->get_plugin_name() . '</strong>' );
			} elseif ( ! wc_checkout_is_https() ) {
				$message = sprintf( __( "%s: SSL is required to use Accept.js at checkout.", 'woocommerce-gateway-authorize-net-aim' ), '<strong>' . $this->get_plugin_name() . '</strong>' );
			}

			if ( $message ) {
				$this->get_admin_notice_handler()->add_admin_notice( $message, 'accept-js-status', array(
					'dismissible'  => false,
					'notice_class' => 'error',
				) );
			}
		}

		// emulation enabled/disabled notice
		if ( ! empty( $_GET['wc_authorize_net_aim_emulation'] ) ) {

			$message = ( 'enable' === $_GET['wc_authorize_net_aim_emulation'] )
				? __( 'Authorize.Net AIM Emulation Gateway is now enabled.', 'woocommerce-gateway-authorize-net-aim' )
				: __( 'Authorize.Net AIM Emulation Gateway is now disabled.', 'woocommerce-gateway-authorize-net-aim');

			$this->get_admin_notice_handler()->add_admin_notice( $message, 'emulation-status', array( 'dismissible' => false, ) );
		}
	}


	/**
	 * Returns true if emulation is enabled
	 *
	 * @since 3.8.0
	 * @return bool
	 */
	private function is_emulation_enabled() {

		return (bool) get_option( 'wc_authorize_net_aim_emulation_enabled' );
	}


	/**
	 * Return the gateway settings for the given gateway ID. Overridden to mark
	 * the emulation gateway as inheriting settings (even though it does not) to
	 * prevent the credit card/eCheck gateways from attempting to inherit it's settings
	 *
	 * TODO: this can be removed once is https://github.com/skyverge/wc-plugin-framework/issues/157
	 * is merged and it's FW version required {MR 2016-06-28}
	 *
	 * @since 3.8.0
	 * @see Framework\SV_WC_Payment_Gateway_Plugin::get_gateway_settings()
	 * @param string $gateway_id gateway identifier
	 * @return array settings array
	 */
	public function get_gateway_settings( $gateway_id ) {

		$settings = parent::get_gateway_settings( $gateway_id );

		if ( $gateway_id === self::EMULATION_GATEWAY_ID ) {
			$settings['inherit_settings'] = 'yes';
		}

		return $settings;
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Authorize.Net AIM Instance, ensures only one instance is/can be loaded
	 *
	 * @since 3.3.0
	 * @see wc_authorize_net_aim()
	 * @return WC_Authorize_Net_AIM
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 3.0
	 * @see Framework\SV_WC_Payment_Gateway::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Authorize.Net AIM Gateway', 'woocommerce-gateway-authorize-net-aim' );
	}


	/**
	 * Gets the plugin documentation URL
	 *
	 * @since 3.0
	 * @see Framework\SV_WC_Plugin::get_documentation_url()
	 * @return string
	 */
	public function get_documentation_url() {
		return 'http://docs.woocommerce.com/document/authorize-net-aim/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 3.4.0
	 * @see Framework\SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 3.14.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/authorize-net-aim/';
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 3.0
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 3.14.4
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Authorize_Net\AIM\Lifecycle( $this );
	}


} // end \WC_Authorize_Net_AIM


/**
 * Returns the One True Instance of Authorize.Net AIM
 *
 * @since 3.3.0
 * @return WC_Authorize_Net_AIM
 */
function wc_authorize_net_aim() {
	return WC_Authorize_Net_AIM::instance();
}
