<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings;

use OptArt\WoocommerceOnePageShopping\Classes\Services\setting_provider;
use OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings\Datatypes\category as wc_category;

/**
 * Class product
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings
 */
class category extends page
{
    /**
     * @var wc_category
     */
    private $category;

    /**
     * Category class constructor
     * @param setting_provider $setting_provider
     */
    public function __construct( setting_provider $setting_provider )
    {
        parent::__construct( $setting_provider );

        $cat = get_query_var( 'product_cat' );
        $term = get_term_by( 'slug', $cat, 'product_cat' );
        $this->category = new wc_category( $term->term_id, $this->setting_provider );
    }

    /**
     * Checks whether cart should be displayed for current category page
     * @return bool
     */
    public function display_cart()
    {
        $display_cart = $this->category->display_element( 'cat-display-cart' );

        return $this->ops_enabled() && $display_cart;
    }

    /**
     * Checks whether checkout should be displayed for current category page
     * @return bool
     */
    public function display_checkout()
    {
        $display_checkout = $this->category->display_element( 'cat-display-checkout' );

        return $this->ops_enabled() && $display_checkout;
    }

    /**
     * Checks whether OPS should be enabled for current category page
     * @return bool
     */
    public function ops_enabled()
    {
        $ops_enabled = $this->category->ops_enabled();

        return $ops_enabled;
    }
} 