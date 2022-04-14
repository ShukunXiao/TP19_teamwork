<?php
/**
 * Delicious Recipes Shortcodes.
 * 
 * @package Delicious_Recipes
 * @since 1.0.0
 */
defined( 'ABSPATH' ) || exit;
/**
 * Shortcodes handler.
 */
class Delicious_Recipes_Shortcodes {

    /**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'recipe'              => __CLASS__ . '::recipe',
			'recipe_page'         => __CLASS__ . '::recipe_page',
			'recipes'             => __CLASS__ . '::recipes',
			'recipe_search'       => __CLASS__ . '::recipe_search',
			'dr_featured_recipes' => __CLASS__ . '::featured_recipes',
			'dr_popular_recipes'  => __CLASS__ . '::popular_recipes',
			'dr_recipes'          => __CLASS__ . '::recent_recipes',
			'print_recipe'        => __CLASS__ . '::print_recipe',
			'recipe_card'         => __CLASS__ . '::recipe_card',
			'dr_surprise_me'      => __CLASS__ . '::surprise_me',
			'dr_user_dashboard'   => __CLASS__ . '::user_dashboard',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
    }
    
    /**
	 * Show a single recipe page.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function recipe_page( $atts ) {

		// If REST_REQUEST is defined (by WordPress) and is a TRUE, then it's a REST API request.
		$is_rest_route = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		if (
			( is_admin() && ! $is_rest_route ) || // admin and AJAX (via admin-ajax.php) requests
			( ! is_admin() && $is_rest_route )    // REST requests only
		) {
			return '';
		}

		if ( empty( $atts ) ) {
			return '';
		}

		if ( ! isset( $atts['id'] ) ) {
			return '';
		}

		$args = array(
			'posts_per_page'      => 1,
			'post_type'           => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'         => ( ! empty( $atts['status'] ) ) ? $atts['status'] : 'any',
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => 1,
		);

		if ( isset( $atts['id'] ) ) {
			$args['p'] = absint( $atts['id'] );
		}

		// Don't render titles if desired.
		if ( isset( $atts['show_title'] ) && ! $atts['show_title'] ) {
			remove_action( 'delicious_recipes_single_recipe_summary', 'delicious_recipes_template_single_title', 5 );
		}

		$single_recipe = new WP_Query( $args );

		$preselected_id = '0';

		// For "is_single" to always make load comments_template() for reviews.
		$single_recipe->is_single = true;

		ob_start();

		global $wp_query;

		// Backup query object so following loops think this is a recipe page.
		$previous_wp_query = $wp_query;
		// @codingStandardsIgnoreStart
		$wp_query          = $single_recipe;
		// @codingStandardsIgnoreEnd

		wp_enqueue_script( 'delicious-recipes-single' );

		while ( $single_recipe->have_posts() ) {
			$single_recipe->the_post()
			?>
			<div class="single-recipe" data-recipe-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>">
				<?php delicious_recipes_get_template_part( 'content', 'single-recipe' ); ?>
			</div>
			<?php
		}

		// Restore $previous_wp_query and reset post data.
		// @codingStandardsIgnoreStart
		$wp_query = $previous_wp_query;
		// @codingStandardsIgnoreEnd
		wp_reset_postdata();

		// Re-enable titles if they were removed.
		if ( isset( $atts['show_title'] ) && ! $atts['show_title'] ) {
			add_action( 'delicious_recipes_single_recipe_summary', 'delicious_recipes_template_single_title', 5 );
		}

		return '<div class="delicious-recipes single-recipe">' . ob_get_clean() . '</div>';
	}

	/**
	 * Show a single recipe card.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function recipe_card( $atts ) {

		// If REST_REQUEST is defined (by WordPress) and is a TRUE, then it's a REST API request.
		$is_rest_route = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		if (
			( is_admin() && ! $is_rest_route ) || // admin and AJAX (via admin-ajax.php) requests
			( ! is_admin() && $is_rest_route )    // REST requests only
		) {
			return '';
		}

		if ( empty( $atts ) ) {
			return '';
		}

		if ( ! isset( $atts['id'] ) ) {
			return '';
		}

		$args = array(
			'posts_per_page'      => 1,
			'post_type'           => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'         => ( ! empty( $atts['status'] ) ) ? $atts['status'] : 'any',
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => 1,
		);

		if ( isset( $atts['id'] ) ) {
			$args['p'] = absint( $atts['id'] );
		}

		// Don't render titles if desired.
		if ( isset( $atts['show_title'] ) && ! $atts['show_title'] ) {
			remove_action( 'delicious_recipes_single_recipe_summary', 'delicious_recipes_template_single_title', 5 );
		}

		$single_recipe = new WP_Query( $args );

		$preselected_id = '0';

		// For "is_single" to always make load comments_template() for reviews.
		$single_recipe->is_single = true;

		ob_start();

		global $wp_query;

		// Backup query object so following loops think this is a recipe page.
		$previous_wp_query = $wp_query;
		// @codingStandardsIgnoreStart
		$wp_query          = $single_recipe;
		// @codingStandardsIgnoreEnd

		wp_enqueue_script( 'delicious-recipes-single' );

		$layout = isset( $atts['layout'] ) && $atts['layout'] ? $atts['layout'] : '';

		while ( $single_recipe->have_posts() ) {
			$single_recipe->the_post()
			?>
			<div class="single-recipe" data-recipe-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>">
				<?php 
					$data = array(
						'layout' => $layout
					);
					delicious_recipes_get_template( 'recipe/recipe-main.php', $data ); 
				?>
			</div>
			<?php
		}

		// Restore $previous_wp_query and reset post data.
		// @codingStandardsIgnoreStart
		$wp_query = $previous_wp_query;
		// @codingStandardsIgnoreEnd
		wp_reset_postdata();

		// Re-enable titles if they were removed.
		if ( isset( $atts['show_title'] ) && ! $atts['show_title'] ) {
			add_action( 'delicious_recipes_single_recipe_summary', 'delicious_recipes_template_single_title', 5 );
		}

		return '<div class="delicious-recipes single-recipe">' . ob_get_clean() . '</div>';
	}

	/**
	 * Recipe search page.
	 *
	 * @return void
	 */
	public static function recipe_search() {
		ob_start();

		delicious_recipes_get_template( 'global/searchpage-content.php' );

		$search_content = ob_get_clean();

		return $search_content;
	}

	/**
	 * Show featured recipes.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function featured_recipes( $atts ) {

		$atts = shortcode_atts( array(
			'num_posts' => '3',
			'layout'    => 'grid',
			), $atts, 'dr_featured_recipes');

		// If REST_REQUEST is defined (by WordPress) and is a TRUE, then it's a REST API request.
		$is_rest_route = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		if (
			( is_admin() && ! $is_rest_route ) || // admin and AJAX (via admin-ajax.php) requests
			( ! is_admin() && $is_rest_route )    // REST requests only
		) {
			return '';
		}

		$layout = isset( $atts['layout'] ) && 'list' === $atts['layout'] ? 'list' : 'grid';

		$cat = get_theme_mod( 'exclude_categories' );
        if( $cat ) $cat = array_diff( array_unique( $cat ), array('') );

		$args = array(
			'post_type'           => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'         => 'publish',
			'posts_per_page'      => absint( $atts['num_posts'] ),
			'meta_query'     => array(
				array(
					'key'   => 'wp_delicious_featured_recipe',
					'value' => 'yes'
				)
			),
			'ignore_sticky_posts' => true,
			'category__not_in'    => $cat
		);

		$featured_recipes = new WP_Query( $args );

		if( empty( $featured_recipes ) ) {
			return '';
		}

		ob_start();

		wp_enqueue_script( 'delicious-recipes-single' );

		while ( $featured_recipes->have_posts() ) {
			$featured_recipes->the_post();
				delicious_recipes_get_template_part( 'recipes', $layout );
		}

		wp_reset_postdata();

		return '<div class="dr-archive-list-gridwrap featured">' . ob_get_clean() . '</div>';
	}

	/**
	 * Show popular recipes.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function popular_recipes( $atts ) {

		$atts = shortcode_atts( array(
			'num_posts' => '3',
			'based_on'  => 'views',
			'layout'    => 'grid',
			), $atts, 'dr_popular_recipes');

		// If REST_REQUEST is defined (by WordPress) and is a TRUE, then it's a REST API request.
		$is_rest_route = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		if (
			( is_admin() && ! $is_rest_route ) || // admin and AJAX (via admin-ajax.php) requests
			( ! is_admin() && $is_rest_route )    // REST requests only
		) {
			return '';
		}

		$layout = isset( $atts['layout'] ) && 'list' === $atts['layout'] ? 'list' : 'grid';

		$cat = get_theme_mod( 'exclude_categories' );
        if( $cat ) $cat = array_diff( array_unique( $cat ), array('') );

        $args = array(
            'post_type'             => DELICIOUS_RECIPE_POST_TYPE,
            'post_status'           => 'publish',
            'posts_per_page'        => absint( $atts['num_posts'] ),
            'ignore_sticky_posts'   => true,
            'category__not_in'      => $cat
        );
        
        if( isset( $atts['based_on'] ) && $atts['based_on'] =='views' ) {
            $args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_delicious_recipes_view_count';
			
        } elseif( $atts['based_on'] == 'comments' )  {
            $args['orderby'] = 'comment_count';
        }

		$popular_recipes = new WP_Query( $args );

		ob_start();

		wp_enqueue_script( 'delicious-recipes-single' );

		while ( $popular_recipes->have_posts() ) {
			$popular_recipes->the_post();
				delicious_recipes_get_template_part( 'recipes', $layout );
		}

		wp_reset_postdata();

		return '<div class="dr-archive-list-gridwrap popular">' . ob_get_clean() . '</div>';
	}

	/**
	 * Show recent recipes.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function recent_recipes( $atts ) {

		$atts = shortcode_atts( array(
			'num_posts' => '3',
			'layout'    => 'grid',
			), $atts, 'dr_recipes');

		// If REST_REQUEST is defined (by WordPress) and is a TRUE, then it's a REST API request.
		$is_rest_route = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		if (
			( is_admin() && ! $is_rest_route ) || // admin and AJAX (via admin-ajax.php) requests
			( ! is_admin() && $is_rest_route )    // REST requests only
		) {
			return '';
		}

		$layout = isset( $atts['layout'] ) && 'list' === $atts['layout'] ? 'list' : 'grid';

		$cat = get_theme_mod( 'exclude_categories' );
        if( $cat ) $cat = array_diff( array_unique( $cat ), array('') );

        $args = array(
            'post_type'             => DELICIOUS_RECIPE_POST_TYPE,
            'post_status'           => 'publish',
            'posts_per_page'        => absint( $atts['num_posts'] ),
            'ignore_sticky_posts'   => true,
            'category__not_in'      => $cat
        );
        
		$recent_recipes = new WP_Query( $args );

		ob_start();

		wp_enqueue_script( 'delicious-recipes-single' );

		while ( $recent_recipes->have_posts() ) {
			$recent_recipes->the_post();
				delicious_recipes_get_template_part( 'recipes', $layout );
		}

		wp_reset_postdata();

		return '<div class="dr-archive-list-gridwrap recent">' . ob_get_clean() . '</div>';
	}

	/**
	 * Show print recipe and Jump to Recipe block.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function print_recipe( $atts ) {

		// If REST_REQUEST is defined (by WordPress) and is a TRUE, then it's a REST API request.
		$is_rest_route = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		if (
			( is_admin() && ! $is_rest_route ) || // admin and AJAX (via admin-ajax.php) requests
			( ! is_admin() && $is_rest_route )    // REST requests only
		) {
			return '';
		}

		ob_start();

		wp_enqueue_script( 'delicious-recipes-single' );

		delicious_recipes_get_template( 'shortcodes/print-jump-btns.php' );

		return '<div class="delicious-recipes print">' . ob_get_clean() . '</div>';
	}

	/**
	 * Show random recipe.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function surprise_me( $atts ) {

		$atts = shortcode_atts( array(
			'title'       => __( 'Surprise Me', 'delicious-recipes' ),
			'enable_icon' => true,
			'icon'        => 'fas fa-random',
			), $atts, 'dr_surprise_me');

		// If REST_REQUEST is defined (by WordPress) and is a TRUE, then it's a REST API request.
		$is_rest_route = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		if (
			( is_admin() && ! $is_rest_route ) || // admin and AJAX (via admin-ajax.php) requests
			( ! is_admin() && $is_rest_route )    // REST requests only
		) {
			return '';
		}

		$title = isset( $atts['title'] ) && '' != $atts['title'] ? $atts['title'] : __( 'Surprise Me', 'delicious-recipes' );
		$icon = isset( $atts['enable_icon'] ) && ( 1 == $atts['enable_icon'] || 'true' == $atts['enable_icon'] )
		&& isset( $atts['icon'] ) && '' != $atts['icon'] ? $atts['icon'] : '';

		// get icon
		if( $atts['enable_icon'] && ( 1 == $atts['enable_icon'] || 'true' == $atts['enable_icon'] ) ) {
			$icon_val = '<i class="'.esc_attr( $icon ).'"></i>';
		} else {
			$icon_val = '';
		}

		$cat = get_theme_mod( 'exclude_categories' );
        if( $cat ) $cat = array_diff( array_unique( $cat ), array('') );

        $args = array(
            'post_type'           => DELICIOUS_RECIPE_POST_TYPE,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'category__not_in'    => $cat,
            'orderby'             => 'rand',
            'posts_per_page'      => '1',
        );
        
		$random_recipes = get_posts( $args );

		ob_start();

		wp_enqueue_script( 'delicious-recipes-single' );

		if( $random_recipes ) {
			foreach( $random_recipes as $recipe ) {
				echo $icon_val; echo ("\r\n");
				echo '<a href="'.esc_url( get_permalink( $recipe->ID )).'">'.esc_html( $title ).'</a>';
			}
		}

		return '<button class="dr-surprise-me">' . ob_get_clean() . '</button>';
	}

	/**
	 * Add user Account shortcode.
	 *
	 * @return string
	 */
	public static function user_dashboard() {

		// If REST_REQUEST is defined (by WordPress) and is a TRUE, then it's a REST API request.
		$is_rest_route = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		if (
			( is_admin() && ! $is_rest_route ) || // admin and AJAX (via admin-ajax.php) requests
			( ! is_admin() && $is_rest_route )    // REST requests only
		) {
			return '';
		}

		ob_start();

		call_user_func( array( 'Delicious_Recipes_User_Account', 'output' ) );

		return '<div class="delicious-recipes user-dashboard">' . ob_get_clean() . '</div>';
	}

}