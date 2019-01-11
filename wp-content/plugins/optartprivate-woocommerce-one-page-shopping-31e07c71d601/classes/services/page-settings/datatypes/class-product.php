<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings\Datatypes;

/**
 * Class product
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services
 */
class product extends datatype
{
    /**
     * @var string
     */
    const SCOPE_IDENTIFIER = 'plugin-scope';

    /**
     * @var string
     */
    const FIXED_DATATYPE_IDENTIFIER = 'fixed-products';

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
        $enabled_for_prod = get_post_meta( $this->id, $setting->get_identifier(), true );

        return $enabled_for_prod === 'yes';
    }

    /**
     * @return string
     */
    protected function get_fixed_datatype_identifier()
    {
        return self::FIXED_DATATYPE_IDENTIFIER;
    }
}