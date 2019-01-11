<?php

class Woocommerce_Pay_Per_Post_Admin
{
    private  $plugin_name ;
    private  $version ;
    private  $allowed_restriction_frequency = array(
        'minute',
        'hour',
        'day',
        'week',
        'month',
        'year'
    ) ;
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    public function add_plugin_options()
    {
        add_menu_page(
            __( 'WooCommerce PayPerPost', 'wc_pay_per_post' ),
            'WooCommerce PayPerPost',
            'manage_options',
            $this->plugin_name,
            array( $this, 'create_options_page' ),
            'dashicons-cart',
            99
        );
        add_submenu_page(
            $this->plugin_name,
            'Settings',
            'Settings',
            'manage_options',
            $this->plugin_name . '-settings',
            array( $this, 'create_options_page' )
        );
        add_submenu_page(
            $this->plugin_name,
            'What\'s New',
            'What\'s New',
            'manage_options',
            $this->plugin_name . '-whats-new',
            array( $this, 'create_whatsnew_page' )
        );
        add_submenu_page(
            $this->plugin_name,
            'Help',
            'Help',
            'manage_options',
            $this->plugin_name . '-help',
            array( $this, 'create_help_page' )
        );
        remove_submenu_page( $this->plugin_name, $this->plugin_name );
    }
    
    public function enqueue_scripts()
    {
        wp_register_script(
            $this->plugin_name . '_admin',
            plugin_dir_url( __FILE__ ) . 'js/wc-ppp-admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_register_script(
            $this->plugin_name . '_select2',
            '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js',
            array(),
            '4.0.6',
            false
        );
    }
    
    public function enqueue_styles()
    {
        wp_register_style(
            $this->plugin_name . '_admin',
            plugin_dir_url( __FILE__ ) . 'css/wc-ppp-admin.css',
            array(),
            $this->version,
            'all'
        );
        wp_register_style(
            $this->plugin_name . '_select2',
            '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"',
            array(),
            $this->version,
            'all'
        );
    }
    
    public function ajax_post_types()
    {
        $post_types = array();
        foreach ( get_post_types( array(
            'public' => true,
        ), 'names' ) as $post_type ) {
            $post_types[] = $post_type;
        }
        return $post_types;
    }
    
    public function create_options_page()
    {
        global  $wcppp_freemius ;
        $restricted_content_default = get_option( $this->plugin_name . '_restricted_content_default', _x( "<h1>Oops, Restricted Content</h1>\n<p>We are sorry but this post is restricted to folks that have purchased this page.</p>\n\n[products ids='{{product_id}}']", 'Default restricted content', 'wc_pay_per_post' ) );
        $custom_post_types = get_option( $this->plugin_name . '_custom_post_types', array() );
        $custom_post_types = ( empty($custom_post_types) ? array() : $custom_post_types );
        if ( !is_array( $custom_post_types ) ) {
            $custom_post_types = explode( ',', $custom_post_types );
        }
        $turn_off_comments_when_protected = get_option( $this->plugin_name . '_turn_off_comments_when_protected', true );
        $allow_admins_access_to_protected_posts = get_option( $this->plugin_name . '_allow_admins_access_to_protected_posts', false );
        $enable_debugging = get_option( $this->plugin_name . '_enable_debugging', false );
        $delete_settings = get_option( $this->plugin_name . '_delete_settings', false );
        $available_post_types = $this->ajax_post_types();
        $only_show_virtual_products = get_option( $this->plugin_name . '_only_show_virtual_products', false );
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( 'You do not have sufficient permissions to access this page.' ) );
        }
        wp_enqueue_style( $this->plugin_name . '_admin' );
        wp_enqueue_style( $this->plugin_name . '_select2' );
        wp_enqueue_script( $this->plugin_name . '_select2' );
        wp_enqueue_script( $this->plugin_name . '_admin' );
        require_once plugin_dir_path( __FILE__ ) . 'partials/settings.php';
    }
    
    public function create_help_page()
    {
        wp_enqueue_style( $this->plugin_name . '_admin' );
        wp_enqueue_style( $this->plugin_name . '_select2' );
        wp_enqueue_script( $this->plugin_name . '_select2' );
        wp_enqueue_script( $this->plugin_name . '_admin' );
        require_once plugin_dir_path( __FILE__ ) . 'partials/help.php';
    }
    
    public function create_whatsnew_page()
    {
        $needs_upgrade = get_option( $this->plugin_name . '_needs_upgrade', 'true' );
        $custom_post_types = get_option( $this->plugin_name . '_custom_post_types', array() );
        $custom_post_types = ( empty($custom_post_types) ? array() : $custom_post_types );
        if ( !is_array( $custom_post_types ) ) {
            $custom_post_types = explode( ',', $custom_post_types );
        }
        $old_products = new WP_Query( array(
            'post_type' => $custom_post_types,
            'meta_key'  => 'woocommerce_ppp_product_id',
            'nopaging'  => true,
        ) );
        if ( isset( $_POST['wc_ppp_upgrade_nonce'] ) && wp_verify_nonce( $_POST['wc_ppp_upgrade_nonce'], 'wc_ppp_upgrade' ) ) {
            $this->upgrade_database( $old_products );
        }
        require_once plugin_dir_path( __FILE__ ) . 'partials/whats-new.php';
    }
    
    protected function upgrade_database( $products )
    {
        foreach ( $products->posts as $post ) {
            // Get old meta key for product id's associated with posts.
            $post_meta = get_post_meta( $post->ID, 'woocommerce_ppp_product_id', true );
            
            if ( '' !== $post_meta ) {
                // Added in to account for fields that were there but with no products associated with them.
                $old_ppp_ids = explode( ',', $post_meta );
                update_post_meta( $post->ID, $this->plugin_name . '_product_ids', $old_ppp_ids );
            }
        
        }
        update_option( 'wc_pay_per_post_needs_upgrade', 'false', false );
        update_option( 'wc_pay_per_post_db_version', WC_PPP_PLUGIN_VERSION, false );
        $url = admin_url( 'admin.php?page=' . $this->plugin_name . '-whats-new&upgrade_complete=true' );
        wp_safe_redirect( $url );
    }
    
    public function meta_box()
    {
        $post_types = $this->get_post_types();
        foreach ( $post_types as $post_type ) {
            add_meta_box(
                $this->plugin_name . '_meta_box',
                __( 'WooCommerce Pay Per Post', 'wc_pay_per_post' ),
                array( $this, 'output_meta_box' ),
                $post_type,
                'normal',
                'high',
                array(
                '__block_editor_compatible_meta_box' => true,
            )
            );
        }
    }
    
    public function get_post_types()
    {
        $user_included_post_types = get_option( $this->plugin_name . '_custom_post_types', array() );
        if ( '' === $user_included_post_types || empty($user_included_post_types) ) {
            $user_included_post_types = array();
        }
        return (array) $user_included_post_types;
    }
    
    public function output_meta_box()
    {
        ob_start();
        global  $post ;
        $id = $post->ID;
        $selected = get_post_meta( $id, $this->plugin_name . '_product_ids', true );
        $restricted_content_override = get_post_meta( $id, $this->plugin_name . '_restricted_content_override', true );
        $delay_restriction_enable = get_post_meta( $id, $this->plugin_name . '_delay_restriction_enable', true );
        $delay_restriction = get_post_meta( $id, $this->plugin_name . '_delay_restriction', true );
        $delay_restriction_frequency = get_post_meta( $id, $this->plugin_name . '_delay_restriction_frequency', true );
        $page_view_restriction_enable = get_post_meta( $id, $this->plugin_name . '_page_view_restriction_enable', true );
        $page_view_restriction = get_post_meta( $id, $this->plugin_name . '_page_view_restriction', true );
        $page_view_restriction_frequency = get_post_meta( $id, $this->plugin_name . '_page_view_restriction_frequency', true );
        $page_view_restriction_enable_time_frame = get_post_meta( $id, $this->plugin_name . '_page_view_restriction_enable_time_frame', true );
        $page_view_restriction_time_frame = get_post_meta( $id, $this->plugin_name . '_page_view_restriction_time_frame', true );
        $expire_restriction_enable = get_post_meta( $id, $this->plugin_name . '_expire_restriction_enable', true );
        $expire_restriction = get_post_meta( $id, $this->plugin_name . '_expire_restriction', true );
        $expire_restriction_frequency = get_post_meta( $id, $this->plugin_name . '_expire_restriction_frequency', true );
        $show_warnings = get_post_meta( $id, $this->plugin_name . '_show_warnings', true );
        $drop_down = $this->generate_products_dropdown( $selected );
        wp_enqueue_style( $this->plugin_name . '_admin' );
        wp_enqueue_style( $this->plugin_name . '_select2' );
        wp_enqueue_script( $this->plugin_name . '_select2' );
        wp_enqueue_script( $this->plugin_name . '_admin' );
        require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/meta-box-base.php';
        echo  ob_get_clean() ;
    }
    
    protected function get_post_products_custom_field( $value, $id = null )
    {
        $custom_field = get_post_meta( $id, $value, true );
        
        if ( !empty($custom_field) ) {
            return ( is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) ) );
        } else {
            return false;
        }
    
    }
    
    protected function generate_products_dropdown( $selected = array() )
    {
        $selected = ( empty($selected) ? array() : $selected );
        $only_show_virtual_products = (bool) get_option( $this->plugin_name . '_only_show_virtual_products', false );
        
        if ( $only_show_virtual_products ) {
            $products = apply_filters( 'wc_pay_per_post_get_virtual_products', $this->get_virtual_products() );
        } else {
            $products = apply_filters( 'wc_pay_per_post_get_all_products', $this->get_all_products() );
        }
        
        $drop_down = '<select id="' . $this->plugin_name . '_product_ids" name="' . $this->plugin_name . '_product_ids[]" style="width: 100%" multiple="multiple">';
        foreach ( $products as $product ) {
            $drop_down .= '<option value="' . $product['ID'] . '"';
            if ( in_array( (string) $product['ID'], $selected, true ) ) {
                $drop_down .= ' selected="selected"';
            }
            $drop_down .= '>' . $product['post_title'] . ' - [#' . $product['ID'] . ']</option>';
        }
        $drop_down .= '</select>';
        return $drop_down;
    }
    
    public function get_all_product_args( $args )
    {
        return $args;
    }
    
    protected function get_all_products()
    {
        $args = array(
            'post_type' => [ 'product' ],
            'orderby'   => 'title',
            'order'     => 'ASC',
            'nopaging'  => true,
        );
        $get_args = apply_filters( 'wc_pay_per_post_all_product_args', $args );
        $products = get_posts( $get_args );
        $return = array();
        foreach ( $products as $product ) {
            $return[] = [
                'ID'         => $product->ID,
                'post_title' => $product->post_title,
            ];
        }
        return $return;
    }
    
    protected function get_virtual_products()
    {
        $args = array(
            'post_type' => [ 'product' ],
            'orderby'   => 'title',
            'order'     => 'ASC',
            'nopaging'  => true,
        );
        $get_args = apply_filters( 'wc_pay_per_post_virtual_product_args', $args );
        $products = get_posts( $get_args );
        $return = array();
        foreach ( $products as $product ) {
            $is_virtual = get_post_meta( $product->ID, '_virtual', true );
            $is_downloadable = get_post_meta( $product->ID, '_downloadable', true );
            if ( 'yes' === $is_virtual || 'yes' === $is_downloadable ) {
                $return[] = [
                    'ID'         => $product->ID,
                    'post_title' => $product->post_title,
                ];
            }
        }
        return $return;
    }
    
    public function get_virtual_product_args( $args )
    {
        return $args;
    }
    
    public function save_meta_box( $post_id )
    {
        // Stop the script when doing autosave.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        // Verify the nonce. If insn't there, stop the script.
        if ( !isset( $_POST[$this->plugin_name . '_nonce'] ) || !wp_verify_nonce( $_POST[$this->plugin_name . '_nonce'], $this->plugin_name . '_nonce' ) ) {
            return;
        }
        // Stop the script if the user does not have edit permissions.
        if ( !current_user_can( 'edit_posts' ) ) {
            return;
        }
        // Boolean Values.
        $_delay_restriction_enable = 0;
        $_page_view_restriction_enable = 0;
        $_page_view_restriction_enable_time_frame = 0;
        $_expire_restriction_enable = 0;
        $_show_warnings = 0;
        Woocommerce_Pay_Per_Post_Helper::logger( 'Saving Meta Box Data ' . print_r( $_POST, true ) );
        // Save the product_id's associated with page/post.
        
        if ( isset( $_POST[$this->plugin_name . '_product_ids'] ) ) {
            update_post_meta( $post_id, $this->plugin_name . '_product_ids', $_POST[$this->plugin_name . '_product_ids'] );
        } else {
            update_post_meta( $post_id, $this->plugin_name . '_product_ids', '' );
        }
    
    }
    
    public function manage_custom_column( $column, $post_id )
    {
        
        if ( $column === $this->plugin_name . '_protected' ) {
            $protected = Woocommerce_Pay_Per_Post_Helper::is_protected( $post_id );
            if ( $protected ) {
                echo  Woocommerce_Pay_Per_Post_Helper::protection_display_icon( $protected ) ;
            }
        }
    
    }
    
    public function manage_columns( $columns )
    {
        $columns[$this->plugin_name . '_protected'] = 'Pay Per Post';
        return $columns;
    }
    
    public function sortable_columns( $columns )
    {
        $columns[$this->plugin_name . '_protected'] = $this->plugin_name . '_protected';
        return $columns;
    }
    
    public function options_init()
    {
        register_setting( $this->plugin_name . '_settings', $this->plugin_name . '_restricted_content_default' );
        register_setting( $this->plugin_name . '_settings', $this->plugin_name . '_custom_post_types' );
        register_setting( $this->plugin_name . '_settings', $this->plugin_name . '_only_show_virtual_products' );
        register_setting( $this->plugin_name . '_settings', $this->plugin_name . '_turn_off_comments_when_protected' );
        register_setting( $this->plugin_name . '_settings', $this->plugin_name . '_allow_admins_access_to_protected_posts' );
        register_setting( $this->plugin_name . '_settings', $this->plugin_name . '_enable_debugging' );
        register_setting( $this->plugin_name . '_settings', $this->plugin_name . '_delete_settings' );
    }
    
    public function plugin_settings_link( $links )
    {
        $url = admin_url( 'options-general.php?page=' . $this->plugin_name );
        $_link = '<a href="' . $url . '">' . __( 'Settings', 'wc_pay_per_post' ) . '</a>';
        $links[] = $_link;
        return $links;
    }
    
    public function prefix_plugin_update_message( $data, $response )
    {
        if ( isset( $data['upgrade_notice'] ) ) {
            printf( '<div class="update-message">%s</div>', esc_html( wpautop( $data['upgrade_notice'] ) ) );
        }
    }

}