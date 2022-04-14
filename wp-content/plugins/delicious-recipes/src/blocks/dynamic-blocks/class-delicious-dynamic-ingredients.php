<?php
/**
 * Ingredients Block
 *
 * @since   1.2.0
 * @package Delicious_Recipes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Delicious_Dynamic_Ingredients Class.
 */
class Delicious_Dynamic_Ingredients {
	/**
	 * Class instance Helpers.
	 *
	 * @var Delicious_Recipes_Helpers
	 * @since 1.0.3
	 */
	public static $helpers;

	/**
	 * Block attributes.
	 *
	 * @since 1.1.0
	 */
	public static $attributes;

	/**
	 * The Constructor.
	 */
	public function __construct() {
        self::$helpers = new Delicious_Recipes_Helpers();
	}

	/**
	 * Registers the ingredients block as a server-side rendered block.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		if ( delicious_recipes_block_is_registered( 'delicious-recipes/dynamic-ingredients' ) ) {
			return;
		}

		$attributes = array(
			'id' => array(
				'type' => 'string',
				'default' => 'dr-block-ingredients'
			),
			'ingredientsTitle' => array(
                'type' => 'string',
                'selector' => '.ingredients-title',
                'default' => 'Ingredients',
            ),
            'jsoningredientsTitle' => array(
                'type' => 'string',
            ),
            'ingredients' => array(
                'type' => 'array',
                'default' => self::get_ingredients_default(),
                'items' => array(
                    'type' => 'object'
                )
            ),
		);

		// Hook server side rendering into render callback
		register_block_type(
			'delicious-recipes/dynamic-ingredients', array(
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
		if ( ! is_array( $attributes ) ) {
			return $content;
		}

		if ( is_singular() ) {
			add_filter( 'the_content', array( $this, 'filter_the_content' ) );
		}

		if ( ! isset($attributes['ingredients']) ) {
			return $content;
		}

		$attributes = self::$helpers->omit( $attributes, array() );
		// Import variables into the current symbol table from an array
		extract( $attributes );

		// Store variables
		self::$attributes 	= $attributes;

		$class = 'dr-summary-holder';

		$ingredients = isset( $ingredients ) ? $ingredients : array();
		$ingredients_content = self::get_ingredients_content( $ingredients );

		$block_content = sprintf(
			'<div id="%1$s" class="%2$s">
				%3$s
			</div>',
			esc_attr( $id ),
			esc_attr( $class ),
			$ingredients_content
		);

		return $block_content;
	}

	public static function get_ingredients_default() {
		return array(
			array(
				'id' 		=> self::$helpers->generateId( "ingredient-item" ), 
				'name' 		=> array(), 
			),
			array(
				'id' 		=> self::$helpers->generateId( "ingredient-item" ), 
				'name' 		=> array(), 
			),
			array(
				'id' 		=> self::$helpers->generateId( "ingredient-item" ), 
				'name' 		=> array(), 
			),
			array(
				'id' 		=> self::$helpers->generateId( "ingredient-item" ), 
				'name' 		=> array(), 
			)
		);
	}

	public static function get_ingredients_content( array $ingredients ) {
		$ingredient_items = self::get_ingredient_items( $ingredients );

		$listClassNames = implode( ' ', array( 'ingredients-list', 'dr-unordered-list' ) );

		return sprintf(
			'<div class="dr-ingredients-list"><div class="dr-ingrd-title-wrap"><h3 class="ingredients-title dr-title">%s</h3></div><ul class="%s">%s</ul></div>',
			self::$attributes['ingredientsTitle'],
			$listClassNames,
			$ingredient_items
		);
	}

	public static function get_ingredient_items( array $ingredients ) {
		$output = '';

		foreach ( $ingredients as $index => $ingredient ) {
			$name = '';
			$isGroup = isset( $ingredient['isGroup'] ) ? $ingredient['isGroup'] : false;
			$ingredient_id = isset( $ingredient['id'] ) ? 'dr-ing-' . $ingredient['id'] : '';

			if ( !$isGroup ) {
				if ( ! empty( $ingredient[ 'name' ] ) ) {
					$name = sprintf( '<span class="dr-ingredient-name">%s</span>', self::wrap_ingredient_name( $ingredient['name'] ) );

					$name = sprintf(
						'<input type="checkbox" id="%s"><label for ="%s">%s</label>',
						esc_attr( $ingredient_id ),
						esc_attr( $ingredient_id ),
						$name
					);
					$output .= sprintf(
						'<li>%s</li>',
						$name
					);
				}
			} else {
				if ( ! empty( $ingredient[ 'name' ] ) ) {
					$name = self::wrap_ingredient_name( $ingredient['name'] );
					$output .= sprintf(
						'<h4 class="dr-title">%s</h4>',
						$name
					);
				}
			}
		}

		return force_balance_tags( $output );
	}

	public static function wrap_ingredient_name( $nodes, $type = '' ) {
		$attributes = self::$attributes;

		if ( ! is_array( $nodes ) ) {
			return $nodes;
		}

		$output = '';
		foreach ( $nodes as $node ) {
			if ( ! is_array( $node ) ) {
				$output .= $node;
			} else {
				$type = isset( $node['type'] ) ? $node['type'] : null;
				$children = isset( $node['props']['children'] ) ? $node['props']['children'] : null;

				$start_tag = $type ? "<$type>" : "";
				$end_tag = $type ? "</$type>" : "";

				if ( 'a' === $type ) {
					$rel 		= isset( $node['props']['rel'] ) ? $node['props']['rel'] : '';
					$aria_label = isset( $node['props']['aria-label'] ) ? $node['props']['aria-label'] : '';
					$href 		= isset( $node['props']['href'] ) ? $node['props']['href'] : '#';
					$target 	= isset( $node['props']['target'] ) ? $node['props']['target'] : '_blank';

					$start_tag = sprintf( '<%s rel="%s" aria-label="%s" href="%s" target="%s">', $type, $rel, $aria_label, $href, $target );
				}
				elseif ( 'br' === $type ) {
					$end_tag = "";
				}

				$output .= $start_tag . self::wrap_ingredient_name( $children, $type ) . $end_tag;
			}
		}

		return $output;
	}

	/**
	 * Filter content when rendering recipe card block
	 * Add snippets at the top of post content
	 *
	 * @since 1.2.0
	 * @param string $content Main post content
	 * @return string HTML of post content
	 */
	public function filter_the_content( $content ) {
		if ( ! in_the_loop() ) {
			return $content;
		}

		$output = '';

		return $output . $content;
	}
}