<?php

class BrightWoocommerceIntegrationWcTest extends BWCTestCase {
  private $reg_id; // Used to keep a reg guid handy for Bright callApi callbacks.
  private $crs_id; // Used to keep a course guid handy for Bright callApi callbacks.
  
  
  function __construct() {
    parent::__construct();
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1'; // Required, else wc_create_order throws an exception
    $_SERVER['SERVER_NAME'] = 'phpunit'; // Otherwise updating the order status to 'completed' will stack trace.
  }
  
  /* write metadata into a product and then read it right back out */
  function test_basicProductMetadataReadAndWrite() {
    $product = WC_Helper_Product::create_simple_product();
    $bm = '{"testx": "foo"}';
    update_post_meta($product->id, BrightWoocommerceConstants::BRIGHT_METADATA, $bm);
    $bright_metadata = BrightWoocommerceIntegration::getBrightProductMetadata($product);
    $this->assertEquals($bright_metadata->testx, "foo");
  }
  
  
  /* validate that the determine quantity function works correctly */
  function test_determineQuantityWithMetadata() {
    $product = WC_Helper_Product::create_simple_product();
    $guid = BrightTestFactory::aValidCourseGuid();
    
    $bm = '{
 "bright-courses": [
   {
     "course-id": "'.$guid.'",
     "quantity-multiplier": 6
   }
 ]
}';
    
    update_post_meta($product->id, BrightWoocommerceConstants::BRIGHT_METADATA, $bm);
    $bright_metadata = BrightWoocommerceIntegration::getBrightProductMetadata($product);
    
    $this->assertEquals($bright_metadata->{'bright-courses'}[0]->{'course-id'},$guid);
    $this->assertEquals($bright_metadata->{'bright-courses'}[0]->{'quantity-multiplier'},6);
    
    $quantity = BrightWoocommerceIntegration::determineQuantity(2,
                                                                $guid,
                                                                $bright_metadata);
    
    $this->assertEquals($quantity,12);
  }

  /**
   * test helper to create an order
   */
  function createOrder($order_data = array()) {
    $user = $this->bright->getCurrentUser();
    
    $order = wc_create_order( array_merge($order_data, array('customer_id' => $user->ID)));
    return $order;
  }
  
  /**
   * A simple test to show that the BrightWoocommerceIntegration::setLicenseOrder and 
   * BrightWoocommerceIntegration::isLicenseOrder work as expected.
   */
  function test_brightLicenseFieldSetting() {
    $order = $this->createOrder(array('status' => 'pending',
                                      'customer_note' => 'test_brightLicenseFieldSetting'));
    
    $this->assertNotEmpty($order);
    /* we do it a few times to make sure there aren't weird side effects */
    $this->assertTrue(BrightWoocommerceIntegration::setLicenseOrder($order->id,'1'));
    $this->assertTrue(BrightWoocommerceIntegration::isLicenseOrder($order));
    $this->assertTrue(BrightWoocommerceIntegration::setLicenseOrder($order->id,'0'));
    $this->assertFalse(BrightWoocommerceIntegration::isLicenseOrder($order));
    $this->assertTrue(BrightWoocommerceIntegration::setLicenseOrder($order->id,'1'));
    $this->assertTrue(BrightWoocommerceIntegration::isLicenseOrder($order));
    $this->assertTrue(BrightWoocommerceIntegration::setLicenseOrder($order->id,'0'));
    $this->assertFalse(BrightWoocommerceIntegration::isLicenseOrder($order));
  }
  
  /**
   *
   */
  function test_simpleLicenseOrder() {
    $this->insureCurrentUser();
    $order = $this->createOrder(array('status' => 'pending',
                                      'customer_note' => 'test_simpleLicenseOrder'));
    
    $this->assertNotEmpty($order);
    $product = WC_Helper_Product::create_simple_product();

    update_post_meta($product->id, BrightWoocommerceConstants::BRIGHT_COURSE_ID, BrightTestFactory::aValidCourseGuid());
    $item_id = $order->add_product($product,2);
    
    $item_count = $order->get_item_count();
    $this->assertEquals($item_count, 2);
    
    /* make it a license Order */
    
    $this->assertTrue(BrightWoocommerceIntegration::setLicenseOrder($order->id,'1'));
    $this->assertTrue(BrightWoocommerceIntegration::isLicenseOrder($order));

    $order->update_status("processing"); /// and they're off,
    $this->assertEquals($order->get_status(),"completed");

    $license_id = BrightWoocommerceIntegration::getBrightLicenseId($order);
    $this->assertNotEmpty($license_id);

    $invitation = $this->bright->callApi('invitation', array('params' => array('name' => $license_id)))[0];
    $this->assertEquals($license_id,$invitation->name);
    $this->assertEquals($invitation->course_guids[0], BrightTestFactory::aValidCourseGuid());
    $json = json_decode($invitation->custom);

    $this->assertTrue($json->license);
    $this->assertEquals($order->id,$json->order_id);
    $this->assertEquals($json->course_guids[0],BrightTestFactory::aValidCourseGuid());
    $this->assertEquals($json->license_data->{BrightTestFactory::aValidCourseGuid()}->seats_available,2);
    
  }    
  
  function test_brightMultiCourseOrder() {
    $this->insureCurrentUser();
    $order = $this->createOrder(array('status' => 'pending',
                                      'customer_note' => 'wut?',
                                      'total' => ''));
    
    $this->assertNotEmpty($order);
    
    $products = array();
    $guids = BrightTestFactory::validCourseGuids();
    $item_count = $order->get_item_count();
    $this->assertEquals($item_count, 0);
    
    $num_products = 2;
    
    for ($x = 0; $x <= ($num_products-1); $x++) {
      $product = WC_Helper_Product::create_simple_product();
      $this->assertNotEmpty($product);
      update_post_meta($product->id, BrightWoocommerceConstants::BRIGHT_COURSE_ID, $guids[$x]);
      array_push($products,$product);
      $item_id = $order->add_product($product);
      $this->assertNotEmpty($item_id);
    }
    
    $item_count = $order->get_item_count();
    $this->assertEquals($item_count, $num_products);
    
    $order->update_status("processing");
    $comments = BWC_Helper::orderComments($order->id);
    
    for ($x = 0; $x <= ($num_products-1); $x++) {
      $reg_note = null;
      $this->crs_id = $guids[$x];
      foreach ($comments as $comment)
        if (strpos($comment->comment_content, 'creating bright registration') !== false &&
            strpos($comment->comment_content, $this->crs_id) !== false)
          $reg_note = $comment;
      $this->assertNotEmpty($reg_note);
      $this->assertContains('registration ID', $reg_note->comment_content);
      preg_match("/.* registration ID '(.+)'/", $reg_note->comment_content, $matches_out);
      
      $this->reg_id = $matches_out[1];
      $responseHandler = function ($rsp, $curlInfo, $curlError) {
        $this->assertEquals($curlInfo['http_code'], "200");
        $this->assertEquals(count($rsp), 1);
        $this->assertEquals($rsp[0]->registration_guid, $this->reg_id);
        $this->assertEquals($rsp[0]->course_guid, $this->crs_id);
      };
      
      $this->bright->callApi('registration', array('params' => array('registration_guid' => $this->reg_id),
                                                   'success' => $responseHandler,
                                                   'failure' => $responseHandler));
    }
  }
  
  function test_brightSingleCourseRegistrationCreation() {
    $this->insureCurrentUser();
    $order = $this->createOrder(array('status' => 'pending',
                                      'customer_note' => 'wut?',
                                      'total' => ''));
    
    $product = WC_Helper_Product::create_simple_product();
    
    // loaded from bright/tests/bright-test-factory.php
    $this->crs_id = BrightTestFactory::aValidCourseGuid();
    
    // set the 'bright_course_id' for this product.
    // This is the thing to change to test different scenarios
    update_post_meta($product->id, BrightWoocommerceConstants::BRIGHT_COURSE_ID, $this->crs_id);
    
    $item_id = $order->add_product($product);
    
    $item_count = $order->get_item_count();
    $this->assertEquals($item_count, 1);
    
    $order->update_status("processing");
    $comments = BWC_Helper::orderComments($order->id);
    
    $reg_note = null;
    foreach ($comments as $comment)
      if (strpos($comment->comment_content, 'creating bright registration') !== false)
        $reg_note = $comment;
    
    $this->assertNotEmpty($reg_note);
    $this->assertContains('registration ID', $reg_note->comment_content);
    preg_match("/.* registration ID '(.+)'/", $reg_note->comment_content, $matches_out);
    $this->reg_id = $matches_out[1];
    $responseHandler = function ($rsp, $curlInfo, $curlError) {
      $this->assertEquals($curlInfo['http_code'], "200");
      $this->assertEquals(count($rsp), 1);
      $this->assertEquals($rsp[0]->registration_guid, $this->reg_id);
      $this->assertEquals($rsp[0]->course_guid, $this->crs_id);
    };
    
    $this->bright->callApi('registration', array('params' => array('registration_guid' => $this->reg_id),
                                                 'success' => $responseHandler,
                                                 'failure' => $responseHandler));

    $this->assertEquals($order->get_status(),"completed");
  }

  function test_brightSingleCourseRegistrationAutocompleteDisabled() {
    $this->insureCurrentUser();
    $order = $this->createOrder(array('status' => 'pending',
                                      'customer_note' => 'test_brightSingleCourseRegistrationAutocompleteDisabled'));
    
    $product = WC_Helper_Product::create_simple_product();
    
    // loaded from bright/tests/bright-test-factory.php
    $this->crs_id = BrightTestFactory::aValidCourseGuid();
    
    // set the 'bright_course_id' for this product.
    // This is the thing to change to test different scenarios
    update_post_meta($product->id, BrightWoocommerceConstants::BRIGHT_COURSE_ID, $this->crs_id);
    
    $item_id = $order->add_product($product);
    
    $order->update_status("processing");
    $this->assertEquals($order->get_status(),"completed");
    // ok let's shut it off via filter [the autocomplete part]
    add_filter('bright_woocommerce_integration_autocomplete_order_default',function($default) {
        return false;
      });
    $order->update_status("processing");
    $this->assertEquals($order->get_status(),"processing");
  }


}
