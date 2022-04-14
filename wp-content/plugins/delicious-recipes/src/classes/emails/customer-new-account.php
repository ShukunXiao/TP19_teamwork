<?php
/**
 * Class Customer_New_Account file.
 *
 * @package Delicious_Recipes
 * @since  1.2.0
 */

use WP_Delicious\EmailHelpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Customer_New_Account', false ) ) :

	/**
	 * Customer New Account.
	 *
	 * An email sent to the customer when they create an account.
	 *
	 * @class       Customer_New_Account
	 * @extends     EmailHelpers
	 */
	class Customer_New_Account extends EmailHelpers {

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
		 * User password.
		 *
		 * @var string
		 */
		public $user_pass;

		/**
		 * Is the password generated?
		 *
		 * @var bool
		 */
		public $password_generated;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->template_html  = 'emails/customer-new-account.php';
			$this->placeholders   = array(
				'{username}'       => '',
				'{password}'       => '',
				'{dashboard_page}' => '',
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
			$subject = delicious_recipes_get_array_values_by_index( $this->settings, 'newAccountSubject', '' );
			$subject = empty( $subject ) ?  __( 'Your {site_title} account has been created!', 'delicious-recipes' ) : $subject;
			return $subject;
		}

		/**
		 * Trigger.
		 *
		 * @param int    $user_id User ID.
		 * @param string $user_pass User password.
		 * @param bool   $password_generated Whether the password was generated automatically or not.
		 */
		public function trigger( $user_id, $user_pass = '', $password_generated = false ) {

			if ( $user_id ) {
				$this->object = new \WP_User( $user_id );

				$this->user_pass          = $user_pass;
				$this->user_login         = stripslashes( $this->object->user_login );
				$this->user_email         = stripslashes( $this->object->user_email );
				$this->recipient          = $this->user_email;
				$this->password_generated = $password_generated;
				$this->placeholders['{username}']       = $this->user_login;
				$this->placeholders['{password}']       = $password_generated ? $this->user_pass . '  (automatically generated)' : $this->user_pass;
				$this->placeholders['{dashboard_page}'] = delicious_recipes_get_page_permalink_by_id( delicious_recipes_get_dashboard_page_id() );
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

			echo  wpautop( wptexturize( delicious_recipes_get_template_content( 'new_account', $this->template_html, 'customer' ) ) );

			$this->email_footer();

			return ob_get_clean();
		}

	}

endif;
