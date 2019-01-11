<?php
/**
 * WordPress OAuth Server Error Filter
 * @deprecated Schedule for removal. The PHP server handles all these now.
 */
function wo_api_error_setup( $errors ) {
	$errors["invalid_access_token"]  = "The access token is invalid or has expired";
	$errors["invalid_refresh_token"] = "The refresh token is invalid or has expired";
	$errors["invalid_credentials"]   = "Invalid user credentials";

	return $errors;
}

add_filter( "WO_API_Errors", "wo_api_error_setup", 1 );

/**
 * Default Method Filter for the resource server API calls
 *
 * @since  3.1.8 Endpoints now can accept public methods that bypass the token authorization
 */
function wo_default_endpoints() {
	$endpoints = array(
		'me'            => array(
			'func'   => '_wo_method_me',
			'public' => false
		),
		'destroy'       => array(
			'func'   => '_wo_method_destroy',
			'public' => true
		),
		'introspection' => array(
			'func'   => '_wo_method_introspection',
			'public' => false
		)
	);

	return $endpoints;
}

add_filter( 'wo_endpoints', 'wo_default_endpoints', 1 );

/**
 * Token Introspection
 * Since spec call for the response to return even with an invalid token, this method
 * will be set to public.
 * @since 4.0
 *
 * @param null $token
 */
function _wo_method_introspection( $token = null ) {
	$access_token = &$token['access_token'];

	$request = OAuth2\Request::createFromGlobals();

	if ( strtolower( @$request->server['REQUEST_METHOD'] ) != 'post' ) {
		$response = new OAuth2\Response();
		$response->setError(
			405,
			'invalid_request',
			'The request method must be POST when calling the introspection endpoint.',
			'https://tools.ietf.org/html/rfc7662#section-2.1'
		);
		$response->addHttpHeaders( array( 'Allow' => 'POST' ) );
		$response->send();
	}

	// Check if the token is valid
	$valid = wo_public_get_access_token( $access_token );
	if ( false == $valid ) {
		$response = new OAuth2\Response( array(
			'active' => false
		) );
		$response->send();
	}

	if ( $valid['user_id'] != 0 || ! is_null( $valid['user_id'] ) ) {
		$user     = get_userdata( $valid['user_id'] );
		$username = $user->user_login;
	}
	$introspection = apply_filters( 'wo_introspection_response', array(
		'active'    => true,
		'scope'     => $valid['scope'],
		'client_id' => $valid['client_id']
	) );
	$response      = new OAuth2\Response( $introspection );
	$response->send();

	exit;
}

/**
 * DEFAULT DESTROY METHOD
 * This method has been added to help secure installs that want to manually destroy sessions (valid access tokens).
 * @since  3.1.5
 *
 * @param null $token
 */
function _wo_method_destroy() {
	$request = $_REQUEST;
	if ( isset( $request['access_token'] ) ) {
		$access_token = $request['access_token'];
	}

	// 10.30.17 Added basic session support for OpenID Connect
	// If there is not access token provided, lets destroy the cookie session.
	// http://openid.net/specs/openid-connect-session-1_0.html#toc

	// OpenID Connect uses ID Tokens. This token will contain the user information for the given session. It is just to that
	// the session matches the user that was sent from the OP. It is recommended but there really is only 1 cookie session
	// with WP. Sooooo... I do not think we will need this in the context it is suggested.
	// @todo Check for id token and ensure the user matches the session being terminated.
	//
	// if ( ! empty( $_REQUEST['id_token_hint'] ) ) {}

	if ( empty( $access_token ) ) {
		$redirect_allowed = false;

		// To prevent abuse of the redirect, we will only redirect if the user was logged in but until we can check for
		// the redirect, we set a reference for later use after as clear the session.
		if ( is_user_logged_in() ) {
			$redirect_allowed = true;
		}

		// Clear the WP session for the given user.
		// @todo Look into clearing all the access tokens for the user. This may be an overlap in WP and OpenID that may be a pain point.
		wp_clear_auth_cookie();

		if ( ! empty( $_REQUEST['post_logout_redirect_uri'] ) && $redirect_allowed ) {

			// @todo Clean this mess up and build a proper query with 1 redirect statement
			// @todo Strip the redirect URL down with no parameters to ensure even better safety of user.
			if ( ! empty( $_REQUEST['state'] ) ) {
				wp_redirect( $_REQUEST['post_logout_redirect_uri'] . '?state=' . $_REQUEST['state'] );
			} else {
				wp_redirect( $_REQUEST['post_logout_redirect_uri'] );
			}
			exit;
		}
	}

	// If there is an access token, remove it from the DB
	if ( ! empty( $access_token ) ) {

		global $wpdb;
		$stmt = $wpdb->delete( "{$wpdb->prefix}oauth_access_tokens", array( 'access_token' => $access_token ) );

		// Remove the refresh token as well
		if ( ! empty( $_REQUEST['refresh_token'] ) ) {
			$stmt = $wpdb->delete( "{$wpdb->prefix}oauth_refresh_tokens", array( 'refresh_token' => $_REQUEST['refresh_token'] ) );
		}
	}

	/**
	 * Clear any session that might be
	 *
	 * @since 3.4.4
	 */
	wp_clear_auth_cookie();

	// prepare the return
	$response = new OAuth2\Response( array(
		'status'      => true,
		'description' => 'Session destroyed successfully'
	) );
	$response->send();
	exit;
}

/**
 * DEFAULT ME METHOD - DO NOT REMOVE DIRECTLY
 * This is the default resource call "/oauth/me". Do not edit or remove.
 *
 * @param null $token
 *
 * @todo Add return response if the user is logged in but did not provide a token. Currently returns "[]"
 */
function _wo_method_me( $token = null ) {

	if ( ! isset( $token['user_id'] ) || $token['user_id'] == 0 ) {
		$response = new OAuth2\Response();
		$response->setError(
			400,
			'invalid_request',
			'Invalid token',
			'https://tools.ietf.org/html/draft-ietf-oauth-v2-31#section-7.2'
		);
		$response->send();
		exit;
	}

	$user = get_user_by( 'id', $token['user_id'] );

	// Typically, if a blank user returns, the token belongs to a client that used client credentials. Inform the user
	// of this.
	if ( ! $user ) {
		$response = new OAuth2\Response();
		$response->setError(
			400,
			'invalid_request',
			'No user found for this token. Ensure it is not a client token.'
		);
		$response->send();
		exit;
	}
	$me_data = (array) $user->data;

	unset( $me_data['user_pass'] );
	unset( $me_data['user_activation_key'] );
	unset( $me_data['user_url'] );

	/**
	 * 10.30.17 by Justin Greer <justin@dash10.digital>
	 *
	 * If openid is presented in the scope, we need to provide some more redundant information in a different variable.
	 */
	$scopes = explode( ' ', $token['scope'] );
	if ( in_array( 'openid', $scopes ) ) {
		$me_data['email'] = $me_data['user_email'];
		$me_data['sub']   = $me_data['ID'];
	}

	/**
	 * user information returned by the default me method is filtered
	 * @since 3.3.7
	 * @filter wo_me_resource_return
	 */
	$me_data = apply_filters( 'wo_me_resource_return', $me_data );

	$response = new OAuth2\Response( $me_data );
	$response->send();
	exit;
}

/**
 * Adds OAuth2 to the WP-JSON index
 *
 * @param $response_object
 *
 * @return mixed
 */
function wo_server_register_routes( $response_object ) {

	if ( empty( $response_object->data['authentication'] ) ) {
		$response_object->data['authentication'] = array();
	}
	$response_object->data['authentication']['oauth2'] = array(
		'authorize' => site_url( 'oauth/authorize' ),
		'token'     => site_url( 'oauth/token' ),
		'me'        => site_url( 'oauth/me' ),
		'version'   => '2.0',
		'software'  => 'WP OAuth Server'
	);

	return $response_object;
}

add_filter( 'rest_index', 'wo_server_register_routes' );

