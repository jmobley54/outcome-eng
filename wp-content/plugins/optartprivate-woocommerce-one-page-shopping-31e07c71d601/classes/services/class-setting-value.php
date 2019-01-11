<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Services;

/**
 * Class setting_value
 * @package OptArt\WoocommerceOnePageShopping\Classes\Services
 */
class setting_value
{
    /**
     * Value identifier
     * @var string
     */
    private $identifier;

    /**
     * Value description
     * @var string
     */
    private $description;

    /**
     * @param string $identifier
     */
    public function __construct( $identifier )
    {
        $this->identifier = $identifier;
    }

    /**
     * Getter for value identifier
     * @return string
     */
    public function get_identifier()
    {
        return $this->identifier;
    }

    /**
     * Setter for value description
     * @param string $description
     * @return $this
     */
    public function set_description( $description )
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Getter for value description
     * @return string
     */
    public function get_description()
    {
        return $this->description;
    }
}