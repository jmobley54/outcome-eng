<?php
// namespace OutcomeEngenuity;
/*
Plugin Name: Outcome Engenuity Skilljar
Plugin URI: https://www.crux.mn/
Description: Hook integration with the Outcome Engenuity Skilljar service.
Version: 0.0.1
Author: Cory Preus
Author URI: http://www.crux.mn/
Copyright: Crux LLC
Text Domain: crux
Domain Path: /lang
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( __DIR__ . '/vendor/autoload.php');

add_action('plugins_loaded', 'oe_skilljar_init');

function oe_skilljar_init() {
  require_once( __DIR__ . '/OutcomeEngenuity/Skilljar.php');
  require_once( __DIR__ . '/OutcomeEngenuity/SkilljarWelcomeEmail.php');

  require_once( __DIR__ . '/filters/SkilljarWelcomeEmailFilter.php');

  new SkilljarWelcomeEmailFilter();
  $skilljar = OutcomeEngenuity\Skilljar::get_instance();
}
