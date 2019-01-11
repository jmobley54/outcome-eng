<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings;

use OptArt\WoocommerceOnePageShopping\Classes\Services\setting_provider;
use OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings\Datatypes\post as wc_post;

/**
 * Class post
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings
 */
class post extends page
{
	
	private $has_section = FALSE;
	
    /**
     * Post class constructor
     * @param setting_provider $setting_provider
     */
    public function __construct( setting_provider $setting_provider )
    {
        parent::__construct( $setting_provider );

    }

    /**
     * Checks whether cart should be displayed for current post page
     * @return bool
     */
    public function display_cart()
    {
		return true;
    }

    /**
     * Checks whether checkout should be displayed for current post page
     * @return bool
     */
    public function display_checkout()
    {
		return true;
    }

    /**
     * Checks whether item should be automatically added to cart on visit.
     * @return bool
     */
    public function add_to_cart()
    {
		return false;
    }

    /**
     * Checks whether OPS should be enabled for post page
     * @return bool
     */
    public function ops_enabled()
    {
		return TRUE;
    }
	
	public function allow_shortcode(){
		
		global $post;
		if(has_shortcode($post->post_content,'ops_section')){
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function has_section(){
		
		// global $post;
		// if(!$this->has_section && has_shortcode($post->post_content,'ops_section')){
			// $this->has_section=TRUE;
			// return TRUE;
		// }
		// return FALSE;
		global $post;
		if(has_shortcode($post->post_content,'ops_section')){
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function is_ops_post(){
		return TRUE;
	}
}
