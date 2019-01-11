<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services;

/**
 * Class setting
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services
 */
class setting
{
    /**
     * Setting identifier
     * @var string
     */
    private $identifier;

    /**
     * Set of setting values
     * @var setting_value[]
     */
    private $values = array();

    /**
     * Contains a reference to one of the setting_values defined in $values array
     * @var setting_value;
     */
    private $default_value;

    /**
     * Setting label
     * @var string
     */
    private $label;

    /**
     * Usually it's plugin identifier
     * @var string
     */
    private $setting_namespace;

    /**
     * @param string $identifier
     * @param string $label
     * @param string $setting_namespace
     */
    public function __construct( $identifier, $label, $setting_namespace )
    {
        $this->identifier = $identifier;
        $this->label = $label;
        $this->setting_namespace = $setting_namespace;
    }

    /**
     * Add value into current setting
     * @param string $identifier
     * @param bool $default
     * @param string $description
     * @return $this
     */
    public function add_value( $identifier, $description = '', $default = false )
    {
        $this->values[$identifier] = new setting_value( $identifier );

        if ( strlen( $description ) > 0 ) {

            $this->values[$identifier]->set_description( $description );
        }

        if ( $default === true ) {

            $this->default_value = & $this->values[$identifier];
        }

        return $this;
    }

    /**
     * Returns default value for current setting
     * @return setting_value
     */
    public function get_default_value()
    {
        return $this->default_value;
    }

    /**
     * Getter for setting identifier
     * @return string
     */
    public function get_identifier()
    {
        return $this->identifier;
    }

    /**
     * Getter for setting label
     * @return string
     */
    public function get_label()
    {
        return $this->label;
    }

    /**
     * Returns the value for given identifier
     * @param string $identifier
     * @return setting_value
     * @throws \Exception
     */
    public function get_value( $identifier )
    {
        if ( !isset( $this->values[$identifier] ) ) {

            throw new \Exception( 'Following value doesn\'t exist in the setting: ' . $identifier );
        }

        return $this->values[$identifier];
    }

    /**
     * Method returns a value of current setting (set on plugin settings page).
     * In case when value is not set, it returns a default type value.
     * @return string
     */
    public function get_stored_value()
    {
        $options = get_option( $this->setting_namespace );
        $name = $this->get_identifier();

        if ( isset( $options[$name] ) && gettype( $this->get_default_value()->get_identifier() ) === gettype( $options[$name] ) ) {

            return $options[$name];
        }

        return $this->get_default_value()->get_identifier();
    }

    /**
     * @return setting_value[]
     */
    public function get_all_values()
    {
        return $this->values;
    }
}