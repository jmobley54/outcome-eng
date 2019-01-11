<?php

	use Carbon\Carbon;


	/**
	 * Class Woocommerce_Pay_Per_Post_General
	 */
class Woocommerce_Pay_Per_Post_Helper extends Woocommerce_Pay_Per_Post {


	/**
	 * @var array
	 */
	public static $protection_types = array( 'standard', 'delay', 'page-view', 'expire' );


	/**
	 * @param $post_id
	 *
	 * @return bool|string
	 */
	public static function is_protected( $post_id = null ) {

		if ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$parent = new Woocommerce_Pay_Per_Post();

		$selected                     = get_post_meta( $post_id, $parent->plugin_name . '_product_ids', true );
		$delay_restriction_enable     = get_post_meta( $post_id, $parent->plugin_name . '_delay_restriction_enable', true );
		$page_view_restriction_enable = get_post_meta( $post_id, $parent->plugin_name . '_page_view_restriction_enable', true );
		$expire_restriction_enable    = get_post_meta( $post_id, $parent->plugin_name . '_expire_restriction_enable', true );

		if ( ! empty( $selected ) || '' !== $selected ) {
			$protection = 'standard';

			if ( (bool) $delay_restriction_enable ) {
				$protection = 'delay';
			}

			if ( (bool) $page_view_restriction_enable ) {
				$protection = 'page-view';
			}

			if ( (bool) $expire_restriction_enable ) {
				$protection = 'expire';
			}

			return $protection;

		} else {
			return false;
		}

	}

	public static function has_access() {
		$parent = new Woocommerce_Pay_Per_Post();
		$public = new Woocommerce_Pay_Per_Post_Public( $parent->get_plugin_name(), $parent->get_version(), $parent->get_template_path() );
		return $public->restrict_content();
	}

	public static function get_no_access_content() {
		$parent = new Woocommerce_Pay_Per_Post();
		$public = new Woocommerce_Pay_Per_Post_Public( $parent->get_plugin_name(), $parent->get_version(), $parent->get_template_path() );
		return $public->show_paywall();
	}

	/**
	 * @param $type
	 *
	 * @return bool|string
	 */
	public static function protection_display_icon( $type ) {

		if ( in_array( $type, self::$protection_types, true ) ) {

			switch ( $type ) {
				case 'standard':
					return '<span class="dashicons dashicons-post-status" title="Standard Purchase Protection" style="color:green"></span>';
					break;
				case 'delay':
					return '<span class="dashicons dashicons-clock" title="Delay Protection" style="color:green"></span>';
					break;
				case 'page-view':
					return '<span class="dashicons dashicons-visibility" title="Page View Protection" style="color:green"></span>';
					break;
				case 'expire':
					return '<span class="dashicons dashicons-backup" title="Expiry Protection" style="color:green"></span>';
					break;
			}
		}

		return false;

	}


	/**
	 * @return Carbon
	 */
	public static function current_time() {
		return Carbon::createFromTimestamp( current_time( 'timestamp' ) );
	}


	public static function logger( $message ) {
		$logger = new Woocommerce_Pay_Per_Post_Logger();
		$logger->log( $message );
	}

	public static function logger_uri() {
		$logger = new Woocommerce_Pay_Per_Post_Logger();

		return $logger->get_log_uri();
	}

	public static function logger_url() {
		$logger = new Woocommerce_Pay_Per_Post_Logger();

		return $logger->get_log_url();
	}

	public static function plugin_name() {
		$parent = new Woocommerce_Pay_Per_Post();

		return $parent->plugin_name;
	}

}
