<?php

class BWC_Helper {
  public static function orderComments($order_id) {
    $args = array(
      'post_id' => $order_id,
      'approve' => 'approve',
      'type' => ''
   );

    remove_filter('comments_clauses', array('WC_Comments', 'exclude_order_comments'));

    $comments = get_comments($args);

    add_filter('comments_clauses', array('WC_Comments', 'exclude_order_comments'));

    return (array) $comments;
  }
}

/**
 *
 * Bootstrapping for phpunit tests for BrightWoocommerceIntegration.
 *
 * Borrowed heavily from the WooCommerce
 */

require_once '/tmp/bright-woocommerce-testlibs/vendor/autoload.php';

class BrightWooTestBootstrap {
  /**
   * Get the single class instance.
   *
   * @since 1.8
   * @return BrightWooTestBootstrap
   */
  public static function instance() {
    if (is_null(self::$instance))
      self::$instance = new self();
    return self::$instance;
  }

  /** @var \BrightWooTestBootstrap instance */
  protected static $instance = null;

  /** @var string directory where wordpress-tests-lib is installed */
  public $wp_tests_dir;

  /** @var string testing directory */
  public $tests_dir;

  /** @var string WooCommerce testing directory */
  public $wc_tests_dir;

  /** @var string plugin directory */
  public $plugin_dir;

  public $require_number;

  /**
   * Setup the unit testing environment.
   *
   * @since 1.8
   */
  public function __construct() {
    ini_set('display_errors','on');
    ini_set('display_startup_errors', 'on');

    /* error_reporting(E_ALL); */
    $this->require_number = 0;

    $this->tests_dir    = dirname(__FILE__);
    $this->plugin_dir   = dirname($this->tests_dir);
    $this->wp_tests_dir = getenv('WP_TESTS_DIR') ? getenv('WP_TESTS_DIR') : '/tmp/wordpress-tests-lib';
    echo "setting tests to dir {$this->wp_tests_dir} from WP_TESTS_DIR env var";

    // load test function so tests_add_filter() is available
    require_once($this->wp_tests_dir . '/includes/functions.php');

    tests_add_filter('muplugins_loaded', array($this, 'muPluginCallback'));
    tests_add_filter( 'setup_theme', array( $this, 'installWooCommerce' ) );

    $bootstrap = $this->wp_tests_dir . '/includes/bootstrap.php';

    echo PHP_EOL . "%%%%%% loading {$bootstrap}       %%%%%%" . PHP_EOL;
    require_once($bootstrap);
    echo PHP_EOL . "%%%%%% finished loading {$bootstrap}       %%%%%%" . PHP_EOL;

    // load WC testing framework
    $this->includes();
  }

  /* lifted and adapted from the bootstrap.php file in woocommerce/tests */
  public function installWooCommerce() {
    global $wpdb;

    /* TODO: remove hardcoded DB name */
    $results = $wpdb->get_var("select count(distinct table_name) from INFORMATION_SCHEMA.tables where table_schema = 'bright_wp_testing' and table_name like 'wptests_woocommerce%'");

    if (!empty($results) && $results == "9") {
      echo "It seems WooCommerce is already installed.  Skipping setup." . PHP_EOL;
      return;
    }

    // clean existing install first
    define( 'WP_UNINSTALL_PLUGIN', true );
    include (ABSPATH . 'wp-content/plugins/woocommerce/uninstall.php');

    WC_Install::install();
    update_option( 'woocommerce_calc_shipping', 'yes' ); // Needed for tests cart and shipping methods

    // reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374
    $GLOBALS['wp_roles']->reinit();

    echo "Installing WooCommerce..." . PHP_EOL;
  }

  public function _require_once($file) {
    /* echo $this->require_number . ": %%%%%% loading {$file}. " . PHP_EOL; */
    require_once($file);
    /* echo $this->require_number . ": finished loading {$file}. %%%%%%" . PHP_EOL;  */
    $this->require_number++;
  }

  public function includes() {
    $this->wc_tests_dir = "/tmp/wordpress/wp-content/plugins/woocommerce/tests";

    // factories
    $this->_require_once( $this->wc_tests_dir . '/framework/factories/class-wc-unit-test-factory-for-webhook.php' );
    $this->_require_once( $this->wc_tests_dir . '/framework/factories/class-wc-unit-test-factory-for-webhook-delivery.php' );

    // framework
    $this->_require_once( $this->wc_tests_dir . '/framework/class-wc-unit-test-factory.php' );
    /* $this->_require_once( $this->wc_tests_dir . '/framework/class-wc-mock-session-handler.php' ); */

    // test cases
    $this->_require_once( $this->wc_tests_dir . '/framework/class-wc-unit-test-case.php' );
    $this->_require_once( $this->wc_tests_dir . '/framework/class-wc-api-unit-test-case.php' );

    // Helpers
    $this->_require_once( $this->wc_tests_dir . '/framework/helpers/class-wc-helper-product.php' );
    $this->_require_once( $this->wc_tests_dir . '/framework/helpers/class-wc-helper-coupon.php' );
    $this->_require_once( $this->wc_tests_dir . '/framework/helpers/class-wc-helper-fee.php' );
    $this->_require_once( $this->wc_tests_dir . '/framework/helpers/class-wc-helper-shipping.php' );
    $this->_require_once( $this->wc_tests_dir . '/framework/helpers/class-wc-helper-customer.php' );
    $this->_require_once( $this->wc_tests_dir . '/framework/helpers/class-wc-helper-order.php' );

  }

  public function muPluginCallback() {
    echo 'in muPluginCallback()' . PHP_EOL;
    $this->loadWooCommerce();
    $this->loadBright();
  }

  public function loadWooCommerce() {
    echo 'In loadWooCommerce()' . PHP_EOL;
    /* load the WooCommerce test chassis including factories */
    /* $this->loadWpUnitTestChassis(); */
    $this->_require_once(ABSPATH . 'wp-content/plugins/woocommerce/woocommerce.php');
  }

  public function loadWpUnitTestChassis() {
    $wp_test_chassis_locale = "/tmp/wordpress-develop";

    echo PHP_EOL . "Loading WP Test Chassis from {$wp_test_chassis_locale}". PHP_EOL;
    echo PHP_EOL . "If you have problems try: ". PHP_EOL;
    echo PHP_EOL . "(cd /tmp/ ; svn co https://develop.svn.wordpress.org/trunk/ wordpress-develop)" . PHP_EOL;

    require_once($wp_test_chassis_locale . "/tests/phpunit/includes/testcase.php");
  }

  public function loadBright() {
    echo ('loading Bright');
    $plugins_dir = "/tmp/wordpress/wp-content/plugins";
    $bright_plugin = "{$plugins_dir}/bright/bright.php";
    echo PHP_EOL . "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%" . PHP_EOL . PHP_EOL;
    echo "Bright: Loading bright plugin from hard-coded path of {$bright_plugin}" . PHP_EOL;
    echo "Bright: In this case, we installed bright by hand in {$plugins_dir}.   Is the right way?   PROBABLY NOT!!!!" . PHP_EOL;
    echo "Bright: So to test against a different version of Bright, change the install in {$plugins_dir}.   TODO: write a script an automate it." . PHP_EOL;

    $this->_require_once($bright_plugin);
    $this->_require_once($plugins_dir."/bright/tests/bright-test-factory.php");
    $this->_require_once($this->plugin_dir.'/bright-woocommerce-integration.php');

    echo PHP_EOL . "%%%%%%   END loadBright() %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%" . PHP_EOL . PHP_EOL;

  }
}

BrightWooTestBootstrap::instance();

class BWCTestCase extends WP_UnitTestCase {

  protected function setVerboseErrorHandler()  {
    $handler = function($errorNumber, $errorString, $errorFile, $errorLine) {
      echo  "
ERROR INFO
Message: $errorString
File: $errorFile
Line: $errorLine
";
    };
    set_error_handler($handler);
  }


  public $bright;
  public $currentUser; /* stash it; in case it goes missing */

  function dumpToStderr($arg) {
    fwrite(STDERR,var_dump($arg));
  }

  protected function checkCode($code,$msg) {
    $this->assertEquals($this->bright->curlHttpCode,$code, $msg);
  }

  protected function insureCurrentUser() {
    wp_set_current_user($this->bright->getCurrentUser()->ID);
    /* wp_set_current_user($this->currentUser->ID); /\* jeez what a hack *\/ */
    $new_user = wp_get_current_user();
    if (empty($new_user->ID)) {
      Bright\testEcho($this->bright->accessToken);
      $brightUser = $this->bright->getCurrentUser();
      Bright\testEcho($brightUser);
      Bright\testEcho($this->currentUser);
      Bright\testEcho($new_user);
      throw new Exception('failed to set current user in WP');
    }
  }

  public function __construct() {
    update_option('bright_api_url','http://localhost:3000/bright/api/v2');
    update_option('bright_realm_guid','sJLtP8Zt8G0Sbz9kxPjQ');
    update_option('bright_secret_key','PcVQflTCUIbe3ps2T86KXAzvXzdpFcgs5Mvku03uZ8w');
    update_option('bright_scorm_cloud_app_id','YW6BSUQCWC');
    update_option('bright_scorm_cloud_secret_key','7TtOtD9J6R5JjoCY8fzQwlQV6S0yMVPlyYJtslWr');

    parent::__construct();

    $this->bright = Bright\Wordpress::getInstance();
    if (empty($this->bright->accessToken)) {

      $faker = Faker\Factory::create();
      $fn = $faker->firstName;
      $ln = $faker->lastName;
      $username = strtolower($fn).'.'.strtolower($ln);
      $email = $username.'@example.com'."\n";

      $user = $this->bright->createUser($username, 'changeme!', $email);
      wp_update_user(array('ID' => $user->ID,
                           'first_name' => $fn,
                           'last_name' => $ln));
      $user_id = $this->bright->setCurrentUser($user);
      wp_set_current_user($user_id);
      $new_user = wp_get_current_user();
      if (empty($new_user->ID)) {
        Bright\testEcho($user);
        Bright\testEcho($new_user);
        throw new Exception('failed to set current user in WP');
      }
      $this->currentUser = $new_user; /* it's getting lost for some reason .... ah php */
      if (is_wp_error($user_id))
        throw new Exception('failed to update first or last name for user');
      $this->bright->getAuthenticationCodeForUser($user);
      $this->bright->log($user,false,'in test setup user');
      $this->bright->log($this->bright->accessToken,false,'in test setup access token');
      $this->bright->runQuiet = true;
      $doecho = !empty(getenv('ECHO'));
      $this->bright->echoLogging = false || $doecho;
      $this->bright->maximumLogMsg = 65900;
    } else {
      $this->insureCurrentUser();
    }
      
    BrightWooTestBootstrap::instance();

  }
}

