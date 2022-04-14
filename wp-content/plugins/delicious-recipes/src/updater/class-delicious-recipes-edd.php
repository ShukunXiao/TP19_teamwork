<?php
/**
 * Addons EDD update handler class
 *
 * @package Delicious_Recipes
 */
/**
 * Class Defination
 */
class Delicious_Recipes_EDD_Handler {

	/**
	 * EDD API URL
	 *
	 * @var string
	 */
	private $edd_api_url = 'https://wpdelicious.com/';
	/**
     * Constructor
     */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initilization init_hooks
	 *
	 * @return void
	 */
	public function init_hooks() {
		add_action( 'admin_menu', array( $this, 'upgrade_to_pro_menu' ) );
	}

	/**
	 * Upgrade to pro link
	 *
	 * @return void
	 */
	public function upgrade_to_pro_menu() {
		/**
		 * Return if pro is already active.
		 */
		if ( $this->is_pro_active() ) return;

		global $submenu;
		$pricing_url = $this->edd_api_url . 'pricing';
		$submenu['edit.php?post_type=recipe'][] = array( __( 'Upgrade to Pro', 'delicious-recipes' ), 'manage_options', $pricing_url);
	}

	/**
	 * Is Pro active
	 *
	 * @return boolean
	 */
	private function is_pro_active() {
		return function_exists('DEL_RECIPE_PRO');
	}
}
