<?php 
/*
 * Login form   [ihc-login-form] - ihc_login_form
 * LogOut Link   [ihc-logout-link] - ihc_logout_link
 * Register Form   [ihc-register] - ihc_register_form
 * Locker   [ihc-hide-content] - ihc_hide_content_shortcode
 * Reset Password Form   [ihc-pass-reset] - ihc_lost_pass_form
 * User Page   [ihc-user-page] - ihc_user_page_shortcode
 * Subscription Plan   [ihc-select-level] - ihc_print_level_link
 * User Data [ihc-user] - ihc_print_user_data
 * User Listing [ihc-list-users] - ihc_public_listing_users 
 * View User Page [ihc-view-user-page] - ihc_public_view_user_page
 */
add_shortcode( 'ihc-login-form', 'ihc_login_form' );
add_shortcode( 'ihc-logout-link', 'ihc_logout_link' );
add_shortcode( 'ihc-register', 'ihc_register_form' );
add_shortcode( 'ihc-hide-content', 'ihc_hide_content_shortcode' );
add_shortcode( 'ihc-pass-reset', 'ihc_lost_pass_form' );
add_shortcode( 'ihc-user-page', 'ihc_user_page_shortcode' );
add_shortcode( 'ihc-select-level', 'ihc_user_select_level' );
add_shortcode( 'ihc-level-link', 'ihc_print_level_link' );
add_shortcode( 'ihc-lgoin-fb', 'ihc_print_fb_login' );
add_shortcode( 'ihc-user', 'ihc_print_user_data');
add_shortcode( 'ihc-list-users', 'ihc_public_listing_users');
add_shortcode( 'ihc-visitor-inside-user-page', 'ihc_public_visitor_inside_user_page');

function ihc_login_form($attr=array()){
	/*
	 * @param array
	 * @return string
	 */
	///////////// LOGIN FORM
	$str = '';
	if (!IHCACTIVATEDMODE){
		$str .= ihc_public_notify_trial_version();
	}
	$msg = '';
	$user_type = ihc_get_user_type();
	if ($user_type!='unreg'){
		////////////REGISTERED USER
		if ($user_type=='pending'){
			//pending user
			$msg = ihc_correct_text(get_option('ihc_register_pending_user_msg', true));
			if ($msg){
				$str .= '<div class="ihc-login-pending">' . $msg . '</div>';
			}					
		} else {
			//already logged in
			if ($user_type=='admin'){
				$str .= '<div class="ihc-wrapp-the-errors"><div class="ihc-register-error">' . __('<strong>Admin Info</strong>: Loggin Form is not showing up when You\'re logged.', 'ihc') . '</div></div>';
			}
		}			
	} else {
		/////////////UNREGISTERED
		$meta_arr = ihc_return_meta_arr('login');
		$str .= ihc_print_form_login($meta_arr);
	}
	
	//print the message
	if (isset($_GET['ihc_success_login']) && $_GET['ihc_success_login']){
		/************************** SUCCESS ***********************/
		$msg .= get_option('ihc_login_succes');
		if (!empty($msg)){
			$str .= '<div class="ihc-login-success">' . ihc_correct_text($msg) . '</div>';
		}
	} else if (!empty($_GET['ihc_pending_email'])){
		/************************ PENDING EMAIL ********************/
		$login_faild = get_option('ihc_login_error_email_pending', true);
		if (empty($login_faild)){
			$arr = ihc_return_meta_arr('login-messages', false, true);
			print_r($arr);
			if (isset($arr['ihc_login_error_email_pending']) && $arr['ihc_login_error_email_pending']){
				$login_faild = $arr['ihc_login_error_email_pending'];
			} else {
				$login_faild = __('Error', 'ihc');
			}
		}
		$str .= '<div class="ihc-login-error">' . ihc_correct_text($login_faild) . '</div>';
	} else if (isset($_GET['ihc_login_fail']) && $_GET['ihc_login_fail']){
		/************************** FAIL *****************************/
		$login_faild = ihc_correct_text( get_option('ihc_login_error', true) );
		if (empty($login_faild)){
			$arr = ihc_return_meta_arr('login-messages', false, true);
			if (isset($arr['ihc_login_error']) && $arr['ihc_login_error']){
				$login_faild = $arr['ihc_login_error'];
			} else {
				$login_faild = __('Error', 'ihc');
			}			
		}
		$str .= '<div class="ihc-login-error">' . ihc_correct_text($login_faild) . '</div>';
	} else if (isset($_GET['ihc_login_pending']) && $_GET['ihc_login_pending']){
		/*********************** PENDING ******************************/
		$str .= '<div class="ihc-login-pending">' . ihc_correct_text(get_option('ihc_login_pending', true)) . '</div>';
	}
	return $str;
}


function ihc_logout_link($attr=array()){
	/*
	 * @param array
	 * @return string
	 */
	///////////// LOGOUT FORM
	$str = '';
	if (is_user_logged_in()){
		$meta_arr = ihc_return_meta_arr('login');
		if($meta_arr['ihc_login_custom_css']){
			$str .= '<style>'.$meta_arr['ihc_login_custom_css'].'</style>';
		}
		$str .= '<div class="ihc-logout-wrap '.$meta_arr['ihc_login_template'].'">';
			$link = add_query_arg( 'ihcaction', 'logout', get_permalink() );
			$str .= '<a href="'.$link.'">'.__('Log Out', 'ihc').'</a>';
		$str .= '</div>';		
	}
	return $str;
}

function ihc_hide_content_shortcode($meta_arr=array(), $content=''){
	/*
	 * @param array, string
	 * @return string
	 */
	///GETTING USER TYPE
	$current_user = ihc_get_user_type();
	if ($current_user=='admin') return $content;//admin can view anything
	
	if (isset($meta_arr['ihc_mb_who'])){
		if ($meta_arr['ihc_mb_who']!=-1 && $meta_arr['ihc_mb_who']!=''){
			$target_users = explode(',', $meta_arr['ihc_mb_who']);
		} else {
			$target_users = FALSE;
		}
		
	} else {
		return do_shortcode($content);
	}
	
	////TESTING USER
	global $post;
	$block = ihc_test_if_must_block($meta_arr['ihc_mb_type'], $current_user, $target_users, @$post->ID);
	
	//IF NOT BLOCKING, RETURN THE CONTENT
	if (!$block) return do_shortcode($content);
	
	//LOCKER HTML
	if (isset($meta_arr['ihc_mb_template'])){
		include_once IHC_PATH . 'public/locker-layouts.php';
		return ihc_print_locker_template($meta_arr['ihc_mb_template']);			
	}
	
	//IF SOMEHOW IT CAME UP HERE, RETURN CONTENT
	return do_shortcode($content);	
}


function ihc_lost_pass_form(){
	/*
	 * @param none
	 * @return string
	 */
	$str = '';
	if (!is_user_logged_in()){
		$meta_arr = ihc_return_meta_arr('login');		
		$str .= ihc_print_form_password($meta_arr);
			
		global $ihc_reset_pass;
		if ($ihc_reset_pass){
			if ($ihc_reset_pass==1){
				//reset ok
				return get_option('ihc_reset_msg_pass_ok');
			} else {
				//reset error
				$err_msg = get_option('ihc_reset_msg_pass_err');
				if ($err_msg){
					$str .= '<div class="ihc-wrapp-the-errors">' . $err_msg . '</div>';
				}
			}
		}		
	}	
	return $str;
}

function ihc_user_page_shortcode($attr=array()){
	/*
	 * @param array
	 * @return string
	 */
	$str = '';
	if (is_user_logged_in()){
		
		require_once IHC_PATH . 'classes/ihcAccountPage.class.php';
		$obj = new ihcAccountPage();
		$tab = isset($_GET['ihc_ap_menu']) ? $_GET['ihc_ap_menu'] : '';
		$str .= $obj->print_page($tab);
	}
	return $str;
}

function ihc_register_form($attr=array()){
	/*
	 * @param array
	 * @return string
	 */
	$str = '';
	
	if (!IHCACTIVATEDMODE){
		$str .= ihc_public_notify_trial_version();
	}
	
	$user_type = ihc_get_user_type();
	if ($user_type=='unreg'){	
		///////ONLY UNREGISTERED CAN SEE THE REGISTER FORM
		
		if (isset($_GET['ihc_register'])) return;

			$template = get_option('ihc_register_template');
			$str .= '<style>' . get_option('ihc_register_custom_css') . '</style>';
			
			global $ihc_error_register;
			if (empty($ihc_error_register)){
				$ihc_error_register = array();
			}
			include_once IHC_PATH . 'classes/UserAddEdit.class.php';
			$args = array(
					'user_id' => false,
					'type' => 'create',
					'tos' => true,
					'captcha' => true,
					'action' => '',
					'is_public' => true,
					'register_template' => $template,
					'print_errors' => $ihc_error_register
			);
			$obj_form = new UserAddEdit();
			$obj_form->setVariable($args);//setting the object variables
			$str .= '<div class="iump-register-form '.$template.'">' . $obj_form->form() . '</div>';
	} else {
		//already logged in
		if ($user_type=='admin'){
			$str .= '<div class="ihc-wrapp-the-errors"><div class="ihc-register-error">' . __('<strong>Admin Info</strong>: Register Form is not showing up when You\'re logged.', 'ihc') . '</div></div>';
		}
	}
	return $str;
}

function ihc_user_select_level($template='', $custom_css=''){
	/*
	 * @param template string, custom css string, coupon field boolean
	 * @return string
	 */
	
	////////////////// AUTHORIZE RECCURING PAYMENT
	if (!empty($_GET['ihc_authorize_fields']) && !empty($_GET['lid'])){
		if (isset($_POST['ihc_submit_authorize'])){
			global $current_user;
			$paid = ihc_pay_new_lid_with_authorize($current_user->ID, $_REQUEST);
			if ($paid){
				return __("Payment Complete", 'ihc');
			} else {
				return __("An error have occured. Please try again later!", 'ihc');
			}
		} else {
			if (!class_exists('ihcAuthorizeNet')){
				require_once IHC_PATH . 'classes/ihcAuthorizeNet.class.php';
			}			
			$auth_pay = new ihcAuthorizeNet();
			$str = '';
			$str .= '<form method="post" action="">';
			$str .= '<div id="ihc_authorize_r_fields">';
			$str .= '<div class="ihc_payment_details">'.__('Complete Payment with Authorize', 'ihc').'</div>';
			$str .=  $auth_pay->payment_fields();
			$str .= '</div>';
			$str .= '<input type="hidden" value="' . $_GET['lid'] . '" name="lid" />';
			if (!empty($_GET['ihc_coupon'])){
				$str .= '<input type="hidden" value="' . $_GET['ihc_coupon'] . '" name="ihc_coupon" />';
			}
			$str .= '<div>';
			$str .= indeed_create_form_element(array('type'=>'submit', 'name'=>'ihc_submit_authorize', 'value' => __('Submit', 'ihc'),
					'class' => 'button button-primary button-large', 'id'=>'ihc_submit_authorize' ));
			$str .= '</div>';
			$str .= '</form>';
			return $str;			
		}
	}
	////////////////// AUTHORIZE RECCURING PAYMENT
	
	$levels = get_option('ihc_levels');
	if ($levels){
		$register_url = '';
		$levels = ihc_reorder_arr($levels);
		$levels = ihc_check_show($levels);
		if (!$template){
			$template = get_option('ihc_level_template');
			if (!$template){
				$template = 'ihc_level_template_1';
			}
		}
		$register_page = get_option('ihc_general_register_default_page');
		if ($register_page){
			$register_url = get_permalink($register_page);
		}
		
		$fields = get_option('ihc_user_fields');
		///PRINT COUPON FIELD
		$num = ihc_array_value_exists($fields, 'ihc_coupon', 'name');
		$coupon_field = (empty($fields[$num]['display_public_ap'])) ? FALSE : TRUE;
		////PRINT SELECT PAYMENT
		$key = ihc_array_value_exists($fields, 'payment_select', 'name');
		$select_payment = (empty($fields[$key]['display_public_ap'])) ? FALSE : TRUE;
		
		$str = '';
		
		$u_type = ihc_get_user_type();
		if ($u_type!='unreg' && $u_type!='pending'){
			if ($coupon_field){
				$str .= "<div class='iump-form-line-register'>";
				$str .= "<label class='iump-labels-register'>" . __("Coupon Code", "ihc") . "</label>";
				$str .= "<input type='text' id='ihc_coupon' />";
				$str .= "</div>";
			}
				
			$default_payment = get_option('ihc_payment_selected');
			if ($select_payment){
				////
				$payments_available = ihc_get_active_payments_services();
				$register_fields_arr = ihc_get_user_reg_fields();
				$key = ihc_array_value_exists($register_fields_arr, 'payment_select', 'name');
		
				if (!empty($payments_available) && count($payments_available)>1 && !empty($register_fields_arr[$key]['display_public_ap'])){
					$str .= ihc_print_payment_select($default_payment, $register_fields_arr[$key], $payments_available, 0);
				}
				////
			}
				
			$the_payment_type = ( ihc_check_payment_available($default_payment) ) ? $default_payment : '';
			$str .= '<input type="hidden" name="ihc_payment_gateway" value="' . $the_payment_type . '" />';
		}
		include_once IHC_PATH . 'public/subscription-layouts.php';
		$str .= ihc_print_subscription_layout($template, $levels, $register_url, $custom_css, $select_payment);
		
		return $str;
	}
	return '';
}

function ihc_print_level_link( $attr, $content='', $print_payments ){
	/*
	 * @param array, string, boolean
	 * @return string
	 */
	///STRIPE PAYMENT
	if (isset($_POST['stripeToken']) && (empty($_GET['ihc_register']) || $_GET['ihc_register']!='create_message') ){
		ihc_pay_new_lid_with_stripe($_POST);//available in functions.php
		unset($_POST['stripeToken']);
	} else if (isset($_GET['ihc_success_bt'])){
		//bank transfer message
		add_filter('the_content', 'ihc_filter_print_bank_transfer_message', 79, 1);
	}
	
	if (!empty($content)){
		$str = $content;
	} else {
		$str =  __('Sign Up', 'ihc');
	}
	
	$href = '';
	if (!isset($attr['class'])){
		$attr['class'] = '';
	}

	$purchased = ihc_user_has_level(get_current_user_id(), $attr['id']);
	
	if ($purchased){
		return ' <div class="ihc-level-item-link ihc-purchased-level"><span class="'.$attr['class'].' " >' .__('Purchased', 'ihc'). '</span></div> ';
	} else {
		$url = FALSE;
		$u_type = ihc_get_user_type();
		if ($u_type!='unreg' && $u_type!='pending'){//is_user_logged_in()
			///////////////////////////////// REGISTERED USER
			$payments_available = ihc_get_active_payments_services(TRUE);
			$level_data = ihc_get_level_by_id($attr['id']);
			
			if (in_array('stripe', $payments_available) || get_option('ihc_payment_selected')=='stripe'){
				/****************** STRIPE *********************/
				if ($level_data['payment_type']=='payment'){
					add_filter("the_content", "ihc_add_stripe_public_form", 80, 1);//available in functions.php
				}
			} 
			
				$page = get_option('ihc_general_user_page');
				$url = get_permalink($page);
				$url = add_query_arg( 'ihcaction', 'paynewlid', $url );
				$url = add_query_arg( 'lid', $attr['id'], $url );
				$url = add_query_arg( 'urlr', urlencode(IHC_PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']), $url );				
			
				$onClick = 'ihc_buy_new_level_from_ap(\''.$level_data['label'].'\', \''.$level_data['price'].'\', '.$attr['id'].', \'' . $url . '\');';
				
			return '<div onClick="' . $onClick . '" class="ihc-level-item-link" style="cursor: pointer;">' . $str . '</div>';
				
		} else {
			//////////////////////////////// NEW USER
			if (isset($attr['register_page'])){
				$url = add_query_arg( 'lid', $attr['id'], $attr['register_page'] );
			} else {
				$page = get_option('ihc_general_register_default_page');
				$url = get_permalink($page);
				$url = add_query_arg( 'lid', $attr['id'], $url );
			}
			return '<div onClick="ihc_buy_new_level(\'' . $url . '\');" class="ihc-level-item-link" style="cursor: pointer;">' . $str . '</div>';
		}
		return $str;
	}
}

function ihc_print_user_data($attr){
	/*
	 * @param array
	 * @return string
	 */
	$str = '';
	if (!empty($attr['field'])){
		global $current_user;
		if (!empty($current_user->ID)){
			$search = "{" . $attr['field'] . "}";
			$return = ihc_replace_constants($search, $current_user->ID);	
			if ($search!=$return){
				$str = $return;
			}		
		}
	}
	return $str;
}

function ihc_public_listing_users($input=array()){
	/*
	 * @param array
	 * @return string
	 */
	$input['current_page'] = (empty($_REQUEST['ihcUserList_p'])) ? 1 : $_REQUEST['ihcUserList_p'];
	require_once IHC_PATH . 'classes/ListingUsers.class.php';
	$obj = new ListingUsers($input);
	$output = $obj->run();
	return $output;
}

function ihc_public_visitor_inside_user_page(){
	/*
	 * @param
	 * @return string
	 */
	if (!empty($_GET['ihc_name'])){
		$name = $_GET['ihc_name'];
	} else {
		$name = get_query_var('ihc_name');	
	}
		
	if (!empty($name)){
		$name = urldecode($name);
		$uid = ihc_get_user_id_by_user_login($name);
		if ($uid>0){
			$output = '';
			$css = '';
			$content = '';
			
			///AVATAR
			$avatar_url = ihc_get_avatar_for_uid($uid);
			
			///SOCIAL MEDIA ICONS WITH LINKS
			$sm_string = ihc_return_user_sm_profile_visit($uid);
			
			///CUSTOM CSS
			$data = get_option('ihc_listing_users_inside_page_custom_css');
			if (!empty($data)){
				$data = stripslashes($data);
				$css = '<style>' . $data . '</style>';
			}
			
			///CONTENT
			$data = get_option('ihc_listing_users_inside_page_content');
			$data = stripslashes($data);
			$content = ihc_replace_constants($data, $uid, FALSE, FALSE, array('{AVATAR_HREF}'=>$avatar_url, '{IHC_SOCIAL_MEDIA_LINKS}'=>$sm_string)); 
			
			$output .= $css;
			$output .= '<div class="ihc-public-wrapp-visitor-user">';
			$output .= $content;
			$output .= '</div>';
			return $output;
		}
	}
	return '';
}