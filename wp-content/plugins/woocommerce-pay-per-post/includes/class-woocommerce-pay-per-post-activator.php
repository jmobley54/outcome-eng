<?php

class Woocommerce_Pay_Per_Post_Activator
{
    protected  $plugin_name = 'wc_pay_per_post' ;
    /**
     * Create the stats table for page view expiration.
     * */
    public static function activate()
    {
        
        if ( !class_exists( 'WooCommerce' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( esc_html__( 'Please install and Activate WooCommerce.', 'wc_pay_per_post' ), 'Plugin dependency check', array(
                'back_link' => true,
            ) );
        }
        
        self::defaults();
    }
    
    private static function defaults()
    {
        global  $wpdb ;
        $post_types = get_option( 'wc_pay_per_post_custom_post_types' );
        // We check to see if we have any options set already, if we do not then we set our defaults.
        // We do this so we do not overwrite existing custom posts if user deactivated / reactivated.
        if ( !$post_types || count( $post_types ) === 0 ) {
            update_option( 'wc_pay_per_post_custom_post_types', array( 'post', 'page' ), false );
        }
        // Needs to upgrade?
        $sql = "SELECT count((1)) as `ct` FROM {$wpdb->postmeta} where meta_key ='woocommerce_ppp_product_id'";
        $exists = (bool) $wpdb->get_var( $sql );
        $db_version = get_option( 'wc_pay_per_post_db_version' );
        
        if ( $exists && (!$db_version || $db_version < 2) ) {
            update_option( 'wc_pay_per_post_needs_upgrade', 'true', false );
        } else {
            update_option( 'wc_pay_per_post_needs_upgrade', 'false', false );
            update_option( 'wc_pay_per_post_db_version', WC_PPP_PLUGIN_VERSION, false );
        }
    
    }

}