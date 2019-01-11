<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\WpPages;

use OptArt\WoocommerceOnePageShopping\Classes\Services\setting_provider;
use OptArt\WoocommerceOnePageShopping\Classes\Vendor\WoocommerceExtendedCategories\extended_categories;

class category_settings extends common
{
    /**
     * @var extended_categories
     */
    private $category_settings;

    /**
     * @var setting_provider
     */
    private $setting_provider;

    /**
     * Default constructor
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setting_provider = new setting_provider( $this->get_translator(), self::get_plugin_identifier() );
    }

    /**
     * Method sets up the hooks
     */
    public function _run()
    {
        $this->category_settings = new extended_categories();
        $this
            ->category_settings_add()
            ->category_settings->_run();
    }

    /**
     * Sets the template path for category settings class
     * @return string
     */
    protected function get_template_path()
    {
        return 'templates/category-settings';
    }

    /**
     * Add some settings into product categories options
     * @return $this
     */
    private function category_settings_add()
    {
        $plugin_scope = $this->setting_provider->get( 'cat-plugin-scope' );
        $display_cart = $this->setting_provider->get( 'cat-display-cart' );
        $display_checkout = $this->setting_provider->get( 'cat-display-checkout' );

        if ( $plugin_scope->get_stored_value() === $plugin_scope->get_value( 'fixed-categories' )->get_identifier() ) {

            $this->category_settings->register_checkbox(
                $plugin_scope->get_identifier(),
                $this->get_translation( 'ops' ),
                $this->get_translation( 'ops.cat.desc' ),
                false,
                10
            );
        }

        if ( $display_cart->get_stored_value() === $display_cart->get_value( 'fixed-categories' )->get_identifier() ) {

            $this->category_settings->register_checkbox(
                $display_cart->get_identifier(),
                $this->get_translation( 'display.cart' ),
                $this->get_translation( 'cat.cart.desc' ),
                false,
                10
            );
        }

        if ( $display_checkout->get_stored_value() === $display_checkout->get_value( 'fixed-categories' )->get_identifier() ) {

            $this->category_settings->register_checkbox(
                $display_checkout->get_identifier(),
                $this->get_translation( 'display.checkout' ),
                $this->get_translation( 'cat.checkout.desc' ),
                false,
                10
            );
        }

        return $this;
    }
}