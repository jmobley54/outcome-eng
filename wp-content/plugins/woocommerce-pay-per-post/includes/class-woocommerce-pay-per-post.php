<?php

class Woocommerce_Pay_Per_Post
{
    protected  $loader ;
    protected  $plugin_name ;
    protected  $version ;
    protected  $template_path ;
    public function __construct()
    {
        
        if ( defined( 'WC_PPP_PLUGIN_VERSION' ) ) {
            $this->version = WC_PPP_PLUGIN_VERSION;
        } else {
            $this->version = '2.1.16';
        }
        
        $this->plugin_name = 'wc_pay_per_post';
        $this->template_path = 'woocommerce-pay-per-post/';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }
    
    private function define_admin_hooks()
    {
        $plugin_admin = new Woocommerce_Pay_Per_Post_Admin( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_options' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'options_init' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'meta_box' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'save_meta_box' );
        $this->loader->add_action(
            'in_plugin_update_message-woocommerce-pay-per-post/woocommerce-pay-per-post.php',
            $plugin_admin,
            'prefix_plugin_update_message',
            10,
            2
        );
        foreach ( $plugin_admin->get_post_types() as $post_type ) {
            $this->loader->add_action(
                'manage_' . $post_type . '_posts_custom_column',
                $plugin_admin,
                'manage_custom_column',
                10,
                2
            );
            $this->loader->add_filter( 'manage_' . $post_type . '_posts_columns', $plugin_admin, 'manage_columns' );
            $this->loader->add_filter( 'manage_edit-' . $post_type . '_sortable_columns', $plugin_admin, 'sortable_columns' );
        }
        $this->loader->add_filter(
            'plugin_action_links_woocommerce-pay-per-post/woocommerce-pay-per-post.php',
            $plugin_admin,
            'plugin_settings_link',
            10,
            2
        );
        $this->loader->add_filter(
            'wc_pay_per_post_all_product_args',
            $plugin_admin,
            'get_all_product_args',
            10,
            2
        );
        $this->loader->add_filter(
            'wc_pay_per_post_virtual_product_args',
            $plugin_admin,
            'get_virtual_product_args',
            10,
            2
        );
    }
    
    private function define_public_hooks()
    {
        $plugin_public = new Woocommerce_Pay_Per_Post_Public( $this->get_plugin_name(), $this->get_version(), $this->get_template_path() );
        $this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
        $this->loader->add_action( 'get_header', $plugin_public, 'should_disable_comments' );
        $this->loader->add_filter( 'the_content', $plugin_public, 'restrict_content' );
        $this->loader->add_filter( 'wc_pay_per_post_args', $plugin_public, 'get_ppp_args' );
    }
    
    private function load_dependencies()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-pay-per-post-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-pay-per-post-i18n.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-pay-per-post-helper.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-pay-per-post-logger.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-pay-per-post-deprecated.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-pay-per-post-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-pay-per-post-public.php';
        $this->loader = new Woocommerce_Pay_Per_Post_Loader();
    }
    
    private function set_locale()
    {
        $plugin_i18n = new Woocommerce_Pay_Per_Post_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }
    
    public function run()
    {
        $this->loader->run();
    }
    
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }
    
    public function get_loader()
    {
        return $this->loader;
    }
    
    public function get_version()
    {
        return $this->version;
    }
    
    public function get_template_path()
    {
        return $this->template_path;
    }

}