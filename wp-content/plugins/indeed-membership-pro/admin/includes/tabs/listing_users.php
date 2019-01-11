<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab='.$tab.'&shortcode_generator';?>"><?php _e('Shortcode Generator', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab='.$tab.'&subtab=inside_page';?>"><?php _e('Inside Page', 'ihc');?></a>	
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab='.$tab.'&subtab=settings';?>"><?php _e('Additional Settings', 'ihc');?></a>	
</div>
<?php
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
$meta_arr = array(
							'num_of_entries' => 10,
							'entries_per_page' => 5,
							'order_by' => 'date',
							'order_type' => 'desc',
							'user_fields' => 'user_login,user_email,first_name,last_name,ihc_avatar,ihc_sm',	
							'include_fields_label' => 0,						
							'theme' => 'ihc-theme_1',
							'color_scheme' => '0a9fd8',
							'columns' => 5,
							'inside_page' => 0,
							'align_center' => 1,
							'slider_set' => 0,
							'items_per_slide' => 2,
							'speed' => 5000,
							'pagination_speed' => 500,
							'bullets' => 1,
							'nav_button' => 1,
							'autoplay' => 1,
							'stop_hover' => 0,
							'autoplay' => 1,
							'stop_hover' => 0,
							'responsive' => 0,
							'autoheight' => 0,
							'lazy_load' => 0,
							'loop' => 1,
							'pagination_theme' => 'pag-theme1',					
						);
?>
<script>
var ihc_url = '<?php echo IHC_URL;?>';
jQuery(document).ready(function(){
	ihc_preview_u_list();
});
</script>
<?php 
	$tab = (empty($_GET['subtab'])) ? 'shortcode_generator' : $_GET['subtab'];
	switch ($tab){
		case 'shortcode_generator':
?>
	<div class="ihc-user-list-wrap">
			<div class="iump-page-title">Ultimate Membership Pro - 
				<span class="second-text"><?php _e('Members List', 'ihc');?></span>
			</div>
			<div class="ihc-user-list-settings-wrapper">
				<div class="box-title">
		            <h3><i class="fa-ihc fa-icon-angle-down-ihc"></i><?php _e("ShortCode Generator", 'ihc')?></h3>
		            <div class="actions pointer">
					    <a onclick="jQuery('#the_ihc_user_list_settings').slideToggle();" class="btn btn-mini content-slideUp">
		                    <i class="fa-ihc fa-icon-cogs-ihc"></i>
		                </a>		                
					</div>
				 	<div class="clear"></div>
				</div>					
				<div id="the_ihc_user_list_settings" class="ihc-list-users-settings">
				
					<!-- DISPLAY ENTRIES -->
					<div class="ihc-column column-one">
                   		<h4 style="background-color: rgb(66, 66, 66);"><i class="fa-ihc fa-icon-dispent-ihc"></i><?php _e('Display Entries', 'ihc');?></h4>
						<div class="ihc-settings-inner">
							<div class="ihc-user-list-row">
								<div class="ihc-label"><?php _e("Number Of Entries:", 'ihc');?></div>
								<div class="ihc-field"><input type="number" value="<?php echo $meta_arr['num_of_entries'];?>" id="num_of_entries" onKeyUp="ihc_preview_u_list();" onChange="ihc_preview_u_list();" style="width: 81px;" min="0" /></div>
							</div>
							<div class="ihc-user-list-row">
								<div class="ihc-label"><?php _e("Entries Per Page:", 'ihc');?></div>
								<div class="ihc-field"><input type="number" value="<?php echo $meta_arr['entries_per_page'];?>" id="entries_per_page" onKeyUp="ihc_preview_u_list();" onChange="ihc_preview_u_list();" style="width: 81px;" min="1" /></div>
							</div>		
							<div class="ihc-spacewp_b_divs"></div>					
							<div class="ihc-user-list-row">
								<div class="ihc-label"><?php _e("Order By:", 'ihc');?></div>
								<div class="ihc-field">
									<select id="order_by" onChange="ihc_preview_u_list();">
										<?php 
											$arr = array( 'user_registered' => __('Register Date','ihc'), 
														  'user_login' => __("UserName", 'ihc'),
														  'user_email' => __("E-mail Address", 'ihc'),
														  'random' => __("Random", 'ihc'),
											);
											foreach ($arr as $k=>$v){
												$selected = ($meta_arr['order_by']==$k) ? 'selected' : ''; 
												?>
												<option value="<?php echo $k?>" <?php echo $selected;?>><?php echo $v;?></option>	
												<?php 
											}
										?>
									</select>
								</div>
							</div>
							<div class="ihc-user-list-row">
								<div class="ihc-label"><?php _e("Order Type:", 'ihc');?></div>
								<div class="ihc-field">
									<select id="order_type" onChange="ihc_preview_u_list();">
										<?php 
											foreach (array('asc'=>'ASC', 'desc'=>'DESC') as $k=>$v){
												$selected = ($meta_arr['order_type']==$k) ? 'selected' : ''; 
												?>
												<option value="<?php echo $k?>" <?php echo $selected;?>><?php echo $v;?></option>	
												<?php 
											}
										?>
									</select>
								</div>
							</div>	
							<div class="ihc-spacewp_b_divs"></div>
							<div class="ihc-user-list-row">							
								<?php $checked = (empty($meta_arr['inside_page'])) ? '' : 'checked';?>
								<input type="checkbox" id="inside_page" <?php echo $checked;?> onClick="ihc_preview_u_list();"/> <?php _e("Activate Inside Page", 'ihc');?>
								<div class="extra-info"><?php _e("Use this option only if You properly set the 'View User Page'", 'ihc');?></div>
							</div>	
							<div class="ihc-spacewp_b_divs"></div>	
							<div class="ihc-user-list-row">
								<div class="ihc-label"><?php _e("Filter By Level", 'ihc');?></div>
								<div class="ihc-field">
									<input type="checkbox" id="filter_by_level" onClick="ihc_checkbox_div_relation(this, '#levels_in__wrap_div');ihc_preview_u_list();" />
								</div>
							</div>	
							<div class="ihc-user-list-row" id="levels_in__wrap_div" style="opacity: 0.5;">
								<div class="ihc-label"><?php _e("User's Levels:", 'ihc');?></div>
								<div class="ihc-field">
									<?php 
										$levels = get_option('ihc_levels');
										if ($levels){
											?>
											<select class="iump-form-select " onchange="ihc_writeTagValue_list_users(this, '#levels_in', '#ihc-select-level-view-values', 'ihc-level-select-v-');ihc_preview_u_list();">
											<?php 
											foreach ($levels as $id=>$level_arr){
												?>
													<option value="<?php echo $id;?>"><?php echo $level_arr['label'];?>
												<?php												
											}
											?>
											</select>
											<?php 
										} 
									?>
									
								</div>
								<div id="ihc-select-level-view-values"></div>
									<input type="hidden" value="" id="levels_in" />	
							</div>																		
						</div>						
					</div>
					<!-- /DISPLAY ENTRIES -->
					
					
					
					<!-- TEMPLATE -->
					<div class="ihc-column column-three">
						<h4 style="background: #1fb5ac;"><i class="fa-ihc fa-icon-temp-ihc"></i>Template</h4>
						<div class="ihc-settings-inner">
							<div class="ihc-user-list-row">
								<div class="ihc-label"><?php _e("Select Theme", 'ihc');?></div>
								<div class="ihc-field">
									<select id="theme" onChange="ihc_preview_u_list();"><?php 
										$themes = array('ihc-theme_1' => __('Theme', 'ihc') . ' 1',
														'ihc-theme_2' => __('Theme', 'ihc') . ' 2',
														'ihc-theme_3' => __('Theme', 'ihc') . ' 3',
														'ihc-theme_4' => __('Theme', 'ihc') . ' 4',
														'ihc-theme_5' => __('Theme', 'ihc') . ' 5',
														'ihc-theme_6' => __('Theme', 'ihc') . ' 6',
														'ihc-theme_7' => __('Theme', 'ihc') . ' 7',
														'ihc-theme_8' => __('Theme', 'ihc') . ' 8',
														'ihc-theme_9' => __('Theme', 'ihc') . ' 9',
														'ihc-theme_10' => __('Theme', 'ihc') . ' 10',
												);
										foreach ($themes as $k=>$v){
											$selected = ($meta_arr['theme']==$k) ? 'selected' : '';
											?>
											<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
											<?php 
										}
									?></select>
								</div>
							</div>
							<div class="ihc-user-list-row">
								<div class="ihc-label"><?php _e("Color Scheme", 'ihc');?></div>
								<div class="ihc-field">
		                            <ul id="colors_ul" class="colors_ul">
		                                <?php
		                                    $color_scheme = array('0a9fd8', '38cbcb', '27bebe', '0bb586', '94c523', '6a3da3', 'f1505b', 'ee3733', 'f36510', 'f8ba01');
		                                    $i = 0;
		                                    foreach ($color_scheme as $color){
		                                        if( $i==5 ) echo "<div class='clear'></div>";
		                                        $class = ($meta_arr['color_scheme']==$color) ? 'color-scheme-item-selected' : 'color-scheme-item';
		                                        ?>
		                                            <li class="<?php echo $class;?>" onClick="ihc_change_color_scheme(this, '<?php echo $color;?>', '#color_scheme');ihc_preview_u_list();" style="background-color: #<?php echo $color;?>;"></li>
		                                        <?php
		                                        $i++;
		                                    }
		                                ?>
										<div class='clear'></div>
		                            </ul>
		                            <input type="hidden" id="color_scheme" value="<?php echo $meta_arr['color_scheme'];?>" />								
								</div>
							</div>
							<div class="ihc-user-list-row">
								<div class="ihc-label"><?php _e("Columns", 'ihc');?></div>
								<div class="ihc-field">
									<select id="columns" onChange="ihc_preview_u_list();"><?php 
										for ($i=1; $i<7; $i++){
											$selected = ($i==$meta_arr['columns']) ? 'selected' : '';
											?>
											<option value="<?php echo $i;?>" <?php echo $selected;?>><?php echo $i . __(" Columns", 'ihc')?></option>
											<?php 
										}
									?></select>
								</div>
							</div>	
							<div class="ihc-user-list-row" style="padding-top: 10px;">	
								<div class="ihc-label"><?php _e("Additional Options", 'ihc');?></div>
							</div>	
							<div class="ihc-user-list-row">							
								<?php $checked = (empty($meta_arr['align_center'])) ? '' : 'checked';?>
								<input type="checkbox" id="align_center" <?php echo $checked;?> onClick="ihc_preview_u_list();"/> <?php _e("Align the Items Centered", 'ihc');?>
							</div>	
							
							<div class="ihc-user-list-row">	
								<?php $checked = ($meta_arr['include_fields_label']) ? 'checked' : '';?>
								<input type="checkbox" class="" id="include_fields_label" onClick="ihc_preview_u_list();" <?php echo $checked;?> />  
								<?php _e('Show Fields Label', 'ihc');?> 								
							</div>																	
						</div>
					</div>
					<!-- /TEMPLATE -->
					
					<!-- SLIDER -->
					<div class="ihc-column column-four" style="width:50%;">
						<h4 style="background: rgba(240, 80, 80, 1.0);"><i class="fa-ihc fa-icon-slider-ihc"></i><?php _e("Slider ShowCase", 'ihc');?></h4>
						<div class="ihc-settings-inner">
							<div class="ihc-user-list-row">
								<?php $checked = (empty($meta_arr['slider_set'])) ? '' : 'checked';?>
								<input type="checkbox" <?php echo $checked;?> id="slider_set" onClick="ihc_checkbox_div_relation(this, '#slider_options');ihc_preview_u_list();"/> <b><?php echo __('Show as Slider', 'ihc');?></b>
	                 		 	<div class="extra-info" style="display:block;"><?php echo __('If Slider Showcase is used, Filter Showcase is disabled.', 'ihc');?></div> 
							</div>
							<div style="opacity:0.5" id="slider_options" >
							
						     <div class="splt-1">
								<div class="ihc-user-list-row">
									<div class="ihc-label"><?php _e('Items per Slide:', 'ihc');?></div>
									<div class="ihc-field">
										<input type="number" min="1" id="items_per_slide" onChange="ihc_preview_u_list();" onKeyup="ihc_preview_u_list();" value="<?php echo $meta_arr['items_per_slide'];?>" class=""/>
									</div>
								</div>
								<div class="ihc-user-list-row">
									<div class="ihc-label"><?php _e('Slider Timeout:', 'ihc');?></div>
									<div class="ihc-field">
										<input type="number" min="1" id="speed" onChange="ihc_preview_u_list();" onKeyup="ihc_preview_u_list();" value="<?php echo $meta_arr['speed'];?>" class=""/>
									</div>
								</div>
								<div class="ihc-user-list-row">
									<div class="ihc-label"><?php _e('Pagination Speed:', 'ihc');?></div>
									<div class="ihc-field">
										<input type="number" min="1" id="pagination_speed" onChange="ihc_preview_u_list();" onKeyup="ihc_preview_u_list();" value="<?php echo $meta_arr['pagination_speed'];?>" class=""/>
									</div>
								</div>
								 <div class="ihc-user-list-row">
	                          		<div class="ihc-label"><?php _e('Pagination Theme:', 'ihc');?></div>
	                          		<div class="ihc-field">
		                          		<select id="pagination_theme" onChange="ihc_preview_u_list();" style="min-width:162px;"><?php 
		                          			$array = array(
		                          								'pag-theme1' => __('Pagination Theme 1', 'ihc'),
		                          								'pag-theme2' => __('Pagination Theme 2', 'ihc'),
		                          								'pag-theme3' => __('Pagination Theme 3', 'ihc'),
		                          							);
		                          			foreach ($array as $k=>$v){
		                          				$selected = ($k==$meta_arr['pagination_theme']) ? 'selected' : '';
		                          				?>
		                          				<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
		                          				<?php 
		                          			}
		                          		?>
		                                </select>
	                          		</div>
	                          </div>
	                          
	                            <div class="ihc-user-list-row">
	                          		<div class="ihc-label"><?php _e('Animation Slide In', 'ihc');?></div>
	                          		<div class="ihc-field">
	                                  <select onChange="ihc_preview_u_list();" id="animation_in" style="min-width:162px;">
										  <option value="none">None</option>
										  <option value="fadeIn">fadeIn</option>
										  <option value="fadeInDown">fadeInDown</option>
										  <option value="fadeInUp">fadeInUp</option>
										  <option value="slideInDown">slideInDown</option>
										  <option value="slideInUp">slideInUp</option>
										  <option value="flip">flip</option>
										  <option value="flipInX">flipInX</option>
										  <option value="flipInY">flipInY</option>
										  <option value="bounceIn">bounceIn</option>
										  <option value="bounceInDown">bounceInDown</option>
										  <option value="bounceInUp">bounceInUp</option>
										  <option value="rotateIn">rotateIn</option>
										  <option value="rotateInDownLeft">rotateInDownLeft</option>
										  <option value="rotateInDownRight">rotateInDownRight</option>
										  <option value="rollIn">rollIn</option>
										  <option value="zoomIn">zoomIn</option>
										  <option value="zoomInDown">zoomInDown</option>
										  <option value="zoomInUp">zoomInUp</option>
									  </select>                          		
	                          		</div>
	                          	</div>
	                          
	                          
	                          <div class="ihc-user-list-row">
	                          		<div class="ihc-label"><?php _e('Animation Slide Out', 'ihc');?></div>
	                          		<div class="ihc-field">
	                                    <select onChange="ihc_preview_u_list();" id="animation_out" style="min-width:162px;">
										  <option value="none">None</option>
										  <option value="fadeOut">fadeOut</option>
										  <option value="fadeOutDown">fadeOutDown</option>
										  <option value="fadeOutUp">fadeOutUp</option>
										  <option value="slideOutDown">slideOutDown</option>
										  <option value="slideOutUp">slideOutUp</option>
										  <option value="flip">flip</option>
										  <option value="flipOutX">flipOutX</option>
										  <option value="flipOutY">flipOutY</option>
										  <option value="bounceOut">bounceOut</option>
										  <option value="bounceOutDown">bounceOutDown</option>
										  <option value="bounceOutUp">bounceOutUp</option>
										  <option value="rotateOut">rotateOut</option>
										  <option value="rotateOutUpLeft">rotateOutUpLeft</option>
										  <option value="rotateOutUpRight">rotateOutUpRight</option>
										  <option value="rollOut">rollOut</option>
										  <option value="zoomOut">zoomOut</option>
										  <option value="zoomOutDown">zoomOutDown</option>
										  <option value="zoomOutUp">zoomOutUp</option>
									  </select>                        		
	                          		</div>                          	
	                          </div>	
							</div>
							<div class="splt-2">	
								
								<div class="ihc-user-list-row">
	                          		<div class="ihc-label"><?php _e('Additional Options', 'ihc');?></div>
								</div>
								<div class="ihc-user-list-row">
									<?php $checked = (empty($meta_arr['bullets'])) ? '' : 'checked';?>
									<input type="checkbox" id="bullets" onClick="ihc_preview_u_list();" <?php echo $checked;?> /> <?php _e("Bullets", 'ihc');?>
								</div>
								<div class="ihc-user-list-row">
									<?php $checked = (empty($meta_arr['nav_button'])) ? '' : 'checked';?>
									<input type="checkbox" id="nav_button" onClick="ihc_preview_u_list();" <?php echo $checked;?> /> <?php _e("Nav Button", 'ihc');?>
								</div>	
								<div class="ihc-user-list-row">
									<?php $checked = (empty($meta_arr['autoplay'])) ? '' : 'checked';?>
									<input type="checkbox" id="autoplay" onClick="ihc_preview_u_list();" <?php echo $checked;?> /> <?php _e("AutoPlay", 'ihc');?>
								</div>	
								<div class="ihc-user-list-row">
									<?php $checked = (empty($meta_arr['stop_hover'])) ? '' : 'checked';?>
									<input type="checkbox" id="stop_hover" onClick="ihc_preview_u_list();" <?php echo $checked;?> /> <?php _e("Stop On Hover", 'ihc');?>
								</div>		
								<div class="ihc-user-list-row">
									<?php $checked = (empty($meta_arr['responsive'])) ? '' : 'checked';?>
									<input type="checkbox" id="responsive" onClick="ihc_preview_u_list();" <?php echo $checked;?> /> <?php _e("Responsive", 'ihc');?>
								</div>
								<div class="ihc-user-list-row">
									<?php $checked = (empty($meta_arr['autoheight'])) ? '' : 'checked';?>
									<input type="checkbox" id="autoheight" onClick="ihc_preview_u_list();" <?php echo $checked;?> /> <?php _e("Auto Height", 'ihc');?>
								</div>																	
								<div class="ihc-user-list-row">
									<?php $checked = (empty($meta_arr['lazy_load'])) ? '' : 'checked';?>
									<input type="checkbox" id="lazy_load" onClick="ihc_preview_u_list();" <?php echo $checked;?> /> <?php _e("Lazy Load", 'ihc');?>
								</div>
								<div class="ihc-user-list-row">
									<?php $checked = (empty($meta_arr['loop'])) ? '' : 'checked';?>
									<input type="checkbox" id="loop" onClick="ihc_preview_u_list();" <?php echo $checked;?> /> <?php _e("Play in Loop", 'ihc');?>
								</div>																				
							</div>	
	                         
		        			<div class="clear"></div>																												
							</div>
						</div>
					</div>
					<!-- /SLIDER -->
		        <div class="clear"></div>
					<!-- ENTRY INFO -->
					<div class="ihc-column column-two" style="float:none; width:100%;">
                  		<h4 style="background: #9972b5;"><i class="fa-ihc fa-icon-entryinfo-ihc"></i><?php _e('Entry User Fields', 'ihc');?></h4>
				  		<div class="ihc-settings-inner">
				  			<div class="ihc-user-list-row">
				  				<?php 
				  					$fields = array('user_login' => 'Username', 
				  									'ihc_avatar' => 'Avatar',
				  									'user_email' => 'Email', 
				  									'ihc_sm' => 'Social Media', 
				  									'first_name'=>'First Name',
				  									'last_name' => 'Last Name',
				  									);
				  					$defaults = explode(',', $meta_arr['user_fields']);
				  					$reg_fields = ihc_get_user_reg_fields();
				  					$exclude = array('pass1', 'pass2', 'tos', 'recaptcha', 'confirm_email', 'ihc_social_media', 'payment_select');
									foreach ($reg_fields as $k=>$v){
										if (!in_array($v['name'], $exclude)){
											if (isset($v['native_wp']) && $v['native_wp']){
												$extra_fields[$v['name']] = __($v['label'], 'ihc');
											} else {
												$extra_fields[$v['name']] = $v['label'];
											}
											if (empty($extra_fields[$v['name']])){
												unset($extra_fields[$v['name']]);	
											}
										}										
									}
									
				  					$fields_arr = array_merge($fields, $extra_fields);
				  					
				  					foreach ($fields_arr as $k=>$v){
				  						$checked = (in_array($k, $defaults)) ? 'checked' : '';
				  						$color = (in_array($v, $fields)) ? '#0a9fd8' : '#000';
				  						?>
				  						<div class="ihc-memberslist-fields" style="color: <?php echo $color;?>;">
				  							<input type="checkbox" <?php echo $checked;?> value="<?php echo $k;?>" onClick="ihc_make_inputh_string(this, '<?php echo $k;?>', '#user_fields');ihc_preview_u_list();" /> <?php echo $v;?>
				  						</div>
				  						<?php 
				  					}
				  				?>
				  				<input type="hidden" value="<?php echo $meta_arr['user_fields'];?>" id="user_fields" />
				  			</div>				  			
				  		</div>	                    				  		
				  	</div>
					<!-- /ENTRY INFO -->
				</div>
		        <div class="clear"></div>
			</div>
			
			<div class="ihc-user-list-shortcode-wrapp">
		        <div class="content-shortcode">
		            <div>
		                <span style="font-weight:bolder; color: #fff; font-style:italic; font-size:13px;"><?php echo __('ShortCode :', 'ihc');?> </span>
		                <span class="the-shortcode"></span>
		            </div>
		            <div style="margin-top:10px;">
		                <span style="font-weight:bolder; color: #fff; font-style:italic; font-size:13px;"><?php echo __('PHP Code:', 'ihc');?> </span>
		                <span class="php-code"></span>
		            </div>
		        </div>
		    </div>
	    
	    	<div class="ihc-user-list-preview">
			    <div class="box-title">
			        <h2><i class="fa-ihc fa-icon-eyes-ihc"></i><?php echo __('Preview', 'ihc');?></h2>
			            <div class="actions-preview pointer">
						    <a onclick="jQuery('#preview').slideToggle();" class="btn btn-mini content-slideUp">
			                    <i class="fa-ihc fa-icon-cogs-ihc"></i>
			                </a>
						</div>
			        <div class="clear"></div>
			    </div>
			    <div id="preview" class="ihc-preview"></div>
			</div>

	</div>
<?php 
	break;
	case 'settings':
		//SETTINGS
		if (!empty($_POST['ihc_save'])){
			///save
			ihc_save_update_metas('listing_users');
		}
		$meta_arr = ihc_return_meta_arr('listing_users');
		?>
	<div class="ihc-user-list-wrap">
		<div class="iump-page-title">Ultimate Membership Pro - <span class="second-text"><?php _e('Members List', 'ihc');?></span>
	</div>		
		<form action="" method="post">
			<div class="ihc-stuffbox">
				<h3><?php _e('Responsive Settings', 'ihc');?></h3>
				<div class="inside">	
					<div class="iump-form-line">
						<span class="iump-labels-special"><?php _e('Screen Max-Width:', 'ihc');?> 479px</span>
						<div class="ihc-general-options-link-pages"><select name="ihc_listing_users_responsive_small"><?php 
							$arr = array( '1' => 1 . __(' Columns', 'ihc'),
										  '2' => 2 . __(' Columns', 'ihc'),
										  '3' => 3 . __(' Columns', 'ihc'),
										  '4' => 4 . __(' Columns', 'ihc'),
									 	  '5' => 5 . __(' Columns', 'ihc'),
									 	  '6' => 6 . __(' Columns', 'ihc'),
										  '0' => __('Auto', 'ihc'),
							);
							foreach ($arr as $k=>$v){
								$selected = ($meta_arr['ihc_listing_users_responsive_small']==$k) ? 'selected' : '';
								?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php 	
							}
						?>
						</select></div>				
					</div>
					<div class="iump-form-line">
						<span class="iump-labels-special"><?php _e('Screen Min-Width:', 'ihc');?> 480px <?php _e(" and Screen Max-Width:");?> 767px</span>
						<div class="ihc-general-options-link-pages"><select name="ihc_listing_users_responsive_medium"><?php 
							foreach ($arr as $k=>$v){
								$selected = ($meta_arr['ihc_listing_users_responsive_medium']==$k) ? 'selected' : '';
								?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php 	
							}
						?>
						</select></div>				
					</div>
					<div class="iump-form-line">
						<span class="iump-labels-special"><?php _e('Screen Min-Width:', 'ihc');?> 768px <?php _e(" and Screen Max-Width:");?> 959px</span>
						<div class="ihc-general-options-link-pages"><select name="ihc_listing_users_responsive_large"><?php 
							foreach ($arr as $k=>$v){
								$selected = ($meta_arr['ihc_listing_users_responsive_large']==$k) ? 'selected' : '';
								?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php 	
							}
						?>
						</select></div>				
					</div>								
					<div class="ihc-wrapp-submit-bttn">
		            	<input type="submit" value="<?php _e('Save changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
		            </div>												
				</div>
			</div>	
				
			<div class="ihc-stuffbox">
				<h3><?php _e('Settings', 'ihc');?></h3>
				<div class="inside">	
					<div class="iump-form-line">
						<div class="ihc-general-options-link-pages">
							<span class="iump-labels-onbutton"><?php _e("Open 'Inside Page' in new Window", 'ihc');?></span>
							<label class="iump_label_shiwtch iump-onbutton">
								<?php $checked = ($meta_arr['ihc_listing_users_target_blank']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_listing_users_target_blank');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_listing_users_target_blank'];?>" name="ihc_listing_users_target_blank" id="ihc_listing_users_target_blank" /> 				
						</div>				
					</div>	
					<div class="ihc-wrapp-submit-bttn">
			           	<input type="submit" value="<?php _e('Save changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
			           </div>														
				</div>
			</div>				
			
			<div class="ihc-stuffbox">
				<h3><?php _e('Custom CSS', 'ihc');?></h3>
				<div class="inside">	
					<div class="iump-form-line">
						<span class="iump-labels-special"><?php _e('Add !important;  after each style option and full style path to be sure that it will take effect!', 'ihc');?></span>
						<div class="ihc-general-options-link-pages"><textarea name="ihc_listing_users_custom_css" style="width: 100%; height: 100px;"><?php echo stripslashes($meta_arr['ihc_listing_users_custom_css']);?></textarea></div>				
					</div>	
					<div class="ihc-wrapp-submit-bttn">
		            	<input type="submit" value="<?php _e('Save changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
		            </div>												
				</div>		
			</div>			
		</form>
	</div>
		<?php 
		break;
	case 'inside_page':
		//SETTINGS
		if (!empty($_POST['ihc_save'])){
			///save
			ihc_save_update_metas('listing_users_inside_page');
		}
		$meta_arr = ihc_return_meta_arr('listing_users_inside_page');
		?>
		<div class="ihc-user-list-wrap">
			<div class="iump-page-title">Ultimate Membership Pro - <span class="second-text"><?php _e('Members List Inside Page', 'ihc');?></span>
		</div>	
			<form action="" method="post">
				<div class="ihc-stuffbox">
					<h3><?php _e('Content', 'ihc');?></h3>
					<div class="inside">	
						<div>
							<span class="iump-labels-onbutton" style="float:left; padding-right:5px; box-sizing:border-box; width:10%;"><?php _e('Content:', 'ihc');?></span>
							<div class="iump-wp_editor" style="float:left; width: 70%;">
							<?php wp_editor(stripslashes($meta_arr['ihc_listing_users_inside_page_content']), 'ihc_listing_users_inside_page_content', array('textarea_name'=>'ihc_listing_users_inside_page_content', 'editor_height'=>200));?>
							</div>
							<div style="float:left; width:10%; color:#999; padding-left:10px; box-sizing:border-box; ">
								<?php 
									$constants = array( '{AVATAR_HREF}' => '', 
														'{username}'=>'', 
														'{user_email}'=>'', 
														'{first_name}'=>'', 
														'{last_name}'=>'',
														'{level_list}'=>'',	
														'{blogname}'=>'', 
														'{blogurl}'=>'', 
														'{IHC_SOCIAL_MEDIA_LINKS}' => '', );
									$extra_constants = ihc_get_custom_constant_fields();
									foreach ($constants as $k=>$v){
									?>
										<div><?php echo $k;?></div>
									<?php 	
									}
									?>
										<h4><?php _e('Custom Fields Constants', 'ihc');?></h4>
									<?php 
									foreach ($extra_constants as $k=>$v){
									?>
										<div><?php echo $k;?></div>
									<?php 	
									}								
								?>
							</div>
							<div class="ihc-clear"></div>						
						</div>
						<div class="ihc-wrapp-submit-bttn">
			            	<input type="submit" value="<?php _e('Save changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
			            </div>							
					</div>
				</div>
				<div class="ihc-stuffbox">
					<h3><?php _e('Custom CSS', 'ihc');?></h3>
					<div class="inside">	
						<div class="iump-form-line">
							<span class="iump-labels-special"><?php _e('Add !important;  after each style option and full style path to be sure that it will take effect!', 'ihc');?></span>
							<div class="ihc-general-options-link-pages"><textarea name="ihc_listing_users_inside_page_custom_css" style="width: 100%; height: 100px;"><?php echo stripslashes($meta_arr['ihc_listing_users_inside_page_custom_css']);?></textarea></div>				
						</div>	
						<div class="ihc-wrapp-submit-bttn">
			            	<input type="submit" value="<?php _e('Save changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
			            </div>								
					</div>
				</div>
			</form>
			
		<?php 
		break;
	}

	