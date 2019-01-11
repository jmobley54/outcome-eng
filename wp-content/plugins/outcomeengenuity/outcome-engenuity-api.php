<?php
/*
Plugin Name: Outcome Engenuity API
Plugin URI: https://www.crux.mn/
Description: Hook integration with the Outcome Engenuity API.
Version: 0.0.1
Author: Cory Preus
Author URI: http://www.crux.mn/
Copyright: Crux LLC
Text Domain: crux
Domain Path: /lang
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( __DIR__ . '/config.php' );
require_once( __DIR__ . '/vendor/autoload.php' );

require_once( __DIR__ . '/OutcomeEngenuity/API.php');
require 'actions/ReviewOrderBeforeSubmit.php';
require 'actions/AfterCheckoutFormCreateUserAccount.php';

$api = new OutcomeEngenuity\API();

$review_order = ReviewOrderBeforeSubmit::get_instance();
$review_order->applyService( $api );

$after_checkout = AfterCheckoutFormCreateUserAccount::get_instance();
$after_checkout->applyService( $api );
