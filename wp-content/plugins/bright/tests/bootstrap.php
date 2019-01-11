<?php

ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) 
  $_tests_dir = '/tmp/wordpress-tests-lib';

echo "setting tests to dir {$_tests_dir} from WP_TESTS_DIR env var";

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
  require dirname( dirname( __FILE__ ) ) . '/bright.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

class BrightApiTestBase extends WP_UnitTestCase {
  public $bright;

  function dumpToStderr($arg) {
	fwrite(STDERR,var_dump($arg));
  }

  protected function checkCode($code,$msg) {
	$this->assertEquals($this->bright->curlHttpCode,$code, $msg);
  }

  public function __construct() {
	update_option('bright_api_url','http://localhost:3000/bright/api/v2' );
	update_option('bright_realm_guid','sJLtP8Zt8G0Sbz9kxPjQ');
	update_option('bright_secret_key','PcVQflTCUIbe3ps2T86KXAzvXzdpFcgs5Mvku03uZ8w');
	update_option('bright_scorm_cloud_app_id','YW6BSUQCWC');
	update_option('bright_scorm_cloud_secret_key','7TtOtD9J6R5JjoCY8fzQwlQV6S0yMVPlyYJtslWr');

	parent::__construct();

	$this->bright = Bright\Wordpress::getInstance();
    if (empty($this->bright->accessToken)) {

      $user = $this->bright->createUser('aurasupport','changeme!','support@aura-software.com');
      wp_update_user(array('ID' => $user->ID,
    					   'first_name' => 'Aura',
    					   'last_name' => 'Support'));
      $user_id = $this->bright->setCurrentUser($user);
      if (is_wp_error($user_id))
    	throw new Exception('failed to update first or last name for user');
      $this->bright->getAuthenticationCodeForUser($user);
      $this->bright->log($user,false,'in test setup user');
      $this->bright->log($this->bright->accessToken,false,'in test setup access token');
      $this->bright->runQuiet = true;
      $doecho = !empty(getenv('ECHO'));
      $this->bright->echoLogging = false || $doecho;
      $this->bright->maximumLogMsg = 65900;
    }

  }
}

class BrightUnauthenticatedTestBase extends WP_UnitTestCase {
  public $bright;

  function dumpToStderr($arg) {
	fwrite(STDERR,var_dump($arg));
  }

  protected function checkCode($code,$msg) {
	$this->assertEquals($this->bright->curlHttpCode,$code, $msg);
  }

  public function __construct() {
	update_option('bright_api_url','http://localhost:3000/bright/api/v2' );
	update_option('bright_realm_guid','sJLtP8Zt8G0Sbz9kxPjQ');
	update_option('bright_secret_key','PcVQflTCUIbe3ps2T86KXAzvXzdpFcgs5Mvku03uZ8w');
	update_option('bright_scorm_cloud_app_id','YW6BSUQCWC');
	update_option('bright_scorm_cloud_secret_key','7TtOtD9J6R5JjoCY8fzQwlQV6S0yMVPlyYJtslWr');

	parent::__construct();

	$this->bright = Bright\Wordpress::getInstance();
  }
}

