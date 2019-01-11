<?php
namespace OutcomeEngenuity;

use GuzzleHttp\Client;

class Skilljar {
  const ENDPOINT = 'https://api.skilljar.com/v1';

  const OE_SKILLJAR_TOKEN = 'sk-live-6a273de082ac212844fa9e2c9ecb02ad8dce5301';
  const OE_SKILLJAR_DOMAIN = 'oe-academy.skilljar.com';
  const OE_SKILLJAR_POOL_ID = '2k7l4qgo8oagh';

  private static $instance = null;

  public static function get_instance() {
    if ( null == self::$instance ) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  public static function generate_token() {
    $bytes = random_bytes(5);
    return strtoupper(bin2hex($bytes));
  }

  public function __construct() {
    \add_action( 'woocommerce_payment_complete', [ $this, 'add_token' ]);
    
    $this->client = new \GuzzleHttp\Client();
  }

  public function add_token($order_id) {
    $order = new \WC_Order( $order_id );
    $items = $order->get_items();
    foreach ( $items as $item ) {
      if ( in_array($item->get_product_id(), \OE_SKILLJAR_ASSESSMENT_IDS )) {
        global $oe_skilljar_code;
        $oe_skilljar_code = self::generate_token();
        $body = $this->add_access_code($order, $item, $oe_skilljar_code);   
      }
    }
  }

  public function add_access_code($order, $item, $code) {
    try {
      $payload = [
        'auth' => [ self::OE_SKILLJAR_TOKEN, '' ],
        'form_params' => [
          'code' => $code,
          'active' => 1,
          'duration' => 12,
          'duration_unit' => 'MONTHS'
        ]
      ];
      $response = $this->client->post($this->add_access_code_endpoint(), $payload);
      $body = (string) $response->getBody();
      return json_decode($body);
    } catch (Exception $e) {
      \wp_mail(\OE_ADMIN_EMAILS, 'Outcome Engenuity Skilljar Plugin Error', $e->getMessage());
    }
  }

  public function ping() {
    $response = $this->client->get(self::ENDPOINT . '/ping', [ 'auth' => [ self::OE_SKILLJAR_TOKEN, '' ] ]);
    $body = (string) $response->getBody();
    return json_decode($body);
  }

  private function add_access_code_endpoint() {
    return join('/', [self::ENDPOINT, 'domains', self::OE_SKILLJAR_DOMAIN, 'access-code-pools', self::OE_SKILLJAR_POOL_ID, 'access-codes']);
  }
}
