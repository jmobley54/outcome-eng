<?php

class BrightDeprecationTest extends BrightApiTestBase {

  /**
   * 
   */
  public function test_brightCurrentUser() {
	$bright = Bright\Wordpress::getInstance();
	$user = bright_get_user();
	$this->assertNotEmpty($user);
	$this->assertInternalType('object',$user);
	$this->assertGreaterThan(0,$user->ID);
  }
  
  /**
   * @group wp-deprecation
   */
  function test_curlErorMsg() {
	$this->assertNotEmpty(bright_curl_error());
  }

  /**
   * @group wp-deprecation
   */
  function test_returnAsJavascript() {
	$this->assertNotEmpty(bright_return_as_javascript('var aJsVar = 1;'));
  }

  /**
   * @group wp-deprecation
   */
  function test_curl() {
	global $bright_curl_error;
	global $bright_curl_info;

	$this->assertNotEmpty(bright_curl('localhost:3000','GET'));
	$this->assertNotEmpty($bright_curl_info);
	bright_curl('localhost:34566','GET');
	$this->assertNotEmpty($bright_curl_error);
  }

  /**
   * @group wp-deprecation
   */
  function test_bright_stop() {
	$ret = bright_stop();
	$this->assertFalse($ret);
  }
  /**
   * @group wp-deprecation
   */
  function test_bright_get_user() {
	$ret = bright_get_user();
	$this->assertNotEmpty($ret);	
  }

  function test_bright_register_user_to_course() {
	$bright = Bright\Wordpress::getInstance();
	$email = $bright->getUserEmail($bright->getCurrentUser());
	$courseGuid = "PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446";

	$reg = bright_register_user_to_course($bright->accessToken,$email,$courseGuid);

	$http_code = $bright->curlInfo['http_code'];
	$this->assertEquals($http_code,"302", 'Check valid status code ...');
	$this->assertNotEmpty($reg);
	$this->assertEquals($courseGuid,$reg->course_guid);
	$this->assertNotEmpty($reg->registration_guid);
  }

  function test_create_invitation() {
	$custom = array('order' => 1,
					'order_id' => 4000);
	$course_guids = array('SequencingSimpleRemediation_SCORM20043rdEditioncf47e718-3a10-47ce-8a86-ecd1bec21fae',
						  'PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446');
	$license_data = array('SequencingSimpleRemediation_SCORM20043rdEditioncf47e718-3a10-47ce-8a86-ecd1bec21fae' => array('seats_available' => 2),
						  'PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446' => array('seats_available' => 3));
	$result = BrightV1Api::create_invitation(array('license' => true,
												   'course_guids' => $course_guids,
												   'license_data' => $license_data,
												   'custom' =>  $custom));
	$this->assertNotEmpty($result);
	$this->assertEquals($this->bright->curlHttpCode,201);
	$guids = $result->course_guids;
	$this->assertEquals(sizeof($guids),2);
	$this->assertTrue(in_array('SequencingSimpleRemediation_SCORM20043rdEditioncf47e718-3a10-47ce-8a86-ecd1bec21fae',$guids));
	$this->assertTrue(in_array('PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446',$guids));
  }

  function test_get_api_key() {
	$rsp = BrightV1Api::_get_api_key('support@aura-software.com');
	$this->assertNotEmpty($rsp);
	$this->assertEquals(strlen($rsp), 32);
  }

  function test_register_user_to_course() {
	$rsp = BrightV1Api::register_user_to_course($this->bright->accessToken,
												'support@aura-software.com',
												'PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446',
												'Aura',
												'Support');

	$this->assertNotEmpty($rsp);
	$this->assertNotEmpty($rsp->registration_guid);
  }

  function test_is_user_registered_to_course() {
    $rsp = BrightV1Api::is_user_registered_to_course('PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446');
    $this->assertNotEmpty($rsp);
    $rsp = BrightV1Api::is_user_registered_to_course('PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446xxxxxxxxxx');
    $this->assertFalse($rsp);
  }
}
