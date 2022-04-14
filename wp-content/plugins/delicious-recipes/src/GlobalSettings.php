<?php
/**
 * Settings page under Settings for global settings.
 *
 * @package Delicious_Recipes
 * @subpackage  Delicious_Recipes
 */

namespace WP_Delicious;

defined( 'ABSPATH' ) || exit;

/**
 * Global Settings.
 */
class GlobalSettings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init() {
		
		// Initialize hooks.
		$this->init_hooks();

		// Allow 3rd party to remove hooks.
		do_action( 'wp_delicious_options_unhook', $this );
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init_hooks() {

		// Add menu in settins page.
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
	}

	/**
	 * Add submenu page for form editor.
	 * 
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function add_submenu_page() {

		add_submenu_page( 
			'edit.php?post_type=' . DELICIOUS_RECIPE_POST_TYPE, 
			__( 'Delicious Recipes', 'delicious-recipes' ), 
			__( 'Settings', 'delicious-recipes' ), 
			'manage_options', 
			'delicious_recipes_global_settings', 
			array( $this, 'display_submenu_page' ),
			10
		);

	}

	/**
	 * Display submenu page template
	 * 
	 * @since 1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function display_submenu_page() {
		
		global $pagenow;

		if ( 'edit.php' === $pagenow ) {
			?>
				<div id="delicious-recipe-global" data-rest-nonce="<?php echo wp_create_nonce( 'wp_rest' ); ?>"></div>
			<?php
		}

	}

}
