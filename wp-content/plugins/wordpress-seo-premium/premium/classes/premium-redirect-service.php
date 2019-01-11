<?php
/**
 * WPSEO Premium plugin file.
 *
 * @package WPSEO\Premium
 */

/**
 * The service for the redirects to WordPress.
 */
class WPSEO_Premium_Redirect_Service {

	/**
	 * Saves the redirect to the redirects.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response to send back.
	 */
	public function save( WP_REST_Request $request ) {
		$redirect = $this->map_request_to_redirect( $request );

		if ( $this->get_redirect_manager()->create_redirect( $redirect ) ) {
			return new WP_REST_Response( 'true' );
		}

		return new WP_REST_Response( 'false' );
	}

	/**
	 * Deletes the redirect from the redirects.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response to send back.
	 */
	public function delete( WP_REST_Request $request ) {
		$redirect = $this->map_request_to_redirect( $request );
		$redirects = array( $redirect );

		$redirect_format = $request->get_param( 'format' );
		if ( ! $redirect_format ) {
			$redirect_format = WPSEO_Redirect::FORMAT_PLAIN;
		}

		if ( $this->get_redirect_manager( $redirect_format )->delete_redirects( $redirects ) ) {
			return new WP_REST_Response( 'true' );
		}

		return new WP_REST_Response( 'false' );
	}

	/**
	 * Creates and returns an instance of the redirect manager.
	 *
	 * @param string $format The redirect format.
	 *
	 * @return WPSEO_Redirect_Manager The redirect maanger.
	 */
	protected function get_redirect_manager( $format = WPSEO_Redirect::FORMAT_PLAIN ) {
		return new WPSEO_Redirect_Manager( $format );
	}

	/**
	 * Maps the given request to an instance of the WPSEO_Redirect.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WPSEO_Redirect Redirect instance.
	 */
	protected function map_request_to_redirect( WP_REST_Request $request ) {
		$origin = $request->get_param( 'origin' );
		$target = $request->get_param( 'target' );
		$type   = $request->get_param( 'type' );

		return new WPSEO_Redirect( $origin, $target, $type );
	}
}
