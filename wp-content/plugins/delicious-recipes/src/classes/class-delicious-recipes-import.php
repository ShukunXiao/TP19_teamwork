<?php
/**
 * Responsible for handling the import of recipes from other sources.
 * 
 * @package Delicious_Recipes
 * @since 1.0.0
 */

class Delicious_Recipes_Import {

	/**
	 * Importers that can be used to import recipes from other sources.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $importers    Array containing all available importers.
	 */
	private static $importers = array();

	/**
     * Constructor
     */
	public function __construct() {
		$this->load_importers();

		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );

		add_action( 'wp_ajax_dr_import_recipes', array( $this, 'ajax_import_recipes' ) );
	}

	/**
	 * Add the import submenu to the Delicious Recipes menu.
	 *
	 * @since    1.0.0
	 */
	public function add_submenu_page() {

		add_submenu_page( 
			'edit.php?post_type=' . DELICIOUS_RECIPE_POST_TYPE, 
			__( 'Import Recipes', 'delicious-recipes' ), 
			__( 'Import Recipes', 'delicious-recipes' ), 
			'manage_options', 
			'delicious_recipes_import_recipes', 
			array( $this, 'display_import_menu_page' ),
			10
		);
	}

	/**
	 * Display import page template
	 * 
	 * @since 1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function display_import_menu_page() {
		include plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/admin/partials/dr-import-recipe-screen.php';
	}

	/**
	 * Load all available importers from the /src/import directory.
	 *
	 * @since    1.0.0
	 */
	public function load_importers() {

		$importers = array();

		$supported_importers = array( 
			'0' => array(
				'id'        => 'blossom-recipe-maker',
				'name'      => 'Blossom_Recipe_Maker',
				'file_path' => plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . 'src/import/class-delicious-recipes-import-blossom-recipe-maker.php',
			),
			'1' => array(
				'id'        => 'wp-recipe-maker',
				'name'      => 'WP_Recipe_Maker',
				'file_path' => plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . 'src/import/class-delicious-recipes-import-wp-recipe-maker.php',
			),
			
		);
    
		foreach( $supported_importers as $key => $importer ) {
			if ( file_exists( plugin_dir_path( $importer['id'] ) ) ) {
				require_once( $importer['file_path'] );
				$class_name    = 'Delicious_Recipes_Import_' . $importer['name'];
				$importers[]   = new $class_name();
			}
		}
		self::$importers = $importers;
	}

	/**
	 * Parse ingredients submitted through AJAX.
	 *
	 * @since    1.0.0
	 */
	public function ajax_import_recipes() {
		if ( wp_verify_nonce( $_POST['security'], 'dr_import_recipes' ) ) {
			$importer_uid = isset( $_POST['importer_uid'] ) ? sanitize_title( wp_unslash( $_POST['importer_uid'] ) ) : '';
			$recipes      = isset( $_POST['recipes'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['recipes'] ) ) : array();
			
			$parsed_array = array();
			$post_data    = isset( $_POST['post_data'] ) ? parse_str( $_POST['post_data'], $parsed_array) : array();
			$post_data    = array_map( 'sanitize_text_field', $parsed_array );

			$importer = $this->get_importer( $importer_uid );

			if ( $importer && count( $recipes ) > 0 ) {
				$result = $this->import_recipes( $importer, $recipes, $post_data ); // Input var okay.
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( $result->get_error_message() );
				}
			}

			wp_send_json_success( array(
				'post_data'        => $post_data,
				'uid'              => $importer_uid,
				'recipes_imported' => $recipes,
			) );
		}

		wp_die();
	}

	/**
	 * Import recipes using the specified importer.
	 *
	 * @since    1.0.0
	 * @param		 object $importer Importer to use for importing.
	 * @param		 array  $recipes IDs of recipes to import.
	 * @param		 array  $post_data POST data passed along when submitting the form.
	 */
	public function import_recipes( $importer, $recipes, $post_data ) {
		// Reverse sort by ID to make sure multiple recipes in the same post are handled correctly.
		arsort( $recipes );

		foreach ( $recipes as $import_recipe_id ) {
			$imported_recipe = $importer->get_recipe( $import_recipe_id, $post_data );
			$imported_recipe = apply_filters( 'delicious_recipes_import_recipe_' . $importer->get_uid(), $imported_recipe, $import_recipe_id, $post_data );

			if ( is_wp_error( $imported_recipe ) ) {
				return $imported_recipe;
			}

			if ( $imported_recipe ) {
				$imported_recipe['import_source'] = $importer->get_uid();

				$recipe_id = isset( $imported_recipe['import_id'] ) ? intval( $imported_recipe['import_id'] ) : 0;
				$recipe = $imported_recipe;

				if ( $recipe_id ) {
					if ( DELICIOUS_RECIPE_POST_TYPE !== get_post_type( $recipe_id ) ) {
						set_post_type( $recipe_id, DELICIOUS_RECIPE_POST_TYPE );
					}
					$this->update_recipe( $recipe_id, $recipe );
				} else {
					$recipe_id = $this->create_recipe( $recipe );
				}

				$result = $importer->replace_recipe( $import_recipe_id, $recipe_id, $post_data );

				if ( is_wp_error( $result ) ) {
					return $result;
				}
			}
		}
	}

	/**
	 * Get importer by UID.
	 *
	 * @since    1.0.0
	 * @param		 int $uid UID of the importer.
	 */
	public function get_importer( $uid ) {
		$importer = false;
		foreach ( self::$importers as $possible_importer ) {
			if ( sanitize_title( $possible_importer->get_uid() ) === $uid ) {
				$importer = $possible_importer;
			}
		}

		return $importer;
	}

	/**
	 * Create a new Delicious Recipe.
	 *
	 * @since    1.0.0
	 * @param		 array $recipe Recipe fields to save.
	 */
	public function create_recipe( $recipe ) {
		$post = array(
			'post_type' => DELICIOUS_RECIPE_POST_TYPE,
			'post_status' => 'draft',
		);

		$recipe_id = wp_insert_post( $post );
		$this->update_recipe( $recipe_id, $recipe );

		return $recipe_id;
	}

	/**
	 * Save recipe fields.
	 *
	 * @since    1.0.0
	 * @param		 int   $id Post ID of the recipe.
	 * @param		 array $recipe Recipe fields to save.
	 */
	public function update_recipe( $id, $recipe ) {
		$meta = array();

		// Featured Image.
		if ( isset( $recipe['image_id'] ) ) {
			if ( $recipe['image_id'] ) {
				set_post_thumbnail( $id, intval( $recipe['image_id'] ) );
			} else {
				delete_post_thumbnail( $id );
			}
		}

		// // Recipe Taxonomies.
		if ( isset( $recipe['tags'] ) ) {
			$taxonomies = delicious_recipes_get_taxonomies();
			foreach ( $taxonomies as $taxonomy => $label ) {
				if( isset( $recipe['tags'][ $taxonomy ] ) ) {
					wp_set_object_terms( $id, $recipe['tags'][ $taxonomy ], $taxonomy, false );
				}
			}
		}

		// Sanitize settings
		$recipe_meta = delicious_recipes_sanitize_metas( $recipe['meta'] );

		update_post_meta( $id, 'delicious_recipes_metadata', $recipe_meta );

		// update seasons 
		if( isset( $recipe_meta['bestSeason'] ) ) {
			update_post_meta( $id, '_dr_best_season', sanitize_text_field( $recipe_meta['bestSeason'] ) );
		}

		// update difficulty levels
		if( isset( $recipe_meta['difficultyLevel'] ) ) {
			update_post_meta( $id, '_dr_difficulty_level', sanitize_text_field( $recipe_meta['difficultyLevel'] ) );
		}

		// update ingredients
		$ingredients = delicious_recipes_get_single_ingredients( $id );

		if( ! empty( $ingredients ) && is_array( $ingredients ) ) {
			$ingredients = array_map( 'sanitize_text_field', $ingredients );
			update_post_meta( $id, '_dr_recipe_ingredients', $ingredients );
			
			$ingredient_count = count( $ingredients );
			update_post_meta( $id, '_dr_ingredient_count', absint( $ingredient_count ) );
		}

		// Post Fields.
		$post = array(
			'ID' => $id,
		);

		if ( isset( $recipe['name'] ) ) {
			$post['post_title'] = $recipe['name'];
			$post['post_name']  =  sanitize_title( $recipe['name'] );
		}

		if ( isset( $recipe['description'] ) ) {
			$post['post_content'] = wp_kses_post( $recipe['description'] );
		}

		if ( isset( $recipe['excerpt'] ) ) {
			$post['post_excerpt'] = wp_kses_post( $recipe['excerpt'] );
		}

		// Always update post to make sure revision gets made.
		wp_update_post( $post );
	}

}

new Delicious_Recipes_Import();
