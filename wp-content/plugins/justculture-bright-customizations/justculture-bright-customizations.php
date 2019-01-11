<?php
    /*
    Plugin Name: Just Culture Bright Customizations
    Plugin URI: http://www.aura-softare.com/
    Description: justculture-bright-customizations
    Author: Aura Software
    Version: 1.9.14
    Author URI: http://www.aura-software.com/

    Source code created by Aura Software, LLC is licensed under a
    Attribution-NoDerivs 3.0 Unported United States License
    http://creativecommons.org/licenses/by-nd/3.0/
    */

$plugin_root = dirname (__FILE__);

require_once($plugin_root.'/oe_bcrypt.php');
require_once($plugin_root.'/license-keys.php');
require_once($plugin_root.'/coupon-report.php');
require_once($plugin_root.'/woocommerce-passwords.php');

# https://bitbucket.org/aura_software/justculture-bright-customizations/issues/146/on-failed-login-lost-password-link-takes
function reset_pass_url() {
    $siteURL = get_option('siteurl');
    return "{$siteURL}/lost-password";
}
add_filter( 'lostpassword_url',  'reset_pass_url', 11, 0 );


class oeBrightCustomizations {
  static function oePasswordCheck($check,$password,$hash,$user_id) {
	if (!empty($user_id)) {
	  $use_store_password = get_user_meta($user_id, 'use_store_password',true);  
	  $passHash = get_user_meta($user_id, 'store_password',true);  

	  bright_log('in oe_password_check');

	  if(!empty($use_store_password)) {
		$bcrypt = new Bcrypt(10);
		$verify = $bcrypt->verify($password, $passHash);
		return $verify;
	  } 
	} 
	return $check;
  }

  static function oeCheckStorePassword($user_id) {
	if (/* the first block is for the password reset form you get from lost password */
		(isset( $_POST['password_1'] ) &&
		 isset( $_POST['password_2'] ) &&
		 isset( $_POST['reset_key'] ) &&
		 $_POST['password_1'] === $_POST['password_2']) ||
		/* I believe this is from the profile edit page */
		(isset( $_POST['password_1'] ) &&
		 isset( $_POST['password_2'] ) &&
		 isset( $_POST['password_current'] ) &&
		 $_POST['password_1'] === $_POST['password_2']) ||
		/* probably the woocommerce reset page */
		(isset ($_POST['pass1']) &&
		 isset ($_POST['pass2']) &&
		 $_POST['pass1'] === $_POST['pass2'])) {
      bright_log($_POST);
      bright_log("in oe_check_store_password for user {$user_id}, removing use_store_password entry");
      delete_user_meta( $user_id, 'use_store_password');
      /* see also function capture_ihc_password_reset( $args ) */
    }
  }

  /**
   * turn off automated order autocompleting
   */

  static function setOrderAutocompleteDefault($default) {
    return false;
  }
}

add_filter('bright_woocommerce_integration_autocomplete_order_default','oeBrightCustomizations::setOrderAutocompleteDefault');


add_filter('check_password','oeBrightCustomizations::oePasswordCheck',10,4);


function oe_profile_update( $user_id ) {
  oeBrightCustomizations::oeCheckStorePassword($user_id);
}

add_action( 'profile_update', 'oe_profile_update' );

add_action( 'password_reset', 'oe_password_reset', 10, 2 );

function oe_password_reset( $user, $new_pass ) {
  oeBrightCustomizations::oeCheckStorePassword($user->ID);
  // Do something before password reset.
}


add_filter( 'wp_mail', 'capture_ihc_password_reset' );

/**
 * if you use the ihc-pass-reset shortcode from the indeed-membership-pro plugin,
 * you need to capture the password reset email to delete the use_store_password wp_usermeta value 
 */
function capture_ihc_password_reset( $args ) {

  $email = 	$args['to'];
  $subject = $args['subject'];

  if (preg_match ( '/Reset Password request/', $subject)) {
    $user = get_user_by('email',$email);
    if (!empty($user)) 
      delete_user_meta( $user->ID, 'use_store_password');
  }
      
  return $args;
}

function justculture_load_bright_customization_styles() {
  wp_register_style('justculture_custom',
					plugins_url('justculture-bright-customizations/justculture-bright-customizations.css'));
  wp_enqueue_style('justculture_custom');

  wp_enqueue_script('jc_bright_custom_js',
					plugins_url('justculture-bright-customizations/justculture-bright-customizations.js'),
                    array('bright')
                    );


}
add_action('wp_enqueue_scripts', 'justculture_load_bright_customization_styles');

global $bright_embedder_templates;

$bright_embedder_templates['justculture-woocommerce-courselist'] = <<<EOF
<div class="woocommerce">
<h2>{{#if attributes.title}}
{{attributes.title}}
{{else}}
My Course Registrations
{{/if}}</h2>
<table class="shop_table shop_table_responsive my_account_orders">
  <thead>
  <tr>
    <th>Course</th>
    <th>Status</th>
    <th>Score</th>
	<th>Take Course</th>
{{#if attributes.certificate}}
	<th>Certificate</th>
{{/if}}
  </tr>
  </thead>
  <tbody>
	{{#bSortCourses this courses sortBy="title"}}
{{#if registration}}
  <tr>
    <td>{{title}}</td>
    <td>
{{#if registration.complete}}
{{registration.complete}}, {{registration.success}}
{{/if}}
</td>
    <td>{{registration.score}}</td>
    <td>{{#courselist-launchbutton this 'Launch Course'}}{{/courselist-launchbutton}}</td>
{{#if attributes.certificate}}
    <td>
{{#compare attributes.certificate 'complete' operator="=="}}
{{#compare registration.complete attributes.certificate_value operator="=="}}
<a href="/certificate?bright_course_id={{course_guid}}">Certificate</a>
{{/compare}}
{{/compare}}
{{#compare attributes.certificate 'success' operator="=="}}
{{#compare registration.success attributes.certificate_value operator="=="}}
<a href="/certificate?bright_course_id={{course_guid}}">Certificate</a>
{{/compare}}
{{/compare}}
    </td>
{{/if}}
  </tr>
{{else}}
{{#if this.user.meta.legacy_aspx_user}}
{{#compare custom.legacy_aspx_user this.user.meta.legacy_aspx_user operator="=="}}
  <tr>
    <td>{{title}}</td>
    <td>
{{#if registration}}
{{#if registration.complete}}
{{registration.complete}}, {{registration.success}}
{{/if}}
{{else}}
{{/if}}
   </td>
    <td>{{registration.score}}</td>
    <td>{{#courselist-launchbutton this}}{{/courselist-launchbutton}}</td>
</tr>
{{/compare}}
{{/if}}
{{/if}}
{{/bSortCourses}}
  </tbody>
</table>
</div>
EOF;

