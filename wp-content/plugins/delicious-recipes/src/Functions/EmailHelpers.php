<?php
/**
 * Email Template and functions.
 *
 * @package DELICIOUS_RECIPES
 * @subpackage  DELICIOUS_RECIPES
 * @since  1.2.0
 */

namespace WP_Delicious;

defined( 'ABSPATH' ) || exit;

/**
 * Email Helpers.
 */
class EmailHelpers {

	/**
	 * Global Settings.
	 *
	 * @var object
	 */
	public $settings;

	/**
	 * HTML template path.
	 *
	 * @var string
	 */
	public $template_html;
	
	/**
	 * Recipients for the email.
	 *
	 * @var string
	 */
	public $recipient;

	/**
	 * Object this email is for, for example a customer, product, or email.
	 *
	 * @var object|bool
	 */
	public $object;

	/**
	 * True when email is being sent.
	 *
	 * @var bool
	 */
	public $sending;

	/**
	 * Strings to find/replace in subjects/headings.
	 *
	 * @var array
	 */
	protected $placeholders = array();

	private static $_instance = null;

    public static function get_instance(){
        if( null === self::$_instance){
            self::$_instance = new self;
        }
        return self::$_instance;
    }

	public function __construct() {
        $this->init_hooks();

    }

	/**
	 * Init email classes.
	 */
	public function init_hooks() {

		$this->settings = get_option( 'delicious_recipe_settings', array() );
		$this->placeholders = array_merge(
			array(
				'{site_title}'   => $this->get_blogname(),
				'{site_address}' => wp_parse_url( home_url(), PHP_URL_HOST ),
				'{site_url}'     => wp_parse_url( home_url(), PHP_URL_HOST ),
				'{admin_email}'  => $this->get_from_name()
			),
			$this->placeholders
		);

		// Hooks for sending emails.
		add_action( 'delicious_recipes_created_customer', array( $this, 'customer_new_account' ), 20, 3 );
		add_action( 'delicious_recipes_reset_password_notification', array( $this, 'customer_reset_password' ), 10, 2 );
	}

	/**
	 * Format email string.
	 *
	 * @param mixed $string Text to replace placeholders in.
	 * @return string
	 */
	public function format_string( $string ) {
		$find    = array_keys( $this->placeholders );
		$replace = array_values( $this->placeholders );

		// Filter for main find/replace.
		return apply_filters( 'delicious_recipes_email_format_string', str_replace( $find, $replace, $string ) );
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		return  $this->format_string( $this->get_default_subject() );
	}

	/**
	 * Get valid recipients.
	 *
	 * @return string
	 */
	public function get_recipient() {
		$recipient  = apply_filters( 'delicious_recipes_email_recipients', $this->recipient );
		$recipients = array_map( 'trim', explode( ',', $recipient ) );
		$recipients = array_filter( $recipients, 'is_email' );
		return implode( ', ', $recipients );
	}

	/**
	 * Get email headers.
	 *
	 * @return string
	 */
	public function get_headers() {
		$header = 'Content-Type: ' . $this->get_content_type() . "\r\n";

		if ( $this->get_from_address() && $this->get_from_name() ) {
			$header .= 'Reply-to: ' . $this->get_from_name() . ' <' . $this->get_from_address() . ">\r\n";
		}

		return apply_filters( 'delicious_recipes_email_headers', $header );
	}

	/**
	 * Get email attachments.
	 *
	 * @return array
	 */
	public function get_attachments() {
		return apply_filters( 'delicious_recipes_email_attachments', array() );
	}

	/**
	 * Get email content type.
	 *
	 * @return string
	 */
	public function get_content_type() {
		$content_type = 'text/html';

		return apply_filters( 'delicious_recipes_email_content_type', $content_type );
	}

	/**
	 * Get WordPress blog name.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Get the from name for outgoing emails.
	 *
	 * @param string $from_name Default wp_mail() name associated with the "from" email address.
	 * @return string
	 */
	public function get_from_name( $from_name = '' ) {
		return wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	}

	/**
	 * Get the from address for outgoing emails.
	 *
	 * @param string $from_email Default wp_mail() email address to send from.
	 * @return string
	 */
	public function get_from_address( $from_email = '' ) {
		return sanitize_email( get_option( 'admin_email' ) );
	}

	/**
	 * Get the email header.
	 *
	 * @param mixed $email_heading Heading for the email.
	 */
	public function email_header() {
		delicious_recipes_get_template( 'emails/email-header.php' );
	}

	/**
	 * Get the email footer.
	 */
	public function email_footer() {
		delicious_recipes_get_template( 'emails/email-footer.php' );
	}

	/**
	 * Get the email content in HTML format.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return ''; 
	}

	/**
	 * Get email content.
	 *
	 * @return string
	 */
	public function get_content() {
		$this->sending = true;

		$email_content = $this->format_string( $this->get_content_html() );

		return $email_content;
	}

	/**
	 * Wraps a message in the delicious recipes mail template.
	 *
	 * @param string $email_heading Heading text.
	 * @param string $message       Email message.
	 *
	 * @return string
	 */
	public function wrap_message( $email_heading, $message ) {
		// Buffer.
		ob_start();

		$this->email_header();

		echo wpautop( wptexturize( $message ) ); // WPCS: XSS ok.

		$this->email_footer();

		// Get contents.
		$message = ob_get_clean();

		return $message;
	}

	/**
	 * Send an email.
	 *
	 * @param string $to Email to.
	 * @param string $subject Email subject.
	 * @param string $message Email message.
	 * @param string $headers Email headers.
	 * @param array  $attachments Email attachments.
	 * @return bool success
	 */
	public function send( $to, $subject, $message, $headers, $attachments ) {
		wp_mail( $to, $subject, $message, $headers, $attachments );
	}

	/**
	 * Customer new account welcome email.
	 *
	 * @param int   $customer_id        Customer ID.
	 * @param array $new_customer_data  New customer data.
	 * @param bool  $password_generated If password is generated.
	 */
	public function customer_new_account( $customer_id, $new_customer_data = array(), $password_generated = false ) {
		if ( ! $customer_id ) {
			return;
		}

		$user_pass = ! empty( $new_customer_data['user_pass'] ) ? $new_customer_data['user_pass'] : '';

		include plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/classes/emails/customer-new-account.php';
		$email = new \Customer_New_Account();
		$email->trigger( $customer_id, $user_pass, $password_generated );
	}

	/**
	 * Customer reset password email.
	 *
	 * @param string $user_login User login.
	 * @param string $reset_key Password reset key.
	 */
	public function customer_reset_password( $user_login = '', $reset_key = '' ) {
		if ( ! $reset_key && ! $user_login ) {
			return;
		}

		include plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/classes/emails/customer-reset-password.php';
		$email = new \Customer_Reset_Password();
		$email->trigger( $user_login, $reset_key );
	}

}

EmailHelpers::get_instance();
