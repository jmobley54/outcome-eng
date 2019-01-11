<?php
/*
 Plugin Name: EventON - Event Lists
 Plugin URI: http://www.myeventon.com/
 Description: Create past and upcoming event lists for eventON
 Author: Ashan Jay
 Version: 0.8
 Author URI: http://www.ashanjay.com/
 Requires at least: 4.0
 Tested up to: 4.7.1
 */
 
class eventon_event_lists{
	
	public $version='0.8';
	public $eventon_version = '2.5';
	public $name = 'EventLists';
		
	public $is_running_el =false;
	
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	public $template_url ;
		
	/* Construct	 */
		public function __construct(){			
			$this->super_init();

			include_once( 'admin/class-admin_check.php' );
			$this->check = new addon_check($this->addon_data);
			$check = $this->check->initial_check();
			
			if($check){
				$this->addon = new evo_addon($this->addon_data);			
				add_action( 'init', array( $this, 'init' ), 0 );
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

	        $this->plugin_url = $this->addon_data['plugin_url'];
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
		}

	// INITIATE please
		function init(){

			include_once( 'includes/class-frontend.php' );
			include_once( 'includes/class-shortcode.php' );
			$this->frontend = new evoel_frontend();
			$this->shortcodes = new evo_el_shortcode();	
					
			// Activation
			$this->activate();		
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			// RUN addon updater only in dedicated pages
			if ( is_admin() )	$this->addon->updater();
		}

	// SECONDARY FUNCTIONS
		function print_scripts(){$this->frontend->print_scripts();}
		function activate(){
			$this->addon->activate();
		}	   		
		function deactivate(){
			$this->addon->remove_addon();
		}	
}

// Initiate this addon within the plugin
$GLOBALS['eventon_el'] = new eventon_event_lists();

// php tag
	function add_eventon_el($args='') {
		global $eventon_el, $eventon;
		
		/*
		// connect to support arguments
		$supported_defaults = $eventon->evo_generator->get_supported_shortcode_atts();		
		$args = shortcode_atts( $supported_defaults, $args ) ;
		*/
		
		$content = $eventon_el->frontend->generate_eventon_el_calendar($args, 'php');		
		echo $content;
	}