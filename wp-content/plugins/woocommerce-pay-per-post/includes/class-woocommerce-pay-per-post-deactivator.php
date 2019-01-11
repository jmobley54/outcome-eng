<?php

class Woocommerce_Pay_Per_Post_Deactivator {



	public static function deactivate() {
		// Check to see if option is set to remove all settings.
		$delete_settings = get_option( Woocommerce_Pay_Per_Post_Helper::plugin_name() . '_delete_settings', false );

		if ( $delete_settings ) {
			Woocommerce_Pay_Per_Post_Helper::logger( 'Delete all settings' );
			self::delete_all_settings();
		}

	}

	private static function delete_all_settings() {
		global $wpdb;
		// Delete.
		$sql = "DELETE TABLE `{$wpdb->prefix}woocommerce_pay_per_post_pageviews`";
		$wpdb->query( $sql );
		Woocommerce_Pay_Per_Post_Helper::logger( 'woocommerce_pay_per_post_pageviews DELETED' );

		$sql = "DELETE FROM `{$wpdb->options}` WHERE `option_name` RLIKE '" . Woocommerce_Pay_Per_Post_Helper::plugin_name() . "_'";
		 $wpdb->query( $sql );
		Woocommerce_Pay_Per_Post_Helper::logger( 'OPTIONS DELETED' );

		$sql = "DELETE FROM {$wpdb->postmeta} `meta_key` RLIKE '" . Woocommerce_Pay_Per_Post_Helper::plugin_name() . "_'";
		 $wpdb->query( $sql );
		Woocommerce_Pay_Per_Post_Helper::logger( 'POST META DELETED' );

		// Delete Log File.
		$log = new Woocommerce_Pay_Per_Post_Logger();
		$log->delete_log_file();
	}

}
