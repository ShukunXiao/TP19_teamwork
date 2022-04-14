<?php
/**
 * User Dashboard Functions.
 *
 * @package Delicious_Recipes
 * @since  1.2.0
 */
/**
 * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from seeing the admin bar.
 *
 * @access public
 * @param bool $show_admin_bar
 * @return bool
 */
function delicious_recipes_disable_admin_bar( $show_admin_bar ) {
	if ( apply_filters( 'delicious_recipes_disable_admin_bar', ! current_user_can( 'edit_posts' ) ) ) {
		$show_admin_bar = false;
	}

	return $show_admin_bar;
}
add_filter( 'show_admin_bar', 'delicious_recipes_disable_admin_bar', 10, 1 );

if ( ! function_exists( 'delicious_recipes_create_new_recipe_user' ) ) {

	/**
	 * Create a new customer.
	 *
	 * @param  string $email Customer email.
	 * @param  string $username Customer username.
	 * @param  string $password Customer password.
	 * @return int|WP_Error Returns WP_Error on failure, Int (user ID) on success.
	 */
	function delicious_recipes_create_new_recipe_user( $email, $username = '', $password = '' ) {

		$global_toggles  = delicious_recipes_get_global_toggles_and_labels();

		// Check the email address.
		if ( empty( $email ) || ! is_email( $email ) ) {
			return new WP_Error( 'registration-error-invalid-email', __( 'Please provide a valid email address.', 'delicious-recipes' ) );
		}

		if ( email_exists( $email ) ) {
			return new WP_Error( 'registration-error-email-exists', apply_filters( 'delicious_recipes_registration_error_email_exists', __( 'An account is already registered with your email address. Please log in.', 'delicious-recipes' ), $email ) );
		}

		// Handle username creation.
		if ( ! $global_toggles['generate_username'] || ! empty( $username ) ) {
			$username = sanitize_user( $username );

			if ( empty( $username ) || ! validate_username( $username ) ) {
				return new WP_Error( 'registration-error-invalid-username', __( 'Please enter a valid account username.', 'delicious-recipes' ) );
			}

			if ( username_exists( $username ) ) {
				return new WP_Error( 'registration-error-username-exists', __( 'An account is already registered with that username. Please choose another.', 'delicious-recipes' ) );
			}
		} else {
			$username = sanitize_user( current( explode( '@', $email ) ), true );

			// Ensure username is unique.
			$append     = 1;
			$o_username = $username;

			while ( username_exists( $username ) ) {
				$username = $o_username . $append;
				$append++;
			}
		}

		// Handle password creation.
		if ( $global_toggles['generate_password'] || empty( $password ) ) {
			$password           = wp_generate_password();
			$password_generated = true;
		} elseif ( empty( $password ) ) {
			return new WP_Error( 'registration-error-missing-password', __( 'Please enter an account password.', 'delicious-recipes' ) );
		} else {
			$password_generated = false;
		}

		// Use WP_Error to handle registration errors.
		$errors = new WP_Error();

		$errors = apply_filters( 'delicious_recipes_registration_errors', $errors, $username, $email );

		if ( $errors->get_error_code() ) {
			return $errors;
		}

		$new_customer_data = apply_filters( 'delicious_recipes_new_customer_data', array(
			'user_login' => $username,
			'user_pass'  => $password,
			'user_email' => $email,
			'role'       => 'delicious_recipes_subscriber',
		) );

		$customer_id = wp_insert_user( $new_customer_data );

		if ( is_wp_error( $customer_id ) ) {
			return new WP_Error( 'registration-error', __( 'Error : ', 'delicious-recipes' )  . __( 'Couldn&#8217;t register you&hellip; please contact us if you continue to have problems.', 'delicious-recipes' ) );
		}

		do_action( 'delicious_recipes_created_customer', $customer_id, $new_customer_data, $password_generated );

		return $customer_id;
	}
}

/**
 * Login a member (set auth cookie and set global user object).
 *
 * @param int $customer_id
 */
function delicious_recipes_set_customer_auth_cookie( $customer_id ) {
	global $current_user;

	$current_user = get_user_by( 'id', $customer_id );

	wp_set_auth_cookie( $customer_id, true );
}

/**
 * Returns the url to the lost password endpoint url.
 *
 * @return string
 */
function delicious_recipes_lostpassword_url() {
	$default_url = wp_lostpassword_url();
	// Avoid loading too early.
	if ( ! did_action( 'init' ) ) {
		$url = $default_url;
	} else {
		// Don't redirect to the Delicious Recipes endpoint on global network admin lost passwords.
		if ( is_multisite() && isset( $_GET['redirect_to'] ) && false !== strpos( wp_unslash( $_GET['redirect_to'] ), network_admin_url() ) ) { // WPCS: input var ok, sanitization ok.
			$url = $default_url;
		} else {
			$dashboard_page_url    = delicious_recipes_get_page_permalink_by_id( delicious_recipes_get_dashboard_page_id() );
			$dashboard_page_exists = delicious_recipes_get_dashboard_page_id() > 0;

			if ( $dashboard_page_exists ) {
				$url = $dashboard_page_url . '?action=lost-pass';
			} else {
				$url = $default_url;
			}
		}
	}
	return apply_filters( 'delicious_recipes_lostpassword_url', $url, $default_url );
}

function delicious_recipes_get_user_by_id_or_email( $id_or_email ){
	$user = false;
	$user_id = false;
	if ( is_numeric( $id_or_email ) ):
		$id = (int) $id_or_email;
		$user = get_user_by( 'id' , $id );
	elseif ( is_object( $id_or_email ) ):
		if ( ! empty( $id_or_email->user_id ) ):
			$id = (int) $id_or_email->user_id;
			$user = get_user_by( 'id' , $id );
		endif;
	else:
		$user = get_user_by( 'email', $id_or_email );
	endif;

	if ( $user && is_object( $user ) ):
		$user_id = $user->data->ID;
	endif;
	return $user_id;
}

function delicious_recipes_get_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ){

	$user_id = delicious_recipes_get_user_by_id_or_email( $id_or_email );
	$_user_meta = get_user_meta( $user_id, 'delicious_recipes_user_meta', true );
	if ( isset($_user_meta['profile_image_id']) && $_user_meta['profile_image_id'] ):
		$src = wp_get_attachment_image_src( $_user_meta['profile_image_id'], array( $size,$size ) );
		$src = ( isset($src[0]) && $src[0] ? $src[0] : $src );
		$avatar = "<img alt='{$alt}' src='{$src}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
	endif;
	return $avatar;

}

add_filter( 'get_avatar', 'delicious_recipes_get_custom_avatar', 1, 5 );

function delicious_recipes_get_custom_avatar_url( $url, $id_or_email, $args ){
	$user_id = delicious_recipes_get_user_by_id_or_email( $id_or_email );
	$_user_meta = get_user_meta( $user_id, 'delicious_recipes_user_meta', true );
	if ( isset($_user_meta['profile_image_id']) && $_user_meta['profile_image_id'] ):
		$url = wp_get_attachment_image_src( $_user_meta['profile_image_id'], 'full' );
		$url = ( isset($url[0]) && $url[0] ? $url[0] : $url );
	endif;
	return $url;

}

add_filter( 'get_avatar_url', 'delicious_recipes_get_custom_avatar_url', 1, 3 );