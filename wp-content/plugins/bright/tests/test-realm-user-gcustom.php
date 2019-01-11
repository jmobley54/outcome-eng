<?php
/**
 *
 */
  
/**
 *
 */
class BrightRealmUserTest extends BrightApiTestBase {
  /**
   *
   */
  public function test_realmUserGcustom() {
	$bright = Bright\Wordpress::getInstance();
	
	$ret = $bright->updateAllUsers();
	$this->assertNotEmpty($ret);
	$this->assertInternalType('array',$ret);
	$this->assertEquals($ret['success'],$ret['records']);
  }
}
