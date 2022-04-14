<?php
// PHP rendering for the block (for frontend)
function delicious_recipes_recipe_card_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
			return;
	}
		register_block_type(
			'delicious-recipes/recipe-card',
			array(
				'attributes'      => array(
					'title'       => array(
						'type'    => 'string',
						'default' => __( 'Recipe Card', 'delicious-recipes' ),
					),
					'heading'     => array(
						'type'    => 'string',
						'default' => 'h2',
					),
					'Recipe'    => array(
						'type'    => 'string',
						'default' => '',
					),
					'layout'      => array(
						'type'    => 'string',
						'default' => 'default',
					),
				),
				'render_callback' => 'delicious_recipes_recipe_card_block_render',
			)
		);
}
add_action( 'init', 'delicious_recipes_recipe_card_block' );

/**
 * Call back function for frontend rendering
 */
if ( ! function_exists( 'delicious_recipes_recipe_card_block_render' ) ) {
	function delicious_recipes_recipe_card_block_render( $attributes ) {
		
		extract( $attributes );

		$latest_recipe = get_posts("post_type=recipe&numberposts=1");
		$post_id       = $latest_recipe[0]->ID;

		if ( isset( $attributes['Recipe'] ) && '' != $attributes['Recipe'] ) {
			$post_id = $attributes['Recipe'];
		}

		$layout = isset( $attributes['layout'] ) && $attributes['layout'] ? $attributes['layout'] : '';
		
		ob_start();

		echo '<div class="dr-recipes-card-block">';

		if ( $title ) {
			printf( '<%1$s class="wte-tr-title">%2$s</%1$s>', $heading, $title );
		}

		if ( absint( $post_id ) ) {
			
			echo do_shortcode( '[recipe_card id="' . $post_id . '" show_title=0 layout="' .$layout .'"]' );

		} else {
			?>
            	<p class="recipe-none"><?php esc_html_e( 'Recipe Card not found.', 'delicious-recipes' ); ?></p>
        	<?php
		}

		echo '</div>';

		return ob_get_clean();
	}
}

/**
 * Ajax from Backend Trip Type Terms for the block.
 */
function delicious_recipes_recipe_card_ajax() {
	$RecipeSelect = new Wp_Query(
		array(
			'post_type'      => DELICIOUS_RECIPE_POST_TYPE,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
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
add_action( 'wp_ajax_delicious_recipes_recipe_card_ajax', 'delicious_recipes_recipe_card_ajax' );

/**
 * Ajax from Backend for the block.
 */
function delicious_recipes_recipe_card_block_ajax() {

	$post__in = filter_input( INPUT_GET, 'posts_in' );
	
	$args = array(
		'offset'           => 0,
		'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
		'post_status'      => 'publish',
		'suppress_filters' => true,
	);

	if ( $post__in && ! empty( $post__in ) ) {
		$args['p'] = absint( $post__in );
	} else {
		$args['posts_per_page'] = 1;
	}

	$recipes_query = new WP_Query( $args );

	$recipes = array();
	// Get global toggles.
	$global_toggles = delicious_recipes_get_global_toggles_and_labels();
	$img_size = $global_toggles['enable_recipe_image_crop'] ? 'recipe-feat-gallery' : 'full';

	if ( $recipes_query->have_posts() ) {

		while ( $recipes_query->have_posts() ) {
            $recipes_query->the_post();
            
            $recipe          = get_post( get_the_ID() );
            $recipe_metas    = delicious_recipes_get_recipe( $recipe );
            $global_settings = delicious_recipes_get_global_settings();

			$thumbnail_id = has_post_thumbnail( $recipe_metas->ID ) ? get_post_thumbnail_id( $recipe_metas->ID ) : '';
			$thumbnail    = $thumbnail_id ? get_the_post_thumbnail( $recipe_metas->ID, $img_size ) : '';
			$fallback_svg = delicious_recipes_get_fallback_svg( 'recipe-feat-gallery', true );

			$recipe_keys     = array();
			$recipe_courses  = array();
			$cooking_methods = array();
			$cuisine         = array();
			$ingredients     = array();
			$instructions    = array();
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
				$tax_color   = isset( $badge_metas['taxonomy_color'] )  && ! empty( $badge_metas['taxonomy_color'] ) ? $badge_metas['taxonomy_color'] : '#E84E3B';

				$recipe_badges = array(
					'badge' => $recipe_metas->badges[0],
					'link'  => $link,
					'color' => $tax_color
				);
			}

			if ( ! empty( $recipe_metas->ingredients ) ) {
				$ingredient_string_format = isset( $global_settings['ingredientStringFormat'] ) ? $global_settings['ingredientStringFormat'] : '{qty} {unit} {ingredient} {notes}';
                foreach( $recipe_metas->ingredients as $key => $ingre_section ) :
                    $ingre  = isset( $ingre_section['ingredients'] ) ? $ingre_section['ingredients'] : array();
            		foreach( $ingre as $ingre_key => $ingredient ) : 

                        $ingredient_qty  = isset( $ingredient['quantity'] ) ? $ingredient['quantity'] : 0;
                        $ingredient_unit = isset( $ingredient['unit'] ) ? $ingredient['unit'] : '';
                        $unit_text       = ! empty( $ingredient_unit ) ? delicious_recipes_get_unit_text( $ingredient_unit, $ingredient_qty ) : '';

                        $ingredient_keys = array(
                            '{qty}'        => isset( $ingredient['quantity'] ) ? '<span class="ingredient_quantity" data-original="'. $ingredient['quantity'] .'" data-recipe="'. $recipe->ID .'">' . $ingredient['quantity'] . '</span>' : '',
                            '{unit}'       => $unit_text,
                            '{ingredient}' => isset( $ingredient['ingredient'] ) ? $ingredient['ingredient'] : '',
                            '{notes}'      => isset( $ingredient['notes'] ) && ! empty( $ingredient['notes'] ) ? '<span class="ingredient-notes" >(' . $ingredient['notes'] . ')</span>' : '',
                        );
						$ingre_string = str_replace( array_keys( $ingredient_keys ), $ingredient_keys, $ingredient_string_format );
						$ingredients[] = array( 
							'ingre_string' => $ingre_string
						);
                    endforeach;
                endforeach;
			}

			if ( ! empty( $recipe_metas->instructions ) ) {
				foreach( $recipe_metas->instructions as $sec_key => $intruct_section ) : 
					foreach( $intruct_section['instruction'] as $inst_key => $instruct ) : 
						$instruction_title = isset( $instruct['instructionTitle'] ) ? $instruct['instructionTitle'] : '';
						$instruction       = isset( $instruct['instruction'] ) ? $instruct['instruction'] : '';
						$instruction_notes = isset( $instruct['instructionNotes'] ) ? $instruct['instructionNotes'] : '';
						$instruction_image = isset( $instruct['image'] ) && ! empty( $instruct['image'] ) ? wp_get_attachment_image( $instruct['image'], 'full' ) : false;
						$instruction_video = isset( $instruct['videoURL'] ) && ! empty( $instruct['videoURL'] ) ? $instruct['videoURL'] : false;
						$instructions[] = array( 
							'title' => $instruction_title,
							'instruction' => $instruction,
							'notes' => $instruction_notes,
							'image' => $instruction_image,
							'video' => $instruction_video
						);
					endforeach;
				endforeach;
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
				'calories'         => $recipe_metas->recipe_calories,
				'difficulty_level' => $recipe_metas->difficulty_level,
				'best_season'      => $recipe_metas->best_season,
				'notes'            => $recipe_metas->notes,
				'ingredients'      => $ingredients,
				'instructions'     => $instructions,
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
add_action( 'wp_ajax_delicious_recipes_recipe_card_block_ajax', 'delicious_recipes_recipe_card_block_ajax' );
