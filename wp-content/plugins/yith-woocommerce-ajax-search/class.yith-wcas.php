<?php
/**
 * Main class
 *
 * @author YITH
 * @package YITH WooCommerce Ajax Search
 * @version 1.1.1
 */

if ( !defined( 'YITH_WCAS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCAS' ) ) {
    /**
     * YITH WooCommerce Ajax Search
     *
     * @since 1.0.0
     */
    class YITH_WCAS {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version = YITH_WCAS_VERSION;

        /**
         * Plugin object
         *
         * @var string
         * @since 1.0.0
         */
        public $obj = null;

        /**
         * Constructor
         *
         * @return mixed|YITH_WCAS_Admin|YITH_WCAS_Frontend
         * @since 1.0.0
         */
        public function __construct() {

	        $this->obj = false;

	        // Load Plugin Framework
	        if( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != 'yith_ajax_search_products' ) {
		        add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

		        if ( is_admin() ) {
			        $this->obj = new YITH_WCAS_Admin( $this->version );

		        }else {
			        $this->obj = new YITH_WCAS_Frontend( $this->version );
		        }
	        }else{
		        if ( class_exists( 'YITH_JetPack' ) ) {
			        include_once( YJP_DIR . 'plugin-fw/yit-woocommerce-compatibility.php' );
		        } else {
			        include_once( YITH_WCAS_DIR . 'plugin-fw/yit-woocommerce-compatibility.php' );
		        }
	        }


            // actions
            add_action( 'widgets_init', array( $this, 'registerWidgets' ) );

            add_action( 'wp_ajax_yith_ajax_search_products', array( $this, 'ajax_search_products' ) );
            add_action( 'wp_ajax_nopriv_yith_ajax_search_products', array( $this, 'ajax_search_products' ) );

            //register shortcode
            add_shortcode( 'yith_woocommerce_ajax_search', array( $this, 'add_woo_ajax_search_shortcode' ) );

            return $this->obj;
        }


        /**
         * Load Plugin Framework
         *
         * @since  1.0
         * @access public
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if( ! empty( $plugin_fw_data ) ){
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }



        /**
         * Load template for [yith_woocommerce_ajax_search] shortcode
         *
         * @access public
         *
         * @param $args array
         *
         * @return void
         * @since  1.0.0
         */
        public function add_woo_ajax_search_shortcode( $args = array() ) {
            $args            = shortcode_atts( array(), $args );
            ob_start();
            $wc_get_template = function_exists( 'wc_get_template' ) ? 'wc_get_template' : 'woocommerce_get_template';
            $wc_get_template( 'yith-woocommerce-ajax-search.php', $args, '', YITH_WCAS_DIR . 'templates/' );
            return ob_get_clean();
        }

        /**
         * Load and register widgets
         *
         * @access public
         * @since  1.0.0
         */
        public function registerWidgets() {
            register_widget( 'YITH_WCAS_Ajax_Search_Widget' );
        }


        /**
         * Perform ajax search products
         */
        public function ajax_search_products() {
            global $woocommerce;
	        $time_start         = getmicrotime();
	        $transient_enabled  = get_option( 'yith_wcas_enable_transient', 'no' );
	        $transient_duration = get_option( 'yith_wcas_transient_duration', 12 );

            $search_keyword =  $_REQUEST['query'];

            $ordering_args = $woocommerce->query->get_catalog_ordering_args( 'title', 'asc' );
            $suggestions   = array();

	        $transient_name = 'ywcas_' . $search_keyword;
	        if ( $transient_enabled == 'no' || false === ( $suggestions = get_transient( $transient_name ) ) ) {
		        $args = array(
			        's'                   => apply_filters( 'yith_wcas_ajax_search_products_search_query', $search_keyword ),
			        'post_type'           => 'product',
			        'post_status'         => 'publish',
			        'ignore_sticky_posts' => 1,
			        'orderby'             => $ordering_args['orderby'],
			        'order'               => $ordering_args['order'],
			        'posts_per_page'      => apply_filters( 'yith_wcas_ajax_search_products_posts_per_page', get_option( 'yith_wcas_posts_per_page' ) ),
			        'suppress_filters'    => false
		        );

		        if ( isset( $_REQUEST['product_cat'] ) ) {
			        $args['tax_query'] = array(
				        'relation' => 'AND',
				        array(
					        'taxonomy' => 'product_cat',
					        'field'    => 'slug',
					        'terms'    => $_REQUEST['product_cat']
				        )
			        );
		        }

		        if ( version_compare( WC()->version, '2.7.0', '<' ) ) {
			        $args['meta_query'] = array(
				        array(
					        'key'     => '_visibility',
					        'value'   => array( 'search', 'visible' ),
					        'compare' => 'IN'
				        ),
			        );
		        }else{
			        $product_visibility_term_ids = wc_get_product_visibility_term_ids();
			        $args['tax_query'][] = array(
				        'taxonomy' => 'product_visibility',
				        'field'    => 'term_taxonomy_id',
				        'terms'    => $product_visibility_term_ids['exclude-from-search'],
				        'operator' => 'NOT IN',
			        );
		        }


		        $products = get_posts( $args );

		        if ( ! empty( $products ) ) {
			        foreach ( $products as $post ) {
				        $product = wc_get_product( $post );

				        $suggestions[] = apply_filters( 'yith_wcas_suggestion', array(
					        'id'    => $product->get_id(),
					        'value' => strip_tags( $product->get_title() ),
					        'url'   => $product->get_permalink()
				        ), $product );
			        }
		        } else {
			        $suggestions[] = array(
				        'id'    => - 1,
				        'value' => __( 'No results', 'yith-woocommerce-ajax-search' ),
				        'url'   => '',
			        );
		        }
		        wp_reset_postdata();

		        if ( $transient_enabled == 'yes' ) {
			        set_transient( $transient_name, $suggestions, $transient_duration * HOUR_IN_SECONDS );
		        }
	        }

		    $time_end = getmicrotime();
		    $time = $time_end - $time_start;
            $suggestions = array(
                'suggestions' => $suggestions,
                'time'        => $time
            );
            echo json_encode( $suggestions );
            die();

        }


    }
}