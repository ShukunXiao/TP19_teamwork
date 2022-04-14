<?php
/**
 * Delicious_Recipes Core Functions.
 *
 * General core functions avaiable on both the front-end and backend.
 *
 * @package Delicious_Recipes\Functions
 *
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrapper for _doing_it_wrong().
 *
 * @since  1.0.0
 * @param string $function Function used.
 * @param string $message Message to log.
 * @param string $version Version the message was added in.
 */
function delicious_recipes_doing_it_wrong( $function, $message, $version ) {
	// @codingStandardsIgnoreStart
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();
	
	_doing_it_wrong( $function, $message, $version );
	// @codingStandardsIgnoreEnd
}

/**
 * Define a constant if is is not already defined.
 *
 * @since 1.0.0
 * @param string $name Constant name.
 * @param string $value Constant value.
 */
function delicious_recipes_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @since 1.0.0
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @return string Template path.
 */
function delicious_recipes_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = DEL_RECIPE()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = DEL_RECIPE()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit(  $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template.
	if ( ! $template || DELICIOUS_RECIPES_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'wp_delicious_locate_template', $template, $template_name, $template_path );
}

/**
 * Get other templates (e.g. article attributes) passing attributes and including the file.
 *
 * @since 1.0.0
 *
 * @param string $template_name   Template name.
 * @param array  $args            Arguments. (default: array).
 * @param string $template_path   Template path. (default: '').
 * @param string $default_path    Default path. (default: '').
 */
function delicious_recipes_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	$cache_key = sanitize_key( implode( '-', array( 'template', $template_name, $template_path, $default_path, DELICIOUS_RECIPES_VERSION ) ) );
	$template = (string) wp_cache_get( $cache_key, 'delicious-recipes' );

	if ( ! $template ) {
		$template = delicious_recipes_locate_template( $template_name, $template_path, $default_path );
		wp_cache_set( $cache_key, $template, 'delicious-recipes' );
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'wp_delicious_get_template', $template, $template_name, $args, $template_path, $default_path );

	if( $filter_template !== $template ) {
		if ( ! file_exists( $filter_template ) ) {
			/* translators: %s template */
			delicious_recipes_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'delicious-recipes' ), '<code>' . $template . '</code>' ), '1.0.0' );
			return;
		}
		$template = $filter_template;
	}

	$action_args = array(
		'template_name' => $template_name,
		'template_path' => $template_path,
		'located'       => $template,
		'args'          => $args,
	);

	if ( ! empty( $args ) && is_array( $args ) ) {
		if ( isset( $args['action_args'] ) ) {
			delicious_recipes_doing_it_wrong(
				__FUNCTION__,
				__( 'action_args should not be overwritten when calling delicious_recipes_get_template.', 'delicious-recipes' ),
				'1.0.0'
			);
			unset( $args['action_args'] );
		}
		extract( $args );
	}

	do_action( 'wp_delicious_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	include $action_args['located'];

	do_action( 'wp_delicious_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}

/**
 * Get template part.
 *
 * DELICIOUS_RECIPES_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
 *
 * @param mixed $slug Template slug.
 * @param string $name Template name (default: '').
 *
 */
function delicious_recipes_get_template_part( $slug, $name = '' ) {
	$cache_key = sanitize_key( implode( '-', array( 'template-part', $slug, $name, DELICIOUS_RECIPES_VERSION ) ) );
	$template  = (string) wp_cache_get( $cache_key, 'delicious-recipes' );

	if ( ! $template ) {
		if ( $name ) {
			$template = DELICIOUS_RECIPES_TEMPLATE_DEBUG_MODE ? '' : locate_template(
				array(
					"{$slug}-{$name}.php",
					DEL_RECIPE()->template_path() . "{$slug}-{$name}.php",
				)
			);

			if ( ! $template ) {
				$fallback = DEL_RECIPE()->plugin_path() . "/templates/{$slug}-{$name}.php";
				$template = file_exists( $fallback ) ? $fallback : '';
			}
		}

		if ( ! $template ) {
			// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/delicious-recipes/slug.php.
			$template = DELICIOUS_RECIPES_TEMPLATE_DEBUG_MODE ? '' : locate_template(
				array(
					"{$slug}.php",
					DEL_RECIPE()->template_path() . "{$slug}.php",
				)
			);
		}

		wp_cache_set( $cache_key, $template, 'delicious-recipes' );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'wp_delicious_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Like delicious_recipes_get_template, but return the HTML instaed of outputting.
 *
 * @see delicious_recipes_get_template
 * @since 1.0.0
 *
 * @param string $template_name Template name.
 * @param array $args           Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 *
 * @return string.
 */
function delicious_recipes_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
    delicious_recipes_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Merge user defined arguments into defaults array.
 * Similar to wp_parse_args() just a bit extended to work with multidimensional arrays.
 *
 * @since 1.0.0
 *
 * @param array $args      (Required) Value to merge with $defaults.
 * @param array $defaults  Array that serves as the defaults. Default value: ''


 * @return void
 */
function delicious_recipes_wp_parse_args( &$args, $defaults = '' ) {
	$args     = (array) $args;
	$defaults = (array) $defaults;
	$result   = $defaults;

	foreach ( $args as $key => &$value ) {
		if ( is_array( $value ) && ! empty( $value ) && isset( $result[ $key ] ) ) {
			$result[ $key ] = delicious_recipes_wp_parse_args( $value, $result[ $key ] );
		} else {
			$result[ $key ] = $value;
		}
	}
	return $result;
}

/**
 * Generate array of any.
 *
 * @since 1.0.0
 *
 * @param any $arr    Multipe object to create.
 * @param int $count  Number of objects.
 * @return array      List of object passed.
 */
function delicious_recipes_generate_arrays( $arr, $count ) {
	$result = array();

	for( $index = 0; $index < $count; $index++ ) {
		array_push( $result, $arr );
	}

	return $result;
}

/**
 * Checks whether the content passed contains a specific shortcode.
 *
 * @param  string $tag Shortcode tag to check.
 * @return bool
 */
function delicious_recipes_post_content_has_shortcode( $tag = '' ) {
	global $post;

	return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
}

/**
 * Retrieve page ids - used for myaccount, edit_address, shop, cart, checkout, pay, view_order, terms. returns -1 if no page is found.
 *
 * @param string $page Page slug.
 * @return int
 */
function delicious_recipes_get_page_id( $page ) {
	$page = apply_filters( 'delicious_recipes_get_' . $page . '_page_id', get_option( 'delicious_recipes_' . $page . '_page_id' ) );

	return $page ? absint( $page ) : -1;
}

/**
 * Is_recipe_taxonomy - Returns true when viewing a recipe taxonomy archive.
 *
 * @return bool
 */
function is_recipe_taxonomy() {
	return is_tax( get_object_taxonomies( DELICIOUS_RECIPE_POST_TYPE ) );
}

/**
 * Is_recipe - Returns true when viewing a single recipe.
 *
 * @return bool
 */
function is_recipe() {
	return is_singular( array( 'recipe' ) );
}

/**
 * Is_recipe_search - Returns true when viewing a recipe search page.
 *
 * @return bool
 */
function is_recipe_search() {
	return is_page( delicious_recipes_get_page_id( 'recipe-search' ) ) || delicious_recipes_post_content_has_shortcode( 'recipe_search' );
}

/**
 * Is_recipe_search - Returns true when viewing a recipe search page.
 *
 * @return bool
 */
function is_recipe_dashboard() {
	return is_page( delicious_recipes_get_page_id( 'recipe-dashboard' ) ) || delicious_recipes_post_content_has_shortcode( 'dr_user_dashboard' );
}

/**
 * Is_recipe_block - Returns true when viewing a recipe block page.
 *
 * @return bool
 */
function is_recipe_block() {
	return has_block( 'delicious-recipes/handpicked-recipes' ) || has_block( 'delicious-recipes/tax-type' ) 
	|| has_block( 'delicious-recipes/recipe-card' ) || has_block( 'delicious-recipes/dynamic-details' )
	|| has_block( 'delicious-recipes/dynamic-ingredients' ) || has_block( 'delicious-recipes/dynamic-instructions' )
	|| has_block( 'delicious-recipes/dynamic-recipe-card' ) || has_block( 'delicious-recipes/block-nutrition' )
	|| has_block( 'delicious-recipes/block-recipe-buttons' );
}

/**
 * Is_recipe_shortcode - Returns true when viewing a recipe shortcode page.
 *
 * @return bool
 */
function is_recipe_shortcode() {
	return delicious_recipes_post_content_has_shortcode( 'recipe_page' ) 
		|| delicious_recipes_post_content_has_shortcode( 'dr_featured_recipes' ) 
		|| delicious_recipes_post_content_has_shortcode( 'dr_popular_recipes' ) 
		||delicious_recipes_post_content_has_shortcode( 'dr_recipes' ) 
		|| delicious_recipes_post_content_has_shortcode( 'print_recipe' )
		|| delicious_recipes_post_content_has_shortcode( 'recipe_card' )
		|| delicious_recipes_post_content_has_shortcode( 'dr_surprise_me' )
		|| delicious_recipes_post_content_has_shortcode( 'dr_user_dashboard' );
}

/**
 * Get Recipe data.
 *
 * @param [type] $recipe
 * @return void
 */
function delicious_recipes_get_recipe( $recipe ) {
	return DEL_RECIPE()->recipe->get_recipe( $recipe );
}

/**
 * When the_post is called, put recipe data into a global.
 *
 * @param mixed $post Post Object.
 * @return Delicious_Recipes_Recipe
 */
function delicious_recipes_setup_recipe_data( $post ) {
	unset( $GLOBALS['recipe'] );

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}

	if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'recipe' ), true ) ) {
		return;
	}

	$GLOBALS['recipe'] = delicious_recipes_get_recipe( $post );

	return $GLOBALS['recipe'];
}

add_action( 'the_post', 'delicious_recipes_setup_recipe_data' );

/**
 * Delicious recipes get global settings.
 *
 * @return void
 */
function delicious_recipes_get_global_settings() {

	$settings = get_option( 'delicious_recipe_settings' ) ? get_option( 'delicious_recipe_settings' ) : array();

	$global_defaults = apply_filters( 'delicious_recipes_global_settings_defaults', array(
		// General fields
		'displayRecipesOnHomepage' => [],
		'recipePerPage'            => get_option( 'posts_per_page' ),

		// Recipe Details fields
		'enableRecipeSingleHead' => [
			'0' => 'yes'
		],
		'enableRecipeFeaturedImage' => [
			'0' => 'yes'
		],
		'ingredientStringFormat' => "{qty} {unit} {ingredient} {notes}",
		'recipeToggles'          => [
			'0' => [
				'label'  => __("Author", 'delicious-recipes'),
				'key'    => "author",
				'id'     => "dr-author",
				'enable' => [
					'0' => "yes"
				],
			],
			'1' => [
				'label'  => __("Courses", 'delicious-recipes'),
				'key'    => "category",
				'id'     => "dr-category",
				'enable' => [
					'0' => "yes"
				],
			],
			'2' => [
				'label'  => __("Cooking Method", 'delicious-recipes'),
				'key'    => "cookingMethod",
				'id'     => "dr-cooking-method",
				'enable' => [
					'0' => "yes"
				],
			],
			'3' => [
				'label'  => __("Cuisine", 'delicious-recipes'),
				'key'    => "cuisine",
				'id'     => "dr-cuisine",
				'enable' => [
					'0' => "yes"
				],
			],
			'4' => [
				'label'  => __("Difficulty", 'delicious-recipes'),
				'key'    => "difficultyLevel",
				'id'     => "dr-difficulty-level",
				'enable' => [
					'0' => "yes"
				],
			],
			'5' => [
				'label'  => __("Description", 'delicious-recipes'),
				'key'    => "description",
				'id'     => "dr-description",
				'enable' => [
					'0' => "yes"
				],
			],
			'6' => [
				'label'  => __("Prep Time", 'delicious-recipes'),
				'key'    => "prepTime",
				'id'     => "dr-prep-time",
				'enable' => [
					'0' => "yes"
				],
			],
			'7' => [
				'label'  => __("Cook Time", 'delicious-recipes'),
				'key'    => "cookTime",
				'id'     => "dr-cook-time",
				'enable' => [
					'0' => "yes"
				],
			],
			'8' => [
				'label'  => __("Rest Time", 'delicious-recipes'),
				'key'    => "restTime",
				'id'     => "dr-rest-time",
				'enable' => [
					'0' => "yes"
				],
			],
			'9' => [
				'label'  => __("Total Time", 'delicious-recipes'),
				'key'    => "totalTime",
				'id'     => "dr-total-time",
				'enable' => [
					'0' => "yes"
				],
			],
			'10' => [
				'label'  => __("Servings", 'delicious-recipes'),
				'key'    => "servings",
				'id'     => "dr-servings",
				'enable' => [
					'0' => "yes"
				],
			],
			'11' => [
				'label'  => __("Calories", 'delicious-recipes'),
				'key'    => "calories",
				'id'     => "dr-calories",
				'enable' => [
					'0' => "yes"
				],
			],
			'12' => [
				'label'  => __("Best Season", 'delicious-recipes'),
				'key'    => "bestSeason",
				'id'     => "dr-best-season",
				'enable' => [
					'0' => "yes"
				],
			],
			'13' => [
				'label'  => __("Recipe Keys", 'delicious-recipes'),
				'key'    => "recipeKeys",
				'id'     => "dr-recipe-keys",
				'enable' => [
					'0' => "yes"
				],
			],
			'14' => [
				'label'  => __("Video", 'delicious-recipes'),
				'key'    => "video",
				'id'     => "dr-video",
				'enable' => [
					'0' => "yes"
				],
			],
			'15' => [
				'label'  => __("Jump To Recipe", 'delicious-recipes'),
				'key'    => "jumpToRecipe",
				'id'     => "dr-jump-to-recipe",
				'enable' => [
					'0' => "yes"
				],
			],
			'16' => [
				'label'  => __("Keywords", 'delicious-recipes'),
				'key'    => "keywords",
				'id'     => "dr-keywords",
				'enable' => [
					'0' => "yes"
				],
			],
			'17' => [
				'label'  => __("File under", 'delicious-recipes'),
				'key'    => "fileUnder",
				'id'     => "dr-file-under",
				'enable' => [
					'0' => "yes"
				],
			],
			'18' => [
				'label'  => __("Note", 'delicious-recipes'),
				'key'    => "notes",
				'id'     => "dr-notes",
				'enable' => [
					'0' => "yes"
				],
			],
			'19' => [
				'label'  => __("Jump To Video", 'delicious-recipes'),
				'key'    => "jumpToVideo",
				'id'     => "dr-jump-to-video",
				'enable' => [
					'0' => "yes"
				],
			],
			'20' => [
				'label'  => __("Mark as complete", 'delicious-recipes'),
				'key'    => "markAsComplete",
				'id'     => "dr-mark-as-complete",
				'enable' => [],
			],
			'21' => [
				'label'  => __("Add to Favorites", 'delicious-recipes'),
				'key'    => "addToWishlist",
				'id'     => "dr-add-to-wishlist",
				'enable' => [
					'0' => "yes"
				],
			],
		],
		'enableNavigation' => [
			'0' => "yes"
		],
		'enablePoweredBy' => [
			'0' => "yes"
		],
		'affiliateLink' => '',
		'enableRecipeAuthor' => [
			'0' => "yes"
		],
		'enablePublishedDate' => [
			'0' => "yes"
		],
		'enableComments' => [
			'0' => "yes"
		],
		'enableRecipeImageCrop' => [
			'0' => "yes"
		],
		'enableRecipeArchiveImageCrop' => [
			'0' => "yes"
		],
		'enablePinit' => [
			'0' => "yes"
		],

		// Recipe Archive fields
		'enableArchiveHeader'   => [],
		'archiveTitle'          => __("Recipe Index", 'delicious-recipes'),
		'archiveDescription'    => "",
		'taxPagesTermsBoxTitle' => __( 'Narrow Your Search', 'delicious-recipes' ),
		'defaultArchiveLayout'  => "grid",

		// Appearance fields
		'enablePluginTypography' => [
			'0' => "yes"
		],
		'primaryColor'      => "#2db68d",
		'primaryColorRGB'   => '45, 182, 141',
		'secondaryColor'    => "#279bc2",
		'secondaryColorRGB' => '232, 78, 59',
		'defaultCardLayout' => "default",

		// Permalink fields
		'recipeBase'        => "recipe",
		'courseBase'        => "recipe-course",
		'cuisineBase'       => "recipe-cuisine",
		'cookingMethodBase' => "recipe-cooking-method",
		'keyBase'           => "recipe-key",
		'tagBase'           => "recipe-tag",
		'badgeBase'         => "recipe-badge",

		// Social Sharing fields
		'enableSocialShare' => [],
		'recipeShareTitle'  => __("Did you make this recipe?", 'delicious-recipes'),
		'socialShare'       => [
			'0' => [
				'social'  => "Instagram",
				'enable'  => [],
				'content' => "",
			],
			'1' => [
				'social'  => "Pinterest",
				'enable'  => [],
				'content' => "",
			],
		],

		// Review fields
		'enableRatings' => [
			'0' => "yes"
		],
		'ratingLabel' => __("Rate this recipe", 'delicious-recipes'),

		// Author fields
		'enableAuthorProfile'     => "",
		'showAuthorArchiveHeader' => "",
		'recipeAuthor'            => '',
		'authorName'              => "",
		'authorSubtitle'          => "",
		'authorDescription'       => "",
		'authorImage'             => "",
		'authorImagePreview'      => "",
		'showAuthorProfileLinks'  => [
			'0' => "yes"
		],
		'facebookLink'  => "",
		'instagramLink' => "",
		'pinterestLink' => "",
		'twitterLink'   => "",
		'youtubeLink'   => "",
		'snapchatLink'  => "",
		'linkedinLink'  => "",

		// Print fields
		'enablePrintRecipeBtn' => [
			'0' => "yes"
		],
		'printRecipeBtnText'      => __( "Print Recipe", 'delicious-recipes' ),
		'printLogoImage'          => "",
		'printLogoImagePreview'   => "",
		'allowPrintCustomization' => [
			'0' => "yes"
		],
		'printOptions' => [
			'0' => [
				'key'    => __( "Title", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
			'1' => [
				'key'    => __( "Info", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
			'2' => [
				'key'    => __( "Description", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
			'3' => [
				'key'    => __( "Images", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
			'4' => [
				'key'    => __( "Ingredients", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
			'5' => [
				'key'    => __( "Instructions", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
			'6' => [
				'key'    => __( "Nutrition", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
			'7' => [
				'key'    => __( "Notes", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
			'8' => [
				'key'    => __( "Social Share", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
			'9' => [
				'key'    => __( "Author Bio", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
			'10' => [
				'key'    => __( "Thank You Note", 'delicious-recipes' ),
				'enable' => [
					'0' => "yes"
				],
			],
		],
		'embedRecipeLink' => [
			'0' => "yes"
		],
		'recipeLinkLabel'          => __( "Read it online:", 'delicious-recipes' ),
		'displaySocialSharingInfo' => [
			'0' => "yes"
		],
		'embedAuthorInfo' => [
			'0' => "yes"
		],
		'thankyouMessage' => "",

		// Nutrition fields
		'showNutritionFacts' => [
			'0' => "yes"
		],
		'nutritionFactsLabel'  => __( "Nutrition Facts", 'delicious-recipes' ),
		'dailyValueDisclaimer' => 
			__( "Percent Daily Values are based on a 2,000 calorie diet. Your daily value may be higher or lower depending on your calorie needs.", 'delicious-recipes' ),
		'displayStandardMode'        => '',
		'displayNutritionZeroValues' => '',

		// Search fields
		'searchPage'       => "",
		'displaySearchBar' => [
			'0' => "yes"
		],
		'searchFilters' => [
			'0' => [
				'label'  => __("Season", 'delicious-recipes'),
				'key'    => "season",
				'enable' => [
					'0' => "yes"
				],
			],
			'1' => [
				'label'  => __("Cuisine", 'delicious-recipes'),
				'key'    => "cuisine",
				'enable' => [
					'0' => "yes"
				],
			],
			'2' => [
				'label'  => __("Recipe Type", 'delicious-recipes'),
				'key'    => "recipe-type",
				'enable' => [
					'0' => "yes"
				],
			],
			'3' => [
				'label'  => __("Cooking Method", 'delicious-recipes'),
				'key'    => "cooking-method",
				'enable' => [
					'0' => "yes"
				],
			],
			'4' => [
				'label'  => __("Difficulty", 'delicious-recipes'),
				'key'    => "difficulty",
				'enable' => [
					'0' => "yes"
				],
			],
			'5' => [
				'label'  => __("Ingredients", 'delicious-recipes'),
				'key'    => "ingredients",
				'enable' => [
					'0' => "yes"
				],
			],
			'6' => [
				'label'  => __("Simple Factor", 'delicious-recipes'),
				'key'    => "simple-factor",
				'enable' => [
					'0' => 'yes'
				],
			],
			'7' => [
				'label'  => __("Sorting", 'delicious-recipes'),
				'key'    => "sorting",
				'enable' => [
					'0' => 'yes'
				],
			],
			'8' => [
				'label'  => __("Recipe Keys", 'delicious-recipes'),
				'key'    => "recipe-keys",
				'enable' => [],
			],
			'9' => [
				'label'  => __("Recipe Tags", 'delicious-recipes'),
				'key'    => "recipe-tags",
				'enable' => [],
			],
			'10' => [
				'label'  => __("Reset", 'delicious-recipes'),
				'key'    => "reset",
				'enable' => [
					'0' => 'yes'
				],
			],
			'11' => [
				'label'  => __("Recipe Badges", 'delicious-recipes'),
				'key'    => "recipe-badges",
				'enable' => [
					'0' => 'yes'
				],
			],
		],

		//Miscellaneous fields
		'disableFAIconsJS' => [],
		
		// User Dashboard fields
		'dashboardPage'          => "",
		'enableUserRegistration' => [
			'0' => 'yes'
		],
		'generateUsername'         => [],
		'generatePassword'         => [],
		'termsNConditions'         => [],
		'termsNConditionsText'     => __("By signing up, you agree to our Terms, Data Policy and Cookies Policy.", 'delicious-recipes'),
		'loginImage'               => "",
		'loginImagePreview'        => "",
		'registrationImage'        => "",
		'registrationImagePreview' => "",

		// Email Templates fields
		'newAccountSubject'    => __( "Your {site_title} account has been created!", 'delicious-recipes' ),
		'newAccountContent'    => "",
		'resetPasswordSubject' => __("Password Reset Request for {site_title}", 'delicious-recipes'),
		'resetPasswordContent' => "",
		) );

		$settings = delicious_recipes_wp_parse_args( $settings, $global_defaults );
		
		return $settings;
	}
	
	/**
	 * Delicious recipes get global toggles and labels.
	 *
	 * @return void
 */
function delicious_recipes_get_global_toggles_and_labels() {
	$global_settings = delicious_recipes_get_global_settings();

	// Display recipes on Home Page
	$display_recipes_on_home_page   = isset( $global_settings['displayRecipesOnHomepage']['0'] ) && 'yes' === $global_settings['displayRecipesOnHomepage']['0'] ? true : false;

	// Enable Featured Image in Recipe Single
	$enable_recipe_featured_image   = isset( $global_settings['enableRecipeFeaturedImage']['0'] ) && 'yes' === $global_settings['enableRecipeFeaturedImage']['0'] ? true : false;

	// Author Toggles and Labels
	$enable_author = isset( $global_settings['recipeToggles']['0']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['0']['enable']['0'] ? true : false;
	$author_lbl    = isset( $global_settings['recipeToggles']['0']['label'] ) && '' != $global_settings['recipeToggles']['0']['label'] ? $global_settings['recipeToggles']['0']['label'] : __( 'Author', 'delicious-recipes' );

	// Category Toggles and Labels
	$enable_category = isset( $global_settings['recipeToggles']['1']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['1']['enable']['0'] ? true : false;
	$category_lbl    = isset( $global_settings['recipeToggles']['1']['label'] ) && '' != $global_settings['recipeToggles']['1']['label'] ? $global_settings['recipeToggles']['1']['label'] : __( 'Category', 'delicious-recipes' );

	// Cooking Method Toggles and Labels
	$enable_cooking_method = isset( $global_settings['recipeToggles']['2']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['2']['enable']['0'] ? true : false;
	$cooking_method_lbl    = isset( $global_settings['recipeToggles']['2']['label'] ) && '' != $global_settings['recipeToggles']['2']['label'] ? $global_settings['recipeToggles']['2']['label'] : __( 'Cooking Method', 'delicious-recipes' );

	// Cuisine Toggles and Labels
	$enable_cuisine = isset( $global_settings['recipeToggles']['3']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['3']['enable']['0'] ? true : false;
	$cuisine_lbl    = isset( $global_settings['recipeToggles']['3']['label'] ) && '' != $global_settings['recipeToggles']['3']['label'] ? $global_settings['recipeToggles']['3']['label'] : __( 'Cuisine', 'delicious-recipes' );

	// Difficulty Level Toggles and Labels
	$enable_difficulty_level = isset( $global_settings['recipeToggles']['4']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['4']['enable']['0'] ? true : false;
	$difficulty_level_lbl    = isset( $global_settings['recipeToggles']['4']['label'] ) && '' != $global_settings['recipeToggles']['4']['label'] ? $global_settings['recipeToggles']['4']['label'] : __( 'Difficulty', 'delicious-recipes' );

	// Description Toggles and Labels
	$enable_description = isset( $global_settings['recipeToggles']['5']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['5']['enable']['0'] ? true : false;
	$description_lbl    = isset( $global_settings['recipeToggles']['5']['label'] ) && '' != $global_settings['recipeToggles']['5']['label'] ? $global_settings['recipeToggles']['5']['label'] : __( 'Description', 'delicious-recipes' );

	// Prep Time Toggles and Labels
	$enable_prep_time = isset( $global_settings['recipeToggles']['6']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['6']['enable']['0'] ? true : false;
	$prep_time_lbl    = isset( $global_settings['recipeToggles']['6']['label'] ) && '' != $global_settings['recipeToggles']['6']['label'] ? $global_settings['recipeToggles']['6']['label'] : __( 'Prep Time', 'delicious-recipes' );

	// Cook Time Toggles and Labels
	$enable_cook_time = isset( $global_settings['recipeToggles']['7']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['7']['enable']['0'] ? true : false;
	$cook_time_lbl    = isset( $global_settings['recipeToggles']['7']['label'] ) && '' != $global_settings['recipeToggles']['7']['label'] ? $global_settings['recipeToggles']['7']['label'] : __( 'Cook Time', 'delicious-recipes' );

	// Rest Time Toggles and Labels
	$enable_rest_time = isset( $global_settings['recipeToggles']['8']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['8']['enable']['0'] ? true : false;
	$rest_time_lbl    = isset( $global_settings['recipeToggles']['8']['label'] ) && '' != $global_settings['recipeToggles']['8']['label'] ? $global_settings['recipeToggles']['8']['label'] : __( 'Rest Time', 'delicious-recipes' );

	// Total Time Toggles and Labels
	$enable_total_time = isset( $global_settings['recipeToggles']['9']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['9']['enable']['0'] ? true : false;
	$total_time_lbl    = isset( $global_settings['recipeToggles']['9']['label'] ) && '' != $global_settings['recipeToggles']['9']['label'] ? $global_settings['recipeToggles']['9']['label'] : __( 'Total Time', 'delicious-recipes' );

	// Servings Toggles and Labels
	$enable_servings = isset( $global_settings['recipeToggles']['10']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['10']['enable']['0'] ? true : false;
	$servings_lbl    = isset( $global_settings['recipeToggles']['10']['label'] ) && '' != $global_settings['recipeToggles']['10']['label'] ? $global_settings['recipeToggles']['10']['label'] : __( 'Servings', 'delicious-recipes' );

	// Calories Toggles and Labels
	$enable_calories = isset( $global_settings['recipeToggles']['11']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['11']['enable']['0'] ? true : false;
	$calories_lbl    = isset( $global_settings['recipeToggles']['11']['label'] ) && '' != $global_settings['recipeToggles']['11']['label'] ? $global_settings['recipeToggles']['11']['label'] : __( 'Calories', 'delicious-recipes' );

	// Season Toggles and Labels
	$enable_seasons = isset( $global_settings['recipeToggles']['12']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['12']['enable']['0'] ? true : false;
	$seasons_lbl    = isset( $global_settings['recipeToggles']['12']['label'] ) && '' != $global_settings['recipeToggles']['12']['label'] ? $global_settings['recipeToggles']['12']['label'] : __( 'Best Season', 'delicious-recipes' );

	// Recipe Keys Toggles and Labels
	$enable_recipe_keys = isset( $global_settings['recipeToggles']['13']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['13']['enable']['0'] ? true : false;
	$recipe_keys_lbl    = isset( $global_settings['recipeToggles']['13']['label'] ) && '' != $global_settings['recipeToggles']['13']['label'] ? $global_settings['recipeToggles']['13']['label'] : __( 'Recipe Keys', 'delicious-recipes' );

	// Video Toggles and Labels
	$enable_video = isset( $global_settings['recipeToggles']['14']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['14']['enable']['0'] ? true : false;
	$video_lbl    = isset( $global_settings['recipeToggles']['14']['label'] ) && '' != $global_settings['recipeToggles']['14']['label'] ? $global_settings['recipeToggles']['14']['label'] : __( 'Video', 'delicious-recipes' );

	// Jump to Recipe Toggles and Labels
	$enable_jump_to_recipe = isset( $global_settings['recipeToggles']['15']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['15']['enable']['0'] ? true : false;
	$jump_to_recipe_lbl    = isset( $global_settings['recipeToggles']['15']['label'] ) && '' != $global_settings['recipeToggles']['15']['label'] ? $global_settings['recipeToggles']['15']['label'] : __( 'Jump To Recipe', 'delicious-recipes' );

	// Keywords Toggles and Labels
	$enable_keywords = isset( $global_settings['recipeToggles']['16']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['16']['enable']['0'] ? true : false;
	$keywords_lbl    = isset( $global_settings['recipeToggles']['16']['label'] ) && '' != $global_settings['recipeToggles']['16']['label'] ? $global_settings['recipeToggles']['16']['label'] : __( 'Keywords', 'delicious-recipes' );

	// File under Toggles and Labels
	$enable_file_under = isset( $global_settings['recipeToggles']['17']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['17']['enable']['0'] ? true : false;
	$file_under_lbl    = isset( $global_settings['recipeToggles']['17']['label'] ) && '' != $global_settings['recipeToggles']['17']['label'] ? $global_settings['recipeToggles']['17']['label'] : __( 'File under', 'delicious-recipes' );

	// Notes Toggles and Labels
	$enable_notes = isset( $global_settings['recipeToggles']['18']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['18']['enable']['0'] ? true : false;
	$notes_lbl    = isset( $global_settings['recipeToggles']['18']['label'] ) && '' != $global_settings['recipeToggles']['18']['label'] ? $global_settings['recipeToggles']['18']['label'] : __( 'Notes', 'delicious-recipes' );

	// Jump to Video Toggles and Labels
	$enable_jump_to_video = isset( $global_settings['recipeToggles']['19']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['19']['enable']['0'] ? true : false;
	$jump_to_video_lbl    = isset( $global_settings['recipeToggles']['19']['label'] ) && '' != $global_settings['recipeToggles']['19']['label'] ? $global_settings['recipeToggles']['19']['label'] : __( 'Jump To Video', 'delicious-recipes' );

	// Mark as complete Toggles and Labels
	$enable_mark_as_complete = isset( $global_settings['recipeToggles']['20']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['20']['enable']['0'] ? true : false;
	$mark_as_complete_lbl    = isset( $global_settings['recipeToggles']['20']['label'] ) && '' != $global_settings['recipeToggles']['20']['label'] ? $global_settings['recipeToggles']['20']['label'] : __( 'Mark as complete', 'delicious-recipes' );

	// Add to Wishlist Toggles and Labels
	$enable_add_to_wishlist = isset( $global_settings['recipeToggles']['21']['enable']['0'] ) && 'yes' === $global_settings['recipeToggles']['21']['enable']['0'] ? true : false;
	$add_to_wishlist_lbl    = isset( $global_settings['recipeToggles']['21']['label'] ) && '' != $global_settings['recipeToggles']['21']['label'] ? $global_settings['recipeToggles']['21']['label'] : __( 'Add to Favorites', 'delicious-recipes' );

	// Social Share Toggle
	$enable_social_share = isset( $global_settings['enableSocialShare']['0'] ) && 'yes' === $global_settings['enableSocialShare']['0'] ? true : false;

	// Review Rating Toggles and Labels
	$enable_ratings = isset( $global_settings['enableRatings']['0'] ) && 'yes' === $global_settings['enableRatings']['0'] ? true : false;
	$ratings_lbl    = isset( $global_settings['ratingLabel'] ) && '' != $global_settings['ratingLabel'] ? $global_settings['ratingLabel'] : __( 'Rate this recipe', 'delicious-recipes' );

	// Print Recipe Toggles and Labels
	$enable_print_recipe = isset( $global_settings['enablePrintRecipeBtn']['0'] ) && 'yes' === $global_settings['enablePrintRecipeBtn']['0'] ? true : false;
	$print_recipe_lbl    = isset( $global_settings['printRecipeBtnText'] ) && '' != $global_settings['printRecipeBtnText'] ? $global_settings['printRecipeBtnText'] : __( "Print Recipe", 'delicious-recipes' );

	// Global Toggles
	$enable_navigation                = isset( $global_settings['enableNavigation']['0'] ) && 'yes' === $global_settings['enableNavigation']['0'] ? true : false;
	$enable_recipe_author             = isset( $global_settings['enableRecipeAuthor']['0'] ) && 'yes' === $global_settings['enableRecipeAuthor']['0'] ? true : false;
	$enable_published_date            = isset( $global_settings['enablePublishedDate']['0'] ) && 'yes' === $global_settings['enablePublishedDate']['0'] ? true : false;
	$enable_comments                  = isset( $global_settings['enableComments']['0'] ) && 'yes' === $global_settings['enableComments']['0'] ? true : false;
	$enable_recipe_image_crop         = isset( $global_settings['enableRecipeImageCrop']['0'] ) && 'yes' === $global_settings['enableRecipeImageCrop']['0'] ? true : false;
	$enable_recipe_archive_image_crop = isset( $global_settings['enableRecipeArchiveImageCrop']['0'] ) && 'yes' === $global_settings['enableRecipeArchiveImageCrop']['0'] ? true : false;
	$enable_pintit                    = isset( $global_settings['enablePinit']['0'] ) && 'yes' === $global_settings['enablePinit']['0'] ? true : false;

	// User Dashboard Toggles
	$enable_user_registration = isset( $global_settings['enableUserRegistration']['0'] ) && 'yes' === $global_settings['enableUserRegistration']['0'] ? true : false;
	$generate_username        = isset( $global_settings['generateUsername']['0'] ) && 'yes' === $global_settings['generateUsername']['0'] ? true : false;
	$generate_password        = isset( $global_settings['generatePassword']['0'] ) && 'yes' === $global_settings['generatePassword']['0'] ? true : false;
	$terms_n_conditions       = isset( $global_settings['termsNConditions']['0'] ) && 'yes' === $global_settings['termsNConditions']['0'] ? true : false;

	$global_toggles_lbls = array(
		'display_recipes_on_home_page'     => $display_recipes_on_home_page,
		'enable_recipe_featured_image'     => $enable_recipe_featured_image,
		'enable_author'                    => $enable_author,
		'author_lbl'                       => $author_lbl,
		'enable_category'                  => $enable_category,
		'category_lbl'                     => $category_lbl,
		'enable_cooking_method'            => $enable_cooking_method,
		'cooking_method_lbl'               => $cooking_method_lbl,
		'enable_cuisine'                   => $enable_cuisine,
		'cuisine_lbl'                      => $cuisine_lbl,
		'enable_difficulty_level'          => $enable_difficulty_level,
		'difficulty_level_lbl'             => $difficulty_level_lbl,
		'enable_description'               => $enable_description,
		'description_lbl'                  => $description_lbl,
		'enable_prep_time'                 => $enable_prep_time,
		'prep_time_lbl'                    => $prep_time_lbl,
		'enable_cook_time'                 => $enable_cook_time,
		'cook_time_lbl'                    => $cook_time_lbl,
		'enable_rest_time'                 => $enable_rest_time,
		'rest_time_lbl'                    => $rest_time_lbl,
		'enable_total_time'                => $enable_total_time,
		'total_time_lbl'                   => $total_time_lbl,
		'enable_servings'                  => $enable_servings,
		'servings_lbl'                     => $servings_lbl,
		'enable_calories'                  => $enable_calories,
		'calories_lbl'                     => $calories_lbl,
		'enable_seasons'                   => $enable_seasons,
		'seasons_lbl'                      => $seasons_lbl,
		'enable_recipe_keys'               => $enable_recipe_keys,
		'recipe_keys_lbl'                  => $recipe_keys_lbl,
		'enable_video'                     => $enable_video,
		'video_lbl'                        => $video_lbl,
		'enable_jump_to_recipe'            => $enable_jump_to_recipe,
		'jump_to_recipe_lbl'               => $jump_to_recipe_lbl,
		'enable_keywords'                  => $enable_keywords,
		'keywords_lbl'                     => $keywords_lbl,
		'enable_file_under'                => $enable_file_under,
		'file_under_lbl'                   => $file_under_lbl,
		'enable_notes'                     => $enable_notes,
		'notes_lbl'                        => $notes_lbl,
		'enable_jump_to_video'             => $enable_jump_to_video,
		'jump_to_video_lbl'                => $jump_to_video_lbl,
		'enable_social_share'              => $enable_social_share,
		'enable_ratings'                   => $enable_ratings,
		'ratings_lbl'                      => $ratings_lbl,
		'enable_print_recipe'              => $enable_print_recipe,
		'print_recipe_lbl'                 => $print_recipe_lbl,
		'enable_navigation'                => $enable_navigation,
		'enable_recipe_author'             => $enable_recipe_author,
		'enable_published_date'            => $enable_published_date,
		'enable_comments'                  => $enable_comments,
		'enable_mark_as_complete'          => $enable_mark_as_complete,
		'mark_as_complete_lbl'             => $mark_as_complete_lbl,
		'enable_add_to_wishlist'           => $enable_add_to_wishlist,
		'add_to_wishlist_lbl'              => $add_to_wishlist_lbl,
		'enable_recipe_image_crop'         => $enable_recipe_image_crop,
		'enable_recipe_archive_image_crop' => $enable_recipe_archive_image_crop,
		'enable_pintit'                    => $enable_pintit,
		'enable_user_registration'         => $enable_user_registration,
		'generate_username'                => $generate_username,
		'generate_password'                => $generate_password,
		'terms_n_conditions'               => $terms_n_conditions
	);

	return $global_toggles_lbls;
}

/**
 * Gets value of provided index.
 *
 * @param array  $array Array to pick value from.
 * @param string $index Index.
 * @param any    $default Default Values.
 * @return mixed
 */
function delicious_recipes_get_array_values_by_index( $array, $index = null, $default = null ) {
	if ( ! is_array( $array ) ) {
		return $default;
	}

	if ( is_null( $index ) ) {
		return $array;
	}

	$multi_label_indices = explode( '.', $index );
	$value               = $array;

	foreach ( $multi_label_indices as $key ) {
		if ( ! isset( $value[ $key ] ) ) {
			$value = $default;
			break;
		}
		$value = $value[ $key ];
	}

	return $value;
}

/**
 * Get ingredient units.
 *
 * @return void
 */
function delicious_recipes_get_ingredient_units() {
	// Use the "delicious_recipes_ingredient_units" filter to add your own measurements.
	$measurements = apply_filters( 'delicious_recipes_ingredient_units', array(
		'g' => array(
			'singular_abbr' => _x('g','Grams Abbreviation (Singular)','delicious-recipes'),
			'plural_abbr'   => _x('g','Grams Abbreviation (Plural)','delicious-recipes'),
			'singular'      => esc_html__('gram','delicious-recipes'),
			'plural'        => esc_html__('grams','delicious-recipes'),
			'variations'    => array( 'g', 'g.', 'gram', 'grams' ),
		),
		'kg' => array(
			'singular_abbr' => _x('kg','Kilograms Abbreviation (Singular)','delicious-recipes'),
			'plural_abbr'   => _x('kg','Kilograms Abbreviation (Plural)','delicious-recipes'),
			'singular'      => esc_html__('kilogram','delicious-recipes'),
			'plural'        => esc_html__('kilograms','delicious-recipes'),
			'variations'    => array( 'kg', 'kg.', 'kilogram', 'kilograms' ),
		),
		'mg' => array(
			'singular_abbr' => esc_html__('mg','delicious-recipes'),
			'plural_abbr'   => esc_html__('mg','delicious-recipes'),
			'singular'      => esc_html__('milligram','delicious-recipes'),
			'plural'        => esc_html__('milligrams','delicious-recipes'),
			'variations'    => array( 'mg', 'mg.', 'milligram', 'milligrams' ),
		),
		'oz' => array(
			'singular_abbr' => esc_html__('oz','delicious-recipes'),
			'plural_abbr'   => esc_html__('oz','delicious-recipes'),
			'singular'      => esc_html__('ounce','delicious-recipes'),
			'plural'        => esc_html__('ounces','delicious-recipes'),
			'variations'    => array( 'oz', 'oz.', 'ounce', 'ounces' ),
		),
		'floz' => array(
			'singular_abbr' => esc_html__('fl oz','delicious-recipes'),
			'plural_abbr'   => esc_html__('fl oz','delicious-recipes'),
			'singular'      => esc_html__('fluid ounce','delicious-recipes'),
			'plural'        => esc_html__('fluid ounces','delicious-recipes'),
			'variations'    => array( 'fl oz', 'fl oz.', 'fl. oz.', 'fluid ounce', 'fluid ounces' ),
		),
		'cup' => array(
			'singular_abbr' => esc_html__('cup','delicious-recipes'),
			'plural_abbr'   => esc_html__('cups','delicious-recipes'),
			'singular'      => esc_html__('cup','delicious-recipes'),
			'plural'        => esc_html__('cups','delicious-recipes'),
			'variations'    => array( 'c', 'c.', 'cup', 'cups' ),
		),
		'tsp' => array(
			'singular_abbr' => esc_html__('tsp','delicious-recipes'),
			'plural_abbr'   => esc_html__('tsp','delicious-recipes'),
			'singular'      => esc_html__('teaspoon','delicious-recipes'),
			'plural'        => esc_html__('teaspoons','delicious-recipes'),
			'variations'    => array( 't', 'tsp.', 'tsp', 'teaspoon', 'teaspoons' ),
		),
		'tbsp' => array(
			'singular_abbr' => esc_html__('tbsp','delicious-recipes'),
			'plural_abbr'   => esc_html__('tbsp','delicious-recipes'),
			'singular'      => esc_html__('tablespoon','delicious-recipes'),
			'plural'        => esc_html__('tablespoons','delicious-recipes'),
			'variations'    => array( 'T', 'tbl.', 'tbl', 'tbs.', 'tbs', 'tbsp.', 'tbsp', 'tablespoon', 'tablespoons' ),
		),
		'ml' => array(
			'singular_abbr' => esc_html__('ml','delicious-recipes'),
			'plural_abbr'   => esc_html__('ml','delicious-recipes'),
			'singular'      => esc_html__('milliliter','delicious-recipes'),
			'plural'        => esc_html__('milliliters','delicious-recipes'),
			'variations'    => array( 'ml', 'ml.', 'mL', 'mL.', 'cc', 'milliliter', 'milliliters', 'millilitre', 'millilitres' ),
		),
		'l' => array(
			'singular_abbr' => esc_html__('l','delicious-recipes'),
			'plural_abbr'   => esc_html__('l','delicious-recipes'),
			'singular'      => esc_html__('liter','delicious-recipes'),
			'plural'        => esc_html__('liters','delicious-recipes'),
			'variations'    => array( 'l', 'l.', 'L', 'L.', 'liter', 'liters', 'litre', 'litres' ),
		),
		'stick' => array(
			'singular_abbr' => esc_html__('stick','delicious-recipes'),
			'plural_abbr'   => esc_html__('sticks','delicious-recipes'),
			'singular'      => esc_html__('stick','delicious-recipes'),
			'plural'        => esc_html__('sticks','delicious-recipes'),
			'variations'    => array( 'stick', 'sticks' ),
		),
		'lb' => array(
			'singular_abbr' => esc_html__('lb','delicious-recipes'),
			'plural_abbr'   => esc_html__('lbs','delicious-recipes'),
			'singular'      => esc_html__('pound','delicious-recipes'),
			'plural'        => esc_html__('pounds','delicious-recipes'),
			'variations'    => array( 'lb', 'lbs', 'lb.', 'lbs.', 'pound', 'pounds' ),
		),
		'dash' => array(
			'singular_abbr' => esc_html__('dash','delicious-recipes'),
			'plural_abbr'   => esc_html__('dashes','delicious-recipes'),
			'singular'      => esc_html__('dash','delicious-recipes'),
			'plural'        => esc_html__('dashes','delicious-recipes'),
			'variations'    => array( 'dash', 'dashes' ),
		),
		'drop' => array(
			'singular_abbr' => esc_html__('drop','delicious-recipes'),
			'plural_abbr'   => esc_html__('drops','delicious-recipes'),
			'singular'      => esc_html__('drop','delicious-recipes'),
			'plural'        => esc_html__('drops','delicious-recipes'),
			'variations'    => array( 'drop', 'drops' ),
		),
		'gal' => array(
			'singular_abbr' => esc_html__('gal','delicious-recipes'),
			'plural_abbr'   => esc_html__('gals','delicious-recipes'),
			'singular'      => esc_html__('gallon','delicious-recipes'),
			'plural'        => esc_html__('gallons','delicious-recipes'),
			'variations'    => array( 'G', 'G.', 'gal', 'gal.', 'gallon', 'gallons' ),
		),
		'pinch' => array(
			'singular_abbr' => esc_html__('pinch','delicious-recipes'),
			'plural_abbr'   => esc_html__('pinches','delicious-recipes'),
			'singular'      => esc_html__('pinch','delicious-recipes'),
			'plural'        => esc_html__('pinches','delicious-recipes'),
			'variations'    => array( 'pinch', 'pinches' ),
		),
		'pt' => array(
			'singular_abbr' => esc_html__('pt','delicious-recipes'),
			'plural_abbr'   => esc_html__('pt','delicious-recipes'),
			'singular'      => esc_html__('pint','delicious-recipes'),
			'plural'        => esc_html__('pints','delicious-recipes'),
			'variations'    => array( 'p', 'p.', 'pt', 'pt.', 'pts', 'pts.', 'fl pt', 'fl. pt.', 'pint', 'pints' ),
		),
		'qt' => array(
			'singular_abbr' => esc_html__('qt','delicious-recipes'),
			'plural_abbr'   => esc_html__('qts','delicious-recipes'),
			'singular'      => esc_html__('quart','delicious-recipes'),
			'plural'        => esc_html__('quarts','delicious-recipes'),
			'variations'    => array( 'q', 'q.', 'qt', 'qt.', 'qts', 'qts.', 'fl qt', 'fl. qt.', 'quart', 'quarts' ),
		),
		'drizzle' => array(
			'singular_abbr' => esc_html__('drizzle','delicious-recipes'),
			'plural_abbr'   => esc_html__('drizzle','delicious-recipes'),
			'singular'      => esc_html__('Drizzle','delicious-recipes'),
			'plural'        => esc_html__('Drizzle','delicious-recipes'),
			'variations'    => array( 'drizzle' ),
		),
		'clove' => array(
			'singular_abbr' => esc_html__('clove','delicious-recipes'),
			'plural_abbr'   => esc_html__('cloves','delicious-recipes'),
			'singular'      => esc_html__('clove','delicious-recipes'),
			'plural'        => esc_html__('cloves','delicious-recipes'),
			'variations'    => array( 'q', 'q.', 'qt', 'qt.', 'qts', 'qts.', 'fl qt', 'fl. qt.', 'quart', 'quarts' ),
		),
		'jar' => array(
			'singular_abbr' => esc_html__('jar','delicious-recipes'),
			'plural_abbr'   => esc_html__('jars','delicious-recipes'),
			'singular'      => esc_html__('jar','delicious-recipes'),
			'plural'        => esc_html__('jars','delicious-recipes'),
			'variations'    => array( 'q', 'q.', 'jar', 'jar.', 'jars', 'jars.', 'fl jar', 'fl. jar.', 'quart', 'quarts' ),
		),
		'can' => array(
			'singular_abbr' => esc_html__('can','delicious-recipes'),
			'plural_abbr'   => esc_html__('cans','delicious-recipes'),
			'singular'      => esc_html__('can','delicious-recipes'),
			'plural'        => esc_html__('cans','delicious-recipes'),
			'variations'    => array( 'q', 'q.', 'can', 'can.', 'cans', 'cans.', 'fl can', 'fl. can.', 'quart', 'quarts' ),
		),
	));

	return $measurements;
}

add_filter('delicious_recipes_ingredient_units', 'delicious_recipes_get_custom_units');

function delicious_recipes_get_custom_units( $measurements ) {
    $global_settings = delicious_recipes_get_global_settings();
    $custom_units    = isset( $global_settings['customUnits'] ) && $global_settings['customUnits'] ? $global_settings['customUnits'] : '';
    $new_units       = array();

    if ( isset( $custom_units ) && ! empty( $custom_units ) ) {
        foreach( $custom_units as $key => $units ) {
            $unit_name = isset( $units['unit'] ) ? $units['unit'] : false;
            if ( $unit_name ) {
                $singular     = isset( $units['singular'] ) ? $units['singular'] : false;
                $singularAbbr = isset( $units['singularAbbr'] ) ? $units['singularAbbr'] : false;
                $plural       = isset( $units['plural'] ) ? $units['plural'] : false;
                $pluralAbbr   = isset( $units['pluralAbbr'] ) ? $units['pluralAbbr'] : false;
                $variations   = array( $unit_name, $singular, $singularAbbr, $plural, $pluralAbbr );
                $new_units[$unit_name] = [
					'singular_abbr' => $singularAbbr,
					'plural_abbr'   => $pluralAbbr,
					'singular'      => $singular,
					'plural'        => $plural,
					'variations'    => array_unique( $variations ),
				];
            }
        }
    }
    
    $measurements = array_merge( $measurements, $new_units );
    return $measurements;
}

/**
 * Set option by key.
 *
 * @param [type] $setting_key
 * @param [type] $value
 * @return void
 */
function delicious_recipes_set_recipe_setting( $setting_key, $value ) {

	$global_settings = delicious_recipes_get_global_settings();

	if ( isset( $setting_key ) ) {
		$global_settings[$setting_key] = $value;
	}

	$updated_global_settings = stripslashes_deep( $global_settings );

	update_option( 'delicious_recipe_settings', $updated_global_settings );

	return true;
}

/**
 * Widgets styles.
 */
function delicious_recipes_widget_styles() {
	
	$styles = array(
		'style-one'   => __('Style One', 'delicious-recipes'),
		'style-two'   => __('Style Two', 'delicious-recipes'),
	);
	$styles = apply_filters( 'delicious_recipes_widget_styles', $styles );

	return $styles;
}

/**
 * Cleanup recipe meta values.
 *
 * @param array $recipe_settings - sanitized values.
 * @return void
 */
function delicious_recipes_sanitize_metas( $recipe_settings ) {

	if ( ! empty( $recipe_settings ) ) {
		foreach( $recipe_settings as $key => $setting ) {

			if ( ! is_array( $setting ) ) {
				if ( 'recipeDescription' === $key || 'recipeNotes' === $key ) {
					$recipe_settings[$key] = wp_kses_post( $setting );
				} else {
					$recipe_settings[$key] = sanitize_text_field( $setting );
				}
			} else {
				foreach( $setting as $sub_key => $sub_val ) {
					if ( ! is_array( $sub_val ) ) {
						$recipe_settings[$key][$sub_key] = sanitize_text_field( $sub_val );
					} else {
						foreach( $sub_val as $sub_val_key => $sub_sub_val ) {
							if ( ! is_array( $sub_sub_val ) ) {
								if ( 'previewURL' === $sub_val_key || 'vidThumb' === $sub_val_key ) {
									$recipe_settings[$key][$sub_key][$sub_val_key] = esc_url( $sub_sub_val );
								} elseif( 'answer' === $sub_val_key ) {
									$recipe_settings[$key][$sub_key][$sub_val_key] = wp_kses_post( $sub_sub_val );
								} else {
									$recipe_settings[$key][$sub_key][$sub_val_key] = sanitize_text_field( $sub_sub_val );
								}
							} else {
								foreach( $sub_sub_val as $sub_sub_val_key => $sub_sub_sub_val ) {
									if ( ! is_array( $sub_sub_sub_val ) ) {
										$recipe_settings[$key][$sub_key][$sub_val_key][$sub_sub_val_key] = sanitize_text_field( $sub_sub_sub_val );
									} else {
										foreach( $sub_sub_sub_val as $s_s_s_v_k => $sub_sub_sub_sub_val ) {
											if ( ! is_array( $sub_sub_sub_sub_val ) ) {
												if ( 'instruction' === $s_s_s_v_k ) {
													$recipe_settings[$key][$sub_key][$sub_val_key][$sub_sub_val_key][$s_s_s_v_k] = wp_kses_post( $sub_sub_sub_sub_val );
												} elseif ( 'image_preview' === $s_s_s_v_k || 'videoURL' === $s_s_s_v_k ) {
													$recipe_settings[$key][$sub_key][$sub_val_key][$sub_sub_val_key][$s_s_s_v_k] = esc_url( $sub_sub_sub_sub_val );
												} elseif ( 'instructionNotes' === $s_s_s_v_k ) {
													$recipe_settings[$key][$sub_key][$sub_val_key][$sub_sub_val_key][$s_s_s_v_k] = sanitize_textarea_field( $sub_sub_sub_sub_val );
												} else {
													$recipe_settings[$key][$sub_key][$sub_val_key][$sub_sub_val_key][$s_s_s_v_k] = sanitize_text_field( $sub_sub_sub_sub_val );
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	return $recipe_settings;
}