<?php
/**
 * Generalized AJDE plugin backend settings
 * @version 0.1
 */

class ajde_settings{
	public $focus_tab;
	public $current_section;

	public function __construct($defailt_tab){
		$this->focus_tab = (isset($_GET['tab']) )? sanitize_text_field( urldecode($_GET['tab'])):$defailt_tab;
		$this->current_section = (isset($_GET['section']) )? sanitize_text_field( urldecode($_GET['section'])):'';
	}

	function get_current_tab_values($options_pre){
		if(empty($options_pre)) return false;

		$current_tab_number = substr($this->focus_tab, -1);
		// if the tab last character is not numeric then get the whole tab name as the variable name for the options 		
		if(!is_numeric($current_tab_number)){ 
			$current_tab_number = $this->focus_tab;
		}
	
		return array($current_tab_number=> get_option($options_pre.$this->focus_tab));
	}

	function header_wraps($args){
		?>
		<div class="wrap ajde_settings" id='<?php echo $args['tab_id'];?>'>
			<h2><?php echo $args['title'];?> (ver <?php echo $args['version'];?>)</h2>
			<h2 class='nav-tab-wrapper' id='meta_tabs'>
				<?php					
					foreach($args['tabs'] as $key=>$val){
						
						echo "<a href='{$args['tab_page']}".$key."' class='nav-tab ".( ($this->focus_tab == $key)? 'nav-tab-active':null)." {$key}' ". 
							( (!empty($args['tab_attr_field']) && !empty($args['tab_attr_pre']))? 
								$args['tab_attr_field'] . "='{$args['tab_attr_pre']}{$key}'":'') . ">".$val."</a>";
					}			
				?>		
			</h2>
		<?php
	}

	function settings_tab_start($args){
		?>
		<form method="post" action="">
			<?php settings_fields($args['field_group']); ?>
			<?php wp_nonce_field( $args['nonce_key'], $args['nonce_field'] );?>
		<div id="<?php echo $args['tab_id'];?>" class="<?php implode(' ', $args['classes']);?>">
			<div class="<?php implode(' ', $args['inside_classes']);?>">
				<?php
	}
	function settings_tab_end(){
		?></div></div><?php
	}

	function save_settings($nonce_key, $nonce_field, $options_pre){
		if( isset($_POST[$nonce_field]) && isset( $_POST ) ){
			if ( wp_verify_nonce( $_POST[$nonce_field], $nonce_key ) ){
				foreach($_POST as $pf=>$pv){
					$pv = (is_array($pv))? $pv: addslashes(esc_html(stripslashes(($pv)))) ;
					$options[$pf] = $pv;
				}

				update_option($options_pre.$this->focus_tab, $options);
			}
		}
	}

	function evo_save_settings($focus_tab, $current_section){
		if( isset($_POST['evcal_noncename']) && isset( $_POST ) ){
			if ( wp_verify_nonce( $_POST['evcal_noncename'], AJDE_EVCAL_BASENAME ) ){
				
				// run through all post values
				foreach($_POST as $pf=>$pv){
					// skip fields
					if(in_array($pf, array('option_page', 'action','_wpnonce','_wp_http_referer','evcal_noncename'))) continue;

					if( ($pf!='evcal_styles' && $focus_tab!='evcal_4') || $pf!='evcal_sort_options'){
						
						$pv = (is_array($pv))? $pv: addslashes(esc_html(stripslashes(($pv)))) ;
						$evcal_options[$pf] = $pv;
					}
					if($pf=='evcal_sort_options'){
						$evcal_options[$pf] =$pv;
					}					
				}
				
				// General settings page - write styles to head option
				if($focus_tab=='evcal_1' && isset($_POST['evcal_css_head']) && $_POST['evcal_css_head']=='yes'){
					EVO()->evo_admin->update_dynamic_styles();
				}					
				
				//language tab
					if($focus_tab=='evcal_2'){
						
						
						$new_lang_opt = array();
						$_lang_version = (!empty($_GET['lang']))? $_GET['lang']: 'L1';

						$lang_opt = get_option('evcal_options_evcal_2');
						if(!empty($lang_opt) ){
							$new_lang_opt[$_lang_version] = $evcal_options;
							$new_lang_opt = array_merge($lang_opt, $new_lang_opt);
						}else{
							$new_lang_opt[$_lang_version] =$evcal_options;
						}
						
						update_option('evcal_options_evcal_2', $new_lang_opt);
						
					}

				elseif($focus_tab == 'evcal_1' || empty($focus_tab)){
					// store custom meta box count
					$cmd_count = evo_calculate_cmd_count();
					$evcal_options['cmd_count'] = $cmd_count;

					update_option('evcal_options_'.$focus_tab, $evcal_options);

				// all other settings tabs
				}else{
					//do_action('evo_save_settings',$focus_tab, $evcal_options);
					$evcal_options = apply_filters('evo_save_settings_optionvals', $evcal_options, $focus_tab);
					update_option('evcal_options_'.$focus_tab, $evcal_options);
				}
				
				// STYLES
				if( isset($_POST['evcal_styles']) )
					update_option('evcal_styles', strip_tags(stripslashes($_POST['evcal_styles'])) );

				// PHP Codes
				if( isset($_POST['evcal_php']) )
					update_option('evcal_php', strip_tags(stripslashes($_POST['evcal_php'])) );
				
				$_POST['settings-updated']='true';			
			
				// update dynamic styles file
					EVO()->evo_admin->generate_dynamic_styles_file();

			// nonce check
			}else{
				echo '<div class="notice error"><p>'.__('Settings not saved, nonce verification failed! Please try again later!','eventon').'</p></div>';
			}	
		}
	}
}