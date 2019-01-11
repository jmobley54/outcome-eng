<?php
/**
 * Plugin Name: WooCommerce Shipping Per Product v2
 * Plugin URI: https://woocommerce.com/products/per-product-shipping/
 * Description: Per product shipping allows you to define different shipping costs for products, based on customer location. These costs can be added to other shipping methods (requires WC 2.0), or used as a standalone shipping method.
 * Version: 2.2.15
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Requires at least: 3.3
 * Tested up to: 4.2
 * WC requires at least: 2.6
 * WC tested up to: 3.5
 *
 * Copyright: © 2009-2015 WooThemes.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 18590:ba16bebba1d74992efc398d575bf269e
 *
 * @package WC_Shipping_Per_Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'ba16bebba1d74992efc398d575bf269e', '18590' );

if ( is_woocommerce_active() ) {

	/**
	 * Wrapper class to init the plugin.
	 *
	 * @since 1.0.0
	 */
	class WC_Shipping_Per_Product_Init {

		/**
		 * Constructor.
		 */
		public function __construct() {
			define( 'PER_PRODUCT_SHIPPING_VERSION', '2.2.15' );
			define( 'PER_PRODUCT_SHIPPING_FILE', __FILE__ );

			if ( is_admin() ) {
				include_once( 'includes/class-wc-shipping-per-product-admin.php' );
			}

			include_once( 'includes/functions-wc-shipping-per-product.php' );

			register_activation_hook( __FILE__, array( $this, 'install' ) );

			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'load_shipping_method' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
			add_action( 'admin_init', array( $this, 'register_importer' ) );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'register_shipping_method' ) );
			add_filter( 'woocommerce_package_rates', array( $this, 'adjust_package_rates' ), 10, 2 );
			add_action( 'woocommerce_loaded', array( $this, 'load_post_wc_class' ) );
		}

		/**
		 * Loads any class that needs to check for WC loaded.
		 *
		 * @since 2.2.13
		 */
		public function load_post_wc_class() {
			require_once( dirname( __FILE__ ) . '/includes/class-wc-shipping-per-product-privacy.php' );
		}

		/**
		 * Installer.
		 */
		public function install() {
			include_once( 'installer.php' );
		}

		/**
		 * Load shipping method class.
		 */
		public function load_shipping_method() {
			include_once( 'includes/class-wc-shipping-per-product.php' );
		}

		/**
		 * Translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'woocommerce-shipping-per-product', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Filter plugin action links.
		 *
		 * @since 2.2.9
		 * @version 2.2.9
		 *
		 * @param array $links Plugin action links.
		 *
		 * @return array Plugin action links.
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=per_product' ) . '">' . __( 'Settings', 'woocommerce-shipping-per-product' ) . '</a>',
				'<a href="https://docs.woocommerce.com/">' . __( 'Support', 'woocommerce-shipping-per-product' ) . '</a>',
				'<a href="https://docs.woocommerce.com/document/per-product-shipping/">' . __( 'Docs', 'woocommerce-shipping-per-product' ) . '</a>',
			);
			return array_merge( $plugin_links, $links );
		}

		/**
		 * Show row meta on the plugin screen.
		 *
		 * @param array  $links Plugin Row Meta.
		 * @param string $file  Plugin Base file.
		 *
		 * @return array
		 */
		public function plugin_row_meta( $links, $file ) {
			if ( plugin_basename( __FILE__ ) === $file ) {
				$row_meta = array(
					'docs'    => '<a href="' . esc_url( apply_filters( 'woocommerce_per_product_shipping_docs_url', 'http://docs.woothemes.com/document/per-product-shipping/' ) ) . '" title="' . esc_attr( __( 'View Documentation', 'woocommerce-shipping-per-product' ) ) . '">' . __( 'Docs', 'woocommerce-shipping-per-product' ) . '</a>',
					'support' => '<a href="' . esc_url( apply_filters( 'woocommerce_per_product_shipping_support_url', 'http://support.woothemes.com/' ) ) . '" title="' . esc_attr( __( 'Visit Premium Customer Support Forum', 'woocommerce-shipping-per-product' ) ) . '">' . __( 'Premium Support', 'wc_shipping_per_products' ) . '</a>',
				);
				return array_merge( $links, $row_meta );
			}
			return (array) $links;
		}

		/**
		 * Register the importer.
		 */
		public function register_importer() {
			if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
				register_importer( 'woocommerce_per_product_shipping_csv', __( 'WooCommerce Per-product shipping rates (CSV)', 'woocommerce-shipping-per-product' ), __( 'Import <strong>per-product shipping rates</strong> to your store via a csv file.', 'woocommerce-shipping-per-product' ), array( $this, 'importer' ) );
			}
		}

		/**
		 * Load the importer.
		 */
		public function importer() {
			require_once ABSPATH . 'wp-admin/includes/import.php';

			if ( ! class_exists( 'WP_Importer' ) ) {
				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				if ( file_exists( $class_wp_importer ) ) {
					require $class_wp_importer;
				}
			}

			include_once( 'includes/class-wc-shipping-per-product-importer.php' );

			$importer = new WC_Shipping_Per_Product_Importer();
			$importer->dispatch();
		}

		/**
		 * Register the shipping method.
		 *
		 * @param array $methods Shipping methods.
		 *
		 * @return array Shipping methods.
		 */
		public function register_shipping_method( $methods ) {
			$methods[] = 'WC_Shipping_Per_Product';
			return $methods;
		}

		/**
		 * Adjust package rates.
		 *
		 * @param array $rates   Rates.
		 * @param array $package Package.
		 *
		 * @return array
		 */
		public function adjust_package_rates( $rates, $package ) {
	    	$_tax = new WC_Tax();

	    	if ( $rates ) {
		    	foreach ( $rates as $rate_id => $rate ) {

		    		// Skip free shipping.
		    		if ( 0 == $rate->cost && apply_filters( 'woocommerce_per_product_shipping_skip_free_method_' . $rate->method_id, true ) ) {
		    			continue;
		    		}

		    		// Skip self.
		    		if ( 'per_product' === $rate->method_id ) {
		    			continue;
		    		}

			    	if ( sizeof( $package['contents'] ) > 0 ) {
	    				foreach ( $package['contents'] as $item_id => $values ) {
							if ( $values['quantity'] > 0 ) {
								if ( $values['data']->needs_shipping() ) {

									$item_shipping_cost = 0;

									$rule = false;

									if ( $values['variation_id'] ) {
										$rule = woocommerce_per_product_shipping_get_matching_rule( $values['variation_id'], $package, false );
									}

									if ( false === $rule ) {
										$rule = woocommerce_per_product_shipping_get_matching_rule( $values['product_id'], $package, false );
									}

									if ( empty( $rule ) ) {
										continue;
									}

									$item_shipping_cost += (float) $rule->rule_item_cost * (int) $values['quantity'];
									$item_shipping_cost += (float) $rule->rule_cost;

									$rate->cost += $item_shipping_cost;

									$rate_options = get_option( 'woocommerce_' . $rate->get_method_id() . '_' . $rate->get_instance_id() . '_settings', true );


									if ( isset( $rate_options['tax_status'] ) && 'taxable' === $rate_options['tax_status'] ) {
										$tax_rates	= $_tax->get_shipping_tax_rates( $values['data']->get_tax_class() );
										$item_taxes = $_tax->calc_shipping_tax( $item_shipping_cost, $tax_rates );
										$taxes      = array();

										// Sum the item taxes.
										foreach ( array_keys( $rate->taxes + $item_taxes ) as $key ) {
											$taxes[ $key ] = ( isset( $item_taxes[ $key ] ) ? $item_taxes[ $key ] : 0 ) + ( isset( $rate->taxes[ $key ] ) ? $rate->taxes[ $key ] : 0 );
										}
										$rate->set_taxes( $taxes );
									}
								}
							}
						}
					}
		    	}
	    	}

	    	return $rates;
		}

	}

	new WC_Shipping_Per_Product_Init();
}
