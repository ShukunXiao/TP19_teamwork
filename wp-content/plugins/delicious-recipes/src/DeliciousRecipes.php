<?php
/**
 * Main DeliciousRecipes class
 *
 * @package DELICIOUS_RECIPES
 */

namespace WP_Delicious;

defined( 'ABSPATH' ) || exit;

/**
 * Main DeliciousRecipes Cass.
 *
 * @class DeliciousRecipes
 */
final class DeliciousRecipes {
	/**
	 * DeliciousRecipes verison.
	 *
	 * @var string
	 */
	public $version = '1.3.7';

	/**
	 * The single instance of the class.
	 *
	 * @var DeliciousRecipes
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main DeliciousRecipes Instance.
	 *
	 * Ensures only one instance of DeliciousRecipes is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see DEL_RECIPE()
	 * @return DeliciousRecipes - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * DeliciousRecipes Constructor.
	 */
	public function __construct() {

        $this->define_constants();
		$this->init_hooks();
		$this->includes();

		// Load class instances.
		$this->global_settings   = new GlobalSettings();
		$this->admin_settings    = new DeliciousAdmin();

		if ( $this->is_request('frontend') ) {
			// Public instances.
			$this->public = new DeliciousPublic();
			$this->seo    = new Delicious_SEO();
		}

		if ( $this->is_request('admin') ) {
			// Updater instances.
			$this->updater = new \Delicious_Recipes_EDD_Handler();
		}

		$this->recipe  = new Delicious_Recipes_Recipe();
		$this->widgets = new Delicious_Recipes_Widgets();
		$this->session = new \Delicious_Recipes_Session();
		$this->notices = new \Delicious_Recipes_Notices();

	}

	/**
	 * Activation hook for Delicious recipes plugin.
	 *
	 * @return void
	 */
	public function activate() {
		require_once plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-activator.php';
		Delicious_Recipes_Activator::activate();
	}

	/**
	 * When WP has loaded all plugins, trigger the 'WP_Delicious_loaded; hook.
	 *
	 * This ensures 'WP_Delicious_loaded' is called only after all the other plugins
	 * are loaded, to avoid issues caused by plugin directory naming changing
	 * the load order.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function on_plugins_loaded() {
		do_action( 'WP_Delicious_loaded' );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ) );
		register_activation_hook( DELICIOUS_RECIPES_PLUGIN_FILE, array( $this, 'activate' ) );
	}

	/**
	 * Define WTE_FORM_EDITOR Constants.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function define_constants() {
		$this->define( 'DELICIOUS_RECIPES_PLUGIN_NAME', 'delicious-recipes' );
		$this->define( 'DELICIOUS_RECIPES_ABSPATH', dirname( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/' );
		$this->define( 'DELICIOUS_RECIPES_PLUGIN_BASENAME', plugin_basename( DELICIOUS_RECIPES_PLUGIN_FILE ) );
		$this->define( 'DELICIOUS_RECIPES_VERSION', $this->version );
		$this->define( 'DELICIOUS_RECIPES_TEMPLATE_DEBUG_MODE', false );
		$this->define( 'DELICIOUS_RECIPES_PLUGIN_URL', $this->plugin_url() );
		$this->define( 'DELICIOUS_RECIPE_POST_TYPE', 'recipe' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name       Constant name.
	 * @param string|bool $value      Constant value.
	 * @return void
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Init DeliciousRecipes when WordPress initializes.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		// Before init action.
		do_action( 'before_delicious_recipes_init' );

		// Set up localization.
		$this->load_plugin_textdomain();

	}

	/**
	 * Include required files.
	 *
	 * @return void
	 */
	public function includes() {
		if ( $this->meets_requirements() ) {

			require plugin_dir_path( __FILE__ ) . '/api/class-delicious-recipes-api-core.php';
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-svg.php';
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-recipe.php';
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-likes-wishlists.php';

			if ( $this->is_request( 'frontend' ) ) {
				require plugin_dir_path( __FILE__ ) . '/public/DeliciousSEO.php';
			}
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-widgets.php';
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-import.php';
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-helpers.php';
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-structured-data-helpers.php';
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-onboard.php';
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-whats-new.php';

			require plugin_dir_path( __FILE__ ) . '/blocks/init.php';

			include plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-notices.php';
			include plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-session.php';

			if ( $this->is_request( 'admin' ) ) {
				require plugin_dir_path( __FILE__ ) . '/updater/class-delicious-recipes-edd.php';
			}

		}
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/delicious-recipes/delicious-recipes-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/delicious-recipes-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'delicious-recipes',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	/**
	 * Get the plugin URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', DELICIOUS_RECIPES_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Get the template path.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'wtefe_template_path', '/delicious-recipes/' );
	}

	/**
	 * Output error message and disable plugin if requirements are not met.
	 *
	 * This fires on admin_notices.
	 *
	 * @since 1.0.0
	 */
	public function maybe_disable_plugin() {

		if ( ! $this->meets_requirements() ) {
			// Deactivate our plugin
			deactivate_plugins( DELICIOUS_RECIPES_PLUGIN_BASENAME );
		}
	}

	/**
	 * Check if all plugin requirements are met.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if requirements are met, otherwise false.
	 */
	private function meets_requirements() {
		return true;
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

}
