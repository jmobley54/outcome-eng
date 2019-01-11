<?php
defined( 'ABSPATH' ) OR exit;
/**
 * Plugin Name: W8 Contact Form
 * Plugin URI: http://contactform.pantherius.com
 * Description: Modern, Customizable Contact Form with Multiple Contacts
 * Author: Pantherius
 * Version: 1.5.1
 * Author URI: http://pantherius.com
 */

define( 'W8CONTACT_FORM_VERSION' , '1.5.1' );
define( 'W8CONTACT_FORM_TEXT_DOMAIN' , 'contact-form-slider' );

if ( ! class_exists( 'contact_form_slider' ) ) {
	class contact_form_slider {
		public static $inserted = 0;
		protected static $instance = null;
		/**
		 * Construct the plugin object
		 */
		public function __construct() {
			// installation and uninstallation hooks
			register_activation_hook(__FILE__, array('contact_form_slider', 'activate'));
			register_deactivation_hook(__FILE__, array('contact_form_slider', 'deactivate'));
			register_uninstall_hook(__FILE__, array('contact_form_slider', 'uninstall'));
			add_action('wp_ajax_ajax_cfs', array(&$this, 'ajax_cfs'));
			add_action('wp_ajax_nopriv_ajax_cfs', array(&$this, 'ajax_cfs'));
			if ( is_admin() ) {
				add_action( 'plugins_loaded', array( &$this, 'contact_form_slider_localization' ) );
				require_once( sprintf( "%s/settings.php", dirname( __FILE__ ) ) );
				$contact_form_slider_settings = new contact_form_slider_settings();
				$plugin = plugin_basename( __FILE__ );
				add_filter( "plugin_action_links_$plugin", array( &$this, 'plugin_settings_link' ) );
			}
			else
			{
				if ( ! contact_form_slider::is_login_page() ) {
					if ( get_option( 'setting_display_globally' ) == 'on' ) {
						add_action( 'init', array( &$this, 'enqueue_custom_scripts_and_styles' ) );
					}
					if ( wp_is_mobile() ) {
						add_action( 'wp_head', array( &$this, 'add_meta_viewport' ) );
					}
				}
				add_filter( 'widget_text', 'do_shortcode' );
				add_shortcode( 'contact_form_slider', array( &$this, 'contact_form_slider_shortcodes' ) );
				add_shortcode( 'w8contact_form', array( &$this, 'contact_form_slider_shortcodes' ) );
				if ( get_option( 'setting_default_translation' ) == "on" ) {
					add_action( 'plugins_loaded', array( &$this, 'contact_form_slider_localization' ) );
				}
			}
		}
		public function contact_form_slider_localization() {
		// Localization
		load_plugin_textdomain( 'contact-form-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}
		
		function add_meta_viewport() {
		//	return print('<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">');
		}

		public static function getInstance() {
			if (!isset($instance)) 
			{
				$instance = new contact_form_slider;
			}
		return $instance;
		}
		/**
		* Handle shortcode
		**/
		public static function contact_form_slider_shortcodes( $atts ) {
			global $wpdb;
			extract( shortcode_atts( array(
					'hideicon' => 'false',
					'lockscreen' => 'true',
					'height' => 'normal',
					'transparency' => '90',
					'closeicon' => 'true',
					'autoopen' => 'false',
					'displayonce' => 'false',
					'sendcopy' => 'false',
					'hidephoto' => 'false',
					'captcha' => 'false',
					'disableonmobile' => 'false',
					'verticaldistance' => '50',
					'iconsize' => 'true',
					'direction' => 'true',
					'pfontweight' => '',
					'headerfontweight' => '',
					'subheaderfontweight' => '',
					'buttonfontweight' => '',
					'fieldfontweight' => '',
					'background' => '',
					'button_background' => '',
					'button_background_hover' => '',
					'defaultcolor' => '',
					'buttoncolor' => '',
					'iconurl' => 'true',
					'animation' => 'true',
					'reverseheader' => 'true',
					'bganim' => 'perspectiveright',
					'style' => 'true',
					'skin' => 'true',
					'photostyle' => 'true',
					'photoborder' => 'true',
					'iconanimation' => 'true',
					'fontfamily' => '',
					'namefontsize' => '',
					'titlefontsize' => '',
					'descfontsize' => '',
					'buttonfontsize' => '',
					'fieldfontsize' => '',
					'placeholder_name' => '',
					'placeholder_email' => '',
					'placeholder_message' => '',
					'placeholder_captcha' => '',
					'text_send' => '',
					'text_sendcopy' => '',
					'text_success' => '',
					'text_failed' => '',
					'flat' => 'false',
					'contacts' => array(1)
				), $atts, 'contact_form' ) );
		$contact_form_slider = contact_form_slider::getInstance();
		$params = $contact_form_slider->get_parameters();
			if (isset($atts['hideicon'])) $params['hide_icon'] = $atts['hideicon'];
			if (isset($atts['lockscreen'])) $params['lock_screen'] = $atts['lockscreen'];
			if (isset($atts['transparency'])) $params['transparency'] = $atts['transparency'];
			if (isset($atts['closeicon'])) $params['closeable'] = $atts['closeicon'];
			if (isset($atts['autoopen'])) $params['auto_open'] = $atts['autoopen'];
			if (isset($atts['displayonce'])) $params['dofsu'] = $atts['displayonce'];
			if (isset($atts['sendcopy'])) $params['sendcopy'] = $atts['sendcopy'];
			if (isset($atts['height'])) $params['height'] = $atts['height'];
			if (isset($atts['hidephoto'])) $params['disableimage'] = $atts['hidephoto'];
			if (isset($atts['captcha'])) $params['captcha'] = $atts['captcha'];
			if (isset($atts['disableonmobile'])) $params['dom'] = $atts['disableonmobile'];
			if (isset($atts['verticaldistance'])) $params['vertical_distance'] = $atts['verticaldistance'];
			if (isset($atts['iconsize'])) $params['icon_size'] = $atts['iconsize'];
			if (isset($atts['direction'])) $params['direction'] = $atts['direction'];
			if (isset($atts['pfontweight'])) $params['pfontweight'] = $atts['pfontweight'];
			if (isset($atts['headerfontweight'])) $params['headerfontweight'] = $atts['headerfontweight'];
			if (isset($atts['subheaderfontweight'])) $params['subheaderfontweight'] = $atts['subheaderfontweight'];
			if (isset($atts['buttonfontweight'])) $params['buttonfontweight'] = $atts['buttonfontweight'];
			if (isset($atts['fieldfontweight'])) $params['fieldfontweight'] = $atts['fieldfontweight'];
			if (isset($atts['background'])) $params['background'] = $atts['background'];
			if (isset($atts['button_background'])) $params['button_background'] = $atts['button_background'];
			if (isset($atts['button_background_hover'])) $params['button_background_hover'] = $atts['button_background_hover'];
			if (isset($atts['defaultcolor'])) $params['defaultcolor'] = $atts['defaultcolor'];
			if (isset($atts['buttoncolor'])) $params['buttoncolor'] = $atts['buttoncolor'];
			if (isset($atts['iconurl'])) $params['icon_url'] = $atts['iconurl'];
			if (isset($atts['animation'])) $params['animation'] = $atts['animation'];
			if (isset($atts['reverseheader'])) $params['reverse_header'] = $atts['reverseheader'];
			if (isset($atts['bganim'])) $params['bganim'] = $atts['bganim'];
			if (isset($atts['bgtarget'])) $params['bgtarget'] = $atts['bgtarget'];
			if (isset($atts['icon_image'])) $params['icon_image'] = $atts['icon_image'];
			if (isset($atts['excludeelements'])) $params['excludeelements'] = $atts['excludeelements'];
			if (isset($atts['style'])) $params['scheme'] = $atts['style'];
			if (isset($atts['skin'])) $params['skin'] = $atts['skin'];
			if (isset($atts['photostyle'])) $params['photostyle'] = $atts['photostyle'];
			if (isset($atts['photoborder'])) $params['photoborder'] = $atts['photoborder'];
			if (isset($atts['iconanimation'])) $params['shake'] = $atts['iconanimation'];
			if (isset($atts['fontfamily'])) $params['fontfamily'] = $atts['fontfamily'];
			if (isset($atts['namefontsize'])) $params['headerfontsize'] = $atts['namefontsize'];
			if (isset($atts['titlefontsize'])) $params['subheaderfontsize'] = $atts['titlefontsize'];
			if (isset($atts['descfontsize'])) $params['pfontsize'] = $atts['descfontsize'];
			if (isset($atts['buttonfontsize'])) $params['buttonfontsize'] = $atts['buttonfontsize'];
			if (isset($atts['fieldfontsize'])) $params['fieldfontsize'] = $atts['fieldfontsize'];
			if (isset($atts['placeholder_name'])) $params['placeholder_name'] = $atts['placeholder_name'];
			if (isset($atts['placeholder_email'])) $params['placeholder_email'] = $atts['placeholder_email'];
			if (isset($atts['placeholder_message'])) $params['placeholder_message'] = $atts['placeholder_message'];
			if (isset($atts['placeholder_captcha'])) $params['placeholder_captcha'] = $atts['placeholder_captcha'];
			if (isset($atts['text_send'])) $params['sendbutton_text'] = $atts['text_send'];
			if (isset($atts['text_sendcopy'])) $params['placeholder_sendcopy'] = $atts['text_sendcopy'];
			if (isset($atts['text_success'])) $params['success_message'] = $atts['text_success'];
			if (isset($atts['text_failed'])) $params['failed_text'] = $atts['text_failed'];
			if (isset($atts['flat'])&&$params['flat'] = "true") {$params['flat'] = $atts['flat']; $params['bganim'] = "disabled";$params['bgtarget'] = "";$params['excludeelements'] = "";$params['icon_image'] = "";}
			else $params['flat'] = "false";
			if (isset($atts['contacts'])) 
			{
			$cc = explode(",",$atts['contacts']);
			$newcc = array();
				foreach($cc as $index=>$content)
				{
					foreach($params['contacts'] as $key=>$pc)
					{
					$current_key = $key+1;
						if ($current_key==$content) {
						unset($params['contacts'][$key]->arsendername);
						unset($params['contacts'][$key]->arsenderemail);
						unset($params['contacts'][$key]->arsendermessage);
						$newcc[] = $params['contacts'][$key];
						}
					}
				}
				if (!empty($newcc)) $params['contacts'] = $newcc;
			}
			else
			{
				foreach($params['contacts'] as $key=>$pc)
				{
					if (isset($pc->status)) {if ($pc->status==0&&$key>0) {unset($params['contacts'][$key]);$params['contacts'] = array_values($params['contacts']);}}
					unset($params['contacts'][$key]->arsendername);
					unset($params['contacts'][$key]->arsenderemail);
					unset($params['contacts'][$key]->arsendermessage);
				}
			}
			$custom_fields = json_decode( stripslashes( get_option('cfs-custom-fields') ) );
			if ( $custom_fields == NULL ) {
				$custom_fields = "";
			}
		/* params for demo end */
			wp_enqueue_style('contact_form_slider_style', plugins_url( '/templates/assets/css/cfs.css' , __FILE__ ));
			wp_enqueue_style('jquery_ui_style', plugins_url( '/templates/assets/css/jquery-ui.css' , __FILE__ ));
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core',array('jquery'));
			wp_enqueue_script('jquery-effects-core',array('jquery'));
			wp_enqueue_script('jquery-effects-fade',array('jquery-effects-core'));
			wp_enqueue_script('jquery-effects-slide',array('jquery-effects-core'));
			wp_enqueue_script('jquery-effects-shake',array('jquery-effects-core'));
			if ( $params[ 'flat' ] != "true" ) {
				wp_enqueue_script( 'jquerymousewheel', plugins_url( '/templates/assets/js/jquery.mousewheel.js', __FILE__ ), array( 'jquery' ), W8CONTACT_FORM_VERSION, false );
				wp_enqueue_script( 'jscrollpane', plugins_url( '/templates/assets/js/jquery.jscrollpane.min.js', __FILE__ ), array( 'jquery' ), W8CONTACT_FORM_VERSION, false );
			}
			wp_register_script( "contact_form_slider_script", plugins_url( '/templates/assets/js/cfs.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core', 'jquery-effects-fade', 'jquery-effects-slide', 'jquery-effects-shake' ), W8CONTACT_FORM_VERSION, false );
			wp_localize_script( 'contact_form_slider_script', 'cfs_params', 
				array( 'customfields'=>$custom_fields, 
						'direction' => $params[ 'direction' ], 
						'pfontweight' => $params[ 'pfontweight' ], 
						'headerfontweight' => $params[ 'headerfontweight' ], 
						'subheaderfontweight' => $params[ 'subheaderfontweight' ], 
						'buttonfontweight' => $params[ 'buttonfontweight' ], 
						'fieldfontweight' => $params[ 'fieldfontweight' ], 
						'background' => $params[ 'background' ], 
						'button_background' => $params[ 'button_background' ], 
						'button_background_hover' => $params[ 'button_background_hover' ], 
						'defaultcolor' => $params[ 'defaultcolor' ], 
						'buttoncolor' => $params[ 'buttoncolor' ], 
						'closeable' => $params[ 'closeable' ], 
						'transparency' => $params['transparency'], 
						'hide_icon' => $params[ 'hide_icon' ], 
						'height' => $params[ 'height' ], 
						'icon_size' => $params[ 'icon_size' ], 
						'auto_open' => $params[ 'auto_open' ], 
						'captcha' => $params[ 'captcha' ], 
						'sendcopy' => $params[ 'sendcopy' ], 
						'disableimage' => $params[ 'disableimage' ], 
						'lock_screen' => $params[ 'lock_screen' ], 
						'dofsu' => $params[ 'dofsu' ], 
						'dom' => $params[ 'dom' ], 
						'vertical_distance' => $params[ 'vertical_distance' ], 
						'scheme' => $params[ 'scheme' ], 
						'skin' => $params[ 'skin' ], 
						'shake' => $params[ 'shake' ], 
						'icon_url' => $params[ 'icon_url' ],
						'customcontact' => $params[ 'contacts' ],
						'placeholder_name' => $params[ 'placeholder_name' ],
						'placeholder_email' => $params[ 'placeholder_email' ],
						'placeholder_message' => $params[ 'placeholder_message' ],
						'placeholder_captcha' => $params[ 'placeholder_captcha' ],
						'placeholder_sendcopy' => $params[ 'placeholder_sendcopy' ],
						'sendbutton_text' => $params[ 'sendbutton_text' ],
						'success_message' => $params[ 'success_message' ],
						'failed_text' => $params[ 'failed_text' ],
						'reverse_header' => $params[ 'reverse_header' ],
						'bordered_photo' => $params[ 'photoborder' ],
						'bodyanim' => $params[ 'bganim' ],
						'bgtarget' => $params[ 'bgtarget' ],
						'icon_image' => $params[ 'icon_image' ],
						'excludeelements' => $params[ 'excludeelements' ],
						'photo_style' => $params[ 'photostyle' ],
						'fontfamily' => $params[ 'fontfamily' ],
						'pfontsize' => $params[ 'pfontsize' ],
						'headerfontsize' => $params[ 'headerfontsize' ],
						'subheaderfontsize' => $params[ 'subheaderfontsize' ],
						'buttonfontsize' => $params[ 'buttonfontsize' ],
						'fieldfontsize' => $params[ 'fieldfontsize' ],
						'animationtype' => $params[ 'animation' ],
						'flat' => $params[ 'flat' ],
						"plugin_directory" => plugins_url( '', __FILE__ ),
						"path" => admin_url( 'admin-ajax.php' ) 
				)
			);
			wp_enqueue_script( 'contact_form_slider_script' );
			if ( isset( $atts[ 'flat' ] ) && $params[ 'flat' ] = "true" ) {
				return( '<div id="cfs-container"></div>' );
			}
		}
		/**
		* Activate the plugin
		**/
		public static function activate()
		{
			global $wpdb;
			$db_info = array();
			//define custom data tables
			$db_info = $wpdb->get_row( "SHOW TABLE STATUS FROM `" . $wpdb->dbname . "` WHERE `name` = '" . $wpdb->prefix . "options'" );
			//creating custom tables
			$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . 'cfs_logs' . " (
			  autoid mediumint(9) NOT NULL AUTO_INCREMENT,
			  type tinyint(1) NOT NULL,
			  content text NOT NULL,
			  logtime timestamp NOT NULL,
			  UNIQUE KEY autoid (autoid)
			) COLLATE '" . $db_info->Collation . "'";
			$wpdb->query( $sql );
			//default general params
			if ( ! get_option( 'setting_keep_logs' ) ) {
				add_option( 'setting_keep_logs', 'off' );
			}
			if ( ! get_option( 'setting_keep_settings' ) )	{
				add_option( 'setting_keep_settings', 'off' );
			}
			if ( ! get_option( 'setting_sendername' ) ) {
				add_option( 'setting_sendername', 'Contact' );
			}
			if ( ! get_option( 'setting_sendermail' ) ) {
				add_option( 'setting_sendermail', 'contact' );
			}
			if ( ! get_option( 'setting_hide_icon' ) ) {
				add_option( 'setting_hide_icon', 'off' );
			}
			if ( ! get_option( 'setting_lock_screen' ) ) {
				add_option( 'setting_lock_screen', 'on' );
			}
			if ( ! get_option( 'setting_closeable' ) ) {
				add_option( 'setting_closeable', 'on' );
			}
			if ( ! get_option('setting_transparency'))
			{
				add_option('setting_transparency' , '60%');
			}
			if ( ! get_option('setting_display_once_for_same_user'))
			{
				add_option('setting_display_once_for_same_user' , 'off');
			}
			if ( ! get_option('setting_display_globally'))
			{
				add_option('setting_display_globally' , 'on');
			}
			if ( ! get_option('setting_auto_open'))
			{
				add_option('setting_auto_open' , 'off');
			}
			if ( ! get_option('setting_sendcopy'))
			{
				add_option('setting_sendcopy' , 'off');
			}
			if ( ! get_option('setting_disableimage'))
			{
				add_option('setting_disableimage' , 'off');
			}
			if ( ! get_option('setting_captcha'))
			{
				add_option('setting_captcha' , 'image');
			}
			if ( ! get_option('setting_disable_on_mobile'))
			{
				add_option('setting_disable_on_mobile' , 'off');
			}
			//default style params
			if ( ! get_option('setting_vertical_distance'))
			{
				add_option('setting_vertical_distance' , '50%');
			}
			if ( ! get_option('setting_icon_size'))
			{
				add_option('setting_icon_size' , 'medium');
			}
			if ( ! get_option('setting_direction'))
			{
				add_option('setting_direction' , 'left');
			}
			if ( ! get_option('setting_pfontweight'))
			{
				add_option('setting_pfontweight' , 'normal');
			}
			if ( ! get_option('setting_headerfontweight'))
			{
				add_option('setting_headerfontweight' , 'normal');
			}
			if ( ! get_option('setting_subheaderfontweight'))
			{
				add_option('setting_subheaderfontweight' , 'normal');
			}
			if ( ! get_option('setting_buttonfontweight'))
			{
				add_option('setting_buttonfontweight' , 'normal');
			}
			if ( ! get_option('setting_fieldfontweight'))
			{
				add_option('setting_fieldfontweight' , 'normal');
			}
			if ( ! get_option('setting_background'))
			{
				add_option('setting_background' , 'off');
			}
			if ( ! get_option('setting_button_background'))
			{
				add_option('setting_button_background' , 'off');
			}
			if ( ! get_option('setting_button_background_hover'))
			{
				add_option('setting_button_background_hover' , 'off');
			}
			if ( ! get_option('setting_defaultcolor'))
			{
				add_option('setting_defaultcolor' , 'off');
			}
			if ( ! get_option('setting_buttoncolor'))
			{
				add_option('setting_buttoncolor' , 'off');
			}
			if ( ! get_option('setting_animation'))
			{
				add_option('setting_animation' , 'Quart');
			}
			if ( ! get_option('setting_reverseheader'))
			{
				add_option('setting_reverseheader' , 'off');
			}
			if ( ! get_option('setting_photoborder'))
			{
				add_option('setting_photoborder' , 'off');
			}
			if ( ! get_option('setting_bgtarget'))
			{
				add_option('setting_bgtarget' , '');
			}
			if ( ! get_option('setting_icon_image'))
			{
				add_option('setting_icon_image' , '');
			}
			if ( ! get_option('setting_excludeelements'))
			{
				add_option('setting_excludeelements' , '');
			}
			if ( ! get_option('setting_bganim'))
			{
				add_option('setting_bganim' , 'cfs_perspectiveright');
			}
			if ( ! get_option('setting_scheme'))
			{
				add_option('setting_scheme' , 'light');
			}
			if ( ! get_option('setting_skin'))
			{
				add_option('setting_skin' , 'default');
			}
			if ( ! get_option('setting_icon_url'))
			{
				add_option('setting_icon_url' , '');
			}
			if ( ! get_option('setting_shake'))
			{
				add_option('setting_shake' , '0');
			}
			if ( ! get_option('setting_fontfamily'))
			{
				add_option('setting_fontfamily' , '');
			}
			if ( ! get_option('setting_pfontsize'))
			{
				add_option('setting_pfontsize' , '12px');
			}
			if ( ! get_option('setting_headerfontsize'))
			{
				add_option('setting_headerfontsize' , '16px');
			}
			if ( ! get_option('setting_height'))
			{
				add_option('setting_height' , 'full');
			}
			if ( ! get_option('setting_subheaderfontsize'))
			{
				add_option('setting_subheaderfontsize' , '12px');
			}
			if ( ! get_option('setting_buttonfontsize'))
			{
				add_option('setting_buttonfontsize' , '14px');
			}
			if ( ! get_option('setting_fieldfontsize'))
			{
				add_option('setting_fieldfontsize' , '12px');
			}
			//default autoreply params
			if ( ! get_option('setting_global_autoreply'))
			{
				add_option('setting_global_autoreply' , 'off');
			}
			if ( ! get_option('setting_global_arsendername'))
			{
				add_option('setting_global_arsendername' , 'Auto-Reply');
			}
			if ( ! get_option('setting_global_arsenderemail'))
			{
				add_option('setting_global_arsenderemail' , 'noreply');
			}
			if ( ! get_option('setting_global_arsendermessage'))
			{
				add_option('setting_global_arsendermessage' , 'Thank you for your message, we will reply as soon as we can.');
			}
			//default translation params
			if ( ! get_option('setting_placeholder_name'))
			{
				add_option('setting_placeholder_name' , 'Enter your name');
			}
			if ( ! get_option('setting_placeholder_email'))
			{
				add_option('setting_placeholder_email' , 'Enter your email address');
			}
			if ( ! get_option('setting_placeholder_message'))
			{
				add_option('setting_placeholder_message' , 'Type your message...');
			}
			if ( ! get_option('setting_placeholder_captcha'))
			{
				add_option('setting_placeholder_captcha' , 'Enter the numbers');
			}
			if ( ! get_option('setting_placeholder_sendcopy'))
			{
				add_option('setting_placeholder_sendcopy' , 'Send a copy to my email address');
			}
			if ( ! get_option('setting_sendbutton_text'))
			{
				add_option('setting_sendbutton_text' , 'Send');
			}
			if ( ! get_option('setting_success_message'))
			{
				add_option('setting_success_message' , 'Message sent successfully.');
			}
			if ( ! get_option('setting_failed_text'))
			{
				add_option('setting_failed_text' , 'FAILED');
			}
			if ( ! get_option('setting_default_translation'))
			{
				add_option('setting_default_translation' , 'off');
			}
			if ( ! get_option('setting_enable_logs'))
			{
				add_option('setting_enable_logs' , 'off');
			}
			if ( ! get_option('cfs-custom-fields'))
			{
				add_option('cfs-custom-fields' , '');
			}
			if ( ! get_option('setting_w8cfs_customcss'))
			{
				add_option('setting_w8cfs_customcss' , '');
			}
			if ( ! get_option('setting_contacts'))
			{
			$blogemail = get_bloginfo( 'admin_email' );
			$be = explode('@',$blogemail);
				add_option('setting_contacts' , '[{"name":"General Questions","email":"'.$be[0].'","emaildomain":"'.$be[1].'","title":"'.get_bloginfo( 'title' ).'","subtitle":"'.get_bloginfo( 'description' ).'","text":"We appreciate your feedback, please leave a message. You can also contact with us on the social networks you can find above this message.","photo":"'.plugins_url('/templates/assets/img/default-photo.png' , __FILE__).'","facebook":"#","googleplus":"#","twitter":"#","pinterest":"#","linkedin":"#","skype":"#","tumblr":"#","flickr":"#","foursquare":"#","youtube":"#"}]');
			}
		}
		//Check it is a login or registration page
		function is_login_page() {
			return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php', '404.php'));
		}
		/**
		* Deactivate the plugin
		**/
		public static function deactivate()
		{
			unregister_setting('contact_form_slider-group', 'setting_sendername');
			unregister_setting('contact_form_slider-group', 'setting_sendermail');
			unregister_setting('contact_form_slider-group', 'setting_hide_icon');
			unregister_setting('contact_form_slider-group', 'setting_lock_screen');
			unregister_setting('contact_form_slider-group', 'setting_closeable');
			unregister_setting('contact_form_slider-group', 'setting_transparency');
			unregister_setting('contact_form_slider-group', 'setting_display_once_for_same_user');
			unregister_setting('contact_form_slider-group', 'setting_display_globally');
			unregister_setting('contact_form_slider-group', 'setting_disable_on_mobile');
			unregister_setting('contact_form_slider-group', 'setting_auto_open');
			unregister_setting('contact_form_slider-group', 'setting_sendcopy');
			unregister_setting('contact_form_slider-group', 'setting_disableimage');
			unregister_setting('contact_form_slider-group', 'setting_captcha');
			unregister_setting('contact_form_slider-group', 'setting_keep_settings');
			unregister_setting('contact_form_slider_styles-group', 'setting_vertical_distance');
			unregister_setting('contact_form_slider_styles-group', 'setting_icon_size');
			unregister_setting('contact_form_slider_styles-group', 'setting_direction');
			unregister_setting('contact_form_slider_styles-group', 'setting_pfontweight');
			unregister_setting('contact_form_slider_styles-group', 'setting_headerfontweight');
			unregister_setting('contact_form_slider_styles-group', 'setting_subheaderfontweight');
			unregister_setting('contact_form_slider_styles-group', 'setting_buttonfontweight');
			unregister_setting('contact_form_slider_styles-group', 'setting_fieldfontweight');
			unregister_setting('contact_form_slider_styles-group', 'setting_background');
			unregister_setting('contact_form_slider_styles-group', 'setting_button_background');
			unregister_setting('contact_form_slider_styles-group', 'setting_button_background_hover');
			unregister_setting('contact_form_slider_styles-group', 'setting_defaultcolor');
			unregister_setting('contact_form_slider_styles-group', 'setting_buttoncolor');
			unregister_setting('contact_form_slider_styles-group', 'setting_icon_url');
			unregister_setting('contact_form_slider_styles-group', 'setting_animation');
			unregister_setting('contact_form_slider_styles-group', 'setting_height');
			unregister_setting('contact_form_slider_styles-group', 'setting_reverseheader');
			unregister_setting('contact_form_slider_styles-group', 'setting_bganim');
			unregister_setting('contact_form_slider_styles-group', 'setting_bgtarget');
			unregister_setting('contact_form_slider_styles-group', 'setting_icon_image');
			unregister_setting('contact_form_slider_styles-group', 'setting_excludeelements');
			unregister_setting('contact_form_slider_styles-group', 'setting_scheme');
			unregister_setting('contact_form_slider_styles-group', 'setting_skin');
			unregister_setting('contact_form_slider_styles-group', 'setting_photostyle');
			unregister_setting('contact_form_slider_styles-group', 'setting_photoborder');
			unregister_setting('contact_form_slider_styles-group', 'setting_shake');
			unregister_setting('contact_form_slider_styles-group', 'setting_fontfamily');
			unregister_setting('contact_form_slider_styles-group', 'setting_pfontsize');
			unregister_setting('contact_form_slider_styles-group', 'setting_headerfontsize');
			unregister_setting('contact_form_slider_styles-group', 'setting_subheaderfontsize');
			unregister_setting('contact_form_slider_styles-group', 'setting_buttonfontsize');
			unregister_setting('contact_form_slider_styles-group', 'setting_fieldfontsize');
			unregister_setting('contact_form_slider_translations-group', 'setting_placeholder_name');
			unregister_setting('contact_form_slider_translations-group', 'setting_placeholder_email');
			unregister_setting('contact_form_slider_translations-group', 'setting_placeholder_message');
			unregister_setting('contact_form_slider_translations-group', 'setting_placeholder_captcha');
			unregister_setting('contact_form_slider_translations-group', 'setting_placeholder_sendcopy');
			unregister_setting('contact_form_slider_translations-group', 'setting_sendbutton_text');
			unregister_setting('contact_form_slider_translations-group', 'setting_success_message');
			unregister_setting('contact_form_slider_translations-group', 'setting_failed_text');
			unregister_setting('contact_form_slider_translations-group', 'setting_default_translation');
			unregister_setting('contact_form_slider_autoreply-group', 'setting_global_autoreply');
			unregister_setting('contact_form_slider_autoreply-group', 'setting_global_arsendername');
			unregister_setting('contact_form_slider_autoreply-group', 'setting_global_arsenderemail');
			unregister_setting('contact_form_slider_autoreply-group', 'setting_global_arsendermessage');
			unregister_setting('contact_form_slider_logs-group', 'setting_enable_logs');
			unregister_setting('contact_form_slider_logs-group', 'setting_keep_logs');
			unregister_setting('contact_form_slider_customcss-group', 'setting_w8cfs_customcss');
		}
		
		/**
		* Uninstall the plugin
		**/
		public static function uninstall()
		{
			global $wpdb;
			//define custom data tables
			if (get_option("setting_keep_logs")=="off") $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix.'cfs_logs');
			if (get_option("setting_keep_settings")=="off")
			{
				delete_option('setting_sendername');
				delete_option('setting_sendermail');
				delete_option('setting_hide_icon');
				delete_option('setting_lock_screen');
				delete_option('setting_closeable');
				delete_option('setting_transparency');
				delete_option('setting_display_once_for_same_user');
				delete_option('setting_display_globally');
				delete_option('setting_disable_on_mobile');
				delete_option('setting_auto_open');
				delete_option('setting_sendcopy');
				delete_option('setting_height');
				delete_option('setting_disableimage');
				delete_option('setting_captcha');
				delete_option('setting_vertical_distance');
				delete_option('setting_icon_size');
				delete_option('setting_direction');
				delete_option('setting_pfontweight');
				delete_option('setting_headerfontweight');
				delete_option('setting_subheaderfontweight');
				delete_option('setting_buttonfontweight');
				delete_option('setting_fieldfontweight');
				delete_option('setting_background');
				delete_option('setting_button_background');
				delete_option('setting_button_background_hover');
				delete_option('setting_defaultcolor');
				delete_option('setting_buttoncolor');
				delete_option('setting_icon_url');
				delete_option('setting_animation');
				delete_option('setting_reverseheader');
				delete_option('setting_bganim');
				delete_option('setting_bgtarget');
				delete_option('setting_icon_image');
				delete_option('setting_excludeelements');
				delete_option('setting_scheme');
				delete_option('setting_skin');
				delete_option('setting_photostyle');
				delete_option('setting_photoborder');
				delete_option('setting_shake');
				delete_option('setting_fontfamily');
				delete_option('setting_pfontsize');
				delete_option('setting_headerfontsize');
				delete_option('setting_subheaderfontsize');
				delete_option('setting_buttonfontsize');
				delete_option('setting_fieldfontsize');
				delete_option('setting_placeholder_name');
				delete_option('setting_placeholder_email');
				delete_option('setting_placeholder_message');
				delete_option('setting_placeholder_captcha');
				delete_option('setting_placeholder_sendcopy');
				delete_option('setting_sendbutton_text');
				delete_option('setting_success_message');
				delete_option('setting_failed_text');
				delete_option('setting_default_translation');
				delete_option('setting_contacts');
				delete_option('setting_global_autoreply');
				delete_option('setting_global_arsendername');
				delete_option('setting_global_arsenderemail');
				delete_option('setting_global_arsendermessage');
				delete_option('setting_enable_logs');
				delete_option('setting_keep_settings');
				delete_option('setting_keep_logs');
				delete_option('setting_w8cfs_customcss');
			}
		}
		
		public function get_parameters()
		{
			if (get_option('setting_hide_icon')=='off') $params['hide_icon'] = 'false';
			else $params['hide_icon'] = 'true';
			if (get_option('setting_lock_screen')=='on') $params['lock_screen'] = 'true';
			else $params['lock_screen'] = 'false';
			if (get_option('setting_closeable')=='on') $params['closeable'] = 'true';
			else $params['closeable'] = 'false';
			if (get_option('setting_transparency')>=0) $params['transparency'] = get_option('setting_transparency');
			else $params['transparency'] = '90%';
			if (get_option('setting_display_once_for_same_user')=='on') $params['dofsu'] = 'true';
			else $params['dofsu'] = 'false';
			if (get_option('setting_disable_on_mobile')=='on') $params['dom'] = 'true';
			else $params['dom'] = 'false';
			if (get_option('setting_auto_open')=='off') $params['auto_open'] = 'false';
			else $params['auto_open'] = 'true';
			if (get_option('setting_sendcopy')=='off') $params['sendcopy'] = 'false';
			else $params['sendcopy'] = 'true';
			if (get_option('setting_height')=='full') $params['height'] = 'full';
			else $params['height'] = 'normal';
			if (get_option('setting_disableimage')=='off') $params['disableimage'] = 'false';
			else $params['disableimage'] = 'true';
			if (get_option('setting_captcha')) $params['captcha'] = get_option('setting_captcha');
			else $params['captcha'] = 'image';
			if (get_option('setting_vertical_distance')) $params['vertical_distance'] = get_option('setting_vertical_distance');
			else $params['vertical_distance'] = '50';
			if (get_option('setting_icon_size')) $params['icon_size'] = get_option('setting_icon_size');
			else $params['icon_size'] = 'medium';
			if (get_option('setting_direction')=='right') $params['direction'] = 'right';
			else $params['direction'] = 'left';
			if (get_option('setting_pfontweight')=='bold') $params['pfontweight'] = 'bold';
			else $params['pfontweight'] = 'normal';
			if (get_option('setting_headerfontweight')=='bold') $params['headerfontweight'] = 'bold';
			else $params['headerfontweight'] = 'normal';
			if (get_option('setting_subheaderfontweight')=='bold') $params['subheaderfontweight'] = 'bold';
			else $params['subheaderfontweight'] = 'normal';
			if (get_option('setting_buttonfontweight')=='bold') $params['buttonfontweight'] = 'bold';
			else $params['buttonfontweight'] = 'normal';
			if (get_option('setting_fieldfontweight')=='bold') $params['fieldfontweight'] = 'bold';
			else $params['fieldfontweight'] = 'normal';
			if (get_option('setting_background')=='off' || ! get_option('setting_background') ) $params['background'] = '';
			else $params['background'] = get_option('setting_background');
			if (get_option('setting_button_background')=='off' || ! get_option('setting_button_background') ) $params['button_background'] = '';
			else $params['button_background'] = get_option('setting_button_background');
			if (get_option('setting_button_background_hover')=='off' || ! get_option('setting_button_background_hover') ) $params['button_background_hover'] = '';
			else $params['button_background_hover'] = get_option('setting_button_background_hover');
			if (get_option('setting_defaultcolor')=='off' || ! get_option('setting_defaultcolor') ) $params['defaultcolor'] = '';
			else $params['defaultcolor'] = get_option('setting_defaultcolor');
			if (get_option('setting_buttoncolor')=='off' || ! get_option('setting_buttoncolor') ) $params['buttoncolor'] = '';
			else $params['buttoncolor'] = get_option('setting_buttoncolor');
			if (get_option('setting_icon_url')) $params['icon_url'] = get_option('setting_icon_url');
			else $params['icon_url'] = '';
			if (get_option('setting_animation')) $params['animation'] = get_option('setting_animation');
			else $params['animation'] = 'Quart';
			if (get_option('setting_reverseheader')=="off") $params['reverse_header'] = 'false';
			else $params['reverse_header'] = 'true';
			if (get_option('setting_bganim')) $params['bganim'] = get_option('setting_bganim');
			else $params['bganim'] = 'cfs_perspectiveright';
			if (get_option('setting_bgtarget')!="") $params['bgtarget'] = get_option('setting_bgtarget');
			else $params['bgtarget'] = '';
			if (get_option('setting_icon_image')!="") $params['icon_image'] = get_option('setting_icon_image');
			else $params['icon_image'] = '';
			if (get_option('setting_excludeelements')!="") $params['excludeelements'] = get_option('setting_excludeelements');
			else $params['excludeelements'] = '';
			if (get_option('setting_scheme')) $params['scheme'] = get_option('setting_scheme');
			else $params['scheme'] = 'light';
			if (get_option('setting_skin')) $params['skin'] = get_option('setting_skin');
			else $params['skin'] = 'default';
			if (get_option('setting_photostyle')) $params['photostyle'] = get_option('setting_photostyle');
			else $params['photostyle'] = 'false';
			if (get_option('setting_photoborder')) $params['photoborder'] = get_option('setting_photoborder');
			else $params['photoborder'] = 'false';
			if (get_option('setting_shake')) $params['shake'] = get_option('setting_shake');
			else $params['shake'] = '0';
			if (get_option('setting_fontfamily')) $params['fontfamily'] = get_option('setting_fontfamily');
			else $params['fontfamily'] = '';
			if (get_option('setting_pfontsize')) $params['pfontsize'] = get_option('setting_pfontsize');
			else $params['pfontsize'] = '12px';
			if (get_option('setting_headerfontsize')) $params['headerfontsize'] = get_option('setting_headerfontsize');
			else $params['headerfontsize'] = '16px';
			if (get_option('setting_subheaderfontsize')) $params['subheaderfontsize'] = get_option('setting_subheaderfontsize');
			else $params['subheaderfontsize'] = '12px';
			if (get_option('setting_buttonfontsize')) $params['buttonfontsize'] = get_option('setting_buttonfontsize');
			else $params['buttonfontsize'] = '14px';
			if (get_option('setting_fieldfontsize')) $params['fieldfontsize'] = get_option('setting_fieldfontsize');
			else $params['fieldfontsize'] = '12px';
			if (get_option('setting_placeholder_name')) $params['placeholder_name'] = get_option('setting_placeholder_name');
			else $params['placeholder_name'] = 'Enter your name';
			if (get_option('setting_placeholder_email')) $params['placeholder_email'] = get_option('setting_placeholder_email');
			else $params['placeholder_email'] = 'Enter your email address';
			if (get_option('setting_placeholder_message')) $params['placeholder_message'] = get_option('setting_placeholder_message');
			else $params['placeholder_message'] = 'Type your message...';
			if (get_option('setting_placeholder_captcha')) $params['placeholder_captcha'] = get_option('setting_placeholder_captcha');
			else $params['placeholder_captcha'] = 'Enter the numbers';
			if (get_option('setting_placeholder_sendcopy')) $params['placeholder_sendcopy'] = get_option('setting_placeholder_sendcopy');
			else $params['placeholder_sendcopy'] = 'Send a copy to my email address';
			if (get_option('setting_sendbutton_text')) $params['sendbutton_text'] = get_option('setting_sendbutton_text');
			else $params['sendbutton_text'] = 'Send';
			if (get_option('setting_success_message')) $params['success_message'] = get_option('setting_success_message');
			else $params['success_message'] = 'Message sent successfully';
			if (get_option('setting_failed_text')) $params['failed_text'] = get_option('setting_failed_text');
			else $params['failed_text'] = 'FAILED';
			if (get_option('setting_default_translation')=="on")
			{
				$params['placeholder_name'] = __( 'Enter your name', W8CONTACT_FORM_TEXT_DOMAIN );
				$params['placeholder_email'] = __( 'Enter your email address', W8CONTACT_FORM_TEXT_DOMAIN );
				$params['placeholder_message'] = __( 'Type your message...', W8CONTACT_FORM_TEXT_DOMAIN );
				$params['placeholder_captcha'] = __( 'Enter the numbers', W8CONTACT_FORM_TEXT_DOMAIN );
				$params['placeholder_sendcopy'] = __( 'Send a copy to my email address', W8CONTACT_FORM_TEXT_DOMAIN );
				$params['sendbutton_text'] = __( 'Send', W8CONTACT_FORM_TEXT_DOMAIN );
				$params['success_message'] = __( 'Message sent successfully', W8CONTACT_FORM_TEXT_DOMAIN );
				$params['failed_text'] = __( 'FAILED', W8CONTACT_FORM_TEXT_DOMAIN );
			}
			$blogemail = get_bloginfo( 'admin_email' );
			$be = explode('@',$blogemail);
			$ccontact = json_decode(stripslashes('[{"name":"General Questions","email":"'.$be[0].'","emaildomain":"'.$be[1].'","title":"'.get_bloginfo( 'title' ).'","subtitle":"'.get_bloginfo( 'description' ).'","text":"We appreciate your feedback, please leave a message. You can also contact with us on the social networks you can find above this message.","photo":"'.plugins_url('/templates/assets/img/default-photo.png' , __FILE__).'","facebook":"#","googleplus":"#","twitter":"#","pinterest":"#","linkedin":"#","skype":"#","tumblr":"#","flickr":"#","foursquare":"#","youtube":"#"}]'));
			if (get_option('setting_contacts')) $params['contacts'] = json_decode(stripslashes(get_option('setting_contacts')));
			else $params['contacts'] = $ccontact;
			return $params;
		}
		
		public function save_log($type,$content)
		{
		global $wpdb;
			$wpdb->insert( $wpdb->prefix."cfs_logs", array( 
				'type' => sanitize_text_field($type), 
				'content' => $content
				) );		
		}
		
		public function clear_log() {
			global $wpdb;
			$trunc = $wpdb->query("TRUNCATE TABLE ".$wpdb->prefix.'cfs_logs');
			return $trunc;
		}
		
		public function ajax_cfs() {
			if ( isset( $_REQUEST[ 'cfscmd' ] ) ) {
				$cfscmd = strip_tags( $_REQUEST[ 'cfscmd' ] );
			}
			else {
				$cfscmd = '';
			}
			if ( $cfscmd == "clearlogs" ) {
				if ( $this->clear_log() ) {
					die("success");
				}
				else {
					die("fail");
				}
			}
			if ( $cfscmd == 'getlogs' ) {
				global $wpdb;
				$sql = "SELECT * FROM ".$wpdb->prefix."cfs_logs ORDER BY autoid DESC";
				$cfs_sql = $wpdb->get_results($sql);
				$result = '<div class="main one-log-row"><div class="one-log-time">Time</div><div class="one-log-rec">Recipient</div><div class="one-log-subject">Subject</div><div class="one-log-status">Status</div></div>';
				if (!empty($cfs_sql))
				{
					foreach($cfs_sql as $cs)
					{
						$datas = @unserialize($cs->content);
						if ($datas)
						{
						$date=date_create($cs->logtime);
						$autoreply = '<hr><div>Auto-Reply: Not sent</div>';
						if (isset($datas['data']['autoreply']))
						{
							if ($datas['data']['autoreply']) 
							{
								$autoreply = '<hr><div>Auto-Reply: '.$datas['data']['autoreply']['type'].'</div>';
								$autoreply .= '<div>Auto-Reply Status: '.$datas['data']['autoreply']['status'].'</div>';
								$autoreply .= '<div>Auto-Reply Sender: '.$datas['data']['autoreply']['from'].' &lt;'.$datas['data']['autoreply']['frommail'].'@'.str_replace("www.","",$_SERVER['HTTP_HOST']).'&gt;</div>';
								$autoreply .= '<div><strong>Auto-Reply Message</strong><br>'.$datas['data']['autoreply']['rmessage'].'</div>';
							}
						}
							$clientcopy = "<i>".__( 'Copy of the email has not been sent to the Client', W8CONTACT_FORM_TEXT_DOMAIN )."</i>";
							if (isset($datas['data']['mail']['copy']))
							{ 
								if ($datas['data']['mail']['copy']=="true") $clientcopy = "<i>".__( 'Copy of the email has been sent to the Client', W8CONTACT_FORM_TEXT_DOMAIN )."</i>";
							}
							$result .= '<div class="one-log-row"><div class="one-log-time">'.date_format($date,"d M Y, H:i").'</div><div class="one-log-rec">'.$datas['data']['mail']['tomail'].'</div><div class="one-log-subject">'.$datas['data']['mail']['subject'].'</div><div class="one-log-status">'.$datas['data']['mail']['status'].'</div><div class="one-log-details"><div class="one-log-details-inner"><div>Mail Sender: '.$datas['data']['mail']['from'].' &lt;'.$datas['data']['mail']['frommail'].'@'.str_replace("www.","",$_SERVER['HTTP_HOST']).'&gt;</div><div>Recipient: '.$datas['data']['mail']['subject'].' &lt;'.$datas['data']['mail']['tomail'].'&gt;</div><div>Client: '.$datas['data']['mail']['to'].' &lt;'.$datas['data']['mail']['sendermail'].'&gt;</div><div>'.$clientcopy.'</div><div><strong>Message</strong><br>'.$datas['data']['mail']['message'].'</div>'.$autoreply.'</div></div></div>';
						}
					}
				die($result);
				}
				else {
					die( 'empty' );
				}
			}
			if ( $cfscmd == 'sendmail' ) {
			if ( isset( $_REQUEST[ 'cmode' ] ) ) {
				$cmode = strip_tags($_REQUEST['cmode']);
			}
			else {
				$cmode = '';
			}
			if ( isset( $_REQUEST[ 'captcha' ] ) ) {
				$captcha = strip_tags( $_REQUEST[ 'captcha' ] );
			}
			else {
				$captcha = '';
			}
			if ( isset( $_REQUEST[ 'remail' ] ) ) {
				$remail = str_replace( "|", "@", strip_tags( $_REQUEST[ 'remail' ] ) );
			}
			else {
				$remail = '';
			}
			if ( isset( $_REQUEST[ 'semail' ] ) ) {
				$semail = strip_tags( $_REQUEST[ 'semail' ] );
			}
			else {
				$semail = '';
			}
			if ( isset( $_REQUEST[ 'name' ] ) ) {
				$name = strip_tags( $_REQUEST[ 'name' ] );
			}
			else {
				$name = '';
			}
			if ( isset( $_REQUEST[ 'subject' ] ) ) {
				$subject = strip_tags( $_REQUEST[ 'subject' ] );
			}
			else {
				$subject = '';
			}
			if ( isset( $_REQUEST[ 'message' ] ) ) {
				$message = 'Message sent from ' . $_SERVER[ 'HTTP_REFERER' ] . '<br><br>' . strip_tags( $_REQUEST[ 'message' ] );
			}
			else {
				$message = '';
			}
			if ( isset( $_REQUEST[ 'sendc' ] ) ) {
				$sendc = strip_tags( $_REQUEST[ 'sendc' ] );
			}
			else {
				$sendc = '';
			}
				if ( $cmode == "image" ) {
					if ( isset( $_COOKIE[ 'cfs-cap' ] ) ) {
						if ( md5( $captcha ) != $_COOKIE[ 'cfs-cap' ] ) {
							die("captcha");
						}
					}
				}
				add_filter( 'wp_mail_content_type', array( &$this, 'set_html_content_type' ) );
				$headers[] = 'MIME-Version: 1.0' . '\r\n';
				$headers[] = 'From: ' . get_option( 'setting_sendername' ) . ' <' . get_option( 'setting_sendermail' ) . '@' . str_replace( "www.", "", $_SERVER[ 'HTTP_HOST' ] ) . '>';
				$headers[] = 'Reply-To: ' . $name . ' <' . $semail . '>';
				if ( $sendc == "true" ) {
					$headers[] = 'Cc: ' . $name . ' <' . $semail . '>';
				}
				
				$cfs_ctc_params = $this->get_parameters();
				if ( isset( $cfs_ctc_params[ 'contacts' ] ) ) {
					foreach( $cfs_ctc_params[ 'contacts' ] as $key => $pc ) {
						if ( isset( $pc->status ) ) {
							if ( $pc->status == 0 && $key > 0 ) {
								unset( $cfs_ctc_params[ 'contacts' ][ $key ] );
								$cfs_ctc_params[ 'contacts' ] = array_values( $cfs_ctc_params[ 'contacts' ] );
							}
						}
						unset( $cfs_ctc_params[ 'contacts' ][ $key ]->arsendername );
						unset( $cfs_ctc_params[ 'contacts' ][ $key ]->arsenderemail );
						unset( $cfs_ctc_params[ 'contacts' ][ $key ]->arsendermessage );
					}
					foreach( $cfs_ctc_params[ 'contacts' ] as $key => $pc ) {
						if ( $pc->name == $remail ) {
							$remail = $pc->email;
						}
					}
				}
				$multiple_rec = explode( ",", $remail );
				if ( count( $multiple_rec ) == 1 ) {
					$remail = $multiple_rec[ 0 ];
				}
				else {
					$remail = $multiple_rec[ 0 ];
					foreach( $multiple_rec as $key=>$mr ) {
						if ( $key > 0 ) {
							$headers[] = 'Cc: ' . $name . ' <' . trim( $mr ) . '>';							
						}
					}
				}
				
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$message = __( 'Sender: ') . ' ' . $name.'  &lt;' . $semail . '&gt;<br /><br />' . nl2br( $message ) . '
				
				';
				if ( isset( $_REQUEST[ 'customfieldsarray' ] ) ) {
							if (!empty($_REQUEST['customfieldsarray'] ) ) {
								foreach( $_REQUEST[ 'customfieldsarray' ] as $cfa ) {
									if ( isset( $_REQUEST[ $cfa ] ) ) {
										if ( $_REQUEST[ $cfa ] == "true" ) {
											$cfval = "checked";
										}
										elseif ( $_REQUEST[ $cfa ] == "" ) {
											$cfval = "empty";
										}
										else {
											$cfval = $_REQUEST[ $cfa ];
										}
									}
									else {
											$cfval = "not specified";										
									}
						$message .='
'.$cfa.': '.$cfval; 
								}
							$log[ 'data' ][ 'mail' ][ 'customfields' ] = $_REQUEST[ 'customfieldsarray' ];
							}
						}
						else $mv = '';
						$message .='
						
IP Address: ' . $_SERVER[ 'REMOTE_ADDR' ] . '				
Date: ' . date( "d-m-Y H:i" ).'

'; 
				if ( wp_mail( $remail, $subject, nl2br( $message ), $headers ) ) {
					$status = "success";
				}
				else {
					$status = "failed";
				}
				$log[ 'data' ][ 'mail' ][ 'status' ] = $status;
				$log[ 'data' ][ 'mail' ][ 'from' ] = get_option( 'setting_sendername' );
				$log[ 'data' ][ 'mail' ][ 'frommail' ] = get_option( 'setting_sendermail' );
				$log[ 'data' ][ 'mail' ][ 'to' ] = $name;
				$log[ 'data' ][ 'mail' ][ 'sendermail' ] = $semail;
				$log[ 'data' ][ 'mail' ][ 'tomail' ] = $remail;
				$log[ 'data' ][ 'mail' ][ 'subject' ] = $subject;
				$log[ 'data' ][ 'mail' ][ 'message' ] = nl2br( $message );
				$log[ 'data' ][ 'mail' ][ 'copy' ] = $sendc;
				if ( $status == "success" ) {
					$contacts = json_decode( stripslashes( get_option( 'setting_contacts' ) ) );
					$headers = array();
					foreach( $contacts as $key=>$pc ) {
						$multiple_arrec = explode( ",", $pc->email );
						if ( count( $multiple_arrec ) == 1 ) {
							$arremail = $multiple_arrec[ 0 ];
						}
						else {
							$arremail = $multiple_arrec[ 0 ];
						}
						if ( $arremail == $remail && ( $pc->name == $subject || $pc->name == "" ) ) {
							if ( ! empty( $pc->arsendername ) && ! empty( $pc->arsenderemail ) && ! empty( $pc->arsendermessage ) ) {
								$headers[] = 'MIME-Version: 1.0' . '\r\n';
								$headers[] = 'From: '.$pc->arsendername.' <'.$pc->arsenderemail.'@'.str_replace("www.","",$_SERVER['HTTP_HOST']).'>';
								$headers[] = 'Content-Type: text/html; charset=UTF-8';
								$rmessage = str_replace('{name}',$name,nl2br($pc->arsendermessage));
								$rmessage = str_replace('{message}',$message,$rmessage);
								$rmessage = str_replace('{subject}',$subject,$rmessage);
								$rmessage = str_replace('{email}',$semail,$rmessage);
								if (wp_mail( $semail, 'AutoReply: '.$subject, $rmessage, $headers )) $status = "success";
								else $status = "failed";
								$log[ 'data' ][ 'autoreply' ][ 'status' ] = $status;								
								$log[ 'data' ][ 'autoreply' ][ 'type' ] = 'custom';								
								$log[ 'data' ][ 'autoreply' ][ 'rmessage' ] = nl2br( $rmessage );
								$log[ 'data' ][ 'autoreply' ][ 'from' ] = $pc->arsendername;
								$log[ 'data' ][ 'autoreply' ][ 'frommail' ] = $pc->arsenderemail;
								$log[ 'data' ][ 'autoreply' ][ 'type' ] = 'custom';
							}
							elseif ( get_option( 'setting_global_autoreply' ) == "on" ) {
								$globalarsendername = get_option( 'setting_global_arsendername' );
								$globalarsenderemail = get_option( 'setting_global_arsenderemail' );
								$globalarsendermessage = get_option( 'setting_global_arsendermessage' );
								if ( ! empty( $globalarsendername ) && ! empty( $globalarsenderemail ) && ! empty( $globalarsendermessage ) ) {
									$headers[] = 'MIME-Version: 1.0' . '\r\n';
									$headers[] = 'From: ' . $globalarsendername . ' <' . $globalarsenderemail . '@' . str_replace( "www.", "", $_SERVER[ 'HTTP_HOST' ] ) . '>';
									$headers[] = 'Content-Type: text/html; charset=UTF-8';
									$rmessage = str_replace( '{name}', $name, nl2br( $globalarsendermessage ) );
									$rmessage = str_replace( '{message}', $message, $rmessage );
									$rmessage = str_replace( '{subject}', $subject, $rmessage );
									$rmessage = str_replace( '{email}', $semail, $rmessage );
									if (wp_mail( $semail, 'AutoReply: '.$subject, $rmessage, $headers ) ) {
										$status = "success";
									}
									else {
										$status = "failed";
									}
									$log[ 'data' ][ 'autoreply' ][ 'status' ] = $status;								
									$log[ 'data' ][ 'autoreply' ][ 'type' ] = 'global';								
									$log[ 'data' ][ 'autoreply' ][ 'rmessage' ] = nl2br( $rmessage );
									$log[ 'data' ][ 'autoreply' ][ 'from' ] = $globalarsendername;
									$log[ 'data' ][ 'autoreply' ][ 'frommail' ] = $globalarsenderemail;
									$log[ 'data' ][ 'autoreply' ][ 'type' ] = 'custom';
								}
							}
						}
					}
				}
				if ( get_option( 'setting_enable_logs' ) == "on" ) {
					$this->save_log( '1', serialize( $log ) );
				}
				// Reset content-type to avoid conflicts
				remove_filter( 'wp_mail_content_type', array( &$this, 'set_html_content_type' ) );
				die( $status );
			}
		}

		function set_html_content_type() {
			return 'text/html';
		}
		function enqueue_custom_scripts_and_styles() {
			$params = $this->get_parameters();
			if ( isset( $params[ 'contacts' ] ) ) {
				foreach( $params[ 'contacts' ] as $key => $pc ) {
					if ( isset( $pc->status ) ) {
						if ( $pc->status == 0 && $key > 0 ) {
							unset( $params[ 'contacts' ][ $key ] );
							$params[ 'contacts' ] = array_values( $params[ 'contacts' ] );
						}
					}
					unset( $params[ 'contacts' ][ $key ]->arsendername );
					unset( $params[ 'contacts' ][ $key ]->arsenderemail );
					unset( $params[ 'contacts' ][ $key ]->arsendermessage );
				}
			}
			$custom_fields = json_decode( stripslashes( get_option( 'cfs-custom-fields' ) ) );
			if ( $custom_fields == NULL ) {
				$custom_fields = "";
			}
			wp_enqueue_style( 'contact_form_slider_style', plugins_url( '/templates/assets/css/cfs.css' , __FILE__ ) );
			wp_enqueue_style( 'jquery_ui_style', plugins_url( '/templates/assets/css/jquery-ui.css' , __FILE__ ) );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core', array( 'jquery' ) );
			wp_enqueue_script( 'jquery-effects-core', array( 'jquery' ) );
			wp_enqueue_script( 'jquery-effects-fade', array( 'jquery-effects-core' ) );
			wp_enqueue_script( 'jquery-effects-slide', array( 'jquery-effects-core' ) );
			wp_enqueue_script( 'jquery-effects-shake', array( 'jquery-effects-core' ) );
			wp_enqueue_script( 'jquerymousewheel', plugins_url( '/templates/assets/js/jquery.mousewheel.js', __FILE__ ), array( 'jquery' ), W8CONTACT_FORM_VERSION, false );
			wp_enqueue_script( 'jscrollpane', plugins_url( '/templates/assets/js/jquery.jscrollpane.min.js', __FILE__ ), array( 'jquery' ), W8CONTACT_FORM_VERSION, false );
			wp_register_script( "contact_form_slider_script", plugins_url('/templates/assets/js/cfs.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-effects-core', 'jquery-effects-fade', 'jquery-effects-slide', 'jquery-effects-shake' ), W8CONTACT_FORM_VERSION, false );
			wp_localize_script( 'contact_form_slider_script', 'cfs_params', array( 'customfields'=>$custom_fields, 'direction'=>$params['direction'], 'pfontweight'=>$params['pfontweight'], 'headerfontweight'=>$params['headerfontweight'], 'subheaderfontweight'=>$params['subheaderfontweight'], 'buttonfontweight'=>$params['buttonfontweight'], 'fieldfontweight'=>$params['fieldfontweight'], 'background'=>$params['background'], 'button_background'=>$params['button_background'], 'button_background_hover'=>$params['button_background_hover'], 'defaultcolor'=>$params['defaultcolor'], 'buttoncolor'=>$params['buttoncolor'], 'closeable'=>$params['closeable'], 'transparency'=>$params['transparency'], 'hide_icon'=>$params['hide_icon'], 'icon_size'=>$params['icon_size'], 'auto_open'=>$params['auto_open'], 'captcha'=>$params['captcha'], 'sendcopy'=>$params['sendcopy'], 'disableimage'=>$params['disableimage'], 'lock_screen'=>$params['lock_screen'], 'dofsu'=>$params['dofsu'], 'dom'=>$params['dom'], 'height'=>$params['height'], 'vertical_distance'=>str_replace("%","",$params['vertical_distance']), 'scheme' => $params['scheme'], 'skin' => $params['skin'], 'shake' => $params['shake'], 'icon_url' => $params['icon_url'],'customcontact'=>$params['contacts'],'placeholder_name'=>$params['placeholder_name'],'placeholder_email'=>$params['placeholder_email'],'placeholder_message'=>$params['placeholder_message'],'placeholder_captcha'=>$params['placeholder_captcha'],'placeholder_sendcopy'=>$params['placeholder_sendcopy'],'sendbutton_text'=>$params['sendbutton_text'],'success_message'=>$params['success_message'],'failed_text'=>$params['failed_text'],'reverse_header'=>$params['reverse_header'],'bordered_photo'=>$params['photoborder'],'bodyanim'=>$params['bganim'],'bgtarget'=>$params['bgtarget'],'icon_image'=>$params['icon_image'],'excludeelements'=>$params['excludeelements'],'photo_style'=>$params['photostyle'],'fontfamily'=>$params['fontfamily'],'pfontsize'=>$params['pfontsize'],'headerfontsize'=>$params['headerfontsize'],'subheaderfontsize'=>$params['subheaderfontsize'],'buttonfontsize'=>$params['buttonfontsize'],'fieldfontsize'=>$params['fieldfontsize'],'animationtype'=>$params['animation'],"plugin_directory"=>plugins_url('',__FILE__),"path"=>admin_url( 'admin-ajax.php')));
			wp_enqueue_script( 'contact_form_slider_script' );
			$custom_css = get_option( 'setting_w8cfs_customcss' );
			if ( $custom_css != ""  ) {
				wp_enqueue_style( 'w8cf-custom-style', plugins_url( '/templates/assets/css/custom_cfs.css' , __FILE__ ) );
				wp_add_inline_style( 'w8cf-custom-style', $custom_css );
			}
		}
		/**
		* Add the settings link to the plugins page
		**/
		function plugin_settings_link( $links ) {
			$settings_link = '<a href="options-general.php?page=contact_form_slider">Settings</a>';
			array_unshift( $links, $settings_link ); 
			return $links; 
		}
	}
}
if ( class_exists( 'contact_form_slider' ) ) {
	// call the main class
	$contact_form_slider = contact_form_slider::getInstance();
}
?>