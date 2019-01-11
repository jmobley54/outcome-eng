<?php
/**
 * EventON Event lists shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-EL/Functions/shortcode
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_el_shortcode{
	
	function __construct(){
		add_shortcode('add_eventon_el', array($this,'evoEL_generate_calendar'));
		add_filter('eventon_shortcode_popup',array($this,'evoEL_add_shortcode_options'), 10, 1);
		add_filter('eventon_calhead_shortcode_args', array($this, 'calhead_args'), 10, 2);
		add_filter('eventon_shortcode_defaults',array($this,  'evoEL_add_shortcode_defaults'), 10, 1);		
	}


	/**	Shortcode processing */	
		function evoEL_generate_calendar($atts){
			global $eventon_el, $eventon;

			// add el scripts to footer
			add_action('wp_footer', array($eventon_el, 'print_scripts'));
						
			// /print_r($atts);
			// connect to support arguments
			$supported_defaults = $eventon->evo_generator->get_supported_shortcode_atts();
			//print_r($supported_defaults);
			
			$args = shortcode_atts( $supported_defaults, $atts ) ;			
			
			ob_start();				
				echo $eventon_el->frontend->generate_eventon_el_calendar($args);			
			return ob_get_clean();
					
		}

	// add new default shortcode arguments
		function evoEL_add_shortcode_defaults($arr){			
			return array_merge($arr, array(
				//'mobreaks'=>'no',
				'el_type'=>'ue',
				'el_title'=>'',
				'sep_month'=>'no', 		// separate events by months
			));			
		}
	// shortcode arguments to calendar header
		function calhead_args($array, $arg=''){
			if(!empty($arg['sep_month']))
				$array['sep_month'] = $arg['sep_month'];
			if(!empty($arg['number_of_months']))
				$array['number_of_months'] = $arg['number_of_months'];
			return $array;
		}

	/*	ADD shortcode buttons to eventON shortcode popup	*/
		function evoEL_add_shortcode_options($shortcode_array){
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_el',
					'name'=>'Event Lists: Extended',
					'code'=>'add_eventon_el',
					'variables'=>array(
						array(
							'name'=>'Custom Calendar title',
							'type'=>'text',
							'guide'=>'You can add custom calendar title for event list calendar in here',
							'var'=>'el_title',	
						),array(
							'name'=>'Select Event List Type',
							'type'=>'select',
							'guide'=>'Type of event list you want to show.',
							'var'=>'el_type',
							'options'=>array(
								'ue'=>'Upcoming Events',
								'pe'=>'Past Events'
							)
						)
							
						,array(
							'name'=>'Event Cut-off',
							'type'=>'select_step',
							'guide'=>'Past or upcoming events cut-off time. This will allow you to override past event cut-off settings for calendar events. Current date = today at 12:00am',
							'var'=>'pec',
							'default'=>'Current Time',
							'options'=>array( 
								'ct'=>'Current Time: '.date('m/j/Y g:i a', current_time('timestamp')),
								'cd'=>'Current Date: '.date('m/j/Y', current_time('timestamp')),
								'ft'=>'Fixed Time'
							)
						)
						
							,array(
								'type'=>'open_select_steps','id'=>'ct'
							)
							,array(	'type'=>'close_select_step')
							,array(
								'type'=>'open_select_steps','id'=>'cd'
							)
							,array(	'type'=>'close_select_step')
							,array(
								'type'=>'open_select_steps','id'=>'ft'
							)
								,$evo_shortcode_box->shortcode_default_field('fixed_d_m_y')
								
							,array(	'type'=>'close_select_step')

						,array(
							'name'=>'Number of Months',
							'type'=>'text',
							'var'=>'number_of_months',
							'default'=>'0',
							'guide'=>'If number of month is not provided, by default it will get events from one month either back or forward of current month',
							'placeholder'=>'eg. 5'
						),
						array(
							'name'=>'Event Count Limit',
							'placeholder'=>'eg. 3',
							'type'=>'text',
							'guide'=>'Limit number of events displayed in the list eg. 3',
							'var'=>'event_count',
							'default'=>'0'
						),
						array(
							'name'=>'Separate events by month',
							'type'=>'YN',
							'guide'=>'This will separate events into months similar to basic event list',
							'var'=>'sep_month',
							'default'=>'no'	
						),
						$evo_shortcode_box->shortcode_default_field('event_order'),
						$evo_shortcode_box->shortcode_default_field('hide_mult_occur')				
						,
						$evo_shortcode_box->shortcode_default_field('event_type'),
						$evo_shortcode_box->shortcode_default_field('event_type_2'),
						$evo_shortcode_box->shortcode_default_field('etc_override'),
						$evo_shortcode_box->shortcode_default_field('evc_open'),
						$evo_shortcode_box->shortcode_default_field('hide_sortO'),
						$evo_shortcode_box->shortcode_default_field('expand_sortO'),
						$evo_shortcode_box->shortcode_default_field('accord'),
						$evo_shortcode_box->shortcode_default_field('show_et_ft_img'),
						
					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}
}
?>