<?php 
/**
 * User onboarding process.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Onboarding Process to assist user on intial setup on first activation of the plugin.
 */
class Delicious_Recipes_Onboard {

	
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current page name 
	 */
	private $page_name = 'delicious-recipes-onboard';

	/**
	 * Initialize onboarding process class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'onboarding_menu_callback' ), 30 );
		add_action( 'admin_menu', array( $this, 'add_onboarding_admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'remove_onboard_menu' ) );
	}

	/** 
		* Add menu for Onboard Process 
	*/
	function add_onboarding_admin_menu(){
		add_menu_page(
			esc_html__( 'Delicious Recipes - User Onboarding', 'delicious-recipes' ),
			esc_html__( 'Delicious Recipes - User Onboarding', 'delicious-recipes' ),
			'manage_options',
			$this->page_name,
			[ $this, 'onboarding_menu_callback' ]
		);
	}

	/** 
		* Onboard Process 
	*/
	function onboarding_menu_callback(){

		// Do not proceed if we're not on the right page.
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== $this->page_name ) {
			return;
		}

		// Dynamic flag set to true, that the first time onboarding page, for Delicious Recipes has been called.
		update_option( 'delicious_recipes_first_time_activation_flag', 'true' );
		
		// Dump Loaded content buffer.
		if ( ob_get_length() ) {
			ob_end_clean();
		}

		$onboard_deps = include_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/build/onboard.asset.php';

		wp_register_script( 'delicious-recipes-onboard', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/build/onboard.js', $onboard_deps['dependencies'], $onboard_deps['version'], true );

		// Add localization vars.
		wp_localize_script(
			'delicious-recipes-onboard',
			'DeliciousRecipes',
			array(
				'adminURL'             => esc_url( admin_url() ),
				'siteURL'              => esc_url( home_url( '/' ) ),
				'pluginUrl'            => esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ),
				'permalinkStructure'   => get_option( 'permalink_structure' ),
				'permalinkOptionsPage' => esc_url( admin_url( 'options-permalink.php' ) )
			)
		);

		wp_enqueue_style( 'toastr', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/toastr/toastr.min.css', array(), '2.1.3', 'all' );

		wp_enqueue_script( 'toastr', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/toastr/toastr.min.js', array( 'jquery' ), '2.1.3', true );

		wp_enqueue_script( 'delicious-recipes-onboard' );

		wp_enqueue_style( 'delicious-recipes-onboard', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/onboard/css/delicious-recipes-onboard.css', array(), DELICIOUS_RECIPES_VERSION, 'all' );

		// Load fresh buffer.
		ob_start();

		/**
		 * Start the actual page content.
		 */
        include plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . 'src/onboard/onboarding.php';
        
		exit;
	}

	/** 
		* Remove Onboard Menu Page 
	*/
	function remove_onboard_menu(){
		remove_menu_page( $this->page_name );
	}

}
$obj = new Delicious_Recipes_Onboard( DELICIOUS_RECIPES_PLUGIN_NAME, DELICIOUS_RECIPES_VERSION );
$obj->init();
