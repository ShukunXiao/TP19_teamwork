<?php
/**
 * Responsible for importing Blossom Recipe Maker recipes.
 *
 * @since      1.0.0
 *
 * @package    Delicious_Recipes
 * @subpackage Delicious_Recipes/src/import
 */

class Delicious_Recipes_Import_Blossom_Recipe_Maker {

	/**
	 * Get the UID of this import source.
	 *
	 * @since    1.0.0
	 */
	public function get_uid() {
		return 'blossomrecipemaker';
	}

	/**
	 * Get the name of this import source.
	 *
	 * @since    1.0.0
	 */
	public function get_name() {
		return __( 'Blossom Recipe Maker', 'delicious-recipes' );
	}

	/**
	 * is_plugin_active
	 */
	public function is_plugin_active() {
		return is_plugin_active( 'blossom-recipe-maker/blossom-recipe-maker.php' );
	}

	/**
	 * Get HTML for the import settings.
	 *
	 * @since    1.0.0
	 */
	public function get_settings_html() {
		$html = '<h4>' . __( "Recipe Taxonomies Mapping", 'delicious-recipes' ) . '</h4>';

		$brm_taxonomies = array(
			'recipe-category'       => 'Categories',
			'recipe-cuisine'        => 'Cuisines',
			'recipe-cooking-method' => 'Cooking Methods',
			'recipe-tag'            => 'Tags',
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
			'post_type'      => 'blossom-recipe',
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
			'post_type'      => 'blossom-recipe',
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
		$brm_recipe = get_post_meta( $id, 'br_recipe', true );
		$brm_gallery = get_post_meta( $id, 'br_recipe_gallery', true);

		$recipe = array(
			'import_id'     => $import_id,
			'import_backup' => array(
				'brm_recipe_id'        => $id,
				'brm_recipe_settings'  => $brm_recipe,
				'brm_gallery_settings' => $brm_gallery
			),
		);

		$recipe['image_id']                = get_post_thumbnail_id( $id );
		$recipe['name']                    = $post->post_title;
		$recipe['description']             = $post->post_content;
		$recipe['excerpt']                 = has_excerpt( $id ) ? get_the_excerpt( $id ) : '';
		$recipe['meta']['noOfServings']    = isset( $brm_recipe['details']['servings'] ) ? $brm_recipe['details']['servings'] : '';
		$recipe['meta']['difficultyLevel'] = isset( $brm_recipe['details']['difficulty_level'] ) ? $brm_recipe['details']['difficulty_level'] : '';

		// Recipe Times.
		$recipe['meta']['prepTime'] = isset( $brm_recipe['details']['prep_time'] ) ? $brm_recipe['details']['prep_time'] : '';
		$recipe['meta']['cookTime'] = isset( $brm_recipe['details']['cook_time'] ) ? $brm_recipe['details']['cook_time'] : '';
		$recipe['meta']['restTime'] = '';
		$recipe['meta']['restTimeUnit'] = 'min';
		$recipe['meta']['prepTimeUnit'] = 'min';
		$recipe['meta']['cookTimeUnit'] = 'min';

		$recipe['meta']['recipeNotes'] = isset( $brm_recipe['notes'] ) ? $brm_recipe['notes'] : ''; 

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
						$tax_image_id = get_term_meta ( $term->term_id, 'taxonomy-thumbnail-id', true );
						$tax_meta['taxonomy_image'] = isset( $tax_image_id ) ? absint( $tax_image_id ) : '';
						update_term_meta( $term->term_id, 'dr_taxonomy_metas', $tax_meta );
					}
				}
			}
		}

		// Recipe Ingredients.
		$ingredients = isset( $brm_recipe['ingredient'] ) ? $brm_recipe['ingredient'] : array();
		$recipe['meta']['recipeIngredients'] = array();

		$current_group = array(
			'sectionTitle' => '',
			'ingredients'  => array(),
		);
		foreach ( $ingredients as $ingredient ) {
			if ( isset( $ingredient['heading'] ) ) {
				// Only add to the array if it has multiple headings
				if( ! empty( $current_group['ingredients'] ) ) {
					$recipe['meta']['recipeIngredients'][] = $current_group;
				}
				$current_group   = array(
					'sectionTitle' => $ingredient['heading'],
					'ingredients'  => array(),
				);
			} else {
				$current_group['ingredients'][] = array(
					'quantity'   => $ingredient['quantity'],
					'unit'       => $ingredient['unit'],
					'ingredient' => $ingredient['ingredient'],
					'notes'      => $ingredient['notes'],
				);
			}
		}
		$recipe['meta']['recipeIngredients'][] = $current_group;

		// Recipe Instructions.
		$instructions = isset( $brm_recipe['instructions'] ) ? $brm_recipe['instructions'] : array();
		$recipe['meta']['recipeInstructions'] = array();

		$current_group = array(
			'sectionTitle' => '',
			'instruction'  => array(),
		);
		foreach ( $instructions as $instruction ) {
			if ( isset( $instruction['heading'] ) ) {
				// Only add to the array if it has multiple headings
				if( ! empty( $current_group['instruction'] ) ) {
					$recipe['meta']['recipeInstructions'][] = $current_group;
				}
				$current_group   = array(
					'sectionTitle' => $instruction['heading'],
					'instruction'  => array(),
				);
			} else {
				$url = isset( $instruction['image'] ) && '' != $instruction['image'] ? wp_get_attachment_image_url( $instruction['image'], 'thumbnail' ) : '';
				$current_group['instruction'][] = array(
					'instructionTitle' => "",
					'instruction'      => $instruction['description'],
					'image'            => $instruction['image'],
					'image_preview'    => $url,
					'videoURL'         => "",
					'instructionNotes' => "",
				);
			}
		}
		$recipe['meta']['recipeInstructions'][] = $current_group;

		// Gallery and Video
		if( isset( $brm_gallery['enable'] ) && '1' === $brm_gallery['enable'] ) {
			$recipe['meta']['enableImageGallery']['0'] = "yes";
		}

		if( isset( $brm_gallery['video_url'] ) && '' != $brm_gallery['video_url'] ) {
			$recipe['meta']['enableVideoGallery']['0'] = "yes";

			$video_data = delicious_recipes_parse_videos( $brm_gallery['video_url'] );
			$video_attr = isset( $video_data['0'] ) && ! empty( $video_data['0'] ) ? $video_data['0'] : array();
			
			if( ! empty( $video_attr ) ) {
				$recipe['meta']['videoGalleryVids'] = array(
					'0' => array(
						'vidID'    => $video_attr['id'],
						'vidType'  => $video_attr['type'],
						'vidThumb' => $video_attr['thumbnail']
					)
				);
			}
		}

		if ( $brm_gallery ) {
			$gallery_images = array();
			unset( $brm_gallery['enable'], $brm_gallery['video_url'] );
			foreach( $brm_gallery as $key => $image ) {
				$url = isset( $image ) && '' != $image ? wp_get_attachment_image_url( $image, 'thumbnail' ) : '';
				$gallery_images[] = array(
					'imageID'    => $image,
					'previewURL' => $url
				);
			}
			$recipe['meta']['imageGalleryImages'] = $gallery_images;
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

		global $wpdb;

		$where[] = "post_type = 'post'";
		$where[] = "post_type = 'page'";
		$where_query = implode(' OR ', $where);

		$search_shortcode = '[recipe-maker id='."'".$id."'".']';
		$replace_shortcode = '[recipe_page id='."'".$dr_id."'".']';

		$search = stripslashes_deep( $search_shortcode );
		$replace = stripslashes_deep( $replace_shortcode );

		$query = $wpdb->prepare( 
			"UPDATE ".$wpdb->posts."
				SET post_excerpt = REPLACE(post_excerpt, %s, %s),
				post_content = REPLACE(post_content, %s, %s),
				post_title = REPLACE(post_title, %s, %s)
				WHERE ".$where_query,
				$search, $replace, $search, $replace, $search, $replace
		);

		$res = $wpdb->query( 
			$query
		);

		$this->replace_recent_popular_shortcodes();

		$delicious_taxonomies = delicious_recipes_get_taxonomies();
		foreach ( $delicious_taxonomies as $dr_taxonomy => $name ) {
			$tag = isset( $post_data[ 'brm-tags-' . $dr_taxonomy ] ) ? $post_data[ 'brm-tags-' . $dr_taxonomy ] : false;

			if ( $tag ) {
				$terms = get_the_terms( $id, $tag );
				if ( $terms && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$dr_term = get_term_by( 'name', $term->name, $dr_taxonomy );

						if ( $dr_term && ! is_wp_error( $dr_term ) && $term->name === $dr_term->name) {
							$tax_image_id = get_term_meta ( $term->term_id, 'taxonomy-thumbnail-id', true );
							$tax_meta['taxonomy_image'] = isset( $tax_image_id ) ? absint( $tax_image_id ) : '';
							update_term_meta( $dr_term->term_id, 'dr_taxonomy_metas', $tax_meta );
							wp_update_term( $dr_term->term_id, $dr_taxonomy, array( 'description' => wp_kses_post( $term->description ) ) );
						}
					}
				}
			}
		}

	}

	public function replace_recent_popular_shortcodes() { 

		$option = get_option( '_dr_import_recent_popular_shortcodes', array() );
		
		if ( isset ( $option ) && '1' === $option ) {
			return;
		}

		global $wpdb;

		$where[] = "post_type = 'post'";
		$where[] = "post_type = 'page'";
		$where_query = implode(' OR ', $where);

		$shortcodes = array(
			'0' => array(
				'search' => '[brm-recipes]',
				'replace' => '[dr_recipes]'
			),
			'1' => array(
				'search' => '[brm-recipes popular='."'views'".']',
				'replace' => '[dr_popular_recipes based_on='."'views'".']'
			),
			'2' => array(
				'search' => '[brm-recipes popular='."'comments'".']',
				'replace' => '[dr_popular_recipes based_on='."'comments'".']'
			),
		);

		foreach( $shortcodes as $key => $shortcode ) {

			$search = stripslashes_deep( $shortcode['search'] );
			$replace = stripslashes_deep( $shortcode['replace'] );
	
			$query = $wpdb->prepare( 
				"UPDATE ".$wpdb->posts."
					SET post_excerpt = REPLACE(post_excerpt, %s, %s),
					post_content = REPLACE(post_content, %s, %s),
					post_title = REPLACE(post_title, %s, %s)
					WHERE ".$where_query,
					$search, $replace, $search, $replace, $search, $replace
			);
	
			$res = $wpdb->query( 
				$query
			);

		}
		
		update_option( '_dr_import_recent_popular_shortcodes', '1' );
	}
}
