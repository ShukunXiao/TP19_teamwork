<?php
/**
 * Responsible for importing WP Recipe Maker recipes.
 *
 * @since      1.0.0
 *
 * @package    Delicious_Recipes
 * @subpackage Delicious_Recipes/src/import
 */

class Delicious_Recipes_Import_WP_Recipe_Maker {

	/**
	 * Get the UID of this import source.
	 *
	 * @since    1.0.0
	 */
	public function get_uid() {
		return 'wprecipemaker';
	}

	/**
	 * Get the name of this import source.
	 *
	 * @since    1.0.0
	 */
	public function get_name() {
		return __( 'WP Recipe Maker', 'delicious-recipes' );
	}

	/**
	 * is_plugin_active
	 */
	public function is_plugin_active() {
		return is_plugin_active( 'wp-recipe-maker/wp-recipe-maker.php' );
	}

	/**
	 * Get HTML for the import settings.
	 *
	 * @since    1.0.0
	 */
	public function get_settings_html() {
		$html = '<h4>' . __( "Recipe Taxonomies Mapping", 'delicious-recipes' ) . '</h4>';

		$brm_taxonomies = array(
			'wprm_course'  => 'Courses',
			'wprm_cuisine' => 'Cuisines',
			'wprm_keyword' => 'Keywords',
		);

		$delicious_taxonomies = delicious_recipes_get_taxonomies();

		foreach ( $delicious_taxonomies as $dr_taxonomy => $dr_name ) {

			$html .= '<label for="brm-tags-' . $dr_taxonomy . '">' . $dr_name . ':</label> ';
			$html .= '<select name="brm-tags-' . $dr_taxonomy . '" id="brm-tags-' . $dr_taxonomy . '">';
			$html .= "<option value=\"\">Don't import anything for this tag</option>";
			foreach ( $brm_taxonomies as $name => $label ) {
				$selected = $dr_taxonomy === $name ? ' selected="selected"' : '';
				$html .= '<option value="' . esc_attr( $name ) . '"' . esc_html( $selected ) . '>' . esc_html( $label ) . '</option>';
			}
			$html .= '</select>';
			$html .= '<br />';
		}

		return $html;
	}

	/**
	 * Get the total number of recipes to import.
	 *
	 * @since    1.0.0
	 */
	public function get_recipe_count() {
		$args = array(
			'post_type'      => 'wprm_recipe',
			'post_status'    => 'any',
			'posts_per_page' => -1,
		);

		$query = new WP_Query( $args );
		return $query->found_posts;
	}

	/**
	 * Get a list of recipes that are available to import.
	 *
	 * @since    1.0.0
	 * @param	 int $page Page of recipes to get.
	 */
	public function get_recipes( $page = 0 ) {
		$recipes = array();

		$limit = 100;
		$offset = $limit * $page;

		$args = array(
			'post_type'      => 'wprm_recipe',
			'post_status'    => 'any',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => $limit,
			'offset'         => $offset,
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			$posts = $query->posts;

			foreach ( $posts as $post ) {
				$recipes[ $post->ID ] = array(
					'name'   => $post->post_title,
					'url'    => get_edit_post_link( $post->ID ),
					'view'   => get_the_permalink( $post->ID ),
					'author' => get_the_author_meta( 'display_name', $post->post_author ),
					'image'  => get_the_post_thumbnail_url( $post->ID, 'post-thumbnail' ),
					'date'   => get_the_date( 'Y-m-d', $post->ID ),
				);
			}
		}

		return $recipes;
	}

	/**
	 * Get recipe with the specified ID in the import format.
	 *
	 * @since    1.0.0
	 * @param		 mixed $id ID of the recipe we want to import.
	 * @param		 array $post_data POST data passed along when submitting the form.
	 */
	public function get_recipe( $id, $post_data ) {
		$post = get_post( $id );

		$import_id = $id;

		$post_meta = get_post_custom( $id );

		$recipe = array(
			'import_id'     => $import_id,
			'import_backup' => array(
				'wprm_recipe_id'   => $id,
				'wprm_recipe_meta' => $post_meta,
			),
		);

		$recipe['image_id']                  = get_post_thumbnail_id( $id );
		$recipe['name']                      = $post->post_title;
		$recipe['meta']['recipeDescription'] = $post->post_content;
		$recipe['excerpt']                   = has_excerpt( $id ) ? get_the_excerpt( $id ) : '';
		$recipe['meta']['noOfServings']      = isset( $post_meta['wprm_servings'][0] ) && ! empty( $post_meta['wprm_servings'][0] ) ? $post_meta['wprm_servings'][0] : '';
		$recipe['meta']['recipeCalories']    = isset( $post_meta['wprm_nutrition_calories'][0] ) && ! empty( $post_meta['wprm_nutrition_calories'][0] ) ? $post_meta['wprm_nutrition_calories'][0] . ' kcal' : '';
		$recipe['meta']['difficultyLevel']   = '';

		// Recipe Times.
		$recipe['meta']['prepTime'] = isset( $post_meta['wprm_prep_time'][0] ) && ! empty( $post_meta['wprm_prep_time'][0] ) ? $post_meta['wprm_prep_time'][0] : '';
		$recipe['meta']['cookTime'] = isset( $post_meta['wprm_cook_time'][0] ) && ! empty( $post_meta['wprm_cook_time'][0] ) ? $post_meta['wprm_cook_time'][0] : '';
		$recipe['meta']['restTime'] = isset( $post_meta['wprm_custom_time'][0] ) && ! empty( $post_meta['wprm_custom_time'][0] ) ? $post_meta['wprm_custom_time'][0] : '';
		$recipe['meta']['restTimeUnit'] = 'min';
		$recipe['meta']['prepTimeUnit'] = 'min';
		$recipe['meta']['cookTimeUnit'] = 'min';

		$recipe['meta']['recipeNotes'] = isset( $post_meta['wprm_notes'][0] ) && ! empty( $post_meta['wprm_notes'][0] ) ? $post_meta['wprm_notes'][0] : ''; 

		$terms = get_the_terms( $id, 'wprm_keyword', '', ',', '' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$keywords[] = $term->name;
			}
			$recipe['meta']['recipeKeywords']  = implode( ", ", $keywords );
		}

		// Recipe Tags.
		$recipe['tags'] = array();

		$delicious_taxonomies = delicious_recipes_get_taxonomies();
		foreach ( $delicious_taxonomies as $dr_taxonomy => $name ) {
			$tag = isset( $post_data[ 'brm-tags-' . $dr_taxonomy ] ) ? $post_data[ 'brm-tags-' . $dr_taxonomy ] : false;

			if ( $tag ) {
				$terms = get_the_terms( $id, $tag );
				if ( $terms && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$recipe['tags'][ $dr_taxonomy ][] = $term->name;
					}
				}
			}
		}

		$recipe['meta']['servingSize']       = isset( $post_meta['wprm_nutrition_serving_size'][0] ) && ! empty( $post_meta['wprm_nutrition_serving_size'][0] ) ? $post_meta['wprm_nutrition_serving_size'][0] : "";
		$recipe['meta']['calories']          = isset( $post_meta['wprm_nutrition_calories'][0] ) && ! empty( $post_meta['wprm_nutrition_calories'][0] ) ? $post_meta['wprm_nutrition_calories'][0] : "";
		$recipe['meta']['caloriesFromFat']   = "";
		$recipe['meta']['totalFat']          = isset( $post_meta['wprm_nutrition_fat'][0] ) && ! empty( $post_meta['wprm_nutrition_fat'][0] ) ? $post_meta['wprm_nutrition_fat'][0] : "";
		$recipe['meta']['saturatedFat']      = isset( $post_meta['wprm_nutrition_saturated_fat'][0] ) && ! empty( $post_meta['wprm_nutrition_saturated_fat'][0] ) ? $post_meta['wprm_nutrition_saturated_fat'][0] : "";
		$recipe['meta']['transFat']          = isset( $post_meta['wprm_nutrition_trans_fat'][0] ) && ! empty( $post_meta['wprm_nutrition_trans_fat'][0] ) ? $post_meta['wprm_nutrition_trans_fat'][0] : "";
		$recipe['meta']['cholesterol']       = isset( $post_meta['wprm_nutrition_cholestrol'][0] ) && ! empty( $post_meta['wprm_nutrition_cholestrol'][0] ) ? $post_meta['wprm_nutrition_cholestrol'][0] : "";
		$recipe['meta']['sodium']            = isset( $post_meta['wprm_nutrition_sodium'][0] ) && ! empty( $post_meta['wprm_nutrition_sodium'][0] ) ? $post_meta['wprm_nutrition_sodium'][0] : "";
		$recipe['meta']['potassium']         = isset( $post_meta['wprm_nutrition_potassium'][0] ) && ! empty( $post_meta['wprm_nutrition_potassium'][0] ) ? $post_meta['wprm_nutrition_potassium'][0] : "";
		$recipe['meta']['totalCarbohydrate'] = isset( $post_meta['wprm_nutrition_carbohydrates'][0] ) && ! empty( $post_meta['wprm_nutrition_carbohydrates'][0] ) ? $post_meta['wprm_nutrition_carbohydrates'][0] : "";
		$recipe['meta']['dietaryFiber']      = isset( $post_meta['wprm_nutrition_fiber'][0] ) && ! empty( $post_meta['wprm_nutrition_fiber'][0] ) ? $post_meta['wprm_nutrition_fiber'][0] : "";
		$recipe['meta']['sugars']            = isset( $post_meta['wprm_nutrition_sugar'][0] ) && ! empty( $post_meta['wprm_nutrition_sugar'][0] ) ? $post_meta['wprm_nutrition_sugar'][0] : "";
		$recipe['meta']['protein']           = isset( $post_meta['wprm_nutrition_protein'][0] ) && ! empty( $post_meta['wprm_nutrition_protein'][0] ) ? $post_meta['wprm_nutrition_protein'][0] : "";
		$recipe['meta']['vitaminA']          = isset( $post_meta['wprm_nutrition_vitamin_a'][0] ) && ! empty( $post_meta['wprm_nutrition_vitamin_a'][0] ) ? $post_meta['wprm_nutrition_vitamin_a'][0] : "";
		$recipe['meta']['vitaminC']          = isset( $post_meta['wprm_nutrition_vitamin_c'][0] ) && ! empty( $post_meta['wprm_nutrition_vitamin_c'][0] ) ? $post_meta['wprm_nutrition_vitamin_c'][0] : "";
		$recipe['meta']['calcium']           = isset( $post_meta['wprm_nutrition_calcium'][0] ) && ! empty( $post_meta['wprm_nutrition_calcium'][0] ) ? $post_meta['wprm_nutrition_calcium'][0] : "";
		$recipe['meta']['iron']              = isset( $post_meta['wprm_nutrition_iron'][0] ) && ! empty( $post_meta['wprm_nutrition_iron'][0] ) ? $post_meta['wprm_nutrition_iron'][0] : "";
		$recipe['meta']['vitaminD']          = "";
		$recipe['meta']['vitaminE']          = "";
		$recipe['meta']['vitaminK']          = "";
		$recipe['meta']['thiamin']           = "";
		$recipe['meta']['riboflavin']        = "";
		$recipe['meta']['niacin']            = "";
		$recipe['meta']['vitaminB6']         = "";
		$recipe['meta']['folate']            = "";
		$recipe['meta']['vitaminB12']        = "";
		$recipe['meta']['biotin']            = "";
		$recipe['meta']['pantothenicAcid']   = "";
		$recipe['meta']['phosphorus']        = "";
		$recipe['meta']['iodine']            = "";
		$recipe['meta']['magnesium']         = "";
		$recipe['meta']['zinc']              = "";
		$recipe['meta']['selenium']          = "";
		$recipe['meta']['copper']            = "";
		$recipe['meta']['manganese']         = "";
		$recipe['meta']['chromium']          = "";
		$recipe['meta']['molybdenum']        = "";
		$recipe['meta']['chloride']          = "";

		// Recipe Ingredients.
		$recipe_ingredients = isset( $post_meta['wprm_ingredients'][0] ) ? $this->unserialize( $post_meta['wprm_ingredients'][0] ) : array();
		$recipe['meta']['recipeIngredients'] = array();

		$current_group = array(
			'sectionTitle' => '',
			'ingredients'  => array(),
		);
		foreach ( $recipe_ingredients as $recipe_ingredient ) {
			if ( isset( $recipe_ingredient['name'] ) || ! empty( $recipe_ingredient['ingredients'] ) ) {
		
				if( isset( $recipe_ingredient['name'] ) && empty( $current_group['sectionTitle'] ) ) {
					$current_group   = array(
						'sectionTitle' => $recipe_ingredient['name'],
						'ingredients'  => array(),
					);
				}
				if ( ! empty( $recipe_ingredient['ingredients'] ) ) {
					foreach ( $recipe_ingredient['ingredients'] as $ingredient ) {
						$current_group['ingredients'][] = array(
							'quantity'   => $ingredient['amount'],
							'unit'       => $ingredient['unit'],
							'ingredient' => $ingredient['name'],
							'notes'      => $ingredient['notes'],
						);
					}
				}
				if( ! empty( $current_group['ingredients'] ) ) {
					$recipe['meta']['recipeIngredients'][] = $current_group;
					$current_group = array(
						'sectionTitle' => '',
						'ingredients'  => array(),
					);   
				} 
			}
		}

		// Recipe Instructions.
		$recipe_instructions = isset( $post_meta['wprm_instructions'][0] ) ? $this->unserialize( $post_meta['wprm_instructions'][0] ) : array();
		$recipe['meta']['recipeInstructions'] = array();

		$current_group = array(
			'sectionTitle' => '',
			'instruction'  => array(),
		);
		foreach ( $recipe_instructions as $recipe_instruction ) {
			if ( isset( $recipe_instruction['name'] ) || ! empty( $recipe_instruction['instructions'] ) ) {
		
				if( isset( $recipe_instruction['name'] ) && empty( $current_group['sectionTitle'] ) ) {
					$current_group   = array(
						'sectionTitle' => $recipe_instruction['name'],
						'instruction'  => array(),
					);
				}
				if ( ! empty( $recipe_instruction['instructions'] ) ) {
					foreach ( $recipe_instruction['instructions'] as $instruction ) {
						$url = isset( $instruction['image'] ) && '' != $instruction['image'] ? wp_get_attachment_image_url( $instruction['image'], 'thumbnail' ) : '';
						$current_group['instruction'][] = array(
							'instructionTitle' => "",
							'instruction'      => $instruction['text'],
							'image'            => $instruction['image'],
							'image_preview'    => $url,
							'videoURL'         => "",
							'instructionNotes' => $instruction['name'],
						);
					}
				}
				if( ! empty( $current_group['instruction'] ) ) {
					$recipe['meta']['recipeInstructions'][] = $current_group;
					$current_group = array(
						'sectionTitle' => '',
						'instruction'  => array(),
					);   
				} 
			}
		}

		// Video
		if( isset( $post_meta['wprm_video_embed'][0] ) && ! empty( $post_meta['wprm_video_embed'][0] ) ) {			
			$video_data = delicious_recipes_parse_videos( $post_meta['wprm_video_embed'][0] );
			$video_attr = isset( $video_data['0'] ) && ! empty( $video_data['0'] ) ? $video_data['0'] : array();
			
			if( ! empty( $video_attr ) ) {
				$recipe['meta']['enableVideoGallery']['0'] = "yes";
				$recipe['meta']['videoGalleryVids'] = array(
					'0' => array(
						'vidID'    => $video_attr['id'],
						'vidType'  => $video_attr['type'],
						'vidThumb' => $video_attr['thumbnail']
					)
				);
			}
		}

		return $recipe;
	}

	/**
	 * Replace the original recipe shortcode with the newly imported Delicious Recipes one.
	 *
	 * @since    1.0.0
	 * @param		 mixed $id ID of the recipe we want replace.
	 * @param		 mixed $dr_id ID of the Delicious Recipes to replace with.
	 * @param		 array $post_data POST data passed along when submitting the form.
	 */
	public function replace_recipe( $id, $dr_id, $post_data ) {
		// The recipe with ID $id has been imported and we now have a Delicious Recipe with ID $dr_id (can be the same ID).
		// $post_data will contain any input fields set in the "get_settings_html" function.
		// Use this function to do anything after the import, like replacing shortcodes.
	}

	/**
	 * Try to unserialize as best as possible.
	 *
	 * @since    1.22.0
	 * @param	 mixed $maybe_serialized Potentially serialized data.
	 */
	public function unserialize( $maybe_serialized ) {
		$unserialized = @maybe_unserialize( $maybe_serialized );

		if ( false === $unserialized ) {
			$maybe_serialized = preg_replace('/\s+/', ' ', $maybe_serialized );
			$unserialized = unserialize( preg_replace_callback( '!s:(\d+):"(.*?)";!', array( $this, 'regex_replace_serialize' ), $maybe_serialized ) );
		}

		return $unserialized;
	}

	/**
	 * Callback for regex to fix serialize issues.
	 *
	 * @since    1.20.0
	 * @param	 mixed $match Regex match.
	 */
	public function regex_replace_serialize( $match ) {
		return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
	}
}
