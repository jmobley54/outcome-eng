<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\WpPages;

use OptArt\WoocommerceOnePageShopping\Classes\Services\setting_provider;

/**
 * Class product_settings
 * @package OptArt\WoocommerceOnePageShopping\Classes\WpPages
 */
class product_settings extends common
{
    /**
     * @var setting_provider
     */
    private $setting_provider;

    /**
     * Run the hooks!
     */
    public function _run()
    {
        $this->setting_provider = new setting_provider( $this->get_translator(), self::get_plugin_identifier() );

        add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'product_tab' ) );
        add_action( 'woocommerce_product_write_panels', array( $this, 'product_write_panel' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'product_save_data' ) );
    }

    /**
     * Returns the path to the templates in product setting page
     * @return string
     */
    public function get_template_path()
    {
        return 'templates/product-settings';
    }

    /**
     * Add a tab for external product
     */
    public function product_tab()
    {
        $this->render_template( 'product-tab.php', array(
            'tab_id' => self::get_plugin_identifier(),
            'label' => $this->get_translation( 'one.page.shopping' )
        ) );
    }

    /**
     * Add a write panel for external product
     */
    public function product_write_panel()
    {
        global $post;
        $plugin_scope = $this->setting_provider->get( 'plugin-scope' );
        $display_cart = $this->setting_provider->get( 'display-cart' );
        $display_checkout = $this->setting_provider->get( 'display-checkout' );
        $settings = array();

        if ( $plugin_scope->get_stored_value() === 'fixed-products' ) {

            $settings['enable_ops'] = array(
                'id' => $plugin_scope->get_identifier(),
                'label' => $this->get_translation( 'enable.for.product' ),
                'desc_tip' => true,
                'value' => get_post_meta( $post->ID, $plugin_scope->get_identifier(), true ),
            );
        }

        if ( $display_cart->get_stored_value() === 'fixed-products' ) {

            $settings['display_cart'] = array(
                'id' => $display_cart->get_identifier(),
                'label' => $this->get_translation( 'display.cart' ),
                'desc_tip' => true,
                'value' => get_post_meta( $post->ID, $display_cart->get_identifier(), true ),
            );
        }

        if ( $display_checkout->get_stored_value() === 'fixed-products' ) {

            $settings['display_checkout'] = array(
                'id' => $display_checkout->get_identifier(),
                'label' => $this->get_translation( 'display.checkout' ),
                'desc_tip' => true,
                'value' => get_post_meta( $post->ID, $display_checkout->get_identifier(), true ),
            );
        }

        $this->render_template( 'product-write-panel.php', array(
            'tab_id' => self::get_plugin_identifier(),
            'settings' => $settings,
            'translator' => $this->get_translator()
        ) );
    }

    /**
     * Save write panel data
     * @global array $post
     */
    public function product_save_data()
    {
        global $post;
        $plugin_scope = $this->setting_provider->get( 'plugin-scope' );
        $display_cart = $this->setting_provider->get( 'display-cart' );
        $display_checkout = $this->setting_provider->get( 'display-checkout' );

        update_post_meta(
            $post->ID,
            $plugin_scope->get_identifier(),
            filter_input( INPUT_POST, $plugin_scope->get_identifier() )
        );

        update_post_meta(
            $post->ID,
            $display_cart->get_identifier(),
            filter_input( INPUT_POST, $display_cart->get_identifier() )
        );

        update_post_meta(
            $post->ID,
            $display_checkout->get_identifier(),
            filter_input( INPUT_POST, $display_checkout->get_identifier() )
        );
    }
}