<?php
/**
 * Contains the query functions for Delicious Recipes which alter the front-end post queries and loops
 *
 * @version 1.2.0
 * @package Delicious_Recipes
 * @author Delicious Recipes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delicious_Recipes_Query Class.
 */
class Delicious_Recipes_Query {

	/**
	 * Query vars to add to wp.
	 *
	 * @var array
	 */
	public $query_vars = array();

	/**
	 * Stores chosen attributes.
	 *
	 * @var array
	 */
	private static $_chosen_attributes;

	/**
	 * Constructor for the query class. Hooks in methods.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
		}
		$this->init_query_vars();
	}
	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {
		// Query vars to add to WP.
		$this->query_vars = array(
			'delicious-recipes-lost-pass'       => 'delicious-recipes-lost-pass',
			'delicious-recipes-customer-logout' => 'delicious-recipes-customer-logout',
		);
	}


	/**
	 * Add query vars.
	 *
	 * @access public
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->get_query_vars() as $key => $var ) {
			$vars[] = $key;
		}
		return $vars;
	}

	/**
	 * Get query vars.
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return apply_filters( 'delicious_recipes_get_query_vars', $this->query_vars );
	}
	/**
	 * Endpoint mask describing the places the endpoint should be added.
	 *
	 * @since 2.6.2
	 * @return int
	 */
	public function get_endpoints_mask() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_on_front     = get_option( 'page_on_front' );
			$myaccount_page_id = delicious_recipes_get_dashboard_page_id();

			if ( in_array( $page_on_front, array( $myaccount_page_id ), true ) ) {
				return EP_ROOT | EP_PAGES;
			}
		}

		return EP_PAGES;
	}

	/**
	 * Add endpoints for query vars.
	 */
	public function add_endpoints() {
		$mask = $this->get_endpoints_mask();

		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( ! empty( $var ) ) {
				add_rewrite_endpoint( $var, $mask );
			}
		}
	}

}
new Delicious_Recipes_Query();
