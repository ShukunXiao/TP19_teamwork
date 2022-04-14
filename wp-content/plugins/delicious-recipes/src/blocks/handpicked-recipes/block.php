<?php
// PHP rendering for the block (for frontend)
function delicious_recipes_handpicked_recipes_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
			return;
	}
		register_block_type(
			'delicious-recipes/handpicked-recipes',
			array(
				'attributes'      => array(
					'title'       => array(
						'type'    => 'string',
						'default' => __( 'Handpicked Recipes', 'delicious-recipes' ),
					),
					'heading'     => array(
						'type'    => 'string',
						'default' => 'h2',
					),
					'Recipe'    => array(
						'type'    => 'array',
						'default' => [],
					),
					'layout'      => array(
						'type'    => 'string',
						'default' => 'grid-view',
					),
				),
				'render_callback' => 'delicious_recipes_handpicked_recipes_block_render',
			)
		);
}
add_action( 'init', 'delicious_recipes_handpicked_recipes_block' );

/**
 * Call back function for frontend rendering
 */
if ( ! function_exists( 'delicious_recipes_handpicked_recipes_block_render' ) ) {
	function delicious_recipes_handpicked_recipes_block_render( $attributes ) {
		
		extract( $attributes );

		if ( isset( $attributes['Recipe'] ) && ! empty( $attributes['Recipe'] ) ) :
			$post_in = array_column( $attributes['Recipe'], 'value' );
		endif;
		
		$layout = 'grid-view' === $attributes['layout'] ? 'grid' : 'list';

		$args = array(
			'offset'           => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		if ( ! empty( $post_in ) && is_array( $post_in ) ) :

			$args['post__in']       = $post_in;
			$args['orderby']        = 'post__in';
			$args['posts_per_page'] = count( $args['post__in'] );

		endif;

		$recipes = new WP_Query( $args );

		ob_start();

		echo '<div class="dr-recipes-handpicked-block">';

		if ( $title ) {
			printf( '<%1$s class="wte-tr-title">%2$s</%1$s>', $heading, $title );
		}

		if ( $recipes->have_posts() ) :
			echo '<div class="dr-gb-block-wrap">';
				$position = 1;
				while( $recipes->have_posts() ) : $recipes->the_post();
					/**
					 * Get search page single block - recipe.
					 */
					$data = array(
						'position'  => $position
					);
						delicious_recipes_get_template( "recipes-{$layout}.php", $data );
					$position++;
				endwhile;
			echo '</div>';
				wp_reset_postdata();

        else : 
        ?>
            <p class="recipe-none"><?php esc_html_e( 'Recipes not found.', 'delicious-recipes' ); ?></p>
        <?php
		endif;

		echo '</div>';

		return ob_get_clean();
	}
}

/**
 * Ajax from Backend Trip Type Terms for the block.
 */
function delicious_recipes_handpicked_recipes_ajax() {
	$RecipeSelect = new Wp_Query(
		array(
			'post_type'      => DELICIOUS_RECIPE_POST_TYPE,
			'posts_per_page' => -1,
			'post_status'    => 'publish'
		)
	);

	if ( $RecipeSelect->have_posts() ) {
		while ( $RecipeSelect->have_posts() ) {

			$RecipeSelect->the_post();

			$RecipeOptions[] = array(
				'value' => get_the_ID(),
				'label' => get_the_title(),
			);
		}
		wp_reset_postdata();
	}

	wp_send_json( array( 'RecipeOptions' => $RecipeOptions ) );
	exit;
}
add_action( 'wp_ajax_delicious_recipes_handpicked_recipes_ajax', 'delicious_recipes_handpicked_recipes_ajax' );

/**
 * Ajax from Backend for the block.
 */
function delicious_recipes_handpicked_recipes_block_ajax() {

	$post__in = filter_input( INPUT_GET, 'posts_in' );
	
	$args = array(
		'offset'           => 0,
		'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
		'post_status'      => 'publish',
		'suppress_filters' => true,
	);

	if ( ! empty( $post__in ) ) {
		$args['post__in']       = explode( ',', $post__in );
		$args['orderby']        = 'post__in';
		$args['posts_per_page'] = count( $args['post__in'] );
	}

	$recipes_query = new WP_Query( $args );

	$recipes = array();
	// Get global toggles.
	$global_toggles = delicious_recipes_get_global_toggles_and_labels();
	$img_size = $global_toggles['enable_recipe_archive_image_crop'] ? 'recipe-archive-grid' : 'full';

	if ( $recipes_query->have_posts() ) {

		while ( $recipes_query->have_posts() ) {
            $recipes_query->the_post();
            
            $recipe       = get_post( get_the_ID() );
            $recipe_metas = delicious_recipes_get_recipe( $recipe );

			$thumbnail_id = has_post_thumbnail( $recipe_metas->ID ) ? get_post_thumbnail_id( $recipe_metas->ID ) : '';
			$thumbnail    = $thumbnail_id ? get_the_post_thumbnail( $recipe_metas->ID, $img_size ) : '';
			$fallback_svg = delicious_recipes_get_fallback_svg( 'recipe-archive-grid', true );

			$recipe_keys    = array();
			$recipe_courses = array();
			$cooking_methods = array();
			$cuisine         = array();
			$recipe_badges   = array();

			if ( ! empty( $recipe_metas->recipe_cuisine ) ) {
				foreach( $recipe_metas->recipe_cuisine as $recipe_cus ) {
					$key  = get_term_by( 'name', $recipe_cus, 'recipe-cuisine' );
					$link = get_term_link( $key, 'recipe-cuisine' );
					$icon = delicious_recipes_get_tax_icon( $key, true );
					$cuisine[] = array(
						'key'  => $recipe_cus,
						'link' => $link,
						'icon' => $icon
					);
				}
			}

			if ( ! empty( $recipe_metas->recipe_keys ) ) {
				foreach( $recipe_metas->recipe_keys as $recipe_key ) {
					$key  = get_term_by( 'name', $recipe_key, 'recipe-key' );
					$link = get_term_link( $key, 'recipe-key' );
					$icon = delicious_recipes_get_tax_icon( $key, true );
					$recipe_keys[] = array(
						'key'  => $recipe_key,
						'link' => $link,
						'icon' => $icon
					);
				}
			}

			if ( ! empty( $recipe_metas->recipe_course ) ) {
				foreach( $recipe_metas->recipe_course as $course ) {
					$ky  = get_term_by( 'name', $course, 'recipe-course' );
					$link = get_term_link( $ky, 'recipe-course' );
					$icon = delicious_recipes_get_tax_icon( $ky, true );
					$recipe_courses[] = array(
						'key'  => $course,
						'link' => $link,
						'icon' => $icon
					);
				}
			}

			if ( ! empty( $recipe_metas->cooking_method ) ) {
				foreach( $recipe_metas->cooking_method as $method ) {
					$ky  = get_term_by( 'name', $method, 'recipe-cooking-method' );
					$link = get_term_link( $ky, 'recipe-cooking-method' );
					$icon = delicious_recipes_get_tax_icon( $ky, true );
					$cooking_methods[] = array(
						'key'  => $method,
						'link' => $link,
						'icon' => $icon
					);
				}
			}

			if ( ! empty( $recipe_metas->badges ) ) {
				$badge       = get_term_by( 'name', $recipe_metas->badges[0], 'recipe-badge' );
				$link        = get_term_link( $badge, 'recipe-badge' );
				$badge_metas = get_term_meta( $badge->term_id, 'dr_taxonomy_metas', true );
				$tax_color   = isset( $badge_metas['taxonomy_color'] )  && ! empty( $badge_metas['taxonomy_color'] ) ? $badge_metas['taxonomy_color'] : 'red';

				$recipe_badges = array(
					'badge' => $recipe_metas->badges[0],
					'link'  => $link,
					'color' => $tax_color
				);
			}

			$recipes[] = array(
				'recipe_id'        => $recipe_metas->ID,
				'title'            => $recipe_metas->name,
				'permalink'        => $recipe_metas->permalink,
				'thumbnail_id'     => $recipe_metas->thumbnail_id,
				'thumbnail_url'    => $recipe_metas->thumbnail,
				'thumbnail'        => $thumbnail,
				'fallback_svg'     => $fallback_svg,
				'recipe_keys'      => $recipe_keys,
				'recipe_course'    => $recipe_courses,
				'cooking_methods'  => $cooking_methods,
				'cuisine'          => $cuisine,
				'date_published'   => $recipe_metas->date_published,
				'comments_number'  => $recipe_metas->comments_number,
				'rating'           => $recipe_metas->rating,
				'author'           => $recipe_metas->author,
				'author_avatar'    => get_avatar_url( $recipe_metas->author_id ),
				'description'      => $recipe_metas->recipe_description,
				'prep_time'        => $recipe_metas->prep_time,
				'prep_time_unit'   => $recipe_metas->prep_time_unit,
				'cook_time'        => $recipe_metas->cook_time,
				'cook_time_unit'   => $recipe_metas->cook_time_unit,
				'rest_time'        => $recipe_metas->rest_time,
				'rest_time_unit'   => $recipe_metas->rest_time_unit,
				'total_time'       => $recipe_metas->total_time,
				'no_of_servings'   => $recipe_metas->no_of_servings,
				'difficulty_level' => $recipe_metas->difficulty_level,
				'recipe_badges'    => $recipe_badges
			);
		}

		wp_reset_postdata();
		wp_send_json(
			array(
				'found' => true,
				'recipes' => $recipes,
			)
		);

	}

	wp_send_json(
		array(
			'found' => false,
			'recipes' => array(),
		)
	);

	die();

}
add_action( 'wp_ajax_delicious_recipes_handpicked_recipes_block_ajax', 'delicious_recipes_handpicked_recipes_block_ajax' );
