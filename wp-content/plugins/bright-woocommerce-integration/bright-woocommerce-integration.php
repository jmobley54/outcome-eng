<?php
/*
  Plugin Name: Bright Woocommerce Integration
  Plugin URI: http://aurabright.aura-softare.com/
  Description: Bright Woocommerce Integration Plugin For WordPress
  Author: Aura Software
  Version: 6.0.4
  Author URI: http://www.aura-software.com/

  Source code created by Aura Software, LLC is licensed under a
  Attribution-NoDerivs 3.0 Unported United States License
  http://creativecommons.org/licenses/by-nd/3.0/
*/

$plugin_root = dirname (__FILE__);


/* XXX - this is a hack that works around WordPress (maybe 4.2.2 on Linux) 
 * not loading plugins in alphabetical order, or using some non-intuitive definition of 'alphabetical'.
 * Also, this is an absolutely terrible way to do this */

if (! class_exists('\Bright\Wordpress')) {
  $brightfile = WP_PLUGIN_DIR . '/bright/bright.php';
  if (file_exists($brightfile))
    require_once($brightfile);  
}

require_once($plugin_root.'/templates.php');
require_once($plugin_root.'/brightRegistrationForm/brightRegistrationForm.php');

class BrightWoocommerceConstants {
  const BRIGHT_AUTOCOMPLETE_ORDER = 'bright_autocomplete_order';
  const LICENSE_ORDER = 'bright_license';
  /**
   * @var this is the user profile field name [via the database] in which the list of bright license keys are stored.
   */
  const LICENSE_META = 'bright_licenses';
  const LICENSE_ORDER_ID = 'bright_license_id';
  const BRIGHT_COURSE_ID = 'bright_course_id';
  const BRIGHT_METADATA = 'bright_metadata';
}

class WoocommerceExtensions {
  /**
   * Returns the associated WC_Product record for an WC_Item
   * @access public
   * @return WC_Product
   * @param WC_Item $item
   */
  static public function item_product($item) {
    $product_id = $item['product_id'];
    $product = new WC_Product ($product_id);
    return $product;
  }
}

class BrightWoocommerceIntegration {

  /**
   * Sets the bright_license field for an order.  If value is '1' [or possibly other true values], Bright will manage as a license order.
   * @param integer $order_id ID of the order
   * @param String $value value to set the metadata field as.
   * @return boolean returns true if correctly set.
   */
  static public function setLicenseOrder($order_id,$value) {
    return boolval(update_post_meta($order_id, BrightWoocommerceConstants::LICENSE_ORDER,$value));
  }

  /**
   * This callback is attached to the WordPress update_user_metadata filter.
   * For more information see the wordpress documentation on the filter. [https://codex.wordpress.org/Plugin_API/Filter_Reference/update_(meta_type)_metadata].
   * Note this filter must return NULL for the save to happen correctly.
   *
   * @access public
   * @param null $null Dummy value of no use.
   * @param int $object_id ID of the object metadata is for
   * @param string $meta_key Metadata key
   * @param mixed $meta_value Metadata value. Must be serializable if non-scalar.
   * @param mixed $prev_value Optional. If specified, only update existing metadata entries with
   *              the specified value. Otherwise, update all entries.
   * @return Boolean
   */
  static function doProfileChange( $null, $object_id, $meta_key, $meta_value, $prev_value ) {
    if ($meta_key == BrightWoocommerceConstants::LICENSE_META)
      BrightWoocommerceIntegration::addLicenseForUser($object_id,$meta_value); /* $object_id is the $user_id */
    /*   * Note this filter must return NULL for the save to happen correctly. */
    /* DO NOT RETURN DATA HERE */
  }
  /* add_filter( 'update_user_metadata', 'BrightWoocommerceIntegration::doProfileChange', 10, 5 ); */

  /**
   * Returns true if product has associated linked Bright product ids.  It also checks for bright_metadata.
   * @access public
   * @return Boolean
   * @param WC_Product $product
   */
  static public function isBrightProduct($product) {
    $bright_course_ids = BrightWoocommerceIntegration::getBrightCourseIds(array('product' => $product));
    if (count($bright_course_ids) > 0)
      return true;
    $bright_metadata = BrightWoocommerceIntegration::getBrightProductMetadata($product,true);
    if ($bright_metadata)
      return true;
    return false;
  }

  /**
   * Returns true if product is a bright product, and one of the following is true:
   *     1. the item quantity is greater than 1.
   *     2. the bright metadata defines a quantity_multiplier.
   *     3. the bright metadata indicates that the product should always be in the form of a license purchase.
   *
   * No arguments is taken, access to the cart is via the global $woocommerce variable.
   *
   * @access public
   * @return Boolean
   */
  static public function isCartProbableLicensePurchase() {
    global $woocommerce;

    $cart = $woocommerce->cart;
    $cart_contents = $cart->cart_contents;

    $ret = false;

    foreach ($cart_contents as $key => $product_a) {
      $product = $product_a['data'];

      if (BrightWoocommerceIntegration::isBrightProduct($product)) {

        $bright_metadata= BrightWoocommerceIntegration::getBrightProductMetadata($product);
        $bright_course_ids = BrightWoocommerceIntegration::getBrightCourseIds(array('product' => $product));
        foreach($bright_course_ids as &$bright_course_id) {
          $quantity = BrightWoocommerceIntegration::determineQuantity($product_a['quantity'],
                                                                      $bright_course_id,
                                                                      $bright_metadata);
          bright_log("quantity for {$bright_course_id} is {$quantity}");
          if ($quantity > 1)
            $ret = true;
        }
      }
    }
    return $ret;
  }

  /**
   * Returns true if cart includes Bright courses:
   *     1. the item quantity is greater than 1.
   *     2. a bright course id is present
   *
   * @access public
   * @return Boolean
   */
  static public function doesCartIncludeBrightCourses() {
    global $woocommerce;

    $cart = $woocommerce->cart;
    $cart_contents = $cart->cart_contents;

    foreach ($cart_contents as $key => $product_a) {
      $product = $product_a['data'];

      if (BrightWoocommerceIntegration::isBrightProduct($product))
        return true;
    }
    return false;
  }

  /* update a user's licenses; whether they need it or not */
  static public function addLicenseForUser($user_id, $licenses=null) {
    $bright = Bright\Wordpress::getInstance();

    $user = get_user_by('id', $user_id);
    if (empty($licenses))
      /* const LICENSE_META = 'bright_licenses'; */
      $licenses = get_user_meta($user_id, BrightWoocommerceConstants::LICENSE_META, true);
    $a_l = explode(",",$licenses);
    /* TODO: strip whitespace */
    $return_data = array();
    foreach ($a_l as &$license)
      array_push($return_data,
                 $bright->addLearnerToInvitation($user->user_email,
                                                 $license,
                                                 array('errorMsgs' => array(),
                                                       'params' => array('nodelay' => true))));

    /* arg3 says synchronous registration */
    return $return_data;
  }

  /* will return a valid non null non empty value if the data for this order has been sent to the bright server
     and a valid ID was returned */
  static function getBrightLicenseId($wc_order) {
    if ($wc_order instanceof WC_Order) {
      $order_id = $wc_order->id;
      return get_post_meta($order_id, BrightWoocommerceConstants::LICENSE_ORDER_ID,true);
    }
    return null;
  }


  /**
   * Sets the 'bright_license_id' field for the order
   * @return boolean returns true if successful, null or false if not.
   */
  static function setBrightLicenseId($wc_order,$license_id) {
    if ($wc_order instanceof WC_Order) {
      $order_id = $wc_order->id;
      return add_post_meta($order_id, BrightWoocommerceConstants::LICENSE_ORDER_ID,$license_id,true) || update_post_meta($order_id, BrightWoocommerceConstants::LICENSE_ORDER_ID,$license_id);
    }
    return null;
  }

  /**
   * @return boolean True if isLicenseOrder
   */
  static function isLicenseOrder($wc_order) {
    $licenseData = get_post_meta($wc_order->id,BrightWoocommerceConstants::LICENSE_ORDER,true);
    if (in_array($licenseData, array('single','multi','multimyself')))       // the new radio button model
      return in_array($licenseData, array('multi','multimyself'));
    else       // the oldcheckbox model
      return boolval($licenseData);
  }

  /**
   * @return boolean True if the purchaser wants to be redeemed on the license order.
   */
  static function isSelfRedeemedOrder($wc_order) {
    $licenseData = get_post_meta($wc_order->id,BrightWoocommerceConstants::LICENSE_ORDER,true);
    return in_array($licenseData, array('multimyself'));
  }


  /**
   * Takes a Woocommerce product, and returns the field name of the variations field, if present.
   */

  static function getProductVariationFromAttributes($product) {
    $bright = Bright\Wordpress::getInstance();

    if ($product instanceof WC_Product) {
      $productAttributes = get_post_meta($product->id, '_product_attributes',true);
      if (!$productAttributes)
        return;
      $bright->log($productAttributes,"product attributes");
      foreach ($productAttributes as $attributeName => $attributeValue) {
        $bright->log($attributeValue, "product attribute {$attributeName}");
        if (isset($attributeValue['is_variation']) && $attributeValue['is_variation'] > 0)
          return $attributeName;
      }
    } else {
      $bright->log('object not of type WC_Product passed to BrightWoocommerceIntegration::getProductVariationFromAttributes');
      return null;
    }
  }

  /**
   * If a variation is selected on the item, then return the value selected by the buyer for the variation.
   * @return String the selected variation
   */

  static function getSelectedVariationForItem($product,$item) {
    $variationName = BrightWoocommerceIntegration::getProductVariationFromAttributes($product);
    if ($variationName) {
      if ($item && is_array($item)) {
        if (isset($item['variation_id']) && isset($item[$variationName]))
          return $item[$variationName];
      } else
        return null;
    }
  }

  /**
   * @return boolean returns the default configuration, whether bright products should autocomplete the order.
   * @todo Make this a site-wide preference.
   */
  static function getAutocompleteOrderDefault() {
    // TODO
    return apply_filters('bright_woocommerce_integration_autocomplete_order_default', true);
  }

  /**
   * @param WC_Product product - The WC_Product
   * @return boolean true if the product is eligible for autocomplete 
   */
  static function getAutocompleteForOrderItem(WC_Product $product) {
    if (BrightWoocommerceIntegration::isBrightProduct($product)) {
      $ret = get_post_meta($product->id,BrightWoocommerceConstants::BRIGHT_AUTOCOMPLETE_ORDER,true);
      if (empty($ret))
        return BrightWoocommerceIntegration::getAutocompleteOrderDefault();
      return boolval($ret);
    } else 
      return false; // not a bright product
  }

  /**
   * @param WC_Product product - The WC_Product
   * @param Boolean assoc - The second argument is the assoc argument to be passed to json_decode, when set to true the resulting data is converted to an associative array.
   */
  static function getBrightProductMetadata(WC_Product $product,$assoc=false) {
    $bright = Bright\Wordpress::getInstance();
    if ($product instanceof WC_Product) {
      $single =  get_post_meta($product->id,BrightWoocommerceConstants::BRIGHT_METADATA,true);
      if (!empty($single)) {
        $decode = json_decode($single,$assoc);
        if (empty($decode)) {
          $bright->log("Could not process Bright Order Metadata of {$single}");

          switch (json_last_error()) {
          case JSON_ERROR_NONE:
            $bright->log( ' - No errors', true);
            break;
          case JSON_ERROR_DEPTH:
            $bright->log( ' - Maximum stack depth exceeded', true);
            break;
          case JSON_ERROR_STATE_MISMATCH:
            $bright->log( ' - Underflow or the modes mismatch', true);
            break;
          case JSON_ERROR_CTRL_CHAR:
            $bright->log( ' - Unexpected control character found', true);
            break;
          case JSON_ERROR_SYNTAX:
            $bright->log( ' - Syntax error, malformed JSON', true);
            break;
          case JSON_ERROR_UTF8:
            $bright->log( ' - Malformed UTF-8 characters, possibly incorrectly encoded', true);
            break;
          default:
            $bright->log( ' - Unknown error', true);
            break;
          }
          $bright->log("This is a misconfiguration please contact your system administrator for repair.");
        }
        /* $bright->log($decode, "decoded metadata to follow"); */
        return $decode;
      }
    }
    return null;
  }

  /**
   * Based on the product setup, it can fetch the required bright course IDS for a product.
   *
   * @params Array args product|item
   * @return Array a list of the bright course ids associated with this product.
   */

  static function getBrightCourseIds($args) {
    $bright = Bright\Wordpress::getInstance();

    $product = Bright\extractFromArray($args,'product');
    $item = Bright\extractFromArray($args,'item');

    if ($item && $product) {
      $metadata = BrightWoocommerceIntegration::getBrightProductMetadata($product,true);
      $selectedVariation = BrightWoocommerceIntegration::getSelectedVariationForItem($product,$item);
      $bright->log($selectedVariation, "selected variation");
      /* they've selected a variation, let's see if there's metadata for this */
      if ($selectedVariation && isset($metadata['variations'])) {
        $variations = $metadata['variations'];
        foreach($variations as &$variation) {
          if (isset($variation['variationName']) &&
              $variation['variationName'] === $selectedVariation) {
            $bright->log($variation['courseGuids'],"matched courseguids for variation {$selectedVariation}");
            return $variation['courseGuids'];
          } else
            $bright->log("no matches found for variation {$selectedVariation}");
        }
      }
    }

    /* didn't find a match in the product variation data, so just look for a bright course ID */

    if ($product instanceof WC_Product) {
      $multiple = get_post_meta($product->id,BrightWoocommerceConstants::BRIGHT_COURSE_ID,false);
      return $multiple;
    } else
      bright_log('Argument must be of type WC_Product',true);

    return null;
  }

  static public function debug_order($wc_order) {
    $order_id = $wc_order->id;
    $items = $wc_order->get_items();
    $course_guids = array();
    $license_data = array();
    foreach($items as &$item) {
      $quantity = $item['qty'];
      $product =  WoocommerceExtensions::item_product($item);
      /* hmmm next line seems to be bogus .... no such function */
      $bright_course_id = BrightWoocommerceIntegration::getBrightCourseId($product);
      array_push($course_guids,$bright_course_id);
      $license_data[$bright_course_id] = array();
      $license_data[$bright_course_id]['seats_available'] = $quantity;
    }
  }

  /**
   * Returns an integer representing the quantity of the bright course to be purchased.
   * This can be a function of the item quantity and/or bright metadata
   * See also http://help.aura-software.com/defining-bright-metadata-for-a-product/
   * @return integer
   * @access public
   * @param integer $item_quantity
   * @param string $bright_course_id
   * @param Array $bright_metadata
   */
  static public function determineQuantity($item_quantity,$bright_course_id,$bright_metadata) {
    bright_log('determineQuantity: item_quantity', $item_quantity);
    bright_log('determineQuantity: bright_course_id', $bright_course_id);
    bright_log($bright_metadata);
    if(!empty($bright_metadata)) {
      if(!empty($bright_course_id)) {
        bright_log("bright_course_id is {$bright_course_id}");
        $bright_courses = $bright_metadata->{'bright-courses'};
        if (!empty($bright_courses))
          foreach($bright_courses as &$course)
            if ($course->{'course-id'} == $bright_course_id) {
              if(property_exists($course, "quantity-multiplier"))
                $quantity_multipier = $course->{'quantity-multiplier'};
              /* DEPRECATED */
              /* if(property_exists($course, "quantity_multiplier")) */
              /*   $quantity_multipier = $course->{'quantity_multiplier'}; */
              if (!empty($quantity_multipier)) {
                bright_log("for course id {$bright_course_id} a quantity_multiplier of {$quantity_multipier} was found");
                $quan = $item_quantity * $quantity_multipier;
                bright_log("for course id {$bright_course_id} returning a quantity of {$quan}");
                return $quan;
              }
            }
      }
    }
    return $item_quantity; /* fallthrough */
  }

  static function assignLicenseData(&$licenseData,$courseGuid,$key,$value) {
    if (!isset($licenseData[$courseGuid]))
      $licenseData[$courseGuid] = array();
    $licenseData[$courseGuid][$key] = $value;
    return $licenseData;
  }

  /**
   * When bright_license is set to 1 in the order metadata, instead of generating registrations, we generate a licens key.
   * @return void
   * @access public
   * @param WC_Order $wc_order
   */

  static function manageLicenseOrder($wc_order) {
    $order_id = $wc_order->id;
    $log_base = "manageLicenseOrder({$order_id}): ";
    $bright = Bright\Wordpress::getInstance();
    $currentUser = $bright->getCurrentUser();

    if (! BrightWoocommerceIntegration::isLicenseOrder($wc_order)) {
      bright_log("{$log_base}: not a license order");
      return false;
    }

    $license_id = BrightWoocommerceIntegration::getBrightLicenseId($wc_order);
    bright_log("{$log_base}license_id: ", $license_id);
    if (empty($license_id)) {
      $items = $wc_order->get_items();
      $course_guids = array();
      $license_data = array();
      foreach($items as &$item) {
        $quantity = $item['qty'];
        $product =  WoocommerceExtensions::item_product($item);
        $bright_course_ids = BrightWoocommerceIntegration::getBrightCourseIds(array('product' => $product));
        $bright_metadata= BrightWoocommerceIntegration::getBrightProductMetadata($product);

        foreach($bright_course_ids as &$bright_course_id) {
          if (! in_array($bright_course_id,$course_guids) )
            array_push($course_guids,$bright_course_id);

          if (! array_key_exists($bright_course_id, $license_data))
            BrightWoocommerceIntegration::assignLicenseData($license_data,$bright_course_id,'seats_available',0);

          BrightWoocommerceIntegration::assignLicenseData($license_data,
                                                          $bright_course_id,
                                                          'seats_available',
                                                          $license_data[$bright_course_id]['seats_available'] +
                                                          BrightWoocommerceIntegration::determineQuantity($quantity,
                                                                                                          $bright_course_id,
                                                                                                          $bright_metadata));
        }
      }

      $custom = array();
      $custom['license'] = true;
      $custom['order_id'] = $order_id;
      $custom['course_guids'] = $course_guids;
      $custom['license_data'] = $license_data;
      $custom['coupons'] = $wc_order->get_used_coupons();

      bright_log('manageLicenseOrder: get_user_coupons', $wc_order->get_used_coupons());

      $url = get_site_url();
      $find = array( 'http://', 'https://' );
      $replace = '';
      $without_protocol = str_replace( $find, $replace, $url );

      $custom['initiating_site'] = $without_protocol;
      $custom['initiating_user'] = bright_get_user()->user_email;

      $bright_license = BrightV1Api::create_invitation(array(
                                                             'license' => true,
                                                             'course_guids' => $course_guids,
                                                             'license_data' => $license_data,
                                                             'custom' =>  $custom
                                                             ));

      global $bright_curl_error;

      $error_func = function($log_base,$errortext,$wc_order,$object=null) {
        $error = "{$log_base}: {$errortext}";
        $wc_order->add_order_note($error);
        bright_log("{$error}",true);
        if ($object)
          bright_log($object);
        bright_log("Please try to refresh the page or contact support if problems continue",true);
        return null;
      };

      if (!empty($bright_curl_error))
        return $error_func($log_base, "Error Contacting The Bright Server: {$bright_curl_error}.",$wc_order);

      if (empty($bright_license))
        return $error_func($log_base, "A valid license was not returned by the bright server", $wc_order);

      if (property_exists($bright_license,'error'))
        return $error_func($log_base, "An error was received from the Bright Server: \"{$bright_license->error} ({$bright_license->details})\".",$wc_order);

      if (!property_exists($bright_license,'name'))
        return $error_func($log_base, "An illegal response was received from the Bright server [no license name returned].",$wc_order,$bright_license);
      $invitation_id = $bright_license->name;
      if (! empty($invitation_id)) {
        BrightWoocommerceIntegration::setBrightLicenseId($wc_order,$invitation_id);
        // TODO: I can see scenarios where we WOULDN't want to do this.
        $wc_order->add_order_note("{$log_base} bright license key set to {$invitation_id}");
        
        if (BrightWoocommerceIntegration::isSelfRedeemedOrder($wc_order)) 
          $ret = $bright->addLearnerToInvitation($currentUser->user_email,
                                                 $invitation_id,
                                                 array('errorMsgs' => array(),
                                                       'params' => array('nodelay' => true,
                                                                         'api_template' => 'extended')));
        do_action('bright_woocommerce_added_license_key',$wc_order,$invitation_id);

        return true;
      } else 
        return $error_func($log_base, "An illegal response was received from the Bright server [blank license key returned].",$wc_order,$bright_license);
    } else {
      $wc_order->add_order_note("Re-using previously created license {$license_id}.  Delete the order custom field " . BrightWoocommerceConstants::LICENSE_ORDER_ID . " will force Bright to create a new license when running Manage Order via Bright.");
      do_action('bright_woocommerce_added_license_key',$wc_order,$license_id);
      return false;
    }
  }

  /**
   * Adds a license key field to your checkout page, if you are purchasing a Bright course.
   * Also will default to checked if any bright product has a quantity > 1.
   * @return boolean returns true if the field was generated.
   * @param WP_Cart $checkout
   * @param mixed $args
   * @param string $args['tooltip'] Set the tooltip text; will be wrapped in a <p> tag.
   * @param string $args['singletext'] Set the text for the non license-key purchase radio button label
   * @param string $args['multitext'] Set the text for the license-key purchase radio button label
   * @param string $args['title'] Set the text for the title of the license key checkout panel
   * @param string $args['includeMultiMyself'] Add a radiobutton to license field allowing for the creation of a license field that will automatically be redeemed for the buyer.
   */
  static function addLicenseFieldToCheckout($checkout,$args=array()) {
    if (!BrightWoocommerceIntegration::doesCartIncludeBrightCourses())
      return false;

    /* TODO: create defaults */
    $title = Bright\extractFromArray($args,'title', "License Key");
    $singletext = Bright\extractFromArray($args,'singletext','No license key, I will receive a link to my online course directly from the checkout page');
    $multitext =  Bright\extractFromArray($args,'multitext','I will receive a license key for my online courses I can share with my learners');
    $tooltext = Bright\extractFromArray($args,'tooltip', "This provides you with the option to create a license key for the online courses in your cart.<br/><br/>If you select <strong>I will receive a license key</strong>, you will receive a link for these courses that you can share with your learners on the checkout page.<br/><br/>If you want to just take a course yourself directly, select <strong>No License Key</strong>.");
    $includeMultiMyself = Bright\extractFromArray($args,'includeMultiMyself',false);


    /* TODO: add arguments to control style and class of encolding div, plus h3 tag */
    echo '<div class="woocommerce-info" id="bright_license_field">';
    if (!empty($title))
      echo '<h3>' . __($title) . '</h3>';

    if (empty($checkout->posted))
      $license_key_setting = BrightWoocommerceIntegration::isCartProbableLicensePurchase();
    else
      $license_key_setting = $checkout->get_value( 'bright_license_check' );

    echo "<div class=\"bright-check-wrapper\"><div class=\"bright-checkout-help-tip\"><p>{$tooltext}</p></div>";

    echo '<input type="hidden" name="doing-bright-license-key-check" value="true">'; /* so we can process required license key radiobutton */

    $radioOptions = array('single' => $singletext,
                          'multi' => $multitext);

    if ($includeMultiMyself)
      $radioOptions['multimyself'] = '<strong>I am buying a license key for others AND myself</strong>.  I will receive a license key and it will be automatically redeemed for me as well.';
    

    woocommerce_form_field('bright_license_check',
                            array('type'          => 'radio',
                                  'class'         => array('bright_license_check input-checkbox'),
                                  /* 'label'         => __($title), */
                                  'options'       => $radioOptions,
                                  'required'      => true,
                                  ), $license_key_setting);

    echo '</div>';
    echo '</div>'; // seems this woocommerce_form_field thing leaves an unclosed div....
    return true;
  }

  /**
   * Adds order details to the Woocommerce Checkout Page.
   *
   * @param Array $args
   *   if 'show-registration-link' is set, a table row will be generated which links to the WP registration page with the license key encoded as a query parameter.
   */
  static function generateOrderDetails($order,$args=array()) {
    $report_page = Bright\extractFromArray($args,'report-page', 'invitation-report');
    $addLicenseKeyPage = Bright\extractFromArray($args,'add-license-key-page', 'my-account');
    $registrationPage = Bright\extractFromArray($args,'registration-page', null);

    

    if ( BrightWoocommerceIntegration::isLicenseOrder($order)) {
      $license_key = BrightWoocommerceIntegration::getBrightLicenseId($order);
      $site_url = get_site_url();
      $license_key_url = "/wp-login.php?action=register&bright_licenses={$license_key}";
      $full_url = $site_url . $license_key_url;
      $invitation_url = "{$site_url}/{$report_page}?invitation_name={$license_key}";
      if ($registrationPage) 
        $addLicenseKeyUrl = "{$site_url}/${registrationPage}?redirect_to={$addLicenseKeyPage}?license_key={$license_key}";
      else
        $addLicenseKeyUrl = "{$site_url}/{$addLicenseKeyPage}?license_key={$license_key}";
?>
                    <tr>
                      <td colspan="2"><div class="woocommerce-message" style="padding-right: inherit">To provide the course to your learners, please email the <strong>Redeem License Key Link</strong> provided below. If they do not already have an account on our website, they will be prompted to create one. If they already have a registered account with us they can log in and submit the license key.</div></td>
                    </tr>

                    <tr class="bright-license-key">
                      <th scope="row" title="Use this link to redeem this license key, for yourself or your learners.">Redeem License Key Link:</th>
                      <td><a href="<?php echo $addLicenseKeyUrl; ?>" target="<?php echo "{$license_key}-redeem"; ?>"><?php echo $addLicenseKeyUrl; ?></a></td>
                    </tr>
<?php
if (isset($args['show-registration-link'])) {
?>
                    <tr class="bright-license-key-link">
                      <th scope="row" title="Use the link to send to your users!!!!">License Key Registration URL:</th>
                    <td><a href="<?php echo $full_url; ?>" target="<?php echo $license_key; ?>"><?php echo $full_url ?></a></td>
                    </tr>
<?php
                       }
?>

                    <tr class="bright-license-key-report">
                    <th scope="row" title="Access a full report on the learner data for this License Key">Learners Report:</th>
                  <td><a href="<?php echo $invitation_url; ?>" target="<?php echo "{$license_key}-report"; ?>"><?php echo $invitation_url ?></a></td>

                    </tr>
    <?php        }
  }

  /**
   * This callback is used to add a custom order action to the order status page.
   * Specifically we add a 'manage order via bright ' action.
   */

  static function addOrderMetaBoxActions($actions) {
    $bright = Bright\Wordpress::getInstance();
    $bright->log("in BrightWoocommerceIntegration::addOrderMetaBoxActions()");

    $actions['bright_manage_order'] = 'Manage Order via Bright';
    return $actions;
  }

  static function processAutoComplete($order) {
    $items = $order->get_items();
    $bright = Bright\Wordpress::getInstance();
    $bright->log("found " . sizeof($items) . " items for order {$order->id}, processing one by one");
    $do_autocomplete = true;
    foreach($items as &$item) {
      $product =  WoocommerceExtensions::item_product($item);
      if (! BrightWoocommerceIntegration::getAutocompleteForOrderItem($product)) {
        $do_autocomplete = false;
        break;
      }
    }
    if ($do_autocomplete) 
      $order->update_status("completed");      
  }

  static function manageOrder($order_id,$statusChangeCallback=true) {
    $bright = Bright\Wordpress::getInstance();
    $bright->setAndAuthenticateCurrentUser($bright->getCurrentUser());
    $bright->log("BrightWoocommerceIntegration::managerOrder({$order_id})");
    $order = new WC_ORDER($order_id);

    $ret = false; // return code;

    if (BrightWoocommerceIntegration::isLicenseOrder($order))  /* don't process registrations for license orders */
      $ret = BrightWoocommerceIntegration::manageLicenseOrder($order);
    else {
      /* Non license key order */
      $items = $order->get_items();
      $bright->log("found " . sizeof($items) . " items for order {$order_id}, processing one by one");
      foreach($items as &$item) {
        $product =  WoocommerceExtensions::item_product($item);
        if (BrightWoocommerceIntegration::isBrightProduct($product)) {
          $bright->log("item for order {$order_id} found bright product");
          $brightCourseIds = BrightWoocommerceIntegration::getBrightCourseIds(array('product' => $product,
                                                                                    'item' => $item));
          $bright->log($brightCourseIds, "bright course ids");
          $user = $order->get_user();
          $email = $user->user_email;
          $user_id = $user->ID;
          $fname = get_user_meta( $user_id, 'first_name', true );
          $lname = get_user_meta( $user_id, 'last_name', true );
          if (sizeof($brightCourseIds) < 1)
            BrightWoocommerceIntegration::addOrderNote($order,
                                                       "No matching bright course ID(s) found for product {$product->post->post_title}");
          else
            foreach ($brightCourseIds as &$bright_course_id) {
              $r=$bright->createRegistration(array('params' => array('dont_duplicate' => 1,
                                                                     'course_guid' => $bright_course_id,
                                                                     'learner_id' => $email,
                                                                     'fname' => $bright->getCurrentUserFirstName(),
                                                                     'lname' => $bright->getCurrentUserLastName())));
              
              if ($r) {
                BrightWoocommerceIntegration::addOrderNote($order,
                                                           "Product '{$product->post->post_title}'; creating bright registration for {$email} on course '{$bright_course_id}', registration ID '{$r->registration_guid}'");
                $ret = true;
              } else 
                BrightWoocommerceIntegration::addOrderNote($order,
                                                           "Product '{$product->post->post_title}'; failed to create bright registration for {$email} on course '{$bright_course_id}'.  Please check the site logs for code " . $bright->getRequestCode());
            } // foreach
        } else
          $bright->log("skipping non-bright product/item");
      } // foreach item
    }
    if ($ret) // success?  process autocomplete
      BrightWoocommerceIntegration::processAutoComplete($order);
    return $ret;
  }

  /**
   * Adds an order note to the order.
   */
  static function addOrderNote($order,$string) {
    $bright = Bright\Wordpress::getInstance();
    $bright->log("Order {$order->id}; " . $string);
    $order->add_order_note($string);
  }

  static function manageOrderCallback($order) {
    $bright = Bright\Wordpress::getInstance();
    $bright->log("in manageOrderCallback for order # ({$order->id})");
    if (method_exists("BrightWoocommerceIntegration","manageOrder"))
      return BrightWoocommerceIntegration::manageOrder($order->id,true);
    else
      $bright->log('no such method BrightWoocommerceIntegration::manageOrder');
  }

  static function custom_checkout_field_process() {
    if ($_POST['doing-bright-license-key-check'] ) 
    // Check if set, if its not set add an error.
      if ( ! $_POST['bright_license_check'] ) 
        wc_add_notice( __( 'Please specify if you are purchasing a shareable license key for your online training, or taking the course yourself.' ), 'error' );
  }

}

/* Action And Templates to Follow */


add_action('woocommerce_order_action_bright_manage_order', 'BrightWoocommerceIntegration::manageOrderCallback');

// http://neversettle.it/add-custom-order-action-woocommerce/
// add our own item to the order actions meta box
add_action( 'woocommerce_order_actions', 'BrightWoocommerceIntegration::addOrderMetaBoxActions');

add_action( 'woocommerce_order_status_processing', function ($order_id) {
    $bright = Bright\Wordpress::getInstance();
    $cu = wp_get_current_user();
    $bright->setAndAuthenticateCurrentUser($cu);
    $bright->log("in woocommerce_order_status_processing callback for order #({$order_id})");
    BrightWoocommerceIntegration::manageOrder($order_id,true);
  }, 8);

global $bright_embedder_templates;

if (empty($bright_embedder_templates))  /* you have to do this in case the variable isn't loaded yet */
  $bright_embedder_templates = array();

$bright_embedder_templates['classic_paywall'] = <<<EOF
{{header_text}}
{{#if registration}}
{{stats_table}}
{{clear}}
{{launchbutton}}
{{else}}
This course will become available after purchase and the successful processing of your payment.
{{/if}}
EOF;

function bright_menu_woocommerce() {
  if (!current_user_can('manage_options')) {
    wp_die(bright_message('You do not have sufficient permissions to access this page.'));
  }
  $hidden_field_name = 'bright_submit_hidden';

  // variables for the field and option names
  $opt_name = 'bright_shared_key_ordering_enabled';
  $data_field_name = $opt_name;

  // Read in existing option value from database
  $opt_val = get_option( $opt_name );

  if(isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y') {
      // Read their posted value
      $opt_val = $_POST[ $data_field_name ];

      // Save the posted value in the database
      update_option( $opt_name, $opt_val );
  }

?>
  <br/>
  <h1>Bright Woocommerce Integration Settings</h1>
  <form name="form1" method="post" action="">
    <div>
      <input type="checkbox" name="bright_shared_key_ordering_enabled" <?php if ($opt_val) { echo 'checked="checked"'; } ?>/>
      <span>Add License Key Option To Checkout Page</span>
    </div>
    <table>
    <tr>
      <td>
        <p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>"/></p>
      </td>
      <td>
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
      </td>
    </tr>
    </table>
</form>
<?php
}

/* adds the bright menu to the WP dashboard */
function bright_woocommerce_menu() {
  $bright_menu_slug = 'bright_options';
  /* add_submenu_page( $bright_menu_slug, 'Sync User Data' , 'UserMeta Sync', 'manage_options', $bright_menu_slug . "_sync" , 'bright_menu_sync'); */
  /* TODO .... commented out below as it generates a stack trace */
  /* add_submenu_page( $bright_menu_slug, 'WooCommerce Integration', 'WooCommerce Integration' , 'manage_options',  $bright_menu_slug . "_woocommerce", 'bright_menu_woocommerce'); */
}
add_action('admin_menu', 'bright_woocommerce_menu', 99);

/**
 * This is a callback from the woocommerce_checkout_update_order_meta action.  It has the effect
 * of setting the 'bright_license' field on the order.
 * @params $order_id integer ID of the order.
 * @return boolean returns true if order successfully updated.
 * 
 */
function bright_license_field_update_order_meta($order_id) {
  if (!empty($_POST['bright_license_check']))
    return BrightWoocommerceIntegration::setLicenseOrder($order_id,sanitize_text_field($_POST['bright_license_check']));
}
add_action( 'woocommerce_checkout_update_order_meta', 'bright_license_field_update_order_meta' );

/**
 * A Filter on update_user_metadata.   Looks for 'bright_licenses' user meta data and updates the user licenses accordingly.
 * Argument 1 is ignored.
 *
 * See https://codex.wordpress.org/Plugin_API/Filter_Reference/update_(meta_type)_metadata
 * https://core.trac.wordpress.org/browser/tags/3.7.1/src/wp-includes/meta.php#L122
 *
 * @return void
 * @access public
 *
 */

add_filter('update_user_metadata', 'BrightWoocommerceIntegration::doProfileChange',10,5);


/**
 * Process the checkout
 */
add_action('woocommerce_checkout_process', 'BrightWoocommerceIntegration::custom_checkout_field_process');


?>
