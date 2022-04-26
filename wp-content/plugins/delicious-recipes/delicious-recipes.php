<?php
/**
 * Plugin Name:     Delicious Recipes
 * Plugin URI:      https://wpdelicious.com/docs/
 * Description:     A powerful recipe plugin to create and display recipes for bloggers. SEO optimized and Schema-friendly to rank recipes higher on search engines.
 * Author:          WP Delicious
 * Author URI:      https://wpdelicious.com
 * Text Domain:     delicious-recipes
 * Domain Path:     /languages
 * Version:         1.3.7
 * Requires at least: 5.1
 * Requires PHP: 5.6
 * 
 * @package         Delicious_Recipes
 */

use WP_Delicious\DeliciousRecipes;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'delicious_recipes_fs' ) ) {
    // Create a helper function for easy SDK access.
    function delicious_recipes_fs() {
        global $delicious_recipes_fs;

        if ( ! isset( $delicious_recipes_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/src/lib/freemius/start.php';

            $first_time_activation_flag = get_option( 'delicious_recipes_first_time_activation_flag', false );

            $slug = $first_time_activation_flag == false ? 'delicious-recipes-onboard' : 'delicious_recipes_global_settings';

            $delicious_recipes_fs = fs_dynamic_init( array(
                'id'                  => '7284',
                'slug'                => 'delicious-recipes',
                'type'                => 'plugin',
                'public_key'          => 'pk_85490c48c376203d6194ac3de232e',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'               => array(
                    'slug'           => $slug,
                    'account'        => false,
                    'contact'        => false,
                    'support'        => false,
                    'parent'         => array(
                        'slug'    => 'edit.php?post_type=recipe',
                    ),
                ),
            ) );
        }

        return $delicious_recipes_fs;
    }

    // Init Freemius.
    delicious_recipes_fs();
	// Signal that parent SDK was initiated.
    do_action( '_loaded' );
    // Signal that SDK was initiated.
    do_action( 'delicious_recipes_fs_loaded' );
}

// Include the autoloader.
require_once __DIR__ . '/vendor/autoload.php';

if ( ! defined( 'DELICIOUS_RECIPES_PLUGIN_FILE' ) ) {
	define( 'DELICIOUS_RECIPES_PLUGIN_FILE', __FILE__ );
}

/**
 * Return the main instance of DeliciousRecipes.
 *
 * @since 1.0.0
 * @return DeliciousRecipes
 */
function DEL_RECIPE() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return DeliciousRecipes::instance();
}

// Global for backwards compatibility.
$GLOBALS['DEL_RECP'] = DEL_RECIPE();
