<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings;
use OptArt\WoocommerceOnePageShopping\Classes\Services\setting_provider;

/**
 * Class page
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings
 */
abstract class page
{
    /**
     * @var setting_provider
     */
    protected $setting_provider;

    /**
     * Default constructor
     * @param setting_provider $setting_provider
     */
    public function __construct( setting_provider $setting_provider )
    {
        $this->setting_provider = $setting_provider;
    }

    /**
     * @return boolean
     */
    abstract public function display_cart();

    /**
     * @return boolean
     */
    abstract public function display_checkout();

    /**
     * @return boolean
     */
    abstract public function ops_enabled();
	
	public function allow_shortcode(){
		return FALSE;
	}
	
	public function is_ops_post(){
		return FALSE;
	}
} 