<?php
/**
 * Template Loader
 *
 * @package Delicious_Recipes/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template loader class.
 */
class Delicious_Recipes_Template_Loader {

	/**
	 * Store the recipe archive page ID.
	 *
	 * @var integer
	 */
	private static $recipe_archive_id = 0;

	/**
	 * Store whether we're processing a recipe inside the_content filter.
	 *
	 * @var boolean
	 */
	private static $in_content_filter = false;

	/**
	 * Is Delicious Recipes support defined?
	 *
	 * @var boolean
	 */
	private static $theme_support = false;

	/**
	 * Hook in methods.
	 */
	public static function init() {
		self::$theme_support     = current_theme_supports( 'delicious-recipes' );
		self::$recipe_archive_id = delicious_recipes_get_page_id( 'recipe-archive' );

		// Supported themes.
		if ( self::$theme_support ) {
			add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
		} else {
			// Unsupported themes.
			add_action( 'template_redirect', array( __CLASS__, 'unsupported_theme_init' ) );
		}
		add_action( 'template_redirect', array( __CLASS__, 'print_recipe_template' ), 10 );
		add_filter( 'template_redirect', array( __CLASS__, 'recipe_search_template' ) );
		add_filter( 'template_redirect', array( __CLASS__, 'author_archive_template' ) );
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the theme's.
	 *
	 * Templates are in the 'templates' folder. Delicious Recipes looks for theme
	 * overrides in /theme/delicious-recipes/ by default.
	 *
	 * For beginners, it also looks for a delicious-recipes.php template first. If the user adds
	 * this to the theme (containing a delicious-recipes inside) this will be used for all
	 * delicious-recipes templates.
	 *
	 * @param string $template Template to load.
	 * @return string
	 */
	public static function template_loader( $template ) {
		if ( is_embed() || is_search() ) {
			return $template;
		}

		$default_file = self::get_template_loader_default_file();

		if ( $default_file ) {
			/**
			 * Filter hook to choose which files to find before Delicious Recipes does it's own logic.
			 *
			 * @since 1.0.0
			 * @var array
			 */
			$search_files = self::get_template_loader_files( $default_file );
			$template     = locate_template( $search_files );

			if ( ! $template || DELICIOUS_RECIPES_TEMPLATE_DEBUG_MODE ) {
				$template = DEL_RECIPE()->plugin_path() . '/templates/' . $default_file;
			}
		}

		return $template;
	}

	/**
	 * Get the default filename for a template.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	private static function get_template_loader_default_file() {
		if ( is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) {
			$default_file = 'single-recipe.php';
		} elseif ( is_recipe_taxonomy() ) {
			$object = get_queried_object();

			if ( is_recipe_taxonomy() ) {
				$default_file = 'taxonomy-' . $object->taxonomy . '.php';
			} else {
				$default_file = 'archive-recipe.php';
			}
		} elseif ( is_post_type_archive( DELICIOUS_RECIPE_POST_TYPE ) || is_page( delicious_recipes_get_page_id( 'recipe-archive' ) ) ) {
			$default_file = self::$theme_support ? 'archive-recipe.php' : '';
		} else {
			$default_file = '';
		}
		return $default_file;
	}

	/**
	 * Get an array of filenames to search for a given template.
	 *
	 * @since  1.0.0
	 * @param  string $default_file The default file name.
	 * @return string[]
	 */
	private static function get_template_loader_files( $default_file ) {
		$templates   = apply_filters( 'delicious_recipes_template_loader_files', array(), $default_file );
		$templates[] = 'delicious_recipes.php';

		if ( is_page_template() ) {
			$page_template = get_page_template_slug();

			if ( $page_template ) {
				$validated_file = validate_file( $page_template );
				if ( 0 === $validated_file ) {
					$templates[] = $page_template;
				} else {
					error_log( "Delicious Recipes: Unable to validate template path: \"$page_template\". Error Code: $validated_file." );
				}
			}
		}

		if ( is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) {
			$object       = get_queried_object();
			$name_decoded = urldecode( $object->post_name );
			if ( $name_decoded !== $object->post_name ) {
				$templates[] = "single-recipe-{$name_decoded}.php";
			}
			$templates[] = "single-recipe-{$object->post_name}.php";
		}

		if ( is_recipe_taxonomy() ) {
			$object      = get_queried_object();
			$templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
			$templates[] = DEL_RECIPE()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
			$templates[] = 'taxonomy-' . $object->taxonomy . '.php';
			$templates[] = DEL_RECIPE()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';
		}

		$templates[] = $default_file;
		$templates[] = DEL_RECIPE()->template_path() . $default_file;

		return array_unique( $templates );
	}

	/**
	 * Unsupported theme compatibility methods.
	 */

	/**
	 * Hook in methods to enhance the unsupported theme experience on pages.
	 *
	 * @since 1.0.0
	 */
	public static function unsupported_theme_init() {
		if ( is_recipe_taxonomy() ) {
			self::unsupported_theme_tax_archive_init();
		} elseif ( is_recipe() ) {
			self::unsupported_theme_recipe_page_init();
		} else {
			// self::unsupported_theme_recipe_archive_page_init();
		}
	}

	/**
	 * Hook in methods to enhance the unsupported theme experience on the recipe_archive page.
	 *
	 * @since 1.0.0
	 */
	private static function unsupported_theme_recipe_archive_page_init() {
		add_filter( 'the_content', array( __CLASS__, 'unsupported_theme_recipe_archive_content_filter' ), 10 );
	}

	/**
	 * Hook in methods to enhance the unsupported theme experience on recipe pages.
	 *
	 * @since 1.0.0
	 */
	private static function unsupported_theme_recipe_page_init() {
		add_filter( 'the_content', array( __CLASS__, 'unsupported_theme_recipe_content_filter' ), 10 );
	}

	/**
	 * Archive page for recipe unsupported theme content.
	 *
	 * @return void
	 */
	public static function unsupported_theme_recipe_archive_content_filter() {
		global $wp_query, $post;

		add_filter( 'template_include', array( __CLASS__, 'recipe_archive_content_filter' ) );
	}

	/**
	 * Enhance the unsupported theme experience on recipe Category and Attribute pages by rendering
	 * those pages using the single template and shortcode-based content. To do this we make a dummy
	 * post and set a shortcode as the post content. This approach is adapted from bbPress.
	 *
	 * @since 1.0.0
	 */
	private static function unsupported_theme_tax_archive_init() {
		global $wp_query, $post;

		add_filter( 'template_include', array( __CLASS__, 'recipe_tax_archive_content_filter' ) );
	}

	/**
	 * Recipe Archive Template.
	 *
	 * @return void
	 */
	public static function recipe_archive_content_filter( $template_path ) {

		// Template file.
		$template_path = delicious_recipes_locate_template( 'archive-recipe.php' );

		return $template_path;
	}

	/**
	 * Tax archive page mapping function
	 *
	 * @param [type] $template_path
	 * @return void
	 */
	public static function recipe_tax_archive_content_filter( $template_path ) {

		// Template file.
		$template_path = delicious_recipes_locate_template( 'taxonomy-archive.php' );

		return $template_path;
	}

	/**
	 * Force the loading of one of the single templates instead of whatever template was about to be loaded.
	 *
	 * @since 1.0.0
	 * @param string $template Path to template.
	 * @return string
	 */
	public static function force_single_template_filter( $template ) {
		$possible_templates = array(
			'page',
			'single',
			'singular',
			'index',
		);

		foreach ( $possible_templates as $possible_template ) {
			$path = get_query_template( $possible_template );
			if ( $path ) {
				return $path;
			}
		}

		return $template;
	}

	/**
	 * Filter the content and insert Delicious Recipes content on the recipe_archive page.
	 *
	 * For non-WC themes, this will setup the main recipe_archive page to be shortcode based to improve default appearance.
	 *
	 * @since 1.0.0
	 * @param string $content Existing post content.
	 * @return string
	 */
	public static function unsupported_theme_recipe_content_filter( $content ) {
		global $wp_query;

		if ( self::$theme_support || ! is_main_query() || ! in_the_loop() ) {
			return $content;
		}

		self::$in_content_filter = true;

		// Remove the filter we're in to avoid nested calls.
		remove_filter( 'the_content', array( __CLASS__, 'unsupported_theme_recipe_content_filter' ) );

		if ( is_recipe() ) {
			$content = do_shortcode( '[recipe_page id="' . get_the_ID() . '" show_title=0]' );
		}

		self::$in_content_filter = false;

		return $content;
	}

	/**
	 * Are we filtering content for unsupported themes?
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public static function in_content_filter() {
		return (bool) self::$in_content_filter;
	}

	/**
	 * print recipe template
	 *
	 * @return void
	 */
	public static function print_recipe_template(){
		if ( is_singular( DELICIOUS_RECIPE_POST_TYPE ) && isset( $_GET['print_recipe'] ) ) :
			while ( have_posts() ) : 
				the_post();	
				delicious_recipes_get_template( 'recipe/print-screen.php' );
			endwhile; // end of the loop.
			exit;
		endif;
	}

	/**
	 * recipe author archive template
	 *
	 * @return void
	 */
	public static function author_archive_template(){
		$global_settings = delicious_recipes_get_global_settings();
		$recipe_author   = isset( $global_settings['recipeAuthor'] ) && ! empty( $global_settings['recipeAuthor'] ) ? $global_settings['recipeAuthor'] : false;

		if ( $recipe_author ) :
			if ( is_author( $recipe_author ) ) :	
					delicious_recipes_get_template( 'archive-author.php' );
				exit;
			endif;
		endif;
	}

	/**
	 * Search page template.
	 *
	 * @return void
	 */
	public static function recipe_search_template() {
		global $post;
		
		$options = delicious_recipes_get_global_settings();

		if ( ! isset( $options['searchPage'] ) ) {
			return;
		}

		$pid = $options['searchPage'];

		if ( ! is_object( $post ) ) {
			return;
		}

		if ( $post->ID != $pid ) {
			return;
		}
		delicious_recipes_get_template( 'global/searchpage.php' );
		exit();
	}

}

add_action( 'init', array( 'Delicious_Recipes_Template_Loader', 'init' ) );
