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

namespace SkyVerge\WooCommerce\Authorize_Net\AIM;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_3_0 as Framework;

class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Performs any install tasks.
	 *
	 * @since 3.14.4
	 */
	protected function install() {

		// versions prior to 3.0 did not set a version option, so the upgrade method needs to be called manually
		if ( get_option( 'woocommerce_authorize_net_settings' ) ) {

			$this->upgrade( '2.1' );
		}
	}


	/**
	 * Performs any upgrade tasks based on the provided installed version.
	 *
	 * @since 3.14.4
	 *
	 * @param string $installed_version currently installed version
	 */
	protected function upgrade( $installed_version ) {

		// upgrade to 3.0
		if ( version_compare( $installed_version, '3.0', '<' ) ) {

			if ( $old_settings = get_option( 'woocommerce_authorize_net_settings' ) ) {

				$new_settings = array();

				// migrate from old settings
				$new_settings['enabled']                  = isset( $old_settings['enabled'] ) ? $old_settings['enabled'] : 'no';
				$new_settings['title']                    = isset( $old_settings['title'] ) ? $old_settings['title'] : '';
				$new_settings['description']              = isset( $old_settings['description'] ) ? $old_settings['description'] : '';
				$new_settings['enable_csc']               = isset( $old_settings['cvv'] ) ? $old_settings['cvv'] : 'yes';
				$new_settings['transaction_type']         = isset( $old_settings['salemethod'] ) && 'AUTH_ONLY' === $old_settings['salemethod'] ? 'authorization' : 'charge';
				$new_settings['environment']              = isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? 'test' : 'production';
				$new_settings['api_login_id']             = isset( $old_settings['apilogin'] ) ? $old_settings['apilogin'] : '';
				$new_settings['debug_mode']               = isset( $old_settings['debugon'] ) && 'yes' === $old_settings['debugon'] ? 'log' : 'off';
				$new_settings['api_transaction_key']      = isset( $old_settings['transkey'] ) ? $old_settings['transkey'] : '';
				$new_settings['test_api_login_id']        = isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? $new_settings['api_login_id'] : '';
				$new_settings['test_api_transaction_key'] = isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? $new_settings['api_transaction_key'] : '';

				// automatically activate legacy SIM gateway if the gateway URL is non-standard
				if ( isset( $old_settings['gatewayurl'] ) &&
					 'https://test.authorize.net/gateway/transact.dll' !== $old_settings['gatewayurl'] &&
					 'https://secure.authorize.net/gateway/transact.dll' !== $old_settings['gatewayurl'] ) {

					update_option( 'wc_authorize_net_aim_sim_active', true );
				}

				if ( isset( $old_settings['cardtypes'] ) && is_array( $old_settings['cardtypes'] ) ) {

					$new_settings['card_types'] = array();

					// map old to new
					foreach ( $old_settings['cardtypes'] as $card_type ) {

						switch ( $card_type ) {

							case 'MasterCard':
								$new_settings['card_types'][] = 'MC';
								break;

							case 'Visa':
								$new_settings['card_types'][] = 'VISA';
								break;

							case 'Discover':
								$new_settings['card_types'][] = 'DISC';
								break;

							case 'American Express':
								$new_settings['card_types'][] = 'AMEX';
								break;
						}
					}
				}

				// update to new settings
				update_option( 'woocommerce_authorize_net_aim_settings', $new_settings );

				// change option name for old settings
				update_option( 'woocommerce_authorize_net_sim_settings', $old_settings );
			}
		}

		// upgrade to 3.8.0
		if ( version_compare( $installed_version, '3.8.0', '<' ) ) {

			// update emulation gateway enabled option
			if ( get_option( 'wc_authorize_net_aim_sim_active', false ) ) {

				update_option( 'wc_authorize_net_aim_emulation_enabled', true );
				delete_option( 'wc_authorize_net_aim_sim_active' );
			}

			// migrate settings from legacy emulation gateway
			if ( $old_settings = get_option( 'woocommerce_authorize_net_sim_settings' ) ) {

				// base settings
				$new_settings = array(
					'enabled'                  => isset( $old_settings['enabled'] ) ? $old_settings['enabled'] : 'no',
					'title'                    => isset( $old_settings['title'] ) ? $old_settings['title'] : 'Credit Card',
					'description'              => isset( $old_settings['description'] ) ? $old_settings['description'] : 'Pay securely using your credit card.',
					'enable_csc'               => isset( $old_settings['cvv'] ) ? $old_settings['cvv'] : 'yes',
					'transaction_type'         => isset( $old_settings['salemethod'] ) && 'AUTH_ONLY' === $old_settings['salemethod'] ? 'authorization' : 'charge',
					'environment'              => isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? 'test' : 'production',
					'debug_mode'               => isset( $old_settings['debugon'] ) && 'yes' === $old_settings['debugon'] ? 'log' : 'off',
					'gateway_url'              => isset( $old_settings['gatewayurl'] ) ? $old_settings['gatewayurl'] : 'https://secure2.authorize.net/gateway/transact.dll',
					'api_login_id'             => isset( $old_settings['apilogin'] ) ? $old_settings['apilogin'] : '',
					'api_transaction_key'      => isset( $old_settings['transkey'] ) ? $old_settings['transkey'] : '',
					'test_gateway_url'         => isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? $old_settings['gatewayurl'] : 'https://test.authorize.net/gateway/transact.dll',
					'test_api_login_id'        => isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? $old_settings['apilogin'] : '',
					'test_api_transaction_key' => isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? $old_settings['transkey'] : '',
				);

				// card types
				if ( isset( $old_settings['cardtypes'] ) && is_array( $old_settings['cardtypes'] ) ) {

					$new_settings['card_types'] = array();

					// map old to new
					foreach ( $old_settings['cardtypes'] as $card_type ) {

						switch ( $card_type ) {

							case 'MasterCard':
								$new_settings['card_types'][] = 'MC';
								break;

							case 'Visa':
								$new_settings['card_types'][] = 'VISA';
								break;

							case 'Discover':
								$new_settings['card_types'][] = 'DISC';
								break;

							case 'American Express':
								$new_settings['card_types'][] = 'AMEX';
								break;
						}
					}
				}

				// set new settings
				update_option( 'woocommerce_authorize_net_aim_emulation_settings', $new_settings );

				// remove old settings
				delete_option( 'woocommerce_authorize_net_sim_settings' );
			}
		}
	}


}
