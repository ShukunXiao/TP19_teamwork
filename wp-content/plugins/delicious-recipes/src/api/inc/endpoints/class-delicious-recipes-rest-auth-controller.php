<?php
/**
 * Delicious Recipes REST Auhtentication Controller.
 *
 * @since 1.0.0
 */
class Delicious_Recipes_REST_API_Auth_Controller {
	/**
	 * Authentication Route Register.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'deliciousrecipe/v1',
			'/auth',
			array(
				// Here we register the readable endpoint for collections.
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'authorize' ),
					'permission_callback' => array( $this, 'is_valid_user' ),
				),
				// Register our schema callback.
				'schema' => null,
			)
		);
	}

	/**
	 * API Callback.
	 *
	 * @param WP_REST_REQUEST $request Current request.
	 * @return $response
	 */
	public function authorize( $request ) {
		$response = array(
			'code'    => 'valid_user',
			'message' => __( 'User Authentication Successful.', 'delicious-recipes' ),
			'data'    => array(
				'status' => 200,
			),
		);
		return $response;
	}

	/**
	 * Check User Exist.
	 *
	 * @param WP_REST_Request $request Current request.
	 * @return mixed
	 */
	public function is_valid_user( $request ) {

		if ( ! wp_get_current_user()->ID ) {
			return new WP_Error( 'invalid_user', esc_html__( 'User Not Found.', 'delicious-recipes' ), array( 'status' => $this->authorization_status_code() ) );
		}
		return true;
	}

	/**
	 * Sets Authorization Status Code.
	 *
	 * @return $status
	 */
	public function authorization_status_code() {
		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}

	/**
	 * Check for authentication error.
	 *
	 * @param WP_Error|null|bool $error Error data.
	 * @return WP_Error|null|bool
	 */
	public function check_authentication_error( $error ) {
		// Pass through other errors.
		if ( ! empty( $error ) ) {
			return $error;
		}

	}

}

/**
 * Initialize Auth Controller.
 *
 * @return void
 */
function delicious_recipes_register_auth_rest_routes() {
	$controller = new Delicious_Recipes_REST_API_Auth_Controller();
	$controller->register_routes();
}

add_action( 'rest_api_init', 'delicious_recipes_register_auth_rest_routes' );
