<?php
/**
 * WC_CSP_Condition_Shipping_Postcode class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.3.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Zip Code Condition.
 *
 * @class    WC_CSP_Condition_Shipping_Postcode
 * @version  1.4.0
 */
class WC_CSP_Condition_Shipping_Postcode extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'zip_code';
		$this->title                          = __( 'Shipping Postcode', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions  = array( 'shipping_methods', 'payment_gateways' );
		$this->supported_product_restrictions = array( 'shipping_methods', 'payment_gateways' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always return false (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		return __( 'choose a valid shipping postcode', 'woocommerce-conditional-shipping-and-payments' );
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  string $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$is_matching_package = false;

		if ( ! empty( $args[ 'order' ] ) ) {

			$order = $args[ 'order' ];

			$order_postcode      = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $order->get_shipping_postcode() : $order->shipping_postcode;
			$postcode            = WC_CSP_Core_Compatibility::wc_normalize_postcode( wc_clean( $order_postcode ) );
			$is_matching_package = $this->is_matching_package( $postcode, $data );

		} elseif ( ! empty( $args[ 'package' ] ) ) {

			$package = $args[ 'package' ];

			if ( ! empty( $package[ 'destination' ][ 'postcode' ] ) ) {

				$postcode            = WC_CSP_Core_Compatibility::wc_normalize_postcode( wc_clean( $package[ 'destination' ][ 'postcode' ] ) );
				$is_matching_package = $this->is_matching_package( $postcode, $data );

			} else {

				if ( 'yes' === get_option( 'woocommerce_shipping_cost_requires_address' ) ) {
					$is_matching_package = true;
				}
			}

		} else {

			$shipping_packages = WC()->shipping->get_packages();

			if ( ! empty( $shipping_packages ) ) {
				foreach ( $shipping_packages as $shipping_package ) {

					$postcode = WC_CSP_Core_Compatibility::wc_normalize_postcode( wc_clean( $shipping_package[ 'destination' ][ 'postcode' ] ) );

					if ( $this->is_matching_package( $postcode, $data ) ) {
						$is_matching_package = true;
						break;
					}
				}
			}
		}

		return $is_matching_package;
	}

	/**
	 * Condition matching package?
	 *
	 * @since  1.4.0
	 *
	 * @param  string $postcode
	 * @param  array  $data
	 * @return boolean
	 */
	protected function is_matching_package( $postcode, $data ) {

		$is_matching      = false;
		$postcode_objects = array();

		foreach ( $data[ 'value' ] as $validation_postcode ) {

			$postcode_object                = new stdClass();
			$postcode_object->location_code = trim( strtoupper( str_replace( chr( 226 ) . chr( 128 ) . chr( 166 ), '...', $validation_postcode ) ) );
			$postcode_object->value         = 0;
			$postcode_objects[]             = $postcode_object;
		}

		$matches = wc_postcode_location_matcher( $postcode, $postcode_objects, 'value', 'location_code' );

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) && ! empty( $matches ) ) {
			$is_matching = true;
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) && empty( $matches ) ) {
			$is_matching = true;
		}

		return $is_matching;
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( isset( $posted_condition_data[ 'value' ] ) ) {

			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = array_filter( array_map( 'strtoupper', array_map( 'wc_clean', explode( "\n", $posted_condition_data[ 'value' ] ) ) ) );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}
	/**
	 * Get cart total conditions content for admin restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier  = '';
		$zip_codes = '';

		if ( ! empty( $condition_data[ 'value' ] ) && is_array( $condition_data[ 'value' ] ) ) {
			$zip_codes = implode( "\n", $condition_data[ 'value' ] );
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
						<option value="in" <?php selected( $modifier, 'in', true ) ?>><?php echo __( 'is', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-in" <?php selected( $modifier, 'not-in', true ) ?>><?php echo __( 'is not', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<textarea class="input-text" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value]" placeholder="<?php _e( 'List 1 postcode per line&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>" cols="25" rows="5"><?php echo $zip_codes; ?></textarea>
				<span class="description"><?php _e( 'Postcodes containing wildcards (e.g. CB23*) and fully numeric ranges (e.g. <code>90210...99000</code>) are also supported.', 'woocommerce' ) ?></span>
			</div>
		</div>
		<?php
	}
}
