<?php

/**
 * I PUT THIS LAST 'ZZZ' as it really should be run by itself ....
 * it requires a different object ; but how should the bootstrapper deal with this?????
 * 
 *  FOR MMA BitBucket #12
 *
 *  ONLY to be run via
 *
 *  phpunit tests/test-zzz.php
 *
 */

class BitbucketTicket12 extends BrightUnauthenticatedTestBase {

  function setPost() {
	global $post;
	$post_id = $this->factory->post->create_object(array('post_status' => 'publish',
														 'post_title' => 'test post',
														 'post_content' => 'dummy content',
														 'post_type' => 'post'));
	$post = get_post($post_id);
  }


  /**
   * 
   */
  public function test_authenticateEmailAddress() {
	$bright = Bright\Wordpress::getInstance();

    $email = 'simona@medizen-medien.at';

    $token = $bright->callApi('api_key', array('method' => 'POST',
                                               'accessMode' => 'realm',
                                               'params' => array('user_email' => $email),
                                               'success' => function ($rsp) {
                                                 return $rsp->access_token;
                                               }));

    $this->assertNotNull($token);
	$this->assertEquals(strlen($token), 32);
  }

  public function test_expansionWithoutWPUser() {
	$bright = Bright\Wordpress::getInstance();

    $email = 'simona@medizen-medien.at';

    $token = $bright->callApi('api_key', array('method' => 'POST',
                                               'accessMode' => 'realm',
                                               'params' => array('user_email' => $email),
                                               'success' => function ($rsp) {
                                                 return $rsp->access_token;
                                               }));

    $this->assertNotNull($token);
	$this->assertEquals(strlen($token), 32);

    $bright->accessToken = $token;
	$this->setPost();

	$result = $bright->expandShortcode(array('type'=>'course',
											 'course' => "PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446"), 
									   "this is my template ");
	$this->assertRegExp('/div id=\"bright-course-/',$result,'we expected a div'); 
	$this->assertRegExp('/Bright.addTemplate/',$bright->footerJs,'we expected a div'); 
	$bright->log($result,false,'the result');
	$bright->log($bright->footerJs,false,'the generated footer JS');



  }

  public function test_courselistExpansionWithoutWPUser() {
	$bright = Bright\Wordpress::getInstance();

    $email = 'simona@medizen-medien.at';

    $token = $bright->callApi('api_key', array('method' => 'POST',
                                               'accessMode' => 'realm',
                                               'params' => array('user_email' => $email),
                                               'success' => function ($rsp) {
                                                 return $rsp->access_token;
                                               }));

    $this->assertNotNull($token);
	$this->assertEquals(strlen($token), 32);

    $bright->accessToken = $token;
	$this->setPost();

	$result = $bright->expandShortcode(array('type'=>'courselist', 
											 'id' => 'mycontainerid'),
									   "this is my template");
	/* $this->assertRegExp('/div id=\"bright-course-/',$result,'we expected a div');  */
	/* $this->assertRegExp('/Bright.addTemplate/',$bright->footerJs,'we expected a div');  */

	$bright->log($result,false,'the result');
	$bright->log($bright->footerJs,false,'the generated footer JS');

  }

  
}
