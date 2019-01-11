<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings;

/**
 * Class shop
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings
 */
class shop extends page
{
    /**
     * Checks whether cart should be displayed on shop page
     * @return bool
     * @throws \Exception
     */
    public function display_cart()
    {
        $shop_setting = $this->setting_provider->get( 'shop-display-cart' );
        $display_cart = $shop_setting->get_stored_value() === $shop_setting->get_value( 'yes' )->get_identifier();

        return $this->ops_enabled() && $display_cart;
    }

    /**
     * Checks whether checkout should be displayed on shop page
     * @return bool
     * @throws \Exception
     */
    public function display_checkout()
    {
        $shop_setting = $this->setting_provider->get( 'shop-display-checkout' );
        $display_cart = $shop_setting->get_stored_value() === $shop_setting->get_value( 'yes' )->get_identifier();

        return $this->ops_enabled() && $display_cart;
    }

    /**
     * Checks whether OPS should be enabled for shop page
     * @return bool
     */
    public function ops_enabled()
    {
        $shop_page = $this->setting_provider->get( 'shop-page' );
        $ops_enabled = $shop_page->get_stored_value() === $shop_page->get_value( 'yes' )->get_identifier();

        return $ops_enabled;
    }
} 