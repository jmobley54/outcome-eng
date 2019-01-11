<?php
/**
 * Event Lists Ext. Addon Front end
 * @version 0.8
 */
class evoel_frontend{

	public $shortcode_args;
	private $shortcode_atts = array();

	public function __construct(){
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	
		add_action('evo_addon_styles', array($this, 'styles') );
	}

	//	Styles for the tab page
		function styles(){
			global $eventon_el;
			ob_start();
			include_once($eventon_el->plugin_path.'/assets/el_styles.css');
			echo ob_get_clean();
		}

	/**
	 *	MAIN Function to generate the calendar outter shell
	 *	for event lists
	 */
		public function generate_eventon_el_calendar($args, $type=''){
			
			global $eventon;
			
			$this->only_el_actions();
				
			// CUT OFF time calculation
				//fixed time list
				if(!empty($args['pec']) && $args['pec']=='ft'){
					$__D = (!empty($args['fixed_date']))? $args['fixed_date']:date("j", current_time('timestamp'));
					$__M = (!empty($args['fixed_month']))? $args['fixed_month']:date("m", current_time('timestamp'));
					$__Y = (!empty($args['fixed_year']))? $args['fixed_year']:date("Y", current_time('timestamp'));

					$current_timestamp = mktime(0,0,0,$__M,$__D,$__Y);

				// current date cd
				}else if(!empty($args['pec']) && $args['pec']=='cd'){
					$current_timestamp = strtotime( date("m/j/Y", current_time('timestamp')) );
				}else{// current time ct
					$current_timestamp = current_time('timestamp');
				}
				// reset arguments
				$args['fixed_date']= $args['fixed_month']= $args['fixed_year']='';
			
			// restrained time unix
				$number_of_months = (!empty($args['number_of_months']))? (int)($args['number_of_months']):0;
				$month_dif = ($args['el_type']=='ue')? '+':'-';
				$unix_dif = strtotime($month_dif.($number_of_months-1).' months', $current_timestamp);

				$restrain_monthN = ($number_of_months>0)?				
					date('n',  $unix_dif):
					date('n',$current_timestamp);

				$restrain_year = ($number_of_months>0)?				
					date('Y', $unix_dif):
					date('Y',$current_timestamp);			

			// upcoming events list 
				if($args['el_type']=='ue'){
					$restrain_day = date('t', mktime(0, 0, 0, $restrain_monthN+1, 0, $restrain_year));
					$__focus_start_date_range = $current_timestamp;
					$__focus_end_date_range =  mktime(23,59,59,($restrain_monthN),$restrain_day, ($restrain_year));
								
				}else{// past events list

					if(!empty($args['event_order']))
						$args['event_order']='DESC';

					$args['hide_past']='no';
					
					$__focus_start_date_range =  mktime(0,0,0,($restrain_monthN),1, ($restrain_year));
					$__focus_end_date_range = $current_timestamp;
				}
			
			
			// Add extra arguments to shortcode arguments
			$new_arguments = array(
				'focus_start_date_range'=>$__focus_start_date_range,
				'focus_end_date_range'=>$__focus_end_date_range,
			);

			//print_r($args);
			$args = (!empty($args) && is_array($args))? 
				wp_parse_args($new_arguments, $args): $new_arguments;
			
			
			// PROCESS variables
			$args__ = $eventon->evo_generator->process_arguments($args);
			$this->shortcode_args=$args__;

			$eventon->evo_generator->events_processed = array();
			
			// ==================
			$content =$eventon->evo_generator->calendar_shell_header(
				array(
					'month'=>$restrain_monthN,'year'=>$restrain_year, 
					'date_header'=>false,
					'sort_bar'=>true,
					'date_range_start'=>$__focus_start_date_range,
					'date_range_end'=>$__focus_end_date_range,
					'title'=>$args['el_title'],
					'send_unix'=>true
				)
			);

			$content .=$eventon->evo_generator->eventon_generate_events($args__);
			
			$content .=$eventon->evo_generator->calendar_shell_footer();

			$this->remove_only_el_actions();
			
			return  $content;	
		}

	// SUPPROT FUNCTIONS
		// ONLY for el calendar actions 
		public function only_el_actions(){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);	
		}
		public function remove_only_el_actions(){
			//add_filter('eventon_cal_class', array($this, 'remove_eventon_cal_class'), 10, 1);
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));				
		}
		// add class name to calendar header for DV
		function eventon_cal_class($name){
			$name[]='evoEL';
			return $name;
		}
		// add class name to calendar header for DV
		function remove_eventon_cal_class($name){
			if(($key = array_search('evoEL', $name)) !== false) {
			    unset($name[$key]);
			}
			return $name;
		}

		/**	Styles for the tab page	 */	
			public function register_styles_scripts(){
				global $eventon_el;
				wp_register_style( 'evo_el_styles',$eventon_el->plugin_url.'/assets/el_styles.css');
				wp_register_script('evo_el_script',$eventon_el->plugin_url.'/assets/el_script.js', array('jquery'), $eventon_el->version, true );	

				if(has_eventon_shortcode('add_eventon_el')){
					// LOAD JS files
					//$this->print_scripts();						
				}
				add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));					
			}
			public function print_scripts(){					
				wp_enqueue_script('evo_el_script');	
			}
			function print_styles(){
				wp_enqueue_style( 'evo_el_styles');	
			}	
}