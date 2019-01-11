<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');
define('MAXBUTTONS_PRO_PLUGIN_NAME', trim(dirname(plugin_basename(MAXBUTTONS_PRO_ROOT_FILE)), '/'));
define('MAXBUTTONS_PRO_PLUGIN_DIR', trim( plugin_dir_path(MAXBUTTONS_PRO_ROOT_FILE) ) );
define('MAXBUTTONS_PRO_PLUGIN_URL', plugins_url() . '/' . MAXBUTTONS_PRO_PLUGIN_NAME);

$uploads = wp_upload_dir();

define('MAXBUTTONS_PRO_PACKS_DIR', $uploads['basedir'] . '/maxbuttons-pro/packs');
define('MAXBUTTONS_PRO_PACKS_URL', $uploads['baseurl'] . '/maxbuttons-pro/packs');
define('MAXBUTTONS_PRO_EXPORTS_DIR', $uploads['basedir'] . '/maxbuttons-pro/exports');
define('MAXBUTTONS_PRO_EXPORTS_URL', $uploads['baseurl'] . '/maxbuttons-pro/exports');


class maxButtonsPro extends maxButtonsPlugin
{
	protected $loaded_web_fonts = array();

	function __construct()
	{
		parent::__construct();
 		self::$instance = $this;


		add_action('media_buttons', array($this, 'media_button') );


		if( is_admin())
		{
			remove_action('admin_enqueue_scripts', array(parent::getInstance(),'add_admin_styles'));
			remove_action('admin_enqueue_scripts', array(parent::getInstance(),'add_admin_scripts'));
			remove_action('media_buttons', array(parent::getInstance(), 'media_button') );

			add_action('admin_menu', array($this, 'pro_admin_menu'),10);
			add_action('admin_enqueue_scripts', array($this,'add_admin_styles'),20);
			add_action('admin_enqueue_scripts', array($this,'add_admin_scripts'),20);

			add_action('mb/packs/display_notices', array($this, 'display_notices') );

			$license = $this->getClass('license');
			$license->check_license();
			$license->update_check();
			add_action('mb/header/display_notices', array($license, 'display_license'));


	 }

		add_filter('upload_mimes', array($this, 'upload_mimes'));
		add_filter('mb-support-link', array($this, 'pro_support_link'));
		add_action('wp_enqueue_scripts', array($this, 'pro_front_scripts'));

		// buttons button
		add_filter('plugin_action_links', array($this, "plugin_action_links"), 10, 2);

		add_action('init', array($this, 'load_textdomain'));

		add_action('wp_ajax_pack_request', array( maxUtils::namespaceit('maxProPack'), 'ajax_actions') );
		add_action('wp_ajax_import_button', array(maxUtils::namespaceit('maxProPack'), 'ajax_import_button') );
		add_action('wp_ajax_font_manager', array( maxUtils::namespaceit('maxButtonsProAdmin'),'ajax_font_actions') );
		add_action('maxbuttons/ajax/load_icons', array( maxUtils::namespaceit('maxButtonsProAdmin'), 'ajax_load_icons') );

		// runs on the form actions ( save, copy delete ) buttons on form
		add_action('mb/editor/form-actions', array(maxUtils::namespaceit('maxLicense'), 'license_locker'));

		maxIntegrationsPRO::init();
	}

	public function setMainClasses()
	{
		parent::setMainClasses();

		$this->mainClasses["admin"] = "maxButtonsProAdmin";
		$this->mainClasses["install"] = "maxInstallPro";
		$this->mainClasses["button"] = "maxProButton";
		$this->mainClasses["pack"] = "maxProPack";
		$this->mainClasses['license'] = 'maxLicense';
	}

 	public static function get_plugin_path($pro = false)
 	{
 		if (! $pro ) return parent::get_plugin_path();
 		return plugin_dir_path(MAXBUTTONS_PRO_ROOT_FILE);
 	}

 	public static function get_plugin_url($pro = false)
 	{
 	 	if (! $pro ) return parent::get_plugin_url();
 		return plugin_dir_url(MAXBUTTONS_PRO_ROOT_FILE);
 	}

	function pro_support_link($link)
	{
		return 'https://maxbuttons.com/forums';
	}

	function pro_admin_menu()
	{
		$maxbuttons_capabilities = get_option('maxbuttons_user_level');
		$menu_slug = 'maxbuttons-controller';
		$capability = $maxbuttons_capabilities;
		$admin_capability = 'manage_options';
		$submenu_function = array($this, 'load_pro_admin_page');

		// Now add the submenu page for the Packs page
		$submenu_page_title = __('MaxButtons Pro: Packs', 'maxbuttons-pro');
		$submenu_title = __('Packs', 'maxbuttons-pro');
		$submenu_slug = 'maxbuttons-packs';
		//$submenu_function = 'mbpro_packs';
		$admin_pages[] = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);


		// Now add the submenu page for the Export page
		$submenu_page_title = __('MaxButtons Pro: Export', 'maxbuttons-pro');
		$submenu_title = __('Export', 'maxbuttons-pro');
		$submenu_slug = 'maxbuttons-export';
		$admin_pages[] = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

		$submenu_page_title = __('MaxButtons Pro: License', 'maxbuttons-pro');
		$submenu_title = __('License', 'maxbuttons-pro');
		$submenu_slug = 'maxbuttons-license';

		$admin_pages[] = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $admin_capability, $submenu_slug, $submenu_function);

		/* Not needed basic screen */
		remove_submenu_page( $menu_slug, 'maxbuttons-pro' );

		global $submenu;
		global $menu;

		// re-order the items.
		$order = array("maxbuttons-controller" => 0,
					   "maxbuttons-controller&action=edit" => 1,
					   "maxbuttons-packs" => 2,
						 'maxbuttons-collections' => 3,
					   "maxbuttons-export" => 4,
					   "maxbuttons-settings" => 5,
					   "maxbuttons-support" => 6,
					   "maxbuttons-license" => 7,

					 );
		if (isset($submenu["maxbuttons-controller"])) // no set if user doens't have access to this plugin
		{
			$maxSub = $submenu["maxbuttons-controller"];
			$newSub = array();

			foreach($menu as $prio => $menu_data)
			{
				if (isset($menu_data[0]) && $menu_data[0] == 'MaxButtons')
					$menu[$prio][0] = __("MaxButtons Pro","maxbuttons-pro");

			}

			foreach ($maxSub as $index => $item)
			{

				$pos = $order[$item[2]];
				// Title MaxButtons -> MaxButtons Pro
				if (strpos($item[3], "MaxButtons Pro") === false)
					$item[3] = str_replace("MaxButtons","MaxButtons Pro",$item[3]);

				$newSub[$pos] = $item;

			}
			 ksort($newSub);
			$submenu["maxbuttons-controller"] = $newSub ;
		}
		add_filter("mb-load-admin-page-maxbuttons-controller", array($this, "controller"));
		add_filter('mb-load-admin-page-maxbuttons-packs', array($this,'controller'));

	}

	function controller($include)
	{
			if (isset($_GET["action"]))
			{
				switch ($_GET["action"])
				{
					case "pack":
						if (isset($_GET['id']) && $_GET["id"] != '')
						{
							return MAXBUTTONS_PRO_PLUGIN_DIR . "/includes/maxbuttons-pack.php";
						}
					break;
					case "pack-delete":
						$id = $_GET["id"];
						return MAXBUTTONS_PRO_PLUGIN_DIR . "/includes/maxbuttons-pack-delete.php";
					break;
					case "font-examples":
						return MAXBUTTONS_PRO_PLUGIN_DIR . "includes/maxbuttons-font-examples.php";
					break;
				}
			}
			return $include;
	}

	function load_pro_admin_page()
	{
		$page = sanitize_text_field($_GET["page"]);

		switch($page)
		{
			case "maxbuttons-packs":
				$pagepath = MAXBUTTONS_PRO_PLUGIN_DIR . "includes/maxbuttons-packs.php";
			break;
			case "maxbuttons-export":
				$pagepath =  MAXBUTTONS_PRO_PLUGIN_DIR . "includes/maxbuttons-export.php";
			break;

			case "maxbuttons-license":
				$pagepath = MAXBUTTONS_PRO_PLUGIN_DIR.  "includes/maxbuttons-license.php";
			break;


		}

		include(apply_filters("mb-load-admin-page-$page", $pagepath));
	}

	function load_admin_page()
	{

		$page = sanitize_text_field($_GET["page"]);

		if ($page == "maxbuttons-settings")
		{
			$version = MAXBUTTONS_VERSION_NUM;

			$js_url = trailingslashit($this->get_plugin_url(true)  . 'js');
			if (! $this->debug_mode)
				$js_url .= 'min/';

			wp_enqueue_script('mbpro-settings', $js_url . 'settings.js', array('maxbutton-admin', 'wp-color-picker'),$version, true);
		}

		parent::load_admin_page($page);
	}

	function add_admin_styles($hook) {
		$version = MAXBUTTONS_VERSION_NUM;

		// only hook in maxbuttons realm.
		if ( strpos($hook,'maxbuttons') === false && $hook != 'post.php' && $hook != 'post-new.php')
			return;

		parent::add_admin_styles($hook);
		wp_enqueue_style('maxbuttons-pro-newcss', MAXBUTTONS_PRO_PLUGIN_URL . "/assets/css/style.css", array(), $version);

	}

	function add_admin_scripts($hook) {

		// only hook in maxbuttons realm.
		if ( strpos($hook,'maxbuttons') === false )
			return;

		$version = MAXBUTTONS_VERSION_NUM;

		parent::add_admin_scripts($hook);
		wp_enqueue_script('media-upload');

		$js_url = trailingslashit($this->get_plugin_url(true) . 'js');
		if (! $this->debug_mode)
			$js_url .= 'min/';

   		wp_enqueue_media();

		wp_register_script('maxbuttons-pro',  $js_url . 'maxbuttons.js',
				array('jquery', 'maxbutton-admin'),$version, true );

 		wp_register_script('maxbuttons-font-library', $js_url . 'maxfonts.js', array('jquery'), $version, true);

		wp_register_script('maxbuttons-pro-license', $js_url . 'license.js', array('maxbuttons-pro'), $version, true);

		wp_register_script('maxbuttons-pro-icons', $js_url . 'icons.js', array('maxbuttons-pro'), $version, true);

		wp_register_script('maxbuttons-pro-init', $js_url . 'init_pro.js',
				array('maxbuttons-pro', 'maxbuttons-font-library'), $version, true);


 		$webfonts = $this->get_plugin_url(true) . 'assets/fonts/webfonts.json';

 		wp_localize_script('maxbuttons-font-library', 'mb_font_options', array(
 			'webfonts' => $webfonts,  //file_get_contents($webfonts),
 			'used_fonts' => get_option('maxbuttons_used_fonts'),
			'user_fonts' => get_option('maxbuttons_additional_fonts'),
			'combined_fonts' => json_encode(MB()->getClass('admin')->loadFonts()),
 			));

		wp_localize_script('maxbuttons-pro', 'mbpro_options', array(
			'colorPalette' => get_option('maxbuttons_colors'),
		));

		//wp_localize_script('maxbuttons-pro-icons');

 		// enqueue
 		wp_enqueue_script('maxbuttons-pro');
		wp_enqueue_script('maxbuttons-pro-license');
		wp_enqueue_script('maxbuttons-font-library');
		wp_enqueue_script('maxbuttons-pro-icons');
		wp_enqueue_script('maxbuttons-pro-init');



		// dequeue
		wp_dequeue_script('maxbutton-js-init');

	}
	function media_button($context) {
		// load styles and scripts into editor context
 		$version = MAXBUTTONS_VERSION_NUM;
 		$js_url = trailingslashit($this->get_plugin_url(true) . 'js');
		if (! $this->debug_mode)
			$js_url .= 'min/';

		wp_enqueue_script('maxbuttons-font', $js_url . 'maxbuttons_fonts.js', array('jquery'),$version, true);

		$context = parent::media_button($context);
		return $context;


	}
	function pro_front_scripts() {
 		$version = MAXBUTTONS_VERSION_NUM;
 		$js_url = trailingslashit($this->get_plugin_url(true) . 'js');
		if (! $this->debug_mode)
			$js_url .= 'min/';

		wp_register_script("mbpro-js", $js_url . 'maxbuttons-pro-front.js',null, $version, true);

		wp_enqueue_script('maxbuttons-font', $js_url . 'maxbuttons_fonts.js', array('jquery'),$version,true);
		wp_enqueue_script("mbpro-js");

	}
	/* Load the plugin textdomain */
	public function load_textdomain()
	{
		// For now manual load Maxbuttons domain due to path issues
		//parent::load_textdomain();

		// see: http://geertdedeckere.be/article/loading-wordpress-language-files-the-right-way
		$domain = 'maxbuttons';

		// The "plugin_locale" filter is also used in load_plugin_textdomain()
		$locale = apply_filters('plugin_locale', get_locale(), $domain);

		load_textdomain($domain, WP_LANG_DIR.'/maxbuttons/'.$domain.'-'.$locale.'.mo');
 		$result = load_plugin_textdomain($domain, false, MAXBUTTONS_PRO_PLUGIN_NAME . '/MaxButtons/languages');


		$domain = 'maxbuttons-pro';
		// The "plugin_locale" filter is also used in load_plugin_textdomain()
		$locale = apply_filters('plugin_locale', get_locale(), $domain);

		load_textdomain($domain, WP_LANG_DIR.'/maxbuttons/'.$domain.'-'.$locale.'.mo');

 		$result = load_plugin_textdomain($domain, false, MAXBUTTONS_PRO_PLUGIN_NAME . '/languages');

 	}

	function admin_footer_text($text)
	{
		if ( (! isset($_GET["page"])) || strpos($_GET["page"],'maxbuttons') === false)
			return $text;

		$text .=  "  <i>" . sprintf("MaxButtons release: %s", MAXBUTTONSPRO_RELEASE) . "</i>";
		return $text;

	}

	public function upload_mimes($existing_mimes ) {
		$existing_mimes['zip'] = 'application/zip';
		return $existing_mimes;
	}

	public function register_settings()
	{
		parent::register_settings();

		register_setting( 'maxbuttons_settings', 'maxbuttons_customcss' );
		register_setting( 'maxbuttons_settings', 'maxbuttons_updatefailhide' );
		register_setting( 'maxbuttons_settings', 'maxbuttons_colors');
		register_setting( 'maxbuttons_settings', 'maxbuttons_usecssfile');
	}

	public function footer()
	{
		$custom = get_option('maxbuttons_customcss');

		if ($custom != '')
		{
			$this->footer["css"]['custom'] = $custom;

		}

		$fonts_loaded = array();
		if (isset($this->footer['font']))
		{
			$webfonts = $this->footer['font'];
			$fonts_loaded = array();

			foreach($webfonts as $id => $fonts)
			{
				foreach($fonts as $font => $decl)
				{
					if (! in_array($font, $fonts_loaded))
					{
						$fonts_loaded[] = $font;
						echo '<link rel="stylesheet" type="text/css" href="' . $decl . '" media="screen">';
					}
				}
			}


			unset($this->footer['font']);
		}

		echo '<script type="text/javascript">
				var fonts_loaded =' . json_encode($fonts_loaded) . ';
			</script>';

		parent::footer();

	}

	function plugin_action_links($links, $file) {

		if ($file == plugin_basename(dirname(MAXBUTTONS_PRO_ROOT_FILE) . '/maxbuttons-pro.php')) {
			$label = __('Buttons', 'maxbuttons-pro');
			$dashboard_link = '<a href="' . admin_url() . 'admin.php?page=maxbuttons-controller&action=list">' . $label . '</a>';
			array_unshift($links, $dashboard_link);
		}

		return $links;
	}

} // CLASS
