<?php 
if (!class_exists('ListingUsers')){
	class ListingUsers{
		private $args = array();
		private $total_pages = 0;
		private $users = array();
		private $div_parent_id = '';
		private $li_width = '';
		private $user_fields = array();
		private $total_users;
		private $single_item_template = '';
		private $general_settings = array();
		private $link_user_page = '';
		private $fields_label = array();
		private $permalink_type = '';
		
		public function __construct($input=array()){
			/*
			 * @param array
			 * @return none
			 */
			if (empty($input)){
				return;
			} else {
				$this->args = $input;
				$this->general_settings = ihc_return_meta_arr('listing_users');	
				$link_user_page = get_option('ihc_general_register_view_user');
				if (!empty($link_user_page)){
					$link_user_page = get_permalink($link_user_page);			
					if (!empty($link_user_page)){
						$this->link_user_page = $link_user_page;
					}
					$this->permalink_type = get_option('permalink_structure');
				}
			}
		}
		
		public function run(){
			/*
			 * @param none
			 * @return string
			 */
			if (empty($this->args)){				
				return;
			}

			$output = '';
			$html = '';
			$js = '';
			$css = '';
			$js_after_html = '';
			$pagination = '';
			
			////// FILTER BY LEVELs
			if (!empty($this->args['filter_by_level']) && !empty($this->args['levels_in'])){
				if (strpos($this->args['levels_in'], ',')!==FALSE){
					$inner_join_levels = explode(',', $this->args['levels_in']);
				} else {
					$inner_join_levels = array($this->args['levels_in']);
				}
			} else {
				$inner_join_levels = array();
			}
			
			////////// ORDER
			$order_by = $this->args['order_by'];
			if ($order_by=='random'){
				$order_by = '';
			}
			$order_type = $this->args['order_type'];
			
			//////////TOTAL USERS
			$this->total_users = $this->get_users($order_by, $order_type, -1, -1, TRUE, $inner_join_levels);
			if ($this->total_users>$this->args['num_of_entries']){
				$this->total_users = $this->args['num_of_entries'];
			}
			
			//limit && offset
			if (empty($this->args['slider_set'])){
				//// NO SLIDER + PAGINATION
				if (!empty($this->args['current_page'])){
					$current_page = $this->args['current_page'];
					$offset = ( $current_page - 1 ) * $this->args['entries_per_page']; //start from
				} else {
					$offset = 0;
				}
				$limit = $this->args['entries_per_page'];
				if ($offset + $limit>$this->total_users){
					$limit = $this->total_users - $offset;
				}			
			} else {
				////SLIDER
				$offset = 0;
				$limit = $this->args['num_of_entries'];				
			}
			
			///GETTING USER IDS
			$user_ids = $this->get_users($order_by, $order_type, (int)$offset, (int)$limit, FALSE, $inner_join_levels);
			if (empty($user_ids)){
				return;//no users available
			}
			////SET USERS DATA
			$this->set_users_data($user_ids);
			
			$this->single_item_template = IHC_PATH .'public/listing_users/themes/' . $this->args['theme'] . "/index.php";
			
			///SET FIELDS LABEL
			$this->set_fields_label();
			
			if (!empty($this->users) && file_exists($this->single_item_template)){
				$html .= $this->create_the_html();
				$js .= $this->create_the_js();
				$css .= $this->create_the_css();
				$js_after_html .= $this->create_the_js_after_html();
			}				
			
			if (empty($this->args['slider_set']) && $this->args['entries_per_page']<$this->total_users){
				///adding pagination
				$pagination .= $this->print_pagination();
			}
			
			$output = $css . $js . $pagination . $html . $js_after_html;
			return $output;
		}
		
		private function set_users_data($user_ids){
			/*
			 * @param array
			 * @return none
			 */
			$this->user_fields = explode(',', $this->args['user_fields']);
			if ($this->args['order_by']=='random'){
				shuffle($user_ids);
			}
			foreach ($user_ids as $k=>$id){
				foreach ($this->user_fields as $field){
					if (empty($users[$id][$field])){
						$user_data = get_userdata($id);
						if (isset($user_data->$field)){
							$this->users[$id][$field] = $user_data->$field;
						} else {
							@$this->users[$id][$field] = get_user_meta($id, $field, TRUE);
						}
					}
				}
			}
		}
		
		private function set_fields_label(){
			/*
			 * @param none
			 * @return none
			 */
			$fields_data = ihc_get_user_reg_fields();
			foreach ($this->user_fields as $field){
				$key = ihc_array_value_exists($fields_data, $field, 'name');
				if ($key && !empty($fields_data[$key]) && !empty($fields_data[$key]['label'])){
					$this->fields_label[$field] = $fields_data[$key]['label'];
				}				
			}
		}
		
		private function get_users($order_by, $order_type, $offset=-1, $limit=-1, $count=FALSE, $inner_join_levels=array()){
			/*
			 * GETTING USERS FROM DB, COUNT USERS FROM DB
			 * @param: string, string, int, int, boolean, array
			 * @return array
			 */
			global $wpdb;
			$data = ihc_get_admin_ids_list();
			$not_in = implode(',', $data);
			
			$q = 'SELECT';
			if ($count){
				if (!empty($inner_join_levels)){
					$q .= " COUNT(DISTINCT b.user_id) as count_val";
				} else {
					$q .= " COUNT(a.ID) as count_val";
				}
			} else {
				if (!empty($inner_join_levels)){
					$q .= " DISTINCT b.user_id as user_id";
				} else {
					$q .= " ID";
				}
			}
			$q .= " FROM " . $wpdb->prefix ."users as a";
			if (!empty($inner_join_levels)){
				$q .= " INNER JOIN " . $wpdb->prefix . "ihc_user_levels as b";
				$q .= " ON a.ID=b.user_id";
			}	
			$q .= " WHERE 1=1";
			if (!empty($inner_join_levels)){
				$q .= " AND (";
				for ($i=0; $i<count($inner_join_levels); $i++){
					if ($i>0){
						$q .= " OR";
					}
					$q .= " b.level_id='" . $inner_join_levels[$i] . "'";
				}
				$q .= ") ";
				$q .= " AND b.start_time<DATE(NOW())";
				$q .= " AND b.expire_time>DATE(NOW())";		
			}
			
			//EXCLUDE ADMINISTRATORS
			if (!empty($not_in)){
				$q .= " AND a.ID NOT IN ('" . $not_in . "')";
			}
			
			if ($order_type && $order_by){
				$q .= " ORDER BY a." . $order_by . " " . $order_type;		
			}
		
		
			if ($limit>-1 && $offset>-1){
				$q .= " LIMIT " . $limit . " OFFSET " . $offset;
			}
				
			$data = $wpdb->get_results($q);
		
			if ($count){
				if (isset($data[0]) && isset($data[0]->count_val)){
					return $data[0]->count_val;
				} 
				return 0;
			} else {
				$return = array();
				if ($data && is_array($data)){
					foreach ($data as $obj){
						if (!empty($inner_join_levels) && isset($obj->user_id)){
							$return[] = $obj->user_id;					
						} else if (isset($obj->ID)){
							$return[] = $obj->ID;
						}
					}			
				}
				return $return;
			}
			return $data;
		}
		
		private function create_the_js_after_html(){
			/*
			 * @param
			 * @return string
			 */
			$str = '';
			if (!empty($this->args['slider_set'])){
				$total_pages = count($this->users) / $this->args['items_per_slide'];
					
				if ($total_pages>1){
					$navigation = (empty($this->args['nav_button'])) ? 'false' : 'true';
					$bullets = (empty($this->args['bullets'])) ? 'false' : 'true';
					if (empty($this->args['autoplay'])){
						$autoplay = 'false';
						$autoplayTimeout = 5000;
					} else {
						$autoplay = 'true';
						$autoplayTimeout = $this->args['speed'];
					}
					$autoheight = (empty($this->args['autoheight'])) ? 'false' : 'true';
					$stop_hover = (empty($this->args['stop_hover'])) ? 'false' : 'true';
					$loop = (empty($this->args['loop'])) ? 'false' : 'true';
					$responsive = (empty($this->args['responsive'])) ? 'false' : 'true';
					$lazy_load = (empty($this->args['lazy_load'])) ? 'false' : 'true';
					$animation_in = (($this->args['animation_in'])=='none') ? 'false' : "'{$this->args['animation_in']}'";
					$animation_out = (($this->args['animation_out'])=='none') ? 'false' : "'{$this->args['animation_out']}'";
					$slide_pagination_speed = $this->args['pagination_speed'];
						
					$str .= "<script>
												jQuery(document).ready(function() {
													var owl = jQuery('#" . $this->div_parent_id . "');
													owl.owlCarousel({
															items : 1,
															mouseDrag: true,
															touchDrag: true,
													
															autoHeight: $autoheight,
													
															animateOut: $animation_out,
															animateIn: $animation_in,
													
															lazyLoad : $lazy_load,
															loop: $loop,
													
															autoplay : $autoplay,
															autoplayTimeout: $autoplayTimeout,
															autoplayHoverPause: $stop_hover,
															autoplaySpeed: $slide_pagination_speed,
													
															nav : $navigation,
															navSpeed : $slide_pagination_speed,
															navText: [ '', '' ],
													
															dots: $bullets,
															dotsSpeed : $slide_pagination_speed,
													
															responsiveClass: $responsive,
															responsive:{
																0:{
																	nav:false
																},
																450:{
																	nav : $navigation
																}
															}
													});	
												});
					</script>";
				}
			}
			return $str;
		}
	
		private function create_the_css(){
			/*
			 * @param none
			 * @return string
			 */
			//add the themes and the rest of CSS here...
			$str = '';			
			if (!empty($this->args['slider_set']) && !defined('IHC_SLIDER_LOAD_CSS')){
				///// SLIDER CSS
				$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'public/listing_users/assets/css/owl.carousel.css">';
				$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'public/listing_users/assets/css/owl.theme.css">';
				$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'public/listing_users/assets/css/owl.transitions.css">';
				define('IHC_SLIDER_LOAD_CSS', TRUE);
			}
			if (!empty($this->args['theme'])){
				///// THEME
				$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'public/listing_users/themes/' . $this->args['theme'] . '/style.css">';
			}
			if (!defined('IHC_COLOR_CSS_FILE')){
				////// COLOR EXTERNAL CSS
				$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'public/listing_users/assets/css/layouts.css">';
				define('IHC_COLOR_CSS_FILE', TRUE);
			}			
			$str .= '<style>';
			///// SLIDER COLORS
			if (!empty($this->args['color_scheme']) && !empty($this->args['slider_set'])){
				$str .= '
							.style_'.$this->args['color_scheme'].' .owl-theme .owl-dots .owl-dot.active span, .style_'.$this->args['color_scheme'].'  .owl-theme .owl-dots .owl-dot:hover span { background: #'.$this->args['color_scheme'].' !important; }
							.style_'.$this->args['color_scheme'].' .pag-theme1 .owl-theme .owl-nav [class*="owl-"]:hover{ background-color: #'.$this->args['color_scheme'].'; }
							.style_'.$this->args['color_scheme'].' .pag-theme2 .owl-theme .owl-nav [class*="owl-"]:hover{ color: #'.$this->args['color_scheme'].'; }
							.style_'.$this->args['color_scheme'].' .pag-theme3 .owl-theme .owl-nav [class*="owl-"]:hover{ background-color: #'.$this->args['color_scheme'].';}
						';
			}		
			////// ALIGN CENTER
			if (!empty($this->args['align_center'])) {
				$str .= '#'.$this->div_parent_id.' ul{text-align: center;}';
			}
			///// CUSTOM CSS
			if (!empty($this->general_settings['ihc_listing_users_custom_css'])){
				$str .= stripslashes($this->general_settings['ihc_listing_users_custom_css']);
			}
			//// RESPONSIVE 
			if (!empty($this->general_settings['ihc_listing_users_responsive_small'])){
				$width = 100 / $this->general_settings['ihc_listing_users_responsive_small'];
				$str .= '
						@media only screen and (max-width: 479px){
							#' . $this->div_parent_id . ' ul li{
								width: calc(' . $width . '% - 1px) !important;
							}
						}
				';				
			}
			if (!empty($this->general_settings['ihc_listing_users_responsive_medium'])){
				$width = 100 / $this->general_settings['ihc_listing_users_responsive_medium'];
				$str .= '
						@media only screen and (min-width: 480px) and (max-width: 767px){
							#' . $this->div_parent_id . ' ul li{
								width: calc(' . $width . '% - 1px) !important;
							}
						}
				';				
			}
			if (!empty($this->general_settings['ihc_listing_users_responsive_large'])){
				$width = 100 / $this->general_settings['ihc_listing_users_responsive_large'];
				$str .= '
						@media only screen and (min-width: 768px) and (max-width: 959px){
							#' . $this->div_parent_id . ' ul li{
								width: calc(' . $width . '% - 1px) !important;
							}
						}
				';				
			}
			$str .= '</style>';
			return $str;		
		}	

		private function create_the_js(){
			/*
			 * @param
			 * @return string
			 */
			$str = '';
			if (!empty($this->args['slider_set']) && !defined('IHC_SLIDER_LOAD_JS')){
				$str .= '<script src="' . IHC_URL . 'public/listing_users/assets/js/owl.carousel.js" ></script>';
				define('IHC_SLIDER_LOAD_JS', TRUE);
			}				
			return $str;
		}
		
		private function print_pagination(){
			/*
			 * @param none
			 * @return string
			 */
			$str = '';
			$current_page = (empty($this->args['current_page'])) ? 1 : $this->args['current_page'];
			$this->total_pages = ceil($this->total_users/$this->args['entries_per_page']);
			$url = IHC_PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			$str = '';
			
			if ($this->total_pages<=5){
				//show all the links
				for ($i=1; $i<=$this->total_pages; $i++){
					$show_links[] = $i;
				}
			} else {
				// we want to show only first, last, and the first neighbors of current page
				$show_links = array(1, $this->total_pages, $current_page, $current_page+1, $current_page-1);
			}
			
			for ($i=1; $i<=$this->total_pages; $i++){
				if (in_array($i, $show_links)){
					$href = (defined('IS_PREVIEW')) ? '#' : add_query_arg('ihcUserList_p', $i, $url);
					$selected = ($current_page==$i) ? '-selected' : '';
					$str .= "<a href='$href' class='ihc-user-list-pagination-item" . $selected . "'>" . $i . '</a>';		
					$dots_on = TRUE;
				} else {
					if (!empty($dots_on)){
						$str .= '<span class="ihc-user-list-pagination-item-break">...</span>';
						$dots_on = FALSE;
					}
				}
			}
			/// Back link
			if ($current_page>1){
				$prev_page = $current_page - 1;
				$href = (defined('IS_PREVIEW')) ? '#' : add_query_arg('ihcUserList_p', $prev_page, $url);
				$str = "<a href='" . $href . "' class='ihc-user-list-pagination-item'> < </a>" . $str;
			}
			///Forward link
			if ($current_page<$this->total_pages){
				$next_page = $current_page + 1;
				$href = (defined('IS_PREVIEW')) ? '#' : add_query_arg('ihcUserList_p', $next_page, $url);
				$str = $str . "<a href='" . $href . "' class='ihc-user-list-pagination-item'> > </a>";
			}
						
			//Wrappers
			$str = "<div class='ihc-user-list-pagination'>" . $str . "</div><div class='ihc-clear'></div>";
			return $str;
		}

		private function create_the_html(){
			/*
			 * @param
			 * @return string
			 */
			$str = '';
			$total_items = count($this->users);
			$items_per_slide = (empty($this->args['slider_set'])) ? $total_items : $this->args['items_per_slide'];
			
			include $this->single_item_template;
			if (empty($list_item_template)){
				return '';
			}
			
			$this->li_width = 'calc(' . 100/$this->args['columns'] . '% - 1px)';
			$i = 1;
			$breaker_div = 1;
			$new_div = 1;
			$color_class = (empty($this->args['color_scheme'])) ? 'style_0a9fd8' : 'style_' . $this->args['color_scheme'];
			$parent_class = (empty($this->args['slider_set'])) ? 'ihc-content-user-list' : 'ihc-carousel-view';//carousel_view
			$num = rand(1, 10000);
			$this->div_parent_id = 'indeed_carousel_view_widget_' . $num;
			$arrow_wrapp_id = 'wrapp_arrows_widget_' . $num;
			$ul_id = 'ihc_list_users_ul_' . rand(1, 10000);
				
			///// WRAPPERS
			$extra_class = (empty($this->args['pagination_theme'])) ? '' : $this->args['pagination_theme'];
			$str .= "<div class='' id='ihc_public_list_users_" . rand(1, 10000) . "'>";
			$str .= "<div class='$color_class'>";
			$str .= "<div class='" . $this->args['theme'] . " " . $extra_class . "'>";
			$str .= "<div class='ihc-wrapp-list-users'>";
			$str .= "<div class='$parent_class' id='$this->div_parent_id' >";
			
			////// ITEMS
			foreach ($this->users as $uid=>$arr){
				if (!empty($new_div)){
					$div_id = $ul_id . '_' . $breaker_div;
					$str .= "<ul id='$div_id' class=''>"; /////ADDING THE UL
				}
			
				$str .= $this->print_item($uid, $list_item_template, $socials_arr);///// PRINT SINGLE ITEM
			
				if ($i % $items_per_slide==0 || $i==$total_items){
					$breaker_div++;
					$new_div = 1;
					$str .= "<div class='ihc-clear'></div></ul>";
				} else {
					$new_div = 0;
				}
				$i++;
			}
				
			///// CLOSE WRAPPERS
			$str .= '</div>'; /// end of $parent_class
			$str .= '</div>'; /// end of ihc-wrapp-list-users
			$str .= '</div>'; /// end of $args['theme'] . " " . $args['pagination_theme']
			$str .= '</div>'; /// end of $color_class
			$str .= '</div>'; //// end of ihc_public_list_users_
			
			return $str;
		}

		private function print_item($uid, $template, $socials_arr){
			/*
			 * SINGLE ITEM
			 * @param int, string, array
			 * @return string
			 */
			$fields = $this->user_fields;
			
			$str = '';
			$str .= "<li style='width: $this->li_width' >";
			
			//AVATAR
			$this->users[$uid]['ihc_avatar'] = ihc_get_avatar_for_uid($uid);
			
			///STANDARD FIELDS
			$standard_fields = array(
										"user_login" => "IHC_USERNAME",
										"first_name" => "IHC_FIRST_NAME",
										"last_name" => "IHC_LAST_NAME",
										"user_email" => "IHC_EMAIL",
										"ihc_avatar" => "IHC_AVATAR",
 			);
			foreach ($standard_fields as $k=>$v){
				$data = '';
				if (in_array($k, $fields)){
					$data = $this->users[$uid][$k];
				}
				$template = str_replace($v, $data, $template);
				$key = array_search($k, $fields);
				unset($fields[$key]);
			}

			///SOCIAL MEDIA STUFF
			if (in_array('ihc_sm', $fields)){
				$key = array_search('ihc_sm', $fields);
				unset($fields[$key]);
				$social_media_string = '';
				$sm_arr = array(
						'ihc_fb' => 'FB',
						'ihc_tw' => 'TW',
						'ihc_in' => 'LIN',
						'ihc_tbr' => 'TBR',
						'ihc_ig' => 'INS',
						'ihc_vk' => 'VK',
						'ihc_goo' => 'GP',
				);
				$sm_base = array(
									'ihc_fb' => 'https://www.facebook.com/profile.php?id=',
									'ihc_tw' => 'https://twitter.com/intent/user?user_id=',
									'ihc_in' => 'https://www.linkedin.com/profile/view?id=',
									'ihc_tbr' => 'https://www.tumblr.com/blog/',
									'ihc_ig' => 'http://instagram.com/_u/',
									'ihc_vk' => 'http://vk.com/id',
									'ihc_goo' => 'https://plus.google.com/',									
								);
				foreach ($sm_arr as $k=>$v){
					$data = get_user_meta($uid, $k, TRUE);
					if (!empty($data)){
						$data = $sm_base[$k] . $data;
						$social_media_string .= str_replace($v, $data, $socials_arr[$k]);
					}
				}
				$template = str_replace("IHC_SOCIAL_MEDIA", $social_media_string, $template);
			}
			
			/// SOME EXTRA FIELDS
			
			$extra_fields = '';
			if ($fields){				
				foreach ($fields as $value){
					$extra_fields_str = '';
					if (!empty($this->users[$uid][$value])){
						if (!empty($this->args['include_fields_label']) && !empty($this->fields_label[$value])){
							$extra_fields_str .= '<span class="ihc-user-list-label">' . $this->fields_label[$value] . ' </span>';
							$extra_fields_str .= '<span class="ihc-user-list-label-result">';
						}else{
							$extra_fields_str .= '<span class="ihc-user-list-result">';
						}
						if (is_array($this->users[$uid][$value])){
							$extra_fields_str .= implode(',', $this->users[$uid][$value]);
						} else {
							$extra_fields_str .= $this->users[$uid][$value];
						}
						$extra_fields_str .= '</span>';
						$extra_fields_str .= '<div class="ihc-clear"></div>';
						if (!empty($extra_fields_str)){
							$extra_fields .= '<div class="member-extra-single-field">' . $extra_fields_str . '</div>';
						}					
					}					
				}
			}
			$template = str_replace('IHC_EXTRA_FIELDS', $extra_fields, $template);

			/// LINK TO USER PAGE
			$link = '#';
			if (!empty($this->args['inside_page']) && !empty($this->link_user_page)){
				$target_blank = (empty($this->general_settings['ihc_listing_users_target_blank'])) ? '' : 'target="_blank"';
				$username = urlencode($this->users[$uid]['user_login']);
				if ($this->permalink_type){
					$link = trailingslashit($this->link_user_page) . $username;
				} else {
					$link = add_query_arg('ihc_name', $username, $this->link_user_page);
				}				
				$link = ' href="' . $link . '" ' . $target_blank;			
			}
			$template = str_replace("#POST_LINK#", $link, $template);
			
			$str .= $template;
			$str .= '</li>';
			return $str;
		}
		
		
	}
}