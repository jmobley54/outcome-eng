<?php
/**
 * The javascript generator.  This class encapsulates the javascript injected into a page for use by bright.js
 */

namespace Bright;

  /**
   *
   */

class JavascriptGenerator {

  /**
   *
   */
  static function returnAsJavascript($msg) {
	return Base::returnAsJavascript($msg);
  }

  /**
   *
   */
  static function initializeBrightCourses() {
	return Base::returnAsJavascript("if (typeof bright_courses === 'undefined') {bright_courses = {};}");
  }
  /**
   *
   */
  static function addBrightCourse($course_id,$raw_course_data) {
	return self::returnAsJavascript("bright_courses['" . $course_id . "'] = " . substr($raw_course_data,1,-1) . ";");
  }
  /**
   *
   */
  static function addRegistrationDataForCourse($course_id,$raw_registration_data) {
	self::returnAsJavascript("bright_courses['" . $course_id . "']['registrations'] = " . $raw_registration_data . ";");
  }

  /**
   *
   */
  static function newBrightDiv(array $args = array()) {
    $typeString = extractFromArray($args,'typeString');
    $container_id = extractFromArray($args,'containerId');
    $templateClass = extractFromArray($args,'templateClass');
    $embedClass = extractFromArray($args,'embedClass');
    $templateName = extractFromArray($args,'templateName');

	/* kill off 'launchbox' in favor of 'course' */
	$deprecatedClassName = ($typeString === "course") ? "bright-launchbox " : "";
	$deprecatedClassName = '';
	$as = "addslashes"; // Only way to get it to work inside the EOF
    if (!empty($templateName)) 
      $templateDivClass = " bright-template-{$templateName}";
    else
      $templateDivClass = '';
    
      
	$new_content = <<<EOF
<div id="bright-{$typeString}-{$as($container_id)}" class="{$as($deprecatedClassName)}{$as($templateClass)} {$as($embedClass)}{$as($templateDivClass)}"></div>
EOF;
    return $new_content;
  }
  /**
   *
   */
  static function newCourselist($bright_course_list) {
	return self::returnAsJavascript("var bright_courselist =" . ($bright_course_list ? $bright_course_list : "[]") . ";");
  }

  /**
   *
   */
  public static function addTemplate($container_id,$type,array $params) {
	$as = "addslashes"; // Only way to get it to work inside the EOF
	
	$template_js = <<<EOF
Bright.addTemplate("{$as($container_id)}", '{$type}', {
  courseId: "{$as($params['courseId'])}",
  embedLocale: "{$as($params['embedLocale'])}",
  embedClass: "{$as($params['embedClass'])}",
  embedType: "{$as($params['embedType'])}",
  embedAttributes: "{$as($params['embedAttributes'])}",
  pageAttributes: "{$as($params['pageAttributes'])}",
  userAttributes: "{$as($params['userAttributes'])}",
  customData: "{$as($params['customData'])}",
  templateName: "{$as($params['templateName'])}",
  template: "{$as(rawurlencode($params['template']))}"
});
EOF;
   
  return self::returnAsJavascript($template_js);
  }

}
