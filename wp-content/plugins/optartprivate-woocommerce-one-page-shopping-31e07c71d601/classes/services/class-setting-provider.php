<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services;

/**
 * Class setting_provider
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services
 */
class setting_provider
{
    /**
     * Array of settings defined for this plugin
     * @var array
     */
    private $settings = array();

    /**
     * Translator service
     * @var translator
     */
    private $translator;

    /**
     * Usually it's plugin identifier
     * @var string
     */
    private $setting_namespace;

    /**
     * @param translator $translator
     * @param string $setting_namespace
     */
    public function __construct( translator $translator, $setting_namespace )
    {
        $this->translator = $translator;
        $this->setting_namespace = $setting_namespace;
        $this->create_settings();
    }

    /**
     * Create setting instances and store them inside the provider
     * @return $this
     */
    private function create_settings()
    {
        // Products: Plugin scope setting
        $plugin_scope = new setting( 'plugin-scope', $this->translator->get_translation( 'plugin.scope' ), $this->setting_namespace );
        $plugin_scope->add_value( 'enabled', $this->translator->get_translation( 'enabled.for.all' ), true );
        $plugin_scope->add_value( 'disabled', $this->translator->get_translation( 'disabled.for.all' ) );
        $plugin_scope->add_value( 'fixed-products', $this->translator->get_translation( 'fixed.products' ) );

        // Products: Display cart
        $display_cart = new setting( 'display-cart', $this->translator->get_translation( 'display.cart' ), $this->setting_namespace );
        $display_cart->add_value( 'yes', $this->translator->get_translation( 'yes' ), true );
        $display_cart->add_value( 'no', $this->translator->get_translation( 'no' ) );
        $display_cart->add_value( 'fixed-products', $this->translator->get_translation( 'fixed.products' ) );

        // Products: Display checkout
        $display_checkout = new setting( 'display-checkout', $this->translator->get_translation( 'display.checkout' ), $this->setting_namespace );
        $display_checkout->add_value( 'yes', $this->translator->get_translation( 'yes' ), true );
        $display_checkout->add_value( 'no', $this->translator->get_translation( 'no' ) );
        $display_checkout->add_value( 'fixed-products', $this->translator->get_translation( 'fixed.products' ) );

        $automatic_add_to_cart = new setting( 'automatically-add-to-cart', $this->translator->get_translation( 'automatic.add-to-cart' ), $this->setting_namespace );
        $automatic_add_to_cart->add_value( 'yes', $this->translator->get_translation( 'yes' ) );
        $automatic_add_to_cart->add_value( 'no', $this->translator->get_translation( 'no' ), true );

        // Shop page: Enabled
        $shop_page = new setting( 'shop-page', $this->translator->get_translation( 'enabled' ), $this->setting_namespace );
        $shop_page->add_value( 'yes', $this->translator->get_translation( 'yes' ) );
        $shop_page->add_value( 'no', $this->translator->get_translation( 'no' ), true );

        // Shop page: Display cart
        $shop_display_cart = new setting( 'shop-display-cart', $this->translator->get_translation( 'display.cart' ), $this->setting_namespace );
        $shop_display_cart->add_value( 'yes', $this->translator->get_translation( 'yes' ), true );
        $shop_display_cart->add_value( 'no', $this->translator->get_translation( 'no' ) );

        // Shop page: Display checkout
        $shop_display_checkout = new setting( 'shop-display-checkout', $this->translator->get_translation( 'display.checkout' ), $this->setting_namespace );
        $shop_display_checkout->add_value( 'yes', $this->translator->get_translation( 'yes' ), true );
        $shop_display_checkout->add_value( 'no', $this->translator->get_translation( 'no' ) );

        // Categories: Plugin scope setting
        $cat_plugin_scope = new setting( 'cat-plugin-scope', $this->translator->get_translation( 'plugin.scope' ), $this->setting_namespace );
        $cat_plugin_scope->add_value( 'enabled', $this->translator->get_translation( 'enabled.for.all.cat' ) );
        $cat_plugin_scope->add_value( 'disabled', $this->translator->get_translation( 'disabled.for.all.cat' ), true );
        $cat_plugin_scope->add_value( 'fixed-categories', $this->translator->get_translation( 'fixed.categories' ) );

        // Categories: Display cart
        $cat_display_cart = new setting( 'cat-display-cart', $this->translator->get_translation( 'display.cart' ), $this->setting_namespace );
        $cat_display_cart->add_value( 'yes', $this->translator->get_translation( 'yes' ), true );
        $cat_display_cart->add_value( 'no', $this->translator->get_translation( 'no' ) );
        $cat_display_cart->add_value( 'fixed-categories', $this->translator->get_translation( 'fixed.categories' ) );

        // Products: Display checkout
        $cat_display_checkout = new setting( 'cat-display-checkout', $this->translator->get_translation( 'display.checkout' ), $this->setting_namespace );
        $cat_display_checkout->add_value( 'yes', $this->translator->get_translation( 'yes' ), true );
        $cat_display_checkout->add_value( 'no', $this->translator->get_translation( 'no' ) );
        $cat_display_checkout->add_value( 'fixed-categories', $this->translator->get_translation( 'fixed.categories' ) );

        $this->add_setting( $plugin_scope );
        $this->add_setting( $display_cart );
        $this->add_setting( $display_checkout );
        $this->add_setting( $automatic_add_to_cart );
        $this->add_setting( $shop_page );
        $this->add_setting( $shop_display_cart );
        $this->add_setting( $shop_display_checkout );
        $this->add_setting( $cat_plugin_scope );
        $this->add_setting( $cat_display_cart );
        $this->add_setting( $cat_display_checkout );

        return $this;
    }

    /**
     * Add setting into current scope
     * @param setting $setting
     * @return $this
     */
    private function add_setting( setting $setting )
    {
        $this->settings[$setting->get_identifier()] = $setting;

        return $this;
    }

    /**
     * Returns the setting of given identifier
     * @param string $identifier
     * @return setting
     * @throws \Exception
     */
    public function get( $identifier )
    {
        if ( !isset( $this->settings[$identifier] ) ) {

            throw new \Exception( 'Following setting doesn\'t exist in the provider: ' . $identifier );
        }

        return $this->settings[$identifier];
    }
}
