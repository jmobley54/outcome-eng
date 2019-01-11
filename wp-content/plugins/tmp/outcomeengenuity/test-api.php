<?php
define('WP_USE_THEMES', false);

require( '../../../wp-blog-header.php' );
require_once( __DIR__ . '/config.php' );
require_once( __DIR__ . '/vendor/autoload.php' );

require_once( __DIR__ . '/OutcomeEnginuity/API.php');
require 'actions/ReviewOrderBeforeSubmit.php';
require 'actions/AfterCheckoutFormCreateUserAccount.php';

$api = new OutcomeEnginuity\API();

echo 'test';
$review_order = ReviewOrderBeforeSubmit::get_instance();
$review_order->applyService( $api );

print_r($argv[1]);
$exists = $review_order->verify_if_email_exists($argv[1]);
print_r($exists);
