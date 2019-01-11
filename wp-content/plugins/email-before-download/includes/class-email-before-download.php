<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks,
 * public-facing site hooks, and processing hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/includes
 * @author     M & S Consulting
 */
class Email_Before_Download {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
	    $this->version = PLUGIN_NAME_VERSION;
		$this->plugin_name = 'email-before-download';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_process_hooks();
		$this->build_shortcode();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-email-before-download-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-email-before-download-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-email-before-download-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-email-before-download-table.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-email-before-download-public.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-email-before-download-shortcode.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-email-before-download-downloader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-email-before-download-process.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-email-before-download-form.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-email-before-download-db.php';
		$this->loader = new Email_Before_Download_Loader();

	}
	private function set_locale() {
		$plugin_i18n = new Email_Before_Download_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}
	private function define_admin_hooks() {
		$plugin_admin = new Email_Before_Download_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'build_settings' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
        $this->loader->add_action( 'admin_post_ebd.csv',$plugin_admin, 'print_csv' );
        $this->loader->add_action( 'admin_post_ebd.purge',$plugin_admin, 'purge_data' );
    }

	private function define_public_hooks() {

		$plugin_public = new Email_Before_Download_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_filter( 'the_content', $plugin_public,'shortcode_cleanup');
        $this->loader->add_action('wp_ajax_ebd_inline_links', $plugin_public, 'ebd_ajax');
        $this->loader->add_action('wp_ajax_nopriv_ebd_inline_links', $plugin_public, 'ebd_ajax');
        $this->loader->add_filter('dlm_can_download', $plugin_public, 'record',10,2);
    }
    private function define_process_hooks(){
        $plugin_process = new Email_Before_Download_Process( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'wpcf7_before_send_mail',$plugin_process, 'process_cf7' );
        $this->loader->add_filter('wpcf7_validate', $plugin_process, 'check_blacklist',10,2);
    }

    public function build_shortcode(){
        add_shortcode('email-download' ,array( new Email_Before_Download_Shortcode( $this->get_plugin_name(), $this->get_version() ),'init_shortcode') );
        add_shortcode('emailreq' ,array( new Email_Before_Download_Shortcode( $this->get_plugin_name(), $this->get_version() ),'init_shortcode') );
    }

	public function run() {
		$this->loader->run();
	}

    public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

    public function run_download($data) {
        $downloader = new Email_Before_download_Downloader( $this->get_plugin_name(), $this->get_version() );
        $downloader->serve_file($data['uid']);
        exit();

    }

}
