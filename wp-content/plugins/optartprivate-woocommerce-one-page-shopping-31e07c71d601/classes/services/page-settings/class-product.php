<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings;

use OptArt\WoocommerceOnePageShopping\Classes\Services\setting_provider;
use OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings\Datatypes\product as wc_product;

/**
 * Class product
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings
 */
class product extends page
{
    /**
     * @var wc_product
     */
    private $product;

    /**
     * Product class constructor
     * @param setting_provider $setting_provider
     */
    public function __construct( setting_provider $setting_provider )
    {
        parent::__construct( $setting_provider );

        global $post;
        $this->product = new wc_product( $post->ID, $this->setting_provider );
    }

    /**
     * Checks whether cart should be displayed for current product page
     * @return bool
     */
    public function display_cart()
    {
        $display_cart = $this->product->display_element( 'display-cart' );

        return $this->ops_enabled() && $display_cart;
    }

    /**
     * Checks whether checkout should be displayed for current product page
     * @return bool
     */
    public function display_checkout()
    {
        $display_checkout = $this->product->display_element( 'display-checkout' );

        return $this->ops_enabled() && $display_checkout;
    }

    /**
     * Checks whether item should be automatically added to cart on visit.
     * @return bool
     */
    public function add_to_cart()
    {
        $add_to_cart = $this->product->display_element( 'automatically-add-to-cart' );

        return $this->ops_enabled() && $add_to_cart;
    }

    /**
     * Checks whether OPS should be enabled for product category page
     * @return bool
     */
    public function ops_enabled()
    {
        $ops_enabled = $this->product->ops_enabled();

        return $ops_enabled;
    }
}
