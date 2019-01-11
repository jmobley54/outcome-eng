<?php
/*
Plugin Name: WooCommerce One Page Shopping
Plugin URI:
Description: Plugin displays cart and checkout pages on product page which makes it possible to do the shopping on one common page. Since version 2.0.0 it's also possible to use the plugin on shop page and category pages as well.
Version: 2.5.4
Author: OptArt | Piotr Szczygiel
Author URI: http://www.optart.biz
*/

register_activation_hook( __FILE__, function(){
	$get_theme = get_option( 'woocommerce_one_page_shopping_notice_get_theme', null );

	if ( $get_theme === null ) {
		update_option( 'woocommerce_one_page_shopping_notice_get_theme', true );
	}
} );

add_action( 'admin_notices', function() {
	if ( isset($_GET['hide_ops_theme_notice']) ) {
		update_option( 'woocommerce_one_page_shopping_notice_get_theme', false );
	}

	if ( get_option( 'woocommerce_one_page_shopping_notice_get_theme' ) ) {
		echo '<div class="updated"><p>';
		printf ( __( '<a href="%s" target="_blank">Grab free theme</a> designed especially for One Page Shopping Plugin! | <a href="%s">Hide Notice</a>', 'woocommerce-one-page-shopping' ), 'http://onepage.optart.biz', add_query_arg( 'hide_ops_theme_notice', 'yes' ) );
		echo '</p></div>';
	}

	if ( isset($_GET['hide_ops_widget_cart_warning']) ) {
		update_option( 'woocommerce_one_page_shopping_widget_cart_warning', TRUE );
	}
	if( is_active_widget(false,false,'woocommerce_widget_cart') && !get_option('woocommerce_one_page_shopping_widget_cart_warning' ) ){
		echo '<div class="update-nag"><p>';
		printf ( __( 'You are using a WooCommerce widget cart that will not be updated by One Page Shopping plugin. | <a href="%s">Hide Notice</a>', 'woocommerce-one-page-shopping' ), add_query_arg( 'hide_ops_widget_cart_warning', 'yes' ) );
		echo '</p></div>';
	}
} );

add_action( 'init', function() {
    if ( class_exists( 'woocommerce' ) ) {
        $ops = new woocommerce_one_page_shopping();
        $ops->start();
    }
} );

/**
 * Plugin-starter class
 */
class woocommerce_one_page_shopping
{
    /**
     * plugin identifier
     * @var string
     */
    const PLUGIN_IDENTIFIER = 'woocommerce_one_page_shopping';

    /**
     * Contains a list of classes that are responsible for running on particular pages
     * @var array
     */
    private $wp_pages = array(
        'OptArt\WoocommerceOnePageShopping\Classes\WpPages\frontpage',
        'OptArt\WoocommerceOnePageShopping\Classes\WpPages\admin_settings',
        'OptArt\WoocommerceOnePageShopping\Classes\WpPages\product_settings',
        'OptArt\WoocommerceOnePageShopping\Classes\WpPages\category_settings',
    );

    /**
     * Plugin starter
     */
    public function start()
    {
        $this->load_classes();
    }

    /**
     * Load classes and create the instances of wp pages
     */
    private function load_classes()
    {
        // load vendors
        require_once( 'classes/vendor/woocommerce-extended-categories/class-woocommerce-extended-categories.php' );
        require_once( 'classes/vendor/abstract-class-wp-helpers.php' );

        // load services
        require_once( 'classes/services/class-translator.php' );
        require_once( 'classes/services/class-setting-value.php' );
        require_once( 'classes/services/class-setting.php' );
        require_once( 'classes/services/class-setting-provider.php' );
        require_once( 'classes/services/page-settings/datatypes/class-datatype.php');
        require_once( 'classes/services/page-settings/datatypes/class-product.php');
        require_once( 'classes/services/page-settings/datatypes/class-category.php');
        require_once( 'classes/services/page-settings/class-page-settings.php' );
        require_once( 'classes/services/page-settings/class-page.php' );
        require_once( 'classes/services/page-settings/class-product.php' );
        require_once( 'classes/services/page-settings/class-shop.php' );
        require_once( 'classes/services/page-settings/class-category.php' );
		require_once( 'classes/services/page-settings/class-ops-post.php' );

        // load pages
        require_once( 'classes/wp-pages/abstract-class-common.php' );
        require_once( 'classes/wp-pages/class-frontpage.php' );
        require_once( 'classes/wp-pages/class-admin-settings.php' );
        require_once( 'classes/wp-pages/class-product-settings.php' );
        require_once( 'classes/wp-pages/class-category-settings.php' );

        // set indentifiers for pages
        \OptArt\WoocommerceOnePageShopping\Classes\WpPages\common::set_identifiers( self::PLUGIN_IDENTIFIER, __FILE__ );

        // run pages instances
        foreach( $this->wp_pages as $page_class ) {

            $instance = new $page_class;
            $instance->_run();
        }
    }
}
