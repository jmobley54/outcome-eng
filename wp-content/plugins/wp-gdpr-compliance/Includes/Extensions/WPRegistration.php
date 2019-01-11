<?php

namespace WPGDPRC\Includes\Extensions;

use WPGDPRC\Includes\Helper;
use WPGDPRC\Includes\Integration;


class WPRegistration {

	const ID = 'wp_registration';
	/** @var null */
	private static $instance = null;

	public function addField() { ?>
        <p>
            <label><input type="checkbox" name="wpgdprc_consent"
                          value="1"/> <?php echo Integration::getCheckboxText( self::ID ) ?><abbr class="wpgdprc-required" title=" <?php echo Integration::getRequiredMessage(self::ID) ?> ">*</abbr></label></p>
        </p><br>
		<?php

	}

	/**
	 * @param $errors
	 * @param $sanitized_user_login
	 * @param $user_email
	 *
	 * @return mixed
	 */
	public function validateGDPRCheckbox( $errors, $sanitized_user_login, $user_email ) {
		if ( ! isset( $_POST['wpgdprc_consent'] ) ) {
			$errors->add( 'gdpr_consent_error', '<strong>ERROR</strong>: ' . Integration::getErrorMessage( self::ID ) );
		}
		return $errors;
	}

	/**
	 *
	 */
	public function logGivenGDPRConsent() {

		if ( isset( $_POST['user_email'] ) ) {

			global $wpdb;

			$wpdb->insert( $wpdb->prefix . 'wpgdprc_log', array(
				'plugin_id'    => self::ID,
				'user'         => Helper::anonymizeEmail( $_POST['user_email'] ),
				'ip_address'   => Helper::anonymizeIP( Helper::getClientIpAddress() ),
				'date_created' => Helper::localDateTime(time())->format('Y-m-d H:i:s'),
				'log'          => 'user has given consent when registering',
				'consent_text' => Integration::getCheckboxText( self::ID )
			) );
		}

	}


	/**
	 * @return null|WPRegistration
	 */
	public static function getInstance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}