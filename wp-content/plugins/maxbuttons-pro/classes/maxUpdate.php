<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

if (! class_exists('maxUpdater100')):
class maxUpdater100
{
	private $api_key = '';
	private $api_url = '';
	private $plugin_slug = '';
	private $plugin_path = '';

	private $version;

	private static $checkInProgress = false;
	private static $response = null;

	public function __construct($api_url, $plugin_path )
	{

		$this->api_url = $api_url;
		$this->plugin_path = $plugin_path;   // relative from plugins dir - maxbuttons-pro/maxbuttons-pro.php
		$this->plugin_slug = basename($plugin_path,'.php');

		$this->version = MAXBUTTONS_VERSION_NUM;

		// Take over the Plugin info screen
		add_filter('plugins_api', array($this, 'plugin_info_call'), 10, 3);

		// The update check
		add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));

		add_action('load-plugins.php', array($this, 'add_update_row'), 30 ); // function that loads the wp_plugin_update_row hooks
		// TESTING
		//get_site_transient( 'update_plugins', null );
	}

	public function add_update_row()
	{
		 $license = MB()->getClass('license');
		 if (! $license->is_valid())
		 {
			remove_action( 'after_plugin_row_' . $this->plugin_path, 'wp_plugin_update_row', 10, 2 );
			add_action( 'after_plugin_row_' . $this->plugin_path, array( $this, 'show_update_notification' ), 10, 3 );
		 }
	}

	public function show_update_notification($file, $data, $status)
	{
			if (count($data) == 0)
				return;

			if( ! current_user_can( 'update_plugins' ) ) {
					return;
			}

		  if (! isset($data['new_version']))
			{
				return; // no new version, no need to show block
			}

			if ( version_compare( $this->version, $data['new_version'],  '<' ) ) {
					 $license = MB()->getClass('license');
			?>
<tr class="plugin-update-tr active" id="maxbuttons-pro-update" data-slug="maxbuttons-pro" data-plugin="maxbuttons-pro/maxbuttons-pro.php"><td colspan="3" class="plugin-update colspanchange"><div class="update-message notice inline notice-error notice-alt"><p><?php _e('There is a new version of MaxButtons Pro available.', 'maxbuttons-pro'); ?> <span><strong><?php _e('Your license is not valid (or expired). Activate your license to update', 'maxbuttons-pro'); ?></strong> </span>
</p></div></td></tr>
			<?php

			}
	}

	public function check_for_update($checked_data) {
		 $api_url = $this->api_url;
		 $license = MB()->getClass('license');
		 $license_key = $license->get_key();
		 $license_is_valid = $license->is_valid();

		if (empty($checked_data))
			return $checked_data;

		// to prevent current double checking, not clue the cause
		if ( static::$checkInProgress === true)
		{
			if ( ! is_null(static::$response))
			{
				$checked_data->response[$this->plugin_path]  = static::$response;
			}
			return $checked_data;
		}
		static::$checkInProgress  = true;

		$current_version = $this->version;

		$request_args = array(
			'version' => $current_version,
			'license' => $license_key,
			'updater_version' => '7.0',
		);

	 	$request_string =  $this->prepare_request($request_args);
		$api_url = $api_url . '/' . $this->plugin_slug . '/check/';

		// Start checking for an update
		$raw_response = wp_remote_post($api_url, $request_string);

		$response = '';
		$wp_error = false;

		if (is_wp_error($raw_response))
			$wp_error = true;

		if (!$wp_error && ($raw_response['response']['code'] == 200))
		{
			$response = maybe_unserialize($raw_response['body']);
			if (! $license_is_valid)
			{
				if (isset($response->package))
				{
					$response->package = null;
				}
			}
		}
		elseif(!$wp_error && $raw_response["response"]["code"] == 404)
		{
			$raw_response = new \WP_Error('404', __("The remote site said 404 not found","maxupdate"));
		}

		if (is_object($response) && !empty($response) && ! $wp_error ) // Feed the update data into WP updater
		{
			set_transient('mbpro_update_cache', $response, 3*HOUR_IN_SECONDS);
			update_option('maxbuttons_pro_update_checks', 0); // no errors,
			static::$response = $response;
			$response->icons = array('default' => MB()->get_plugin_url(true) . 'images/mb-peach-64.png' );

			$checked_data->response[$this->plugin_path] = $response;
		}
		elseif (empty($response) && ! $wp_error )
		{
			set_transient('mbpro_update_cache', array(), 3*HOUR_IN_SECONDS);
			update_option('maxbuttons_pro_update_checks', 0);
			return $checked_data;
		} // no updates
		elseif (! $wp_error && !empty($response) )
		{
			$raw_response = new \WP_Error('Unknown',
							__('No update information was returned for 5 attempts. This could be caused by slow connection speeds.','maxupdate') . ' (' . print_r($response,true) . ')' );
		}

		if ( is_wp_error($raw_response))
		{
			$update_checks = get_option('maxbuttons_pro_update_checks',0);
			$update_checks++;
			update_option('maxbuttons_pro_update_checks', $update_checks);

			$update_failhide = get_option('maxbuttons_updatefailhide');

			if ($update_checks >= 5 && $update_failhide !== 1) // limit amount of error displays in case connection is slow
			{
				add_action( 'admin_notices', function () use ($raw_response)
				{
					echo '<div class="error"><p><strong>' . __("Something went wrong with checking for MaxButtons PRO updates:","maxupdate");
					echo '</strong></p> <p>';
					echo $raw_response->get_error_message();
					echo '</p> <p>';
					echo __("Please contact MaxButtons PRO support if you do not receive automatic updates","maxupdate") . '</p></div>';

				});
				update_option('maxbuttons_pro_update_checks', 0);
			}
		}

		return $checked_data;
	}

	public function plugin_info_call($def, $action, $args) {
		if (! isset($args->slug) || $args->slug != $this->plugin_slug)
			return false;

		$api_url = $this->api_url;

		// Get the current version
		$plugin_info = get_site_transient('update_plugins');
		$current_version = isset($plugin_info->checked) && isset($plugin_info->checked[$this->plugin_path]) ? $plugin_info->checked[$this->plugin_path] : '';
		$args->version = $current_version;

		$request_args = array(
			'version' => $current_version,
			'updater_version' => '7.0',
		);

		$api_url = $api_url . '/' . $this->plugin_slug . '/information/';

	 	$request_string = $this->prepare_request($request_args);

		$request = wp_remote_post($api_url, $request_string);

		if (is_wp_error($request)) {
			$res = new \WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
		} else {
			$res = maybe_unserialize($request['body']);

			if ($res === false)
				$res = new \WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
		}

		return $res;
	}

  function prepare_request(  $args ) {
    		global $wp_version;

    		return array(
    			'body' => array(
    				'request' => $args,
    			),

    			'user-agent' => 'WordPress/'. $wp_version .'; '. home_url()
    		);
    	}

} // class
endif; // class exists
