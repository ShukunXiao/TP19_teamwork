<?php
// PHP rendering for the block (for frontend)
function delicious_recipes_recipe_type_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
			return;
	}

		register_block_type(
			'delicious-recipes/tax-type',
			array(
				'attributes'      => array(
					'title'       => array(
						'type'    => 'string',
						'default' => __( 'Recipes By Taxonomy', 'delicious-recipes' ),
					),
					'heading'     => array(
						'type'    => 'string',
						'default' => 'h2',
					),
					'recipeType'    => array(
						'type'    => 'string',
						'default' => '',
					),
					'tax'         => array(
						'type'    => 'string',
						'default' => '',
					),
					'layout'      => array(
						'type'    => 'string',
						'default' => 'grid-view',
					),
					'recipeNumber' => array(
						'type'    => 'number',
						'default' => '2',
					),
				),
				'render_callback' => 'delicious_recipes_tax_by_type_block_render_callback',
			)
		);
}
add_action( 'init', 'delicious_recipes_recipe_type_block' );

/**
 * Call back function for frontend rendering
 */
if ( ! function_exists( 'delicious_recipes_tax_by_type_block_render_callback' ) ) {
	function delicious_recipes_tax_by_type_block_render_callback( $attributes ) {
		extract( $attributes );

		$layout = 'grid-view' === $attributes['layout'] ? 'grid' : 'list';
		$tax    = ! empty( $tax ) && 'undefined' !== $tax ? $tax : 'recipe-course';

		$args = array(
			'posts_per_page'   => $recipeNumber,
			'offset'           => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
			'tax_query'			=> array(
				array(
					'taxonomy'         => $tax,
					'operator'         => 'EXISTS',
					'include_children' => false,
				)
			)
		);

		if ( $recipeType ) {
			$args['tax_query'] = array(
				array(
					'taxonomy'         => $tax,
					'field'            => 'id',
					'terms'            => $recipeType, // Where term_id of Term 1 is "1".
					'include_children' => false,
				),
			);
		}

		$recipes = new WP_Query( $args );

		ob_start();

		echo '<div class="dr-recipes-by-tax-block">';

		if ( $title ) {
			printf( '<%1$s class="wte-tr-title">%2$s</%1$s>', $heading, $title );
		}

		if ( $recipes->have_posts() ) :
			
			$position = 1;
			echo '<div class="dr-gb-block-wrap">';
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
function delicious_recipes_recipe_type_term_ajax() {

	$tax = filter_input( INPUT_GET, 'tax' );

	$taxonomy = ! empty( $tax ) && 'undefined' !== $tax ? $tax : 'recipe-course';

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			// 'parent'     => 0,
		)
	);

	$types = array(
		array(
			'value' => '',
			'label' => 'All',
		),
	);

	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$types[] = array(
				'value' => $term->term_id,
				'label' => $term->name,
			);
		}
	}

	wp_send_json( array( 'types' => $types ) );
	exit;
}
add_action( 'wp_ajax_delicious_recipes_recipe_type_taxomomy', 'delicious_recipes_recipe_type_term_ajax' );

/**
 * Ajax from Backend for the block.
 */
function delicious_recipes_recipe_type_block_ajax() {

	$posts_per_page = filter_input( INPUT_GET, 'post_number' );
	$taxonomy       = filter_input( INPUT_GET, 'taxonomy' );
	$term           = filter_input( INPUT_GET, 'term' );

	
	$args = array(
		'posts_per_page'   => $posts_per_page ? $posts_per_page : 3,
		'offset'           => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
		'post_status'      => 'publish',
		'suppress_filters' => true,
		'tax_query'        => array(
			array(
				'taxonomy'         => $taxonomy,
				'operator'         => 'EXISTS',
				'include_children' => false,
			)
		)
	);

	if ( ! empty( $term ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy'         => $taxonomy,
				'field'            => 'id',
				'terms'            => absint( $term ), // Where term_id of Term 1 is "1".
				'include_children' => false,
			),
		);
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
			$recipe_badges  = array();

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
				'date_published'   => $recipe_metas->date_published,
				'comments_number'  => $recipe_metas->comments_number,
				'rating'           => $recipe_metas->rating,
				'author'           => $recipe_metas->author,
				'author_avatar'    => get_avatar_url( $recipe_metas->author_id ),
				'description'      => $recipe_metas->recipe_description,
				'total_time'       => $recipe_metas->total_time,
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
add_action( 'wp_ajax_delicious_recipes_recipe_type_block', 'delicious_recipes_recipe_type_block_ajax' );
