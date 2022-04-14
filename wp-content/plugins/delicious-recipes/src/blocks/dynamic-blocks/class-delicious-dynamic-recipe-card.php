<?php
/**
 * Dynamic Recipe Card Block
 *
 * @since   1.0.8
 * @package Delicious_Recipes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Delicious_Dynamic_Recipe_Card Class.
 */
class Delicious_Dynamic_Recipe_Card {
	/**
	 * The post Object.
	 *
	 * @since 1.0.8
	 */
	private static $recipe;

	/**
	 * Class instance Structured Data Helpers.
	 *
	 * @var Delicious_Recipes_Structured_Data_Helpers
	 * @since 1.0.8
	 */
	public static $structured_data_helpers;

	/**
	 * Class instance Helpers.
	 *
	 * @var Delicious_Recipes_Helpers
	 * @since 1.0.8
	 */
	public static $helpers;

	/**
	 * Recipe Block ID.
	 *
	 * @since 1.2.0
	 */
	public static $recipeBlockID;

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
		self::$structured_data_helpers = new Delicious_Recipes_Structured_Data_Helpers();
		self::$helpers = new Delicious_Recipes_Helpers();
	}

	/**
	 * Registers the recipe-card block as a server-side rendered block.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		if ( delicious_recipes_block_is_registered( 'delicious-recipes/dynamic-recipe-card' ) ) {
			return;
		}

		$attributes = array(
            'id' => array(
                'type'    => 'string',
                'default' => 'dr-dynamic-recipe-card'
            ),
            'image' => array(
                'type' => 'object',
            ),
            'hasImage' => array(
                'type'    => 'boolean',
                'default' => false
            ),
            'video' => array(
                'type' => 'object',
            ),
            'hasVideo' => array(
                'type'    => 'boolean',
                'default' => false
            ),
            'videoTitle' => array(
                'type'     => 'string',
                'selector' => '.video-title',
                'default'  => 'Video',
            ),
            'hasInstance' => array(
                'type'    => 'boolean',
                'default' => false
            ),
            'recipeTitle' => array(
                'type'     => 'string',
                'selector' => '.recipe-card-title',
            ),
            'summary' => array(
                'type'     => 'string',
                'selector' => '.recipe-card-summary',
                'default'  => ''
			),
			'summaryTitle' => array(
                'type'     => 'string',
                'selector' => '.summary-title',
                'default'  => 'Description',
            ),
            'jsonSummary' => array(
                'type' => 'string',
            ),
            'course' => array(
                'type'  => 'array',
                'items' => array(
                    'type' => 'string'
                )
            ),
            'cuisine' => array(
                'type'  => 'array',
                'items' => array(
                    'type' => 'string'
                )
			),
			'method' => array(
                'type'  => 'array',
                'items' => array(
                    'type' => 'string'
                )
			),
			'recipeKey' => array(
                'type'  => 'array',
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
                'type'  => 'array',
                'items' => array(
                    'type' => 'string'
                )
            ),
            'settings' => array(
                'type'    => 'array',
                'default' => array(
                    array(
                        'print_btn'            => true,
                        'pin_btn'              => true,
						'custom_author_name'   => '',
                        'displayCourse'        => true,
                        'displayCuisine'       => true,
                        'displayCookingMethod' => true,
                        'displayRecipeKey'     => true,
                        'displayDifficulty'    => true,
                        'displayAuthor'        => true,
                        'displayServings'      => true,
                        'displayPrepTime'      => true,
                        'displayCookingTime'   => true,
                        'displayRestTime'      => true,
                        'displayTotalTime'     => true,
                        'displayCalories'      => true,
                        'displayBestSeason'    => true,
                    )
                ),
                'items' => array(
                    'type' => 'object'
                )
			),
			'details' => array(
				'type'    => 'array',
				'default' => self::get_details_default(),
				'items'   => array(
					'type' => 'object'
				)
			),
            'ingredientsTitle' => array(
                'type'     => 'string',
                'selector' => '.ingredients-title',
                'default'  => 'Ingredients',
            ),
            'jsonIngredientsTitle' => array(
                'type' => 'string',
            ),
            'ingredients' => array(
                'type'    => 'array',
                'default' => self::get_ingredients_default(),
                'items'   => array(
                    'type' => 'object'
                )
            ),
            'directionsTitle' => array(
                'type'     => 'string',
                'selector' => '.directions-title',
                'default'  => 'Instructions',
            ),
            'jsonDirectionsTitle' => array(
                'type' => 'string',
            ),
            'steps' => array(
                'type'    => 'array',
                'default' => self::get_steps_default(),
                'items'   => array(
                    'type' => 'object'
                )
            ),
            'notesTitle' => array(
                'type'     => 'string',
                'selector' => '.notes-title',
                'default'  => 'Notes',
            ),
            'notes' => array(
                'type'     => 'string',
                'selector' => '.recipe-card-notes-list',
                'default'  => ''
            )
        );

		// Hook server side rendering into render callback
		register_block_type(
			'delicious-recipes/dynamic-recipe-card', array(
				'attributes'      => $attributes,
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

		$attributes = self::$helpers->omit( $attributes, array( 'toInsert', 'activeIconSet', 'showModal', 'searchIcon', 'icons' ) );
		// Import variables into the current symbol table from an array
		extract( $attributes );

		// Recipe post variables
		self::$recipe 			= get_post();
		$recipe_ID 				= get_the_ID( self::$recipe );
		$recipe_title 			= get_the_title( self::$recipe );
		$recipe_thumbnail_url 	= get_the_post_thumbnail_url( self::$recipe );
		$recipe_thumbnail_id 	= get_post_thumbnail_id( self::$recipe );
		$recipe_permalink 		= get_the_permalink( self::$recipe );
		$recipe_author_name 	= get_the_author_meta( 'display_name', self::$recipe->post_author );
		$attachment_id 			= isset( $image['id'] ) ? $image['id'] : $recipe_thumbnail_id;
		
		// Variables from attributes
		// add default value if not exists
		$recipeTitle 	= isset( $recipeTitle ) ? $recipeTitle : '';
		$summary 		= isset( $summary ) ? $summary : '';
		$className 		= isset( $className ) ? $className : '';
		$hasImage 		= isset( $hasImage ) ? $hasImage : false;
		$course 		= isset( $course ) ? $course : array();
		$cuisine 		= isset( $cuisine ) ? $cuisine : array();
		$difficulty 	= isset( $difficulty ) ? $difficulty : array();
		$keywords 		= isset( $keywords ) ? $keywords : array();
		$details 		= isset( $details ) ? $details : array();
		$ingredients 	= isset( $ingredients ) ? $ingredients : array();
		$steps 			= isset( $steps ) ? $steps : array();
		
		// Store variables
		self::$recipeBlockID = esc_attr( $id );
		self::$attributes    = $attributes;
		self::$settings      = self::$helpers->parse_block_settings( $attributes );
		self::$attributes['summaryTitle']     = isset( $summaryTitle ) ? $summaryTitle : __('Description', 'delicious-recipes');
		self::$attributes['ingredientsTitle'] = isset( $ingredientsTitle ) ? $ingredientsTitle : __('Ingredients', 'delicious-recipes');
		self::$attributes['directionsTitle']  = isset( $directionsTitle ) ? $directionsTitle : __('Instructions', 'delicious-recipes');
		self::$attributes['videoTitle']       = isset( $videoTitle ) ? $videoTitle : __('Video', 'delicious-recipes');
		self::$attributes['difficultyTitle']  = isset( $difficultyTitle ) ? $difficultyTitle : __('Difficulty', 'delicious-recipes');
		self::$attributes['seasonTitle']      = isset( $seasonTitle ) ? $seasonTitle : __('Best Season', 'delicious-recipes');
		
		$class = 'dr-summary-holder wp-block-delicious-recipes-block-recipe-card';
		$class .= $hasImage && isset($image['url']) ? '' : ' recipe-card-noimage';
		$RecipeCardClassName = implode( ' ', array( $class, $className ) );

		$custom_author_name = $recipe_author_name;
		if ( ! empty( self::$settings['custom_author_name'] ) ) {
			$custom_author_name = self::$settings['custom_author_name'];
		}
		
		$styles = '';
		$printStyles = self::$helpers->render_styles_attributes( $styles );
		$pin_description = strip_tags($recipeTitle);

		$recipe_card_image = '';

		if ( $hasImage && isset( $image['url'] ) ) {
			$img_id = $image['id'];
			$src 	= $image['url'];
			$alt 	= ( $recipeTitle ? strip_tags( $recipeTitle ) : strip_tags( $recipe_title ) );
			$sizes 	= isset( $image['sizes'] ) ? $image['sizes'] : array();
			$size 	= self::get_recipe_image_size( $sizes, $src );
			$img_class = ' delicious-recipes-card-image';

			// Check if attachment image is from imported content
			// in this case we don't have attachment in our upload directory
			$upl_dir = wp_upload_dir();
			$findpos = strpos( $src, $upl_dir['baseurl'] );

			if ( $findpos === false ) {
				$attachment = sprintf(
					'<img src="%s" alt="%s" class="%s"/>',
					$src,
					$alt,
					trim( $img_class )
				);
			}
			else {
				$attachment = wp_get_attachment_image(
					$img_id,
					$size,
					false,
					array(
						'alt' => $alt,
                        'id' => $img_id,
                        'class' => trim( $img_class )
					)
				);
			}

			$recipe_card_image = '<div class="dr-image">
				<figure>
					'. $attachment .'
					<figcaption>
						'.
							( self::$settings['pin_btn'] ? self::get_pinterest_button( $image, $recipe_permalink, $pin_description ) : '' ).
							( self::$settings['print_btn'] ? self::get_print_button( $id, array( 'title' => __( "Print", "delicious-recipes" ), 'style' => $printStyles ) ) : '' )
						.'
					</figcaption>
				</figure>
			</div>';
		}
		elseif ( ! $hasImage && ! empty( $recipe_thumbnail_url ) ) {
			$img_id = $recipe_thumbnail_id;
			$src 	= $recipe_thumbnail_url;
			$alt 	= ( $recipeTitle ? strip_tags( $recipeTitle ) : strip_tags( $recipe_title ) );
			$sizes 	= isset( $image['sizes'] ) ? $image['sizes'] : array();
			$size 	= self::get_recipe_image_size( $sizes, $src );
			$img_class = ' delicious-recipes-card-image';

			// Check if attachment image is from imported content
			// in this case we don't have attachment in our upload directory
			$upl_dir = wp_upload_dir();
			$findpos = strpos( $src, $upl_dir['baseurl'] );

			if ( $findpos === false ) {
				$attachment = sprintf(
					'<img src="%s" alt="%s" class="%s"/>',
					$src,
					$alt,
					trim( $img_class )
				);
			}
			else {
				$attachment = wp_get_attachment_image(
					$img_id,
					$size,
					false,
					array(
						'alt'   => $alt,
						'id'    => $img_id,
						'class' => trim( $img_class )
					)
				);
			}

			$recipe_card_image = '<div class="dr-image">
				<figure>
					'. sprintf( '<img id="%s" src="%s" alt="%s" class="%s"/>', $img_id, $src, $alt, trim($img_class) ) .'
					<figcaption>
						'.
							( self::$settings['pin_btn'] ? self::get_pinterest_button( array( 'url' => $recipe_thumbnail_url ), $recipe_permalink, $pin_description ) : '' ).
							( self::$settings['print_btn'] ? self::get_print_button( $id, array( 'title' => __( "Print", "delicious-recipes" ), 'style' => $printStyles ) ) : '' )
						.'
					</figcaption>
				</figure>
			</div>';
		} 
		else {
			$fallback_svg = delicious_recipes_get_fallback_svg( 'recipe-feat-gallery', true );
			$recipe_card_image = '<div class="dr-image">
				<figure>
					'. $fallback_svg .'
					<figcaption>
						'.
							( self::$settings['print_btn'] ? self::get_print_button( $id, array( 'title' => __( "Print", "delicious-recipes" ), 'style' => $printStyles ) ) : '' )
						.'
					</figcaption>
				</figure>
			</div>';
		}

		$recipe_card_heading = '
			<div class="dr-title-wrap">
				'. sprintf( '<h2 class="%s">%s</h2>', "dr-title recipe-card-title", ( $recipeTitle ? strip_tags( $recipeTitle ) : strip_tags( $recipe_title ) ) ) .
				'<div class="dr-entry-meta">'.
					( self::$settings['displayAuthor'] ? '<span class="dr-byline"><span class="dr-meta-title">
					<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ). 'assets/images/sprite.svg#author"></use></svg>'. __( "Author:", "delicious-recipes" ) . " " .
					'</span>'. $custom_author_name .'</span>' : '' ) .
					( self::$settings['displayCookingMethod'] ? self::get_recipe_terms( 'recipe-cooking-method' ) : '' ) .
					( self::$settings['displayCuisine'] ? self::get_recipe_terms( 'recipe-cuisine' ) : '' ) .
					( self::$settings['displayCourse'] ? self::get_recipe_terms( 'recipe-course' ) : '' ) .
					( self::$settings['displayRecipeKey'] ? self::get_recipe_terms( 'recipe-key' ) : '' ) .
				'</div>'.
			'</div>';

		$summary_text = '';
		if ( ! empty( $summary ) ) {
			$summary_class = 'dr-summary recipe-card-summary';
			$summary_text = sprintf(
				'<div class="%s"><h3 class="%s">%s</h3><p>%s</p></div>',
				esc_attr( $summary_class ),
				'dr-title summary-title',
				@$summaryTitle,
				$summary
			);
		}

		$details_content     = self::get_details_content( $details );
		$ingredients_content = self::get_ingredients_content( $ingredients );
		$steps_content       = self::get_steps_content( $steps );
		$recipe_card_video   = self::get_video_content();

		$strip_tags_notes = isset( $notes ) ? strip_tags($notes) : '';
		$notes            = str_replace('<li></li>', '', $notes);       // remove empty list item
		$notes_content = ! empty($strip_tags_notes) ?
			sprintf(
				'<div class="dr-note">
					<h3 class="dr-title notes-title">%s</h3>
					<ul class="recipe-card-notes-list">%s</ul>
				</div>',
				@$notesTitle,
				@$notes
			) : '';

		$keywords_text = '';
		if ( ! empty( $keywords ) ) {
			$keywords_class = 'dr-keywords';
			$keywords_text = sprintf(
				'<div class="%s"><span class="%s">%s</span>%s</div>',
				esc_attr( $keywords_class ),
				'dr-meta-title',
				__("Keywords:", 'delicious-recipes'),
				implode( ', ', $keywords )
			);
		}

		$json_ld              = self::get_json_ld( $attributes );
		$structured_data_json = '';

		if ( ! empty( $json_ld ) ) {
			$structured_data_json = '<script type="application/ld+json">' . wp_json_encode( $json_ld ) . '</script>';
		}

		$block_content = sprintf(
			'<div class="%1$s" id="%2$s">
				<div class="dr-post-summary">
					<div class="dr-recipe-summary-inner">%3$s</div>
					%4$s
					%5$s
				</div>
			</div>',
			esc_attr( trim($RecipeCardClassName) ),
			esc_attr( $id ),
			$recipe_card_image .
			$recipe_card_heading ,
			$details_content .
			$summary_text ,
			$ingredients_content .
			$steps_content .
			$recipe_card_video .
			$notes_content .
			$keywords_text .
			$structured_data_json
		);

		return $block_content;
	}

	/**
	 * Returns the JSON-LD for a recipe-card block.
	 *
	 * @return array The JSON-LD representation of the recipe-card block.
	 */
	protected static function get_json_ld() {
		$attributes = self::$attributes;
		$tag_list  	= wp_get_post_terms( self::$recipe->ID, 'post_tag', array( 'fields' => 'names' ) );
		$cat_list 	= wp_get_post_terms( self::$recipe->ID, 'category', array( 'fields' => 'names' ) );

		$json_ld = array(
			'@context' 		=> 'https://schema.org',
			'@type'    		=> 'Recipe',
			'name'			=> isset( $attributes['recipeTitle'] ) ? $attributes['recipeTitle'] : self::$recipe->post_title,
			'image'			=> '',
			'description' 	=> isset( $attributes['summary'] ) ? $attributes['summary'] : self::$recipe->post_excerpt,
			'keywords'  	=> $tag_list,
			'author' 		=> array(
				'@type'		=> 'Person',
				'name'		=> get_the_author()
			),
			'datePublished' => get_the_time('c'),
			'prepTime' 		=> '',
			'cookTime'		=> '',
			'totalTime' 	=> '',
			'recipeCategory' => $cat_list,
			'recipeCuisine'  => array(),
			'cookingMethod'  => array(),
			'recipeYield'	=> '',
			'nutrition' 	=> array(
				'@type' 	=> 'NutritionInformation'
			),
			'recipeIngredient'	 => array(),
			'recipeInstructions' => array(),
			'video'			=> array(
				'@type'			=> 'VideoObject',
				'name'  		=> isset( $attributes['recipeTitle'] ) ? $attributes['recipeTitle'] : self::$recipe->post_title,
				'description' 	=> isset( $attributes['summary'] ) ? $attributes['summary'] : self::$recipe->post_excerpt,
				'thumbnailUrl' 	=> '',
				'contentUrl' 	=> '',
				'embedUrl' 		=> '',
				'uploadDate' 	=> get_the_time('c'), // by default is post plublish date
				'duration' 		=> '',
			),
		);

		if ( ! empty( $attributes['recipeTitle'] ) ) {
			$json_ld['name'] = $attributes['recipeTitle'];
		}

		if ( ! empty( $attributes['summary'] ) ) {
			$json_ld['description'] = strip_tags( $attributes['summary'] );
		}

		if ( ! empty( $attributes['image'] ) && isset( $attributes['hasImage'] ) && $attributes['hasImage'] ) {
			$image_id = isset( $attributes['image']['id'] ) ? $attributes['image']['id'] : 0;
 			$image_sizes = isset( $attributes['image']['sizes'] ) ? $attributes['image']['sizes'] : array();
 			$image_sizes_url = array(
 				self::get_image_size_url( $image_id, 'full', $image_sizes ),
 				self::get_image_size_url( $image_id, 'delrecpe-structured-data-1_1', $image_sizes ),
 				self::get_image_size_url( $image_id, 'delrecpe-structured-data-4_3', $image_sizes ),
 				self::get_image_size_url( $image_id, 'delrecpe-structured-data-16_9', $image_sizes ),
 			);
 			$json_ld['image'] = array_values( array_unique( $image_sizes_url ) );
		}

		if ( isset( $attributes['video'] ) && ! empty( $attributes['video'] ) && isset( $attributes['hasVideo'] ) && $attributes['hasVideo'] ) {
			$video = $attributes['video'];
			$video_id = isset( $video['id'] ) ? $video['id'] : 0;
			$video_type = isset( $video['type'] ) ? $video['type'] : '';

			if ( 'self-hosted' === $video_type ) {
 				$video_attachment = get_post( $video_id );

 				if ( $video_attachment ) {
 					$video_data = wp_get_attachment_metadata( $video_id );
 					$video_url = wp_get_attachment_url( $video_id );

 					$image_id = get_post_thumbnail_id( $video_id );
 					$thumb = wp_get_attachment_image_src( $image_id, 'full' );
 					$thumbnail_url = $thumb && isset( $thumb[0] ) ? $thumb[0] : '';

 					$json_ld['video'] = array_merge(
 						$json_ld['video'], array(
 							'name' => $video_attachment->post_title,
 							'description' => $video_attachment->post_content,
 							'thumbnailUrl' => $thumbnail_url,
 							'contentUrl' => $video_url,
 							'uploadDate' => date( 'c', strtotime( $video_attachment->post_date ) ),
 							'duration' => 'PT' . $video_data['length'] . 'S',
 						)
 					);
 				}
 			}

			if ( isset( $video['title'] ) && ! empty( $video['title'] ) ) {
				$json_ld['video']['name'] = esc_html( $video['title'] );
			}
			if ( isset( $video['caption'] ) && !empty( $video['caption'] ) ) {
				$json_ld['video']['description'] = esc_html( $video['caption'] );
			}
			if ( isset( $video['description'] ) && !empty( $video['description'] ) ) {
				if ( is_string( $video['description'] ) ) {
					$json_ld['video']['description'] = esc_html( $video['description'] );
				}
			}
			if ( isset( $video['poster']['url'] ) ) {
				$json_ld['video']['thumbnailUrl'] = esc_url( $video['poster']['url'] );

				if ( isset( $video['poster']['id'] ) ) {
 					$poster_id = $video['poster']['id'];
 					$poster_sizes_url = array(
 						self::get_image_size_url( $poster_id, 'full' ),
 						self::get_image_size_url( $poster_id, 'delrecpe-structured-data-1_1' ),
 						self::get_image_size_url( $poster_id, 'delrecpe-structured-data-4_3' ),
 						self::get_image_size_url( $poster_id, 'delrecpe-structured-data-16_9' ),
 					);
 					$json_ld['video']['thumbnailUrl'] = array_values( array_unique( $poster_sizes_url ) );
 				}
			}
			if ( isset( $video['url'] ) ) {
				$json_ld['video']['contentUrl'] = esc_url( $video['url'] );

				if ( 'embed' === $video_type ) {
					$video_embed_url = $video['url'];

					$json_ld['video']['@type'] = 'VideoObject';

					if ( ! empty( $attributes['image'] ) && isset( $attributes['hasImage'] ) && $attributes['hasImage'] ) {
						$image_id = isset( $attributes['image']['id'] ) ? $attributes['image']['id'] : 0;
 						$image_sizes = isset( $attributes['image']['sizes'] ) ? $attributes['image']['sizes'] : array();
 						$image_sizes_url = array(
 							self::get_image_size_url( $image_id, 'full', $image_sizes ),
 							self::get_image_size_url( $image_id, 'delrecpe-structured-data-1_1', $image_sizes ),
 							self::get_image_size_url( $image_id, 'delrecpe-structured-data-4_3', $image_sizes ),
 							self::get_image_size_url( $image_id, 'delrecpe-structured-data-16_9', $image_sizes ),
 						);
 						$json_ld['video']['thumbnailUrl'] = array_values( array_unique( $image_sizes_url ) );
					}

					if ( strpos( $video['url'], 'youtu' ) ) {
						$video_embed_url = self::$helpers->convert_youtube_url_to_embed( $video['url'] );
					}
					elseif ( strpos( $video['url'] , 'vimeo' ) ) {
						$video_embed_url = self::$helpers->convert_vimeo_url_to_embed( $video['url'] );
					}

					$json_ld['video']['embedUrl'] = esc_url( $video_embed_url );
				}
			}
			if ( isset( $video['date'] ) && 'embed' === $video_type ) {
				$json_ld['video']['uploadDate'] = $video['date'];
			}
		}
		else {

			// we have no video added
			// removed video attribute from json_ld array
			unset( $json_ld['video'] );

		}

		if ( ! empty( $attributes['course'] ) && self::$settings['displayCourse'] ) {
			$json_ld['recipeCategory'] = $attributes['course'];
		}

		if ( ! empty( $attributes['cuisine'] ) && self::$settings['displayCuisine'] ) {
			$json_ld['recipeCuisine'] = $attributes['cuisine'];
		}

		if ( ! empty( $attributes['method'] ) && self::$settings['displayCookingMethod'] ) {
			$json_ld['cookingMethod'] = $attributes['method'];
		}

		if ( ! empty( $attributes['keywords'] ) ) {
			$json_ld['keywords'] = $attributes['keywords'];
		}

		if ( ! empty( $attributes['details'] ) && is_array( $attributes['details'] ) ) {
			$details = array_filter( $attributes['details'], 'is_array' );

			foreach ( $details as $key => $detail ) {
				if ( $key === 4 ) {
					if ( ! empty( $detail[ 'value' ] ) && self::$settings['displayServings'] ) {
						if ( !is_array( $detail['value'] ) ) {
							$yield = array(
 								$detail['value']
 							);

							if ( isset( $detail['unit'] ) && ! empty( $detail['unit'] ) ) {
								$yield[] = $detail['value'] .' '. $detail['unit'];
							}
						}
						elseif ( isset( $detail['jsonValue'] ) ) {
							$yield = array(
 								$detail['jsonValue']
 							);

							if ( isset( $detail['unit'] ) && ! empty( $detail['unit'] ) ) {
								$yield[] = $detail['value'] .' '. $detail['unit'];
							}
						}

						if ( isset( $yield ) ) {
 							$json_ld['recipeYield'] = $yield;
 						}
					}
				}
				elseif ( $key === 5 ) {
					if ( ! empty( $detail[ 'value' ] ) && self::$settings['displayCalories'] ) {
						if ( !is_array( $detail['value'] ) ) {
							$json_ld['nutrition']['calories'] = $detail['value'] .' cal';
						}
						elseif ( isset( $detail['jsonValue'] ) ) {
							$json_ld['nutrition']['calories'] = $detail['jsonValue'] .' cal';
						}
					}
				}
				elseif ( $key === 0 ) {
					if ( ! empty( $detail[ 'value' ] ) && self::$settings['displayPrepTime'] ) {
						if ( !is_array( $detail['value'] ) ) {
							$prepTime = self::$structured_data_helpers->get_number_from_string( $detail['value'] );
						    $json_ld['prepTime'] = self::$structured_data_helpers->get_period_time( $detail['value'] );
						}
						elseif ( isset( $detail['jsonValue'] ) ) {
							$prepTime = self::$structured_data_helpers->get_number_from_string( $detail['jsonValue'] );
						    $json_ld['prepTime'] = self::$structured_data_helpers->get_period_time( $detail['jsonValue'] );
						}
					}
				}
				elseif ( $key === 1 ) {
					if ( ! empty( $detail[ 'value' ] ) && self::$settings['displayCookingTime'] ) {
						if ( !is_array( $detail['value'] ) ) {
							$cookTime = self::$structured_data_helpers->get_number_from_string( $detail['value'] );
						    $json_ld['cookTime'] = self::$structured_data_helpers->get_period_time( $detail['value'] );
						}
						elseif ( isset( $detail['jsonValue'] ) ) {
							$cookTime = self::$structured_data_helpers->get_number_from_string( $detail['jsonValue'] );
						    $json_ld['cookTime'] = self::$structured_data_helpers->get_period_time( $detail['jsonValue'] );
						}
					}
				}
				elseif ( $key === 3 ) {
					if ( ! empty( $detail[ 'value' ] ) && self::$settings['displayTotalTime'] ) {
						if ( !is_array( $detail['value'] ) ) {
							$json_ld['totalTime'] = self::$structured_data_helpers->get_period_time( $detail['value'] );
						}
						elseif ( isset( $detail['jsonValue'] ) ) {
							$json_ld['totalTime'] = self::$structured_data_helpers->get_period_time( $detail['jsonValue'] );
						}
					}
				}
			}

			if ( empty( $json_ld['totalTime'] ) ) {
				if ( isset( $prepTime, $cookTime ) && ( $prepTime + $cookTime ) > 0 ) {
					$json_ld['totalTime'] = self::$structured_data_helpers->get_period_time( $prepTime + $cookTime );
				}
			}
		}

		if ( ! empty( $attributes['ingredients'] ) && is_array( $attributes['ingredients'] ) ) {
			$ingredients = array_filter( $attributes['ingredients'], 'is_array' );
			foreach ( $ingredients as $ingredient ) {
				$isGroup = isset( $ingredient['isGroup'] ) ? $ingredient['isGroup'] : false;

				if ( ! $isGroup ) {
					$json_ld['recipeIngredient'][] = self::$structured_data_helpers->get_ingredient_json_ld( $ingredient );
				}

			}
		}

		if ( ! empty( $attributes['steps'] ) && is_array( $attributes['steps'] ) ) {
			$steps = array_filter( $attributes['steps'], 'is_array' );
			$groups_section = array();
			$instructions = array();

			foreach ( $steps as $key => $step ) {
				$isGroup = isset( $step['isGroup'] ) ? $step['isGroup'] : false;
				$parent_permalink = get_the_permalink( self::$recipe );
				
				if ( $isGroup ) {
					$groups_section[ $key ] = array(
						'@type' => 'HowToSection',
						'name' => '',
						'itemListElement' => array(),
					);
					if ( ! empty( $step['jsonText'] ) ) {
						$groups_section[ $key ]['name'] = $step['jsonText'];
					} else {
						$groups_section[ $key ]['name'] = self::$structured_data_helpers->step_text_to_JSON( $step['text'] );
					}
				}

				if ( count( $groups_section ) > 0 ) {
					end( $groups_section );
					$last_key = key( $groups_section );

					if ( ! $isGroup && $key > $last_key ) {
						$groups_section[ $last_key ]['itemListElement'][] = self::$structured_data_helpers->get_step_json_ld( $step, $parent_permalink );
					}
				} else {
					$instructions[] = self::$structured_data_helpers->get_step_json_ld( $step, $parent_permalink );
				}
			}

			$groups_section = array_merge( $instructions, $groups_section );
			$json_ld['recipeInstructions'] = $groups_section;
		}

		return $json_ld;
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

	public static function get_steps_default() {
		return array(
			array(
				'id' 		=> self::$helpers->generateId( "direction-step" ),
				'text' 		=> array(),
			),
		    array(
		    	'id' 		=> self::$helpers->generateId( "direction-step" ),
		    	'text' 		=> array(),
		    ),
		    array(
		        'id' 		=> self::$helpers->generateId( "direction-step" ),
		        'text' 		=> array(),
		    ),
		    array(
		        'id' 		=> self::$helpers->generateId( "direction-step" ),
		        'text' 		=> array(),
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

	public static function get_steps_content( array $steps ) {
		$direction_items = self::get_direction_items( $steps );

		$listClassNames = implode( ' ', array( 'directions-list', 'dr-ordered-list' ) );

		return sprintf(
			'<div class="dr-instructions"><div class="dr-instrc-title-wrap"><h3 class="directions-title dr-title">%s</h3></div><ol class="%s">%s</ol></div>',
			self::$attributes['directionsTitle'],
			$listClassNames,
			$direction_items
		);
	}

	public static function get_direction_items( array $steps ) {
		$output = '';

		foreach ( $steps as $index => $step ) {
			$text = '';
			$isGroup = isset( $step['isGroup'] ) ? $step['isGroup'] : false;

			if ( !$isGroup ) {
				if ( ! empty( $step['text'] ) ) {
					$text = self::wrap_direction_text( $step['text'] );
					$output .= sprintf(
						'<li>%s</li>',
						$text
					);
				}
			} else {
				if ( ! empty( $step['text'] ) ) {
					$text = self::wrap_direction_text( $step['text'] );
					$output .= sprintf(
						'<h4 class="dr-title">%s</h4>',
						$text
					);
				}
			}
		}

		return force_balance_tags( $output );
	}

	public static function get_recipe_terms( $taxonomy ) {
		$attributes 	= self::$attributes;
		$render 		= true;

		$className = $label = $terms_output = '';

		extract( $attributes );

		$course     = isset( $course ) ? $course : array();
		$cuisine    = isset( $cuisine ) ? $cuisine : array();
		$method     = isset( $method ) ? $method : array();
		$recipeKey  = isset( $recipeKey ) ? $recipeKey : array();

		if ( 'recipe-course' === $taxonomy ) {
			if ( empty( $course ) ) {
				$render = false;
			}
			$terms     = $course;
			$className = 'dr-category';
			$label     = __( "Courses:", "delicious-recipes" );
			$svg       = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ).'assets/images/sprite.svg#category"></use></svg>';
		}
		elseif ( 'recipe-cuisine' === $taxonomy ) {
			if ( empty( $cuisine ) ) {
				$render = false;
			}
			$terms     = $cuisine;
			$className = 'dr-cuisine';
			$label     = __( "Cuisine:", "delicious-recipes" );
			$svg       = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ).'assets/images/sprite.svg#cuisine"></use></svg>';
		}
		elseif ( 'recipe-cooking-method' === $taxonomy ) {
			if ( empty( $method ) ) {
				$render = false;
			}
			$terms     = $method;
			$className = 'dr-method';
			$label     = __( "Cooking Method:", "delicious-recipes" );
			$svg       = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ).'assets/images/sprite.svg#cooking-method"></use></svg>';
		}
		elseif ( 'recipe-key' === $taxonomy ) {
			if ( empty( $recipeKey ) ) {
				$render = false;
			}
			$terms     = $recipeKey;
			$className = 'dr-category dr-recipe-keys';
			$label     = __( "Recipe Keys:", "delicious-recipes" );
			$svg       = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ).'assets/images/sprite.svg#recipe-keys"></use></svg>';
		}

		if ( $render ) {
			$terms_output = sprintf( '<span class="%s"><span class="dr-meta-title">%s %s</span>%s</span>', $className, $svg, $label, implode( ', ', $terms ) );
		}

		return $terms_output;
	}

	public static function wrap_direction_text( $nodes, $type = '' ) {
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

				if ( 'img' === $type ) {
					$src = isset( $node['props']['src'] ) ? $node['props']['src'] : false;
					if ( $src ) {
						$attachment_id = isset( $node['key'] ) ? $node['key'] : 0;
						$alt = isset( $node['props']['alt'] ) ? $node['props']['alt'] : '';
						$title = isset( $node['props']['title'] ) ? $node['props']['title'] : ( isset( $attributes['recipeTitle'] ) ? $attributes['recipeTitle'] : self::$recipe->post_title );
						$class = ' direction-step-image';
						$img_style = isset($node['props']['style']) ? $node['props']['style'] : '';

						// Try to get attachment ID by image url if attribute `key` is not found in $node array
						if ( ! $attachment_id ) {
							$new_src = $src;

							$re = '/-\d+[Xx]\d+\./m';
							preg_match_all( $re, $src, $matches );

							// Remove image size from url to be able to get attachment id
							// e.g. .../wp-content/uploads/sites/30/2019/10/image-example-1-500x375.jpg
							// 	 => .../wp-content/uploads/sites/30/2019/10/image-example-1.jpg
							if ( isset( $matches[0][0] ) ) {
								$new_src = str_replace( $matches[0][0], '.', $new_src );
							}

							// The found post ID, or 0 on failure.
							$attachment_id = attachment_url_to_postid( $new_src );

							if ( $attachment_id ) {
								$attachment = wp_get_attachment_image( $attachment_id, 'full', false, array( 'title' => $title, 'alt' => $alt, 'class' => trim( $class ), 'style' => self::parseTagStyle( $img_style ) ) );
							}
						}
						else {
							$attachment = wp_get_attachment_image( $attachment_id, 'full', false, array( 'title' => $title, 'alt' => $alt, 'class' => trim( $class ), 'style' => self::parseTagStyle( $img_style ) ) );
						}

						if ( $attachment ) {
							$start_tag = $attachment;
						}
						else {
							$start_tag = sprintf(
								'<%s src="%s" title="%s" alt="%s" class="%s" style="%s"/>',
								$type,
								$src,
								$title,
								$alt,
								trim( $class ),
								self::parseTagStyle( $img_style )
							);
						}
					}
					else {
						$start_tag = "";
					}
					$end_tag = "";
				}
				elseif ( 'a' === $type ) {
					$rel 		= isset( $node['props']['rel'] ) ? $node['props']['rel'] : '';
					$aria_label = isset( $node['props']['aria-label'] ) ? $node['props']['aria-label'] : '';
					$href 		= isset( $node['props']['href'] ) ? $node['props']['href'] : '#';
					$target 	= isset( $node['props']['target'] ) ? $node['props']['target'] : '_blank';

					$start_tag = sprintf( '<%s rel="%s" aria-label="%s" href="%s" target="%s">', $type, $rel, $aria_label, $href, $target );
				}
				elseif ( 'br' === $type ) {
					$end_tag = "";
				}

				$output .= $start_tag . self::wrap_direction_text( $children, $type ) . $end_tag;
			}
		}

		return $output;
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

				if ( 'img' === $type ) {
					$src = isset( $node['props']['src'] ) ? $node['props']['src'] : false;
					if ( $src ) {
						$alt = isset( $node['props']['alt'] ) ? $node['props']['alt'] : '';
						$title = isset( $node['props']['title'] ) ? $node['props']['title'] : ( isset( $attributes['recipeTitle'] ) ? $attributes['recipeTitle'] : self::$recipe->post_title );
						$class = ' direction-step-image';
						$img_style = isset($node['props']['style']) ? $node['props']['style'] : '';

						$start_tag = sprintf( '<%s src="%s" title="%s" alt="%s" class="%s" style="%s"/>', $type, $src, $title, $alt, trim($class), self::parseTagStyle($img_style) );
					} else {
						$start_tag = "";
					}
					$end_tag = "";
				}
				elseif ( 'a' === $type ) {
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
	 * Get HTML content for recipe video
	 * 
	 * @since 2.1.1
	 * @return void
	 */
	public static function get_video_content() {
		$attributes = self::$attributes;
		$hasVideo = isset( $attributes['hasVideo'] ) && $attributes['hasVideo'];
		$output = '';

		if ( ! $hasVideo ) {
			return '';
		}

		$video = isset( $attributes['video'] ) && ! empty( $attributes['video'] ) ? $attributes['video'] : array();
		$video_type = isset( $video['type'] ) ? $video['type'] : '';
		$video_id = isset( $video['id'] ) ? $video['id'] : 0;
		$video_url = isset( $video['url'] ) ? esc_url( $video['url'] ) : '';
		$video_poster = isset( $video['poster']['url'] ) ? esc_url( $video['poster']['url'] ) : '';
		$video_settings = isset( $video['settings'] ) ? $video['settings'] : array();

		if ( 'embed' === $video_type ) {
			$output = wp_oembed_get( $video_url );
		}
		elseif ( 'self-hosted' === $video_type ) {
			$attrs = array();
			foreach ( $video_settings as $attribute => $value ) {
				if ( $value ) {
					$attrs[] = $attribute;
				}
			}
			$attrs = implode( ' ', $attrs );

			if ( empty( $video_url ) && 0 !== $video_id ) {
 				$video_url = wp_get_attachment_url( $video_id );
 			}

			$output = sprintf(
				'<video %s src="%s" poster="%s"></video>',
				esc_attr( $attrs ),
				$video_url,
				$video_poster
			);
		}

		return sprintf( '<div class="dr-instructions-video" id="dr-video-gallery"><h3 class="video-title">%s</h3><div class="dr-vdo-thumbnail">%s</div></div>', $attributes['videoTitle'], $output );
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

	/**
	 * Parse HTML tag styles
	 *
	 * @since 2.1.0
	 * @param string|array $style Tag styles to parse
	 * @return string 			  CSS styles
	 */
	public static function parseTagStyle( $styles ) {
		$css = '';
		if ( is_array( $styles ) ) {
			foreach ( $styles as $property => $value ) {
				$css .= $property.': '.$value.';';
			}
		} elseif ( is_string( $styles ) ) {
			$css = $styles;
		}
		return $css;
	}

	/**
	 * Get HTML for print button
	 * 
	 * @since 2.2.0
	 * 
	 * @param array $media        The recipe media image array which include 'url'
	 * @param string $url         The recipe permalink url
	 * @param string $description The description to display on pinterest board
	 * @param array $attributes   Additional html attributes like ('style' => 'color: red; font-size: inherit')
	 * @return string
	 */
	public static function get_print_button( $content_id, $attributes = array() ) {
		if ( empty( $content_id ) )
			return '';

		$PrintClasses = implode( ' ', array( "dr-buttons", "dr-recipe-card-block-print" ) );

		/**
		 * Add additional attributes to print button
		 * [serving-size, recipe-id]
		 * 
		 * @since 2.6.3
		 */
		$servings = isset( self::$attributes['details'][4]['value'] ) ? self::$attributes['details'][4]['value'] : 0;
		$attributes = array_merge( $attributes, array( 'data-servings-size' => $servings ) );

		if ( self::$recipe ) {
			$attributes = array_merge( $attributes, array( 'data-recipe-id' => self::$recipe->ID ) );
		}

		$atts = self::$helpers->render_attributes( $attributes );

		$output = sprintf(
			'<div class="%s">
	            <a class="dr-print-trigger dr-btn-link dr-btn2" href="#%s" %s>
					<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ). 'assets/images/sprite.svg#print"></use></svg>
	                %s
	            </a>
	        </div>',
			esc_attr( $PrintClasses ),
			esc_attr( $content_id ),
			$atts,
			__( "Print Recipe", "delicious-recipes" )
		);

		return $output;
	}

	/**
	 * Get HTML for pinterest button
	 * 
	 * @since 2.2.0
	 * 
	 * @param array $media        The recipe media image array which include 'url'
	 * @param string $url         The recipe permalink url
	 * @param string $description The description to display on pinterest board
	 * @param array|string $attributes   Additional html attributes: array('style' => 'color: red; font-size: inherit') or 
	 * 									 string 'style="color: red; font-size: inherit"'
	 * @return string
	 */
	public static function get_pinterest_button( $media, $url, $description = '', $attributes = '' ) {
		if ( ! isset(  $media['url'] ) )
			return '';

		$PinterestClasses = implode( ' ', array( "post-pinit-button" ) );
		$pinitURL 		  = 'https://www.pinterest.com/pin/create/button/?url=' . esc_url( $url ) .'/&media='. esc_url( $media['url'] ) .'&description='. esc_html( $description ) .'';

		$atts = self::$helpers->render_attributes( $attributes );

		$output = sprintf(
			'<span class="%s">
	            <a data-pin-do="buttonPin" href="%s" data-pin-custom="true" %s>
					<img src="%s" alt="pinit">
	            </a>
	        </span>',
	        esc_attr( $PinterestClasses ),
	        esc_url( $pinitURL ),
			$atts,
			esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ).'/assets/images/pinit-sm.png'
		);

		return $output;
	}

	/**
     * Get recipe card image size name
     * 
     * @since 2.6.3
     * 
     * @return object
     */
    public static function get_recipe_image_size( $sizes, $src ) {
    	if ( is_array( $sizes ) && ! empty( $sizes ) ) {
    		foreach ( $sizes as $size_name => $size_attrs ) {
    			if ( isset( $size_attrs['url'] ) ) {
    				if ( $size_attrs['url'] === $src ) {
    					$size = $size_name;
    				}
    			}
    			elseif ( isset( $size_attrs['source_url'] ) ) {
    				if ( $size_attrs['source_url'] === $src ) {
    					$size = $size_name;
    				}
    			}
    		}
    	}

    	if ( ! isset( $size ) ) {
    		$size = 'full';
    	}

    	return $size;
    }

    /**
     * Get image url by specified $size
     * 
     * @since 2.6.3
     * 
     * @param  string|number $image_id    	The image id to get url
     * @param  string $size        			The specific image size
     * @param  array  $image_sizes 			Available image sizes for specified image id
     * @return string              			The image url
     */
    public static function get_image_size_url( $image_id, $size = 'full', $image_sizes = array() ) {
    	if ( isset( $image_sizes[ $size ] ) ) {
    		if ( isset( $image_sizes[ $size ]['url'] ) ) {
	    		$image_url = $image_sizes[ $size ]['url'];
    		} elseif ( isset( $image_sizes[ $size ]['source_url'] ) ) {
	    		$image_url = $image_sizes[ $size ]['source_url'];
    		}
    	}

    	if ( function_exists( 'fly_get_attachment_image_src' ) ) {
    		$thumb = fly_get_attachment_image_src( $image_id, $size );

    		if ( $thumb ) {
    			$image_url = isset( $thumb[0] ) ? $thumb[0] : $thumb['src'];
    		}
    	}

    	if ( !isset( $image_url ) ) {
    		$thumb = wp_get_attachment_image_src( $image_id, $size );
    		$image_url = $thumb && isset( $thumb[0] ) ? $thumb[0] : '';
    	}

    	return $image_url;
    }

    /**
     * Check whether a url is a blob url.
     * 
     * @since 2.6.3
     *
     * @param string $url 	The URL.
     *
     * @return boolean 		Is the url a blob url?
     */
    public static function is_blob_URL( $url ) {
    	if ( ! is_string( $url ) || empty( $url ) ) {
    		return false;
    	}
		return strpos( $url, 'blob:' ) === 0;
	}
}