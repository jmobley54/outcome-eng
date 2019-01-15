<?php
/**
 * WC_CSP_Restrict_Shipping_Countries class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Restrict Shipping Countries.
 *
 * @class    WC_CSP_Restrict_Shipping_Countries
 * @version  1.5.0
 */
class WC_CSP_Restrict_Shipping_Countries extends WC_CSP_Restriction implements WC_CSP_Checkout_Restriction, WC_CSP_Cart_Restriction {

	public function __construct() {

		$this->id                       = 'shipping_countries';
		$this->title                    = __( 'Shipping Countries &amp; States', 'woocommerce-conditional-shipping-and-payments' );
		$this->description              = __( 'Restrict the allowed shipping countries based on product-related constraints.', 'woocommerce-conditional-shipping-and-payments' );
		$this->validation_types         = array( 'checkout', 'cart' );
		$this->has_admin_product_fields = true;
		$this->supports_multiple        = true;

		$this->has_admin_global_fields  = true;
		$this->method_title             = __( 'Shipping Country Restrictions', 'woocommerce-conditional-shipping-and-payments' );
		$this->restricted_key           = 'countries';

		// Shows a woocommerce error on the 'woocommerce_review_order_before_cart_contents' hook when country restrictions apply.
		add_action( 'woocommerce_review_order_before_cart_contents', array( $this, 'excluded_country_notice' ) );

		// Save global settings.
		add_action( 'woocommerce_update_options_restrictions_' . $this->id, array( $this, 'update_global_restriction_data' ) );

		// Initialize global settings.
		$this->init_form_fields();
	}

	/**
	 * Declare 'admin_global_fields' type, generated by 'generate_admin_global_fields_html'.
	 *
	 * @return void
	 */
	function init_form_fields() {

		$this->form_fields = array(
			'admin_global_fields' => array(
				'type' => 'admin_global_fields'
				)
			);
	}

	/**
	 * Generates the 'admin_global_fields' field type, which is based on metaboxes.
	 *
	 * @return string
	 */
	function generate_admin_global_fields_html() {
		?><p>
			<?php echo __( 'Restrict the shipping countries allowed at checkout. Complex rules can be created by adding multiple restrictions. Each individual restriction becomes active when all defined conditions match.', 'woocommerce-conditional-shipping-and-payments' ); ?>
		</p><?php

		$this->get_admin_global_metaboxes_html();
	}

	/**
	 * Display admin options.
	 *
	 * @param  int    $index
	 * @param  array  $options
	 * @return string
	 */
	public function get_admin_fields_html( $index, $options = array(), $field_type = 'global' ) {

		$description = '';
		$countries   = array();
		$states      = array();
		$message     = '';

		if ( isset( $options[ 'description' ] ) ) {
			$description = $options[ 'description' ];
		}

		if ( isset( $options[ 'countries' ] ) ) {
			$countries = $options[ 'countries' ];
		}

		if ( isset( $options[ 'states' ] ) ) {
			$states = $options[ 'states' ];
		}

		if ( ! empty( $options[ 'message' ] ) ) {
			$message = $options[ 'message' ];
		}

		$shipping_countries = WC()->countries->get_shipping_countries();

		?>
		<div class="woocommerce_restriction_form">
			<div class="sw-form-field">
				<label>
					<?php _e( 'Short Description', 'woocommerce-conditional-shipping-and-payments' ); ?>
				</label>
				<div class="sw-form-content">
					<input class="short_description" name="restriction[<?php echo $index; ?>][description]" id="restriction_<?php echo $index; ?>_short_description" placeholder="<?php _e( 'Optional short description for this rule&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>" value="<?php echo $description; ?>"/>
				</div>
			</div>
			<div class="sw-form-field">
				<label><?php _e( 'Exclude Countries', 'woocommerce-conditional-shipping-and-payments' ); ?></label>
				<div class="sw-form-content select-field">
					<select name="restriction[<?php echo $index; ?>][countries][]" class="csp_shipping_countries multiselect wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Select Countries&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
						<?php
							foreach ( $shipping_countries as $key => $val ) {
								echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $countries ), true, false ) . '>' . $val . '</option>';
							}
						?>
					</select>
					<span class="restriction_form_row">
						<a class="wccsp_select_all button" href="#"><?php _e( 'All', 'woocommerce' ); ?></a>
						<a class="wccsp_select_none button" href="#"><?php _e( 'None', 'woocommerce' ); ?></a>
					</span>
				</div>
			</div>
			<div class="sw-form-field">
				<label><?php _e( 'Exclude States / Regions', 'woocommerce-conditional-shipping-and-payments' ); ?></label>
				<div class="sw-form-content select-field">

					<select name="restriction[<?php echo $index; ?>][states][]" class="csp_shipping_states multiselect wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Select States / Regions&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
						<?php
						if ( ! empty( $countries ) ) {
							foreach ( $countries as $country_key ) {

								if ( ! isset( $shipping_countries[ $country_key ] ) ) {
									continue;
								}

								$country_value = $shipping_countries[ $country_key ];

								if ( $country_states = WC()->countries->get_states( $country_key ) ) {
									echo '<optgroup label="' . esc_attr( $country_value ) . '">';
										foreach ( $country_states as $state_key => $state_value ) {
											echo '<option value="' . esc_attr( $country_key ) . ':' . $state_key . '"';
											if ( ! empty( $states[ $country_key ] ) && in_array( $state_key, $states[ $country_key ] ) ) {
												echo ' selected="selected"';
											}
											echo '>' . $country_value . ' &mdash; ' . $state_value . '</option>';
										}
									echo '</optgroup>';
								}
							}
						}
						?>
					</select>
					<span class="form_row restriction_form_row">
						<a class="wccsp_select_all button" href="#"><?php _e( 'All', 'woocommerce' ); ?></a>
						<a class="wccsp_select_none button" href="#"><?php _e( 'None', 'woocommerce' ); ?></a>
					</span>
				</div>
			</div>
			<div class="sw-form-field">
				<label>
					<?php _e( 'Custom Notice', 'woocommerce-conditional-shipping-and-payments' ); ?>
					<?php

						if ( $field_type === 'global' ) {
							$tiptip = __( 'Defaults to:<br/>&quot;Unfortunately your order cannot be shipped {to_excluded_destination}. To complete your order, please select an alternative shipping country / state.&quot;<br/>When conditions are defined, resolution instructions are added to the default message.', 'woocommerce-conditional-shipping-and-payments' );
						} else {
							$tiptip = __( 'Defaults to:<br/>&quot;Unfortunately your order cannot be shipped {to_excluded_destination}. To complete your order, please select an alternative shipping country / state, or remove {product} from your cart.&quot;<br/>When conditions are defined, resolution instructions are added to the default message.', 'woocommerce-conditional-shipping-and-payments' );
						}
					?>
				</label>
				<div class="sw-form-content">
					<textarea class="custom_message" name="restriction[<?php echo $index; ?>][message]" id="restriction_<?php echo $index; ?>_message" placeholder="" rows="2" cols="20"><?php echo $message; ?></textarea>
					<?php
						echo WC_CSP_Core_Compatibility::wc_help_tip( $tiptip );

						if ( $field_type === 'global' ) {
							$tip = __( 'Define a custom checkout error message to show when selecting an excluded shipping destination. You may include <code>{to_excluded_destination}</code> and have it substituted by the selected shipping country / state.', 'woocommerce-conditional-shipping-and-payments' );
						} else {
							$tip = __( 'Define a custom checkout error message to show when selecting an excluded shipping destination. You may include <code>{product}</code> and <code>{to_excluded_destination}</code> and have them substituted by the actual product title and the excluded country / state.', 'woocommerce-conditional-shipping-and-payments' );
						}

						echo '<span class="description">' . $tip . '</span>';
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display a short summary of the restriction's settings.
	 *
	 * @param  array  $options
	 * @return string
	 */
	public function get_options_description( $options ) {

		if ( ! empty( $options[ 'description' ] ) ) {
			return $options[ 'description' ];
		}

		$country_strings = array();
		$countries       = array();

		if ( isset( $options[ 'countries' ] ) ) {
			$countries = $options[ 'countries' ];
		}

		$shipping_countries = WC()->countries->get_shipping_countries();

		foreach ( $shipping_countries as $key => $val ) {

			if ( in_array( $key, $countries ) ) {
				$country_strings[] = $val;
			}
		}

		return trim( implode( ', ', $country_strings ), ', ' );
	}

	/**
	 * Display options on the global Restrictions page.
	 *
	 * @param  int    $index    restriction metabox unique id
	 * @param  string $options  metabox options
	 * @return string
	 */
	public function get_admin_global_fields_html( $index, $options = array() ) {

		$this->get_admin_fields_html( $index, $options, 'global' );
	}

	/**
	 * Display options on the product Restrictions write-panel.
	 *
	 * @param  int    $index    restriction metabox unique id
	 * @param  string $options  metabox options
	 * @return string
	 */
	public function get_admin_product_fields_html( $index, $options = array() ) {
		?><div class="restriction-description">
			<?php echo __( 'Restrict the allowed shipping countries when an order contains this product.', 'woocommerce-conditional-shipping-and-payments' ); ?>
		</div><?php

		$this->get_admin_fields_html( $index, $options, 'product' );
	}

	/**
	 * Validate, process and return options.
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_fields( $posted_data ) {

		$processed_data = array();

		$processed_data[ 'countries' ] = array();

		if ( ! empty( $posted_data[ 'countries' ] ) ) {
			$processed_data[ 'countries' ] = array_map( 'stripslashes', $posted_data[ 'countries' ] );

			if ( ! empty( $posted_data[ 'states' ] ) ) {
				$processed_data[ 'states' ] = array();
				$country_states             = array_map( 'stripslashes', $posted_data[ 'states' ] );

				foreach ( $country_states as $country_state_key ) {
					$country_state_key = explode( ':', $country_state_key );
					$country_key       = current( $country_state_key );
					$state_key         = end( $country_state_key );

					if ( in_array( $country_key, $processed_data[ 'countries' ] ) ) {
						$processed_data[ 'states' ][ $country_key ][] = $state_key;
					}
				}
			}
		} else {
			return false;
		}

		if ( ! empty( $posted_data[ 'message' ] ) ) {
			$processed_data[ 'message' ] = wp_kses_post( stripslashes( $posted_data[ 'message' ] ) );
		}

		if ( ! empty( $posted_data[ 'description' ] ) ) {
			$processed_data[ 'description' ] = strip_tags ( stripslashes( $posted_data[ 'description' ] ) );
		}

		return $processed_data;
	}

	/**
	 * Validate, process and return product metabox options.
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_product_fields( $posted_data ) {

		$processed_data = $this->process_admin_fields( $posted_data );

		if ( ! $processed_data ) {

			WC_Admin_Meta_Boxes::add_error( sprintf( __( 'Restriction #%s was not saved. Before saving a &quot;Shipping Countries&quot; restriction, remember to add at least one shipping country to the exclusions list.', 'woocommerce-conditional-shipping-and-payments' ), $posted_data[ 'index' ] ) );
			return false;
		}

		return $processed_data;
	}

	/**
	 * Validate, process and return global settings.
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_global_fields( $posted_data ) {

		$processed_data = $this->process_admin_fields( $posted_data );

		if ( ! $processed_data ) {

			WC_CSP_Admin_Notices::add_notice( sprintf( __( 'Restriction #%s was not saved. Before saving a &quot;Shipping Countries&quot; restriction, remember to add at least one shipping country to the exclusions list.', 'woocommerce-conditional-shipping-and-payments' ), $posted_data[ 'index' ] ), 'error', true );
			return false;
		}

		return $processed_data;
	}

	/**
	 * Shows a woocommerce error on the 'woocommerce_review_order_before_cart_contents' hook when country restrictions apply.
	 *
	 * @return void
	 */
	public function excluded_country_notice() {

		if ( defined( 'WOOCOMMERCE_CHECKOUT' ) ) {

			$result = $this->check_restriction();

			if ( $result->has_messages() ) {
				foreach ( $result->get_messages() as $message ) {
					wc_add_notice( $message[ 'text' ], $message[ 'type' ] );
				}
			}
		}
	}

	/**
	 * Evaluate restriction objectives and return WC_CSP_Check_Result object.
	 *
	 * @return  WC_CSP_Check_Result
	 */
	private function check_restriction( $msg_type = 'error' ) {

		$result = new WC_CSP_Check_Result();

		$cart_contents = WC()->cart->get_cart();

		/**
		 * 'woocommerce_csp_shipping_packages' filter.
		 *
		 * Alters the shipping packages seen by CSP's validation routine.
		 *
		 * @since  1.4.0
		 * @param  array  $packages
		 */
		$shipping_packages      = apply_filters( 'woocommerce_csp_shipping_packages', WC()->shipping->get_packages() );
		$shipping_package_index = 0;

		// Initialize args.
		$args                    = array();
		$args[ 'package_count' ] = sizeof( $shipping_packages );

		/* ----------------------------------------------------------------- */
		/* Product Restrictions
		/* ----------------------------------------------------------------- */

		// Loop package contents.
		if ( ! empty( $shipping_packages ) ) {
			foreach ( $shipping_packages as $shipping_package ) {

				$shipping_package_index++;

				if ( empty( $shipping_package[ 'contents' ] ) ) {
					continue;
				}

				// Add extra args.
				$args[ 'package' ]       = $shipping_package;
				$args[ 'package_index' ] = $shipping_package_index;

				// Get current package destination.
				$shipping_country = $shipping_package[ 'destination' ][ 'country' ];
				$shipping_state   = $shipping_package[ 'destination' ][ 'state' ];

				foreach ( $shipping_package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

					$product = $cart_item_data[ 'data' ];

					$product_restriction_data = $this->get_product_restriction_data( $product );
					$product_rules_map        = $this->get_matching_rules_map( $product_restriction_data, array( 'country' => $shipping_country, 'state' => $shipping_state ), $args );

					foreach ( $product_rules_map as $rule_index => $excluded_country_locales ) {

						if ( ! empty( $excluded_country_locales ) ) {
							$result->add( 'country_excluded_by_product_restriction', $this->get_resolution_message( $product_restriction_data[ $rule_index ], 'product', array_merge( $args, array( 'cart_item_data' => $cart_item_data ) ) ), $msg_type );
						}
					}
				}
			}
		}

		/* ----------------------------------------------------------------- */
		/* Global Restrictions
		/* ----------------------------------------------------------------- */

		// Grab global restrictions.
		$global_restriction_data = $this->get_global_restriction_data();
		$shipping_package_index  = 0;

		// Check once for each package.
		if ( ! empty( $shipping_packages ) ) {
			foreach ( $shipping_packages as $shipping_package ) {

				$shipping_package_index++;

				if ( empty( $shipping_package[ 'contents' ] ) ) {
					continue;
				}

				// Add extra args.
				$args[ 'package' ]       = $shipping_package;
				$args[ 'package_index' ] = $shipping_package_index;

				// Get current package destination.
				$shipping_country = $shipping_package[ 'destination' ][ 'country' ];
				$shipping_state   = $shipping_package[ 'destination' ][ 'state' ];

				$global_rules_map = $this->get_matching_rules_map( $global_restriction_data, array( 'country' => $shipping_country, 'state' => $shipping_state ), $args );

				foreach ( $global_rules_map as $rule_index => $excluded_country_locales ) {

					if ( ! empty( $excluded_country_locales ) ) {
						$result->add( 'country_excluded_by_global_restriction', $this->get_resolution_message( $global_restriction_data[ $rule_index ], 'global', $args ), $msg_type );
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Generate map data for each active rule.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $payload
	 * @param  array  $restriction
	 * @param  bool   $include_data
	 * @return array
	 */
	protected function generate_rules_map_data( $payload, $restriction, $include_data ) {
		// Note:
		// We need to alter the restriction data structure in order to make $this->restricted_key work as expected.
		// Include one restricted key that contains both countries & states restriction data.
		// For details, see: https://github.com/somewherewarm/woocommerce-conditional-shipping-and-payments/issues/168#issuecomment-422400070
		return in_array( $payload[ 'country' ], $restriction[ 'countries' ] ) && ( empty( $restriction[ 'states' ][ $payload[ 'country' ] ] ) || in_array( $payload[ 'state' ], $restriction[ 'states' ][ $payload[ 'country' ] ] ) ) ? array( $payload ) : array();
	}

	/**
	 * Generate resolution message.
	 *
	 * @since  1.4.0
	 *
	 * @param  array   $restriction
	 * @param  string  $context
	 * @param  array   $args
	 * @return string
	 */
	protected function get_resolution_message( $restriction, $context, $args = array() ) {

		$message   = '';
		$locale    = WC()->countries->get_country_locale();
		$countries = WC()->countries->get_countries();

		$package_count  = $args[ 'package_count' ];
		$package_index  = $args[ 'package_index' ];

		// From currect package.
		$shipping_country = $args[ 'package' ][ 'destination' ][ 'country' ];
		$shipping_state   = $args[ 'package' ][ 'destination' ][ 'state' ];
		$states           = WC()->countries->get_states( $shipping_country );

		// Generate destination variables.
		if ( empty( $restriction[ 'states' ][ $shipping_country ] ) ) {

			$destination      = $countries[ $shipping_country ];
			$to_destination   = sprintf( _x( '%1$s %2$s', 'to country destination', 'woocommerce-conditional-shipping-and-payments' ), WC()->countries->shipping_to_prefix(), $destination );
			$destination_type = __( 'Country', 'woocommerce-conditional-shipping-and-payments' );

		} elseif ( in_array( $shipping_state, $restriction[ 'states' ][ $shipping_country ] ) ) {

			$destination      = $states[ $shipping_state ];
			$to_destination   = sprintf( _x( 'to %s', 'to state destination', 'woocommerce-conditional-shipping-and-payments' ), $destination );
			$destination_type = isset( $locale[ $shipping_country ][ 'state' ][ 'label' ] ) ? $locale[ $shipping_country ][ 'state' ][ 'label' ] : __( 'State / County', 'woocommerce' );
		}

		if ( 'product' === $context ) {

			$product = $args[ 'cart_item_data' ][ 'data' ];

			if ( ! empty( $restriction[ 'message' ] ) ) {

				$message 	= str_replace( array( '{product}', '{to_excluded_destination}', '{excluded_package_index}' ), array( '&quot;%1$s&quot;', '%2$s', '%4$s' ), $restriction[ 'message' ] );
				$resolution = '';

			} else {

				$conditions_resolution = $this->get_conditions_resolution( $restriction, $args );

				if ( $conditions_resolution ) {

					if ( $package_count === 1 ) {
						$resolution = sprintf( __( 'To have &quot;%1$s&quot; shipped %2$s, please %3$s. Otherwise, select an alternative shipping %4$s, or remove &quot;%1$s&quot; from your cart.', 'woocommerce-conditional-shipping-and-payments' ), $product->get_title(), $to_destination, $conditions_resolution, $destination_type );
					} else {
						$resolution = sprintf( __( 'To have &quot;%1$s&quot; shipped %2$s, please %3$s. Otherwise, select an alternative shipping %4$s, or remove &quot;%1$s&quot; from package #%5$s.', 'woocommerce-conditional-shipping-and-payments' ), $product->get_title(), $to_destination, $conditions_resolution, $destination_type, $package_index );
					}

				} else {

					if ( $package_count === 1 ) {
						$resolution = sprintf( __( 'To complete your order, please select an alternative shipping %1$s, or remove &quot;%2$s&quot; from your cart.', 'woocommerce-conditional-shipping-and-payments' ), $destination_type, $product->get_title() );
					} else {
						$resolution = sprintf( __( 'To complete your order, please select an alternative shipping %1$s, or remove &quot;%2$s&quot; from package #%3$s.', 'woocommerce-conditional-shipping-and-payments' ), $destination_type, $product->get_title(), $package_index );
					}
				}

				$message = __( 'Unfortunately, &quot;%1$s&quot; is not eligible for shipping %2$s. %3$s', 'woocommerce-conditional-shipping-and-payments' );
			}

			$message = sprintf( $message, $product->get_title(), $to_destination, $resolution, $package_index );

		} elseif ( 'global' === $context ) {

			if ( ! empty( $restriction[ 'message' ] ) ) {

				$message 	= str_replace( array( '{to_excluded_destination}', '{excluded_package_index}' ), array( '%1$s', '%3$s' ), $restriction[ 'message' ] );
				$resolution = '';

			} else {

				$conditions_resolution = $this->get_conditions_resolution( $restriction, $args );

				if ( $conditions_resolution ) {

					if ( $package_count === 1 ) {
						$resolution = sprintf( __( 'To have your order shipped %1$s, please %2$s. Otherwise, select an alternative shipping %3$s.', 'woocommerce-conditional-shipping-and-payments' ), $to_destination, $conditions_resolution, $destination_type );
					} else {
						$resolution = sprintf( __( 'To have package #%4$s shipped %1$s, please %2$s. Otherwise, select an alternative shipping %3$s.', 'woocommerce-conditional-shipping-and-payments' ), $to_destination, $conditions_resolution, $destination_type, $package_index );
					}

				} else {

					$resolution = sprintf( __( 'To complete your order, please select an alternative shipping %1$s.', 'woocommerce-conditional-shipping-and-payments' ), $destination_type );
				}

				if ( $package_count === 1 ) {
					$message = __( 'Unfortunately your order cannot be shipped %1$s. %2$s', 'woocommerce-conditional-shipping-and-payments' );
				} else {
					$message = __( 'Unfortunately package #%3$s cannot be shipped %1$s. %2$s', 'woocommerce-conditional-shipping-and-payments' );
				}
			}

			$message = sprintf( $message, $to_destination, $resolution, $package_index );
		}

		return $message;
	}

	/**
	 * Validate order checkout and return errors if restrictions apply.
	 *
	 * @param  array  $posted
	 * @return void
	 */
	public function validate_checkout( $posted ) {
		return $this->check_restriction();
	}

	/**
	 * Validate cart and return errors if restrictions apply.
	 *
	 * @return void
	 */
	public function validate_cart() {

		if ( ! is_checkout() && ! defined( 'WOOCOMMERCE_CHECKOUT' ) && get_option( 'woocommerce_enable_shipping_calc' ) === 'yes' ) {
			return $this->check_restriction( 'notice' );
		} else {
			return new WC_CSP_Check_Result();
		}
	}
}