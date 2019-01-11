<?php
/**
 * Language Settings 
 *
 * @version		2.4.7
 * @package		EventON/settings
 * @category	Settings
 * @author 		AJDE
 */

class evo_settings_lang{
	private $eventon_months = array(1=>'january','february','march','april','may','june','july','august','september','october','november','december');
		
	private $eventon_days = array(1=>'monday','tuesday','wednesday','thursday','friday','saturday','sunday');

	function __construct($evcal_opt)	{
		$this->evcal_opt = $evcal_opt;
		$this->evopt = get_option('evcal_options_evcal_1');
		$this->lang_version = (!empty($_GET['lang']))? $_GET['lang']: 'L1';
		
		$this->lang_options = (!empty($this->evcal_opt[2][$this->lang_version]))? $this->evcal_opt[2][$this->lang_version]:null;

		$this->lang_variations = apply_filters('eventon_lang_variation', array('L1','L2', 'L3'));
		$this->uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
	}
	// return content for 
	function get_content(){
		ob_start(); ?>
		<form method="post" action=""><?php settings_fields('evcal_field_group'); 
			wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' ); ?>
			<div id="evcal_2" class="postbox evcal_admin_meta">	
				<div class="inside">
					<h2><?php _e('Type in custom language text for front-end calendar','eventon');?></h2>
					<?php echo $this->_section_lang_selection();?>

					<p style='padding-bottom:15px;'><i><?php _e('Please use the below fields to type in custom language text that will be used to replace the default language text on the front-end of the calendar.','eventon')?><br/><?php _e('NOTE: Text strings with label "Duplicate" will be replaced with first text string value you entered, after saving changes.');?></i></p>
					<?php
						echo $this->interpret_array( apply_filters('eventon_settings_lang_tab_content',$this->language_variables_array()) );
					?>
				</div>
			</div>
			
			<p style='padding:0'><input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes','eventon') ?>" style='margin-top:15px'/></p>

		</form>
		
		<?php
			/**
			 * Language Import and Exporting
			 * @version 0.1
			 * @added 	2.3.2
			 */
		?>
		<div class="evo_lang_export" style='padding-top:10px; margin-top:30px; border-top:1px solid #d0d0d0'>
			<h3><?php _e('Import/Export translations','eventon');?></h3>
			<p><i><?php _e('NOTE: Make sure to save changes after importing. This will import/export the current selected language ONLY.','eventon');?></i></p>

			<div id="import_box" style='display:none'>
				<span id="close">X</span>
				<form id="file-form" action="" method="POST" data-link='<?php echo AJDE_EVCAL_PATH;?> '>
					  <input type="file" id="file-select" name="photos[]" multiple accept=".csv" />
					  <button type="submit" id="upload-button"><?php _e('Upload','eventon');?></button>
				</form>
				<p class="msg" style='display:none'><?php _e('File Uploading','eventon');?></p>
			</div>
			<p><a id='evo_lang_import' class='evo_admin_btn btn_prime'><?php _e('Import to Current Language options','eventon');?></a> <a id='evo_lang_export' class='evo_admin_btn btn_prime'><?php _e('Export Current Language options','eventon');?></a></p>
		</div>

		

		<?php echo ob_get_clean();
	}

		function _section_lang_selection(){	
			global $ajde;		
			ob_start(); ?>
				<h4><?php _e('Select your language','eventon');?> <select id='evo_lang_selection' url=<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->uri_parts[0];?>>		
				<?php
					foreach($this->lang_variations as $lang){
						echo "<option value='{$lang}' ".(($this->lang_version==$lang)? 'selected="select"':null).">{$lang}</option>";
					}
				?></select>
				<?php $ajde->wp_admin->echo_tooltips(__("You can use this to save different languages for customized text for calendar. Once saved use the shortcode to show calendar text in that customized language. eg. [add_eventon lang='L2']",'eventon'));?></h4>

			<?php 
			return ob_get_clean();
		}

		// interpret the language array information
		function interpret_array($array){

			global $ajde;

			$output = '';

			if(!is_array($array)) return;

			$LNG_names = array();

			foreach($array as $item){
				$item_type = !empty($item['type'])? $item['type']: '';				
				$label = (!empty($item['label']))?  $item['label']: '';
				$legend = (!empty($item['legend']))?  $item['legend']: '';
				$placeholder = (!empty($item['placeholder']))?  $item['placeholder']: $legend;


				switch($item_type){
					case 'togheader':
						$output .= "<div class='evoLANG_section_header evo_settings_toghead'>{$item['name']}</div><div class='evo_settings_togbox'>";
					break;
					case 'multibox_open':
						if(!empty($item['items']) && is_array($item['items'])){
							$output .= "<div class='evcal_lang_box ' style='padding-bottom:5px; clear:both'>";
						
							foreach($item['items'] as $box=>$boxval){
								if(is_array($boxval)){
									$output .= "<p class='evcal_lang_p'><input type='text' name='{$box}' class='evcal_lang' value='{$boxval['default']}' placeholder='{$boxval['placeholder']}'/></p>";
								}else{
									$output .= "<p class='evcal_lang_p'><input type='text' name='{$box}' class='evcal_lang' value='{$boxval}'/></p>";
								}
							}
							$output .= "<div style='clear:both'></div></div>";
						}
						
					break;					
					case 'subheader':
						$output .= '<div class="evoLANG_subheader">'.$label.'</div><div class="evoLANG_subsec">';
					break;

					case 'togend':
						$output .= "</div><!--close-->";
					break;
					default:

						//if(empty($item['name'])) continue;

				
						//@v 2.2.28 
						// self sufficient names for language
							if(!empty($item['var']) && $item['var']=='1'){
								$name = evo_lang_texttovar_filter($label);
							}else{
								$name = $item['name'];
							}

						$duplicate_string = in_array($name, $LNG_names)? true:false;

						// field name processing
							if(in_array($name, $LNG_names) && !empty($this->lang_options[$name]) ){
								$val = $this->lang_options[$name];
							}else{
								$name = in_array($name, $LNG_names)? $name.'_v_': $name;
								$LNG_names[] = $name;	
								$val = (!empty($this->lang_options[$name]))?  $this->lang_options[$name]: '';
							}
								

						$output .= "<div class='eventon_custom_lang_line ".($duplicate_string?'dup':'')."'>
							<div class='eventon_cl_label_out'>
								<p class='eventon_cl_label'>{$label}</p>
							</div>";

						$value = is_array($val)? $val[0]: stripslashes($val);

						$output .= '<input class="eventon_cl_input " type="text" name="'.$name.'" placeholder="'.$placeholder.'" value="'.
							$value.'"/>';

						if($placeholder) $output .= $ajde->wp_admin->tooltips($placeholder,'L');
						$output .= "<div class='clear'></div></div>";
						//$output .= (!empty($legend))? "<p class='eventon_cl_legend'>{$legend}</p>":null;

					break;
				}
			}

			return $output;
		}

		function language_variables_array(){
			$output =  array(
				array('type'=>'togheader','name'=>__('Months and Dates','eventon')),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_months()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_3letter_months()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_1letter_months()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_day_names()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_3leter_day_names()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_ampm()),
				array('type'=>'togend'),
				array('type'=>'togheader','name'=>__('General Calendar','eventon')),
					array('label'=>'No Events','name'=>'evcal_lang_noeve',),
					array('label'=>'No Events on The List at This Time','var'=>1),
					array('label'=>'All Day','name'=>'evcal_lang_allday',),
					array('label'=>'Year Around Event','name'=>'evcal_lang_yrrnd'),
					array('label'=>'Month Long Event','name'=>'evcal_lang_mntlng'),
					array(
						'label'=>'Events','name'=>'evcal_lang_events',
					),array(
						'label'=>'Show More Events','name'=>'evcal_lang_sme',
					),
					array('label'=>'Event Cancelled','name'=>'evcal_evcard_evcancel'),
					array('label'=>'Featured','var'=>1),
					array('label'=>'Event Tags','name'=>'evo_lang_eventtags',),
					array('label'=>'YES','name'=>'evo_lang_yes',),
					array('label'=>'NO','name'=>'evo_lang_no',),
					array('label'=>'MORE','name'=>'evo_lang_more'),
					
					array('label'=>'Search Events','name'=>'evoSR_001','legend'=>'placeholder for search input fields'),
					array('label'=>'Search Calendar Events','name'=>'evoSR_001a'),
					array('label'=>'Searching','name'=>'evoSR_002'),
					array('label'=>'What do you want to search for?','name'=>'evoSR_003'),
					array('label'=>'Event(s) found','name'=>'evoSR_004'),
					array('label'=>'Download all events as ICS file','var'=>'1'),
				array('type'=>'togend'),
			);

			$output = array_merge($output, $this->_array_part_taxonomies());
			$output = array_merge($output, array(
				
				array('type'=>'togheader','name'=>__('Calendar Header','eventon')),
					array(
						'label'=>'Jump Months','name'=>'evcal_lang_jumpmonths',
					),array(
						'label'=>'Jump Months: Month','name'=>'evcal_lang_jumpmonthsM',
					),array(
						'label'=>'Jump Months: Year','name'=>'evcal_lang_jumpmonthsY',
					),array(
						'label'=>'Filter Events','name'=>'evcal_lang_sopt',
					)
					,array(
						'label'=>'Sort By','name'=>'evcal_lang_sort',
					),array(
						'label'=>'Date','name'=>'evcal_lang_sdate',
					),array(
						'label'=>'Posted','name'=>'evcal_lang_sposted',
					),array(
						'label'=>'Title','name'=>'evcal_lang_stitle',
					),array(
						'label'=>'All','name'=>'evcal_lang_all',
						'placeholder'=>'Sort options all text'
					),array(
						'label'=>'Current Month','name'=>'evcal_lang_gototoday',
					),array('label'=>'Apply Filters','name'=>'evcal_lang_apply_filters'),

					array('type'=>'togend'),
				array('type'=>'togheader','name'=>__('Event Card','eventon')),
					array(
						'label'=>'Location Name','name'=>'evcal_lang_location_name',
					)
					,array('label'=>'Location','name'=>'evcal_lang_location')
					,array('label'=>'Event Location','name'=>'evcal_evcard_loc')
					,array(
						'label'=>'Type your address','name'=>'evcalL_getdir_placeholder',
						'legend'=>'Get directions section'
					),array(
						'label'=>'Click here to get directions',
						'name'=>'evcalL_getdir_title',
						'legend'=>'Get directions section'
					),
					array('label'=>'Time','name'=>'evcal_lang_time'),
					array('label'=>'Future Event Times in this Repeating Event Series','name'=>'evcal_lang_repeats'),
					array('label'=>'Color','name'=>'evcal_lang_scolor',),
					array('label'=>'At (event location)','name'=>'evcal_lang_at',),
					array('label'=>'Event Details','name'=>'evcal_evcard_details',),
					array('label'=>'Organizer','name'=>'evcal_evcard_org',),
					//array('label'=>'Event Organizer','name'=>'evcal_lang_evorg',),
					array(
						'label'=>'Close event button text',
						'name'=>'evcal_lang_close',
					),array(
						'label'=>'More',
						'name'=>'evcal_lang_more',
						'legend'=>'More/less text for long event description'
					),array(
						'label'=>'Less',
						'name'=>'evcal_lang_less',
						'legend'=>'More/less text for long event description'
					),array(
						'label'=>'Buy ticket via Paypal',
						'name'=>'evcal_evcard_tix1',
						'legend'=>'for Paypal'
					),array(
						'label'=>'Buy Now button text',
						'name'=>'evcal_evcard_btn1',
						'legend'=>'for Paypal'
					),array(
						'label'=>'Ticket for the event',
						'name'=>'evcal_evcard_tix2',
						'legend'=>'for eventbrite'
					),array(
						'label'=>'Buy now button',
						'name'=>'evcal_evcard_btn2',
						'legend'=>'for eventbrite'
					),array(
						'label'=>'Event Capacity',
						'name'=>'evcal_evcard_cap',
					),array(
						'label'=>'Learn More about this event',
						'name'=>'evcal_evcard_learnmore',
						'legend'=>'for meetup'
					),array(
						'label'=>'Learn More link text',
						'name'=>'evcal_evcard_learnmore2',
						'legend'=>'for event learn more text'
					),
					array('label'=>'Login required to see the information','var'=>'1',),
					array('type'=>'subheader','label'=>__('Add to calendar Section','eventon')),
						array(
							'label'=>'Calendar','name'=>'evcal_evcard_calncal',			
						),array(
							'label'=>'GoogleCal','name'=>'evcal_evcard_calgcal',			
						),array(
							'label'=>'Add to your calendar',
							'name'=>'evcal_evcard_addics',
							'legend'=>'Hover over text for add to calendar button'
						),array(
							'label'=>'Add to google calendar',
							'name'=>'evcal_evcard_addgcal',
							'legend'=>'Hover over text for add to google calendar button'
						),
					array('type'=>'togend'),
						array('type'=>'subheader','label'=>__('Custom Meta Fields (if activated)','eventon')),
						array('type'=>'multibox_open', 'items'=>$this->_array_part_custom_meta_field_names()),
					array('type'=>'togend'),
					
				array('type'=>'togend'),

			)); 
		
			// single events
			$singleEvents = array(
				array('type'=>'togheader','name'=>'Single Events'),
				array('label'=>'Login','var'=>'1',),
				array('label'=>'You must login to see this event','var'=>'1'),
				array('label'=>'This is a repeating event','var'=>'1'),
				array('type'=>'togend'),
			);

			$output = array_merge($output, $singleEvents);
			
			return $output;
		}
			function _array_part_months(){
				$output = array();
				for($x=1; $x<13; $x++){
					$output['evcal_lang_'.$x] = array(
						'default'=>((!empty($this->lang_options['evcal_lang_'.$x]))?  $this->lang_options['evcal_lang_'.$x]: ''), 
						'placeholder'=>$this->eventon_months[$x]
					);
				}
				return $output;
			}
			function _array_part_3letter_months(){
				$output = array();
				for($x=1; $x<13; $x++){
					$month_3l = substr($this->eventon_months[$x],0,3);
					$output['evo_lang_3Lm_'.$x] = array('default'=> ((!empty($this->lang_options['evo_lang_3Lm_'.$x]))?  $this->lang_options['evo_lang_3Lm_'.$x]: ''), 'placeholder'=>$month_3l);
				}
				return $output;
			}
			function _array_part_1letter_months(){
				$output = array();
				for($x=1; $x<13; $x++){
					$month_1l = substr($this->eventon_months[$x],0,1);
					$output['evo_lang_1Lm_'.$x] = array('default'=>((!empty($this->lang_options['evo_lang_1Lm_'.$x]))?  $this->lang_options['evo_lang_1Lm_'.$x]: ''), 'placeholder'=>$month_1l);
				}
				return $output;
			}
			function _array_part_day_names(){
				$output = array();
				for($x=1; $x<8; $x++){
					$default = $this->eventon_days[$x];
					$output['evcal_lang_day'.$x] = array('default'=>((!empty($this->lang_options['evcal_lang_day'.$x]))?  $this->lang_options['evcal_lang_day'.$x]: ''), 'placeholder'=>$default);
				}
				return $output;
			}
			function _array_part_3leter_day_names(){
				$output = array();
				for($x=1; $x<8; $x++){
					$default = substr($this->eventon_days[$x],0,3);
					$output['evo_lang_3Ld_'.$x] = array('default'=>((!empty($this->lang_options['evo_lang_3Ld_'.$x]))?  $this->lang_options['evo_lang_3Ld_'.$x]: ''),'placeholder'=>$default);
				}
				return $output;
			}
			function _array_part_ampm(){
				$output = array();
				$output['evo_lang_am'] = array('default'=>((!empty($this->lang_options['evo_lang_am']))?  $this->lang_options['evo_lang_am']: ''),'placeholder'=>'am');
				$output['evo_lang_pm'] = array('default'=>((!empty($this->lang_options['evo_lang_pm']))?  $this->lang_options['evo_lang_pm']: ''),'placeholder'=>'pm');
				return $output;
			}
			function _array_part_taxonomies(){
				$output =  array();
				
				$event_type_names = evo_get_ettNames($this->evopt);
				$ett_verify = evo_get_ett_count($this->evopt);

				$output[] =array('type'=>'togheader','name'=>'Event Type Categories');

				for($x=1; $x<($ett_verify+1); $x++){

					$default = $event_type_names[$x];
					$output[] = array('label'=>$default, 'name'=>'evcal_lang_et'.$x);

					// each term of taxonomy
					$ab = $x==1?'':'_'.$x;
					$terms = get_terms('event_type'.$ab, array('hide_empty'=>false));
					$termitem = array();
					if(!empty($terms)){
						foreach($terms as $term){
							$var = 'evolang_'.'event_type'.$ab.'_'.$term->term_id;
							$termitem[$var]=(!empty($this->lang_options[$var]))?  $this->lang_options[$var]: $term->name;
						}
					}
					if(!empty($termitem)){
						$output[] = array('type'=>'multibox_open', 'items'=>$termitem);
					}
				}

				$output[] = array('label'=>'Event Location', 'name'=>'evcal_lang_evloc');
				$output[] = array('label'=>'Events at this location', 'var'=>'1');
				$output[] = array('label'=>'Event Organizer', 'name'=>'evcal_lang_evorg');
				$output[] = array('label'=>'Events by this organizer', 'var'=>'1');

				// for MDT
					for($y=1; $y <= EVO()->mdt->evo_max_mdt_count() ; $y++){
						$output[] = array('label'=>'Multi Data Type '.$y, 'var'=>'1');
					}

				$output[] = array('type'=>'togend');
				return $output;
			}
			function _array_part_custom_meta_field_names(){
				$output = array();
				$cmd_verify = evo_retrieve_cmd_count($this->evopt);

				for($x=1; $x<($cmd_verify+1); $x++){
					$default = $this->evopt['evcal_ec_f'.$x.'a1'];
					$output['evcal_cmd_'.$x] = array('default'=>((!empty($this->lang_options['evcal_cmd_'.$x]))?  $this->lang_options['evcal_cmd_'.$x]: ''), 'placeholder'=>$default);
				}
				return $output;
			}

}

?>