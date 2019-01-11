<?php

class BrightTest extends BrightApiTestBase {

  /**
   * Tests whether after initialization, URLs are returning something valid.
   */
  public function test_apiUrl() {
	$this->assertNotEmpty($this->bright->apiRoot);
  }


  function test_echoErrorBlock() {
  	$bright = Bright\Wordpress::getInstance();
  	$ret = $bright->errorBlock('dummy msg');
  	$this->assertNotEmpty($ret);
  }

  function test_BrightBasicWPWebstack() {
  	// replace this with some actual testing code
  	$bright = Bright\Wordpress::getInstance();

  	$this->assertNotEmpty($bright);
  	$user= $bright->getUserByEmail('admin@example.org');
  	$this->assertNotEmpty($user);
  	$id = $bright->getUserId($user);
  	$this->assertNotEmpty($id);
  	$this->assertGreaterThan(0,$id);
  	$this->assertTrue($bright->isValidUser($user));
  	$this->assertNotEmpty($bright->getEmail($user));

  	$user= $bright->getUserByEmail('nosuchuser@example.org');
  	$this->assertFalse($bright->isValidUser($user));
  }
  /**
   * @group getUserByEmail
   */

  function test_BrightWpCreateUser() {
  	// replace this with some actual testing code
  	$bright = Bright\Wordpress::getInstance();
  	$user = $bright->createUser('aurasupport',
  								'changeme!',
  								'support@aura-software.com');
  	$this->assertInternalType('object',$user);
  	$this->assertNotEmpty($user);
  }
  function test_BrightWpGetCurrentUser() {
  	$bright = Bright\Wordpress::getInstance();
  	$user = $bright->getCurrentUser();
  	$this->assertInternalType('object',$user);
  	$this->assertNotEmpty($user);
  }

  function test_BrightBrightCurl() {
	$bright = Bright\Wordpress::getInstance();
	$rsp = $bright->curl('localhost:3000');
  }

  /**
   * It seems when this test was written, we'd already been testing a bit and had created a cached key.
   * Now that I've restarted testing on a new enviornment, the cached key isn't there, so this test seems broken.
   * TODO: set up logic so that the cached key can be created, then select it back out using the call below.
   *
   * @group api_key
   * @group broken
   */
  function test_FetchApiKey() {
	$bright = Bright\Wordpress::getInstance();

	$user =	$bright->getCurrentUser();
	$bright->log($user,false,'user');

	$this->assertNotEmpty($user);

    if ($bright->setAccessTokenFromValidCachedKey($user))
      $cacheKey = $bright->accessToken;

    #\Bright\Wordpress::testEcho($cacheKey);
	/* $this->assertNotEmpty($cacheKey); */
	/* $this->assertEquals(strlen($cacheKey), 32); */
	/* $this->assertEquals($bright->accessToken,$cacheKey); */
  }

  /**
   * @group getInstance
   */

  function test_getInstance() {
  	$bright = Bright\Wordpress::getInstance();
  	$this->assertNotEmpty($bright);
  }


  /**
   * @group getReportageUrls
   */

  function test_GetReportageUrls() {
  	$bright = Bright\Wordpress::getInstance();

  	$ret = $bright->getReportageUrls();
  	$this->assertNotEmpty($ret);
  }

  /**
   * @group getTemplateFromApi
   * @group templates
   */

  function test_getTemplateFromApi() {
  	$bright = Bright\Wordpress::getInstance();

  	$ret = $bright->getTemplateFromApi('nosuchtemplate');
  	$this->assertTrue(empty($ret));
  }

  /**
   * @group getTemplateText
   * @group templates
   */
  function test_getTemplateText() {
  	$bright = Bright\Wordpress::getInstance();
  	$ret = $bright->getTemplateText(array('template' => 'nosuchtemplate'));
  	$this->assertNull($ret);
  }

  /**
   * @group getTemplateText
   * @group templates
   */
  function test_getTemplateTextWithFilter() {
  	$bright = Bright\Wordpress::getInstance();

	global $test_getTemplateTextWithFilter_numtemplates;
	$pre_filter_size = sizeof($bright->getLocalTemplates());

	add_filter('bright_templates', function (array $templates = array()) {
		global $test_getTemplateTextWithFilter_numtemplates;
		$test_getTemplateTextWithFilter_numtemplates = sizeof($templates);
		return $templates;
	  });

  	$ret = $bright->getTemplateText(array('template' => 'nosuchtemplate'));
  	$this->assertNull($ret);
	$this->assertEquals($pre_filter_size,$test_getTemplateTextWithFilter_numtemplates);

	add_filter('bright_templates', function (array $templates = array()) {
		$templates['nosuchtemplate'] = 'Some text';
		return $templates;
	  });

  	$ret = $bright->getTemplateText(array('template' => 'nosuchtemplate'));
  	$this->assertNotEmpty($ret);
	$this->assertRegExp('/Some text/',$ret);
  }

  function test_isUserLoggedInFilter() {
  	$bright = Bright\Wordpress::getInstance();
    
    $isUserLoggedIn = $bright->isUserLoggedIn();
  	$this->assertFalse($isUserLoggedIn);    
    add_filter('bright_is_user_logged_in', function() {
        return true;
      });
    $isUserLoggedIn = $bright->isUserLoggedIn();
  	$this->assertTrue($isUserLoggedIn);    
  }
  
  function test_getCurrentUserFilter() {
  	$bright = Bright\Wordpress::getInstance();
    $user = $bright->getCurrentUserFromWebstack();
  	$this->assertEquals($user->ID,0);
    add_filter('bright_get_wp_current_user', function($user) {
        if (empty($user->ID))
          $user = get_user_by('email','support@aura-software.com');
        return $user;
      });

    $user = $bright->getCurrentUserFromWebstack();
  	$this->assertEquals($user->user_email, 'support@aura-software.com');
  }

}

