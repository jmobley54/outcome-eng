<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\WpPages;
use OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings\page;
use OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings\page_settings;
use OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings\product;
use OptArt\WoocommerceOnePageShopping\Classes\Services\setting_provider;
use DOMDocument;

/**
 * Class performs actions on frontpage panel side
 */
class frontpage extends common
{
    /**
     * @var string
     */
    private $add_to_cart_css = 'add_to_cart_button_ops';

    /**
     * @var setting_provider
     */
    private $setting_provider;

	private $advanced_settings;

    /**
     * @var page_settings
     */
    private $page_settings;

    /**
     * Method sets up the hooks
     */
    public function _run()
    {
        $this->setting_provider = new setting_provider( $this->get_translator(), self::get_plugin_identifier() );
        $this->page_settings = new page_settings( $this->setting_provider );

        if ( !defined( 'SHOW_ORDER_REVIEW' ) ) {
            define( 'SHOW_ORDER_REVIEW', true );
        }

		$this->advanced_settings();
        // registering AJAX functions
        $this->register_ajax_function( 'ops_add_to_cart', array( $this, 'ajax_add_to_cart' ), false );
        $this->register_ajax_function( 'ops_remove_from_cart', array( $this, 'ajax_remove_from_cart' ), false );
        $this->register_ajax_function( 'ops_update_cart', array( $this, 'ajax_update_cart' ), false );
        $this->register_ajax_function( 'ops_update_checkout', array( $this, 'ajax_update_checkout' ), false );
        $this->register_ajax_function( 'ops_remove_coupon', array( $this, 'ajax_remove_coupon' ), false );
        $this->register_ajax_function( 'ops_add_to_cart_related', array( $this, 'ajax_add_to_cart_related' ), false );
		$this->register_ajax_function( 'ops_update_header', array( $this, 'ajax_update_header' ), false );

        add_action( 'template_redirect', array( $this, 'add_to_cart' ), 10 );
        add_action( 'woocommerce_after_single_product', array( $this, 'add_cart' ), 10 );
        add_action( 'woocommerce_after_single_product', array( $this, 'add_checkout' ), 11 );

        add_action( 'woocommerce_after_shop_loop', array( $this, 'add_cart' ), 10 );
        add_action( 'woocommerce_after_shop_loop', array( $this, 'add_checkout' ), 11 );

        add_action( 'wp_enqueue_scripts', array( $this, 'add_assets_front' ) );
        add_filter( 'woocommerce_cart_needs_payment', array( $this, 'cart_needs_payment' ), 10, 1 );
        add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'related_prod_links' ), 10, 1 );

		add_shortcode( 'ops_section', array($this, 'render_sections') );
		add_shortcode( 'ops_to_cart', array($this, 'render_to_cart_section') );
    }

    /**
     * Sets the template path for frontpage class
     * @return string
     */
    protected function get_template_path()
    {
        return 'templates/frontpage';
    }

    /**
     * Loading assets for front site
     */
    public function add_assets_front()
    {
        $current_page = $this->page_settings->get_current_page();
        if ( !( $current_page instanceof page ) || $current_page->ops_enabled() === false ) {

            return false;
        }

        global $woocommerce;

        // CSS
        wp_enqueue_style( self::get_plugin_identifier() . '_front_styles', plugins_url( 'assets/css/front.css', self::get_plugin_file() ) );

        // JS
        $this->enqueue_script( self::get_plugin_identifier() . '_front_scripts', 'front-min.js', self::get_plugin_file(), false, array(
//         $this->enqueue_script( self::get_plugin_identifier() . '_front_scripts', 'unpacked/front.js', self::get_plugin_file(), false, array(
            'jquery'
        ) );

        $this->enqueue_script( self::get_plugin_identifier() . '_country_select', 'country-select-min.js', self::get_plugin_file(), true );
        wp_localize_script( self::get_plugin_identifier() . '_country_select', 'ops_country_select_params', array(
            'countries'              => json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
            'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
        ) );

        wp_enqueue_script( 'wc-address-i18n' );

		$user_check = 'TRUE';
		if(!get_current_user_id()){
			$user_check = 'FALSE';
		}

        wp_localize_script( self::get_plugin_identifier() . '_front_scripts', 'ops_php_data', array(
            'nonce_post_id' => self::ADD_TO_CART_NONCE_POST_ID,
            'nonce' => wp_create_nonce( self::ADD_TO_CART_NONCE ),
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'remove_items' => array(
                    '.one-page-shopping-section .shipping_calculator',
                    '#one-page-shopping-cart-content .coupon'
                ) + (($this->page_settings->get_current_page()->display_checkout()) ? array( 2 => '#one-page-shopping-cart-content .cart-collaterals' ) : array()),
            // below filter code comes from original checkout/form-shipping.php
            // (WC 2.0.20, useless for latest versions, can be removed in the future)
            'ship_to_billing_def' => apply_filters(
                'woocommerce_shiptobilling_default',
                get_option( 'woocommerce_ship_to_same_address' ) === 'yes' ? 1 : 0
            ),
            // below filter code comes from original checkout/form-shipping.php
            // (WC 2.1, useless for previous versions)
            'ship_to_different_def' => apply_filters(
                'woocommerce_ship_to_different_address_checked',
                get_option( 'woocommerce_ship_to_destination' ) === 'shipping' ? 1 : 0
            ),
            'display_cart' => $this->page_settings->get_current_page()->display_cart(),
            'display_checkout' => $this->page_settings->get_current_page()->display_checkout(),
            'cart_count' => WC()->cart->get_cart_contents_count(),
			'check_cond' => $user_check,
			'enable_sidebar_update' => $this->get_advanced_setting('update-sidebar-enable', FALSE),
			'enable_cart_amount_update' => $this->get_advanced_setting('update-cart-total-enable', FALSE),
			'enable_cart_contents_update' => $this->get_advanced_setting('update-cart-contents-enable', FALSE),
			'sidebar_tag' => $this->get_advanced_setting('update-sidebar-tag', 'li'),
			'sidebar_attribute' => $this->get_advanced_setting('update-sidebar-attribute', 'class'),
			'sidebar_attribute_value' => $this->get_advanced_setting('update-sidebar-attribute-value', 'cart'),
			'amount_tag' => $this->get_advanced_setting('update-cart-total-tag', 'span'),
			'amount_attribute' => $this->get_advanced_setting('update-cart-total-attribute', 'class'),
			'amount_attribute_value' => $this->get_advanced_setting('update-cart-total-attribute-value', 'amount'),
			'contents_tag' => $this->get_advanced_setting('update-cart-contents-tag', 'span'),
			'contents_attribute' => $this->get_advanced_setting('update-cart-contents-attribute', 'class'),
			'contents_attribute_value' => $this->get_advanced_setting('update-cart-contents-attribute-value', 'contents'),
            'force_refresh' => (boolean) $this->get_advanced_setting('update-cart-contents-force-refresh', false)
        ) );

        $this->enqueue_script( self::get_plugin_identifier() . '_front_checkout', 'front-checkout-min.js', self::get_plugin_file() );
        // $this->enqueue_script( self::get_plugin_identifier() . '_front_checkout', 'unpacked/front-checkout.js', self::get_plugin_file() );
        $checkout_data = array(
            'wc_old_version' => version_compare( $woocommerce->version, self::WOOCOMMERCE_NEW_VERSION, '<' ),
            'update_order_review_nonce' => wp_create_nonce( "update-order-review" ),
            'apply_coupon_nonce' => wp_create_nonce( "apply-coupon" ),
            'ajax_url' => '',
            'ajax_loader_url' => '',
            'option_guest_checkout' => get_option( 'woocommerce_enable_guest_checkout' )
        );
        if ( function_exists( 'WC' ) ) {
            $checkout_data['ajax_url'] = WC()->ajax_url();
            $checkout_data['ajax_loader_url'] = apply_filters( 'woocommerce_ajax_loader_url', str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/images/ajax-loader@2x.gif' );
        }
        wp_localize_script( self::get_plugin_identifier() . '_front_checkout', 'ops_checkout_data', $checkout_data );

        $this->enqueue_script( self::get_plugin_identifier() . '_scrollto', 'jquery.ScrollTo-min.js', self::get_plugin_file(), false );

        wp_enqueue_script( 'wc-cart', WC()->plugin_url() . '/assets/js/frontend/cart.min.js', array( 'jquery', 'wc-country-select', 'wc-address-i18n') );
        wp_enqueue_script( 'wc-checkout', WC()->plugin_url() . '/assets/js/frontend/checkout.min.js', array( 'jquery', 'wc-country-select', 'wc-address-i18n') );

        return true;
    }

	public function advanced_settings(){
		$settings = get_option(self::get_plugin_identifier());
		$this->advanced_settings = $settings;
	}

	public function get_advanced_setting($setting, $default){
		if(isset($this->advanced_settings[$setting]) && !empty($this->advanced_settings[$setting])){
			return $this->advanced_settings[$setting];
		}
		return $default;
	}

    /**
     * Method adds the cart into product page
     */
    public function add_to_cart()
    {
	      /** @var product $current_page */
	      $current_page = $this->page_settings->get_current_page();
        if ( ($current_page instanceof product) && $current_page->add_to_cart() ) {
	          if ( ! is_admin() ) {
			          $product = wc_get_product();
			          if ($product->is_type('simple')) {
				            $found = false;
				            if (sizeof(WC()->cart->get_cart()) > 0) {
						            foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
								            $_product = $values['data'];
								            if ($_product->id == $product->id) {
										            $found = true;
										            break;
								            }
						            }
				            }

				            if (!$found) {
					              WC()->cart->add_to_cart($product->id);
				            }
	              }
            }
        }
    }

    /**
     * Method adds the cart into product page
     */
    public function add_cart()
    {
        // if ( $this->page_settings->get_current_page()->display_cart() ) {
        $current_page = $this->page_settings->get_current_page();
        if ( ( $current_page instanceof page ) && $current_page->ops_enabled() ) {

            $this->render_template( 'add-cart.php', array(
                'plugin_identifier' => self::get_plugin_identifier(),
            ) );
        }
    }

    /**
     * Method adds the checkout into product page
     */
    public function add_checkout()
    {
        $current_page = $this->page_settings->get_current_page();
        if ( ( $current_page instanceof page ) && $this->page_settings->get_current_page()->display_checkout() ) {
            $this->render_template( 'add-checkout.php', array(
                'plugin_identifier' => self::get_plugin_identifier(),
            ) );
        }
    }

	/*	Shortcode [ops_section] function
	*	@params array $atts
	*	cart, checkout
	*/
	public function render_sections( $atts ){

		$current_page = $this->page_settings->get_current_page();
		if( !( $current_page instanceof page ) || !$current_page->is_ops_post() ){
			return '';
		}
		// if( $current_page->has_section() ){
			// return '';
		// }

		ob_start();
		if(empty($atts)){
			$this->add_cart();
			$this->add_checkout();
		}
		if(is_array($atts) ? in_array('cart',$atts) : FALSE){
			$this->add_cart();
		}
		if(is_array($atts) ? in_array('checkout',$atts) : FALSE){
			$this->add_checkout();
		}
		return ob_get_clean();

	}


	/*	Shortcode [ops_to_cart] function
	*	@params array $atts
	*	id="", sku="", style=""
	*/
	public function render_to_cart_section( $atts ){

		$current_page = $this->page_settings->get_current_page();
		if( !( $current_page instanceof page ) || !( $current_page->is_ops_post() ) ){
			return '';
		}
		elseif( !$current_page->allow_shortcode() ){
			return 'Please add [ops_section] shortcode to your post.';
		}
		elseif( empty($atts) ){
			return '';
		}
		elseif( is_array($atts) ){
			$shortcode = '[add_to_cart';
			if( array_key_exists('id', $atts) ){
				$shortcode .=' id="'.$atts['id'].'"';
			}
			elseif( array_key_exists('sku', $atts) ){
				$shortcode .= ' sku="'.$atts['sku'].'"';
			}
			if( array_key_exists('style', $atts) ){
				$shortcode .= ' style="'.$atts['style'].'"';
			}
			$shortcode .= ']';

			$html = '<section class="one-page-shopping-section one-page-shopping-add-to-cart">';
			$html .= do_shortcode($shortcode);
			$html .= '</section>';

			return $html;
		}

		return '';
	}

    /**
     * Method handles the AJAX request when clicking on "Add to cart" button
     */
    public function ajax_add_to_cart()
    {
        check_ajax_referer( self::ADD_TO_CART_NONCE, self::ADD_TO_CART_NONCE_POST_ID );
        global $woocommerce;

        // adding the product into cart
        $form_data = filter_input( INPUT_POST, 'form', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
        $variation = '';

        // simple fix to make WC Booking plugin work
        if ( class_exists( '\WC_Booking_Form' ) ) {
            $_POST = $form_data;
        }

        foreach ($form_data as $product_id => $product_info) {

            foreach ( $product_info as $id => $value ) {
                if ( strpos( $id, 'attribute_' ) !== 0 ) {
                    continue;
                }

                $parts = explode( 'attribute_', $id );
                if ( isset( $parts[1] ) ) {
                    $variation[$parts[1]] = $value;
                }
            }

            $woocommerce->cart->add_to_cart(
                $product_id,
                isset( $product_info['quantity'] ) ? $product_info['quantity'] : 1,
                isset( $product_info['variation_id'] ) ? $product_info['variation_id'] : '',
                $variation
            );
        }

        // now we need to render the cart again and return it as an AJAX response
        $this->render_ajax_template( 'cart', true );
        exit;
    }

    /**
     * Method handles the AJAX request when clicking on "Add to cart" button placed under each product from
     * "related list"
     */
    public function ajax_add_to_cart_related()
    {
        check_ajax_referer( self::ADD_TO_CART_NONCE, self::ADD_TO_CART_NONCE_POST_ID );
        global $woocommerce;

        $woocommerce->cart->add_to_cart(
            filter_input( INPUT_POST, 'product_id' )
        );

        // now we need to render the cart again and return it as an AJAX response
        $this->render_ajax_template( 'cart' );
        exit;
    }

    /**
     * Method handles removing the coupon via AJAX
     * @global $woocommerce
     */
    public function ajax_remove_coupon()
    {
        check_ajax_referer( self::ADD_TO_CART_NONCE, self::ADD_TO_CART_NONCE_POST_ID );
        global $woocommerce;

        $woocommerce->cart->remove_coupon( filter_input( INPUT_POST, 'coupon' ) );

        exit;
    }

    /**
     * Method handles the AJAX request when clicking on remove cart item icon
     * @global type $woocommerce
     */
    public function ajax_remove_from_cart()
    {
        check_ajax_referer( self::ADD_TO_CART_NONCE, self::ADD_TO_CART_NONCE_POST_ID );
        global $woocommerce;

        $woocommerce->cart->set_quantity( filter_input( INPUT_POST, 'cart_item' ), 0 );

        // now we need to render the cart again and return it as an AJAX response
        $this->render_ajax_template( 'cart' );
        exit;
    }

    /**
     * Method handles the AJAX request when clicking on update cart button
     */
    public function ajax_update_cart()
    {
        check_ajax_referer( self::ADD_TO_CART_NONCE, self::ADD_TO_CART_NONCE_POST_ID );

        if ( !defined( 'WOOCOMMERCE_CART' ) ) {
            define( 'WOOCOMMERCE_CART', true );
        }

        $this->update_cart( filter_input( INPUT_POST, 'cart', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY ) );
        $this->render_ajax_template( 'cart' );

        exit;
    }

    /**
     * Method returns the html code of the checkout
     */
    public function ajax_update_checkout()
    {
        check_ajax_referer( self::ADD_TO_CART_NONCE, self::ADD_TO_CART_NONCE_POST_ID );

        if ( !defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
            define( 'WOOCOMMERCE_CHECKOUT', true );
        }

        // generate and calculate shipping items
        global $woocommerce;
        $woocommerce->cart->calculate_totals();
        $woocommerce->cart->calculate_shipping();

        $this->render_ajax_template( 'checkout' );
        exit;
    }

	public function ajax_update_header(){

		global $woocommerce;
        $singular = '';
        $plural = '';
        if($this->get_advanced_setting('update-cart-contents-add-text', FALSE)){
            $singular = ' '.$this->get_advanced_setting('update-cart-contents-singular-form', 'item');
            $plural = ' '.$this->get_advanced_setting('update-cart-contents-plural-form', 'items');
        }
		echo $woocommerce->cart->get_cart_total();
		echo '<span class="contents">' . sprintf(_n('%d'.$singular, '%d'.$plural, $woocommerce->cart->get_cart_contents_count(), 'woothemes'), $woocommerce->cart->get_cart_contents_count()) . '</span>';
		exit;

	}

    /**
     * Method hacks the woocommerce_cart_needs_payment filter by setting true value
     * in case of ajax request
     * @param boolean $val
     * @return boolean
     */
    public function cart_needs_payment( $val )
    {
        global $woocommerce;

        if ( is_ajax() && ( $woocommerce->cart->get_cart_total() > 0 ) ) {
            $val = true;
        }

        return $val;
    }

    /**
     * Method clears the current buffer and renders a template of given name.
     * @param string $tpl_name
     * @param boolean $set_cart_cookies
     */
    private function render_ajax_template( $tpl_name, $set_cart_cookies = false )
    {
        if ( ob_get_length() > 0 ) {

            ob_clean();
        }

        if ( $set_cart_cookies ) {

            global $woocommerce;
            $woocommerce->cart->maybe_set_cart_cookies();
        }

        $this->render_template( $tpl_name . '.php' );
    }

    /**
     * Takes a markup of "Add to cart" buttons and replaces the css class, so we can override their functionality
     *
     * @param string $markup
     * @return string
     */
    public function related_prod_links( $markup )
    {
        // change the markup only when single product page context
        if ( is_product() ) {

            $doc = new DOMDocument();
            $doc->loadHTML($markup);

            foreach($doc->getElementsByTagName('a') as $element){
                $class = $element->getAttribute("class");
                $class = str_replace( 'add_to_cart_button', $this->add_to_cart_css, $class );
                $element->setAttribute("class", $class);
                $doc->saveHTML($element);
            }
            return $doc->saveHTML();
        }

        return $markup;
    }

    /**
     * This method contains the code responsible for updating the cart. It has been
     * copied from original function woocommerce_update_cart_action() and changed a bit.
     * @global $woocommerce
     * @param array $cart_totals
     */
    private function update_cart( $cart_totals )
    {
        global $woocommerce;

        if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
            foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {

                // Skip product if no updated quantity was posted
                if ( !isset( $cart_totals[ $cart_item_key ]['qty'] ) ) {
                    continue;
                }

                // Sanitize
                $quantity = apply_filters(
                    'woocommerce_stock_amount_cart_item',
                    apply_filters(
                        'woocommerce_stock_amount',
                        preg_replace( "/[^0-9\.]/", "", $cart_totals[ $cart_item_key ]['qty'] )
                    ),
                    $cart_item_key
                );
                if ( "" === $quantity || $quantity == $values['quantity'] ) {
                    continue;
                }

                // Update cart validation
                $passed_validation 	= apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $values, $quantity );
                $_product = $values['data'];

                // is_sold_individually
                if ( $_product->is_sold_individually() && $quantity > 1 ) {
                    $woocommerce->add_error( sprintf( __( 'You can only have 1 %s in your cart.', 'woocommerce' ), $_product->get_title() ) );
                    $passed_validation = false;
                }

                if ( $passed_validation ) {
                    $woocommerce->cart->set_quantity( $cart_item_key, $quantity, false );
                }
            }

            $woocommerce->cart->calculate_totals();
        }
    }
}
