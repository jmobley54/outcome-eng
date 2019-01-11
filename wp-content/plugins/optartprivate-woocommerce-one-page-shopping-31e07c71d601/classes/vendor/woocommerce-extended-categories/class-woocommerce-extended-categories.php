<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\Vendor\WoocommerceExtendedCategories;

use OptArt\WoocommerceOnePageShopping\Classes\Vendor\wp_helpers;

/**
 * class contains some helpers useful when you want to extend the product categories
 * for your WooCommerce installation.
 */
class extended_categories
{
    /**
     * Contains a list of elements which will be added to standard product
     * categories settings
     * @var array
     */
    private $elements = array();

    /**
     * Default WordPress priority for a hook used in this vendor
     * @var int
     */
    private $wp_priority = 5;

    /**
     * A path to directory containing templates for this vendor
     */
    const TEMPLATES_DIR = 'classes/vendor/woocommerce-extended-categories/templates';

    /**
     * Identifier of checkbox element
     */
    const CHECKBOX_TYPE = 'checkbox';

    /**
     * Identifier of select list element
     */
    const SELECT_TYPE = 'select_list';


    /**
     * Method enabled this vendor. Contains hooks definition, so note that it
     * should be fired in correct place.
     */
    public function _run() 
    {
        // sort the array by key (priority) ascending
        ksort( $this->elements );

        // add end edit hooks
        add_action( 'product_cat_add_form_fields' , array( $this, 'settings_add' ), $this->wp_priority );
        add_action( 'product_cat_edit_form_fields', array( $this, 'settings_edit' ), $this->wp_priority, 1 );

        // storing the values
        add_action( 'created_term', array( $this, 'save_values' ), 10, 3 );
        add_action( 'edit_term', array( $this, 'save_values' ), 9, 3 );

        // add assets
        add_action( 'admin_enqueue_scripts', array( $this, 'add_assets_admin' ) );
    }

    /**
     * Load assets for admin panel
     */
    public function add_assets_admin() 
    {
        wp_enqueue_style( 'wc_extended_categories_admin_styles', plugins_url( 'assets/css/admin.css', __FILE__ ) );
    }

    /**
     * Returns a value stored for checkbox with defined identifier
     * @param int $term_id
     * @param string $identifier
     * @param bool $default
     * @return boolean
     */
    public static function get_checkbox_val( $term_id, $identifier, $default )
    {
        $return = $default;
        $val = get_woocommerce_term_meta( $term_id, $identifier );
        if ( $val !== '' ) {
            $return = $val;
        }

        return (bool)$return;
    }

    /**
     * Processing the settings - in here all registered options are rendered on
     * category add/edit page. Method is common for both hooks.
     * 
     * @param null|int $term_id
     */
    private function process_settings( $term_id = null ) 
    {
        foreach ( $this->elements as $prior_elements ) {

            foreach ( $prior_elements as $elem ) {

                switch( $elem['type'] ) {

                    case self::CHECKBOX_TYPE :
                        $this->render_checkbox( $elem, $term_id );
                        break;

                    case self::SELECT_TYPE:
                        $this->render_select_list( $elem, $term_id );
                        break;
                }
            }
        }
    }

    /**
     * Use this method to register a checkbox element which will be rendered on
     * categories settings page.
     *
     * @param string $identifier - pick an indentifier for the checkbox
     * @param string $label
     * @param string $description
     * @param bool $default - default value (checked or unchecked)
     * @param int $priority - internal vendor priority (not WP priority)
     * @throws \Exception
     * @return $this
     */
    public function register_checkbox( $identifier, $label, $description = '', $default = false, $priority = 10 ) 
    {
        if ( !is_string( $identifier ) || strlen( $identifier ) === 0 ) {
            throw new \Exception( 'Checkbox identifier needs to be a non-empty string' );
        }

        if ( !is_string( $label ) || strlen( $label ) === 0 ) {
            throw new \Exception( 'Checkbox label needs to be a non-empty string' );
        }

        if ( !is_string( $description ) ) {
            throw new \Exception ( 'Checkbox description needs to be a string' );
        }

        $this->elements[$priority][] = array(
            'identifier' => $identifier,
            'label' => $label,
            'description' => $description,
            'type' => self::CHECKBOX_TYPE,
            'default' => $default
        );

        return $this;
    }

    /**
     * Use this method to register a select list element which will be rendered on
     * categories settings page.
     *
     * @param string $identifier
     * @param string $label
     * @param array $options
     * @param array $first_option
     * @param string $description
     * @param int $priority
     * @throws \Exception
     * @return $this
     */
    public function register_select_list( $identifier, $label, array $options, array $first_option = array(), $description = '', $priority = 10 ) 
    {
        if ( !is_string( $identifier ) || strlen( $identifier ) === 0 ) {
            throw new \Exception( 'Select list identifier needs to be a non-empty string' );
        }

        if ( !is_string( $label ) || strlen( $label ) === 0 ) {
            throw new \Exception( 'Select list label needs to be a non-empty string' );
        }

        if ( !is_string( $description ) ) {
            throw new \Exception( 'Select list description needs to be a string' );
        }

        // $first_option array can contain only value and description
        if ( !in_array( sizeof( $first_option ), array( 0, 2 ) ) ) {
            throw new \Exception( '$first_option array needs to contain only value and description' );
        }

        $this->elements[$priority][] = array(
            'identifier' => $identifier,
            'label' => $label,
            'description' => $description,
            'type' => self::SELECT_TYPE,
            'options' => $options,
            'first_option' => $first_option
        );

        return $this;
    }

    /**
     * Method renders a view for a checkbox input on category add or edit screen
     *
     * @param array $data - contains checkbox data defined when registering it
     * @param null|int $term_id - if null, method runs in 'add' context. If not,
     *                            it runs in 'edit' context
     * @throws \Exception
     */
    private function render_checkbox( $data, $term_id = null ) 
    {
        if ( !is_string( $data['identifier'] ) || strlen( $data['identifier'] ) === 0 ) {
            throw new \Exception( 'Checkbox identifier needs to be a non-empty string' );
        }

        if ( !is_string( $data['label'] ) || strlen( $data['label'] ) === 0 ) {
            throw new \Exception( 'Checkbox label needs to be a non-empty string' );
        }

        if ( !is_string( $data['description'] ) ) {
            throw new \Exception ( 'Checkbox description needs to be a string' );
        }

        $checked = $data['default'];
        $view = 'add';
        if ( !is_null( $term_id ) ) {
            $checked = self::get_checkbox_val( $term_id, $data['identifier'], $data['default'] );
            $view = 'edit';
        }

        $this->render_template( 'checkbox-' . $view . '.php', array(
            'identifier' => $data['identifier'],
            'label' => $data['label'],
            'description' => $data['description'],
            'checked' => $checked
        ), self::TEMPLATES_DIR );
    }

    private function render_select_list( $data, $term_id = null ) 
    {
        if ( !is_string( $data['identifier'] ) || strlen( $data['identifier'] ) === 0 ) {
            throw new \Exception( 'Select list identifier needs to be a non-empty string' );
        }

        if ( !is_string( $data['label'] ) || strlen( $data['label'] ) === 0 ) {
            throw new \Exception( 'Select list label needs to be a non-empty string' );
        }

        if ( !is_string( $data['description'] ) ) {
            throw new \Exception ( 'Select list description needs to be a string' );
        }

        $selected = null;
        $view = 'add';

        // edit mode
        if ( !is_null( $term_id ) ) {
            $view = 'edit';
            $selected = $val = get_woocommerce_term_meta( $term_id, $data['identifier'] );
        }

        $this->render_template( 'select-list-' . $view . '.php', array(
            'identifier' => $data['identifier'],
            'label' => $data['label'],
            'description' => $data['description'],
            'options' => $data['options'],
            'selected' => $selected,
            'first_option' => $data['first_option']
        ), self::TEMPLATES_DIR );
    }

    /**
     * Method stores the values for registered elements
     * @param int $term_id
     * @param type $tt_id
     * @param string $taxonomy
     * @return bool
     */
    public function save_values( $term_id, $tt_id, $taxonomy ) 
    {
        if ( $taxonomy !== 'product_cat' ) {
            return false;
        }

        foreach ( $this->elements as $prior_elements ) {

            foreach ( $prior_elements as $elem ) {

                $value = null;
                switch( $elem['type'] ) {

                    case self::CHECKBOX_TYPE :
                        $value = is_null( filter_input( INPUT_POST, $elem['identifier'] ) ) ? 0 : 1;
                        break;

                    case self::SELECT_TYPE:
                        $value = filter_input( INPUT_POST, $elem['identifier'] );
                        break;
                }
                update_woocommerce_term_meta( $term_id, $elem['identifier'], $value );
            }
        }

        return true;
    }

    /**
     * Sets a priority for wordpress hooks used in this vendor
     * (product_cat_add_form_fields and product_cat_edit_form_fields)
     * @param int $wp_priority
     * @throws \Exception
     */
    public function set_priority( $wp_priority ) 
    {
        if ( !filter_var( $wp_priority, FILTER_VALIDATE_INT ) ) {
            throw new \Exception( 'Need an integer in this point' );
        }

        $this->wp_priority = $wp_priority;
    }

    /**
     * Hook method which renders your custom settings on category add page
     */
    public function settings_add() 
    {
        $this->process_settings();
    }

    /**
     * Hook method which renders your custom settings on category edit page
     * @param type $term
     */
    public function settings_edit( $term ) 
    {
        $this->process_settings( $term->term_id );
    }

    /**
     * Render the template basing on given path
     * @param $name
     * @param array $args
     */
    private function render_template( $name, array $args = array() )
    {
        wc_get_template( $name, $args, '', trailingslashit( wp_helpers::get_plugin_path() .self::TEMPLATES_DIR ) );
    }
}