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
 * @package   WC-Gateway-Authorize-Net-AIM/API/Request
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2019, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_3_0 as Framework;


/**
 * Authorize.Net AIM API transaction details response class.
 *
 * @since 3.14.0
 */
class WC_Authorize_Net_AIM_API_Transaction_Details_Response extends WC_Authorize_Net_AIM_API_Response {


	/** transaction status for an authorized transaction, pending capture */
	const STATUS_AUTHORIZED = 'authorizedPendingCapture';

	/** transaction status for a captured transaction, pending settlement */
	const STATUS_CAPTURED = 'capturedPendingSettlement';

	/** transaction status for a fully settled transaction */
	const STATUS_SETTLED = 'settledSuccessfully';


	/**
	 * Determines if this transaction has been authorized but not yet captured.
	 *
	 * @since 3.14.0
	 *
	 * @return bool
	 */
	public function is_authorized() {

		return self::STATUS_AUTHORIZED === $this->get_transaction_status();
	}


	/**
	 * Determines if this transaction has been captured.
	 *
	 * Settled transactions are also considered to have been captured.
	 *
	 * @since 3.14.0
	 *
	 * @return bool
	 */
	public function is_captured() {

		return self::STATUS_CAPTURED === $this->get_transaction_status() || $this->is_settled();
	}


	/**
	 * Determines if this transaction is settled.
	 *
	 * @since 3.14.0
	 *
	 * @return bool
	 */
	public function is_settled() {

		return self::STATUS_SETTLED === $this->get_transaction_status();
	}


	/**
	 * Gets the transaction status.
	 *
	 * @since 3.14.0
	 *
	 * @return string|null
	 */
	public function get_transaction_status() {

		return isset( $this->response_xml->transaction->transactionStatus ) ? (string) $this->response_xml->transaction->transactionStatus : null;
	}


	/**
	 * Determines if this was a test request.
	 *
	 * @since 3.14.0
	 *
	 * @return false
	 */
	public function is_test_request() {

		return false;
	}


}
