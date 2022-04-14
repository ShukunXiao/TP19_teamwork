<?php
/**
 * Details Block
 *
 * @since   1.2.0
 * @package Delicious_Recipes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Delicious_Dynamic_Details Class.
 */
class Delicious_Dynamic_Details {
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
	 * Block settings.
	 *
	 * @since 1.1.0
	 */
	public static $settings;

	/**
	 * The Constructor.
	 */
	public function __construct() {
        self::$helpers = new Delicious_Recipes_Helpers();
	}

	/**
	 * Registers the details block as a server-side rendered block.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		if ( delicious_recipes_block_is_registered( 'delicious-recipes/dynamic-details' ) ) {
			return;
		}

		$attributes = array(
			'id' => array(
				'type' => 'string',
				'default' => 'dr-block-details'
			),
			'course' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string'
                )
            ),
            'cuisine' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string'
                )
			),
			'method' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string'
                )
			),
			'recipeKey' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string'
                )
			),
            'difficulty' => array(
				'type'    => 'string',
				'default' => 'beginner'
			),
			'difficultyTitle' => array(
				'type' => 'string',
				'selector' => '.difficulty-label',
				'default' => __("Difficulty", "delicious-recipes"),
			),
			'season' => array(
				'type'    => 'string',
				'default' => 'summer'
			),
			'seasonTitle' => array(
				'type' => 'string',
				'selector' => '.season-label',
				'default' => __("Best Season", "delicious-recipes"),
			),
            'keywords' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string'
                )
			),
			'settings' => array(
                'type' => 'array',
                'default' => array(
                    array(
                        'displayCourse' => true,
						'displayCuisine' => true,
						'displayCookingMethod' => true,
						'displayRecipeKey' => true,
                        'displayDifficulty' => true,
                        'displayAuthor' => true,
                        'displayServings' => true,
                        'displayPrepTime' => true,
						'displayCookingTime' => true,
						'displayRestTime' => true,
                        'displayTotalTime' => true,
						'displayCalories' => true,
						'displayBestSeason' => true,
                    )
                ),
                'items' => array(
                    'type' => 'object'
                )
			),
			'details' => array(
			    'type' => 'array',
			    'default' => self::get_details_default(),
			    'items' => array(
			    	'type' => 'object'
			    )
			),
		);

		// Hook server side rendering into render callback
		register_block_type(
			'delicious-recipes/dynamic-details', array(
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

		if ( ! isset($attributes['details']) ) {
			return $content;
		}

		$attributes = self::$helpers->omit( $attributes, array( 'toInsert', 'activeIconSet', 'showModal', 'searchIcon', 'icons' ) );
		// Import variables into the current symbol table from an array
		extract( $attributes );

		// Store variables
		self::$attributes 	= $attributes;
		self::$settings 	= self::$helpers->parse_block_settings( $attributes );

		self::$attributes['difficultyTitle']  = isset( $difficultyTitle ) ? $difficultyTitle : __('Difficulty', 'delicious-recipes');
		self::$attributes['seasonTitle']      = isset( $seasonTitle ) ? $seasonTitle : __('Best Season', 'delicious-recipes');

		$class = 'dr-summary-holder';

		$details = isset( $details ) ? $details : array();
		$details_content = $this->get_details_content( $details );

		$block_content = sprintf(
			'<div id="%1$s" class="%2$s">
				<div class="dr-post-summary">
					%3$s
				</div>
			</div>',
			esc_attr( $id ),
			esc_attr( $class ),
			$details_content
		);

		return $block_content;
	}

	public static function get_details_default() {
		return array(
			array(
		    	'id' 		=> self::$helpers->generateId( "detail-item" ),
		    	'icon' 		=> 'time',
		    	'label' 	=> __( "Prep time", "delicious-recipes" ),
		    	'unit' 		=> __( "minutes", "delicious-recipes" ),
		    	'value'		=> '30'
		    ),
		    array(
		        'id' 		=> self::$helpers->generateId( "detail-item" ),
		        'icon' 		=> 'time',
		        'label' 	=> __( "Cook time", "delicious-recipes" ),
		        'unit' 		=> __( "minutes", "delicious-recipes" ),
		        'value'		=> '40'
			),
			array(
		        'id' 		=> self::$helpers->generateId( "detail-item" ),
		        'icon' 		=> 'time',
		        'label' 	=> __( "Rest time", "delicious-recipes" ),
		        'unit' 		=> __( "minutes", "delicious-recipes" ),
		        'value'		=> '40'
			),
			array(
		        'id' 		=> self::$helpers->generateId( "detail-item" ),
		        'icon' 		=> 'time',
		        'label' 	=> __( "Total time", "delicious-recipes" ),
		        'unit' 		=> __( "minutes", "delicious-recipes" ),
		        'value'		=> '0'
			),
			array(
				'id' 		=> self::$helpers->generateId( "detail-item" ),
				'icon' 		=> 'yield',
				'label' 	=> __( "Servings", "delicious-recipes" ),
				'unit' 		=> __( "servings", "delicious-recipes" ),
				'value'		=> '4'
			),
			array(
		        'id' 		=> self::$helpers->generateId( "detail-item" ),
		        'icon' 		=> 'calories',
		        'label' 	=> __( "Calories", "delicious-recipes" ),
		        'unit' 		=> __( "kcal", "delicious-recipes" ),
		        'value'		=> '300'
		    )
		);
	}

	public static function get_details_content( array $details ) {
		$detail_items = self::get_detail_items( $details );
		$details_class = 'dr-extra-meta';

		if ( !empty($detail_items) ) {
			return sprintf(
				'<div class="%s">%s</div>',
				esc_attr( $details_class ),
				$detail_items
			);
		} else {
			return '';
		}
	}

	public static function get_detail_items( array $details ) {
		$output = '';

		$attributes 	= self::$attributes;
		extract( $attributes );

		$difficulty     = isset( $difficulty ) && self::$settings['displayDifficulty'] ? $difficulty : '';

		if( $difficulty) {
			$svg = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ). 'assets/images/sprite.svg#difficulty"></use></svg>';
			$output .= sprintf(
				'<span class="%1$s"><span class="%2$s">%3$s %4$s:</span><b>%5$s</b></span>',
				'dr-sim-metaa dr-lavel',
				'dr-meta-title',
				$svg,
				$difficultyTitle,
				ucfirst( $difficulty )
			);
		}
		
		foreach ( $details as $index => $detail ) {
			$value    = '';
			$icon_svg = '';
			$icon     = ! empty( $detail['icon'] ) ? $detail['icon'] : '';
			$label    = ! empty( $detail['label'] ) ? $detail['label'] : '';
			$unit     = ! empty( $detail['unit'] ) ? $detail['unit'] : '';

			if( ! empty( $icon ) ) {
				$icon_svg = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ). 'assets/images/sprite.svg#'.$icon.'"></use></svg>';
			}

			if ( ! empty( $detail[ 'value' ] ) ) {
				if ( ! is_array( $detail['value'] ) ) {
					$value = $detail['value'];
				} elseif ( isset( $detail['jsonValue'] ) ) {
					$value = $detail['jsonValue'];
				}
			}
			
			if ( 0 === $index && self::$settings['displayPrepTime'] != '1' ) {
				continue;
			} elseif ( 1 === $index && self::$settings['displayCookingTime'] != '1' ) {
				continue;
			} elseif ( 2 === $index && self::$settings['displayRestTime'] != '1' ) {
				continue;
			} elseif ( 3 === $index && self::$settings['displayTotalTime'] != '1' ) {
				continue;
			} elseif ( 4 === $index && self::$settings['displayServings'] != '1' ) {
				continue;
			} elseif ( 5 === $index && self::$settings['displayCalories'] != '1' ) {
				continue;
			}

			// convert minutes to hours for 'prep time', 'cook time' and 'total time'
			if ( 0 === $index || 1 === $index || 2 === $index || 3 === $index ) {
				if ( ! empty( $detail['value'] ) ) {
					$converts = self::$helpers->convertMinutesToHours( $detail['value'], true );
					if ( ! empty( $converts ) ) {
						$value = $unit = '';
						if ( isset( $converts['hours'] ) ) {
							$value .= $converts['hours']['value'];
							$value .= ' '. $converts['hours']['unit'];
						}
						if ( isset( $converts['minutes'] ) ) {
							$unit .= $converts['minutes']['value'];
							$unit .= ' '. $converts['minutes']['unit'];
						}
					}
				}
			}

			$output .= sprintf(
				'<span class="%1$s"><span class="dr-meta-title">%2$s:</span><b>%3$s</b></span>',
				'dr-sim-metaa',
				$icon_svg . $label ,
				$value .' '. $unit
			);
		}

		$season     = isset( $season ) && self::$settings['displayBestSeason'] ? $season : '';

		if( $season) {
			$svg = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ). 'assets/images/sprite.svg#season"></use></svg>';
			$output .= sprintf(
				'<span class="%1$s"><span class="%2$s">%3$s %4$s:</span><b>%5$s</b></span>',
				'dr-sim-metaa dr-season',
				'dr-meta-title',
				$svg,
				$seasonTitle,
				ucfirst( $season )
			);
		}

		return force_balance_tags( $output );
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