<?php
/*
Plugin Name: Contact Form Email
Plugin URI: https://form2email.dwbooster.com/download
Description: Contact form that sends the data to email and also to a database list and CSV file.
Version: 1.2.77
Author: CodePeople
Author URI: https://form2email.dwbooster.com
Text Domain: contact-form-to-email
License: GPL
*/

define('CP_CFEMAIL_DEFER_SCRIPTS_LOADING', (get_option('CP_CFTE_LOAD_SCRIPTS',"1") == "1"?true:false));

define('CP_CFEMAIL_DEFAULT_form_structure', '[[{"name":"email","index":0,"title":"Email","ftype":"femail","userhelp":"","csslayout":"","required":true,"predefined":"","size":"medium"},{"name":"subject","index":1,"title":"Subject","required":true,"ftype":"ftext","userhelp":"","csslayout":"","predefined":"","size":"medium"},{"name":"message","index":2,"size":"large","required":true,"title":"Message","ftype":"ftextarea","userhelp":"","csslayout":"","predefined":""}],[{"title":"Contact Form","description":"","formlayout":"top_aligned"}]]');

define('CP_CFEMAIL_DEFAULT_fp_subject', 'Contact from the website...');
define('CP_CFEMAIL_DEFAULT_fp_inc_additional_info', 'false');
define('CP_CFEMAIL_DEFAULT_fp_return_page', get_site_url());
define('CP_CFEMAIL_DEFAULT_fp_message', "The following contact message has been sent:\n\n<%INFO%>\n\n");

define('CP_CFEMAIL_DEFAULT_cu_enable_copy_to_user', 'true');
define('CP_CFEMAIL_DEFAULT_cu_user_email_field', '');
define('CP_CFEMAIL_DEFAULT_cu_subject', 'Confirmation: Message received...');
define('CP_CFEMAIL_DEFAULT_cu_message', "Thank you for your message. We will reply you as soon as possible.\n\nThis is a copy of the data sent:\n\n<%INFO%>\n\nBest Regards.");
define('CP_CFEMAIL_DEFAULT_email_format','text');

define('CP_CFEMAIL_DEFAULT_vs_use_validation', 'true');

define('CP_CFEMAIL_DEFAULT_vs_text_is_required', 'This field is required.');
define('CP_CFEMAIL_DEFAULT_vs_text_is_email', 'Please enter a valid email address.');

define('CP_CFEMAIL_DEFAULT_vs_text_datemmddyyyy', 'Please enter a valid date with the format mm/dd/yyyy');
define('CP_CFEMAIL_DEFAULT_vs_text_dateddmmyyyy', 'Please enter a valid date with the format dd/mm/yyyy');
define('CP_CFEMAIL_DEFAULT_vs_text_number', 'Please enter a valid number.');
define('CP_CFEMAIL_DEFAULT_vs_text_digits', 'Please enter only digits.');
define('CP_CFEMAIL_DEFAULT_vs_text_max', 'Please enter a value less than or equal to %0%.');
define('CP_CFEMAIL_DEFAULT_vs_text_min', 'Please enter a value greater than or equal to %0%.');

define('CP_CFEMAIL_DEFAULT_cv_enable_captcha', 'true');
define('CP_CFEMAIL_DEFAULT_cv_width', '170');
define('CP_CFEMAIL_DEFAULT_cv_height', '65');
define('CP_CFEMAIL_DEFAULT_cv_chars', '5');
define('CP_CFEMAIL_DEFAULT_cv_font', 'font-1.ttf');
define('CP_CFEMAIL_DEFAULT_cv_min_font_size', '25');
define('CP_CFEMAIL_DEFAULT_cv_max_font_size', '30');
define('CP_CFEMAIL_DEFAULT_cv_noise', '190');
define('CP_CFEMAIL_DEFAULT_cv_noise_length', '4');
define('CP_CFEMAIL_DEFAULT_cv_background', 'ffffff');
define('CP_CFEMAIL_DEFAULT_cv_border', '000000');
define('CP_CFEMAIL_DEFAULT_cv_text_enter_valid_captcha', 'Please enter a valid captcha code.');


/* initialization / install */

include_once dirname( __FILE__ ) . '/classes/cp-base-class.inc.php';
include_once dirname( __FILE__ ) . '/cp-main-class.inc.php';

$cp_cfte_plugin = new CP_ContactFormToEmail;

register_activation_hook(__FILE__, array($cp_cfte_plugin,'install') ); 
add_action( 'media_buttons', array($cp_cfte_plugin, 'insert_button'), 11);
add_action( 'init', array($cp_cfte_plugin, 'data_management'));

function cfte_plugin_init() {
   load_plugin_textdomain( 'contact-form-to-email', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
   $ao_options = get_option('autoptimize_js_exclude',"seal.js, js/jquery/jquery.js");
   if (!strpos($ao_options,'stringify.js'))
      update_option('autoptimize_js_exclude',"jQuery.stringify.js,jquery.validate.js,".$ao_options);
}
add_action('plugins_loaded', 'cfte_plugin_init');

//START: activation redirection 
function cfte_activation_redirect( $plugin ) {
    if(
        $plugin == plugin_basename( __FILE__ ) &&
        (!isset($_POST["action"]) || $_POST["action"] != 'activate-selected') &&
        (!isset($_POST["action2"]) || $_POST["action2"] != 'activate-selected') 
      )
    {
        exit( wp_redirect( admin_url( 'admin.php?page=cp_contactformtoemail' ) ) );
    }
}
add_action( 'activated_plugin', 'cfte_activation_redirect' );
//END: activation redirection

if ( is_admin() ) {    
    add_action('admin_enqueue_scripts', array($cp_cfte_plugin,'insert_adminScripts'), 1);    
    add_filter("plugin_action_links_".plugin_basename(__FILE__), array($cp_cfte_plugin,'plugin_page_links'));   
    add_action('admin_menu', array($cp_cfte_plugin,'admin_menu') );
    add_action('enqueue_block_editor_assets', array($cp_cfte_plugin,'gutenberg_block') );
    add_action('wp_loaded', array($cp_cfte_plugin, 'data_management_loaded') );
} else {    
    add_shortcode( $cp_cfte_plugin->shorttag, array($cp_cfte_plugin, 'filter_content') );    
}  

// register gutemberg block
if (function_exists('register_block_type'))
{
    register_block_type('cfte/form-rendering', array(
                        'attributes'      => array(
                                'formId'    => array(
                                    'type'      => 'string'
                                ),
                                'instanceId'    => array(
                                    'type'      => 'string'
                                ),
                            ),
                        'render_callback' => array($cp_cfte_plugin, 'render_form_admin')
                    )); 
}

$codepeople_promote_banner_plugins[ 'contact-form-to-email' ] = array( 'plugin_name' => 'Contact Form Email', 'plugin_url'  => 'https://wordpress.org/support/plugin/contact-form-to-email/reviews/#new-post');
require_once 'banner.php';

// improve block
$codepeople_cftedk_banner_plugins[ 'contact-form-to-email' ] = array( 'plugin_name' => 'Contact Form Email', 'plugin_url'  => 'https://form2email.dwbooster.com/download');
require_once 'bannerdk.php';

// optional opt-in deactivation feedback
require_once 'cp-feedback.php';

// code for compatibility with third party scripts
add_filter('option_sbp_settings', 'cpcfte_sbp_fix_conflict' );
function cpcfte_sbp_fix_conflict($option)
{
    if(!is_admin())
    {
       if(is_array($option) && isset($option['jquery_to_footer'])) 
           unset($option['jquery_to_footer']);
    }
    return $option;
}

// elementor integration
include_once dirname( __FILE__ ) . '/controllers/elementor/cp-elementor-widget.inc.php';


?>