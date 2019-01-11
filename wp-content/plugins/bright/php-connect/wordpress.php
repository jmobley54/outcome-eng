<?php

/**
 * Wordpress Bright Connector
 */

namespace Bright;

/**
 * Wordpress Bright Connector
 */
class Wordpress extends Base {
  /**
   *
   */
  function __construct() {
	parent::__construct();

	// Disable textureize filter
	// This mangles quotations into unicode likenesses which cause unwanted
	// behavior in Bright templates, like not working at all.
	remove_filter('the_content', 'wptexturize');

	/* filter for expanding bright invitation embed codes in the post content */
	/* add_filter('the_content','bright_content_filter'); */

	/* TODO: this probably doesn't belong here.  For sites that don't want shortcodes in widgets; there'd be no way to remove it.... */
	/* see also https://wordpress.org/support/topic/shortcode-in-widgets-not-working */
	$this->setCurrentCourseProvider();

	$this->loadScripts();
  }

  static function setupMenus() {
	$bright = Wordpress::getInstance();
	$bright->setCurrentUser(Wordpress::getCurrentUserFromWebstack());

	$bright_menu_slug = 'bright_options';
	/* TODO: write these functions */
	add_menu_page( 'Bright Overview', 'Bright' , 'manage_options', $bright_menu_slug, 'Bright\Wordpress::overviewMenu');
	add_submenu_page( $bright_menu_slug, 'Bright Settings' , 'Settings', 'manage_options', $bright_menu_slug . "_settings" , 'Bright\Wordpress::settingsMenu');
	add_submenu_page( $bright_menu_slug, 'Bright Admin' , 'Admin', 'manage_options', $bright_menu_slug . "_admin" , 'Bright\Wordpress::menuAdmin');
	add_submenu_page( $bright_menu_slug, 'Sync User Data' , 'UserMeta Sync', 'manage_options', $bright_menu_slug . "_sync" , 'Bright\Wordpress::syncMenu');
  }


  /**
   * static function to update the realm_user metadata for a user, typically initiated by a profile modification callback.
   */

  public static function updateRealmUserMeta($user_id, $old_user_data=null) {
	$bright = Wordpress::getInstance();
    $bright->log($user_id, 'user_id passed to updateRealmUserMeta');
	$bright->updateBrightUserMeta(get_user_by('ID',$user_id));
  }

  /*
   * this function gets the current user from the webstack.
   */
  public static function getCurrentUserFromWebstack() {
    $currentUser = wp_get_current_user();
    return apply_filters('bright_get_wp_current_user',$currentUser);
  }

  public static function isUserLoggedIn() {
    $logged_in = is_user_logged_in();
    return apply_filters('bright_is_user_logged_in',$logged_in);
  }

  /**
   *
   */
  public function checkCompatibility() {
	global $bright_usermeta_export;

	if (isset($bright_usermeta_export))
	  return('<div class="error">The support of $bright_usermeta_export was removed in Bright 6.0+.  Please remove this from your site and use the UserMeta Sync bright setting for this.</div>');

	global $bright_embedder_templates;

	if (isset($bright_embedder_templates))
	  return('<div class="error">The support of $bright_embedder_templates is deprecated in Bright 8.0+.  Please remove this from your site.</div>');
  }

  public static function getScriptUrl($script) {
	return plugins_url($script,dirname(__FILE__));
  }


  /**
   * Returns a textual display name for the user form the WP webstack.
   */

  public function getDisplayName($user) {
	if (empty($user))
	  $user = $this->getCurrentUser();
	$userdata = get_userdata($user->ID);
	if (!empty($userdata))
	  return $userdata->display_name;
  }

  /**
   * The callback for the wp_enqueue_scripts action.
   */
  public function loadScriptsAction() {
    wp_register_style('bright_css', $this->getScriptUrl('bright.css'), false, '1.0.0');
    wp_enqueue_style('bright_css');
    wp_enqueue_script('jquery-i18n',
                      Wordpress::getScriptUrl('jquery.i18n.min.js'),
                      array('jquery'));
    wp_enqueue_script('handlebars',
                      /* Wordpress::getScriptUrl('handlebars-v3.0.1.js'), */
                      Wordpress::getScriptUrl('handlebars-v4.0.4.js'),
                      array('jquery'));
    wp_enqueue_script('bright-lang',
                      Wordpress::getScriptUrl('bright.lang.js'),
                      array('bright'));
    wp_enqueue_script('underscore5',
                      Wordpress::getScriptUrl('underscore-min-1.5.1.js'),
                      array('jquery'));
    $bright_js = plugin_dir_path( dirname(__FILE__)) . "/bright.js";

    wp_enqueue_script('bright',
                      Wordpress::getScriptUrl(file_exists($bright_js) ? 'bright.js' : 'bright.min.js'),
                      array('jquery-i18n','handlebars','underscore5'));

    if ($this->isTesting()) {
      wp_enqueue_script('qunit',
                        /* Wordpress::getScriptUrl('qunit-1.19.0.js'), */
                        '//code.jquery.com/qunit/qunit-1.19.0.js',
                        array('jquery'));

      wp_enqueue_script('bright-testing',
                        Wordpress::getScriptUrl('qunit-tests.js'),
                        array('bright'));

      wp_register_style('qunit-1.19.0.css',
                        '/code.jquery.com/qunit/qunit-1.19.0.css');
    }
  }

  public function loadScripts() {
    add_action('wp_enqueue_scripts', array($this,'loadScriptsAction'));
  }


  /**
   * Returns true if this WP post has the 'bright-stop' custom field set.
   * This can be useful if you 'don't want bright to load on a particular post;
   */
  public function stop() {
	if (in_the_loop()) {
	  $post_id = get_the_ID();
	  if(!empty($post_id)) {
		$stop=get_post_meta($post_id,'bright-stop',true);
        if (!empty($stop))
          return true;
	  }

      /* settings have 'initialize_bright_on_request' set to true.   If so, post must have
         postmeta field of 'bright-start' to true, or we will stop right here.

         https://code.aura-software.com/aura/wp-bright-plugin/issues/4
      */

      if ($this->initializeBrightOnRequestOnly()) {
		$start=get_post_meta($post_id,'bright-start',true);
        if (empty($start))
          return true;
      }
	}
	return false;
  }

  /**
   * returns true if 'initialize_bright_on_request' option is set.
   *
   * see https://code.aura-software.com/aura/wp-bright-plugin/issues/4
   */
  public function initializeBrightOnRequestOnly() {
    if ($this->isBooleanOptionSet(array('option' => 'initialize_bright_on_request')))
      return true;
    return false;
  }

  /**
   *
   */
  public function getLoginUrl() {
	return $this->extensionPoint('filter','bright_login_url','/wp-login.php');
  }

  /**
   *
   */
  public function getTaxonomy($categories) {
	$cats = array();
	if ($categories)
	  foreach($categories as $cat)
		$cats[$cat->slug] = $cat;
	return $cats;
  }

  /**
   * This function handles extension points for the encapsulating PHP web stack, in this case Wordpress.  It is possible that
   * a particular webstack doesn't implement [or can't] a type of extension point.
   *
   * In any event, this allows us to write generalized extension-aware code without being concerned with the implementation in any particular
   * webstack
   *
   * @params Array $args A variable lenght argument list is passed.
   *   1. Argument one is the extension type (filter|hook|...)
   *   2. The rest of the arguments are passed to the extension point function.
   * @return mixed the return data from the extension point is returned.
   */
  public function extensionPoint() {
	$args = func_get_args();
	$type = array_shift($args);
	if ($type === 'filter')
	  return call_user_func_array('apply_filters',$args);
	else if ($type === 'action')
	  return call_user_func_array('do_action', $args);
	throw new \Exception("exceptionPoint of {$type} not implemented");
  }


  /* Filter: bright_templates */
  /* Function: Allows a plugin developer to modify the global $bright_embedder_templates on the fly */


  /**
   * Returns and array of data about the user, like roles, avatar, etc.
   *
   * @param WP_User $user a Wordpress User object
   * @param array $args An array of arguments.  Set raw => true to suppress json encoding of results.
   *
   * @return mixed if raw is true,  an array structure representing the user attributes for the $user argument.  This is typically passed to a rendered
   * Bright template for the purposes of enabling formulations like {{user.roles.administrator}}, for example.
   * if Raw not set, json_encode() of the returned array (which means a string).
   *
   */
  public function getUserAttributes($user=null,array $args = array()) {
    if (! $user)
      $user = $this->getCurrentUser();
    if (! $user) /* it seems that there is no current user .... */
      return null;
	$bright_usermeta_export_raw = get_option("bright_usermeta_export");
	// Set this to an empty array, just in case the option is empty. -TL
	$user_attributes = array();

	// preg_split will produce a one element array in those cases, so
	// let's make sure there is actually content. -TL

	$usermeta_export = (strlen($bright_usermeta_export_raw) > 0) ?
	  preg_split('/,/',$bright_usermeta_export_raw) :
	  array();

	if (sizeof($usermeta_export) > 0) {
	  $usermeta = array();
	  foreach ($usermeta_export as $meta_key) {
		// Interesting quirk to get_user_meta. If $meta_key was a zero-length
		// string in the following statement, it would return ALL of the
		// metadata! This is not desirable to us here. -TL
		if (strlen($meta_key) > 0) {
		  $result = get_user_meta($user->ID, $meta_key,true);
		  $usermeta[$meta_key] = $result;
		}
	  }
	  $user_attributes['meta'] = $usermeta;
	}

	$user_attributes['display_name'] = $this->getDisplayName($user);
	$user_attributes['site_roles'] = array();
	$user_attributes['email'] = $user->user_email;

	if ($user->{'roles'})
	  foreach($user->{'roles'} as $role)
		$user_attributes['site_roles'][$role] = true;

	$user_attributes['avatar'] = $this->getAvatarUrl(get_avatar($user->ID));

	// If BuddyPress is installed and group functionality is on,
	// we should include the user's BuddyPress groups.
	if (function_exists('bp_has_groups') && bp_has_groups()) {
	  $groups = groups_get_groups(array('user_id' => $user->id,));
	  $user_attributes['groups'] = array_map(function ($group) {
		  return array('id' => $group->id,
					   'slug' => $group->slug,
					   'name' => $group->name);
		}, $groups['groups']);
	}
	return extractFromArray($args,'raw',false) ? $user_attributes : json_encode($user_attributes);
  }

  /**
   *
   */
  public function getAvatarUrl($avatar) {
	preg_match("/src='(.*?)'/i", $avatar, $matches);
	if (count($matches) > 0)
	  return $matches[1];
	return null;
  }

  /**
   * @param a WP_Post object
   * @return URL to a thumbnail or featured image for the page.
   */
  public function getThumbnail($post) {
	$thumb = get_post_thumbnail_id($post->ID);
	$src = wp_get_attachment_image_src($thumb);
	return $src[0];
  }

  /**
   * @param WP_Post $post a wordpress page or post
   * @return a JSON document representing metadata for this page; to be passed to the bright template at render time.
   */
  public function getPageAttributes($post) {
	$pageAttributes = array();

	$pageAttributes['featured_image'] = $this->getThumbnail($post);
	$pageAttributes['id'] = $post->ID;

	$pageAttributes['categories'] = $this->getTaxonomy(get_the_category());
	$pageAttributes['tags'] = $this->getTaxonomy(get_the_tags());

	return json_encode($pageAttributes);
  }


  /**
   * reconstructs a bright embedding shortcode as text from the $attr array() passed from the do_shortcode WP filter
   * @param array $attr
   * @return string
   */
  public function buildShortcode($attr) {
	/* don't render this template */
	/* the basic use of this is in writing documentation about the bright embedder */
	/* usering ignore="true" means we can actually get the embed code out to the web page */
	/* since we don't get any formatting, bold='' lets us geta little bit of styling inthere.
	   pretty rickety though */

	$bold = $attr['bold'];
	$ret = '[bright ';
	foreach ($attr as $k => $v)
	  if (! ($k == "ignore" || $k == "bold"))
		$ret .= ($k == $bold) ? "<strong>{$k}=\"{$v}\"</strong> " : "{$k}=\"{$v}\" ";

	return $ret . "][/bright]";
  }

  /**
   *
   */
  public function renderCourseList($courseList) {
	/* TODO: while we can cache multiple courselists; bright.js cannot process them correctly and only one can be rendered */

	if (!$this->renderedCourseList)
	  $this->footerJs .= JavascriptGenerator::newCourselist($courseList);
	$this->renderedCourseList = true;
	return true;
  }

  /**
   * Converts a [bright] shortcode into two things:
   *   1. a piece of converted content, most likely a <div> tag to be populated by the Bright.js templating library.
   *   2. Javascript that is cached, and then echo-ed into the page footer.
   *
   * For more about the functionality of this method, see http://help.aura-software.com/bright-shortcode-reference
   *
   * @param array $attr
   * @param string $content
   * @return the content string.
   */
  public function expandShortcode($attr,$content) {
	global $post;
	if ($this->stop() || empty($post) || ! $post->ID || $post->ID === 0)
	  return null;

	if (isset($attr['ignore']))
	  return $this->buildShortcode($attr);

    // recursively expand shortcodes.
    $content = do_shortcode($content);

	/* TODO: bright extension points */
	$this->extensionPoint('action','bright_before_rewrite_embed_code');
	$this->extensionPoint('action','before_bright_rewrite_embed_code'); /* deprecated TODO: delete it from CCGcloud */

	if (empty($this->accessToken)) 
	  if ($this->curlError) 
        return $this->extensionPoint('filter','bright_curl_error',$this->errorBlock($this->curlError));
      else 
        if (! $this->successfullyInitialized) 
          return $this->extensionPoint('filter', 'bright_not_initialized',
                                       "<div class=\"bright_not_initialized\">{$this->initializationError}</div>");
        else
          return $this->extensionPoint('filter', 'bright_please_login',
                                       '<div class="bright_not_logged_in">Please <a href="' . $this->getLoginUrl() . '?redirect_to=' .  urlencode(get_permalink()) . '">login or register</a> to view this content.</div>');

	/* TODO: add this to the template reference.  In fact, I doubt it really works. */
	// id allows us defined a unique ID for this launch box, as opposed to using the default.
	$courseGuid = extractFromArray($attr,'course');
	$id = extractFromArray($attr,'id', md5(strval(rand())));

	$locale = extractFromArray($attr,'locale');
	$type = extractFromArray($attr,'type','course');
	$embedClass = extractFromArray($attr,'class', "bright-{$type}");
	$template = extractFromArray($attr,'template');
	$content = (!empty($template)) ? $this->getTemplateText($attr) : $content;
	$apiTemplate = extractFromArray($attr,'api_template','public_minimum');

	$this->footerJs .= JavascriptGenerator::initializeBrightCourses();

	if ($type === "course") {
	  if (empty($courseGuid)) {
		$courseGuid = extractFromArray($_GET, 'bright_course_id');
		$courseGuid = $this->extensionPoint('filter','bright_course_id',
											$courseGuid, /* pass in the one from template shortcode if set */
											array('attr' => $attr));
		if (empty($courseGuid))
		  return '<div class="bright-error">Bright: No course ID set in your [bright] shortcode or via URL or via filter.</div>';
	  }

	  $rawCourseData = $this->getCourseDataByGuid($courseGuid,array('raw' => true, 'params' => array('api_template' => $apiTemplate)));

	  if (empty($rawCourseData) or strlen($rawCourseData) < 3)
		return '<div class="bright-error">Bright: No course with ID of ' . $courseGuid . ' was found.</div>';

	  $this->footerJs .= JavascriptGenerator::addBrightCourse($courseGuid,$rawCourseData);


	  $rawRegistrationData = $this->getRegistrationDataForCourse($courseGuid,array('raw' => true,
																				   'params' => array('api_template' => $apiTemplate)));

	  if (!empty($rawRegistrationData))
		$this->footerJs .= JavascriptGenerator::addRegistrationDataForCourse($courseGuid,$rawRegistrationData);

	  $rawCourseData = $this->extensionPoint('filter','bright_extend_on_course',$rawCourseData,$courseGuid,$rawRegistrationData);
	  $jsonData = json_decode($rawCourseData);
	  $customData = $jsonData[0]->{'custom'};
	} else if ($type === "courselist") {
	  $courseList = $this->getCourseList("raw-{$apiTemplate}",array('raw' => true,
																	'params' => array('api_template' => $apiTemplate)));
	  $this->renderCourseList($courseList);
	  /* $customData = $this->extensionPoint('filter','bright_extend_on_courselist',$courseList,$attr); */
	  $customData = $this->extensionPoint('filter','bright_extend_on_courselist',null,$attr);
	} else if ($type==="generic")
	  $customData = $this->extensionPoint('filter','bright_extend_on_generic',null,$attr);
    else
	  return '<div class="bright-error">Bright: No embedder of type ' . $type . ' found.</div>';

	$this->footerJs .= JavascriptGenerator::addTemplate($id,$type, array('courseId' => $courseGuid,
																		 'embedLocale' => $locale,
																		 'embedClass' => $embedClass,
																		 'embedType' => $type,
																		 'embedAttributes' => json_encode($attr),
																		 'pageAttributes' => $this->getPageAttributes($post),
																		 'userAttributes' => $this->getUserAttributes($this->getCurrentUser()),
																		 'customData' => $customData,
                                                                         'templateName' => $template,
																		 'template' => $content));
	return JavascriptGenerator::newBrightDiv(array('typeString' => $type,
                                                   'containerId' => $id,
                                                   'templateClass' => $this->getTemplateClass($template),
                                                   'embedClass' => $embedClass,
                                                   'templateName' => $template));
  }

  /**
   *
   */
  public static function overviewMenu() {
	$bright = Wordpress::getInstance();
	$bright->setCurrentUser(Wordpress::getCurrentUserFromWebstack());
	$msg = '<h2>Bright!</h2>';
    if (!function_exists('curl_version')) {
	  $msg .= $bright->CURLMISSINGERROR;
    }
    else {
	  $links = $bright->getReportageUrls();
	  $msg .= '<ul>';
	  foreach ($links as $label => $url)
	    $msg .= '<li><a href="'.$url.'" target="_blank">'.$label.'</a></li>';

	  $msg .= '</ul>';
    }
	$bright->writeToPage($msg);
	return true;
  }



  /**
   * Generates the user sync meta data for the Wordpress Admin dashboard
   */
  public static function syncMenu() {
	$bright = Wordpress::getInstance();
	$bright->setCurrentUser(Wordpress::getCurrentUserFromWebstack());

	if (! $bright->extensionPoint('filter', 'bright_can_manage_bright', 'syncMenu',current_user_can('manage_options')))
	  wp_die($bright->i18n('You do not have sufficient permissions to access this page.',false));

	$ret = <<<EOF
  <form name="sync" method="post" action="">

    <br/>
    <table>
      <tr>
        <td><label title="For large datasets, this can assist in working around timeout errors." for="SkipToday">Skip Records Already Updated Via Full Sync Today:</label></td>
		<td><input type="checkbox" name="SkipToday"></td>
		</tr>
      <tr>
        <td><label title="For large datasets, this can assist in working around timeout errors." for="MaxRecords">Max records to process:</label></td>
		<td><input type="number" name="MaxRecords"></td>
		</tr>

			   <tr>
   <td>
    <br/>
	<input type="submit" name="Submit" class="button-primary" value="Sync User Metadata">
	</td>
	</tr>
	</table>
  </form>
  <br/>
EOF;
   if ($bright->hasWebserver()) {
     echo $ret;
     if (! empty($_POST)) {
       $args = array();
       $args['skip-today'] = isset($_POST['SkipToday']) ? $_POST['SkipToday'] : null;
	   $args['max-records'] = $_POST['MaxRecords'];
	   return $bright->updateAllUsers($args);
     } else
       return true;
   } else
     return $ret;
  }

  /**
   *
   */
  public function updateAllUsers(array $args = array()) {
	global $wpdb;
	$skiptoday = extractFromArray($args,'skip-today',false);
	$max_records = extractFromArray($args,'max-records');

    $blog_id = $GLOBALS['blog_id'];
    $users = get_users(array('blog_id' => $blog_id));

	$today = date("d-m-Y");
	$recno = 0;
	$success = 0;
	foreach ($users as $user) {
	  $current = get_user_meta($user->ID, 'bright-last-fullsync',true);
	  if ($skiptoday && $current === $today) {
		$this->writeToPage("Skipped Bright User MetaData for {$user->user_email} {$this->curlError}<br/>");
		next;
	  } else {
		$this->writeToPage("Updated Bright User MetaData for {$user->user_email} {$this->curlError}<br/>");
		$this->updateBrightUserMeta($this->getUserByEmail($user->user_email));
		$http_code = $this->curlInfo['http_code'];
		/* fwrite(STDERR,"{$http_code}\n"); */
		$success += $http_code === 200 ? 1 : 0;
		if(empty($this->curlError))
		  add_user_meta($user->ID, 'bright-last-fullsync',$today,true) || update_user_meta($user->ID, 'bright-last-fullsync',$today);
		$recno++;
		if (!empty($max_records) && $recno >= $max_records)
		  break;
	  }
	}
	return array('records' => $recno,
				 'success' => $success);
  }

  /**
   *
   */
  public function getOption($opt) {
	return get_option($opt);
  }

  /**
   *
   */
  public function getEmail($user) {
	return $user->user_email;
  }

  /**
   * Returns an option for the current web site/web stack
   */
  public function getSiteOption($option) {
	return get_option($option);
  }

  /**
   * Returns true if the currentUser is something sane and valid.
   */
  public function isValidUser($user) {
	if (empty($user))
	  return false;
	if (get_class($user) != 'WP_User')
	  return false;
	return !(empty($user) || $this->getUserId($user) == 0);
  }

  /**
   * Returns a user object for WP based on email
   */
  public function getUserByEmail($email) {
	return get_user_by('email',$email);
  }

  /**
   *
   */
  public function createUser($username,$password,$email) {
	$user_id= wp_create_user($username,$password,$email);

	if (is_wp_error($user_id))
	  $this->log($user_id->get_error_message(),'error message in Wordpress->createUser()');

	/* ultimately we don't care really */
	return $this->getUserByEmail($email);
  }

  /**
   *
   */
  public function setUserOption($uid,$key,$value) {
	$this->log("setting {$key} to {$value} for UID {$uid}");
	update_user_option($uid,$key,$value);
  }

  /**
   *
   */

  public function getCurrentUserId() {
	$user = $this->getCurrentUser();
	if (!empty($user))
	  return $this->getUserId($user);
	return null;
  }

  /**
   *
   */
  public function getUserId(\WP_User $user) {
	if (empty($user))
	  return null;
	return $user->ID;
  }

  /**
   *
   */
  public function getUserEmail($user) {
	if (empty($user))
	  return null;
	return $user->user_email;
  }

  /**
   *
   */
  public function getUserOption($option,$id) {
	return get_user_option($option,$id);
  }

  public function getRequestCode(){
    return sprintf("%08x", abs(crc32(extractFromArray($_SERVER,'REMOTE_ADDR') . extractFromArray($_SERVER,'REQUEST_TIME') . extractFromArray($_SERVER,'REMOTE_PORT'))));
  }

  /**
   *
   */
  public function log($message,$extra_text=null) {
	$uniqueid = $this->getRequestCode();

	if (!(is_array($message) || is_object($message))) {
	  if (strlen($message) > $this->maximumLogMsg)
		$message = substr($message, 0, $this->maximumLogMsg) . " ... (clipped) ";
	  if (!$this->hasWebserver())
		$message .= "\n";
	}

	$trace = debug_backtrace();

	$caller = "{$uniqueid}: ";
	/* $caller .= $this->formatBacktraceLine($trace[5],false,true) . "\n"; */
	/* $caller .= $this->formatBacktraceLine($trace[4],false,true) . "\n"; */
	/* $caller .= $this->formatBacktraceLine($trace[3],false,true) . "\n"; */
	/* $caller .= $this->formatBacktraceLine($trace[2],false,true) . "\n"; */
	/* $caller .= $this->formatBacktraceLine($trace[1],false,true) . "\n"; */
	/* $caller .= $this->formatBacktraceLine($trace[0],false,true) . "\n"; */
	/* $caller .= $this->formatBacktraceLine($trace[1],true,false) . ": "; */

	if (!empty($extra_text))
	  $caller .= $extra_text . " ";

	if ((defined('BRIGHT_DEBUG') &&
    BRIGHT_DEBUG &&
    strstr ($_SERVER['REQUEST_URI'],'brightdebug') &&
    defined('BRIGHT_DEBUG_KEY') &&
    strstr ($_SERVER['REQUEST_URI'],BRIGHT_DEBUG_KEY)
    )
    )
	  if (is_array($message) || is_object($message)) {
		$msg = array(
          'message' => $caller,
          'object' => $message
        );
		echo var_dump($msg);

	  } else
        if ($this->hasWebserver())
		  echo "<pre class=\"xdebug-var-dump\">{$caller} is/are {$message}</pre>";

    if ($this->isBooleanOptionSet(array('option' => 'logging')))
      if (WP_DEBUG === true) {
        if (!$this->hasWebserver())
          $this->errorLog("################################################################################");
        if (is_array($message) || is_object($message))
          $this->errorLog("{$caller}is/are " .print_r($message, true));
        else
          $this->errorLog("{$caller}is/are " . $message);
      }
	if ($this->echoLogging && !$this->hasWebserver())
      testEcho($message);
  }

  /**
   *
   */
  public function getSiteUrl($without_protocol=true) {
	$url = get_site_url();
	if ($without_protocol) {
	  $find = array('http://', 'https://');
	  $replace = '';
	  $url = str_replace($find, $replace, $url);
	}
	return $url;
  }

  /**
   *
   */
  public function setCurrentCourseProvider() {
	$user = $this->getCurrentUser();
	if (!$this->isValidUser($user))
	  return false;

    if (isset($_GET["clear_course_provider_id"]))
	  $this->deleteCurrentUserOption('bright_course_provider_id');
	else
	  if (isset($_GET["course_provider_id"])) {
		$provider_id = (int) $_GET["course_provider_id"];
		update_user_option($user->ID, 'bright_course_provider_id', $provider_id);
	  }
	return true;
  }

  /**
   *
   */
  public function isBooleanOptionSet(array $args = array()) {
    $option = extractFromArray($args,'option');
	$bright_options = extractFromArray($args,'option_string', get_option('bright_options'));
	parse_str($bright_options,$parsedOptions);
	$result = null;
	if (array_key_exists($option,$parsedOptions)) {
	  $variable = $parsedOptions[$option];
	  try {
		if (!empty($variable))
		  eval("\$result = $variable;");
	  } catch (Exception $e) {
	  }
	}
	return $result;
  }
  /**
   * returns the first name of the user from the user's profile.
   */
  public function getCurrentUserFirstName() {
	$currentUser = $this->getCurrentUser();
	if (!empty($currentUser))
	  return get_user_meta($currentUser->ID, "first_name",true);
	return null;
  }

  /**
   * returns the first name of the user from the user's profile.
   */
  public function getCurrentUserLastName() {
	$currentUser = $this->getCurrentUser();
	if (!empty($currentUser))
	  return get_user_meta($currentUser->ID, "last_name",true);
	return null;
  }

  /**
   *
   */
  public function getCurrentUserEmail() {
	$currentUser = $this->getCurrentUser();
	if (!empty($currentUser))
	  return $this->getUserEmail($currentUser);
	return null;
  }

  /**
   * Sets up styles and JS for admin pages.  You should make sure all page header directives required for admin pages run here.
   */
  public function setupAdminPage() {
	if (!$this->isReady())
	  return false;

	global $wp_scripts;
	$ui = $wp_scripts->query('jquery-ui-core');
	wp_enqueue_script('jquery-ui-dialog');
	if (!wp_style_is('jquery-ui'))
	  wp_enqueue_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/'.$ui->ver.'/themes/cupertino/jquery-ui.css');

	// Only load the GWT stuff on the admin page.
	if (get_current_screen()->id == 'bright_page_bright_options_admin') {
	  wp_register_style('bright_settings_css', $this->getScriptUrl('BrightSettings.css'), false, '1.0.0');
	  wp_enqueue_style('bright_settings_css');
	  wp_enqueue_script('bright_settings_js', $this->getScriptUrl('/brightsettings/brightsettings.nocache.js'));
	}
	return true;
  }

  public function getCurrentCourseProvider() {
	return get_user_option('bright_course_provider_id');
  }

  public function deleteCurrentUserOption($option) {
	delete_user_option($this->getCurrentUserId(), $option);
  }

  public function doAdminFooter() {
	if (!$this->isReady())
	  return false;

	$localTemplates = $this->getLocalTemplates();
	// Don't run if user doesn't have the rights
	if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
	  return;

	// Don't run if not a editor page
	if(!(strstr($_SERVER['REQUEST_URI'], 'wp-admin/post-new.php') ||
		 strstr($_SERVER['REQUEST_URI'], 'wp-admin/post.php') ||
		 strstr($_SERVER['REQUEST_URI'], 'wp-admin/edit.php')))
	  return;

	$json_templates = json_encode($localTemplates);
	$template_names = array_keys($localTemplates);
	sort($template_names);
?>
<div id="bright-embed-dialog">
  <div>
    <select class="course">
      <option value="">Select a course</option>
      <option value="__COURSE_LIST__">All Courses</option>
      <option value="__GENERIC__">No Courses</option>
      <option value="">-------------</option>
    </select>
  </div>
  <div>
    <select class="template">
      <option value="">Select a template</option>
      <optgroup label="Local templates">
        <?php
          foreach($template_names as $name) {
            echo "<option>".htmlspecialchars($name)."</option>";
          }
        ?>
      </optgroup>
    </select>
    <input class="confirm insert" type="button" value="Embed Template"/>
    <input class="confirm insert-all" type="button" value="Insert Template Contents"/>
    <input class="cancel" type="button" value="Cancel"/>
  </div>
</div>

<script type="text/javascript" charset="utf-8">
// <![CDATA[
  jQuery.fn.insertAtCaret = function(text) {
      return this.each(function() {
          if (document.selection && this.tagName == 'TEXTAREA') {
              //IE textarea support
              this.focus();
              sel = document.selection.createRange();
              sel.text = text;
              this.focus();
          } else if (this.selectionStart || this.selectionStart == '0') {
              //MOZILLA/NETSCAPE support
              startPos = this.selectionStart;
              endPos = this.selectionEnd;
              scrollTop = this.scrollTop;
              this.value = this.value.substring(0, startPos) + text + this.value.substring(endPos, this.value.length);
              this.focus();
              this.selectionStart = startPos + text.length;
              this.selectionEnd = startPos + text.length;
              this.scrollTop = scrollTop;
          } else {
              // IE input[type=text] and other browsers
              this.value += text;
              this.focus();
              this.value = this.value;    // forces cursor to end
          }
      });
  };

  (function(){
    var url = jQuery('meta[name=bright-api-url]').attr('content');
    var token = jQuery('meta[name=bright-token]').attr('content');
    var BrightTemplates = <?php echo $json_templates; ?>;

    jQuery('#bright-embed-dialog').dialog({
      autoOpen: false,
      minWidth: 800
    });
    jQuery('#bright-embed-dialog select').change(function () {
      var courseId = jQuery('#bright-embed-dialog select.course').val();
      var templateName = jQuery('#bright-embed-dialog select.template').val();
      if (courseId && templateName) {
        jQuery('#bright-embed-dialog .confirm').button('option', 'disabled', false);
      }
      else {
        jQuery('#bright-embed-dialog .confirm').button('option', 'disabled', true);
      }
    });
    jQuery('#bright-embed-dialog input').button();
    jQuery('#bright-embed-dialog .confirm').button('option', 'disabled', true);

    BrightAdmin = {
      "getTypeDirective": function (courseId) {
        var typeText = 'type="';
        if (courseId == "__COURSE_LIST__")
          typeText += 'courselist'
        else if(courseId == "__GENERIC__")
          typeText += 'generic'
        else
          return null
        typeText += '"';
        return typeText
      }
    };

    jQuery('#bright-embed-dialog .insert').click(function () {
      var courseId = jQuery('#bright-embed-dialog select.course').val();
      var templateName = jQuery('#bright-embed-dialog select.template').val();

      var typeDirective = BrightAdmin.getTypeDirective(courseId);
      var useCourseId = typeDirective ? false : true;

      var insertText = '[bright template="' + templateName + '"';
      insertText += (useCourseId ? ' course="' + courseId + '"' : '');

      insertText += (typeDirective ? " " + typeDirective : '')  + '/]';

      jQuery('#content').insertAtCaret(insertText);
      jQuery('#bright-embed-dialog').dialog('close');
    });
    jQuery('#bright-embed-dialog .insert-all').click(function () {
      var courseId = jQuery('#bright-embed-dialog select.course').val();
      var templateName = jQuery('#bright-embed-dialog select.template').val();
      var typeDirective = BrightAdmin.getTypeDirective(courseId);
      var useCourseId = typeDirective ? false : true;

      var insertText = '[bright';
      insertText += (useCourseId ? ' course="' + courseId + '"': '"');

      insertText += (typeDirective ? " " + typeDirective : '')  + ']';
      insertText += "\n" +
        BrightTemplates[templateName] +
        "\n[/bright]";

      jQuery('#content').insertAtCaret(insertText);
      jQuery('#bright-embed-dialog').dialog('close');
    });
    jQuery('#bright-embed-dialog .cancel').click(function () {
      jQuery('#bright-embed-dialog').dialog('close');
    });

    var controller = 'course';
    if (url.match(/v1/)) {
      controller = 'scorm_cloud_course';
    }

    jQuery.ajax({
      url:url + '/' + controller,
      data:{
        api_key:token,
        format:'json'
      },
      dataType:'jsonp',
      success:function (data) {
        for (var i = 0; i < data.length; i++) {
          jQuery('#bright-embed-dialog select.course').append(
            '<option value="' + data[i].course_guid + '">' +
                data[i].title + '</option>'
         );
        }
      }
    });

    jQuery.ajax({
      url:url + '/template',
      data:{
        api_key:token,
        format:'json'
      },
      dataType:'jsonp',
      success:function (data) {
        optgroupCode = '<optgroup label="Templates from Bright">';
        for (var i = 0; i < data.length; i++) {
          optgroupCode += '<option value="' + data[i].name + '">' +
              data[i].name + '</option>'
          BrightTemplates[data[i].name] = data[i].body;
        }
        optgroupCode += '</optgroup>';
        jQuery('#bright-embed-dialog select.template').append(
          optgroupCode);
      }
    });

    if (typeof QTags != 'undefined' && QTags.addButton) { // Newer Wordpress, like 3.5.1
      QTags.addButton('bright_embed', 'Bright Embed', function() {
        jQuery('#bright-embed-dialog').dialog({autoOpen: true});
      });
    }
    else { // Fallback to clumsy jQuery insert if older Wordpress
      jQuery("#ed_toolbar").append('<input type="button" class="ed_button" onclick="jQuery(\'#bright-embed-dialog\').dialog({autoOpen: true});return false;" title="Insert a Bright Embed" value="Bright Embed"/>');
    }
  }());
// ]]>
</script>
<?php
  }

  /**
   * Return an internationalized version of a message.
   * @param string $message The message to internationalize
   * @param boolean $echo Defaults to true; if set, message is written to page or echo-ed to terminal.
   */
  public function i18n($message,$echo=true) {
	$msg = __($message,'bright-plugin');
	if ($echo)
	  $this->writeToPage($msg);
	return $msg;
  }

  static public function menuAdmin() {
	$bright = Wordpress::getInstance();
	$bright->setCurrentUser(Wordpress::getCurrentUserFromWebstack());
	if (! $bright->extensionPoint('filter', 'bright_can_manage_bright', 'menuAdmin', current_user_can('manage_options')))
	  wp_die($bright->i18n('You do not have sufficient permissions to access this page.',false));

	$providers = $bright->getCourseProviders();
	$currentProviderId = $bright->getCurrentCourseProvider();

  echo 'Course Provider(s): ';
  for ($i = 0; $i < count($providers); ++$i) {
    $provider = $providers[$i];
    if ($provider->id == $currentProviderId)
      echo htmlspecialchars($provider->name);
    else {
      echo '<a href="/wp-admin/admin.php?page=bright_options_admin&course_provider_id='.
        htmlspecialchars($provider->id).'">'.
        htmlspecialchars($provider->name).'</a>';
    }
    if ($i < count($providers) - 1)
	  echo " | ";
  }
if (!empty($currentProviderId)) {
  echo " | ";
  echo '<a href="/wp-admin/admin.php?page=bright_options_admin&clear_course_provider_id=true">Clear Selected Course Provider</a>';
}


  ?>
    <iframe src="javascript:''" id="__gwt_historyFrame" tabIndex='-1' style="position:absolute;width:0;height:0;border:0"></iframe>
    <noscript>
      <div style="width: 22em; position: absolute; left: 50%; margin-left: -11em; color: red; background-color: white; border: 1px solid red; padding: 4px; font-family: sans-serif">
        Your web browser must have JavaScript enabled
        in order for this application to display correctly.
      </div>
    </noscript>
    <div id="bright-settings"></div>
<?php
  }

  static public function settingsMenu() {
	$bright = Wordpress::getInstance();
	$bright->setCurrentUser(Wordpress::getCurrentUserFromWebstack());

	if (! $bright->extensionPoint('filter', 'bright_can_manage_bright', 'settingsMenu',current_user_can('manage_options')))
	  wp_die($bright->i18n('You do not have sufficient permissions to access this page.',false));

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'

	$fields = array(array('name' => 'api_url',
						  'type' => 'text',
						  'title' => 'Bright API URL',
						  'length' => 50,
						  'description' => "This is the API url supplied to you by Bright support."),
					array('type' => 'hr'),
					array('name' => 'scorm_cloud_app_id',
						  'type' => 'text',
						  'title' => 'SCORM Cloud Application ID',
						  'length' => 16,
						  'description' => ''),
					array('name' => 'scorm_cloud_secret_key',
						  'type' => 'text',
						  'title' => 'SCORM Cloud Secret Key',
						  'length' => 50,
						  'description' => ''),
					array('type' => 'hr'),
					array('name' => 'realm_guid',
						  'type' => 'text',
						  'title' => 'Bright Realm GUID',
						  'length' => 30,
						  'description' => 'Note; this is completely different than a SCORMCloud realm.'),
					array('name' => 'secret_key',
						  'type' => 'text',
						  'title' => 'Bright Realm Secret Key',
						  'length' => 50,
						  'description' => ''),
					array('type' => 'separator'),
					array('name' => 'usermeta_export',
						  'type' => 'text',
						  'title' => 'UserMeta Sync',
						  'length' => 80,
						  'description' => "This is a list of 'usermeta' keys that will by synced to bright's realm user table"),
					array('name' => 'options',
						  'type' => 'text',
						  'title' => 'Options',
						  'length' => 80,
						  'description' => "A '&'-separated [like a query string] list of options in tag=value pairs, denoting special modes of operation.  For example, jstest=true will deliver special testchassis javascript site to the web page.")
					);


    $hiddenFieldName = 'bright_submit_hidden';
	if(isset($_POST[$hiddenFieldName]) && $_POST[$hiddenFieldName] == 'Y') {
	  $bright->log('hidden field is set');
	  foreach ($fields as $field) {
		if ($field['type'] == 'text') {
		  $optionName = 'bright_' . $field['name'];
		  $bright->log("update option {$optionName} to {$_POST[$optionName]}");
		  update_option($optionName, $_POST[$optionName]);
		}
	  }
	  $bright->reset();
?>
<div class="updated"><p><strong>API KEY: <?php  echo $bright->accessToken; ?></strong></p></div>
<div class="<?php echo empty($bright->accessToken) ? 'error' : 'updated'; ?>"><p><strong><?php echo('Settings checked and <strong>' . (empty($bright->accessToken) ? 'failed' : 'succeeded') . '</strong>.') ?></strong></p></div>
<div class="updated"><p><strong><?php _e('settings saved.', 'bright-menu'); ?></strong></p></div>
<?php

	  if (empty($bright->accessToken)) { ?>
<div class="error"><p><?php echo $bright->curlErrorMsg(); ?></p></div>
<?php
	  }
	} ?>
<img src="<?php echo Base::getBrightLogoImage(); ?>"/>
<div class="wrap">
  <h2><?php $bright->i18n('Bright Settings'); ?></h2>
	<div class="info"><?php $bright->i18n('Mouse over the option header for more information.') ?></div>
<span><a href="http://help.aura-software.com/configuring-the-bright-plugin/" target="configuring-the-bright-plugin">Need Help?</a></span>
  <form name="bright-settings-form" method="post" action="">
	<div class="bright-compat">
	  <?php $bright->checkCompatibility(); ?>
	</div>
    <table>
<?php

	  foreach ($fields as $field) {
		/*
		  PHP-mode not so good with this .... but emacs helps:
		  (c-set-offset (quote brace-list-entry) 2 nil)
		  (c-set-offset (quote case-label) 2 nil)

		  C-c C-o
		*/
		$optionName = "bright_" . extractFromArray($field,'name');
		$optionValue = get_option($optionName);
		$description = extractFromArray($field,'description');
		$title = extractFromArray($field,'title');
		$type = extractFromArray($field,'type');
		$length = extractFromArray($field,'length',40);

		switch($type) {
		  case 'hr':
			$bright->writeToPage('<tr><td colspan="2"><hr/></td></tr>');
			break;
		  case 'text':
			$title = $bright->i18n($title,false);
?>
<div>
  <tr title="<?php echo $description; ?>">
    <td><?php $bright->i18n($title); ?></td>
    <td>
      <input type="<?php echo $type; ?>" name="<?php echo $optionName; ?>" value="<?php echo $optionValue; ?>" size="<?php echo $length; ?>">
    </td>
  </tr>
</div>
<?php
			break;
		}
	  }
?>
  <tr>
    <td>
      <p class="submit"><input type="submit" name="Test Settings" class="button-primary" value="<?php esc_attr_e('Test Settings') ?>"/></p>
    </td>
    <td>
      <p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>"/></p>
    </td>
    <td>
      <input type="hidden" name="<?php echo $hiddenFieldName; ?>" value="Y">
    </td>
  </tr>
</form>
</div>
<?php
  }
}


require_once(dirname(__FILE__) . '/wp-deprecation.php');
