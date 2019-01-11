<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

/** License management
*/
class maxLicense
{
	protected static $instance;

	protected $product_id = "maxbuttons-pro";
	protected $license_key = null;
	protected $license_activated = null;
	protected $license_lastcheck = null;
	protected $license_expires = null;

	protected $api_url = 'https://maxbuttons.com';
	protected $update_url = 'https://maxbuttons.com/maxupdate';

	protected $is_valid = false;
	protected $is_expired = true;


	public function __construct()
	{
		$this->load_license();
		add_action('maxbuttons/ajax/activate_license', array($this, 'ajax_action'));
		add_action('maxbuttons/ajax/deactivate_license', array($this, 'ajax_action'));
	}

	public static function getInstance()
	{
		if (is_null(self::$instance))
			self::$instance = new maxLicense();

		return self::$instance;
	}

	protected function load_license()
	{
		$this->license_key = get_option('maxbuttons_pro_license_key');
		$this->license_lastcheck = get_transient('maxbuttons_pro_license_lastcheck');
		$this->license_activated = (bool) get_option('maxbuttons_pro_license_activated');
		$this->license_expires = get_option('maxbuttons_pro_license_expires', -1);

		/** This is to remote check the license if the plugin didn't set the license expires yet, this is quite new. It checks on license actived to prevent
		*   license check floods if the license is not valid, expired or otherwise. The remote license function sets license to not actived in those cases
		*/
		if ( ($this->license_expires <= 0 || $this->license_expires == '') && $this->license_activated == true )
		{
			$this->get_remote_license();
		}

		if ($this->license_expires > time() )
		{

				$this->is_expired = false;
		}
		else {
				if (intval($this->license_expires) > 0) // don't expire on empty, or not existing timestamps.
					$this->is_expired = true;
				elseif ($this->license_expires == -1)
				{
					$this->is_expired = false; // no existing expiry date doesn't mean it's expired, just not active.
				}
		}

		if ($this->is_activated() && ! $this->is_expired() )
		{
			$this->is_valid = true;
		}
		else {
			$this->is_valid = false;
		}

	}

	/** Function called at plugin notice time. Will check the license, will check the validity and trigger periodical checks for expiration. */
	public function display_license()
	{
		if (! $this->is_valid()) // is valid - working and current.
		{

			if (! $this->is_activated() )
			{
				include_once( MB()->get_plugin_path(true) . 'includes/no-license-warning.php');
			}
			elseif ($this->is_expired() )
			{
				$expiration = $this->get_expiration();
				include_once( MB()->get_plugin_path(true) . 'includes/expired-license-warning.php');
			}
		}
	}

	public function get_key()
	{
		return $this->license_key;
	}

	public function get_renewal_url()
	{
		$url = $this->api_url . '/checkout/';
		$license_key = $this->get_key();
		$url = add_query_arg('edd_license_key', $license_key, $url);
		return $url;
	}

	public function get_expiration($format = 'date')
	{
		 $expiration = $this->license_expires;

		 if (is_null($expiration) || $expiration === false)
		 	return 0;

		 switch($format)
		 {
			 case 'date':
			 		return date_i18n( get_option( 'date_format' ), $expiration );
			 break;
		 }

	}

	public function is_activated()
	{
		return $this->license_activated;
	}

	public function is_valid()
	{
		return $this->is_valid;
	}

	public function is_expired()
	{
		return $this->is_expired;
	}

	/** Check for plugin updates via the WP update system */
	public function update_check() {

			new maxUpdater100($this->update_url,
							  plugin_basename(MAXBUTTONS_PRO_ROOT_FILE)  // check this
							 );

	}

	public function ajax_action($post)
	{
		if (isset($post['form']))
		{
			$form = array();
			parse_str($post['form'], $form);
		}

		$action = sanitize_text_field($post['plugin_action']);

		if (isset($form['license_key']) && $form['license_key'] !== '')
		{
			$license_key = (isset($form["license_key"])) ? trim(sanitize_text_field($form["license_key"])) : '';

			if ($action == 'activate_license')
				$this->activate_license($license_key);
			if ($action == 'deactivate_license')
				$this->deactivate_license($license_key);
		}
		else {
			$result = array('status' => 'error', 'error' => 'no_key', 'additional_info' => __('No License Key provided', 'maxbuttons-pro') );
			echo json_encode($result);
			exit();
		}

		echo json_encode(array('status' => 'error', 'error' => 'License check fell through' ));

		exit();
	}

	protected function get_api_args()
	{
		$product_id = $this->product_id;
		$api_url = $this->api_url;

		$args = array(
				//"edd_action" => "activate_license",
				//"request" => "activation",
				"item_name" => $this->product_id,
				"url" => home_url(),

			);
			return $args;
	}

	public function deactivate_license($license_key)
	{
		 $args = $this->get_api_args();
		 $args['edd_action'] = 'deactivate_license';
		 $args['license'] = $license_key;

		 $data = $this->do_api_post($args);

		 if (isset($data->success) && $data->success == true)
		 {

			// delete_option('maxbuttons_pro_license_key');
	 		 delete_option('maxbuttons_pro_license_expires');
 			 delete_option('maxbuttons_pro_license_activated');
			 echo json_encode(array('status' => 'success'));
			 exit();

		 }
		 else {
			 	$this->handle_error($data);
		 }

	}

	/** Check the given license against the system */
	public function activate_license($license_key)
	{
		$error = false; // error handling

		$args = $this->get_api_args();
		$args["license"] = $license_key;
		$args["edd_action"] = 'activate_license';

		$free_created = get_option("MBFREE_CREATED");
		$free_url = get_option("MBFREE_HOMEURL");

		if ($free_created != '')
			$args["free_created"] = $free_created;
		if ($free_url != '')
			$args["free_url"] = $free_url;

		//$api_url = add_query_arg($args, $this->api_url);

		header('Content-Type: application/json');

		if ($error) // errors before the request
		{
			echo json_encode($error_body);
			exit();
		}

		$data = $this->do_api_post($args);

		if (isset($data->license) && $data->license == 'valid')
		{
			$expires = strtotime($data->expires);
			$result  = array("status" => 'success'); // clean output
			update_option('maxbuttons_pro_license_key', $license_key, true);
			update_option('maxbuttons_pro_license_expires', $expires );
			update_option('maxbuttons_pro_license_activated', true, true);

			echo json_encode($result);
			exit();
		}
		else
		{
			$this->handle_error($data);
		}

	}


	protected function do_api_post($args)
	{
			$result = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $args ) );

			if ( is_wp_error( $result ) || 200 !== wp_remote_retrieve_response_code( $result ) )
			{
				$error = $result->get_error_message();

				$message =  ( is_wp_error( $result ) && ! empty( $error ) ) ? $error : __( 'An connection error occurred, please try again.', 'maxbuttons-pro');
				$result = array('status' => 'error',
								'error' => $message,
								"additional_info" => $message,
								);
				echo json_encode($result);

				exit();
			}

			$data = json_decode( wp_remote_retrieve_body( $result ) );

			return $data;
	}


	protected function handle_error($data)
	{
			$new_result = array(); // clean output;

			$new_result["status"] = "error";
			$new_result["error"] = (isset($data->error)) ? $data->error : '';

			switch( $data->error ) {
					case 'expired' :
						$message = sprintf(
							__( 'Your license key expired on %s.', 'maxbuttons-pro' ),
							date_i18n( get_option( 'date_format' ), strtotime( $data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
					case 'disabled' :
						$message = __( 'Your license key has been disabled.','maxbuttons-pro');
						break;
					case 'missing' :
						$message = __( 'Invalid license.', 'maxbuttons-pro');
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.', 'maxbuttons-pro' );
						break;
					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'maxbuttons-pro' ), 'MaxButtons PRO' );
						break;
					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.','maxbuttons-pro' );
						break;
					default :
						$message = __( 'An error occurred, please try again.', 'maxbuttons-pro' );
						break;
			}
			$new_result["additional_info"] = $message ;
			$result = $new_result;

		echo json_encode($result);
		exit();
	}

	/** Check if the license is valid. Don't check more than given amount of days. */
	public function check_license()
	{
		if (! $this->license_activated)
			return false; // not activated,  no further checks needed

		if ($this->license_lastcheck === false)
			$remote_result = $this->get_remote_license();
		else
			return $this->license_activated; // if transient exists, return status quo

		$this->load_license(); // reinit;

		$reason = (isset($remote_result)) ? $remote_result : '';

		if($this->license_activated)
		{
			return true;
		}
		else
			return false;

	}

	public function update_license_checked_time($seconds = false)
	{
		 if (! $seconds)
		 {
		 	$seconds = DAY_IN_SECONDS * 4;
		 }
		 set_transient('maxbuttons_pro_license_lastcheck',true, $seconds );
	}

	public function get_remote_license()
	{
		$args = array(
				"edd_action" => "check_license",
				"license" => $this->license_key,
				"item_name" => $this->product_id,
				"url" => home_url(),
		);

		if ($this->license_key == '')
			return false; // no license no checks

		$request = wp_remote_post($this->api_url,  array( 'body' => $args, 'timeout' => 15, 'sslverify' => false ) );

		if(is_wp_error($request))
		{
			// failed - defer check three hours - prevent check license flood
			$this->update_license_checked_time( (3*HOUR_IN_SECONDS) );
			error_log("MBPRO - License server failed to respond");
			return "Request failed";
		}

		$data = json_decode( wp_remote_retrieve_body( $request ) );

		if (isset($data->expires))
		{
			$expires = strtotime($data->expires);
			update_option('maxbuttons_pro_license_expires', $expires );
		}

		// this is probably not correct! valid || expired?
		$active_statuses = array('valid', 'expired', 'inactive');
		if (isset($data->license) && in_array($data->license, $active_statuses)  )
		{
			$this->update_license_checked_time();
			return true;
		}
		else
		{
			update_option('maxbuttons_pro_license_activated', false, true);
			$this->update_license_checked_time();
			return false;
		}
	}

	public static function license_locker()
	{
			$license = static::getInstance();
		if (! $license->is_valid() )
		{
			echo "<div id='license_not_active' data-lock=true> <div class='license_warning'><p>" . __("No valid license. Changes can't be saved", 'maxbuttons-pro') . "</p></div> </div>";
		}
	}

	/** This function is to get remote license and reload. This should *ONLY* be run on the license interface where last-minute info is crucial */
	public function checkRemoteandReload()
	{
			$this->get_remote_license();
			$this->load_license();
	}

} // Class
