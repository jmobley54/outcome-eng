<?php

class JustCultureBrightCustomizations {

  static function bright_license_field($checkout) {
	$title = "License Key";
	$msg = "";
	
	/* a comment */
	
	/* BrightWoocommerceIntegration::addLicenseFieldToCheckout($checkout, */
    /*                                                         array('title' => 'Please select from the following options for using license keys:', */
    /*                                                               'singletext' => '<strong>I\'m buying training for myself.</strong>  (No license key necessary - courses will automatically be added to your My Account Page)', */
    /*                                                               'multitext' => '<strong>I\'m buying online training for others.</strong>  I will receive a license key to share with them, and I can redeem it for myself as well. (My learners will redeem the License Key from the link that I will give to them and register an account if necessary.)', */
    /*                                                               'tooltip' => 'This provides you with the option to create a license key for the online courses in your cart.<br/><br/>If you select <strong>I\'m buying online training for others</strong>, you will receive a link for these courses that you can share with your learners on the checkout page.<br/><br/>If you want to just take a course yourself directly, select <strong>I\'m buying training for myself</strong>.' */
    /*                                                               //                                                                  'includeMultiMyself' => true */
    /*                                                               )); */

    $title = "License Key";
    $msg = "";
	
    /* a comment */

    $license_key_setting = BrightWoocommerceIntegration::isCartProbableLicensePurchase();
    $bright = \Bright\Wordpress::getInstance();

    $bright->log($license_key_setting, 'brightLicenseField $license_key_setting');
        
    /* if ($license_key_setting) { */
    /*   echo '<div class="woocommerce-info">You have a quantity of greater than one in your cart.   After checkout, you will receive a license key that you can use to distribute course access, including for yourself.</div>'; */
    /*   woocommerce_form_field('bright_license_check', */
    /*                          array('type'          => 'hidden', */
    /*                               ), 'multi'); */

    /* } else */
        BrightWoocommerceIntegration::addLicenseFieldToCheckout($checkout, array(
            'title' => 'Who is learning?',
            'singletext' => '<strong>I\'m buying online training for myself.</strong> ',
            'multitext' => '<strong>I\'m buying training for someone else.</strong>',
            'tooltip' => 'This provides you with the option to create a license key for the online courses in your cart.<br/><br/>If you select <strong>I\'m buying online training for others</strong>, you will receive a link for these courses that you can share with your learners on the checkout page.<br/><br/>If you want to just take a course yourself directly, select <strong>I\'m buying training for myself</strong>.'            
        ));
    


  }

  /**
   * Adds in order details.  Intended to be called from a WooCommerce callback.
   */
  static function addOrderDetails($order) {
	return BrightWoocommerceIntegration::generateOrderDetails($order,array('registration-page' => 'bright-registration',
                                                                           'add-license-key-page' => 'my-account',
                                                                           'report-page' => 'invitation-report'));
  }

  static function afterLicenseKeyCreated($wc_order,$invitationId) {
    $url = get_site_url();
    $invitationReport = "{$url}/invitation-report?invitation_name={$invitationId}";
    $wc_order->add_order_note("License key report is available at <a href=\"{$invitationReport}\" target=\"{$invitationId}\">{$invitationReport}</a>");
  }
}

/* 'woocommerce_before_checkout_shipping_form' */
/* add_action( 'woocommerce_after_order_notes', 'JustCultureBrightCustomizations::bright_license_field'); */

$actions = array ('woocommerce_checkout_before_order_review',
				  'woocommerce_checkout_before_customer_details');

add_action($actions[1], 'JustCultureBrightCustomizations::bright_license_field');


add_action('woocommerce_order_items_table','JustCultureBrightCustomizations::addOrderDetails');

add_action('bright_woocommerce_added_license_key','JustCultureBrightCustomizations::afterLicenseKeyCreated',10,2);

