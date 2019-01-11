<?php

/**
 * Allows for the sale of a specific post/page in WordPress through WooCommerce.
 *
 * @link                    pramadillo.com
 * @since                   2.0.0
 * @package                 Woocommerce_pay_per_post
 * @wordpress-plugin
 * Plugin Name:             WooCommerce Pay Per Post
 * Plugin URI:              pramadillo.com/plugins/woocommerce-pay-per-post
 * Description:             Allows for the sale of a specific post/page in WordPress through WooCommerce.
 * Version:                 2.1.16
 * WC requires at least:    2.6
 * WC tested up to:         3.5.3
 * Author:                  Pramadillo
 * Author URI:              pramadillo.com
 * License:                 GPL-2.0+
 * License URI:             http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:             woocommerce_pay_per_post
 * Domain Path:             /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( !function_exists( 'wcppp_freemius' ) ) {
    define( 'WC_PPP_PLUGIN_VERSION', '2.1.16' );
    require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-pay-per-post.php';
    require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
    function activate_woocommerce_pay_per_post()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-pay-per-post-activator.php';
        Woocommerce_Pay_Per_Post_Activator::activate();
    }
    
    function deactivate_woocommerce_pay_per_post()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-pay-per-post-deactivator.php';
        Woocommerce_Pay_Per_Post_Deactivator::deactivate();
    }
    
    register_activation_hook( __FILE__, 'activate_woocommerce_pay_per_post' );
    register_deactivation_hook( __FILE__, 'deactivate_woocommerce_pay_per_post' );
    /**
     * Create a helper function for easy SDK access.
     */
    function wcppp_freemius()
    {
        global  $wcppp_freemius ;
        
        if ( !isset( $wcppp_freemius ) ) {
            // Include Freemius SDK.
            require_once plugin_dir_path( __FILE__ ) . 'vendor/freemius/wordpress-sdk/start.php';
            try {
                $wcppp_freemius = fs_dynamic_init( array(
                    'id'             => '1664',
                    'slug'           => 'woocommerce-pay-per-post',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_3421f16894101749f184e4e1535da',
                    'is_premium'     => false,
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                    'days'               => 7,
                    'is_require_payment' => true,
                ),
                    'menu'           => array(
                    'slug'       => 'wc_pay_per_post',
                    'first-path' => 'admin.php?page=wc_pay_per_post-whats-new',
                    'support'    => false,
                ),
                    'is_live'        => true,
                ) );
            } catch ( Freemius_Exception $e ) {
                die( esc_html( $e->getMessage() ) );
            }
        }
        
        return $wcppp_freemius;
    }
    
    function run_woocommerce_pay_per_post()
    {
        wcppp_freemius();
        do_action( 'wcppp_freemius_loaded' );
        wcppp_freemius()->add_filter( 'plugin_icon', 'wcppp_fs_custom_icon' );
        $plugin = new Woocommerce_Pay_Per_Post();
        $plugin->run();
    }
    
    function wcppp_fs_custom_icon()
    {
        return dirname( __FILE__ ) . '/admin/img/icon.png';
    }

}

run_woocommerce_pay_per_post();