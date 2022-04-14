<?php
/**
 * Delicious Recipes Widgets.
 * 
 * @package Delicious_Recipes
 * @since 1.0.0
 */
namespace WP_Delicious;

defined( 'ABSPATH' ) || exit;

// Include widget classes.
require_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/widgets/featured.php';
require_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/widgets/popular.php';
require_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/widgets/recent.php';
require_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/widgets/categories.php';
require_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/widgets/popular-tags.php';
require_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/widgets/recipe-keys.php';


/**
 * Widgets handler.
 */
class Delicious_Recipes_Widgets {

	public function __construct() {
		add_action( 'widgets_init', array($this, 'register_widgets') );
	}

	/**
	 * Init widgets.
	 */
	public function register_widgets() {
		$widgets = apply_filters( 'wp_delicious_recipe_widgets', array(
			'Delicious_Featured_Recipes_Widget',
			'Delicious_Popular_Recipes_Widget',
			'Delicious_Recent_Recipes_Widget',
			'Delicious_Recipe_Categories_Widget',
			'Delicious_Popular_Tags_Widget',
			'Delicious_Recipe_Keys_Widget'
		) );

		foreach ( $widgets as $widget ) {
			register_widget( $widget );
		}
    }
}