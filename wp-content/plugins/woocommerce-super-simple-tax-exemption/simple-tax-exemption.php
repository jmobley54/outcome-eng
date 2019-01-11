<?php
   /*
   Plugin Name: Woocommerce Super Simple Tax Exemption
   Plugin URI: http://www.bobbiejwilson.com/woocommerce-super-simple-tax-exempt
   Description: A plugin to add simple tax exemption to the Woocommerce checkout page. Records the Tax Exempt ID to the order meta.
   Version: 1.3
   Author: Bobbie Wilson
   Author URI: http://www.bobbiejwilson.com
   License: GPL2
   */

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

/*Tax Exempt Checkout for Woocommerce */

add_action('woocommerce_before_order_notes', 'taxexempt_before_order_notes' );
function taxexempt_before_order_notes( $checkout ) {

        echo '<div style="clear: both"></div>

        <h3>Tax Exempt Details</h3>';

        woocommerce_form_field( 'tax_exempt_checkbox', array(
            'type'          => 'checkbox',
            'class'         => array('tax-exempt-checkbox'),
            'label'         => __('Tax Exempt'),
            ), $checkout->get_value( 'tax_exempt_checkbox' ));


        woocommerce_form_field( 'tax_exempt_id', array(
            'type'          => 'text',
            'class'         => array('tax-exempt-id', 'update_totals_on_change'),
            'label'         => __('Tax Exempt ID: <i><font color="red">Please enter your tax exempt id# in the box below to have tax removed from the order. You must send your tax exempt certificate to <a href=mailto:support@outcome-eng.com><font color="blue">support@outcome-eng.com</font></a>. Once your certificate is received we can process your order. Thank you! </font></i>'),
            ), $checkout->get_value( 'tax_exempt_id' ));
}
add_action( 'woocommerce_checkout_update_order_review', 'taxexempt_checkout_update_order_review');
function taxexempt_checkout_update_order_review( $post_data ) {
        global $woocommerce;

        $woocommerce->customer->set_is_vat_exempt(FALSE);

        parse_str($post_data);

        if ( isset($tax_exempt_checkbox) && isset($tax_exempt_id) && $tax_exempt_checkbox == '1' && !empty($tax_exempt_id))
            $woocommerce->customer->set_is_vat_exempt(true);
    }
/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'tax_exempt_field_update_order_meta');

function tax_exempt_field_update_order_meta( $order_id ) {
    if ($_POST['tax_exempt_id']) update_post_meta( $order_id, 'Tax Exempt ID', esc_attr($_POST['tax_exempt_id']));
}
/**
 * Display field value on the order edition page
 **/
add_action( 'woocommerce_admin_order_data_after_billing_address', 'tax_exempt_field_display_admin_order_meta', 10, 1 );
 
function tax_exempt_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Tax Exempt ID').':</strong> ' . get_post_meta( $order->id, 'Tax Exempt ID', true ) . '</p>';
}

/*Enqueue the tax exempt trigger script*/
function woocommerce_tax_exempt_script() {
    wp_enqueue_style('tax-exempt-css', plugins_url('/css/tax-exempt.css', __FILE__ ));
    wp_enqueue_script('tax_exempt', plugins_url('/js/tax-exempt.js', __FILE__ ), array('jquery'), '1.0', true );
}
add_action('init', 'woocommerce_tax_exempt_script', 100);
}
?>