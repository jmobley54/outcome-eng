<?php
use OutcomeEngenuity\SkilljarWelcomeEmail;

class SkilljarWelcomeEmailFilter {

  private static $instance = null;

  public static function get_instance() {
    if ( null == self::$instance ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function __construct() {
    \add_filter( 'woocommerce_email_classes', [ $this, 'welcome_email' ] );
  }

	public function welcome_email($email_classes) {
    $email_classes['SkilljarWelcomeEmail'] = new OutcomeEngenuity\SkilljarWelcomeEmail();
    return $email_classes;
  }

}
