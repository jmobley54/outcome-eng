<?php
/**
 *
 */
class BrightApiTest extends BrightApiTestBase {
  /**
   * @group getCourseData
   */
  function test_getCourseDataAll() {
	$bright = Bright\Wordpress::getInstance();

	$rsp=$bright->getCourseData();
	$this->assertNotEmpty($rsp);
	$this->assertInternalType('array',$rsp);
	$this->assertGreaterThan(0,sizeof($rsp));
	$firstRow = $rsp[0];
	$this->assertNotEmpty($firstRow);
	$this->assertInternalType('object',$firstRow);
  }

  /**
   * @group getCourseData
   */
  function test_getCourseDataGuid() {
	$bright = Bright\Wordpress::getInstance();

	$rsp=$bright->getCourseData(array('params' => array('course_guid' => "PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446")));

	$this->assertNotEmpty($rsp);
	$this->assertInternalType('array',$rsp);
	$bright->log($rsp,false,"getCourseData response");
	$this->assertEquals(sizeof($rsp),1);
	$firstRow = $rsp[0];
	$this->assertNotEmpty($firstRow);
	$this->assertInternalType('object',$firstRow);
	$this->assertEquals($firstRow->title,"The Role of the Psoas in Yoga Asana and Postural Health");
  }

  /**
   * @group getCourseDataByGuid
   * @group getCourseData
   */
  function test_getCourseDataByGuidNotFound() {
	$bright = Bright\Wordpress::getInstance();
	$this->assertEmpty($bright->getCourseDataByGuid("PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446c"));
  }

  /**
   * @group getRegistrationDataForCourse
   * @group registration
   */
  public function test_getRegistrationDataForCourse() {
	$bright = Bright\Wordpress::getInstance();
	$nosuchcourse = $bright->getRegistrationDataForCourse("PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446c");
	$this->assertEquals($nosuchcourse,'{}');
	$guid = "PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446";
	$rsp = $bright->getRegistrationDataForCourse($guid);
	$this->assertNotEmpty($rsp);
	$this->assertNotEmpty($rsp->course_guid);
	$this->assertEquals($rsp->course_guid,$guid);
  }


  /**
   */
  public function test_getRegistrationDataForCourseWhereThisIsNoReg() {
	$guid = "SequencingSimpleRemediation_SCORM20043rdEditioncf47e718-3a10-47ce-8a86-ecd1bec21fae";
	$ret = $this->bright->getRegistrationDataForCourse($guid);
	$this->checkCode("404", "bright getRegistrationDataForCourse should throw a 404 for unknown courses");
	$this->assertEquals($ret,'{}');
  }


  /**
   * @group getCourseDataByGuid
   * @group getCourseData
   */
  function test_getCourseDataByGuid() {
	$bright = Bright\Wordpress::getInstance();

	$rsp=$bright->getCourseDataByGuid("PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446");
	
	$this->assertNotEmpty($rsp);
	$this->assertInternalType('object',$rsp);
	$this->assertEquals($rsp->title,"The Role of the Psoas in Yoga Asana and Postural Health");

  }
  /**
   * @group getCourseDataByGuid
   * @group getCourseData
   */
  function test_getCourseDataByGuidRaw() {
	$bright = Bright\Wordpress::getInstance();

	$rsp=$bright->getCourseDataByGuid("PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446",array('raw' => true));
	$this->assertNotEmpty($rsp);
	$this->assertInternalType('string',$rsp);
  }
  /**
   * @group getCourseDataByGuid
   * @group getCourseData
   */
  function test_getCourseDataByGuidPublicMin() {
	$guid = "PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446";
	$rsp=$this->bright->getCourseDataByGuid($guid,array('api_template' => 'public_minimum'));
	$this->assertNotEmpty($rsp);
	$this->assertEquals($rsp->course_guid,$guid);
  }


  /**
   * @group getCourseDataByGuid
   * @group getCourseData
   */
  function test_getCourseDataByGuidSuccessCallback() {
	$bright = Bright\Wordpress::getInstance();

	$foo = false;
	$rsp=$bright->getCourseDataByGuid("PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446",
									  array('success' => function($rsp) use (&$foo) {
										  $foo = true;
										  return $rsp->title;
										}));

	$this->assertNotEmpty($rsp);
	$this->assertEquals($rsp,"The Role of the Psoas in Yoga Asana and Postural Health");
	$this->assertInternalType('string',$rsp);
	$this->assertTrue($foo);
  }

  /**
   * @group getApiCallback
   */
  function test_getApiCallback() {
	$courseGuid = "PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446";
	$gotCallback = false;
	$this->bright->callApi('course',array('params' => array('course_guid' => $courseGuid),
									'success' => function($rsp,$curlInfo,$curlError) use (&$bright,&$gotCallback) {
									  $gotCallback = true;
									})
					 );
	$this->assertTrue($gotCallback);
  }

  /**
   * NOTE: if you create a new registration for the test user, this test will begin to fail!
   */
  function test_createRegistration() {
	$email = $this->bright->getCurrentUserEmail();
	$courseGuid = "PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446";
	$reg = $this->bright->createRegistration(array('params' => array('learner_id' => $email,
                                                                     'dont_duplicate' => 1,
                                                                     'course_guid' => $courseGuid)));

	$this->assertEquals($this->bright->curlHttpCode,"302", 'Check valid status code ...');
	$this->assertNotEmpty($reg);
	$this->assertEquals($courseGuid,$reg->course_guid);
	$this->assertNotEmpty($reg->registration_guid);
  }

  function setupInvitation() {
	$bright = $this->bright;
    $name = 'A nice tidy invitation';
	$courseGuid = "PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446";
    
    $exists = $bright->invitationExists(array('params' => array('name' => $name)));
    if ($exists)
      return $exists;
    return $bright->createInvitation(array('params' => array('name' => 'A nice tidy invitation',
                                                             'course_guids' => array($courseGuid))));
  }

  function test_addLearnerToInvitation() {
	$bright = $this->bright;
    $invitation = $this->setupInvitation();

	$email = $bright->getUserEmail($bright->getCurrentUser());	
	$result = $bright->addLearnerToInvitation($email, $invitation->name,array('params' => array('skip_external_initialization' => 'true')));
	$this->assertNotEmpty($result);
	$this->assertEquals($result->name,"A nice tidy invitation");
  }

  function test_addLearnerToInvitationExtended() {
	$bright = $this->bright;
	$email = $bright->getUserEmail($bright->getCurrentUser());	
    $invitation = $this->setupInvitation();
	$result = $bright->addLearnerToInvitation($email, $invitation->name,array('params' => array('skip_external_initialization' => 'true',
                                                                                                'api_template' => 'extended')));
	$this->assertNotEmpty($result);
	$this->assertEquals($result->record->name,"A nice tidy invitation");
    $this->assertNotEmpty($result->messages);
	$this->assertEquals(count($result->messages),1);
  }

}


	