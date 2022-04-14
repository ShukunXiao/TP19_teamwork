<?php
/**
 * API core class
 *
 * @package Delicious_Recipes/API
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Delicious_Recipes_API_Core' ) ) :
	/**
	 * Delicious recipes API Core.
	 */
	class Delicious_Recipes_API_Core {

        /**
         * Constructor
         */
        public function __construct() {
            $this->init();
        }

        /**
         * Abspath
         *
         * @var [type]
         */
		protected static $abspath;

		/**
		 * The single instance of the class.
		 *
		 * @var Delicious_Recipes_API
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Main Delicious_Recipes_API_Core Instance.
		 * Ensures only one instance of Delicious_Recipes_API_Core is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see Delicious_Recipes_API_Core()
		 * @return Delicious_Recipes_API_Core - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Init core.
		 *
		 * @param Array $params Core class init paramerters.
		 */
		public static function init() {
			self::$abspath     = plugin_dir_path( __FILE__ );

			include_once self::$abspath . 'inc/class-delicious-recipes-rest-authentication.php';
			include_once self::$abspath . 'inc/endpoints/class-delicious-recipes-rest-auth-controller.php';
			include_once self::$abspath . 'inc/endpoints/class-delicious-recipes-rest-controller.php';
			include_once self::$abspath . 'inc/endpoints/class-delicious-recipes-rest-recipe-controller.php';
			include_once self::$abspath . 'inc/endpoints/class-delicious-recipes-rest-global-settings-controller.php';
		}
    }
endif;

// Init core API.
/**
 * Return the main instance of Delicious_Recipes_API_Core.
 *
 * @since 1.0.0
 * @return Delicious_Recipes_API_Core
 */
function delicious_recipes_run_api_core() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return Delicious_Recipes_API_Core::instance();
}

// Run.
delicious_recipes_run_api_core();
