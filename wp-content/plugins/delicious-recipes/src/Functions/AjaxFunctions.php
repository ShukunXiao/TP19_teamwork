<?php
/**
 * AJAX Functions.
 *
 * @package DELICIOUS_RECIPES
 * @subpackage  DELICIOUS_RECIPES
 */

namespace WP_Delicious;

defined( 'ABSPATH' ) || exit;

/**
 * Global Settings.
 */
class AjaxFunctions {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init() {
		// Initialize hooks.
		$this->init_hooks();

		// Allow 3rd party to remove hooks.
		do_action( 'wfe_ajaxfunctions_unhook', $this );
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init_hooks() {
		// Ajax for adding featured recipe meta
		add_action( 'wp_ajax_featured_recipe', array( $this, 'featured_recipe_admin_ajax' ) );

		// Ajax for Recipe Categories Widget
		add_action( 'wp_ajax_dr_recipe_taxonomy_terms', array( $this, 'dr_recipe_taxonomy_terms' ) );

		// Clone Existing Recipes
		add_action( 'wp_ajax_dr_clone_recipe_data', array( $this, 'dr_clone_recipe_data' ) );

		// Ajax for Recipe Search
		add_action( 'wp_ajax_recipe_search_results', array( $this, 'recipe_search_results' ) );
		add_action( 'wp_ajax_nopriv_recipe_search_results', array( $this, 'recipe_search_results' ) );

		// AJAX for Whats new page changelog query
		add_action( 'wp_ajax_dr_get_latest_changelog', array( $this, 'get_latest_changelog' ) );
	}

	/**
	 * Ajax for adding featured recipe meta	 
	 * 
	 * */
	public function featured_recipe_admin_ajax() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'wp_delicious_featured_recipe_nonce' ) ) {
			exit( 'invalid' );
		}

		header( 'Content-Type: application/json' );
		$post_id         = intval( $_POST['post_id'] );
		$featured_status = esc_attr( get_post_meta( $post_id, 'wp_delicious_featured_recipe', true ) );
		$new_status      = $featured_status == 'yes' ? 'no' : 'yes';
		update_post_meta( $post_id, 'wp_delicious_featured_recipe', $new_status );
		echo json_encode(
			array(
				'ID'         => $post_id,
				'new_status' => $new_status,
			)
		);
		die();
	}

	/**
	 * Ajax for Recipe Categories Widget 
	 * 
	 * */
	public function dr_recipe_taxonomy_terms() {

		$terms    = array();
		$taxonomy = isset( $_POST['taxonomy'] ) && ! empty( $_POST['taxonomy'] ) ? sanitize_title( $_POST['taxonomy'] ) : false;

		if ( $taxonomy ) {
			$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
			) );
		}

		wp_send_json_success( $terms );

	}

	/**
	 * Ajax callback function to clone recipe data.
	 *
	 */
	public function dr_clone_recipe_data() {

        // Nonce checks.
		check_ajax_referer( 'dr_clone_recipe_nonce', 'security' );

		if ( ! isset( $_POST['post_id'] ) || empty( $_POST['post_id'] ) ) {
			return;
		}

		$post_id   = absint( $_POST['post_id'] );
		$post_type = get_post_type( $post_id );

		if ( DELICIOUS_RECIPE_POST_TYPE !== $post_type ) {
			return;
		}
		$post = get_post( $post_id );

		$post_array = array(
			'post_title'   => $post->post_title,
			'post_content' => $post->post_content,
			'post_status'  => 'draft',
			'post_type'    => DELICIOUS_RECIPE_POST_TYPE,
		);

		// Cloning old recipe.
		$new_post_id = wp_insert_post( $post_array );

		// Cloning old recipe meta.
		$all_old_meta = get_post_meta( $post_id );

		if ( is_array( $all_old_meta ) && count( $all_old_meta ) > 0 ) {
			foreach ( $all_old_meta as $meta_key => $meta_value_array ) {
				$meta_value = isset( $meta_value_array[0] ) ? $meta_value_array[0] : '';

				if ( '' !== $meta_value ) {
					$meta_value = maybe_unserialize( $meta_value );
				}
				update_post_meta( $new_post_id, $meta_key, $meta_value );
			}
		}

		// Cloning taxonomies
		$recipe_taxonomies = array( 'recipe-key', 'recipe-tag', 'recipe-cooking-method', 'recipe-cuisine', 'recipe-course' );
		foreach ( $recipe_taxonomies as $taxonomy ) {
			$recipe_terms      = get_the_terms( $post_id, $taxonomy );
			$recipe_term_names = array();
			if ( is_array( $recipe_terms ) && count( $recipe_terms ) > 0 ) {
				foreach ( $recipe_terms as $post_terms ) {
					$recipe_term_names[] = $post_terms->name;
				}
			}
			wp_set_object_terms( $new_post_id, $recipe_term_names, $taxonomy );
		}
		wp_send_json( array( 'true' ) );
	}

	/**
	 * Ajax for Recipe Search 
	 * 
	 * */
	public function recipe_search_results() {

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'dr-search-nonce' ) ) {
			exit( 'invalid' );
		}

		$options                = delicious_recipes_get_global_settings();
		$default_posts_per_page = isset ( $options['recipePerPage'] ) && ( ! empty( $options['recipePerPage'] ) ) ? $options['recipePerPage'] : get_option( 'posts_per_page' );

		$recipe_search_args = array(
			'post_type'        => DELICIOUS_RECIPE_POST_TYPE,
			'posts_per_page'   => absint( $default_posts_per_page ),
			'suppress_filters' => false,
			'post_status'      => 'publish'
		);
		
		$meta_query = array();

        if ( isset( $_REQUEST['search']['recipe_ingredients'] ) && ! empty( $_REQUEST['search']['recipe_ingredients'] ) && is_array( $_REQUEST['search']['recipe_ingredients'] ) ) {
			$recipe_ingredients = array_map( 'sanitize_text_field', $_REQUEST['search']['recipe_ingredients'] );
			foreach( $recipe_ingredients as $ingredient ) {
				array_push( $meta_query,
					array(
						'key' 		=> '_dr_recipe_ingredients',
						'value' 	=> sanitize_text_field( $ingredient ),
						'compare' 	=> 'LIKE',
					)
				);
			}
		}

		if( isset( $_REQUEST['search']['seasons'] ) && ! empty( $_REQUEST['search']['seasons'] ) ){
			$seasons = array_map( 'sanitize_text_field', $_REQUEST['search']['seasons'] );
			array_push( $meta_query,
				array(
					'key' 		=> '_dr_best_season',
					'value' 	=> $seasons,
					'compare' 	=> 'IN',
				)
			);
		}

		if( isset( $_REQUEST['search']['difficulty_level'] ) && ! empty( $_REQUEST['search']['difficulty_level'] ) ){
			$difficulty_level = array_map( 'sanitize_text_field', $_REQUEST['search']['difficulty_level'] );
			array_push( $meta_query,
				array(
					'key' 		=> '_dr_difficulty_level',
					'value' 	=> $difficulty_level,
					'compare' 	=> 'IN',
				)
			);
		}

		if ( isset( $_REQUEST['search']['simple_factor'] ) && ! empty( $_REQUEST['search']['simple_factor'] ) ) {
			$simple_factor_array = is_array( $_REQUEST['search']['simple_factor'] ) ? array_map( 'sanitize_text_field', $_REQUEST['search']['simple_factor'] ) : array();
			foreach( $simple_factor_array as $key => $factor ) {
				switch( $factor ) {
					case '10-ingredients-or-less' :
						array_push( $meta_query,
							array(
								'key'     => '_dr_ingredient_count',
								'value'   => 10,
								'compare' => '<=',
							)
						);
					break;
					case '15-minutes-or-less' :
						array_push( $meta_query,
							array(
								'key'     => '_dr_recipe_total_time',
								'value'   => 15,
								'compare' => '<=',
							)
						);
					break;
					case '30-minutes-or-less' :
						array_push( $meta_query,
							array(
								'key'     => '_dr_recipe_total_time',
								'value'   => 30,
								'compare' => '<=',
							)
						);
					break;
					case '7-ingredients-or-less' :
						array_push( $meta_query,
							array(
								'key'     => '_dr_ingredient_count',
								'value'   => 7,
								'compare' => '<=',
							)
						);
					break;
				}
			}
		}

		if ( !empty( $meta_query ) ) {
			$recipe_search_args['meta_query'] = $meta_query;
		}
		
		$taxquery = array();

		if( ! empty( $_REQUEST["search"]["recipe_courses"] ) && $_REQUEST["search"]["recipe_courses"] != '-1'  ){
			$recipe_courses = is_array( $_REQUEST["search"]["recipe_courses"] ) ? array_map( 'absint', $_REQUEST["search"]["recipe_courses"] ) : array();
			array_push( $taxquery, array(
					'taxonomy' => 'recipe-course',
					'field'    => 'term_id',
					'terms'    => $recipe_courses,
					'include_children' => false,
				)
			);
		}

		if( ! empty( $_REQUEST["search"]["recipe_cooking_methods"] ) && $_REQUEST["search"]["recipe_cooking_methods"] != '-1'  ){
			$recipe_cooking_methods = is_array( $_REQUEST["search"]["recipe_cooking_methods"] ) ? array_map( 'absint', $_REQUEST["search"]["recipe_cooking_methods"] ) : array();
			array_push( $taxquery, array(
					'taxonomy' => 'recipe-cooking-method',
					'field'    => 'term_id',
					'terms'    => $recipe_cooking_methods,
					'include_children' => false,
				));
		}

		if( ! empty( $_REQUEST["search"]["recipe_cuisines"] ) && $_REQUEST["search"]["recipe_cuisines"] != '-1'  ){
			$recipe_cuisines = is_array( $_REQUEST["search"]["recipe_cuisines"] ) ? array_map( 'absint', $_REQUEST["search"]["recipe_cuisines"] ) : array();
			array_push( $taxquery, array(
					'taxonomy' => 'recipe-cuisine',
					'field'    => 'term_id',
					'terms'    => $recipe_cuisines,
					'include_children' => false,
				));
		}

		if( ! empty( $_REQUEST["search"]["recipe_keys"] ) && $_REQUEST["search"]["recipe_keys"] != '-1'  ){
			$recipe_keys = is_array( $_REQUEST["search"]["recipe_keys"] ) ? array_map( 'absint', $_REQUEST["search"]["recipe_keys"] ) : array();
			array_push( $taxquery, array(
					'taxonomy' => 'recipe-key',
					'field'    => 'term_id',
					'terms'    => $recipe_keys,
					'include_children' => false,
				));
		}

		if( ! empty( $_REQUEST["search"]["recipe_tags"] ) && $_REQUEST["search"]["recipe_tags"] != '-1'  ){
			$recipe_tags = is_array( $_REQUEST["search"]["recipe_tags"] ) ? array_map( 'absint', $_REQUEST["search"]["recipe_tags"] ) : array();
			array_push( $taxquery, array(
					'taxonomy' => 'recipe-tag',
					'field'    => 'term_id',
					'terms'    => $recipe_tags,
					'include_children' => false,
				));
		}

		if( ! empty( $_REQUEST["search"]["recipe_badges"] ) && $_REQUEST["search"]["recipe_badges"] != '-1'  ){
			$recipe_badges = is_array( $_REQUEST["search"]["recipe_badges"] ) ? array_map( 'absint', $_REQUEST["search"]["recipe_badges"] ) : array();
			array_push( $taxquery, array(
					'taxonomy' => 'recipe-badge',
					'field'    => 'term_id',
					'terms'    => $recipe_badges,
					'include_children' => false,
				));
		}

		if( ! empty( $taxquery ) ) {
			$recipe_search_args['tax_query']['relation'] = 'AND';
			$recipe_search_args['tax_query']             = $taxquery;
		}

		if ( isset( $_REQUEST['search']['sorting']['0'] ) && ! empty( $_REQUEST['search']['sorting']['0'] ) ) {
			$sort = sanitize_title( $_REQUEST['search']['sorting']['0'] );
			switch( $sort ) {
				case 'title_asc' :
					$recipe_search_args['order'] = 'ASC';
					$recipe_search_args['orderby'] = 'title'; 
				break;
				case 'title_desc' :
					$recipe_search_args['order'] = 'DESC';
					$recipe_search_args['orderby'] = 'title'; 
				break;
				case 'date_desc' :
					$recipe_search_args['order'] = 'DESC';
					$recipe_search_args['orderby'] = 'date'; 
				break;
				case 'date_asc' :
					$recipe_search_args['order'] = 'ASC';
					$recipe_search_args['orderby'] = 'date'; 
				break;
			}
		}

		if ( isset( $_REQUEST['paged'] ) && ! empty( $_REQUEST['paged'] ) ) {
			$recipe_search_args['paged'] = absint( $_REQUEST['paged'] );
		}

		$recipe_search = new \WP_Query( $recipe_search_args );

		$results = array();

		while ( $recipe_search->have_posts() ) {

			$recipe_search->the_post();

			$recipe = get_post( get_the_ID() );
			$recipe_metas = \delicious_recipes_get_recipe( $recipe );

			// Get global toggles.
			$global_toggles = delicious_recipes_get_global_toggles_and_labels();

			$img_size = $global_toggles['enable_recipe_archive_image_crop'] ? 'recipe-archive-grid' : 'full';
			$thumbnail_id = has_post_thumbnail( $recipe_metas->ID ) ? get_post_thumbnail_id( $recipe_metas->ID ) : '';
			$thumbnail    = $thumbnail_id ? get_the_post_thumbnail( $recipe_metas->ID, $img_size ) : '';

			$recipe_keys = array();

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

			$recipe_badges = false;

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

			$results[] = array(
				'recipe_id'        => $recipe_metas->ID,
				'title'            => $recipe_metas->name,
				'permalink'        => $recipe_metas->permalink,
				'thumbnail_id'     => $recipe_metas->thumbnail_id,
				'thumbnail_url'    => $recipe_metas->thumbnail,
				'thumbnail'        => $thumbnail,
				'recipe_keys'      => $recipe_keys,
				'total_time'       => $recipe_metas->total_time,
				'difficulty_level' => $recipe_metas->difficulty_level,
				'recipe_calories'  => $recipe_metas->recipe_calories,
				'enable_pinit'     => $global_toggles['enable_pintit'],
				'badges'           => $recipe_badges,
			);

		}
			$pagination = false;
			/**
			 * Get Pagination.
			 */
			$total_pages = $recipe_search->max_num_pages;
			$big         = 999999999;                   // need an unlikely integer
			$paged       = isset( $_REQUEST['paged'] ) && ! empty( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;

			if ( $total_pages > 1 ){
				$current_page = max( 1, $paged );
	
				$pagination = paginate_links(array(
					'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format'    => '?paged=%#%',
					'current'   => $current_page,
					'total'     => absint( $total_pages ),
					'prev_text' => __( 'Prev', 'delicious-recipes' ) .
					'<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689">
						<g transform="translate(17.729 11.628) rotate(180)">
							<path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none"
								stroke="#374757" stroke-linecap="round" stroke-width="1.5" />
							<path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#374757"
								stroke-linecap="round" stroke-width="1.5" />
						</g>
					</svg>',
					'next_text' => __( "Next", 'delicious-recipes' ) .
					'<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(0.75 1.061)">
							<path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none"
								stroke="#374757" stroke-linecap="round" stroke-width="1.5" />
							<path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#374757"
								stroke-linecap="round" stroke-width="1.5" />
						</g>
					</svg>',
				));
			}
		// Reset postdata.
		wp_reset_postdata();

		wp_send_json_success( [ 'results' => $results, 'pagination' => $pagination ] );
		die();

	}

	/**
	 * Get Latest Changelog
	 *
	 * @return void
	 */
	public function get_latest_changelog() {
		$changelog = null;
		$access_type = get_filesystem_method();

		if ($access_type === 'direct') {
			$creds = request_filesystem_credentials(
				site_url() . '/wp-admin/',
				'', false, false,
				[]
			);

			if (WP_Filesystem($creds)) {
				global $wp_filesystem;

				$changelog = $wp_filesystem->get_contents(
					plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/changelog.txt'
				);
			}
		}

		wp_send_json_success([
			'changelog' => apply_filters(
				'delicious_recipes_changelogs_list',
				[
					[
						'title'     => __('Plugin', 'delicious-recipes'),
						'changelog' => $changelog,
					]
				]
			)
		]);
	}

}

new AjaxFunctions();
