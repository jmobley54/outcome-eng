<?php
/**
 * This file creates a pre-bright 8.0 workalike for Wordpress.
 * But really don't use these calls.  They will be removed .... soon.
 */


function bright_run_stored_query($name,$params,$query_scope=nil) {
  bright_log('illegal/deprecated function bright_run_stored_query');
  return '{"bright_run_stored_query": "not implemented"}';

}


function bright_log($message,$doecho=false,$extra_text=null) {
  return Bright\Wordpress::getInstance()->log($message,$doecho,$extra_text);
}  
  

function bright_is_boolean_option_set($option) {
  return Bright\Wordpress::getInstance()->isBooleanOptionSet(array('option' => $option));
}
  

/* DEPRECATED: use Bright::register_user_to_course() */
function bright_register_user_to_course($api_key,$email,$course) {
  return BrightV1Api::register_user_to_course($api_key,$email,$course);
}

/**
 *
 */
function bright_update_user_meta($field,$learner) {
  $user = ($field === "bright_user") ? $learner : get_user_by($field,$learner);
  return Bright\Wordpress::getInstance()->updateBrightUserMeta($user);
}



/**
 *
 */
function bright_curl_error() {
  $bright = Bright\Wordpress::getInstance();
  return $bright->curlErrorMsg();
}

/**
 * @param string $url
 * @param string $method
 * @param array $data
 * @return String response data from the curl call
 */
function bright_curl($url, $method=null, $data=null, $showerrors=false) {
  global $bright_curl_error, $bright_curl_info;

  $bright = Bright\Wordpress::getInstance();
  $ret = $bright->curl($url, array('method' => $method, 'params' => $data));
  $bright_curl_error = $bright->curlError;
  $bright_curl_info = $bright->curlInfo;
  return $ret;
}

/**
 *
 */
function bright_stop() {
  $bright = Bright\Wordpress::getInstance();
  return $bright->stop();
}

/**
 *
 */
function bright_get_user() {
  return Bright\Wordpress::getInstance()->getCurrentUser();
}

/**
 *
 * Reset the global bright token to null
 */

function bright_reset_token() {
  $bright = Bright\Wordpress::getInstance();
  $bright->accessToken = null;
}

/**
 *
 */
function bright_return_as_javascript($code) {
  return Bright\Base::returnAsJavascript($code);
}

function bright_fetch_user_attributes($user,array $args = array()) {
  return Bright\Wordpress::getInstance()->getUserAttributes($user,$args);
}


/**
 * returns TRUE if we are in test mode 
 */
function bright_testing() {
  return Bright\Wordpress::getInstance()->isTesting();
}

function dump_bright_js_for_footer() {
  return Bright\Wordpress::getInstance()->doFooter();
}

class BrightV1Api {

  public static function is_user_registered_to_course($course_id,$use_cache=true) {
	return Bright\Wordpress::getInstance()->isUserRegisteredToCourse($course_id);
  }

  /* nodelay ... don't create a background job */
  public static function add_learner_to_invitation($learner_id,$invitation_name,$nodelay=false) {
	return Bright\Wordpress::getInstance()->addLearnerToInvitation($learner_id,
																   $invitation_name,
																   array('params' => array('nodelay' => $nodelay)));
  }

  #####
  public static function create_invitation(array $params=array()) {
	return Bright\Wordpress::getInstance()->createInvitation(array('params' => $params));
  }

  public static function _get_api_key($email) {
	return Bright\Wordpress::getInstance()->getAuthenticationCodeForEmail($email);
  }



  public static function register_user_to_course($api_key,$user_email,$course,$first_name=null,$last_name=null) {
	return Bright\Wordpress::getInstance()->createRegistration(array('params' => array('dont_duplicate' => 1,
																					   'course_guid' => $course,
																					   'learner_id' => $user_email,
																					   'first_name' => $first_name,
																					   'last_name' => $last_name)));
  }



  public static function fetch_bright_realm_key($course_provider_id=false) {
	/* seems to be only used here:
	   ./penman/bretsmac2/wp-content/plugins/penman-bright-customizations/penman-bright-customizations.php */
	/* return Bright\Wordpress::getInstance()-> */
	wp_die('deprecation function for BrightV1API::fetch_bright_realm_key not implemented');
  }

  public static function json_encode_unicode($data) {
	/* don't see any where this is used */
	/* return Bright\Wordpress::getInstance()-> */
	wp_die('deprecation function for BrightV1API::json_encode_unicode not implemented');
  }

  public static  function json_cb(&$item, $key) { 
	/* return Bright\Wordpress::getInstance()-> */
	wp_die('deprecation function for BrightV1API::json_cb not implemented');
  }


  public static  function my_json_encode($arr){
	/* return Bright\Wordpress::getInstance()-> */
	wp_die('deprecation function for BrightV1API::my_json_encode not implemented');
  }



  public static function realm_user_gcustom($learner_id,$data) {
	/* return Bright\Wordpress::getInstance()-> */
	wp_die('deprecation function for BrightV1API::realm_user_gcustom not implemented');
  }

}

