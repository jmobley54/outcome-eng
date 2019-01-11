<?php /* phpcs:ignore */

if ( defined( 'ABSPATH' ) && ! class_exists( 'WJECF_Admin_Data_Update' ) ) {
	class WJECF_Admin_Data_Update extends Abstract_WJECF_Plugin {

		public function __construct() {
			$this->set_plugin_data(
				array(
					'description'     => __( 'Automatically update data when a new version of this plugin is installed.', 'woocommerce-jos-autocoupon' ),
					'dependencies'    => array(),
					'can_be_disabled' => true,
				)
			);
		}

		public function init_admin_hook() {
			$this->auto_data_update();
		}

		//Upgrade database on version change
		public function auto_data_update() {
			if ( ! class_exists( 'WC_Coupon' ) ) {
				return;
			}

			//WJECF()->set_option('db_version', 0); // Will force all upgrades
			global $wpdb;
			$prev_version    = WJECF()->get_option( 'db_version' );
			$current_version = $prev_version;

			//DB_VERSION 1: Since 2.1.0-b5
			if ( $current_version < 1 ) {
				//RENAME meta_key _wjecf_matching_product_qty TO _wjecf_min_matching_product_qty
				$where = array( 'meta_key' => '_wjecf_matching_product_qty' );
				$set   = array( 'meta_key' => '_wjecf_min_matching_product_qty' );
				$wpdb->update( _get_meta_table( 'post' ), $set, $where );

				//RENAME meta_key woocommerce-jos-autocoupon TO _wjecf_is_auto_coupon
				$where = array( 'meta_key' => 'woocommerce-jos-autocoupon' );
				$set   = array( 'meta_key' => '_wjecf_is_auto_coupon' );
				$wpdb->update( _get_meta_table( 'post' ), $set, $where );
				//Now we're version 1
				$current_version = 1;
			}

			//DB_VERSION 2: Since 2.3.3-b3 No changes; but used to omit message if 2.3.3-b3 has been installed before
			if ( $current_version < 2 ) {
				$current_version = 2;
			}

			//DB_VERSION 3: Since 3.0.0
			if ( $current_version < 3 ) {
				$disabled_plugins = WJECF()->get_option( 'disabled_plugins' );
				foreach ( $disabled_plugins as $key => $value ) {
					//Remove WJECF_ prefix and replace '_' by '-'. e.g. 'WJECF_Admin_Settings' -> 'admin-settings'
					$disabled_plugins[ $key ] = Abstract_WJECF_Plugin::sanitize_plugin_name( $value );
				}
				$current_version = 3;
			}

			if ( $current_version > 3 ) {
				WJECF_ADMIN()->enqueue_notice( __( 'Please note, you\'re using an older version of this plugin, while the data was upgraded to a newer version.', 'woocommerce-jos-autocoupon' ), 'notice-warning' );
			}

			//An upgrade took place?
			if ( $current_version != $prev_version ) {
				// Set version and write options to database
				WJECF()->set_option( 'db_version', $current_version );
				WJECF()->save_options();

				WJECF_ADMIN()->enqueue_notice( __( 'Data succesfully upgraded to the newest version.', 'woocommerce-jos-autocoupon' ), 'notice-success' );
			}
		}
	}
}
