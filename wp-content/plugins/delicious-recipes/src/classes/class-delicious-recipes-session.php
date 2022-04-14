<?php
/**
 * Wrapper for WP Session Manager.
 *
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * WP Session Manager wraper.
 */
class Delicious_Recipes_Session {
	/**
	 * Holds session data.
	 *
	 * @var array
	 */
	private $session;

	/**
	 * Constructor function.
	 */
	public function __construct() {
		// Let users change the session cookie name.
		if ( ! defined( 'DELICIOUS_RECIPES_SESSION_COOKIE' ) ) {
			define( 'DELICIOUS_RECIPES_SESSION_COOKIE', '_delicious_recipes_session' );
		}

		if ( ! class_exists( 'Recursive_ArrayAccess' ) ) {
			include plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/lib/wp-session/class-recursive-arrayaccess.php';
		}

		if ( ! class_exists( 'WP_Session_Utils' ) ) {
			include plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/lib/wp-session/class-wp-session-utils.php';
		}

		if ( ! class_exists( 'WP_Session' ) ) {
			include plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/lib/wp-session/class-wp-session.php';
			include plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/lib/wp-session/wp-session.php';
		}

		if ( empty( $this->session ) ) { // on page load or refresh.
			add_action( 'plugins_loaded', array( $this, 'init' ), -1 );
		} else {
			add_action( 'init', array( $this, 'init' ), -1 ); // maybe not required.
		}
	}

	/**
	 * Setup the WP_Session instance
	 *
	 * @access public
	 * @since 1.5
	 * @return array
	 */
	public function init() {
		$this->session = WP_Session::get_instance();
		return $this->session;
	}

	/**
	 * Get session data.
	 *
	 * @param  string $key session data key.
	 * @return mixed      session data.
	 */
	public function get( $key ) {
		$key = sanitize_key( $key );
		return isset( $this->session[ $key ] ) ? maybe_unserialize( $this->session[ $key ] ) : false;
	}

	/**
	 * Set data in session.
	 *
	 * @param string $key   session data key.
	 * @param mixed  $value  session data.
	 * @return mixed
	 */
	public function set( $key, $value ) {
		$key = sanitize_key( $key );
		if ( is_array( $value ) ) {
			$this->session[ $key ] = serialize( $value );
		} else {
			$this->session[ $key ] = $value;
		}

		return $this->session[ $key ];
	}
}
