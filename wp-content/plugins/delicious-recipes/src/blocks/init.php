<?php
/**
 * Init Gutenberg Blocks
 *
 * @package Delicious_Recipes
 */
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
add_action( 'enqueue_block_assets', 'delicious_recipes_gb_block_assets' );
/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function delicious_recipes_gb_block_assets() { // phpcs:ignore

	if( ! is_admin() ) {

		$should_enqueue =
			has_block( 'delicious-recipes/dynamic-recipe-card' ) ||
			has_block( 'delicious-recipes/block-recipe-buttons' );

		if ( $should_enqueue ) {

			// Scripts.
			wp_enqueue_script(
				'delicious-recipes-block-print-js',
				plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/public/js/block-print.js',
				array( 'jquery' ),
				DELICIOUS_RECIPES_VERSION,
				true
			);

			/**
			 * Localize script data.
			 */
			wp_localize_script(
				'delicious-recipes-block-print-js',
				'delrecipesRecipeCard',
				array(
					'homeURL' => delicious_recipes_get_home_url(),
					'permalinks' => get_option( 'permalink_structure' ),
				)
			);
		}
	}
}


add_action( 'enqueue_block_editor_assets', 'delicious_recipes_gb_editor_assets' );
/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function delicious_recipes_gb_editor_assets() { // phpcs:ignore
	$global_settings = delicious_recipes_get_global_settings();
	$blocks_deps = include_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/build/blocks.asset.php';
	// Scripts.
	wp_enqueue_script(
		'delicious-recipes-gb-block-js', // Handle.
		plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . 'assets/admin/build/blocks.js',
		$blocks_deps['dependencies'],
		$blocks_deps['version'],
		true
	);
	wp_localize_script( 'delicious-recipes-gb-block-js', 'delrcp', array(
		'setting_options'     => $global_settings,
		'ajaxURL'             => admin_url( 'admin-ajax.php' ),
		'pluginURL'           => DELICIOUS_RECIPES_PLUGIN_URL,
		'nutritionFactsLabel' => Delicious_Dynamic_Nutrition::$labels
	) );

	// Styles.
	wp_enqueue_style(
		'delicious-recipes-gb-style-css', // Handle.
		plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . 'assets/blocks/blocks.css', // Block style CSS.
		array( 'wp-editor' ), // Dependency to include the CSS after it.
		DELICIOUS_RECIPES_VERSION // Version: File modification time.
	);

	wp_enqueue_style('delicious-recipe-block-styles', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/build/blockCSS.css');
}

global $wp_version;
// Compatibility for WordPress 5.8
if ( version_compare( $wp_version, '5.8', '<' ) ) {
	add_filter( 'block_categories', 'delicious_recipes_block_categories', 10, 2 );
} else {
	add_filter( 'block_categories_all', 'delicious_recipes_block_categories', 10, 2 );
}

/**
 * Register new Block Category
 */
function delicious_recipes_block_categories( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'delicious-recipes',
				'title' => __( 'Delicious Recipes', 'delicious-recipes' ),
				// 'icon'  => '',
			),
		)
	);
}

// List by Trip Types Block.
require_once dirname( __FILE__ ) . '/handpicked-recipes/block.php';
require_once dirname( __FILE__ ) . '/recipe-by-tax/block.php';
require_once dirname( __FILE__ ) . '/recipe-card/block.php';
require_once dirname( __FILE__ ) . '/dynamic-blocks/dynamic-blocks-init.php';
