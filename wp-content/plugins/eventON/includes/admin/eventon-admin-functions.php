<?php
/**
 * EventON Admin Functions
 *
 * Hooked-in functions for EventON related events in admin.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin
 * @version     1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Prevent non-admin access to backend */
	function eventon_prevent_admin_access() {
		if ( get_option('eventon_lock_down_admin') == 'yes' && ! is_ajax() && ! ( current_user_can('edit_posts') || current_user_can('manage_eventon') ) ) {
			//wp_safe_redirect(get_permalink(woocommerce_get_page_id('myaccount')));
			exit;
		}
	}

/* eventON shortcode generator button for WYSIWYG editor */
//add_action('init', 'eventon_shortcode_button_init');
	 function eventon_shortcode_button_init() {

	 	global $pagenow, $typenow, $post;	
		
		if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	        $post = get_post( $_GET['post'] );
	        $typenow = (!empty($post) )? $post->post_type : '';
	    }
		
		if ( $typenow == '' || $typenow == "ajde_events" ) return;
		

	      //Abort early if the user will never see TinyMCE
	      if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
	           return;

	      //Add a callback to regiser our tinymce plugin   
	      add_filter("mce_external_plugins", "eventon_register_tinymce_plugin"); 

	      // Add a callback to add our button to the TinyMCE toolbar
	      add_filter('mce_buttons', 'eventon_add_tinymce_button');
	}
	//This callback registers our plug-in
	function eventon_register_tinymce_plugin($plugin_array) {
	    $plugin_array['eventon_shortcode_button'] = AJDE_EVCAL_URL.'/assets/js/admin/shortcode.js';
	    return $plugin_array;
	}

	//This callback adds our button to the toolbar
	function eventon_add_tinymce_button($buttons) {
	            //Add the button ID to the $button array
	    $buttons[] = "eventon_shortcode_button";
	    return $buttons;
	}

// @since 2.2.24
// check if repeat post data are good to go
	function eventon_is_good_repeat_data(){
		return ( isset($_POST['evcal_rep_freq'])
			&& isset($_POST['evcal_repeat']) 
			&& $_POST['evcal_repeat']=='yes')? 	true: false;
	}


// SAVE: closed meta field boxes
	function eventon_save_collapse_metaboxes( $page, $post_value ) {
		
		if(empty($post_value)) return;
		
		$user_id = get_current_user_id();
		$option_name = 'closedmetaboxes_' . $page; // use the "pagehook" ID
		
		$meta_box_ids = array_unique(array_filter(explode(',',$post_value)));
		
		$meta_box_id_ar =serialize($meta_box_ids);
		
		update_user_option( $user_id, $option_name,  $meta_box_id_ar , true );
		
	}

	function eventon_get_collapse_metaboxes($page){
		
		$user_id = get_current_user_id();
	    $option_name = 'closedmetaboxes_' . $page; // use the "pagehook" ID
		$option_arr = get_user_option( $option_name, $user_id );
		
		if(empty($option_arr)) return;
		
		return unserialize($option_arr);
		//return ($option_arr);		
	}

// added in version 2.2.21
	// check if curl installed
		function evo_curl_installed(){
			return (function_exists('curl_exec'))? true: false;
		}
	// get file content using curl
		function evo_file_get_contents_curl($url) {
		    $ch = curl_init();

		    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

		    $data = curl_exec($ch);
		    curl_close($ch);

		    return $data;
		}
	// test wp_remote_post() call to json.php file
		function evo_wp_remote_test($protocol){
			if($protocol=='post'){
				$request_string = array(
					'body' => array(
						'action' => 'wp_remote_post', 
					),
				);		
			
				$request = wp_remote_post('http://74.62.111.12/~updates/eventon/json.php', $request_string);
				if(!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200 ){
					if(isset($request['response']) && $request['response']['code']== 200 && $request['body']=='wp_remote_post'){
						save_evoOPT('1', 'wp_remote_post','worked');
						return true;
					}else{
						save_evoOPT('1', 'wp_remote_post','didnt_work');
						return false;
					}					
				}else{	save_evoOPT('1', 'wp_remote_post','didnt_work'); return false;	}
			}else{// GET
				$url = 'http://74.62.111.12/~updates/eventon/json.php';
				$request = wp_remote_get($url);
				if (!is_wp_error($request) && $request['response']['code']===200) { 
					save_evoOPT('1', 'wp_remote_get','worked');
					return (!empty($request['body']) && $request['body']=='other_content')? true: false;
				}else{	
					save_evoOPT('1', 'wp_remote_get','didnt_work');
					return false;	}
			}			
		}

// HEX code to RGB
	function eventon_hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb[0].','.$rgb[1].','.$rgb[0]; // returns an array with the rgb values
	}

// create backend pages
	function eventon_create_page($slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ){
		global $wpdb;

		$option_value = get_option( $option );

		if ( $option_value > 0 && get_post( $option_value ) )
			return;

		$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s LIMIT 1;", $slug ) );
		if ( $page_found ) {
			if ( ! $option_value )
				update_option( $option, $page_found );
			return;
		}

		$page_data = array(
	        'post_status' 		=> 'publish',
	        'post_type' 		=> 'page',
	        'post_author' 		=> 1,
	        'post_name' 		=> $slug,
	        'post_title' 		=> $page_title,
	        'post_content' 		=> $page_content,
	        'post_parent' 		=> $post_parent,
	        'comment_status' 	=> 'closed'
	    );
	    $page_id = wp_insert_post( $page_data );
	    update_option( $option, $page_id );
	}

// save event functions
	// @version 2.4.7

	
	// Save location and organizer taxonomy values
		function evoadmin_save_event_tax_termmeta($post_id){
			$taxs = apply_filters('evo_event_tax_save_values',array(
				'event_location'=>array(
					'select'=>'evcal_location_name_select',
					'name'=>'evcal_location_name',
					'id'=>'evo_location_tax_id',
					'fields'=>array(
						'evcal_location_link',
						'location_address',
						'evo_loc_img'
					)
				),
				'event_organizer'=> array(
					'select'=>'evcal_organizer_name_select',
					'name'=>'evcal_organizer',
					'id'=>'evo_organizer_tax_id',
					'fields'=>array(
						'evcal_org_contact',
						'evcal_org_address',
						'evo_org_img',
						'evcal_org_exlink',
						'_evocal_org_exlink_target'
					)
				)
			));

			// each taxonomy
			foreach($taxs as $tax=>$data){
				$taxtermID = false;
				// tax name chosen from the list
				if(isset($_POST[ $data['select'] ]) && isset($_POST[ $data['name']  ]) && 
					$_POST[ $data['select'] ] == $_POST[ $data['name']  ]){
					if(!empty($_POST[ $data['id']  ])){
						$taxtermID = (int)$_POST[ $data['id']  ];
					}					
				}elseif(isset($_POST[ $data['name'] ])){ // create new term
					$term_name = esc_attr(stripslashes($_POST[ $data['name'] ]));
					$term = term_exists( $term_name, $tax );
					if($term !== 0 && $term !== null){
						$taxtermID = (int)$term['term_id'];
						wp_set_object_terms( $post_id, $taxtermID, $tax );
					}else{
						// create slug from location name
							$trans = array(" "=>'-', ","=>'');
							$term_slug= strtr($term_name, $trans);

						// create wp term
						$new_term_ = wp_insert_term( $term_name, $tax , array('slug'=>$term_slug) );

						if(!is_wp_error($new_term_)){
							$taxtermID = (int)$new_term_['term_id'];
						}		
					}
				}

				// update term meta and assign term to event
					if($taxtermID){
						$term_meta = array();
						foreach( $data['fields'] as $field){
							do_action('evo_tax_save_each_field', $field);
							if($field=='location_address'){
								if(isset($_POST['evcal_location']))
									$latlon = eventon_get_latlon_from_address($_POST['evcal_location']);

								// longitude
								$term_meta['location_lon'] = (!empty($_POST['evcal_lon']))?$_POST['evcal_lon']:
									(!empty($latlon['lng'])? floatval($latlon['lng']): null);

								// latitude
								$term_meta['location_lat'] = (!empty($_POST['evcal_lat']))?$_POST['evcal_lat']:
									(!empty($latlon['lat'])? floatval($latlon['lat']): null);

								$term_meta['location_address' ] = (isset($_POST[ 'evcal_location' ]))?$_POST[ 'evcal_location' ]:null;

								continue;
							}
							$term_meta[ $field ] = (isset($_POST[ $field ]))?
								str_replace('"', "'", $_POST[ $field ]):null; 
						}

						// save meta values
							evo_save_term_metas($tax, $taxtermID, $term_meta);
						// assign term to event & replace
							wp_set_object_terms( $post_id, $taxtermID, $tax , false);	
					}
			} // endforeach

		}

// get converted unix time for saving event date time using $_POST
	function evoadmin_get_unix_time_fromt_post($post_id){
		// field names that pertains only to event date information
			$fields_sub_ar = apply_filters('eventon_event_date_metafields', array(
				'evcal_start_date',
				'evcal_end_date', 
				'evcal_start_time_hour',
				'evcal_start_time_min',
				'evcal_st_ampm',
				'evcal_end_time_hour',
				'evcal_end_time_min',
				'evcal_et_ampm',
				'evcal_allday'
				)
			);

		// DATE and TIME data
			$date_POST_values = array();
			foreach($fields_sub_ar as $ff){
				
				if(empty($_POST[$ff])) continue;
				$date_POST_values[$ff]=$_POST[$ff];

				// remove these values from previously saved
				delete_post_meta($post_id, $ff);
			}

		// hide end time filtering of data values
			if( !empty($_POST['evo_hide_endtime']) && $_POST['evo_hide_endtime']=='yes'){

				if(evo_settings_check_yn($_POST,'evo_span_hidden_end')){
					$date_POST_values['evcal_end_date']=$_POST['evcal_end_date'];
				}else{
					$date_POST_values['evcal_end_date']=$_POST['evcal_start_date'];
					$date_POST_values['evcal_end_time_hour'] = '11';
					$date_POST_values['evcal_end_time_min'] = '50';
					$date_POST_values['evcal_et_ampm'] = 'pm';
				}				
			}
		
		// convert the post times into proper unix time stamps
			$date_format = !empty($_POST['_evo_date_format']) ? $_POST['_evo_date_format']: get_option('date_format');
			$time_format = !empty($_POST['_evo_time_format']) ? $_POST['_evo_time_format']: get_option('time_format');

			return eventon_get_unix_time($date_POST_values, $date_format, $time_format);
	}
?>