<?php
/**
 * WooCommerce General Settings
 *
 * @author        WooThemes
 * @category    Admin
 * @package    WooCommerce/Admin
 * @version     2.1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WC_Crm_Settings_General')) :

    /**
     * WC_Crm_Settings_General
     */
    class WC_Crm_Settings_General extends WC_Settings_Page
    {

        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->id = 'general_crm';
            $this->label = __('General', 'wc_crm');

            add_filter('wc_crm_settings_tabs_array', array($this, 'add_settings_page'), 20);
            add_action('wc_crm_settings_' . $this->id, array($this, 'output'));
            add_action('wc_crm_settings_save_' . $this->id, array($this, 'save'));

        }

        /**
         * Get settings array
         *
         * @return array
         */
        public function get_settings()
        {
            global $woocommerce, $wp_roles;
            $options = array(
                'user_agents' => __('Agent', 'wc_crm'),
                'user_roles' => __('User Roles', 'wc_crm'),
                'last_order' => __('Last Order', 'wc_crm'),
                'state' => __('State', 'wc_crm'),
                'city' => __('City', 'wc_crm'),
                'country' => __('Country', 'wc_crm'),
                'customer_name' => __('Customer Name', 'wc_crm'),
                'products' => __('Products', 'wc_crm'),
                'products_variations' => __('Products Variations', 'wc_crm'),
                'order_status' => __('Order Status', 'wc_crm'),
                'customer_status' => __('Customer Status', 'wc_crm'),
                'products_categories' => __('Product Categories', 'wc_crm'));
            if (class_exists('acf')) {
                $acf_options = get_acf_fields_array();
                $options = array_merge($options, $acf_options);
            }
            $statuses = wc_crm_get_statuses_slug();
            $settings = array();
            $filters = array(
                'name' => __('Filters', 'wc_crm'),
                'desc_tip' => 'Choose which filters you would like to display on the Customers page.',
                'id' => 'wc_crm_filters',
                'class' => 'chosen_select',
                'type' => 'multiselect',
                'options' => $options
            );

            $search_options = array(
                'name' => __('Search Parameters', 'wc_crm'),
                'desc_tip' => 'Set what parameters from the customers record to search in.',
                'id' => 'wc_crm_search_options',
                'class' => 'chosen_select',
                'type' => 'multiselect',
                'options' => array(
                    'billing_first_name' => __('Billing First Name', 'wc_crm'),
                    'billing_last_name' => __('Billing Last Name', 'wc_crm'),
                    'billing_company' => __('Billing Company', 'wc_crm'),
                    'billing_address_1' => __('Billing Address 1', 'wc_crm'),
                    'billing_address_2' => __('Billing Address 2', 'wc_crm'),
                    'billing_city' => __('Billing City', 'wc_crm'),
                    'billing_postcode' => __('Billing Postcode', 'wc_crm'),
                    'billing_country' => __('Billing Country', 'wc_crm'),
                    'billing_state' => __('Billing State', 'wc_crm'),
                    'billing_email' => __('Billing Email', 'wc_crm'),
                    'billing_phone' => __('Billing Phone', 'wc_crm'),
                    //'order_items' => __('Order items', 'wc_crm'),
                    'shipping_first_name' => __('Shipping First Name', 'wc_crm'),
                    'shipping_last_name' => __('Shipping Last Name', 'wc_crm'),
                    'shipping_company' => __('Shipping Company', 'wc_crm'),
                    'shipping_address_1' => __('Shipping Address 1', 'wc_crm'),
                    'shipping_address_2' => __('Shipping Address 2', 'wc_crm'),
                    'shipping_city' => __('Shipping City', 'wc_crm'),
                    'shipping_postcode' => __('Shipping Postcode', 'wc_crm'),
                    'shipping_country' => __('Shipping Country', 'wc_crm'),
                    'shipping_state' => __('Shipping State', 'wc_crm'),
                    'first_name' => __('First Name', 'wc_crm'),
                    'last_name' => __('Last Name', 'wc_crm'),
                    'email' => __('Email Address', 'wc_crm'),
                    'phone' => __('Phone', 'wc_crm'),
                    'fax' => __('Fax', 'wc_crm'),
                    'twitter' => __('Twitter', 'wc_crm'),
                    'skype' => __('Skype', 'wc_crm'),
                    'username' => __('Username', 'wc_crm'),
                ),
            );

            if (class_exists('WC_Brands_Admin')) {
                $filters['options']['products_brands'] = __('Product Brands', 'wc_crm');
            }

            $settings[] = array('title' => __('General Options', 'woocommerce'), 'type' => 'title', 'desc' => '', 'id' => 'general_crm_options');
            $settings[] = array(
                'name' => __('Username', 'wc_crm'),
                'desc_tip' => __('Choose what the username is when customers are added.', 'wc_crm'),
                'id' => 'wc_crm_username_add_customer',
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'options' => array(
                    1 => __('First & last name e.g. johnsmith', 'wc_crm'),
                    2 => __('Hyphen separated e.g. john-smith', 'wc_crm'),
                    3 => __('Email address', 'wc_crm')
                ),
                'autoload' => true
            );
            $settings[] = array(
                'name' => __('Secondary Label', 'wc_crm'),
                'desc_tip' => __('Choose which customer field you would like to appear underneath the customers name in the customers table.', 'wc_crm'),
                'id' => 'wc_crm_username_secondary_label',
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'default' => 'username',
                'options' => array(
                    'username' => __('Username', 'wc_crm'),
                    'company' => __('Company', 'wc_crm'),
                ),
                'autoload' => true
            );
            $settings[] = $filters;
            $settings[] = $search_options;
            $settings[] = array(
                'name' => __('Money Spent', 'wc_crm'),
                'desc_tip' => __('Choose which statuses the orders must be before counting towards the money spent value.', 'wc_crm'),
                'id' => 'wc_crm_total_value',
                'class' => 'wc-enhanced-select',
                'type' => 'multiselect',
                'options' => wc_get_order_statuses(),
            );
            $settings[] = array(
                'title' => __('Automatic Emails', 'wc_crm'),
                'desc' => __('Check this box to send an email with username and password when creating a new customer.', 'wc_crm'),
                'id' => 'wc_crm_automatic_emails_new_customer',
                'default' => 'yes',
                'type' => 'checkbox',
                'checkboxgroup' => 'start'
            );

            $settings[] = array(
                'name' => __('Email Action', 'wc_crm'),
                'desc_tip' => __('Choose how to open email composition from customers page.', 'wc_crm'),
                'id' => 'wc_crm_email_composition',
                'class' => 'wc-enhanced-select',
                'default' => 'new',
                'type' => 'select',
                'options' => array(
                    'new' => __('New Tab ', 'wc_crm'),
                    'existing' => __('Existing Window', 'wc_crm'),
                )
            );

            $settings[] = array(
                'name' => __('Order Action', 'wc_crm'),
                'desc_tip' => __('Choose how to create a new order from customers page', 'wc_crm'),
                'id' => 'wc_crm_new_order_composition',
                'class' => 'wc-enhanced-select',
                'default' => 'new',
                'type' => 'select',
                'options' => array(
                    'new' => __('New Tab ', 'wc_crm'),
                    'existing' => __('Existing Window', 'wc_crm'),
                )
            );

            if (class_exists('WC_Subscriptions')) {
                $settings[] = array(
                    'title' => __('Subscribers', 'wc_crm'),
                    'desc' => __('Check this box to show column indicating whether customer is an active subscriber.', 'wc_crm'),
                    'id' => 'wc_crm_show_subscribers_column',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'checkboxgroup' => 'start'
                );
            }
            if (class_exists('Groups_WordPress') && class_exists('Groups_WS')) {
                $settings[] = array(
                    'title' => __('Groups Integration', 'wc_crm'),
                    'desc' => __('Check this box to show column indicating which group is the customer a member of.', 'wc_crm'),
                    'id' => 'wc_crm_show_groups_wc_column',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'checkboxgroup' => 'start'
                );
            }

            $settings[] = array('type' => 'sectionend', 'id' => 'general_crm_options');
            $settings[] = array('title' => __('Google Maps', 'wc_crm'), 'type' => 'title', 'desc' => __('The following options affects how the Google Maps settings are loaded.', 'wc_crm'), 'id' => 'general_crm_options');

            $settings[] = array(
                'title' => __('Enable Google Maps', 'wc_crm'),
                'desc' => __('Check this box to enable google maps.', 'wc_crm'),
                'id' => 'wc_crm_enable_google_map',
                'default' => 'no',
                'type' => 'checkbox'
            );

            $settings[] = array(
                'title' => __('Google Map API', 'wc_crm'),
                'desc' => sprintf(__('Enter your Google Maps API key here, you can get a key from %shere%s.', 'wc_crm'),
                    '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">', '</a>'),
                'id' => 'wc_crm_google_map_api_key',
                'type' => 'text'
            );

            $settings[] = array(
                'name' => __('Google Map Address', 'wc_crm'),
                'id' => 'wc_crm_google_map_address',
                'desc_tip' => __('Check this box to send an email with username and password when creating a new customer.', 'wc_crm'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'default' => 'billing',
                'options' => array(
                    'billing' => __('Billing', 'wc_crm'),
                    'shipping' => __('Shipping', 'wc_crm'),
                ),
                'autoload' => true
            );

            $settings[] = array('type' => 'sectionend', 'id' => 'general_crm_options');
            $settings[] = array('title' => __('Fetch Customers', 'wc_crm'), 'type' => 'title', 'desc' => __('The following options affects how the customers in the customers table should be fetched.', 'wc_crm'), 'id' => 'crm_fetch_customers');
            $settings[] = array(
                'name' => __('User Roles', 'wc_crm'),
                'desc_tip' => 'Choose which User Roles of the customers/users that will be shown in the customers table.',
                'id' => 'wc_crm_user_roles',
                'type' => 'multiselect',
                'class' => 'chosen_select',
                'options' => $wp_roles->role_names,
            );
            $settings[] = array(
                'title' => __('Guest Customers', 'woocommerce'),
                'desc' => 'Select whether guest customers appear on the customers table.',
                'id' => 'wc_crm_guest_customers',
                'default' => 'no',
                'type' => 'checkbox',
                'checkboxgroup' => 'start'
            );
            $settings[] = array(
                'name' => __('Customer Name Format', 'wc_crm'),
                'desc_tip' => __('Choose the format of the names displayed on the Customers page.', 'wc_crm'),
                'id' => 'wc_crm_customer_name',
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'default' => 'fl',
                'options' => array(
                    'fl' => __('First, Last', 'wc_crm'),
                    'lf' => __('Last, First', 'wc_crm'),
                ),
            );

            $settings[] = array(
                'name' => __('Agent Visiblity', 'wc_crm'),
                'desc_tip' => __('Choose what customers the Agent can see.', 'wc_crm'),
                'id' => 'wc_crm_agent_customer_view',
                'class' => 'wc-enhanced-select',
                'default' => 'only_allowed',
                'type' => 'select',
                'options' => array(
                    'all' => __('All Customers', 'wc_crm'),
                    'only_allowed' => __('Only Assigned Customers', 'wc_crm'),
                )
            );
            $settings[] = array('type' => 'sectionend', 'id' => 'crm_fetch_customers');
            $settings[] = array('title' => __('Default Status', 'wc_crm'), 'type' => 'title', 'desc' => __('The following options determine the default status for the customers when added to this site.', 'wc_crm'), 'id' => 'crm_default_customer_status');
            $settings[] = array(
                'name' => __('Manually Added', 'wc_crm'),
                'desc_tip' => __('Added manually via this plugin.', 'wc_crm'),
                'id' => 'wc_crm_default_status_crm',
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'default' => 'Lead',
                'options' => $statuses,
            );
            $settings[] = array(
                'name' => __('Purchased Customers', 'wc_crm'),
                'desc_tip' => __('Added automatically via purchases made.', 'wc_crm'),
                'id' => 'wc_crm_default_status_store',
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'default' => 'Customer',
                'options' => $statuses,
            );

            $settings[] = array(
                'name' => __('Registration Page', 'wc_crm'),
                'desc_tip' => __('Added via the account registration page.', 'wc_crm'),
                'id' => 'wc_crm_default_status_account',
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'default' => 'Prospect',
                'options' => $statuses,
            );
            $settings[] = array('type' => 'sectionend', 'id' => 'crm_default_customer_status');
            $settings[] = array('title' => __('Orders Page', 'wc_crm'), 'type' => 'title','desc' => __('The following options determine the customer options available within order management.', 'wc_crm'), 'id' => 'crm_orders_page');
            $settings[] = array(
                'title' => __('Customer Status', 'woocommerce'),
                'desc' => __('Show customer status option on order details', 'wc_crm'),
                'desc_tip' => __('Allows you to change the customer status from the order details page.', 'wc_crm'),
                'id' => 'wc_crm_orders_customer',
                'type' => 'checkbox',
                'default' => 'no'
            );
            $settings[] = array(
                'name' => __('Customer Link', 'wc_crm'),
                'desc_tip' => __('Choose what the link of the customer is on the Orders page, customer or user profile.', 'wc_crm'),
                'id' => 'wc_crm_customer_link',
                'css' => '',
                'std' => '',
                'class' => 'wc-enhanced-select',
                'type' => 'select',
                'options' => array(
                    'customer' => __('Customer ', 'wc_crm'),
                    'user_profile' => __('User profile', 'wc_crm'),
                )
            );
            $settings[] = array('type' => 'sectionend', 'id' => 'crm_orders_page');

            return apply_filters('woocommerce_customer_relationship_general_settings_fields', $settings);

        }

        /**
         * Save settings
         */
        public function save()
        {
            $settings = $this->get_settings();

            WC_CRM_Admin_Settings::save_fields($settings);
        }

    }

endif;

return new WC_Crm_Settings_General();
