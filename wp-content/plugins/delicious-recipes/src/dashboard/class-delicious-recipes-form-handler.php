<?php
// Form handling for Dashboard.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handle frontend forms.
 *
 * @class 		Delicious_Recipes_Form_Handler
 * @version		1.2.0
 * @category	Class
 */
class Delicious_Recipes_Form_Handler {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'redirect_reset_password_link' ) );
		add_action( 'template_redirect', array( __CLASS__, 'save_edit_profile_details' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'process_login' ), 20 );		
		add_action( 'wp_loaded', array( __CLASS__, 'process_registration' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_lost_password' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_reset_password' ), 20 );

		add_action( 'wp_ajax_delicious_recipes_profile_image_upload', array( __CLASS__, 'upload_profile_image' ) );
		add_action( 'wp_ajax_nopriv_delicious_recipes_process_login', array( __CLASS__, 'process_login' ) );
	}

	/**
	 * Process the login form.
	 */
	public static function process_login() {

		$nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
		$nonce_value = isset( $_POST['delicious_recipes_user_login_nonce'] ) ? $_POST['delicious_recipes_user_login_nonce'] : $nonce_value;

		if ( ! empty( $_POST['login'] ) && wp_verify_nonce( $nonce_value, 'delicious_recipes_user_login' ) ) {

			try {
				$creds = array(
					'user_login'    => sanitize_user( $_POST['username'] ),
					'user_password' => $_POST['password'],
					'remember'      => isset( $_POST['rememberme'] ),
				);

				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'delicious_recipes_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( __( 'Error : ', 'delicious-recipes' )  . $validation_error->get_error_message() );
				}

				if ( empty( $creds['user_login'] ) ) {
					throw new Exception( __( 'Error : ', 'delicious-recipes' )  . __( 'Username is required.', 'delicious-recipes' ) );
				}

				// On multisite, ensure user exists on current site, if not add them before allowing login.
				if ( is_multisite() ) {
					$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

					if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
						add_user_to_blog( get_current_blog_id(), $user_data->ID, 'delicious_recipes_subscriber' );
					}
				}

				// Perform the login.
				$user = wp_signon( apply_filters( 'delicious_recipes_login_credentials', $creds ), is_ssl() );

				if ( is_wp_error( $user ) ) {
					$message = $user->get_error_message();
					$message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', $message );
					throw new Exception( $message );

				} elseif ( isset( $_POST['calling_action'] ) && 'delicious_recipes_modal_login' === $_POST['calling_action'] ) { 

					wp_send_json_success( 
						array(
						'success' => __( 'Success :  Login Successful', 'delicious-recipes' )
					) );

				} else {

					if ( ! empty( $_POST['redirect'] ) ) {
						$redirect = $_POST['redirect'];
					} elseif ( delicious_recipes_get_raw_referer() ) {
						$redirect = delicious_recipes_get_raw_referer();
					} else {
						$redirect = delicious_recipes_get_page_permalink_by_id( delicious_recipes_get_dashboard_page_id() );
					}

					wp_redirect( wp_validate_redirect( apply_filters( 'delicious_recipes_login_redirect', remove_query_arg( 'delicious_recipes_error', $redirect ), $user ), delicious_recipes_get_page_permalink_by_id( delicious_recipes_get_dashboard_page_id() ) ) );

					exit;
				}
			} catch ( Exception $e ) {

				if ( isset( $_POST['calling_action'] ) && 'delicious_recipes_modal_login' === $_POST['calling_action'] ){

					wp_send_json_error( 
						array(
						'error' => __( 'Error :  Invalid Username or Password', 'delicious-recipes' )
					) );
				}
				DEL_RECIPE()->notices->add( apply_filters( 'delicious_recipes_login_errors', __( 'Error :  Invalid Username or Password', 'delicious-recipes' ) ), 'error' );

			}
		} elseif ( isset( $_POST['username'] ) && empty( $_POST['username'] ) && wp_verify_nonce( $nonce_value, 'delicious_recipes_user_login' ) ) {

			DEL_RECIPE()->notices->add( apply_filters( 'delicious_recipes_login_errors', __( 'Error :  Username can not be empty', 'delicious-recipes' ) ), 'error' );

		}
	}

	/**
	 * Process the registration form.
	 */
	public static function process_registration() {
		$nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
		$nonce_value = isset( $_POST['delicious-recipes-user-register-nonce'] ) ? $_POST['delicious-recipes-user-register-nonce'] : $nonce_value;

		if ( ! empty( $_POST['register'] ) && wp_verify_nonce( $nonce_value, 'delicious-recipes-user-register' ) ) {
			$global_toggles  = delicious_recipes_get_global_toggles_and_labels();

			$username = $global_toggles['generate_username'] ? '' : sanitize_user( $_POST['username'] );
			$password = $global_toggles['generate_password'] ? '' : $_POST['password'];
			$email    = sanitize_email( $_POST['email'] );

			try {
				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'delicious_recipes_process_registration_errors', $validation_error, $username, $password, $email );

				if ( $password && ( $_POST['password'] !== $_POST['c-password'] ) ) {
					throw new Exception( __( 'Passwords do not match', 'delicious-recipes' ) );
				}

				if ( $validation_error->get_error_code() ) {
					throw new Exception( $validation_error->get_error_message() );
				}

				$new_customer = delicious_recipes_create_new_recipe_user( sanitize_email( $email ), $username, $password );

				if ( is_wp_error( $new_customer ) ) {
					throw new Exception( $new_customer->get_error_message() );
				}

				if ( apply_filters( 'delicious_recipes_registration_auth_new_customer', true, $new_customer ) ) {
					delicious_recipes_set_customer_auth_cookie( $new_customer );
				}

				if ( ! empty( $_POST['redirect'] ) ) {
					$redirect = wp_sanitize_redirect( $_POST['redirect'] );
				} elseif ( delicious_recipes_get_raw_referer() ) {
					$redirect = delicious_recipes_get_raw_referer();
				} else {
					$redirect = delicious_recipes_get_page_permalink_by_id( delicious_recipes_get_dashboard_page_id() );
				}

				wp_redirect( wp_validate_redirect( apply_filters( 'delicious_recipes_register_redirect', remove_query_arg( array( 'register', 'delcious_recipes_error' ), $redirect ), $username ), delicious_recipes_get_page_permalink_by_id( delicious_recipes_get_dashboard_page_id() ) ) );
				exit;

			} catch ( Exception $e ) {
				DEL_RECIPE()->notices->add( __( 'Error : ', 'delicious-recipes' )  . $e->getMessage(), 'error' );
			}
		}
	}

	/**
	 * Handle lost password form.
	 */
	public static function process_lost_password() {
		if ( isset( $_POST['delicious_recipes_reset_password'] ) && isset( $_POST['user_login'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'delicious_recipes_lost_password' ) ) {
			
			$success = Delicious_Recipes_User_Account::retrieve_password();

			// If successful, redirect to my account with query arg set.
			if ( $success ) {
				wp_redirect( add_query_arg( 'reset-link-sent', 'true', delicious_recipes_lostpassword_url() ) );
				exit;
			}
		}
	}

	/**
	 * Handle reset password form.
	 */
	public static function process_reset_password() {
		$posted_fields = array( 'delicious_recipes_reset_password', 'password_1', 'password_2', 'reset_key', 'reset_login', '_wpnonce' );

		foreach ( $posted_fields as $field ) {
			if ( ! isset( $_POST[ $field ] ) ) {
				return;
			}
			$posted_fields[ $field ] = $_POST[ $field ];
		}

		if ( ! wp_verify_nonce( $posted_fields['_wpnonce'], 'delicious_recipes_reset_password_nonce' ) ) {
			return;
		}

		$user = Delicious_Recipes_User_Account::check_password_reset_key( $posted_fields['reset_key'], $posted_fields['reset_login'] );

		if ( $user instanceof WP_User ) {
			if ( empty( $posted_fields['password_1'] ) ) {
				DEL_RECIPE()->notices->add( __( 'Error :  Please enter your password.', 'delicious-recipes' ), 'error' );
			}

			if ( $posted_fields['password_1'] !== $posted_fields['password_2'] ) {
				DEL_RECIPE()->notices->add( __( 'Error :  Passwords do not match', 'delicious-recipes' ), 'error' );
			}

			$errors = new WP_Error();

			do_action( 'validate_password_reset', $errors, $user );

			delicious_recipes_add_wp_error_notices( $errors );

			if ( 0 === delicious_recipes_get_notice_count( 'error' ) ) {
				Delicious_Recipes_User_Account::reset_password( $user, $posted_fields['password_1'] );

				do_action( 'delicious_recipes_customer_reset_password', $user );

				wp_redirect( add_query_arg( 'password-reset', 'true', delicious_recipes_get_page_permalink_by_id( delicious_recipes_get_dashboard_page_id() ) ) );
				exit;
			}
		}
	}

	/**
	 * Remove key and login from query string, set cookie, and redirect to account page to show the form.
	 */
	public static function redirect_reset_password_link() {

		if ( delicious_recipes_is_account_page() && ! empty( $_GET['key'] ) && ! empty( $_GET['login'] ) ) {

			$value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );

			Delicious_Recipes_User_Account::set_reset_password_cookie( $value );

			wp_safe_redirect( add_query_arg( 'show-reset-form', 'true', delicious_recipes_lostpassword_url() ) );
			exit;
		}
	}

	/**
	 * Save the password/account details and redirect back to the user dashboard page.
	 */
	public static function save_edit_profile_details() {
		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		if ( empty( $_POST['action'] ) || 'delicious_recipes_edit_user_profile' !== $_POST['action'] || empty( $_POST['delicious_recipes_edit_profile_nonce'] ) || ! wp_verify_nonce( $_POST['delicious_recipes_edit_profile_nonce'], 'delicious-recipes-edit-profile-nonce' ) ) {
			return;
		}

		nocache_headers();

		if( ! is_user_logged_in() ) {
			return;
		}

		if ( get_current_user_id() == $_POST['user_id'] || current_user_can('administrator') ) {
			$user = get_user_by( 'id', absint( $_POST['user_id'] ) );
			$user_id = $user->ID;
		} else {
			return;
		}

		$current_user       = get_user_by( 'id', $user_id );
		$current_first_name = $current_user->first_name;
		$current_last_name  = $current_user->last_name;
		$current_email      = $current_user->user_email;

		$account_username     = ! empty( $_POST['username'] ) ? sanitize_user( $_POST['username'] ): '';
		$account_email        = ! empty( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		$account_display_name = ! empty( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$pass_cur             = ! empty( $_POST['current_password'] ) ? $_POST['current_password'] : '';
		$pass1                = ! empty( $_POST['new_password'] ) ? $_POST['new_password'] : '';
		$pass2                = ! empty( $_POST['confirm_password'] ) ? $_POST['confirm_password'] : '';
		$save_pass            = true;

		$user               = new stdClass();
		$user->ID           = $user_id;
		$user->first_name   = $current_first_name;
		$user->last_name    = $current_last_name;

		$user->display_name = ! empty( $account_display_name ) ? $account_display_name : $current_user->display_name;

		// Handle required fields.
		$required_fields = apply_filters( 'delicious_recipes_edit_profile_required_fields', array(
			'username' => __( 'Username', 'delicious-recipes' ),
			'email'    => __( 'Email', 'delicious-recipes' ),
		) );

		foreach ( $required_fields as $field_key => $field_name ) {
			if ( empty( $_POST[ $field_key ] ) ) {
				/* translators: %s: field name */
				DEL_RECIPE()->notices->add( sprintf( __( 'Error :   %s is a required field.', 'delicious-recipes' ), esc_html( $field_name ) ), 'error' );
			}
		}

		if ( $account_email ) {
			$account_email = sanitize_email( $account_email );
			if ( ! is_email( $account_email ) ) {
				DEL_RECIPE()->notices->add( __( 'Please provide a valid email address', 'delicious-recipes' ), 'error' );
			} elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
				DEL_RECIPE()->notices->add( __( 'The email address is already registered', 'delicious-recipes' ), 'error' );
			}
			$user->user_email = $account_email;
		}

		if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
			DEL_RECIPE()->notices->add( __( 'Please Fill Out All Password Fields.', 'delicious-recipes' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
			DEL_RECIPE()->notices->add( __( 'Please Enter Your Current Password', 'delicious-recipes' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
			DEL_RECIPE()->notices->add( __( 'Please re-enter your password', 'delicious-recipes' ), 'error' );
			$save_pass = false;
		} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
			DEL_RECIPE()->notices->add( __( 'New Passwords do not match', 'delicious-recipes' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
			DEL_RECIPE()->notices->add( __( 'Your current password is incorrect', 'delicious-recipes' ), 'error' );
			$save_pass = false;
		}

		if ( $pass1 && $save_pass ) {
			$user->user_pass = $pass1;
		}

		$_user_meta = get_user_meta( $user_id, 'delicious_recipes_user_meta', true );

		if ( ! is_array( $_user_meta ) || empty( $_user_meta ) ) : 
			$_user_meta = array(); 
		endif;

		$profile_image_file = isset( $_POST['profile_image'] ) && ! empty( $_POST['profile_image'] ) ? sanitize_text_field( wp_normalize_path( $_POST['profile_image'] ) ) : false;
		$profile_image_url = isset( $_POST['profile_image_url'] ) && ! empty( $_POST['profile_image_url'] ) ? esc_url_raw( $_POST['profile_image_url'] ) : false;

		if ( $profile_image_file && $profile_image_url && $profile_image_file != 'custom' ) :

			if ( isset($_user_meta['profile_image_id']) && is_numeric($_user_meta['profile_image_id']) ) : 
				wp_delete_attachment( $_user_meta['profile_image_id'] ); 
			endif;

			$attach_id = self::set_profile_image( $profile_image_file, $user_id );

			if ( ! $attach_id ) :
				DEL_RECIPE()->notices->add( __( 'There was an issue updating your profile photo.', 'delicious-recipes' ), 'error' );
			else :
				$_user_meta['profile_image_id'] = absint( $attach_id );
				update_user_meta( $user_id, 'delicious_recipes_user_meta', $_user_meta );
			endif;

		elseif ( ! $profile_image_file ):
			if ( isset($_user_meta['profile_image_id']) && is_numeric($_user_meta['profile_image_id']) ) : 
				wp_delete_attachment( $_user_meta['profile_image_id'] ); 
			endif;

			$_user_meta['profile_image_id'] = false;
			update_user_meta( $user_id, 'delicious_recipes_user_meta', $_user_meta );
		endif;

		// Allow plugins to return their own errors.
		$errors = new WP_Error();
		do_action_ref_array( 'delicious_recipes_edit_profile_errors', array( &$errors, &$user ) );

		if ( $errors->get_error_messages() ) {
			foreach ( $errors->get_error_messages() as $error ) {
				DEL_RECIPE()->notices->add( $error, 'error' );
			}
		}

		if ( delicious_recipes_get_notice_count( 'error' ) === 0 ) {
			wp_update_user( $user );

			DEL_RECIPE()->notices->add( __( 'Account Details Updated Successfully', 'delicious-recipes' ), 'success' );

			do_action( 'delicious_recipes_edit_profile', $user->ID );
		}

		if ( is_numeric( $_POST['redirect_id'] ) ) {
			wp_safe_redirect( get_permalink( $_POST['redirect_id'] ) );
			exit;
		}
	}

	/**
	 * Set user dashboard profile image
	 *
	 * @param boolean $image_url
	 * @param boolean $user_id
	 * @return void
	 */
	public static function set_profile_image( $image_url = false, $user_id = false ){

		if ( $image_url && $user_id ) :

			$_user_meta = get_user_meta( $user_id, 'delicious_recipes_user_meta' );

			if ( ! is_array($_user_meta) ):
				$_user_meta = array();
			endif;

            $_uploads         = wp_upload_dir();
            $recipe_image_dir = trailingslashit( $_uploads['basedir'] ) . 'delicious-recipes/images/users';
            $_wp_filetype     = wp_check_filetype( $image_url, null );
            $file_ext         = isset( $_wp_filetype['ext'] ) ? $_wp_filetype['ext'] : '.jpg';

			$_image_file  = $recipe_image_dir . "/delicious_recipes_user_" . $user_id . '.' .$file_ext;

            if ( wp_mkdir_p( $recipe_image_dir ) ) :

                if ( file_exists($image_url) || file_exists($recipe_image_dir . "/delicious_recipes_user_" . $user_id . '.' .$file_ext) ) :

                    if ( file_exists($recipe_image_dir . "/delicious_recipes_user_" . $user_id . '.' .$file_ext) ) :
                        unlink( $recipe_image_dir . "/delicious_recipes_user_" . $user_id . '.' .$file_ext );
                    endif;
                    
					if ( file_exists($image_url) ):
                        rename( $image_url, $recipe_image_dir . "/delicious_recipes_user_" . $user_id . '.' .$file_ext );
                    endif;
                
				endif;

            endif;

			$attachment   = array(
				'post_mime_type' => $_wp_filetype['type'],
				'post_title'     => sanitize_file_name( "delicious_recipes_user_" . $user_id . '.' .$file_ext ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $_image_file );

			if ( defined( 'ABSPATH' ) && file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) :
				
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				
				$attach_data        = wp_generate_attachment_metadata( $attach_id, $_image_file );
				$update_attachment  = wp_update_attachment_metadata( $attach_id, $attach_data );
				$_user_meta['profile_image_id'] = $attach_id;
				
				return $attach_id;

			endif;

        endif;

        return false;

	}

	/**
	 * Upload profile image from form.
	 *
	 * @return void
	 */
	public static function upload_profile_image() {

        if ( ! empty( $_FILES )  && wp_verify_nonce( $_REQUEST['nonce'], 'delicious-recipes-profile-image-nonce' )  ) :

			$allowed_filetypes = [ 'image/jpeg', 'image/png', 'image/gif' ];

            $_uploads   = wp_upload_dir();
            $dr_tmp_dir = trailingslashit( $_uploads['basedir'] ) . 'delicious-recipes/tmp';
            $dr_tmp_url = str_replace( array( 'http://', 'https://' ), '//', trailingslashit( $_uploads['baseurl'] ) . 'delicious-recipes/tmp' );
			
			$source      = $_FILES['file']['tmp_name'];
			$salt        = md5( $_FILES['file']['name'] . time() );
			$file_name   = $salt . '-' . $_FILES['file']['name'];
			$destination = trailingslashit( $dr_tmp_dir ) . $file_name;
			
			$upload_url        = trailingslashit( $dr_tmp_url ) . $file_name;
			$uploaded_filetype = wp_check_filetype( basename( $destination ), null );

			$uploaded_filesize = $_FILES['file']['size'];
			$max_upload_size   = wp_max_upload_size();

			if ( $uploaded_filesize > $max_upload_size ) {
				wp_send_json_error( [ 'message' => __( 'File size too large.', 'delicious-recipes' ) ] );
			}

			if ( ! in_array( $uploaded_filetype['type'], $allowed_filetypes ) ) {
				wp_send_json_error( [ 'message' => __( 'Unsupported file type uploaded.', 'delicious-recipes' ) ] );
			}
            
			if ( wp_mkdir_p( $dr_tmp_dir ) ) :
				if ( move_uploaded_file( $source, $destination ) ):
                    
					$file_array = array( 'file' => $destination, 'url' => $upload_url, 'type' => $uploaded_filetype );
                    echo json_encode( $file_array );
					wp_die();

                endif;
            endif;
        endif;

		wp_send_json_error( __( 'Invalid request. Nonce verification failed.', 'delicious-recipes' ) );
    }

}
// Run the show.
Delicious_Recipes_Form_Handler::init();
