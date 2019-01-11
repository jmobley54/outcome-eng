<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services;

/**
 * Class separates the translation used in the extension from code layer
 */
class translator
{
    /**
     * Contains key => value pairs of translation index and test
     *
     * @var array
     */
    private $translations = null;

    /**
     * Constructor sets the translations on place
     */
    public function __construct()
    {
        // admin settings page
        $this->add( 'one.page.shopping', __( 'One page shopping', 'woocommerce-one-page-shopping' ) );
        $this->add( 'ops.settings', __( 'One page shopping settings', 'woocommerce-one-page-shopping' ) );
        $this->add( 'save.changes', __( 'Save changes', 'woocommerce-one-page-shopping' ) );
        $this->add( 'product.settings', __( 'Product Page Settings', 'woocommerce-one-page-shopping' ) );
        $this->add( 'shop.page.settings', __( 'Shop Page Settings', 'woocommerce-one-page-shopping' ) );
        $this->add( 'cat.page.settings', __( 'Category Page Settings', 'woocommerce-one-page-shopping' ) );
        $this->add( 'settings.saved', __( 'One page shopping settings saved', 'woocommerce-one-page-shopping' ) );

        // setting_provider service
        $this->add( 'plugin.scope', __( 'Plugin scope', 'woocommerce-one-page-shopping' ) );
        $this->add( 'enabled.for.all', __( 'Enable for all products', 'woocommerce-one-page-shopping' ) );
        $this->add( 'enabled.for.all.cat', __( 'Enable for all categories', 'woocommerce-one-page-shopping' ) );
        $this->add( 'disabled.for.all', __( 'Disable for all products', 'woocommerce-one-page-shopping' ) );
        $this->add( 'disabled.for.all.cat', __( 'Disable for all categories', 'woocommerce-one-page-shopping' ) );
        $this->add( 'fixed.products', __( 'Fixed products', 'woocommerce-one-page-shopping' ) );
        $this->add( 'fixed.categories', __( 'Fixed categories', 'woocommerce-one-page-shopping' ) );
        $this->add( 'display.cart', __( 'Display cart', 'woocommerce-one-page-shopping' ) );
        $this->add( 'display.checkout', __( 'Display checkout', 'woocommerce-one-page-shopping' ) );
        $this->add( 'automatic.add-to-cart', __( 'Automatically add to cart on visit (simple products only)', 'woocommerce-one-page-shopping' ) );
        $this->add( 'yes', __( 'Yes', 'woocommerce-one-page-shopping' ) );
        $this->add( 'no', __( 'No', 'woocommerce-one-page-shopping' ) );
		
		$this->add( 'tiptip.plugin-scope.enabled', __( 'Choose this option, if you want plugin to be <b>enabled</b> for all your product pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.plugin-scope.disabled', __( 'Choose this option, if you want plugin to be <b>disabled</b> for all your product pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.plugin-scope.fixed-products', __( 'Choose this option, if you want plugin to be enabled for selected product pages only.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.display-cart.yes', __( 'Choose this option, if you want to show cart on all product pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.display-cart.no', __( 'Choose this option, if you don\'t want to show cart on product pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.display-cart.fixed-products', __( 'Choose this option, if you want to show cart on selected product pages only.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.display-checkout.yes', __( 'Choose this option, if you want to show checkout on all product pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.display-checkout.no', __( 'Choose this option, if don\'t want to show checkout on product pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.display-checkout.fixed-products', __( 'Choose this option, if you want to show checkout on selected product pages only.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.automatically-add-to-cart.yes', __( 'Choose this option, if you want plugin to add automatically a product into a cart on a product page visit. Available only for single type products.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.automatically-add-to-cart.no', __( 'Choose this option, if you don\'t want to add a product into a cart on a product page visit.', 'woocommerce-one-page-shopping' ) );
		
		$this->add( 'tiptip.shop-page.yes', __( 'Choose this option, if you want plugin to be <b>enabled</b> on the shop page.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.shop-page.no', __( 'Choose this option, if you want plugin to be <b>disabled</b> on the shop page.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.shop-display-cart.yes', __( 'Choose this option, if you want plugin to show cart on the shop page.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.shop-display-cart.no', __( 'Choose this option, if you don\'t want plugin to show cart on the shop page.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.shop-display-checkout.yes', __( 'Choose this option, if you want plugin to show checkout on the shop page.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.shop-display-checkout.no', __( 'Choose this option, if you don\'t want plugin to show checkout on the shop page.', 'woocommerce-one-page-shopping' ) );
		
		$this->add( 'tiptip.cat-plugin-scope.enabled', __( 'Choose this option, if you want plugin to be <b>enabled</b> on all category pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.cat-plugin-scope.disabled', __( 'Choose this option, if you want plugin to be <b>disabled</b> on category pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.cat-plugin-scope.fixed-categories', __( 'Choose this option, if you want plugin to be <b>enabled</b> on selected category pages only.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.cat-display-cart.yes', __( 'Choose this option, if you want plugin to show cart on all category pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.cat-display-cart.no', __( 'Choose this option, if you don\'t want plugin to show cart on category pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.cat-display-cart.fixed-categories', __( 'Choose this option, if you want to show cart on selected category pages only.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.cat-display-checkout.yes', __( 'Choose this option, if you want plugin to show checkout on all category pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.cat-display-checkout.no', __( 'Choose this option, if you don\'t want plugin to show checkout on category pages.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.cat-display-checkout.fixed-categories', __( 'Choose this option, if you want to show checkout on selected category pages only.', 'woocommerce-one-page-shopping' ) );
		
		//advanced settings tiptips
		$this->add( 'tiptip.update-sidebar-enable', __( 'Check if you want your sidebar cart to be updated by one page shopping plugin.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-sidebar-tag', __( 'Enter the tag, that contains cart total and cart contents count. Default: li', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-sidebar-attribute', __( 'Select proper attribute of the container. Default: class', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-sidebar-attribute-value', __( 'Enter proper attribute value of the container. Default: cart', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-cart-total-enable', __( 'Check if you want cart total amount to be updated.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-cart-total-tag', __( 'Enter proper tag of the total amount container. Default: span', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-cart-total-attribute', __( 'Select proper attribute of the total amount container. Default: class', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-cart-total-attribute-value', __( 'Enter proper attribute value of the total amount container. Default: amount', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-cart-contents-enable', __( 'Check if you want cart contents count to be updated.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-cart-contents-tag', __( 'Enter proper tag of the contents count container. Default: span', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-cart-contents-attribute', __( 'Select proper attribute of the contents count container. Default: class', 'woocommerce-one-page-shopping' ) );
        $this->add( 'tiptip.update-cart-contents-attribute-value', __( 'Enter proper attribute value of the contents count container. Default: contents', 'woocommerce-one-page-shopping' ) );
        $this->add( 'tiptip.update-cart-contents-add-text', __( 'Select this option if you want to add text to total cart contents count in sidebar.', 'woocommerce-one-page-shopping' ) );
        $this->add( 'tiptip.update-cart-contents-singular-form', __( 'Provide here your singular form of the text you want to add.', 'woocommerce-one-page-shopping' ) );
        $this->add( 'tiptip.update-cart-contents-plural-form', __( 'Provide here your plural form of the text you want to add.', 'woocommerce-one-page-shopping' ) );
		$this->add( 'tiptip.update-cart-contents-force-refresh', __( 'This option forces page to refresh on cart/checkout action. Please keep in mind, that this is an experimental option.', 'woocommerce-one-page-shopping' ) );
		
		//advanced settings labels
		$this->add( 'label.update-sidebar-enable', __( 'Enable sidebar update?', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-sidebar-tag', __( 'Sidebar tag', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-sidebar-attribute', __( 'Sidebar attribute', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-sidebar-attribute-value', __( 'Sidebar attribute value', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-cart-total-enable', __( 'Enable cart total update?', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-cart-total-tag', __( 'Cart total tag', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-cart-total-attribute', __( 'Cart total attribute', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-cart-total-attribute-value', __( 'Cart total attribute value', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-cart-contents-enable', __( 'Enable cart count update?', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-cart-contents-tag', __( 'Cart count tag', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-cart-contents-attribute', __( 'Cart count attribute', 'woocommerce-one-page-shopping' ) );
        $this->add( 'label.update-cart-contents-attribute-value', __( 'Cart count attribute value', 'woocommerce-one-page-shopping' ) );
        $this->add( 'label.update-cart-contents-add-text', __( 'Add text to cart content', 'woocommerce-one-page-shopping' ) );
        $this->add( 'label.update-cart-contents-singular-form', __( 'Singular form', 'woocommerce-one-page-shopping' ) );
        $this->add( 'label.update-cart-contents-plural-form', __( 'Plural form', 'woocommerce-one-page-shopping' ) );
		$this->add( 'label.update-cart-contents-force-refresh', __( 'Force refresh (ajax disable, experimental!)', 'woocommerce-one-page-shopping' ) );

        // product_settings page
        $this->add( 'enabled', __( 'Enabled', 'woocommerce-one-page-shopping' ) );
        $this->add( 'disabled', __( 'Disabled', 'woocommerce-one-page-shopping' ) );
        $this->add( 'enable.for.product', __( 'Enable for this product', 'woocommerce-one-page-shopping' ) );
        $this->add( 'settings.unavailable', __( 'OPS settings are not available for this product. Go to "WooCommerce" > "One page shopping" tab to see possibilities.', 'woocommerce-one-page-shopping' ) );

        // category_settings page
        $this->add( 'cat.cart.desc', __( 'Use this setting to handle displaying the cart for One Page Shopping plugin.', 'woocommerce-one-page-shopping' ) );
        $this->add( 'cat.checkout.desc', __( 'Use this setting to handle displaying the checkout for One Page Shopping plugin.', 'woocommerce-one-page-shopping' ) );
        $this->add( 'ops', __( 'One Page Shopping (OPS)', 'woocommerce-one-page-shopping' ) );
        $this->add( 'ops.cat.desc', __( 'Enable or disable One Page Shopping plugin for this category page.', 'woocommerce-one-page-shopping' ) );
		
		// settings_tabs
		$this->add( 'ops.tab.product', __( 'Product', 'woocommerce-one-page-shopping' ) );
		$this->add( 'ops.tab.shop', __( 'Shop', 'woocommerce-one-page-shopping' ) );
		$this->add( 'ops.tab.category', __( 'Category', 'woocommerce-one-page-shopping' ) );
		$this->add( 'ops.tab.advanced', __( 'Advanced', 'woocommerce-one-page-shopping' ) );
		
		//advanced_settings
		$this->add( 'advanced.settings', __( 'Advanced Plugin Settings', 'woocommerce-one-page-shopping' ) );
    }

    /**
     * Method adds the translation entry into an array
     *
     * @param string $identifier
     * @param string $translation - this should be a value returned by __() function
     * @throws \Exception
     */
    private function add($identifier, $translation)
    {
        if (isset($this->translations[$identifier])) {
            throw new \Exception('Translation identifier "'.$identifier.'" already exists');
        }

        $this->translations[$identifier] = $translation;
    }

    /**
     * Makes it possible to get the translation from a main set using translation index/key
     *
     * @param string $key
     * @return string
     * @throws \Exception
     */
    public function get_translation($key)
    {
        if (!isset($this->translations[$key])) {
            throw new \Exception('There\'s no translation for given key: '.$key);
        }

        return $this->translations[$key];
    }
}
