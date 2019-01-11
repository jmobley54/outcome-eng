<?php
/*
Plugin Name: WooCommerce Order Export and More
Plugin URI: http://www.jem-products.com
Description: Export your woocommerce orders and more with this free plugin
Version: 2.0.10
WC requires at least: 3.0.0
WC tested up to: 3.5.1
Author: JEM Plugins
Author URI: http://www.jem-products.com
Text Domain: order-export-and-more-for-woocommerce
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define ( 'JEM_EXP_PLUGIN_PATH' , plugin_dir_path( __FILE__ ) );
define('JEM_EXP_DOMAIN', 'order-export-and-more-for-woocommerce');
define( 'JEM_EXP_URL', plugin_dir_url( __FILE__ ) );

//only proceed if we are in admin mode!
if( ! is_admin() ){
	return;
}

// FREEMIUS
if ( ! function_exists( 'jemxplite_fs' ) ) {
    // Create a helper function for easy SDK access.
    function jemxplite_fs() {
        global $jemxplite_fs;

        if ( ! isset( $jemxplite_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $jemxplite_fs = fs_dynamic_init( array(
                'id'                  => '3035',
                'slug'                => 'order-export-and-more-for-woocommerce',
                'type'                => 'plugin',
                'public_key'          => 'pk_09640761558789ca4a5964d550528',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'JEM_EXPORT_MENU',
                    'account'        => false,
                    'contact'        => false,
                    'support'        => false,
                    'parent'         => array(
                        'slug' => 'woocommerce',
                    ),
                ),
            ) );
        }

        return $jemxplite_fs;
    }

    // Init Freemius.
    jemxplite_fs();
    // Signal that SDK was initiated.
    do_action( 'jemxplite_fs_loaded' );
}

function jemxplite_fs_custom_connect_message_on_update(
    $message,
    $user_first_name,
    $plugin_title,
    $user_login,
    $site_link,
    $freemius_link
) {
    return sprintf(
        __( 'Hey %1$s' ) . ',<br>' .
        __( 'never miss an important update -- opt-in to our security and feature updates notifications, and non-sensitive diagnostic tracking with freemius.com.', 'order-export-and-more-for-woocommerce' ),
        $user_first_name,
        '<b>' . $plugin_title . '</b>',
        '<b>' . $user_login . '</b>',
        $site_link,
        $freemius_link
    );
}

jemxplite_fs()->add_filter('connect_message_on_update', 'jemxplite_fs_custom_connect_message_on_update', 10, 6);
//END FREEMIUS
//Globals
global $jem_export_globals;

//At activation, languages are NOT loaded! so we need to do this somewhere else and only add them if they do not exist


function order_export_more_plugin_deactivation() {
  delete_option( 'jemx_products_option' );
  delete_option( 'jemx_orders_option' );
  delete_option( 'jemx_customers_option' );
  delete_option( 'jemx_shipping_option' );
  delete_option( 'jemx_coupons_option' );
  delete_option( 'jemx_categories_option' );
  delete_option( 'jemx_tags_option' );
}
register_deactivation_hook( __FILE__, 'order_export_more_plugin_deactivation' );


//This handles internationalization
function load_jem_export_lite_textdomain() {
    error_log('loading langauges');
    load_plugin_textdomain( JEM_EXP_DOMAIN, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
}

load_plugin_textdomain( JEM_EXP_DOMAIN, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );


//add_action( 'plugins_loaded', 'load_jem_export_lite_textdomain' );


$entities = array();
$entities[] = "Product";
$entities[] = "Order";
$entities[] = "Customer";
$entities[] = "Shipping";
$entities[] = "Coupon";
$entities[] = "Categories";
$entities[] = "Tags";

//Create an array of which entities are active
$active = array();
$active["Product"] = true;
$active["Order"] = true;

$jem_export_globals['entities'] = $entities;
$jem_export_globals['active'] = $active;

//Include the basic stuff
include_once(JEM_EXP_PLUGIN_PATH . 'inc/jem-exporter.php');
include_once(JEM_EXP_PLUGIN_PATH . 'inc/BaseEntity.php');

//include the entities
foreach($jem_export_globals['entities'] as $entity){
	include_once(JEM_EXP_PLUGIN_PATH . 'inc/' . $entity . '.php');

}

/**
 * Loads the right js & css assets
*/
function load_jem_exp_scripts(){

    //TODO we should only load these if we are on the plugin page

    //@simon - Nov '18 only load these scripts if we are on our page
    if (!isset($_GET['page']) || ($_GET['page'] != 'JEM_EXPORT_MENU')){
        return;
    }
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');


 	//Need the jquery CSS files
	global $wp_scripts;
	$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
	// Admin styles for WC pages only
	wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
	wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );
	
	
	wp_enqueue_style('dashicons');
		
	wp_enqueue_script( 'jem-css',  plugin_dir_url( __FILE__ ). 'js/main.js' );
	wp_enqueue_style( 'jem-css',  plugin_dir_url( __FILE__ ). 'css/jem-export-lite.css' );
}


add_action('admin_enqueue_scripts', 'load_jem_exp_scripts');

//TODO does this get called ALL the time or only when we're on our admin pages??
$jem_exporter_lite = new JEM_export_lite();

//=========   function for ajax call
function ajax_call_for_save_sorting(){
?>
<script type="text/javascript">

        jQuery(".sortable_table").sortable({
            update: function (ev, tbody) {
            var current_table_name = jQuery('.checkbox-class:checked').val();
            var obj = {};
            var counter = 0;
            jQuery('.'+current_table_name+' > tbody  > tr').each(function() {
                    if(jQuery(this).attr('data-key') != ""){
                    var get_place_holder = jQuery(this).attr('data-key');}
//                   console.log(get_place_holder);
                    if(get_place_holder != "undefined"){
                        counter++;
                        obj[get_place_holder] = counter;
                    }
                    
            });
            var form_data = {
                         action : 'savefieldorder',
                         pass_obj : obj,
                         pass_current_table_name : current_table_name,
                 };
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: form_data,
                    success: function(data) {
                    }
                });
            }
        });
        
</script>
<?php
}
//add_action('admin_footer','ajax_call_for_save_sorting');

function saving_field_order()
{
    global $post;
    $get_obj = $_POST['pass_obj'];
    $obj_product = json_encode($get_obj);
    $get_current_table_name = $_POST['pass_current_table_name'];
    if($get_current_table_name == 'Product'){
        update_option('product_option', $obj_product);
    }
    if($get_current_table_name == 'Order'){
        update_option('jemx_order_option', $obj_product);
    }
    if($get_current_table_name == 'Customer'){
        update_option('customer_option', $obj_product);
    }
    if($get_current_table_name == 'Shipping'){
        update_option('shipping_option', $obj_product);
    }
    if($get_current_table_name == 'Coupons'){
        update_option('coupons_option', $obj_product);
    }
    if($get_current_table_name == 'Categories'){
        update_option('categories_option', $obj_product);
    }
    if($get_current_table_name == 'Tags'){
        update_option('tags_option', $obj_product);
    }
    exit;
}
add_action('wp_ajax_savefieldorder', 'saving_field_order');
add_action('wp_ajax_nopriv_savefieldorder', 'saving_field_order');

function my_function() {
    global $wpdb;
    $form_data = $_POST['form_data'];
    $formInput = $_POST['post_id'];
    // $new_data =  unserialize($form_data);
    // $data['name'] = 'harish '.$formInput;

    $product = 'Product';
    $order = 'Order';

    $product_array = array();
    $order_array = array();
    if($formInput == 'Product')
    {
        foreach ($form_data as $data) {
            # code...
            //print_r($data['name']); die();

            $type =  explode('[', $data['name']);

            if ($type[0] == 'Product_fields') {
                # code...
                $parsed = get_string_between($data['name'], '[', ']');
                $product_array[$parsed] = 'on';
            }
        }
         update_option('jemx_product_option' , $product_array);
    }
    else
    {
        foreach ($form_data as $data) 
        {
           $type =  explode('[', $data['name']);
            if ($type[0] == 'Order_fields') {
            # code...
            $parsed = get_string_between($data['name'], '[', ']');
            $order_array[$parsed] = 'on';
           }
        }
       update_option('jemx_order_option' , $order_array );   
    }
    
    die('1'); 
}


add_action("wp_ajax_my_function", "my_function");
add_action("wp_ajax_nopriv_my_function", "my_function");

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
?>