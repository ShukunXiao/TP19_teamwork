<?php
/**
 * Recipe Buttons Block
 *
 * @since   1.2.0
 * @package Delicious_Recipes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Delicious_Recipe_Buttons Class.
 */
class Delicious_Recipe_Buttons {
	/**
	 * The post Object.
	 *
	 * @since 1.0.8
	 */
	private static $recipe;

	/**
	 * Class instance Helpers.
	 *
	 * @var Delicious_Recipes_Helpers
	 * @since 1.2.0
	 */
	private static $helpers;

	/**
	 * Block attributes.
	 *
	 * @since 1.0.8
	 */
	public static $attributes;

	/**
	 * Block settings.
	 *
	 * @since 1.0.8
	 */
	public static $settings;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		self::$helpers = new Delicious_Recipes_Helpers();
	}

	/**
	 * Registers the recipe buttons block as a server-side rendered block.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		if ( delicious_recipes_block_is_registered( 'delicious-recipes/block-recipe-buttons' ) ) {
			return;
		}

		$attributes = array(
			'id' => array(
			    'type' => 'string',
			    'default' => 'dr-recipe-buttons'
			),
			'jumptorecipeTitle' => array(
				'type' => 'string',
				'selector' => '.jump-to-recipe-label',
				'default' => __("Jump to Recipe", "delicious-recipes"),
			),
			'jumptovideoTitle' => array(
				'type' => 'string',
				'selector' => '.jump-to-video-label',
				'default' => __("Jump to Video", "delicious-recipes"),
			),
			'printrecipeTitle' => array(
				'type' => 'string',
				'selector' => '.print-recipe-label',
				'default' => __("Print Recipe", "delicious-recipes"),
			),
			'settings' => array(
                'type'      => 'array',
                'default' => array(
                    array(
						'jump_to_recipe_btn' => true,
						'jump_to_video_btn'  => true,
						'print_recipe_btn'   => true,
					)
				),
				'items' => array(
					'type' => 'object'
				)
			)
		);

		// Hook server side rendering into render callback
		register_block_type(
			'delicious-recipes/block-recipe-buttons', array(
				'attributes' => $attributes,
				'render_callback' => array( $this, 'render' ),
		) );
	}

	/**
	 * Renders the block.
	 *
	 * @param array  $attributes The attributes of the block.
	 * @param string $content    The HTML content of the block.
	 *
	 * @return string The block preceded by its JSON-LD script.
	 */
	public function render( $attributes, $content ) {

		if ( ! is_array( $attributes ) || ! is_singular() ) {
			return $content;
		}

		$attributes = self::$helpers->omit( $attributes, array() );
		// Import variables into the current symbol table from an array
		extract( $attributes );

		self::$recipe     = get_post();
		self::$attributes = $attributes;
		self::$settings   = self::$helpers->parse_recipe_buttons_block_settings( $attributes );

		self::$attributes['jumptorecipeTitle']      = isset( $jumptorecipeTitle ) ? $jumptorecipeTitle : __('Jump to Recipe', 'delicious-recipes');
		self::$attributes['jumptovideoTitle']      = isset( $jumptovideoTitle ) ? $jumptovideoTitle : __('Jump to Video', 'delicious-recipes');
		self::$attributes['printrecipeTitle']      = isset( $printrecipeTitle ) ? $printrecipeTitle : __('Print Recipe', 'delicious-recipes');

		$class          = implode( ' ', array( "dr-buttons", "dr-recipe-buttons-block" ) );
		$recipe_card_id = "dr-dynamic-recipe-card";

		$jtrecipe_btn_content = self::$settings['jump_to_recipe_btn'] ? self::get_jump_to_recipe_button( $recipe_card_id, array( 'title' => self::$attributes['jumptorecipeTitle'] ) ) : '';
		$jtvideo_btn_content  = self::$settings['jump_to_video_btn'] ? self::get_jump_to_video_button( $id, array( 'title' => self::$attributes['jumptovideoTitle'] ) ) : '';
		$print_btn_content    = self::$settings['print_recipe_btn'] ? self::get_print_button( $recipe_card_id, array( 'title' => self::$attributes['printrecipeTitle'] ) ) : '';

		$block_content = sprintf(
			'<div class="%1$s">
				%2$s
				%3$s
				%4$s
			</div>',
			esc_attr( $class ),
			$jtrecipe_btn_content,
			$jtvideo_btn_content,
			$print_btn_content
		);

		return $block_content;
	}

	/**
	 * Get HTML for jump to recipe button
	 * 
	 * @return string
	 */
	public static function get_jump_to_recipe_button( $content_id, $attributes = array() ) {
		if ( empty( $content_id ) )
			return '';

		$jtrecipeTitle = isset( $attributes['title'] ) ? $attributes['title'] : __( "Jump to Recipe", "delicious-recipes" );

		$output = sprintf(
			'<a href="#%s" class="dr-btn-link dr-btn1 dr-smooth-scroll">
				%s 
				<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ). 'assets/images/sprite.svg#go-to"></use></svg>
			</a>',
			esc_attr( $content_id ),
			$jtrecipeTitle
		);

		return $output;
	}

	/**
	 * Get HTML for jump to video button
	 * 
	 * @return string
	 */
	public static function get_jump_to_video_button( $content_id, $attributes = array() ) {
		if ( empty( $content_id ) )
			return '';

		$jtvideoTitle = isset( $attributes['title'] ) ? $attributes['title'] : __( "Jump to Video", "delicious-recipes" );

		$output = sprintf(
			'<a href="#dr-video-gallery" class="dr-btn-link dr-btn1 dr-smooth-scroll">
				<i class="fas fa-play"></i>
				%s 
			</a>',
			$jtvideoTitle
		);

		return $output;
	}

	/**
	 * Get HTML for print button
	 * 
	 * @return string
	 */
	public static function get_print_button( $content_id, $attributes = array() ) {
		if ( empty( $content_id ) )
			return '';

		$printTitle = isset( $attributes['title'] ) ? $attributes['title'] : __( "Print Recipe", "delicious-recipes" );

		if ( self::$recipe ) {
			$attributes = array_merge( $attributes, array( 'data-recipe-id' => self::$recipe->ID ) );
		}

		$atts = self::$helpers->render_attributes( $attributes );

		$output = sprintf(
			'<a class="dr-print-trigger dr-btn-link dr-btn2" href="#%s" %s>
				<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ). 'assets/images/sprite.svg#print"></use></svg>
				%s
			</a>',
			esc_attr( $content_id ),
			$atts,
			$printTitle
		);

		return $output;
	}
}