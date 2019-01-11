<?php
/*
 Plugin Name: EventON - Lists and Items
 Plugin URI: http://www.myeventon.com/addons/event-lists-items
 Description: Create custom eventON category lists and item boxes
 Author: Ashan Jay
 Version: 0.4
 Author URI: http://www.ashanjay.com/
 Requires at least: 4.0
 Tested up to: 4.7.2
 */

class EVO_lists{
	
	public $version='0.4';
	public $eventon_version = '2.5';
	public $name = 'Event Lists & Items';
	public $id = 'EVOLI';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	public $template_url ;
	
	// construct
		public function __construct(){
			$this->super_init();

			include_once( 'includes/admin/class-admin_check.php' );
			$this->check = new addon_check($this->addon_data);
			$check = $this->check->initial_check();
			
			if($check){
				$this->addon = new evo_addon($this->addon_data);
				$this->helper = new evo_helper();

				$this->opt2 = get_option('evcal_options_evcal_2');
				add_action( 'init', array( $this, 'init' ), 0 );

				// settings link in plugins page
				// add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));
			}			
		}
	
	// SUPER init
		function super_init(){
			// PLUGIN SLUGS			
			$this->addon_data['plugin_url'] = path_join(WP_PLUGIN_URL, basename(dirname(__FILE__)));
			$this->addon_data['plugin_slug'] = plugin_basename(__FILE__);
			list ($t1, $t2) = explode('/', $this->addon_data['plugin_slug'] );
	        $this->addon_data['slug'] = $t1;
	        $this->addon_data['plugin_path'] = dirname( __FILE__ );
	        $this->addon_data['evo_version'] = $this->eventon_version;
	        $this->addon_data['version'] = $this->version;
	        $this->addon_data['name'] = $this->name;

	        // guide file
	        	$this->addon_data['guide_file'] = ( file_exists($this->addon_data['plugin_path'].'/guide.php') )? 
								$this->addon_data['plugin_url'].'/guide.php':null;

	        $this->plugin_url = $this->addon_data['plugin_url'];
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
	        $this->assets_path = str_replace(array('http:','https:'), '',$this->addon_data['plugin_url']).'/assets/';	        
		}

	// INITIATE please
		function init(){				
			// Activation
			$this->activate();		
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			include_once( 'includes/class-shortcode.php' );
			$this->shortcodes = new evoli_shortcode();

			include_once( 'includes/class-frontend.php' );
			$this->frontend = new evoli_front();
			
			if ( defined('DOING_AJAX') ){
				include_once( 'includes/class-ajax.php' );
			}
			if ( is_admin() ){
				$this->addon->updater();	
				include_once( 'includes/admin/admin-init.php' );
			}
		}

	// SECONDARY FUNCTIONS	
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_li">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		// ACTIVATION			
			function activate(){
				// add actionUser addon to eventon addons list
				$this->addon->activate();
			}
		
			// Deactivate addon
			function deactivate(){
				$this->addon->remove_addon();
			}
		// duplicate language function to make it easy on the eye
			function lang($variable, $default_text, $lang=''){
				return eventon_get_custom_language($this->opt2, $variable, $default_text, $lang);
			}
}
// Initiate this addon within the plugin
$GLOBALS['eventon_li'] = new EVO_lists();
?>