<?php

use  Carbon\Carbon ;
/**
 * Class Woocommerce_Pay_Per_Post_Public
 */
class Woocommerce_Pay_Per_Post_Public
{
    private  $plugin_name ;
    private  $version ;
    private  $template_path ;
    private  $available_templates ;
    public  $user_post_info ;
    private  $should_track_pageview = true ;
    public function __construct( $plugin_name, $version, $template_path )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->template_path = $template_path;
        $this->available_templates = array(
            'purchased'         => 'shortcode-purchased.php',
            'all'               => 'shortcode-all.php',
            'remaining'         => 'shortcode-remaining.php',
            'expiration-status' => 'expiration-status.php',
            'pageview-status'   => 'pageview-status.php',
        );
    }
    
    public function should_disable_comments()
    {
        $turn_off_comments_completely_to_everyone_on_protected_posts = (bool) get_option( $this->plugin_name . '_turn_off_comments_when_protected', true );
        $allow_admins_access_to_protected_posts = get_option( $this->plugin_name . '_allow_admins_access_to_protected_posts', false );
        $is_protected = Woocommerce_Pay_Per_Post_Helper::is_protected( get_the_ID() );
        
        if ( $turn_off_comments_completely_to_everyone_on_protected_posts && $is_protected ) {
            add_filter( 'comments_open', array( $this, 'comments_closed' ) );
            add_filter( 'get_comments_number', array( $this, 'comments_count_zero' ) );
        }
    
    }
    
    public function register_shortcodes()
    {
        add_shortcode( 'woocommerce-payperpost', array( $this, 'process_shortcode' ) );
    }
    
    /**
     * @param $atts
     *
     * @return string
     */
    public function process_shortcode( $atts )
    {
        $template = 'purchased';
        $orderby = 'post_date';
        $order = 'DESC';
        if ( isset( $atts['template'] ) && array_key_exists( $atts['template'], $this->available_templates ) ) {
            $template = $atts['template'];
        }
        $custom_post_types = get_option( $this->plugin_name . '_custom_post_types', array() );
        $custom_post_types = ( empty($custom_post_types) ? array() : $custom_post_types );
        if ( !is_array( $custom_post_types ) ) {
            $custom_post_types = explode( ',', $custom_post_types );
        }
        $args = array(
            'orderby'      => $orderby,
            'order'        => $order,
            'nopaging'     => true,
            'meta_key'     => $this->plugin_name . '_product_ids',
            'meta_value'   => '0',
            'meta_compare' => '>',
            'post_status'  => 'publish',
            'post_type'    => $custom_post_types,
        );
        Woocommerce_Pay_Per_Post_Helper::logger( print_r( $args, true ) );
        // Get all posts that are protected by WC PPP.  We do this by checking to see if they have a product_ids associated with post.
        $get_ppp_args = apply_filters( 'wc_pay_per_post_args', $args );
        $ppp_posts = get_posts( $get_ppp_args );
        ob_start();
        switch ( $template ) {
            case 'purchased':
                $this->shortcode_purchased( $template, $ppp_posts );
                break;
            case 'remaining':
                $this->shortcode_remaining( $template, $ppp_posts );
                break;
            case 'all':
                $this->shortcode_all( $template, $ppp_posts );
                break;
        }
        return ob_get_clean();
    }
    
    /**
     * @param $args
     *
     * @return mixed
     */
    public function get_ppp_args( $args )
    {
        return $args;
    }
    
    /**
     * @param $template
     * @param $ppp_posts
     */
    protected function shortcode_purchased( $template, $ppp_posts )
    {
        $purchased = [];
        
        if ( is_user_logged_in() ) {
            foreach ( $ppp_posts as $post ) {
                if ( $this->has_purchased_products( $post->ID ) ) {
                    $purchased[] = $post;
                }
            }
            $template_file = ( locate_template( $this->template_path . $this->available_templates[$template] ) ?: plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/' . $this->available_templates[$template] );
            require $template_file;
        }
    
    }
    
    /**
     * @param $template
     * @param $ppp_posts
     */
    protected function shortcode_remaining( $template, $ppp_posts )
    {
        $remaining = [];
        
        if ( is_user_logged_in() ) {
            foreach ( $ppp_posts as $post ) {
                if ( !$this->has_purchased_products( $post->ID ) ) {
                    $remaining[] = $post;
                }
            }
            $template_file = ( locate_template( $this->template_path . $this->available_templates[$template] ) ?: plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/' . $this->available_templates[$template] );
            require $template_file;
        }
    
    }
    
    /**
     * @param $template
     * @param $ppp_posts
     */
    protected function shortcode_all( $template, $ppp_posts )
    {
        $template_file = ( locate_template( $this->template_path . $this->available_templates[$template] ) ?: plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/' . $this->available_templates[$template] );
        require $template_file;
    }
    
    /**
     * @param null $content
     *
     * @return bool|null|string
     */
    public function restrict_content( $content = null )
    {
        $post_id = get_the_ID();
        if ( is_admin() || !$post_id ) {
            return $content;
        }
        
        if ( is_null( $content ) ) {
            $return_content = false;
        } else {
            $return_content = true;
        }
        
        $admins_allowed_access = (bool) get_option( $this->plugin_name . '_allow_admins_access_to_protected_posts', false );
        // Check and see if admins are allowed to view protected content.
        if ( $admins_allowed_access && $return_content && is_super_admin() ) {
            return $content;
        }
        $is_protected = Woocommerce_Pay_Per_Post_Helper::is_protected( $post_id );
        
        if ( $is_protected ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id );
            Woocommerce_Pay_Per_Post_Helper::logger( 'Protection Type: ' . $is_protected );
            $should_show_paywall = $this->should_show_paywall( $is_protected );
            Woocommerce_Pay_Per_Post_Helper::logger( 'Should Show Paywall? - ' . (( $should_show_paywall ? 'true' : 'false' )) );
            // Show Paywall if user is NOT logged in.
            
            if ( $should_show_paywall && !is_user_logged_in() ) {
                Woocommerce_Pay_Per_Post_Helper::logger( 'Paywall is to be Shown and User has NOT LOGGED IN' );
                
                if ( $return_content ) {
                    return $this->show_paywall();
                } else {
                    return false;
                }
            
            }
            
            $has_purchased = $this->has_purchased_products();
            Woocommerce_Pay_Per_Post_Helper::logger( 'Has Purchased Product? - ' . (( $has_purchased ? 'true' : 'false' )) );
            // Show Paywall if user has NOT purchased product no matter what.
            
            if ( $should_show_paywall && !$has_purchased ) {
                Woocommerce_Pay_Per_Post_Helper::logger( 'Paywall is to be Shown and User has NOT purchased product' );
                
                if ( $return_content ) {
                    return $this->show_paywall();
                } else {
                    return false;
                }
            
            }
            
            $has_access = $this->has_access( $is_protected );
            Woocommerce_Pay_Per_Post_Helper::logger( 'User Has Access? - ' . (( $has_access ? 'true' : 'false' )) );
            // If user has purchased product, but fails the has_access function show paywall.
            
            if ( $should_show_paywall && $has_purchased && !$has_access ) {
                Woocommerce_Pay_Per_Post_Helper::logger( 'Paywall is to be Shown and User HAS purchased product but does NOT have access' );
                
                if ( $return_content ) {
                    return $this->show_paywall();
                } else {
                    return false;
                }
            
            }
        
        }
        
        
        if ( $return_content ) {
            $show_warnings = get_post_meta( $post_id, $this->plugin_name . '_show_warnings', true );
            if ( $show_warnings ) {
                switch ( $is_protected ) {
                    case 'page-view':
                        ob_start();
                        $template = 'pageview-status';
                        $user_info = $this->user_post_info;
                        $number_of_allowed_pageviews = get_post_meta( $post_id, $this->plugin_name . '_page_view_restriction', true );
                        require ( locate_template( $this->template_path . $this->available_templates[$template] ) ?: plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/' . $this->available_templates[$template] );
                        $template_file = ob_get_clean();
                        return $template_file . $content;
                        break;
                    case 'expire':
                        ob_start();
                        $template = 'expiration-status';
                        $user_info = $this->user_post_info;
                        require ( locate_template( $this->template_path . $this->available_templates[$template] ) ?: plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/' . $this->available_templates[$template] );
                        $template_file = ob_get_clean();
                        return $template_file . $content;
                        break;
                    default:
                        return $content;
                }
            }
            return $content;
        } else {
            return true;
        }
    
    }
    
    /**
     * @return string
     */
    public function show_paywall()
    {
        $post_id = get_the_ID();
        $paywall_content = $this->get_paywall_content( $post_id );
        return $paywall_content;
    }
    
    protected function show_comments( $has_purchased = false )
    {
        $turn_off_comments_when_protected = get_option( $this->plugin_name . '_turn_off_comments_when_protected', true );
        $allow_admins_access_to_protected_posts = get_option( $this->plugin_name . '_allow_admins_access_to_protected_posts', false );
        if ( $turn_off_comments_when_protected ) {
            add_filter( 'comments_open', array( $this, 'comments_closed' ) );
        }
    }
    
    /**
     * @param $protection_type
     *
     * @return bool
     */
    protected function has_access( $protection_type )
    {
        switch ( $protection_type ) {
            case 'standard':
                // Since we already check to see if they purchased the product standard protection returns true all the time.
            // Since we already check to see if they purchased the product standard protection returns true all the time.
            case 'delay':
                // Delay protection is same protection as standard, just difference in when to display pay wall, we already checked to see if they purchased product we return true.
                return true;
                break;
            case 'page-view':
                return $this->has_access_page_view_protection__premium_only();
                break;
            case 'expire':
                return $this->has_access_expiry_protection__premium_only();
                break;
        }
        return true;
    }
    
    /**
     * @param $is_protected
     *
     * @return bool
     */
    protected function should_show_paywall( $is_protected )
    {
        switch ( $is_protected ) {
            case 'standard':
            case 'page-view':
            case 'expire':
                return true;
                break;
            case 'delay':
                return $this->enable_delay_protection_paywall__premium_only();
                break;
        }
        return true;
    }
    
    /**
     * @return bool
     */
    public function comments_open()
    {
        return true;
    }
    
    /**
     * @return bool
     */
    public function comments_closed()
    {
        return false;
    }
    
    /**
     * @return bool
     */
    public function is_admin()
    {
        $current_user = wp_get_current_user();
        if ( user_can( $current_user, 'administrator' ) ) {
            return true;
        }
        return false;
    }
    
    /**
     * @return int
     */
    public function comments_count_zero()
    {
        return 0;
    }
    
    /**
     * @return int
     */
    public function comments_count()
    {
        return get_comments_number();
    }
    
    /**
     * @param null $post_id
     *
     * @return bool
     */
    public function has_purchased_products( $post_id = null )
    {
        if ( is_null( $post_id ) ) {
            $post_id = get_the_ID();
        }
        $product_ids = array();
        $product_ids = get_post_meta( $post_id, $this->plugin_name . '_product_ids', true );
        $current_user = wp_get_current_user();
        foreach ( (array) $product_ids as $id ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Looking to see if purchased product ' . trim( $id ) );
            if ( wc_customer_bought_product( $current_user->user_email, $current_user->ID, trim( $id ) ) ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @param $post_id
     *
     * @return string
     */
    protected function get_paywall_content( $post_id )
    {
        $default_paywall_content = get_option( $this->plugin_name . '_restricted_content_default', _x( "<h1>Oops, Restricted Content</h1><p>We are sorry but this post is restricted to folks that have purchased this page.</p>[products ids='{{product_id}}']", 'wc_pay_per_post' ) );
        $override_paywall_content = get_post_meta( $post_id, $this->plugin_name . '_restricted_content_override', true );
        $product_ids = get_post_meta( $post_id, $this->plugin_name . '_product_ids', true );
        $parent_id = wp_get_post_parent_id( $product_ids[0] );
        $product_ids = str_replace( ' ', '', $product_ids );
        $product_ids = implode( ',', (array) $product_ids );
        $paywall_content = ( empty($override_paywall_content) ? $default_paywall_content : $override_paywall_content );
        $return_content = str_replace( '{{product_id}}', $product_ids, $paywall_content );
        $return_content = str_replace( '{{parent_id}}', $parent_id, $return_content );
        return do_shortcode( $return_content );
    }

}