<?php
/**
 *
 */
class BrightEmbedderTest extends BrightApiTestBase {
  /**
   * @group getCourseData
   */

  function setPost(array $options = array()) {
	global $post;

    $defaultContent = array('post_status' => 'publish',
                            'post_title' => 'test post',
                            'post_content' => 'dummy content',
                            'post_type' => 'post');

    $post_id = $this->factory->post->create_object(array_merge($defaultContent,$options));
	$post = get_post($post_id);
  }

  function test_courseTemplateNoGUID() {
	$bright = Bright\Wordpress::getInstance();
	$this->setPost();
	$result = $bright->expandShortcode(array('type'=>'course'), " this is my template ");
	$this->assertRegExp('/No course ID/',$result,'we expected a failure for not setting a course ID');
  }

  function test_courseTemplateValidGuid() {
	$bright = Bright\Wordpress::getInstance();
	$this->setPost();
	$result = $bright->expandShortcode(array('type'=>'course',
											 'course' => "PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446"), 
									   "this is my template ");
	$this->assertRegExp('/div id=\"bright-course-/',$result,'we expected a div'); 
	$this->assertRegExp('/Bright.addTemplate/',$bright->footerJs,'we expected a div'); 
	$bright->log($result,false,'the result');
	$bright->log($bright->footerJs,false,'the generated footer JS');
  }

  function test_courseList() {
    $bright = Bright\Wordpress::getInstance();
	$this->setPost();
	$result = $bright->expandShortcode(array('type'=>'courselist', 
											 'id' => 'mycontainerid'),
									   "this is my template");
	/* $this->assertRegExp('/div id=\"bright-course-/',$result,'we expected a div');  */
	/* $this->assertRegExp('/Bright.addTemplate/',$bright->footerJs,'we expected a div');  */
	$bright->log($result,false,'the result');
	$bright->log($bright->footerJs,false,'the generated footer JS');
  }

  function test_templateDiv() {
    $bright = Bright\Wordpress::getInstance();
    global $post; /* see https://codex.wordpress.org/Displaying_Posts_Using_a_Custom_Select_Query */
	$this->setPost(array('post_content' => '[bright template="classic" course="PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446"]'));
    setup_postdata($post);

    ob_start(); /* https://digwp.com/2009/07/putting-the_content-into-a-php-variable/ */
    the_content();
    $content = ob_get_clean();

	$this->assertRegExp('/div id=\"bright-course-/',$content,'we expected a div');
	$this->assertRegExp('/inline/',$content,'we expected an inline class');
	$this->assertRegExp('/bright-course/',$content,'we expected an bright-course class');
	$this->assertRegExp('/bright-template-classic/',$content,'we expected an bright-template-classic class');

   }

  /* TODO: WIP
     Set bright-stop and/or bright-start, and verify we get the right functionality. */

  /* function test_templateDivStop() { */
  /*   $bright = Bright\Wordpress::getInstance(); */
  /*   global $post; /\* see https://codex.wordpress.org/Displaying_Posts_Using_a_Custom_Select_Query *\/ */
  /*   $this->setPost(array('post_content' => '[bright template="classic" course="PSOAS_SCORM_12-17318b2dc9-7128-4e7e-b7e3-fa591b7fc446"]')); */
  /*   setup_postdata($post); */
  /*   update_post_meta($post->ID,'bright-stop','1'); */
  /*   the_content(); */

  /*   ob_start(); /\* https://digwp.com/2009/07/putting-the_content-into-a-php-variable/ *\/ */
  /*   the_content(); */
  /*   $content = ob_get_clean(); */

  /*  } */


}