<?php
/**
 * Class Customer_Reset_Password file.
 *
 * @package Delicious_Recipes
 * @since  1.2.0
 */

use WP_Delicious\EmailHelpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Customer_Reset_Password', false ) ) :

	/**
	 * Customer Reset Password.
	 *
	 * An email sent to the customer to reset password.
	 *
	 * @class       Customer_Reset_Password
	 * @extends     EmailHelpers
	 */
	class Customer_Reset_Password extends EmailHelpers {

		/**
		 * User ID.
		 *
		 * @var integer
		 */
		public $user_id;

		/**
		 * User login name.
		 *
		 * @var string
		 */
		public $user_login;

		/**
		 * User email.
		 *
		 * @var string
		 */
		public $user_email;

		/**
		 * Reset key.
		 *
		 * @var string
		 */
		public $reset_key;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->template_html  = 'emails/customer-reset-password.php';
			$this->placeholders   = array(
				'{username}'            => '',
				'{reset_password_link}' => '',
			);

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Get email subject.
		 *
		 * @return string
		 */
		public function get_default_subject() {
			$subject = delicious_recipes_get_array_values_by_index( $this->settings, 'resetPasswordSubject', '' );
			$subject = empty( $subject ) ?  __( 'Password Reset Request for {site_title}', 'delicious-recipes' ) : $subject;
			return $subject;
		}

		/**
		 * Trigger.
		 *
		 * @param int    $user_id User ID.
		 * @param string $user_pass User password.
		 * @param bool   $password_generated Whether the password was generated automatically or not.
		 */
		public function trigger( $user_login = '', $reset_key = '' ) {

			if ( $user_login && $reset_key ) {
				$this->object     = get_user_by( 'login', $user_login );
				$this->user_id    = $this->object->ID;
				$this->user_login = $user_login;
				$this->reset_key  = $reset_key;
				$this->user_email = stripslashes( $this->object->user_email );
				$this->recipient  = $this->user_email;

				$this->placeholders['{username}']            = $this->user_login;
				$this->placeholders['{reset_password_link}'] = add_query_arg( array( 'key' => $this->reset_key, 'login' => rawurlencode( $this->user_login ) ), delicious_recipes_lostpassword_url() );
				
			}

			if ( $this->get_recipient() ) {
				$this->send( 
					$this->get_recipient(), 
					$this->get_subject(), 
					$this->get_content(), 
					$this->get_headers(), 
					$this->get_attachments() );
			}

		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			ob_start();

			$this->email_header();

			echo  wpautop( wptexturize( delicious_recipes_get_template_content( 'reset_password', $this->template_html, 'customer' ) ) );

			$this->email_footer();

			return ob_get_clean();
		}

	}

endif;
