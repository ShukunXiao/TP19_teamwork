<?php 
/**
 * What's New.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * What's New page to display important plugin links and changelogs.
 */
class Delicious_Recipes_Whats_New {

	/**
     * Constructor
     */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_whats_new_menu' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/** 
		* Add menu for What's New 
	*/
	public function add_whats_new_menu(){
		add_submenu_page( 
			'delicious-recipes', 
			__( "What's New", 'delicious-recipes' ), 
			__( "What's New", 'delicious-recipes' ), 
			'manage_options', 
			'delicious_recipes_whats_new', 
			array( $this, 'display_whats_new_menu_page' ),
			0
		);
	}

	/**
	 * Callback page.
	 *
	 * @return void
	 */
	public function display_whats_new_menu_page(){
		echo '<div id="dr_whats_new_screen_page" class="delicious-recipe-outer"></div>';
	}

	/**
	 * Enqueue Assets.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( isset( $screen->id ) && 'delicious-recipes_page_delicious_recipes_whats_new' === $screen->id ) {

			$whatsNew_deps = include_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/build/whatsNew.asset.php';

			wp_register_script( 'delicious-recipes-whatsNew', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/build/whatsNew.js', $whatsNew_deps['dependencies'], $whatsNew_deps['version'], true );

			// Add localization vars.
			wp_localize_script(
				'delicious-recipes-whatsNew',
				'DeliciousRecipes',
				array(
					'ajaxURL'   => esc_url( admin_url('admin-ajax.php') ),
					'siteURL'   => esc_url( home_url( '/' ) ),
					'adminURL'  => esc_url( admin_url() ),
					'pluginUrl' => esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ),
				)
			);

			wp_enqueue_script( 'delicious-recipes-whatsNew' );

		}
	}

}
new Delicious_Recipes_Whats_New();
