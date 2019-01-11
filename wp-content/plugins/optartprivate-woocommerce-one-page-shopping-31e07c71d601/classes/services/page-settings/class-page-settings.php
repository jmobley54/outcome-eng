<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings;

use OptArt\WoocommerceOnePageShopping\Classes\Services\setting_provider;

class page_settings
{
    /**
     * @var string
     */
    const PAGE_TYPE_PRODUCT = 'product';

    /**
     * @var string
     */
    const PAGE_TYPE_SHOP = 'shop';

    /**
     * @var string
     */
    const PAGE_TYPE_CATEGORY = 'category';
	
	/**
     * @var string
     */
    const PAGE_TYPE_POST = 'post';

    /**
     * @var setting_provider
     */
    private $setting_provider;

    /**
     * @var page
     */
    private $current_page = null;

    /**
     * @param setting_provider $setting_provider
     */
    public function __construct( setting_provider $setting_provider)
    {
        $this->setting_provider = $setting_provider;
    }

    /**
     * Returns the type of currently displayed page
     * @return string
     */
    private function get_page_type()
    {
		global $post;

        // echo the_ID();
        // break;

        $page_type = '';

        if( is_product() || ((isset($post)) ? has_shortcode( $post->post_content, 'product_page') : false)) {
            $page_type = self::PAGE_TYPE_PRODUCT;
        }
        elseif( is_shop() ) {
            $page_type = self::PAGE_TYPE_SHOP;
        }
        elseif( is_product_category() ) {
            $page_type = self::PAGE_TYPE_CATEGORY;
        }
		elseif(((isset($post)) ? has_shortcode( $post->post_content, 'ops_section') : false) || ( (isset($post)) ? has_shortcode( $post->post_content, 'ops_to_cart') : false) ){
			$page_type = self::PAGE_TYPE_POST;
		}

        return $page_type;
    }

    /**
     * Returns the instance of 'page' class. These means product, shop or category object.
     * @return null|page
     */
    public function get_current_page()
    {
        if ( is_null( $this->current_page ) ) {

            switch ( $this->get_page_type() ) {

                case self::PAGE_TYPE_PRODUCT:
                    $this->current_page = new product( $this->setting_provider );
                    break;

                case self::PAGE_TYPE_SHOP:
                    $this->current_page = new shop( $this->setting_provider );
                    break;

                case self::PAGE_TYPE_CATEGORY:
                    $this->current_page = new category( $this->setting_provider );
                    break;

				case self::PAGE_TYPE_POST:
                    $this->current_page = new post( $this->setting_provider );
                    break;
            }
        }

        return $this->current_page;
    }
} 