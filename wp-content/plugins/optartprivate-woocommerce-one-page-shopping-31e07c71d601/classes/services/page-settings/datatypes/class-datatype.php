<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings\Datatypes;

use OptArt\WoocommerceOnePageShopping\Classes\Services\setting_provider;

/**
 * Class datatype
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services\PageSettings\Datatypes
 */
abstract class datatype
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var setting_provider
     */
    protected $setting_provider;

    /**
     * @param $id
     * @param setting_provider $setting_provider
     */
    public function __construct( $id, setting_provider $setting_provider )
    {
        $this->id = $id;
        $this->setting_provider = $setting_provider;
    }

    /**
     * Returns the string identifier of plugin scope setting
     * @return string
     */
    abstract protected function get_scope_setting_identifier();

    /**
     * Returns the string identifier for fixed datatype setting
     * @return string
     */
    abstract protected function get_fixed_datatype_identifier();

    /**
     * Returns the value of setting checkbox that is set for current datatype
     * @param string $setting_identifier
     * @return bool
     */
    abstract protected function display_element_setting( $setting_identifier );

    /**
     * Returns true if OPS should be enabled for current datatype
     * @return bool
     */
    public function ops_enabled()
    {
        $plugin_scope = $this->setting_provider->get( $this->get_scope_setting_identifier() );
        $final_enable = $plugin_scope->get_stored_value() === $plugin_scope->get_value( 'enabled' )->get_identifier() ||
            ( $this->display_element_setting( $this->get_scope_setting_identifier() ) && $plugin_scope->get_stored_value() ===
                $plugin_scope->get_value( $this->get_fixed_datatype_identifier() )->get_identifier() );

        return $final_enable;
    }

    /**
     * @param $setting_identifier
     * @return bool
     * @throws \Exception
     */
    public function display_element( $setting_identifier )
    {
        $setting = $this->setting_provider->get( $setting_identifier );
        $final_enable = $setting->get_stored_value() === 'yes' ||
            ( $this->display_element_setting( $setting_identifier ) &&
                $setting->get_stored_value() === $this->get_fixed_datatype_identifier() );

        return $final_enable;
    }
}