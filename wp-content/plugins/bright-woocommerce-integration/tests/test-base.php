<?php

class BrightWoocommerceIntegrationBaseTest extends BWCTestCase {
  function test_basic() {
    $this->assertNotEquals(1,0);
  }

  function test_bright() {
    $b = Bright\WordPress::getInstance();
    $this->assertNotEmpty($b);
  }

  /**
   * @group getCourseData
   * just sort of checking the bright is functioning.
   */
  function test_getCourseDataAll() {
    $rsp=$this->bright->getCourseData();
    $this->assertNotEmpty($rsp);
    $this->assertInternalType('array',$rsp);
    $this->assertGreaterThan(0,sizeof($rsp));
    $firstRow = $rsp[0];
    $this->assertNotEmpty($firstRow);
    $this->assertInternalType('object',$firstRow);
  }

  function test_wcisLoaded() {
  }

}
