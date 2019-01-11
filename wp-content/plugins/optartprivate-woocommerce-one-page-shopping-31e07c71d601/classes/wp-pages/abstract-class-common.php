<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\WpPages;

use OptArt\WoocommerceOnePageShopping\Classes\Vendor\wp_helpers;

/**
 * Parent class for admin and frontpage classes
 */
abstract class common extends wp_helpers
{
    // nonce identifiers
    const ADD_TO_CART_NONCE = 'ops-add-to-cart';
    const ADD_TO_CART_NONCE_POST_ID = 'ops_add_to_cart_nonce';
    
    // version of woocommerce with big changes
    const WOOCOMMERCE_NEW_VERSION = '2.1';
    
    /**
     * Children classes are required to contain a method which is responsible
     * for implementing WordPress hooks
     */
    public abstract function _run();

    /**
     * Returns the path to templates inside current wp page
     * @return string
     */
    protected abstract function get_template_path();

    /**
     * Render the template basing on given path
     * @param $name
     * @param array $args
     */
    protected function render_template( $name, array $args = array() )
    {
        wc_get_template( $name, $args, '', trailingslashit( self::get_plugin_path() . $this->get_template_path() ) );
    }
}