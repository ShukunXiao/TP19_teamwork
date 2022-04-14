<?php
/**
 * Delicious Recipe single Recipe Class
 * 
 * @package Delicious_Recipes
 * @since 1.0.0
 */
namespace WP_Delicious;

defined( 'ABSPATH' ) || exit;
/**
 * Instance of global recipe object.
 */
class Delicious_Recipes_Recipe {
    /**
	 * Get a recipe.
	 *
	 * @param mixed $recipe_id Delicious_Recipes_Recipe|WP_Post|int|bool $recipe recipe instance, post instance, numeric or false to use global $post.
	 * @return Delicious_Recipes_Recipe|bool recipe object or false if the recipe cannot be loaded.
	 */
	public function get_recipe( $recipe_id = false, $deprecated = array() ) {
		$recipe_id = $this->get_recipe_id( $recipe_id ); 

		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_data = array(
			'ID'                   => $recipe_id,
			'name'                 => get_the_title( $recipe_id ),
			'permalink'            => get_the_permalink( $recipe_id ),
			'thumbnail'            => has_post_thumbnail( $recipe_id ) ? get_the_post_thumbnail_url( $recipe_id, 'full' ) : '',
			'thumbnail_id'         => $this->get_recipe_featured_image( $recipe_id ),
			'author_id'            => $author_id = get_post_field( 'post_author', $recipe_id ),
			'author'               => get_the_author_meta( 'display_name', $author_id ),
			'date_published'       => get_the_date( 'Y-m-d', $recipe_id ),
			'excerpt'              => has_excerpt( $recipe_id ) ? get_the_excerpt( $recipe_id ) : '',
			'description'          => get_the_content( $recipe_id ),
			'recipe_description'   => $this->get_recipe_description( $recipe_id ),
			'recipe_course'        => $this->get_taxonomy_terms( $recipe_id, 'recipe-course' ),
			'recipe_cuisine'       => $this->get_taxonomy_terms( $recipe_id, 'recipe-cuisine' ),
			'cooking_method'       => $this->get_taxonomy_terms( $recipe_id, 'recipe-cooking-method' ),
			'recipe_keys'          => $this->get_taxonomy_terms( $recipe_id, 'recipe-key' ),
			'tags'                 => $this->get_taxonomy_terms( $recipe_id, 'recipe-tag' ),
			'badges'               => $this->get_taxonomy_terms( $recipe_id, 'recipe-badge' ),
			'keywords'             => $this->get_recipe_keywords( $recipe_id ),
			'recipe_subtitle'      => $this->get_recipe_subtitle( $recipe_id ),
			'difficulty_level'     => $this->get_difficulty_level( $recipe_id ),
			'prep_time'            => $this->get_preparation_time( $recipe_id ),
			'prep_time_unit'       => $this->get_preparation_time_unit( $recipe_id ),
			'cook_time'            => $this->get_cook_time( $recipe_id ),
			'cook_time_unit'       => $this->get_cook_time_unit( $recipe_id ),
			'rest_time'            => $this->get_rest_time( $recipe_id ),
			'rest_time_unit'       => $this->get_rest_time_unit( $recipe_id ),
			'total_time'           => $this->get_total_time( $recipe_id ),
			'total_time_unit'      => $this->get_total_time_unit( $recipe_id ),
			'recipe_calories'      => $this->get_recipe_calories( $recipe_id ),
			'best_season'          => $this->get_best_season( $recipe_id ),
			'ingredient_title'     => $this->get_ingredient_title( $recipe_id ),
			'no_of_servings'       => $this->get_no_of_servings( $recipe_id ),
			'ingredients'          => $this->get_ingredients( $recipe_id ),
			'instruction_title'    => $this->get_instruction_title( $recipe_id ),
			'instructions'         => $this->get_instructions( $recipe_id ),
			'enable_image_gallery' => $this->is_image_gallery_enabled( $recipe_id ),
			'image_gallery'        => $this->get_image_gallery( $recipe_id ),
			'enable_video_gallery' => $this->is_video_gallery_enabled( $recipe_id ),
			'video_gallery'        => $this->get_video_gallery( $recipe_id ),
			'comments_number'      => get_comments_number( $recipe_id ),
			'rating'               => delicious_recipes_get_average_rating( $recipe_id ),
			'rating_count'         => delicious_recipes_get_average_rating( $recipe_id, true ),
			'notes'                => $this->get_recipe_notes( $recipe_id ),
			'faqs_title'           => $this->get_faqs_title( $recipe_id ),
			'faqs'                 => $this->get_recipe_faqs( $recipe_id ),
			'nutrition'            => array(
                'servingSize'       => $this->get_nutrition_information( $recipe_id, 'servingSize' ),
                'servings'          => $this->get_nutrition_information( $recipe_id, 'servings' ),
                'calories'          => $this->get_nutrition_information( $recipe_id, 'calories' ),
                'caloriesFromFat'   => $this->get_nutrition_information( $recipe_id, 'caloriesFromFat' ),
                'totalFat'          => $this->get_nutrition_information( $recipe_id, 'totalFat' ),
                'saturatedFat'      => $this->get_nutrition_information( $recipe_id, 'saturatedFat' ),
                'transFat'          => $this->get_nutrition_information( $recipe_id, 'transFat' ),
                'cholesterol'       => $this->get_nutrition_information( $recipe_id, 'cholesterol' ),
                'sodium'            => $this->get_nutrition_information( $recipe_id, 'sodium' ),
                'potassium'         => $this->get_nutrition_information( $recipe_id, 'potassium' ),
                'totalCarbohydrate' => $this->get_nutrition_information( $recipe_id, 'totalCarbohydrate' ),
                'dietaryFiber'      => $this->get_nutrition_information( $recipe_id, 'dietaryFiber' ),
                'sugars'            => $this->get_nutrition_information( $recipe_id, 'sugars' ),
                'protein'           => $this->get_nutrition_information( $recipe_id, 'protein' ),
                'vitaminA'          => $this->get_nutrition_information( $recipe_id, 'vitaminA' ),
                'vitaminC'          => $this->get_nutrition_information( $recipe_id, 'vitaminC' ),
                'calcium'           => $this->get_nutrition_information( $recipe_id, 'calcium' ),
                'iron'              => $this->get_nutrition_information( $recipe_id, 'iron' ),
                'vitaminD'          => $this->get_nutrition_information( $recipe_id, 'vitaminD' ),
                'vitaminE'          => $this->get_nutrition_information( $recipe_id, 'vitaminE' ),
                'vitaminK'          => $this->get_nutrition_information( $recipe_id, 'vitaminK' ),
                'thiamin'           => $this->get_nutrition_information( $recipe_id, 'thiamin' ),
                'riboflavin'        => $this->get_nutrition_information( $recipe_id, 'riboflavin' ),
                'niacin'            => $this->get_nutrition_information( $recipe_id, 'niacin' ),
                'vitaminB6'         => $this->get_nutrition_information( $recipe_id, 'vitaminB6' ),
                'folate'            => $this->get_nutrition_information( $recipe_id, 'folate' ),
                'vitaminB12'        => $this->get_nutrition_information( $recipe_id, 'vitaminB12' ),
                'biotin'            => $this->get_nutrition_information( $recipe_id, 'biotin' ),
                'pantothenicAcid'   => $this->get_nutrition_information( $recipe_id, 'pantothenicAcid' ),
                'phosphorus'        => $this->get_nutrition_information( $recipe_id, 'phosphorus' ),
                'iodine'            => $this->get_nutrition_information( $recipe_id, 'iodine' ),
                'magnesium'         => $this->get_nutrition_information( $recipe_id, 'magnesium' ),
                'zinc'              => $this->get_nutrition_information( $recipe_id, 'zinc' ),
                'selenium'          => $this->get_nutrition_information( $recipe_id, 'selenium' ),
                'copper'            => $this->get_nutrition_information( $recipe_id, 'copper' ),
                'manganese'         => $this->get_nutrition_information( $recipe_id, 'manganese' ),
                'chromium'          => $this->get_nutrition_information( $recipe_id, 'chromium' ),
                'molybdenum'        => $this->get_nutrition_information( $recipe_id, 'molybdenum' ),
                'chloride'          => $this->get_nutrition_information( $recipe_id, 'chloride' ),
			),
			'wishlists_count' => $this->get_wishlists_count( $recipe_id ),
			'is_pro_active'   => delicious_recipes_is_pro_activated(),
		);

		$recipe_data_object = (object) $recipe_data;
		return $recipe_data_object;
	}
	
    /**
	 * Get the recipe ID depending on what was passed.
	 *
	 * @since  1.0.0
	 * @param  Delicious_Recipes_Recipe|WP_Post|int|bool $recipe recipe instance, post instance, numeric or false to use global $post.
	 * @return int|bool false on failure
	 */
	private function get_recipe_id( $recipe ) {
		// global $post;

		if ( isset( $recipe, $recipe->ID ) && 'recipe' === get_post_type( $recipe->ID ) ) {
			return absint( $recipe->ID );
		} else {
			return false;
		}
	}

	/**
	 * Get the recipe taxonomy terms.
	 *
	 * @since  1.0.0
	 * @param  $recipe_id, $taxonomy
	 * @return mixed
	 */
	private function get_taxonomy_terms( $recipe_id, $taxonomy ) {
		$categories = '';

        if( has_term( '', $taxonomy, $recipe_id ) ) {
            $categories = get_the_terms( $recipe_id, $taxonomy );
            if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                $categories = wp_list_pluck( $categories, 'name' );
			}
		}
		
		return $categories;
	}

	/**
	 * Get recipe metas.
	 *
	 * @param int $recipe_id
	 * @return mixed $recipe_meta | false
	 * @since 1.0.0
	 */
	private function get_recipe_metas( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = get_post_meta( $recipe_id, 'delicious_recipes_metadata', true );

		return ! empty( $recipe_meta ) ? $recipe_meta : false;
	}

	/**
	 * Get recipe Subtitle string.
	 *
	 * @param [type] $recipe_id
	 * @return void
	 */
	private function get_recipe_subtitle( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$recipe_subtitle = isset( $recipe_meta['recipeSubtitle'] ) && $recipe_meta['recipeSubtitle'] ? $recipe_meta['recipeSubtitle'] : '';

		return apply_filters( 'wp_delicious_recipe_subtitle', $recipe_subtitle, $recipe_id );
	}

	/**
	 * Get thumbnail id
	 *
	 * @param [type] $recipe_id
	 * @return void
	 */
	private function get_recipe_featured_image( $recipe_id ) {
		$recipe_thumb  = '';
		$image_gallery = $this->get_image_gallery( $recipe_id );
		if( has_post_thumbnail( $recipe_id ) ) {
			$recipe_thumb = get_post_thumbnail_id( $recipe_id );	
		} elseif ( $image_gallery && ! empty( $image_gallery && is_array( $image_gallery ) ) ) {
			$recipe_thumb = isset( $image_gallery['0']['imageID'] ) ? $image_gallery['0']['imageID'] : '';
		}
		return $recipe_thumb;
	}

	private function difficulty_levels(){
		$levels = array(
			"beginner"     => __("Beginner", 'delicious-recipes'),
			"intermediate" => __("Intermediate", 'delicious-recipes'),
			"advanced"     => __("Advanced", 'delicious-recipes')
		);

		$levels = apply_filters( 'wp_delicious_difficulty_level_options', $levels );

		return $levels;
	}

	/**
	 * Get difficulty level.
	 *
	 * @param int $recipe_id
	 * @return mixed $difficulty_level | false
	 * @since 1.0.0
	 */
	private function get_difficulty_level( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$difficulty_level = isset( $recipe_meta['difficultyLevel'] ) && $recipe_meta['difficultyLevel'] ? $recipe_meta['difficultyLevel'] : '';

		$levels = $this->difficulty_levels();

	    $difficulty_level = $difficulty_level && isset( $levels[$difficulty_level] ) ? $levels[$difficulty_level] : $difficulty_level;

		return apply_filters( 'wp_delicious_difficulty_level', $difficulty_level, $recipe_id );
	}

	/**
	 * Get preparation time.
	 *
	 * @param int $recipe_id
	 * @return mixed $preparation_time | false
	 * @since 1.0.0
	 */
	private function get_preparation_time( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$preparation_time = isset( $recipe_meta['prepTime'] ) && $recipe_meta['prepTime'] ? $recipe_meta['prepTime'] : '';

		return apply_filters( 'wp_delicious_preparation_time', $preparation_time, $recipe_id );
	}

	/**
	 * Get preparation time unit.
	 *
	 * @param int $recipe_id
	 * @return mixed $preparation_time_unit | false
	 * @since 1.0.0
	 */
	private function get_preparation_time_unit( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$preparation_time_unit = isset( $recipe_meta['prepTimeUnit'] ) && $recipe_meta['prepTimeUnit'] ? $recipe_meta['prepTimeUnit'] : '';

		$time_units = delicious_recipes_get_time_units();

	    $preparation_time_unit = $preparation_time_unit ? $time_units[$preparation_time_unit] : $preparation_time_unit;

		return apply_filters( 'wp_delicious_preparation_time_unit', $preparation_time_unit, $recipe_id );
	}

	/**
	 * Get cook time.
	 *
	 * @param int $recipe_id
	 * @return mixed $cook_time | false
	 * @since 1.0.0
	 */
	private function get_cook_time( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$cook_time = isset( $recipe_meta['cookTime'] ) && $recipe_meta['cookTime'] ? $recipe_meta['cookTime'] : '';

		return apply_filters( 'wp_delicious_cook_time', $cook_time, $recipe_id );
	}

	/**
	 * Get cook time unit.
	 *
	 * @param int $recipe_id
	 * @return mixed $cook_time_unit | false
	 * @since 1.0.0
	 */
	private function get_cook_time_unit( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$cook_time_unit = isset( $recipe_meta['cookTimeUnit'] ) && $recipe_meta['cookTimeUnit'] ? $recipe_meta['cookTimeUnit'] : '';

		$time_units = delicious_recipes_get_time_units();

	    $cook_time_unit = $cook_time_unit ? $time_units[$cook_time_unit] : $cook_time_unit;

		return apply_filters( 'wp_delicious_cook_time_unit', $cook_time_unit, $recipe_id );
	}

	/**
	 * Get rest time.
	 *
	 * @param int $recipe_id
	 * @return mixed $rest_time | false
	 * @since 1.0.0
	 */
	private function get_rest_time( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$rest_time = isset( $recipe_meta['restTime'] ) && $recipe_meta['restTime'] ? $recipe_meta['restTime'] : '';

		return apply_filters( 'wp_delicious_rest_time', $rest_time, $recipe_id );
	}

	/**
	 * Get rest time unit.
	 *
	 * @param int $recipe_id
	 * @return mixed $rest_time_unit | false
	 * @since 1.0.0
	 */
	private function get_rest_time_unit( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$rest_time_unit = isset( $recipe_meta['restTimeUnit'] ) && $recipe_meta['restTimeUnit'] ? $recipe_meta['restTimeUnit'] : '';

		$time_units = delicious_recipes_get_time_units();

	    $rest_time_unit = $rest_time_unit ? $time_units[$rest_time_unit] : $rest_time_unit;

		return apply_filters( 'wp_delicious_rest_time_unit', $rest_time_unit, $recipe_id );
	}

	/**
	 * Get Recipe description.
	 *
	 * @param [type] $recipe_id
	 * @return void
	 */
	private function get_recipe_description( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$recipe_description = isset( $recipe_meta['recipeDescription'] ) && $recipe_meta['recipeDescription'] ? $recipe_meta['recipeDescription'] : '';

		return apply_filters( 'wp_delicious_recipe_description', $recipe_description, $recipe_id );
	}

	/**
	 * Get total time.
	 *
	 * @param int $recipe_id
	 * @return mixed $total_time | false
	 * @since 1.0.0
	 */
	private function get_total_time( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$prep_time = $this->get_preparation_time( $recipe_id );
		$cook_time = $this->get_cook_time( $recipe_id );
		$rest_time = $this->get_rest_time( $recipe_id );

		$preptime_unit = isset( $recipe_meta['prepTimeUnit'] ) && $recipe_meta['prepTimeUnit'] ? $recipe_meta['prepTimeUnit'] : '';
		$cooktime_unit = isset( $recipe_meta['cookTimeUnit'] ) && $recipe_meta['cookTimeUnit'] ? $recipe_meta['cookTimeUnit'] : '';
		$resttime_unit = isset( $recipe_meta['restTimeUnit'] ) && $recipe_meta['restTimeUnit'] ? $recipe_meta['restTimeUnit'] : '';

		$PrepTimeMins = "min" === $preptime_unit ? $prep_time : $prep_time * 60;
		$CookTimeMins = "min" === $cooktime_unit ? $cook_time : $cook_time * 60;
		$RestTimeMins = "min" === $resttime_unit ? $rest_time : $rest_time * 60;

		$total_time = absint( $PrepTimeMins ) + absint( $CookTimeMins ) + absint( $RestTimeMins );

		$Hours   = absint( $total_time / 60 );
		$Minutes = $total_time % 60;

		$hour_string = '';
		$min_string  = '';

		if ( 0 < $Hours ) {
			/* translators: %s: time in hours */
			$hour_string = sprintf( _nx( '%s hr', '%s hrs', $Hours, 'total time hours', 'delicious-recipes' ), number_format_i18n( $Hours ) );
		}

		if ( 0 < $Minutes ) {
			/* translators: %s: time in minutes */
			$min_string = sprintf( _nx( '%s min', '%s mins', $Minutes, 'total time minutes', 'delicious-recipes' ), number_format_i18n( $Minutes ) );
		}

		$total_time = $hour_string . ' ' . $min_string;

		return apply_filters( 'wp_delicious_total_time', trim( $total_time ), $recipe_id );
	}

	/**
	 * Get total time unit.
	 *
	 * @param int $recipe_id
	 * @return mixed $total_time_unit | false
	 * @since 1.0.0
	 */
	private function get_total_time_unit( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$total_time_unit = isset( $recipe_meta['totalDurationUnit'] ) && $recipe_meta['totalDurationUnit'] ? $recipe_meta['totalDurationUnit'] : '';

		return apply_filters( 'wp_delicious_total_time_unit', $total_time_unit, $recipe_id );
	}

	private function best_seasons(){
		$seasons = array(
			"fall"      => __("Fall", 'delicious-recipes'),
			"winter"    => __("Winter", 'delicious-recipes'),
			"summer"    => __("Summer", 'delicious-recipes'),
			"spring"    => __("Spring", 'delicious-recipes'),
			"available" => __("Suitable throughout the year", 'delicious-recipes')
		);

		$seasons = apply_filters( 'wp_delicious_best_seasons_options', $seasons );

		return $seasons;
	}

	/**
	 * Get best season.
	 *
	 * @param int $recipe_id
	 * @return mixed $best_season | false
	 * @since 1.0.0
	 */
	private function get_best_season( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$best_season = isset( $recipe_meta['bestSeason'] ) && $recipe_meta['bestSeason'] ? $recipe_meta['bestSeason'] : '';

		$seasons = $this->best_seasons();

	    $best_season = $best_season ? $seasons[$best_season] : $best_season;

		return apply_filters( 'wp_delicious_best_season', $best_season, $recipe_id );
	}

	/**
	 * Get ingredient_title.
	 *
	 * @param int $recipe_id
	 * @return mixed $ingredient_title | false
	 * @since 1.0.0
	 */
	private function get_ingredient_title( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$ingredient_title = isset( $recipe_meta['ingredientTitle'] ) && $recipe_meta['ingredientTitle'] ? $recipe_meta['ingredientTitle'] : __( 'Ingredients', 'delicious-recipes' );

		return apply_filters( 'wp_delicious_ingredient_title', $ingredient_title, $recipe_id );
	}

	/**
	 * Get no of servings.
	 *
	 * @param int $recipe_id
	 * @return mixed $no_of_servings | false
	 * @since 1.0.0
	 */
	private function get_no_of_servings( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$no_of_servings = isset( $recipe_meta['noOfServings'] ) && $recipe_meta['noOfServings'] ? $recipe_meta['noOfServings'] : '';

		return apply_filters( 'wp_delicious_no_of_servings', $no_of_servings, $recipe_id );
	}

	/**
	 * Get ingredients.
	 *
	 * @param int $recipe_id
	 * @return mixed $ingredients | false
	 * @since 1.0.0
	 */
	private function get_ingredients( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$ingredients = isset( $recipe_meta['recipeIngredients'] ) && $recipe_meta['recipeIngredients'] ? $recipe_meta['recipeIngredients'] : '';

		return apply_filters( 'wp_delicious_ingredients', $ingredients, $recipe_id );
	}

	/**
	 * Get instruction title.
	 *
	 * @param int $recipe_id
	 * @return mixed $instruction_title | false
	 * @since 1.0.0
	 */
	private function get_instruction_title( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$instruction_title = isset( $recipe_meta['instructionsTitle'] ) && $recipe_meta['instructionsTitle'] ? $recipe_meta['instructionsTitle'] : __( 'Instructions', 'delicious-recipes' );

		return apply_filters( 'wp_delicious_instruction_title', $instruction_title, $recipe_id );
	}

	/**
	 * Get instructions.
	 *
	 * @param int $recipe_id
	 * @return mixed $instructions | false
	 * @since 1.0.0
	 */
	private function get_instructions( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$instructions = isset( $recipe_meta['recipeInstructions'] ) && $recipe_meta['recipeInstructions'] ? $recipe_meta['recipeInstructions'] : '';

		return apply_filters( 'wp_delicious_instructions', $instructions, $recipe_id );
	}

	/**
	 * Is image gallery enabled?
	 * @param int $recipe_id
	 * @return bool true | false
	 * @since 1.0.0
	 */
	private function is_image_gallery_enabled( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$enable_image_gallery = isset( $recipe_meta['enableImageGallery']['0'] ) && "yes" === $recipe_meta['enableImageGallery']['0'] ? true : false;

		return apply_filters( 'wp_delicious_enable_image_gallery', $enable_image_gallery, $recipe_id );
	}

	/**
	 * Get image gallery.
	 *
	 * @param int $recipe_id
	 * @return mixed $image_gallery | false
	 * @since 1.0.0
	 */
	private function get_image_gallery( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$image_gallery = isset( $recipe_meta['imageGalleryImages'] ) && $recipe_meta['imageGalleryImages'] ? $recipe_meta['imageGalleryImages'] : '';

		if( $image_gallery ) {
			// Get global toggles.
			$global_toggles = delicious_recipes_get_global_toggles_and_labels();

			// Image size.
			$img_size = $global_toggles['enable_recipe_image_crop'] ? 'recipe-feat-gallery' : 'full';
			foreach ( $image_gallery as $key => $image ) {
				$image_id = isset( $image['imageID'] ) ? $image['imageID'] : false;
				if( $image_id ) {
					$image_gallery[$key]['previewURL'] = wp_get_attachment_image_url( $image_id, $img_size );
				}
			}
		}

		return apply_filters( 'wp_delicious_image_gallery', $image_gallery, $recipe_id );
	}

	/**
	 * Is video gallery enabled?
	 * @param int $recipe_id
	 * @return bool true | false
	 * @since 1.0.0
	 */
	private function is_video_gallery_enabled( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$enable_video_gallery = isset( $recipe_meta['enableVideoGallery']['0'] ) && "yes" === $recipe_meta['enableVideoGallery']['0'] ? true : false;

		return apply_filters( 'wp_delicious_enable_video_gallery', $enable_video_gallery, $recipe_id );
	}

	/**
	 * Get video gallery.
	 */
	function get_video_gallery( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$video_gallery = isset( $recipe_meta['videoGalleryVids'] ) && ! empty( $recipe_meta['videoGalleryVids'] ) ? $recipe_meta['videoGalleryVids'] : array();

		return apply_filters( 'wp_delicious_video_gallery', $video_gallery, $recipe_id );
	}

	/**
	 * Get Nutrition field value information.
	 *
	 * @param $recipe_id
	 * @return mixed $nutrition_info | false
	 * @since 1.0.0
	 */
	private function get_nutrition_information( $recipe_id, $field ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$nutrition_info = isset( $recipe_meta[$field] ) && 
		( '' != $recipe_meta[$field] || 0 === $recipe_meta[$field] ) ? $recipe_meta[$field] : '';

		return apply_filters( "wp_delicious_nutrition_{$field}", $nutrition_info, $recipe_id );
	}

	/**
	 * Get Recipe calories.
	 *
	 * @param [type] $recipe_id
	 * @return void
	 */
	private function get_recipe_calories( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$recipe_calories = isset( $recipe_meta['recipeCalories'] ) && ! empty( $recipe_meta['recipeCalories'] ) ? $recipe_meta['recipeCalories'] : '';

		return apply_filters( 'wp_delicious_recipe_calories', $recipe_calories, $recipe_id );
	}

	/**
	 * Get Recipe notes.
	 *
	 * @param [type] $recipe_id
	 * @return void
	 */
	private function get_recipe_notes( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$recipe_notes = isset( $recipe_meta['recipeNotes'] ) && $recipe_meta['recipeNotes'] ? $recipe_meta['recipeNotes'] : '';

		return apply_filters( 'wp_delicious_recipe_notes', $recipe_notes, $recipe_id );
	}

	/**
	 * Get faqs title.
	 *
	 * @param int $recipe_id
	 * @return mixed $faqs_title | false
	 */
	private function get_faqs_title( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$faqs_title = isset( $recipe_meta['faqsTitle'] ) ? $recipe_meta['faqsTitle'] : __( 'Frequently Asked Questions', 'delicious-recipes' );

		return apply_filters( 'wp_delicious_recipe_faqs_title', $faqs_title, $recipe_id );
	}

	/**
	 * Get Recipe FAQs
	 */
	private function get_recipe_faqs( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$recipe_faqs = isset( $recipe_meta['recipeFAQs'] ) && $recipe_meta['recipeFAQs'] ? $recipe_meta['recipeFAQs'] : '';

		return apply_filters( 'wp_delicious_recipe_faqs', $recipe_faqs, $recipe_id );
	}

	/**
	 * Get recipe keywords.
	 *
	 * @param [type] $recipe_id
	 * @return void
	 */
	private function get_recipe_keywords( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$recipe_meta = $this->get_recipe_metas( $recipe_id );

		if ( ! $recipe_meta ) {
			return false;
		}

		$recipe_keywords = isset( $recipe_meta['recipeKeywords'] ) && $recipe_meta['recipeKeywords'] ? $recipe_meta['recipeKeywords'] : '';

		return apply_filters( 'wp_delicious_recipe_keywords', $recipe_keywords, $recipe_id );
	}

	/**
	 * Get recipe wishlists count.
	 *
	 * @param [type] $recipe_id
	 * @return void
	 */
	private function get_wishlists_count( $recipe_id ) {
		if ( ! $recipe_id ) {
			return false;
		}

		$wishlists_count = get_post_meta( $recipe_id, '_delicious_recipes_wishlists', true );

		if ( ! $wishlists_count ) {
			return false;
		}

		$wishlists_count = isset( $wishlists_count ) && $wishlists_count ? $wishlists_count : 0;

		return apply_filters( 'wp_delicious_recipe_wishlists_count', $wishlists_count, $recipe_id );
	}

}
