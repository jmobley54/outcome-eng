<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Vendor;
use OptArt\WoocommerceOnePageShopping\Classes\Services\translator;

/**
 * This class contains a set of methods which can be useful when implementing
 * own WordPress plugins. Class is designed for extending other classes, so all
 * of its methods should be declared as protected (or private in some cases).
 *
 * @author Piotr Szczygiel <psz.szczygiel@gmail.com>
 * @link https://github.com/piotr-szczygiel/wp-helpers - check out latest version
 * @license MIT
 * @version 1.3
 */
abstract class wp_helpers
{
    // JS file prefix
    const MIN_PREFIX = '-min';
    // path for plugin's JS files
    const JS_ASSETS_PATH = 'assets/js/';

    // identifier of current plugin (use set_identifiers to set its data)
    private static $plugin_identifier = '';

    // path to plugin file
    private static $plugin_file = '';

    /**
     * This property contains an information about in which class (1st element of the array)
     * and method (2nd element of the array) helper will look for the translation.
     * Remember that this method needs to contain only one input parameter and should
     * return the string.
     *
     * @var array
     */
    private $translation_info = array( 'OptArt\WoocommerceOnePageShopping\Classes\Services\translator', 'get_translation' );

    /**
     * The object of class defined in $translation_info property
     *
     * @var Translator
     */
    private $translation_object = null;

    /**
     * Method can be used when you want to use packed JavaScript files.
     * How to use it:
     * - create 'unpacked' folder under plugin's 'assets/js' folder
     * - create a new file under 'unpacked' folder and name it something like 'test.js'
     * - put some content into your file
     * - copy all the content and generate minified content using tool http://dean.edwards.name/packer/
     * - create file 'test-min.js' under 'assets/js' and paste the minified content there
     * - next step is to configure your WP to debug into file by settings following flags in wp-config.php file:
     *   - define( 'WP_DEBUG', true );
     *   - define( 'WP_DEBUG_LOG', true );
     *   - define( 'WP_DEBUG_DISPLAY', false );
     * You can also use only minified files (f.e third party plugins) by setting $debug parameter as true.
     *
     * @param string $identifier
     * @param string $file
     * @param string $plugin_file
     * @param boolean $debug
     * @param array $dep
     */
    protected function enqueue_script( $identifier, $file, $plugin_file, $debug = true, $dep = array( 'jquery' ) )
    {
        // debug mode
        if ( $debug === true && WP_DEBUG === true && WP_DEBUG_LOG === true && WP_DEBUG_DISPLAY === false ) {
            $file_path = 'unpacked/'.str_replace( self::MIN_PREFIX, '', $file );
        } // live mode
        else {
            $file_path = $file;
        }

        wp_enqueue_script( $identifier, plugins_url( self::JS_ASSETS_PATH.$file_path, $plugin_file ), $dep );
    }

    /**
     * This method is an interface for returning translation strings. Simply configureate the
     * $translation_info property and create adequate class to start using it.
     *
     * @param string $key
     * @return string
     * @throws \Exception
     */
    protected function get_translation( $key )
    {
        $method = $this->translation_info[1];

        return $this->get_translator()->$method( $key );
    }

    /**
     * Registering an ajax functions. Method supports both: admin and non-admin
     * user typing.
     *
     * @param string $name
     * @param array|string $callback
     * @param boolean $admin_only
     */
    protected function register_ajax_function( $name, $callback, $admin_only = true )
    {
        add_action( 'wp_ajax_'.$name, $callback );

        if ( $admin_only === false ) {
            add_action( 'wp_ajax_nopriv_' . $name, $callback );
        }
    }

    /**
     * Set the properties required for
     *
     * @param $plugin_identifier
     * @param $plugin_file
     */
    public static function set_identifiers( $plugin_identifier, $plugin_file )
    {
        self::$plugin_identifier = $plugin_identifier;
        self::$plugin_file = $plugin_file;
    }

    /**
     * Returns a path to plugin file
     *
     * @return string
     */
    public static function get_plugin_file()
    {
        return self::$plugin_file;
    }

    /**
     * Returns the path of current plugin
     *
     * @return string
     */
    public static function get_plugin_path()
    {
        return trailingslashit( plugin_dir_path( self::get_plugin_file() ) );
    }

    /**
     * Returns the object responsible for translating
     *
     * @return translator
     * @throws \Exception
     */
    public function get_translator()
    {
        if ( is_null( $this->translation_object ) ) {

            // check whether class exists
            if ( !class_exists( $this->translation_info[0] ) ) {
                throw new \Exception( 'Class defined in $translation_info property doesn\'t exist' );
            }

            // create an instance of translation class
            $this->translation_object = new $this->translation_info[0];
        }

        if ( !method_exists( $this->translation_object, $this->translation_info[1] ) ) {
            throw new \Exception( 'Method defined in $translation_info property doesn\'t exist' );
        }

        return $this->translation_object;
    }

    /**
     * Returns the plugin identifier
     * @return string
     */
    public static function get_plugin_identifier()
    {
        return self::$plugin_identifier;
    }
}