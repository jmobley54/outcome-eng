<?php

class Woocommerce_Pay_Per_Post_Logger extends Woocommerce_Pay_Per_Post {


	protected $log_file_name = 'debug.log';
	protected $log_file_uri;
	protected $log_file_url;
	protected $log_directory;
	protected $debug = false;


	public function __construct() {
		$parent = new Woocommerce_Pay_Per_Post();

		$this->debug = get_option( $parent->plugin_name . '_enable_debugging', true );

		if ( $this->debug ) {
			$uploads_dir         = wp_upload_dir();
			$log_dir             = $uploads_dir['basedir'] . '/woocommerce-pay-per-post-logs';
			$log_dir_url         = $uploads_dir['baseurl'] . '/woocommerce-pay-per-post-logs';
			$log_file            = $log_dir . '/' . $this->log_file_name;
			$log_file_url        = $log_dir_url . '/' . $this->log_file_name;
			$this->log_directory = $log_dir;
			$this->log_file_uri  = $log_file;
			$this->log_file_url  = $log_file_url;

			$this->create_log_file();
		}


	}


	private function create_log_file() {

		if ( ! file_exists( $this->log_directory ) ) {
			wp_mkdir_p( $this->log_directory );
		}

		if ( ! file_exists( $this->log_file_uri ) ) {
			$log = fopen( $this->log_file_uri, "w" ) or die( "Unable to open file!" );
			fwrite( $log, $this->log_format( 'Initial Log File Created' ) );
			fclose( $log );
		}

	}


	private function log_format( $message ) {
		return '[' . current_time( 'mysql' ) . ']   -----[DEBUG]-----  ' . print_r( $message, true ) . "\n";
	}


	public function log( $message ) {
		if ( $this->debug ) {
			$log_contents = file_get_contents( $this->log_file_uri );
			file_put_contents( $this->log_file_uri, $this->log_format( $message ) . $log_contents );
		}
	}


	public function get_log_uri() {
		return $this->log_file_uri;
	}

	public function get_log_url() {
		return $this->log_file_url;
	}

	public function delete_log_file() {
		unlink( $this->log_file_uri );
	}

}
