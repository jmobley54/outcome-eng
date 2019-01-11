<?php
/**
 * Function ajax for backend
 * @version   2.5.1
 */
class EVO_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'deactivate_lic'=>'eventon_deactivate_evo',
			'validate_license'=>'validate_license',
			'verify_key'=>'verify_key',
			'remote_validity'=>'remote_validity',
			'get_license_api_url'=>'get_license_api_url',
			'deactivate_addon'=>'deactivate_addon',			
			'export_events'=>'export_events',			
			'get_addons_list'=>'get_addons_list',
			'export_settings'=>'export_settings',
			'import_settings'=>'import_settings',
			'get_event_tax_term_section'=>'get_event_tax_term_section',
			'event_tax_list'=>'event_tax_list',
			'event_tax_save_changes'=>'event_tax_save_changes',
			'event_tax_remove'=>'event_tax_remove',
			'eventpost_update_meta'=>'evo_eventpost_update_meta',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {

			$prepend = 'eventon_';
			add_action( 'wp_ajax_'. $prepend . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'. $prepend . $ajax_event, array( $this, $class ) );
		}

		add_action('wp_ajax_eventon-feature-event', array($this, 'eventon_feature_event'));
	}

	// update event post meta
		function evo_eventpost_update_meta(){
			if(isset($_POST['eid']) && isset($_POST['values']) ){
			
				$post = array();
				foreach($_POST['values'] as $key=>$val){
					update_post_meta($_POST['eid'], $key, $val);

					do_action('eventon_saved_event_metadata', $_POST['eid'], $key, $val);
				}
				echo json_encode(array(
					'status'=>	'good',
					'msg'=>	__('Successfully saved event meta data!','eventon')
				)); exit;
			}else{
				echo 'Event ID not available!'; exit;
			}
		}

	// get event singular tax term form or list
		function get_event_tax_term_section(){
			
			echo json_encode(array(
				'status'=>'good',
				'content'=> EVO()->evo_admin->metaboxes->get_tax_form()
			)); exit;
		}

		// tax term list
		function event_tax_list(){
			$terms = get_terms(
				$_POST['tax'],
				array(
					'orderby'           => 'name', 
				    'order'             => 'ASC',
				    'hide_empty'=>false
				) 
			);

			ob_start();
			echo "<div class='evo_tax_entry' data-eventid='{$_POST['eventid']}' data-tax='{$_POST['tax']}' data-type='list'>";

			if(count($terms)>0){
				
				?><select class='field' name='event_tax_termid'><?php

				if(empty($_POST['termid'])){
					?><option value=""><?php _e('Select from the list','eventon');?></option><?php
				}

				foreach ( $terms as $term ) {
					$selected = (!empty($_POST['termid']) && $term->term_id == $_POST['termid'])? 'selected="selected"':'';
					?><option <?php echo $selected;?> value="<?php echo $term->term_id;?>"><?php echo $term->name;?></option><?php
				}
				?></select>
				<p style='text-align:center; padding-top:10px;'><span class='evo_btn evo_term_submit'><?php _e('Save Changes','eventon');?></span></p>
				<?php
			}else{
				?><p><?php _e('You do not have any items saved! Please add new!','eventon');?></p><?php
			}

			echo "</div>";

			echo json_encode(array(
				'status'=>'good',
				'content'=>ob_get_clean()
			)); exit;
		}

		// save changes
		function event_tax_save_changes(){
			$status = 'bad';
			$content = '';
			$tax = $_POST['tax'];

			switch($_POST['type']){
			case 'list':
				if(!empty($_POST['event_tax_termid'])){
					wp_set_object_terms( $_POST['eventid'], (int)$_POST['event_tax_termid'], $tax , false);
					$status = 'good';
					$content = 'Changes successfully saved!';	
				}else{
					$content = 'Term ID was not passed!';	
				}
			break;
			case 'new':
			case 'edit':
				
				$term_name = esc_attr(stripslashes($_POST[ 'term_name' ]));
				$term = term_exists( $term_name, $tax );
				if($term !== 0 && $term !== null){
					$taxtermID = (int)$term['term_id'];
					wp_set_object_terms( $_POST['eventid'], $taxtermID, $tax );
				}else{
					// create slug from term name
						$trans = array(" "=>'-', ","=>'');
						$term_slug= strtr($term_name, $trans);

					// create wp term
					$new_term_ = wp_insert_term( $term_name, $tax , array('slug'=>$term_slug) );

					if(!is_wp_error($new_term_)){
						$taxtermID = (int)$new_term_['term_id'];
					}	
				}

				$fields = EVO()->taxonomies->get_event_tax_fields_array($_POST['tax'],'');

				// if a term ID is present
				if($taxtermID){
					$term_meta = array();

					// save description
					$term_description = isset($_POST['description'])? sanitize_text_field($_POST['description']):'';
					$tt = wp_update_term($taxtermID, $tax, array( 'description'=>$term_description ));
					
					foreach($fields as $key=>$value){
						if(in_array($key, array('description', 'submit','term_name','evcal_lat','evcal_lon'))) continue;

						if(isset($_POST[$value['var']])){

							do_action('evo_tax_save_each_field',$value['var'], $_POST[$value['var']]);

							if($value['var']=='location_address'){
								if(isset($_POST['location_address']))
									$latlon = eventon_get_latlon_from_address($_POST['location_address']);

								// longitude
								$term_meta['location_lon'] = (!empty($_POST['location_lon']))?$_POST['location_lon']:
									(!empty($latlon['lng'])? floatval($latlon['lng']): null);

								// latitude
								$term_meta['location_lat'] = (!empty($_POST['location_lat']))?$_POST['location_lat']:
									(!empty($latlon['lat'])? floatval($latlon['lat']): null);

								$term_meta['location_address' ] = (isset($_POST[ 'location_address' ]))? $_POST[ 'location_address' ]:null;

								continue;
							}

							$term_meta[ $value['var'] ] = str_replace('"', "'", $_POST[$value['var']]); 

						}else{
							$term_meta[ $value['var'] ] = ''; 
						}
					}

					// save meta values
						evo_save_term_metas($tax, $taxtermID, $term_meta);
					// assign term to event & replace
						wp_set_object_terms( $_POST['eventid'], $taxtermID, $tax , false);	

					$status = 'good';
					$content = 'Changes successfully saved!';	
				}

			break;
			}

			echo json_encode(array(
				'status'=>$status,
				'content'=>$content,
				'htmldata'=> EVO()->evo_admin->metaboxes->event_edit_tax_section($tax , $_POST['eventid'] )
			)); exit;
		}
		// remove a taxonomy term
		function event_tax_remove(){
			$status = 'bad';
			$content = '';
			
			if(!empty($_POST['termid'])){
				wp_remove_object_terms( $_POST['eventid'], (int)$_POST['termid'], $_POST['tax'] , false);
				$status = 'good';
				$content = 'Changes successfully saved!';	
			}else{
				$content = 'Term ID was not passed!';	
			}

			echo json_encode(array(
				'status'=>$status,
				'content'=>$content,
				'htmldata'=> EVO()->evo_admin->metaboxes->event_edit_tax_section($_POST['tax'] , $_POST['eventid'] )
			)); exit;
		}


	// export eventon settings
		function export_settings(){
			// check if admin and loggedin
				if(!is_admin() && !is_user_logged_in()) die('User not loggedin!');

			// verify nonce
				if(!wp_verify_nonce($_REQUEST['nonce'], 'evo_export_settings')) die('Security Check Failed!');

			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=Evo_settings__".date("d-m-y").".json");
			
			$json = array();
			$evo_options = get_option('evcal_options_evcal_1');
			foreach($evo_options as $field=>$option){
				// skip fields
				if(in_array($field, array('option_page','action','_wpnonce','_wp_http_referer'))) continue;
				$json[$field] = $option;
			}

			echo json_encode($json);
			exit;
		}
	// import settings
		function import_settings(){
			$output = array('status'=>'','msg'=>'');
			// verify nonce
				$output['success'] =wp_create_nonce('eventon_admin_nonce');
				if(!wp_verify_nonce($_POST['nonce'], 'eventon_admin_nonce')) $output['msg'] = __('Security Check Failed!','eventon');

			// check if admin and loggedin
				if(!is_admin() && !is_user_logged_in()) $output['msg'] = __('User not loggedin!','eventon');

			$JSON_data = $_POST['jsondata'];

			// check if json array present
			if(!is_array($JSON_data))  $output['msg'] = __('Not correct json format!','eventon');

			// if all good
			if( empty($output['msg'])){
				update_option('evcal_options_evcal_1', $JSON_data);
				$output['success'] = 'good';
				$output['msg'] = 'Successfully updated settings!';
			}
			
			echo json_encode($output);
			exit;

		}

	// export events as CSV
	// @version 2.2.30
		function export_events(){

			// check if admin and loggedin
				if(!is_admin() && !is_user_logged_in()) die('User not loggedin!');

			// verify nonce
				if(!wp_verify_nonce($_REQUEST['nonce'], 'eventon_download_events')) die('Security Check Failed!');

			header('Content-Encoding: UTF-8');
        	header('Content-type: text/csv; charset=UTF-8');
			header("Content-Disposition: attachment; filename=Eventon_events_".date("d-m-y").".csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			echo "\xEF\xBB\xBF"; // UTF-8 BOM
			
			$evo_opt = get_option('evcal_options_evcal_1');
			$event_type_count = evo_get_ett_count($evo_opt);
			$cmd_count = evo_calculate_cmd_count($evo_opt);

			$fields = apply_filters('evo_csv_export_fields',array(
				'publish_status',				
				'evcal_event_color'=>'color',
				'event_name',				
				'event_description','event_start_date','event_start_time','event_end_date','event_end_time',

				'evcal_allday'=>'all_day',
				'evo_hide_endtime'=>'hide_end_time',
				'evcal_gmap_gen'=>'event_gmap',
				'evo_year_long'=>'yearlong',
				'_featured'=>'featured',

				'evo_location_id'=>'evo_location_id',
				'evcal_location_name'=>'location_name',				
				'evcal_location'=>'event_location',				
				
				'evo_organizer_id'=>'evo_organizer_id',
				'evcal_organizer'=>'event_organizer',

				'evcal_subtitle'=>'evcal_subtitle',
				'evcal_lmlink'=>'learnmore link',
				'image_url',

				'evcal_repeat'=>'repeatevent',
				'evcal_rep_freq'=>'frequency',
				'evcal_rep_num'=>'repeats',
				'evp_repeat_rb'=>'repeatby',
			));
			
			$csvHeader = '';
			foreach($fields as $var=>$val){	$csvHeader.= $val.',';	}

			// event types
				for($y=1; $y<=$event_type_count;  $y++){
					$_ett_name = ($y==1)? 'event_type': 'event_type_'.$y;
					$csvHeader.= $_ett_name.',';
					$csvHeader.= $_ett_name.'_slug,';
				}
			// for event custom meta data
				for($z=1; $z<=$cmd_count;  $z++){
					$_cmd_name = 'cmd_'.$z;
					$csvHeader.= $_cmd_name.",";
				}

			$csvHeader = apply_filters('evo_export_events_csv_header',$csvHeader);
			$csvHeader.= "\n";
			echo iconv("UTF-8", "ISO-8859-2", $csvHeader);
 
			$events = new WP_Query(array(
				'posts_per_page'=>-1,
				'post_type' => 'ajde_events',
				'post_status'=>'any'			
			));

			if($events->have_posts()):
				date_default_timezone_set('UTC');

				// date and time format
				/*$date_format = get_option('date_format');
				$time_format = get_option('time_format');
				$date_time_format = evo_settings_val('evo_usewpdateformat', $evo_opt)? 
					$date_format.','.$time_format:
					'n/j/Y,g:i:A';
					*/

				// for each event
				while($events->have_posts()): $events->the_post();
					$__id = get_the_ID();
					$pmv = get_post_meta($__id);

					$csvRow = '';
					$csvRow.= get_post_status($__id).",";
					//echo (!empty($pmv['_featured'])?$pmv['_featured'][0]:'no').",";
					$csvRow.= (!empty($pmv['evcal_event_color'])? $pmv['evcal_event_color'][0]:'').",";

					// event name
						$eventName = get_the_title();
						$eventName = htmlentities($eventName);
						//$output = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $eventName);
						//$output =  preg_replace("/^'|[^A-Za-z0-9\s-]|'$/", '', $output); 
						$csvRow.= '"'.$eventName.'",';

					$event_content = get_the_content();
						$event_content = str_replace('"', "'", $event_content);
						$event_content = str_replace(',', "\,", $event_content);
						$event_content = htmlentities( $event_content);
					$csvRow.= '"'.$event_content.'",';

					// start time
						$start = (!empty($pmv['evcal_srow'])?$pmv['evcal_srow'][0]:'');
						if(!empty($start)){
							// date and time as separate columns
							$csvRow.= '"'. date( apply_filters('evo_csv_export_dateformat','m/d/Y'), $start) .'",';
							$csvRow.= '"'. date( apply_filters('evo_csv_export_timeformat','h:i:A'), $start) .'",';
							//$csvRow.= '"'. date($date_time_format, $start) .'",';
						}else{ $csvRow.= "'','',";	}

					// end time
						$end = (!empty($pmv['evcal_erow'])?$pmv['evcal_erow'][0]:'');
						if(!empty($end)){
							// date and time as separate columns
							$csvRow.= '"'. date( apply_filters('evo_csv_export_dateformat','m/d/Y'), $end) .'",';
							$csvRow.= '"'. date( apply_filters('evo_csv_export_timeformat','h:i:A'), $end) .'",';
							//$csvRow.= '"'. date($date_time_format,$end) .'",';
						}else{ $csvRow.= "'','',";	}

					// taxonomy meta
						$taxopt = get_option( "evo_tax_meta");
						

					// FOR EACH field
					$loctaxid = $orgtaxid = '';
					$loctaxname = $orgtaxname = '';
					foreach($fields as $var=>$val){

						// yes no values
						if(in_array($val, array('featured','all_day','hide_end_time','event_gmap','evo_year_long','_evo_month_long','repeatevent'))){
							$csvRow.= ( (!empty($pmv[$var]) && $pmv[$var][0]=='yes') ? 'yes': 'no').',';
						}

						// organizer field
							if($val == 'evo_organizer_id'){
								$Orgterms = wp_get_object_terms( $__id, 'event_organizer' );
								if ( $Orgterms && ! is_wp_error( $Orgterms ) ){
									$orgtaxid = $Orgterms[0]->term_id;
									$orgtaxname = $Orgterms[0]->name;
									$csvRow.= '"'.$orgtaxid . '",';
								}else{	$csvRow.= ",";	}
							}
							if($val == 'evcal_organizer'){
								if($orgtaxname){
									$csvRow.= '"'. htmlentities($orgtaxname) . '",';									
								}elseif(!empty($pmv[$var]) ){
									$value = htmlentities($pmv[$var][0]);
									$csvRow.= '"'.$value.'"';
								}else{	$csvRow.= ",";	}
								continue;
							}
						// location tax field
							if($val == 'evo_location_id'){
								$Locterms = wp_get_object_terms( $__id, 'event_location' );
								if ( $Locterms && ! is_wp_error( $Locterms ) ){
									$loctaxid = $Locterms[0]->term_id;
									$loctaxname = $Locterms[0]->name;
									$csvRow.= '"'.$loctaxid . '",';
								}else{	$csvRow.= ",";	}
							}
							if($val == 'location_name'){
								if($loctaxname){
									$csvRow.= '"'. htmlentities($loctaxname) . '",';									
								}elseif(!empty($pmv[$var]) ){
									$value = htmlentities($pmv[$var][0]);
									$csvRow.= '"'.$value.'"';
								}else{	$csvRow.= ",";	}
								update_post_meta(3089,'aa',$loctaxname.'yy');
								continue;
							}
							if($val == 'event_location'){
								if($loctaxid){
									$termMeta = evo_get_term_meta('event_location',$loctaxid, $taxopt, true);
									$csvRow.= !empty($termMeta['location_address'])? 
										'"'. htmlentities($termMeta['location_address']) . '",':
										",";									
								}elseif(!empty($pmv[$var]) ){
									$value = htmlentities($pmv[$var][0]);
									$csvRow.= '"'.$value.'"';
								}else{	$csvRow.= ",";	}
								continue;
							}

						// skip fields
						if(in_array($val, array('featured','all_day','hide_end_time','event_gmap','evo_year_long','_evo_month_long','repeatevent','color','publish_status','event_name','event_description','event_start_date','event_start_time','event_end_date','event_end_time','evo_organizer_id', 'evo_location_id'
							)
						)) continue;

						// image
						if($val =='image_url'){
							$img_id =get_post_thumbnail_id($__id);
							if($img_id!=''){
								$img_src = wp_get_attachment_image_src($img_id,'full');
								$csvRow.= $img_src[0].",";
							}else{ $csvRow.= ",";}
						}else{
							if(!empty($pmv[$var])){
								$value = htmlentities($pmv[$var][0]);
								$csvRow.= '"'.$value.'"';
							}else{ $csvRow.= '';}
							$csvRow.= ',';
						}
					}
					
					// event types
						for($y=1; $y<=$event_type_count;  $y++){
							$_ett_name = ($y==1)? 'event_type': 'event_type_'.$y;
							$terms = get_the_terms( $__id, $_ett_name );

							if ( $terms && ! is_wp_error( $terms ) ){
								$csvRow.= '"';
								foreach ( $terms as $term ) {
									$csvRow.= $term->term_id.',';
									//$csvRow.= $term->name.',';
								}
								$csvRow.= '",';

								// slug version
								$csvRow.= '"';
								foreach ( $terms as $term ) {
									$csvRow.= $term->slug.',';
								}
								$csvRow.= '",';
							}else{ $csvRow.= ",";}
						}
					// for event custom meta data
						for($z=1; $z<=$cmd_count;  $z++){
							$cmd_name = '_evcal_ec_f'.$z.'a1_cus';
							$csvRow.= (!empty($pmv[$cmd_name])? 
								'"'.str_replace('"', "'", htmlentities($pmv[$cmd_name][0])) .'"'
								:'');
							$csvRow.= ",";
						}

					$csvRow = apply_filters('evo_export_events_csv_row',$csvRow, $__id, $pmv);
					$csvRow.= "\n";

				echo iconv("UTF-8", "ISO-8859-2", $csvRow);

				endwhile;

			endif;

			wp_reset_postdata();
		}

	// Validation of eventon products
		function validate_license(){
			global $eventon;

			$status = 'bad'; $error_code = '00';
			
			if(empty($_POST['type'])){ _e("Missing Data",'eventon'); exit;}

			// Initial values
			$type = $_POST['type'];
			$license_key = $_POST['purchase_key'];
			$slug = $_POST['slug'];
			
			$verifyformat = evo_license()->purchase_key_format($license_key, $type );

			// save envato data for eventon
			if($type=='main') evo_license()->save_envato_data();

			// not valid license format
			if(!$verifyformat) $error_code = '10';	
			
			// if license key format is validated
			if($verifyformat){
				$status = 'good';
				$msg = ($slug=='eventon')?
					'Excellent! Purchase key verified and saved. Thank you for activating EventON!':
					'Excellent! License key verified and saved. Thank you for activating EventON addon!';

				$data_args = array(
					'type'=>(!empty($_POST['type'])?$_POST['type']:'main'),
					'slug'=> addslashes ($slug),
					'key'=> addslashes( str_replace(' ','',$license_key) ),
					'email'=>(!empty($_POST['email'])? $_POST['email']: null),
					'product_id'=>(!empty($_POST['product_id'])?$_POST['product_id']:''),						
					'instance'=> (!empty($_POST['instance'])?(int)$_POST['instance']:'1'),
				);

				$api_url = evo_license()->get_api_url($data_args);

				$validation = evo_license()->eventon_remote_validation($api_url, $license_key,$slug);

				// perform remote validation
				if($type=='main'){

					evo_license()->eventon_kriyathmaka_karanna();

					if($validation['status'] =='bad'){
						$msg = 'Your EventON license key is validated locally';
						$error_code = !empty($validation['error_code'])? $validation['error_code']: 13;
					}

				}else{ // for addons

					// update other addon fields
					foreach(array(
						'email','product_id','instance'
					) as $field){
						if(!empty($_POST[$field]))
							evo_license()->update_field($slug, $field, $_POST[$field]);
					}

					if($validation['status']=='bad'){
						evo_license()->evo_kriyathmaka_karanna_locally($slug);
						$error_code = 13;
					}

					$msg = '';
					
				}
			}

			$return_content = array(
				'status'=>$status,
				'msg'=> $msg,
				'error_msg'=> evo_license()->error_code( $error_code),
			);
			echo json_encode($return_content);		
			exit;
		}

		
		// deactivate addon 
			function deactivate_addon(){
				global $eventon;

				// initial values
					$debug = $content ='';
					$status = 'success';
					$error_code = '00';
					$error_msg='';

				// deactivate the license locally
				$dea_local = evo_license()->deactivate($_POST['slug']);
				
				// passing data
					$__data = array(
						'slug'=> addslashes ($_POST['slug']),
						'key'=> addslashes( str_replace(' ','',$_POST['key']) ),
						'email'=>(!empty($_POST['email'])? $_POST['email']: null),
						'product_id'=>(!empty($_POST['product_id'])? $_POST['product_id']: null),
					);

				// deactivate addon from remote server
					$url='http://www.myeventon.com/woocommerce/?wc-api=software-api&request=deactivation&email='.$__data['email'].'&licence_key='.$__data['key'].'&instance=0&product_id='.$__data['product_id'];

					$request = wp_remote_get($url);

					if (!is_wp_error($request) && $request['response']['code']===200) {

						$status_ = (!empty($request['body']))? json_decode($request['body']): $request; 
					}
				
				$return_content = array(
					'status'=>$status,					
					'extra'=>$status_,
					'error_msg'=> evo_license()->error_code($error_code),
					'content'=>"License Status: <strong>Deactivated</strong>"
				);
				echo json_encode($return_content);		
				exit;
			}

	// deactivate eventon license
		function eventon_deactivate_evo(){
			$error_msg =''; 

			$status = evo_license()->deactivate('eventon');

			if(!$status)	$error_msg = evo_license()->error_code();

			$return_content = array(
				'status'=> ($status?'success':'bad'),		
				'error_msg'=>$error_msg
			);
			echo json_encode($return_content);		
			exit;
		}

	/** Feature an event from admin */
		function eventon_feature_event() {

			if ( ! is_admin() ) die;

			if ( ! current_user_can('edit_eventons') ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'eventon' ) );

			if ( ! check_admin_referer('eventon-feature-event')) wp_die( __( 'You have taken too long. Please go back and retry.', 'eventon' ) );

			$post_id = isset( $_GET['eventID'] ) && (int) $_GET['eventID'] ? (int) $_GET['eventID'] : '';

			if (!$post_id) die;

			$post = get_post($post_id);

			if ( ! $post || $post->post_type !== 'ajde_events' ) die;

			$featured = get_post_meta( $post->ID, '_featured', true );

			if ( $featured == 'yes' )
				update_post_meta($post->ID, '_featured', 'no');
			else
				update_post_meta($post->ID, '_featured', 'yes');

			wp_safe_redirect( remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() ) );
		}

	// get all addon details
		public function get_addons_list(){

			// verifications
			if(!is_admin()) return false;

			require_once('settings/addon_details.php');

			$activePlugins = get_option( 'active_plugins' );
			$products = get_option('_evo_products');

			ob_start();
			// installed addons		

				$count=1;
				// EACH ADDON
				foreach($addons as $slug=>$product){
					if($slug=='eventon') continue; // skip for eventon
					$_has_addon = false;
					$_this_addon = (!empty($products[$slug]))? $products[$slug]:$product;

					// check if the product is activated within wordpress
					if(!empty($activePlugins)){
						foreach($activePlugins as $plugin){
							// check if foodpress is in activated plugins list
							if(strpos( $plugin, $slug.'.php') !== false){
								$_has_addon = true;
							}
						}
					}else{	$_has_addon = false;	}
								
					// initial variables
						$guide = ($_has_addon && !empty($_this_addon['guide_file']) )? "<span class='eventon_guide_btn ajde_popup_trig' ajax_url='{$_this_addon['guide_file']}' poptitle='How to use {$product['name']}'>Guide</span> | ":null;
						
						$__action_btn = (!$_has_addon)? "<a class='evo_admin_btn btn_secondary' target='_blank' href='". $product['download']."'>Get it now</a>": "<a class='ajde_popup_trig evo_admin_btn btn_prime' data-dynamic_c='1' data-content_id='eventon_pop_content_{$slug}' poptitle='Activate {$product['name']} License'>Activate Now</a>";

						//$__remote_version = (!empty($_this_addon['remote_version']))? '<span title="Remote server version"> /'.$_this_addon['remote_version'].'</span>': false;

						$pluginData = array();
						if(file_exists(AJDE_EVCAL_DIR.'/'.$slug.'/'.$slug.'.php'))
							$pluginData = get_plugin_data(AJDE_EVCAL_DIR.'/'.$slug.'/'.$slug.'.php');
					
						// /print_r($pluginData);
					// ACTIVATED
					if(!empty($_this_addon['status']) && $_this_addon['status']=='active' && $_has_addon):


					
					?>
						<div id='evoaddon_<?php echo $slug;?>' class="addon activated" data-slug='<?php echo $slug;?>' data-key='<?php echo $_this_addon['key'];?>' data-email='<?php echo $_this_addon['email'];?>' data-product_id='<?php echo $product['id'];?>'>
							<h2><?php echo $product['name']?></h2>
							<?php if(!empty($pluginData['Version'])):?>
								<p class='version'><span><?php echo $pluginData['Version']?></span></p>
							<?php endif;?>

							<p class='status'>License Status: <strong>Activated</strong></p>
							<p><a class='evo_deact_adodn ajde_popup_trig evo_admin_btn btn_triad' data-dynamic_c='1' data-content_id='eventon_pop_content_dea_<?php echo $slug;?>' poptitle='Deactivate <?php echo $product['name'];?> License'>Deactivate</a></p>
							<p class="links"><?php echo $guide;?><a href='<?php echo $product['link'];?>' target='_blank'>Learn More</a></p>
								<div id='eventon_pop_content_dea_<?php echo $slug;?>' class='evo_hide_this'>
									<p class="evo_loader"></p>
								</div>
						</div>
					
					<?php	
						// NOT ACTIVATED
						else:
							global $ajde;
					?>
						<div id='evoaddon_<?php echo $slug;?>' class="addon <?php echo (!$_has_addon)?'donthaveit':null;?>" data-slug='<?php echo $slug;?>' data-key='<?php echo !empty($_this_addon['key'])?$_this_addon['key']:'';?>' data-email='<?php echo !empty($_this_addon['email'])?$_this_addon['email']:'';?>' data-product_id='<?php echo !empty($product['id'])? $product['id']:'';?>'>
							<h2><?php echo $product['name']?></h2>
							<?php if(!empty($pluginData['Version'])):?>
								<p class='version'><span><?php echo $pluginData['Version']?></span></p>
							<?php endif;?>
							<p class='status'>License Status: <strong>Not Activated</strong></p>
							<p class='action'><?php echo $__action_btn;?></p>
							<p class="links"><?php echo $guide;?><a href='<?php echo $product['link'];?>' target='_blank'>Learn More</a></p>
							<p class='activation_text'></p>
								<div id='eventon_pop_content_<?php echo $slug;?>' class='evo_hide_this'>
									<p>
										<label><?php _e('Addon License Key','eventon');?>*</label>
										<input class='eventon_license_key_val fields' name='purchase_key' type='text' style='width:100%' placeholder='Enter the addon license key'/>
										<input class='eventon_slug fields' name='slug' type='hidden' value='<?php echo $slug;?>' />
										<input class='eventon_id fields' name='product_id' type='hidden' value='<?php echo $product['id'];?>' />
										<input class='eventon_license_div' type='hidden' value='evoaddon_<?php echo $slug;?>' />
										<i style='opacity:0.6;padding-top:5px; display:block'><?php _e('Find addon license key from','eventon');?> <a href='http://www.myeventon.com/my-account/licenses/' target='_blank'><?php _e('My eventon > My licenses','eventon');?></a></i>
									</p>

									<p>
										<label><?php _e('Email Address','eventon');?>* <?php $ajde->wp_admin->echo_tooltips('The email address you have used to purchase eventon addon from myeventon.com.');?></label>
										<input class='eventon_email_val fields' name='email' type='text' style='width:100%' placeholder='Email address used for purchasing addon'/>
									</p>
									
									<p>
										<label><?php _e('Site Instance','eventon');?> <?php $ajde->wp_admin->echo_tooltips('If your license allow more than one site activations, please select which site you are activating now eg. 1 - for 1st website, 2 - for 2nd website, 3 - for 3rd website etc.');?></label>
										<input class='eventon_index_val fields' name='instance' type='text' style='width:100%'/>
									</p>

									<p><a class='eventonADD_submit_license evo_admin_btn btn_prime' data-type='addon' data-slug='<?php echo $slug;?>'>Activate Now</a></p>
								</div>
						</div>
					<?php		
						endif;
						$count++;
				} //endforeach

			$content = ob_get_clean();

			$return_content = array(
				'content'=> $content,
				'status'=>true
			);
			
			echo json_encode($return_content);		
			exit;	
		}

	// Deprecating
		// get API data
		// deprecated since 2.5
			function get_license_api_url(){
				global $eventon;
				
				$__passing_instance = (!empty($_POST['instance'])?(int)$_POST['instance']:'1');
				$data = array(
					'type'=>(!empty($_POST['type'])?$_POST['type']:'main'),
					'slug'=> addslashes ($_POST['slug']),
					'key'=> addslashes( str_replace(' ','',$_POST['key']) ),
					'email'=>(!empty($_POST['email'])? $_POST['email']: null),
					'product_id'=>(!empty($_POST['product_id'])?$_POST['product_id']:''),						
					'instance'=>$__passing_instance,
				);					

				echo json_encode( array('json_url'=> $eventon->evo_updater->get_api_url($data) ));
				exit;
			}
		// update remote validity status of a license
		// deprecating
			function remote_validity(){
				global $eventon;

				$new_content = '';

				// EventON update remote validity
				if($_POST['slug'] == 'eventon'){
					$eventon->evo_updater->eventon_kriyathmaka_karanna();
					if(!empty($_POST['buyer'])) 
						$eventon->evo_updater->product->update_field($_POST['slug'], 'buyer', $_POST['buyer']);

					$new_content = '';
				}

				$remote_validity = !empty($_POST['remote_validity'])? 'valid':'';
				$status = $eventon->evo_updater->product->update_field($_POST['slug'], 'remote_validity',$remote_validity );

				if(!empty($_POST['key'])) $eventon->evo_updater->product->update_field($_POST['slug'], 'key',$_POST['key'] );

				$return_content = array(
					'status'=>($status?'good':'bad'),	
					'new_content'=>	$new_content		
				);
				echo json_encode($return_content);		
				exit;
			}
		// verify license key
			function verify_key(){
				global $eventon;

				// initial values
					$debug = $content = $addition_msg ='';
					$status = 'success';
					$error_code = '00';
					$error_msg='';

				// passing data
					$__passing_instance = (!empty($_POST['instance'])?(int)$_POST['instance']:'1');
					$__data = array(
						'slug'=> addslashes($_POST['slug']),
						'key'=> addslashes( str_replace(' ','',$_POST['key']) ),
						'email'=>(!empty($_POST['email'])? $_POST['email']: null),
						'product_id'=>(!empty($_POST['product_id'])?$_POST['product_id']:''),
						'instance'=>$__passing_instance,
					);

				// for eventon
				if($_POST['slug']=='eventon'){
					$api_url = $eventon->evo_updater->get_api_url($__data);		
					$__save_new_lic = $eventon->evo_updater->product->save_license(
						$__data['slug'],
						$__data['key']
					);
					$return_content = array(
						'status'=>$status,
						'error_msg'=>$eventon->evo_updater->error_code_($error_code),
						'addition_msg'=>$addition_msg,
						'json_url'=>$api_url,
					);
				// Addons
				}else{
					$status_ = $eventon->evo_updater->verify_product_license($__data);
					//content for success activation
						$content ="License Status: <strong>Activated</strong>";

					// save verified eventon addon product info
						$__save_new_lic = $eventon->evo_updater->product->save_license(
							$__data['slug'],
							$__data['key'],
							$__data['email'],
							$__data['product_id'],
							'valid','', (!empty($status_->instance)? $status_->instance:'1')
						);

					// CHECK remote validation results
					if($status_){
						// if activated value is true
						if($status_->activated){							
							$status = 'success';

							// append additional mesages passed from remote server
							$addition_msg = !empty($status_->message)? $status_->message:null;

						}else{ // return activated to be not true
							// if there were errors returned from eventon server
							if(!empty($status_->code) && $status_->code=='103' && $__passing_instance=='1'){
								$status = 'success';
								$error_code = '12';
							}elseif(!empty($status_->code) && $status_->code=='103'){
								$status = 'bad';
								$error_code = '103'; //exceeded max activations
							}else{
								$status = 'success';
								$error_code = '13'; //general validation failed
							}				
						}
					}else{ // couldnt connect to myeventon.com to check
						$status = 'good';
						$error_code = '13';							
					}
					$return_content = array(
						'status'=>$status,
						'error_msg'=>$eventon->evo_updater->error_code_($error_code),
						'addition_msg'=>$addition_msg,
						'this_content'=>$content,
						'extra'=>$status_,
					);
				}
				
				echo json_encode($return_content);		
				exit;				
			}


}
new EVO_admin_ajax();