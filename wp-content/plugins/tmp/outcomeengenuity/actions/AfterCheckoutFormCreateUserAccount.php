<?php
final class AfterCheckoutFormCreateUserAccount{

  private static $instance = null;

  public static function get_instance() {
    if ( null == self::$instance ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function __construct() {
      \add_action( 'woocommerce_thankyou', [ $this, 'create_user_account_for_products' ], 20 );
  }
  
  public function applyService($service) {
    $this->service = $service;
  }

  public function create_user_account_for_products($order_id) {
    $order = new WC_Order( $order_id );
    $items = $order->get_items();
    foreach ( $items as $item ) {
      if ($item->get_product_id() == \OE_ASSESSMENT_PRODUCT_ID ) {
        $this->create_user_account($order, $item, false);
      } elseif ($item->get_product_id() == \OE_FREE_ASSESSMENT_PRODUCT_ID ) {
        $this->create_user_account($order, $item, true);
      }
    }
  }

  public function create_user_account($order, $item, $free=false) {
    $this->service->getAccessToken();

    // If the company name isn't set -- not a required WC field.
    $company_name = $order->get_billing_company();
    if ( empty($company_name) ) {
       $company_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
    }

    $result = $this->service->post('/accounts',
      [
        'name' => $company_name,
        'admin_email' => $order->get_billing_email(),
        'admin_first_name' => $order->get_billing_first_name(),
        'admin_last_name' => $order->get_billing_last_name(),
        'num_users' => $item->get_quantity(),
        'free_trial' => $free 
      ]
    );
  }

}
