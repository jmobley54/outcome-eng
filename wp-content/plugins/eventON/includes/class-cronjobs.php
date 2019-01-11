<?php
/**
 * Handle general Cron functions for eventon and its addons
 * @since  2.5.5
 */

class evo_cron{

	// return the cron data for a cron hook
		function get_cron_data($cron_hook){
			$crons = get_option('cron');

			if(!is_array($crons)) return false;

			$cron_job = array();

			foreach($crons as $time=>$cron){
				if(!is_array($cron)) continue;
				foreach ( $cron as $hook => $dings ) {

					if($hook != $cron_hook) continue;

					foreach ( $dings as $sig => $data ) {

						$cron_job = array(
							'time'=>$time,
							'sig'=>$sig,
							'schedule'=>(!empty($data['schedule'])? $data['schedule']:''),
							'interval'=>(!empty($data['interval'])? $data['interval']:'')
						);

					}
				}
			}

			return $cron_job;
	 	}

	// next_run
		function next_run($hookname){
			$crons = _get_cron_array();

			if($crons){
				foreach($crons as $time =>$cron){
					foreach($cron as $hook=>$dings){
						if($hook == $hookname) return $time;
					}
				}
			}

		}

	// perform a cron manually
	 	function run_cron($hookname, $sig){
	 		$crons = _get_cron_array();
			foreach ( $crons as $time => $cron ) {
				// for matching cron hook
				if ( isset( $cron[ $hookname ][ $sig ] ) ) {
					$args = $cron[ $hookname ][ $sig ]['args'];
					delete_transient( 'doing_cron' );
					wp_schedule_single_event( time() - 1, $hookname, $args );
					spawn_cron();
					return true;
				}
			}
			return false;

	 	}
	// cron log creation
		function record_log($data, $key){
			$logs = get_option('evo_cron_logs');

			$logs = !empty($logs)? $logs: array();

			$logs[$key][] = $data;
			update_option('evo_cron_logs', $logs);
		}
		function get_log($key){
			$logs = get_option('evo_cron_logs');
			if(empty($logs[$key])) return false;
			return $logs[$key];
		}
}