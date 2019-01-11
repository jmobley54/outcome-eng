<?php

class BrightWordpressTest extends BrightApiTestBase {
  function test_requestCode() {
    /* hmmmm .... there is no request code is there? */
    $rc = $this->bright->getRequestCode();
	$this->assertNotEmpty($rc);
  }
  function test_getUserAttributes() {
  	$ret=$this->bright->getUserAttributes();
	$this->assertRegExp('/support@aura-software.com/',$ret);
  }
  
  function test_getEmail() {
	$email = $this->bright->getEmail($this->bright->getCurrentUser());
	$this->assertEquals($email,"support@aura-software.com");
  }
  
  function test_doHeader() {
	$ret = $this->bright->doHeader();
	$this->assertRegExp('/content=\'support@aura-software.com/',$ret);
  }
  
  function test_syncMenu() {
	$bright = Bright\Wordpress::getInstance();
	
	$ret = $bright->syncMenu();
	$this->assertTrue(!empty($ret));
  }
  
  function test_menuOverview() {
	$bright = Bright\Wordpress::getInstance();
	
	$ret = $bright->overviewMenu();
	$this->assertTrue($ret);
  }
  
  /**
   * @group updateAllUsers
   * Tests the api edit form
   */
  function test_updateAllUsers() {
	$bright = Bright\Wordpress::getInstance();
	$this->assertNotEmpty($bright);
	$args = array();
	
	$bright->updateAllUsers($args);
  }
  
  /**
   * @group echoErrorBlock
   */
  function test_echoErrorBlock() {
	$bright = Bright\Wordpress::getInstance();
	$ret = $bright->errorBlock('a msg');
	$this->assertNotEmpty($ret);
  }
  
  function test_expandShortCode() {
  }
  
  function test_getSupportEmail() {
	$ret = $this->bright->getSupportEmail();
	$this->assertNotEmpty($ret);
	$this->assertEquals($ret,'support@aura-software.com');
  }

}
