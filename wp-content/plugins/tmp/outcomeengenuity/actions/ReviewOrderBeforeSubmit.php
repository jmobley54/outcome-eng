<?php
final class ReviewOrderBeforeSubmit {

  private static $instance = null;

  public static function get_instance() {
    if ( null == self::$instance ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function __construct() {
    \add_action( 'woocommerce_checkout_process', [ $this, 'verify_if_email_exists' ] );
  }

  public function applyService($service) {
    $this->service = $service;
  }

  public function verify_if_email_exists() {
    $items = WC()->cart->get_cart();
    foreach ( $items as $key => $values) {
      if ($values['product_id'] == \OE_ASSESSMENT_PRODUCT_ID || $values['product_id'] == \OE_FREE_ASSESSMENT_PRODUCT_ID) {
	    $email = filter_input(INPUT_POST, 'billing_email');

            $this->service->getAccessToken();

	    $result = $this->service->queryString(
	      '/accounts/state',
	      [ 'email' => $email ]
	    );

	    if ($result->account_exists == true) {
	      wc_add_notice(__('It looks like <strong>' . $email . '</strong> is already registered. Please contact Outcome Engenuity to add more users.'), 'error');
	    }

      }
    }
    
  }
}
