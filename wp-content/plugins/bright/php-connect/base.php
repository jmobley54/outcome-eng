<?php

/**
 * Bright Namespace
 */
namespace Bright;
use strict;

require_once(dirname(__FILE__) . '/singleton.php');
require_once(dirname(__FILE__) . '/javascript-generator.php');

/**
 * Syntactic sugar around the endless conditional assignments from issets to arrays.
 */
function extractFromArray (array $args,$field,$default=null) {return isset($args[$field]) ? $args[$field] : $default; }

/**
 *
 */
function testEcho($arg) {
  fwrite(STDERR,var_dump($arg));
}

/**
 * This is the base object for interacting w/ Bright from PHP.
 *
 * It should never be instantiated itself, but rather through creating a singleton of an inheriting class, like:
 *
 * $bright = \Bright\[WebStackClass]::getInstance(); // Wordpress, Drupal, etc.
 *
 * Once instantiated, for most useful functions it is required to acquire an API token.
 *
 * Note the leading \ before the Bright Namespace.   While not always necessarily, this will always take you to
 * the root namespace and should generally be included.
 *
 * $bright->setCurrentUser(...); // replace ... with a the current user object; however you derive it.<br/>
 * $bright->getAuthenticationCodeForUser(); // ... initializes the api and/or fetches an accessToken
 */
class Base extends Singleton {
  /**
   * @var The Bright Access Token as return from the Bright API or from local cache.
   */
  public $accessToken;
  /**
   * @var HTTP Code returned from last curl() call
   */

  public $successfullyInitialized;
  public $initializationError;

  /**
   * @var When this token will expire
   */
  public $accessTokenExpiration;

  /**
   *
   * @var $isHeaderWritten is set to true once the header is generated to the page.   Some webstacks may generate
   *      the 'header' callback multiple times [like wordpress];
   */

  public $isHeaderWritten;
  public $curlHttpCode;
  public $curlError, $curlInfo;
  public $embedderTemplates;
  public $realmGuid, $realmSecretKey;
  public $scormCloudAppId, $scormCloudSecretKey;
  public $apiRoot;
  public $renderedCourseList;
  private $courseLists;
  /**
   * @var Bright is initialized to use this user for authenticated user.  But you can actually set it to
   * any valid user.  This can be useful when the host authentication system is broken.
   */
  private $currentUser;
  /**
   * @var Defaults to 1000 characters.  Logging messages than this are clipped.
   */
  public $maximumLogMsg;

  /**
   * @var boolean $runQuiet for non webserver mode, don't echo out messages.  If WP_DEBUG=true, logging is generated.
   */
  public $runQuiet;

  /**
   * @var boolean $echoLogging for some reason, phpunit running inconsistently echos error_log messages.
   *                           This forces an echo of the message.
   */
  public $echoLogging;

  /**
   * @var string footerJs this variable holds javascript to be dumped in the page footer for bright to function.
   */
  public $footerJs;

  public $CURLMISSINGERROR = '<div class="error"><p><strong>Bright requires php-curl. Please install.</strong></p></div>';

  /**
   *
   */
  protected function __construct()
  {
	global $bright_embedder_templates; /* deprecate */
	if (empty($bright_embedder_templates))
	  $bright_embedder_templates = array();
	parent::__construct();
	$this->embedderTemplates = array(); /* the good one */
	$this->getOptions();
	$this->renderedCourseList = false; /* set it true once it's done */
	$this->courseLists = array();
	$this->maximumLogMsg = 1000;

    /* do initialization */
    $this->successfullyInitialized = true;
    if (! function_exists('curl_init')) {
      $this->successfullyInitialized = false;
      $this->initializationError = "PHP Curl is not found.  Please check your operating system documentation for installing curl for PHP and try again!";
    }
  }

  public function getOptions() {
	$this->apiRoot = $this->getOption('bright_api_url');
	$this->realmGuid = $this->getOption('bright_realm_guid');
	$this->realmSecretKey = $this->getOption('bright_secret_key');
	$this->scormCloudAppId = $this->getOption('bright_scorm_cloud_app_id');
	$this->scormCloudSecretKey = $this->getOption('bright_scorm_cloud_secret_key');
  }

  /**
   * Resets the bright internal data and fetches a new authentication code.
   */
  public function reset() {
	$this->resetAuthenticationToken();
	$this->getOptions();
	return $this->authenticateCurrentUser();
  }

  /**
   * Add in a template to the local dictionary.
   */
  public function addTemplate($name,$text) {
	$this->embedderTemplates[$name] = $text;
    return true;
  }

  /**
   * sets the current user.  Note when running from phpunit; calling this more than once can clear the
   * current user altogether
   *
   * @param number $uid the user ID of the user to make the current user
   * @return WP_USER a WP_USER object of the current user.
   */
  public function setCurrentUser($user) {
	$this->currentUser = $user;
	return $user;
  }

  public function setAndAuthenticateCurrentUser($user) {
	if (! $this->isValidUser($user))
	  throw new \Exception('argument 1 ($user) to setAndAuthenticateCurrentUser must be set and valid, which it is not');

	$this->setCurrentUser($user);
	return $this->getAuthenticationCodeForUser($user);
  }

  /**
   * Takes the current user object and authenticates it.
   */

  public function authenticateCurrentUser() {
	$r=$this->getAuthenticationCodeForUser();
	return $r;
  }

  /**
   *
   */
  public function getCurrentUser() {
	return $this->currentUser;
  }

  public function doFooter() {
    if (!$this->isReady() || $this->stop())
      return false;
	return $this->writeToPage($this->footerJs);
  }


  /**
   * Return a curl error , or a comment that we didn't see an error.  Don't use this function to check for errors!
   */
  public function curlErrorMsg() {
	if (!empty($this->curlError))
	  return $this->curlError;
	return 'An empty response was returned from bright server.  Typically this means your Bright settings are incorrect; please check and try again.';
  }

  /**
   * @param string $code the code to embed in script tags
   */
  static function returnAsJavascript($code) {
	return "  <script type='text/javascript'>\n		" . $code . "\n    </script>";
  }

  /**
   * @return boolean returns true if we are running in a webserver.  False means command line.
   */
  public function hasWebserver() {
	return isset($_SERVER['HTTP_USER_AGENT']);
  }

  /**
   * Returns a formatted line from a backtrace.
   * @param $c Array() an array returned as an element of the array returned from debug_trace();
   * @return String
   */
  public function formatBacktraceLine($c,$function=true,$file=true) {
	$caller='';
	$fwrapstart="";
	$fwrapend="";
	if ($function) {
	  $fwrapstart="(";
	  $fwrapend=")";

	  $caller = isset($c['class']) ? $c['class'] . '::' . $c['function'] : $c['function'];
	}
	if ($file)
	  if (isset($c['file'])) {
		$caller .= "{$fwrapstart}{$c['file']}";
		$caller .= isset($c['line']) ? ":{$c['line']}" : '';
		$caller .= $fwrapend;
	  }
	return $caller;
  }

  /**
   * Manages 'echo' statements.  Originally we ran in a webserver and errors were written to the page.  Now, for command line mode,
   * it's nice to be able shut these statement off if necessary [like in the test chassis].
   *
   * Thus, if the class variable $runQuiet is set, we won't generate echo statements.
   */
  public function writeToPage($msg) { /* echo is a reserver word it seems */
	if ($this->hasWebserver() || ! $this->runQuiet) {
	  echo $msg;
	  return true;
	}
	return false;
  }

  /**
   * @param array $args - key 'params' becomes the query parameters
   * @return string the full URL with query parameters.
   */
  public function getBrightServerUrl($base,array $args = array()) {
	$ret= "{$this->apiRoot}/${base}.json";
	$is_post = (isset($args['method']) && $args['method']==="POST");
	$query_params = $is_post ? array() : (isset($args['params']) ? $args['params'] : array());

	return $ret . '?' . http_build_query($query_params);
  }

  /**
   * Call the PHP base curl function, based on arguments below.  As a side effect the following are set:
   *   * $this->curlInfo
   *   * $this->curlHttpCode
   *   * $this->curlError
   *
   * @param string $url Full url to get/post
   * @param array $args * 'postData' indicates a POST, with the assigned value being an array.
   *                    * 'errorMsgs' allows a set of error message to be passed in as an array, where the key is the HTTP response
   *                       code, and values are the messages to be generated.
   *                       Message generation is handled by the Bright->writeToPage() function.
   *                       To suppress errorMsgs, just pass in a empty array for errorMsgs.
   *                    * 'params' for the post or get data [get data will be in the URL].
   *                    * 'suppressErrorBlock' - if any value, we will not generate the generic Bright error block on failure.
   *
   * @return string The returned data.  Use the $curlError and $curlInfo public class variables to get additional data about the curl
   *                call.
   */
  public function curl($url, array $args = array()) {
    $doLogging = $this->isBooleanOptionSet(array('option' => 'log_curl'));
    if (!function_exists('curl_version')) {
	  $this->writeToPage($this->CURLMISSINGERROR);
      return;
    }
	$is_post = (isset($args['method']) && $args['method']==="POST");
	if ($is_post)
	  $postData = extractFromArray($args,'params',array()); /* for simplicity, we use 'params' everywhere */

	$errorMsgs = extractFromArray($args,'errorMsgs', array('401' => "Failed to authenticate with Bright server; code 401.",
														   '404' => "A resource was attempted to be fetched from Bright Server, but not found; code 404."));

    $suppressErrorBlock = extractFromArray($args, 'suppressErrorBlock');

	$this->curlHttpCode = $this->curlInfo = $this->curlError = null;
	$ch = curl_init();
	$options = array(CURLOPT_URL => $url,
					 /* CURLOPT_SSLVERSION => 3, */
					 CURLOPT_RETURNTRANSFER => true,
					 CURLOPT_FOLLOWLOCATION => true);
	if (!empty($postData)) {
	  $options[CURLOPT_POSTFIELDS] = $postData;
      if ($doLogging)
        $this->log($postData,"postData");
	}

	curl_setopt_array($ch,$options);

	$response = curl_exec($ch);
    if ($doLogging)
      $this->log($response,"response to {$url}");

	$this->curlInfo = curl_getinfo($ch);
	$this->curlHttpCode = $this->curlInfo['http_code'];
    if ($doLogging)
      $this->log($this->curlInfo, "curl info for {$url}");
	$this->curlError = curl_error($ch);

    if (empty($suppressErrorBlock)) {
      if (!empty($this->curlError))
        $this->writeToPage($this->errorBlock($this->curlError));

      $httpCode = $this->curlInfo['http_code'];
      if (isset($errorMsgs[$httpCode]))
        $this->writeToPage($this->errorBlock($errorMsgs[$httpCode]));
    }

	curl_close($ch);
	return $response;
  }

  /**
   *
   */
  public function errorBlock($msg) {
	$support_text = $this->getSupportEmail();
	if ($this->hasWebserver()) {
	  $startText = "<div style=\"border-style: solid; border-width: 2px; border-color: red;\" class=\"bright_cannot_connect\">";
	  $endText = "</div>";
	  $newline = "<br/>";
	  $emphStart = "<strong>";
	  $emphEnd = "</strong>";
	  $resolution = "<a href=\".\">refresh the current page</a> to try again or";
	} else {
	  $startText = '';
	  $newline = "\n";
	  $emphStart = "";
	  $emphEnd = "";
	  $endText = "";
	  $resolution = '';
	}

	$text = "{$startText}Bright: An error occurred connecting to the bright server: \"{$msg}\".{$newline}Please {$resolution}contact us at $support_text with the following information (below) if the problem persists.{$newline}";
	$text .= "{$emphStart}User Agent: {$emphEnd}" . $this->getUserAgent() . "{$newline}";
	$text .= "{$emphStart}User Host: {$emphEnd}" . $_SERVER["HTTP_HOST"] . "{$newline}";
	$text .= "{$emphStart}Request URI: {$emphEnd}" . $_SERVER["REQUEST_URI"] . "{$newline}";
	$text .= "{$emphStart}User: {$emphEnd}" . $this->getUserEmail($this->getCurrentUser()) . "{$newline}";
	$text .= "{$emphStart}Bright URL: {$emphEnd}" . $this->apiRoot . "{$newline}";
	$text .= "{$emphStart}Error: {$emphEnd}" . $msg . "{$newline}";
	$text .= "</div>";

	return $text;
  }

  /**
   *
   */
  public function getUserAgent() {
	return array_key_exists("HTTP_USER_AGENT",$_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : 'N/A';
  }

  /**
   * Returns true if Bright has enough information to run.  If not, return false.   By enough we mean, enough config data and
   * a user object.
   *
   * @return boolean
   */

  public function isReady() {
    if (!$this->successfullyInitialized) {
      $this->log("Bright could not be initialized: " . $this->initializionError);
      return false;
    }

    $user = $this->getCurrentUser();
    if (empty($this->apiRoot) ||
        empty($this->realmSecretKey) ||
        empty($this->realmGuid) ||
        empty($user)) {
      $this->log('isReady finds data missing');
      return false;
    }
    if (!$this->isValidUser($user)) {
      $this->log('isReady finds not a valid user');
      return false;
    }
    return true;
  }

  /* get a magic Realm key for godlike usage */
  public function getRealmKey() {
    $query_data = array('realm_guid' => $this->realmGuid,
    'realm_secret_key' => $this->realmSecretKey);

    $json = $this->curl($this->getBrightServerUrl("api_key"),
						array('method' => 'POST',
							  'params' => $query_data));
	if (!empty($json)) {
	  $json_data = json_decode($json);
      return $json_data->access_token;
    }
	return $this->accessToken;
  }
    

  /**
   * Makes a call to Bright API for the current user, fetches an API key, and stores it in the Bright\Base object.
   * We also set the accessTokenExpiration Bright instance variable.
   *
   */
  public function getAuthenticationCodeForUser($user=null) {
    if (! $this->successfullyInitialized)
      return false;

	if (empty($user))
	  $user = $this->getCurrentUser();

	if (empty($user)) {
	  $this->errorBlock('no user passed to getAuthenticationCodeForUser(), nor set via setCurrentUser()');
	  return null;
	}

	if (!empty($this->accessToken))
	  return $this->accessToken;

	if (!$this->isReady())
	  return false;

	$uid = $this->getUserId($user);

	if ($this->setAccessTokenFromValidCachedKey($user)) /* sets expiration as well via side-effect */
      return $this->accessToken;

	if (!$this->isValidUser($user))
	  return false;

	$courseProviderId = $this->getUserOption('bright_course_provider_id', $uid);

	$query_data = array('realm_guid' => $this->realmGuid,
						'realm_secret_key' => $this->realmSecretKey,
						'user_email' => $this->getEmail($user));

	if ($courseProviderId)
	  $query_data['course_provider_id'] = $courseProviderId;
	else
	  if (!empty($this->scormCloudAppId) && !empty($this->scormCloudSecretKey)) {
		$query_data['sc_app_id'] = $this->scormCloudAppId;
		$query_data['sc_secret_key'] = $this->scormCloudSecretKey;
	  }

	$this->accessToken = null;
	$json = $this->curl($this->getBrightServerUrl("api_key"),
						array('method' => 'POST',
							  'params' => $query_data));
	if (!empty($json)) {
	  $json_data = json_decode($json);
	  if (!empty($json_data)) {
		$this->log($json,"json data");
		$this->accessToken = $json_data->access_token;

        $this->accessTokenExpiration = $json_data->expires_at;
		$this->setUserOption($uid, 'bright_cached_api_key', $this->accessToken);
		$this->setUserOption($uid, 'bright_cached_api_key_expiration', $this->accessTokenExpiration);
		$this->setUserOption($uid, 'bright_cached_api_key_url', $this->apiRoot);
	  }
	}
	return $this->accessToken;
  }

  /**
   * Just fetches a one-time access token for an email
   */

  public function getAuthenticationCodeForEmail($email) {
	return $this->callApi('api_key',array('method' => 'POST',
										  'params' => array('realm_guid' => $this->realmGuid,
															'realm_secret_key' => $this->realmSecretKey,
															'user_email' => $email),
										  'success' => function ($rsp) {
											return $rsp->access_token;
										  }));
  }

  /**
   * Derive the div CSS Class for a generated template, based on the
   * @return string the CSS class for a generated Bright Div.
   */
  public function getTemplateClass($template) {
	return "bright-template-" . empty($template) ? 'inline' : $template;
  }

  /**
   *
   */
  public function getTemplateFromApi($template,array $args = array()) {
	return $this->callApi('template', array('params' => array('name' => $template),
											'success' => function ($rsp) use ($args) {
											  if (isset($args['raw']))
												return $rsp;
											  if (sizeof($rsp) > 0)
												return $rsp[0]->body;
											  return null;
											}));
  }

  /**
   * Calls the Bright Api and returns the result.
   *
   * Defined keys for the $args array() include:<br/>
   * * accessMode: one of 'accessToken' or 'realm'.  If accessToken, uses the apiKey derived when the
   *   Bright object is initialized.  If 'realm', the accessToken is ignored and access is made just with
   *   the Bright realm guid and realm secret key.
   * * params, these are used to assemble the query string and/or POST data.  Anything placed here is passed
   *   through directly to the API call.<br/>
   * * raw, which as a boolen when set disables parsing of returned JSON.<br/>
   * * method.  Set to "POST" for post method.
   * * errorMsgs.  You can define an array of error messages based on HTTP returl codes.  The defaults are:
   *   array('401' => "Failed to authenticate with Bright server; code 401.",
   *         '404' => "A resource was attempted to be fetched from Bright Server, but not found; code 404.");
   * * encode.  A list of params that should be json_encooded.
   *
   * @param string $apiController this is the app interface to hit (course|registration|api_key|...)
   * @param array $args An array of arguments.  See description for the arguments.
   *
   * @return mixed an array parsed json; unless raw is set, then the raw response data.
   */
  public function callApi($apiController,array $args = array()) {
	$accessMode = extractFromArray($args,'accessMode','accessToken');
	$encode = extractFromArray($args,'encode');
	$args['params'] = extractFromArray($args,'params',array());

	if (!empty($encode) && is_array($encode))
	  foreach ($encode as $parameter_to_encode)
        if (isset($args['params'][$parameter_to_encode]))
          $args['params'][$parameter_to_encode] = json_encode($args['params'][$parameter_to_encode]);

	if ($accessMode === "accessToken") {
	  if (empty($this->accessToken)) {
        $this->log($args,"no accessToken available in callApi for {$apiController}, args ");
		return null;
      }
	  $args['params'] = array_merge($args['params'], array('accessToken' => $this->accessToken));
	} else if ($accessMode === "realm")
	  $args['params'] = array_merge($args['params'], array('realm_guid' => $this->realmGuid, 'realm_secret_key' => $this->realmSecretKey));

	$authUrl = $this->getBrightServerUrl($apiController,$args);

	$rsp = $this->curl($authUrl,$args);

	if (!empty($rsp) && strlen($rsp) > 1) { /* not sure where this single byte string comes from.... but we treat it as an invalid resp. */
      /* I've learned a little bit more; it exists even from rspec to the Rails tests...... So we will continue to expect it for now. */
	  $rsp = isset($args['raw']) ? $rsp : json_decode($rsp); /* in some cases you don't want parsed json; so set raw = true */
	  /* $this->log($rsp,"response from call to {$authUrl}"); */
	  return array_key_exists('success',$args) ? $args['success']($rsp,$this->curlInfo,$this->curlError) : $rsp;
	}
	return array_key_exists('failure',$args) ? $args['failure']($rsp,$this->curlInfo,$this->curlError) : null;
  }


  /**
   * @param String $courseGuid the unique course id [or even SCORMCloud course id] of the course.  If this is ommitted, the complete
   * course list is returned.
   *
   * @return Array the complete parsed json response
   */
  public function getCourseData(array $args = array()) {
	return $this->callApi('course',$args);
  }

  /*
   * @param String $key a unique key that allows for this courselist to be cached and fetched later by key
   * @param array $args the arguments to be passed to the callApi() function for the course API.
   * @return mixed the result of the api call.  If 'raw', then a string; otherwise a parsed JSON array.  See the callApi() function for more details.
   */
  public function getCourseList($key, array $args = array()) {
	if (array_key_exists($key,$this->courseLists))
	  return $this->courseLists[$key];
	$this->courseLists[$key] = $this->getCourseData($args);
	return $this->courseLists[$key];
  }

  /**
   * @param string $courseGuid the course ID aka SCORMCloud course ID for the registration to be fetched.  Note, on the most recent non-deleted registration is returned per the 'last_only' attribute as passed to bright server via the Bright API.
   * @param array $args a set of args to pass to Bright::Base->callApi().  See the documentation of that function for more details.
   * @return mixed Null if not found, or a stdObject with the registration data.
   */
  public function getRegistrationDataForCourse($courseGuid, array $args = array()) {
	/* http://trac.aura-software.com/aura/ticket/922 */
	/* 'refresh_if_launched' => 1, */
	$success = extractFromArray($args,'success');
	$failure = extractFromArray($args,'failure');
    $params = extractFromArray($args,'params',array());
    $params = array_merge($params, array('last_only' => 1,'course_guid' => $courseGuid));
	return $this->callApi('registration',
						  array_merge($args,
									  array('errorMsgs' => array('401' => "Failed to authenticate with Bright server; code 401."),
											'params' => $params,
											'success' => function($rsp,$curlInfo,$curlError) use ($args,$success) {
											  if ($success)
												return $success($rsp,$curlInfo,$curlError);
											  return isset($args['raw']) ? $rsp : $rsp[0];
											},
											'failure' => function($rsp,$curlInfo,$curlError) use ($args,$failure) {
											  if ($failure)
												return $failure($rsp,$curlInfo,$curlError);
											  if ($curlInfo['http_code'] == "404")
												return '{}';
											  return null;
											})
									  )
						  );
  }

  /*
   * @param Array $args an array of arguments to send to callApi()
   * @param sting $name the parameter name
   * @param string $value the parameter value
   * @return Array the annotated args array()
   */
  public function setParam(array $args=array(),$name,$value) {
	$args['params'] = isset($args['params']) ? $args['params'] : array();
	$args['params'][$name] = $value;
	return $args;
  }

  /**
   * @param string $courseGuid the course ID aka SCORMCloud course ID for the course to be fetched.
   * @param array $args a set of args to pass to Bright::Base->callApi().  See the documentation of that function for more details.
   * @return mixed Null if not found, or a stdObject with the course data.
   */
  public function getCourseDataByGuid($courseGuid, array $args = array()) {
	$args = $this->setParam($args,'course_guid',$courseGuid);
	$success = isset($args['success']) ? $args['success'] : NULL;
	return $this->getCourseData(array_merge($args,
											array('success' => function($rsp,$curlInfo,$curlError) use ($success,$args) {
												$rsp = isset($args['raw']) ? $rsp : (sizeof($rsp) >0 ? $rsp[0] : null);
												if ($success)
												  $rsp = $success($rsp,$curlInfo,$curlError); /* chain success handlers ... ? */
												return $rsp;
											  })));
  }


  /**
   *
   */
  public function getReportageUrls() {
	return $this->callApi('util/reportage_urls');
  }

  /**
   * @return String the support text for this method [an HTML block if running in a webserver].
   */
  public function getSupportEmail() {
	$supportEmail = 'support@aura-software.com';
	$text = $this->hasWebserver() ? "<a href=\"mailto:{$supportEmail}\">Bright Support</a>" : $supportEmail;
	return $this->extensionPoint('filter',"bright_support_email", $text);
  }


  public function resetCachedKey() {
	$this->deleteCurrentUserOption('bright_cached_api_key');
	$this->deleteCurrentUserOption('bright_cached_api_key_expiration');
	$this->deleteCurrentUserOption('bright_cached_api_key_url');
  }

  /**
   * Checks in the webstack DB and looks for a valid cached API key that hasn't expired, and was generated from the same Bright URL
   * we are currently talking to.
   *
   * Also lift out the expiration data and provide that in a Bright instance variable.
   */
  public function setAccessTokenFromValidCachedKey($user) {
	$uid = $this->getUserId($user);

	/* $this->log($uid,'uid'); */

	$cached_api_key = $this->getUserOption('bright_cached_api_key', $uid);
	$cached_api_expiration = $this->getUserOption('bright_cached_api_key_expiration',$uid);
	$cached_api_url = $this->getUserOption('bright_cached_api_key_url',$uid);

    if (empty($cached_api_expiration) || empty($cached_api_url) || empty($cached_api_key)) /* not enough information */
      return null;

    if ($this->apiRoot != $cached_api_url) {
      $this->log("cached apiRoot of {$this->apiRoot} doesn't match {$cached_api_url}, so cached key is rejected");
      return null;
    }

    date_default_timezone_set("UTC");

    preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2}).([0-9]{3})Z/", $cached_api_expiration, $output_array);

    $exp_date = \DateTime::createFromFormat('Y-m-d H:i:s', "$output_array[1]-$output_array[2]-$output_array[3] $output_array[4]:$output_array[5]:$output_array[6]")->getTimestamp();
    $cur_date = time();
    if ($cur_date > $exp_date)
      return null;
    $this->accessTokenExpiration = $cached_api_expiration;
    $this->accessToken = $cached_api_key;
	return true;
  }

  /**
   * Wraps the error_log function.  If $echoLogging is set on the Bright object, the message will also be echoed.
   */

  public function errorLog($msg) {
	error_log($msg);
  }

  /**
   * Initiates a call to the Bright API, to update user attributes from this site for the user.
   *
   * @param mixed $user A user object in the native format for the Webstack.
   * @return string Returns the data passed back from the bright API in raw format [as a string].
   */
  public function updateBrightUserMeta($user) {
	if (empty($user))
	  return false;
	$email = $this->getUserEmail($user);
	$this->log('updating user meta for '. $email);
	/* we fetch it raw; because json for the API needs special encoding */
	return $this->updateRealmUserCustom($email,$this->getUserAttributes($user,array('raw' => true)));
  }

  /**
   *
   */
  public static  function encodeJsonPrimitive(&$item, $key) {
	if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
  }

  /**
   *
   */
  public static function encodeJson(array $arr = array()){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, 'Bright\Base::encodeJsonPrimitive');
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');

  }

  /**
   *
   */
  public function updateRealmUserCustom($email,$userAttributes,array $args = array()) {
	$siteUrl = $this->getSiteUrl();
	/* this is called from bright.js too; so it was implemented as a JSONP custom; hence it's not a post */
	$this->log($userAttributes,'user attributes');
	return $this->callApi('realm_user/gcustom',array('accessMode' => 'realm', /* see https://github.com/bretweinraub/bright-api-doc/blob/master/v2_api_controller.md#sec-4 */
													 'params' => array($this->getSiteUrl() => self::encodeJson($userAttributes),
																	   'email' => $email,
																	   'key' => 'hostdata',
																	   'php_json_encoded' => "1")));
  }


  /**
   * Returns all the local templates available before filtering.  Currently keys in the global $bright_embedder_templates will override the keys in
   * the Bright class variable $embedderTemplates.
   *
   * @return Array all the local templates available before filtering.
   */

  public function getLocalTemplates() {
	/* TODO: remove this global */
	global $bright_embedder_templates;

	return array_merge($bright_embedder_templates,$this->embedderTemplates);
  }


  /**
   * Returns the template text.  Searches in the following order:
   *
   * 1. the Bright class $embedderTemplates variable.
   * 2. the deprecated $bright_embedder_templates variable [this will be remove in Bright 9.0].
   * 3. the API is queried to see if the template is stored there.
   *
   * @param string $templateName the name of the template to fetch
   * @return string the template text
   */
  public function getTemplateText(array $args = array()) {
    $templateName = extractFromArray($args,'template');

	$templates = $this->getLocalTemplates();

	$bright_embedder_templates = $this->extensionPoint('filter','bright_templates',$templates,$args);

	if (!empty($this->embedderTemplates[$templateName]))
	  return $this->embedderTemplates[$templateName];

	if(!empty($bright_embedder_templates[$templateName]))
	  return $bright_embedder_templates[$templateName];

	$text = $this->getTemplateFromApi($templateName);
	if (empty($text))
	  $this->writeToPage('<div class="error">No embedder template named ' . $templateName . ' found.  Please check the spelling and/or validate that the container plugin is enabled on this site.</div>');
	return $text;
  }

  /**
   * Resets the authentication token.
   */
  public function resetAuthenticationToken() {
	$this->log('resetting authentication token in resetAuthenticationToken()');
	$this->accessToken = null;
	$this->resetCachedKey();
	return true;
  }


  /**
   *
   */
  public function getCourseProviders(array $args = array()) {
	return $this->callApi('course_provider',$args);
  }

  /**
   *
   */
  public function createRegistration(array $args = array()) {
	$params = extractFromArray($args,'params',array());

    $this->log($params, "params to Bright::createRegistration()");

	if (!isset($params['learner_id']) || !isset($params['course_guid']))
	  throw New \Exception('learner_id and course_guid not set in params array to createRegistration()');
	$args['method'] = 'POST';
	return $this->callApi('registration',$args);
  }
  /**
   *
   */
  public function isTesting() {
	return $this->isBooleanOptionSet(array('option' => 'jstest'));
  }


  public function writePageHeader() {
	if ($this->isHeaderWritten)
	  return false;

	$user_attributes = $this->getUserAttributes($this->getCurrentUser());
	$as = "addslashes";

	$ret =<<<EOF
<script>
var bright_user_attributes = "{$as($user_attributes)}";
</script>
EOF;

	$this->doErrorception();

	$first_name = $this->getCurrentUserFirstName();
	$last_name =  $this->getCurrentUserLastName();
	$email = $this->getEmail($this->getCurrentUser());

	$ret .=  "<meta name='bright-token' content='{$this->accessToken}'/>\n";
	$ret .=  "<meta name='bright-token-expiration' content='{$this->accessTokenExpiration}'/>\n";
	$ret .=  "<meta name='bright-api-url' content='{$this->apiRoot}'/>\n";
	$ret .=  "<meta name='bright-first-name' content='{$first_name}'/>\n";
	$ret .=  "<meta name='bright-last-name' content='{$last_name}'/>\n";
	$ret .=  "<meta name='bright-email' content='{$email}'/>\n";

	if ($this->isTesting())
      $this->footerJs .=   '<div id="qunit" style="padding: 50px;"></div>';
	$this->log('writing page header');
	$this->writeToPage($ret);
	$this->isHeaderWritten = true;
	return $ret;
  }

  /**
   * Dumps out the necessary JS to integrate this site with errorception.
   */
  public function doErrorception() {
	$currentUser = $this->getCurrentUser();
	$first_name = $this->getCurrentUserFirstName();
	$last_name =  $this->getCurrentUserLastName();
	$email = $this->getEmail($currentUser);

	$this->footerJs .= <<<EOF
<script>
if (typeof (_errs) === "undefined")
  _errs = {};

_errs.meta = {
  api_key: '{$this->accessToken}',
  email: '{$email}',
  api_url: '{$this->apiRoot}'
};
</script>
EOF;

  }

  /**
   *
   */

  public function doHeader($user=null) {
	if ($this->isHeaderWritten) {
	  $this->log('header already written in doHeader()');
	  return false;
	}
	if (method_exists($this,'stop') && $this->stop()) {
	  $this->log('stop set in doHeader()');
	  return;
	}

	if (empty($user))
	  $user = $this->getCurrentUser();
	else
	  $this->setCurrentUser($user);

	if (!$this->isReady()) {
	  $this->log('bright not ready in doHeader()');
	  return false;
	}
	$this->getAuthenticationCodeForUser($user);
	if (empty($this->accessToken)) {
	  $this->log('no access token found in Bright\Base->doHeader()');
	  return false;
	}

	$this->setCurrentCourseProvider();
    return $this->writePageHeader();
  }

  /**
   * @return string URL of a bright logo image
   */
  static public function getBrightLogoImage() {
	return "https://www.aura-software.com/wp-content/uploads/2012/04/just-bulb-01.png";
  }

  /**
   * returns the invitation object if an invitation exists with the requested parameters, otherwise returns NULL
   */
  public function invitationExists(array $args = array()) {
    $matches = $this->callApi('invitation', $args);
    if (sizeof($matches) === 1) {
      $exists = $matches[0];
      if ($exists && property_exists($exists,'name'))
        return $exists;
    }
    return null;
  }


  /**
   *
   * @tests test_addLearnerToInvitation
   */
  public function addLearnerToInvitation($learnerId,$invitationName,array $args = array()) {
	$params = extractFromArray($args,'params',array());
	$params['name'] = $invitationName;
	$params['learners'] = Base::encodeJson(array($learnerId));

    $args['params'] = $params;
    $args['accessMode'] = 'realm';

	return $this->callApi('invitation/add_learners',$args);
  }

  /**
   *
   */
  public function createInvitation(array $args = array()) {
	$args['method'] = 'POST';
    $args['encode'] = array('course_guids',
                            'license_data',
                            'custom');
	return $this->callApi('invitation', $args);
  }

  public function isUserRegisteredToCourse($courseGuid,array $args = array()) {
    $data = $this->getRegistrationDataForCourse($courseGuid);
    if ($data) {
      if (property_exists($data,'registration_guids'))
        if ($data->registration_guids && sizeof($data->registration_guids) > 0)
          return $data->registration_guids;
      if (property_exists($data,'registration_guid'))
        return $data->registration_guid;
    }
    return false;
  }
}
