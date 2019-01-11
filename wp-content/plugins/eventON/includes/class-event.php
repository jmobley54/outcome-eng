<?php
/**
 * Event Class for one event
 * @version 2.4.10
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_this_event{
	public $event_id;
	public function __construct($event_id, $event_pmv=''){
		$this->event_id = $event_id;
		$this->pmv = !empty($event_pmv)? $event_pmv: get_post_custom($event_id);
	}

	// permalinks
		function get_permalink($ri=0){
			$event_link = get_the_permalink($this->event_id);
			if($ri==0) return $event_link;

			return strpos($event_link, '?')=== false? $event_link.'?ri='.$ri: $event_link.'&ri='.$ri;
		}

	// time and date related
		function is_current_event($ri=0, $cutoff='end'){
			date_default_timezone_set('UTC');	
			$current_time = current_time('timestamp');

			$evodate = new evo_datetime();
			$event_time = $evodate->get_int_correct_event_time($this->pmv,$ri,$cutoff);
			return $event_time>$current_time? true: false;
		}
	// repeating events
		function is_repeating_event(){
			if(!$this->check_yn('evcal_repeat')) return false;
			if(empty($this->pmv['repeat_intervals'])) return false;

			return true;
		}
		function get_repeats(){
			if(empty($this->pmv['repeat_intervals'])) return false;
			return unserialize($this->pmv['repeat_intervals'][0]);
		}
		function get_next_current_repeat($current_ri_index){
			$repeats = $this->get_repeats();

			if(!$repeats) return false;
			
			foreach($repeats as $index=>$repeat){
				if($index<= $current_ri_index) continue;

				if($this->is_current_event($index)) return array('ri'=>$index, 'times'=>$repeat);			
			}

			return false;
		}

	// event post meta values
		function get_prop($field){
			if(empty($this->pmv[$field])) return false;
			return $this->pmv[$field][0];
		}

		function check_yn($field){
			if(empty($this->pmv[$field])) return false;

			if($this->pmv[$field][0]=='yes') return true;
			return false;
		}

	// Location data for an event
		public function get_location_data(){
			$event_id = $this->event_id;
			$location_terms = wp_get_post_terms($event_id, 'event_location');

			if ( $location_terms && ! is_wp_error( $location_terms ) ){

				$output = array();

				$evo_location_tax_id =  $location_terms[0]->term_id;
				$event_tax_meta_options = get_option( "evo_tax_meta");
				
				// check location term meta values on new and old
				$LocTermMeta = evo_get_term_meta( 'event_location', $evo_location_tax_id, $event_tax_meta_options);
				
				// location name
					$output['name'] = stripslashes( $location_terms[0]->name );

				// description
					if(!empty($location_terms[0]->description))
						$output['description'] = $location_terms[0]->description;

				// meta values
				foreach(array(
					'location_address','location_lat','location_lon','evo_loc_img'
				) as $key){
					if(empty($LocTermMeta[$key])) continue;
					$output[$key] = $LocTermMeta[$key];
				}
				
				return $output;
				
			}else{
				return false;
			}
		}

}