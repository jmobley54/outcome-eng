<?php
/**
 *
 */
class SimpleTokenTest extends BrightApiTestBase {

  function test_apiToken() {
	$bright = Bright\Wordpress::getInstance();
	$bright->getAuthenticationCodeForUser($bright->getCurrentUser());
	$this->assertNotEmpty($bright->accessToken);
	$bright->log($bright->accessToken,false,'access token');
  }

  
}
