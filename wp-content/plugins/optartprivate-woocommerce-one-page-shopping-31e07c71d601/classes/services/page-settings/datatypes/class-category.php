<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings\Datatypes;
use OptArt\WoocommerceOnePageShopping\Classes\Vendor\WoocommerceExtendedCategories\extended_categories;

/**
 * Class product
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services
 */
class category extends datatype
{
    /**
     * @var string
     */
    const SCOPE_IDENTIFIER = 'cat-plugin-scope';

    /**
     * @var string
     */
    const FIXED_DATATYPE_IDENTIFIER = 'fixed-categories';

    /**
     * Returns the identifier for scope setting
     * @return string
     */
    protected function get_scope_setting_identifier()
    {
        return self::SCOPE_IDENTIFIER;
    }

    /**
     * Returns the value of setting checkbox that is set for current product
     * @param string $setting_identifier
     * @return bool
     * @throws \Exception
     */
    protected function display_element_setting( $setting_identifier )
    {
        $setting = $this->setting_provider->get( $setting_identifier );
        $enabled_for_cat = extended_categories::get_checkbox_val( $this->id, $setting->get_identifier(), false );

        return $enabled_for_cat;
    }

    /**
     * @return string
     */
    protected function get_fixed_datatype_identifier()
    {
        return self::FIXED_DATATYPE_IDENTIFIER;
    }
}