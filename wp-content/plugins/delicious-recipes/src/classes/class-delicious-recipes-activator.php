<?php
namespace WP_Delicious;
/**
 * Fired during plugin activation
 *
 * 
 * @since      1.0.0
 *
 * @package    delicious_recipes
 * @subpackage delicious_recipes/includes
 */
class Delicious_Recipes_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		/**
		 * Insert pages
		 *
		 * @since 1.0.0
		 */
        $pages = apply_filters(
            'delicious_recipes_create_pages', array(
                'recipe-search' => array(
                    'name'    => _x( 'recipe-search', 'Page slug', 'delicious-recipes' ),
                    'title'   => _x( 'Recipe Search', 'Page title', 'delicious-recipes' ),
                    'content' => '[' . apply_filters( 'delicious_recipes_search_page_shortcode_tag', 'recipe_search' ) . ']',
                ),
                'recipe-dashboard' => array(
                    'name' => _x( 'recipe-dashboard', 'Page slug', 'delicious-recipes' ),
                    'title'   => _x( 'Recipe Dashboard', 'Page title', 'delicious-recipes' ),
                    'content' => '[' . apply_filters( 'delicious_recipes_dashboard_page_shortcode_tag', 'dr_user_dashboard' ) . ']',
                )
            )
        );

		foreach ( $pages as $key => $page ) {
            self::create_page( esc_sql( $page['name'] ), 'delicious_recipes_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? self::get_page_id( $page['parent'] ) : '' );
        }
        
        // Insert demo recipes.
        self::insert_demo_recipe();

        // Insert demo taxonomies.
        self::insert_initial_page_templates();

        // Create user roles
        self::create_roles();

        // Add caps to roles.
        self::add_caps();

        // Check if it is first activation.
        self::check_first_activation();

        update_option( 'delicious_recipes_queue_flush_rewrite_rules', 'yes' );
    }

    /**
	 * Create roles and capabilities.
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
		}

        // Recipe Editor role.
        $recipe_editor_caps = apply_filters( 'delicious_recipes_editor_caps', array(
	    	'manage_categories'      => 1,
	    	'upload_files'           => 1,
	    	'unfiltered_html'        => 1,
	    	'edit_posts'             => 1,
	    	'edit_others_posts'      => 1,
	    	'edit_published_posts'   => 1,
	    	'publish_posts'          => 1,
	    	'read'                   => 1,
	    	'delete_posts'           => 1,
	    	'delete_others_posts'    => 1,
	    	'delete_published_posts' => 1,
	    	'delete_private_posts'   => 1,
	    	'edit_private_posts'     => 1,
	    	'read_private_posts'     => 1,
	    	'level_7'                => 1,
	    	'level_6'                => 1,
	    	'level_5'                => 1,
	    	'level_4'                => 1,
	    	'level_3'                => 1,
	    	'level_2'                => 1,
	    	'level_1'                => 1,
	    	'level_0'                => 1
	    ) );
		add_role( 'delicious_recipes_editor', __( 'Recipe Editor', 'delicious-recipes' ), $recipe_editor_caps );

        // Recipe Subscriber role.
		add_role( 'delicious_recipes_subscriber', __( 'Recipe Subscriber', 'delicious-recipes' ), array(
            'read'                   => true,
            'edit_delicious_recipes' => true
            )
        );
    }
    
    /**
     * Add Caps
     *
     * @return void
     */
    public static function add_caps() {
		global $wp_roles;

		if ( class_exists('WP_Roles') ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {

			// Edit Recipes
			$wp_roles->add_cap( 'delicious_recipes_editor', 'edit_delicious_recipes' );
			$wp_roles->add_cap( 'contributor', 'edit_delicious_recipes' );
			$wp_roles->add_cap( 'author', 'edit_delicious_recipes' );
			$wp_roles->add_cap( 'editor', 'edit_delicious_recipes' );
			$wp_roles->add_cap( 'administrator', 'edit_delicious_recipes' );

			// Recipe Settings
			$wp_roles->add_cap( 'administrator', 'edit_delicious_settings' );

		}
	}
    
    /**
     * Create page.
     *
     * @return void
     */
    public static function create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
        global $wpdb;
    
        $option_value = get_option( $option );
    
        if ( $option_value > 0 && ( $page_object = get_post( $option_value ) ) ) {
            if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
                // Valid page is already in place
                if ( strlen( $page_content ) > 0 ) {
                    // Search for an existing page with the specified page content (typically a shortcode)
                    $valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
                } else {
                    // Search for an existing page with the specified page slug
                    $valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
                }
    
                $valid_page_found = apply_filters( 'delicious_recipes_create_page_id', $valid_page_found, $slug, $page_content );
    
                if ( $valid_page_found ) {
                    if ( $option ) {
                        update_option( $option, $valid_page_found );
                    }
                    return $valid_page_found;
                }
            }
        }
    
        // Search for a matching valid trashed page
        if ( strlen( $page_content ) > 0 ) {
            // Search for an existing page with the specified page content (typically a shortcode)
            $trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
        } else {
            // Search for an existing page with the specified page slug
            $trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
        }
    
        if ( $trashed_page_found ) {
            $page_id   = $trashed_page_found;
            $page_data = array(
                'ID'          => $page_id,
                'post_status' => 'publish',
            );
            wp_update_post( $page_data );
        } else {
            $page_data = array(
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => 1,
                'post_name'      => $slug,
                'post_title'     => $page_title,
                'post_content'   => $page_content,
                'post_parent'    => $post_parent,
                'comment_status' => 'closed',
            );
            $page_id   = wp_insert_post( $page_data );
        }
    
        if ( $option ) {
            update_option( $option, $page_id );
        }
    
        return $page_id;
    }

    /**
     * get page ID.
     *
     * @return $page 
     */
    public static function get_page_id( $page ) {

        $settings = get_option( 'delicious_recipe_settings' ); // Not used delicious_recipes_get_global_settings due to infinite loop.
        $page     = str_replace( 'delicious-recipes-', '', $page );
        $page_id  = ( isset( $settings[ $page . '_page_id' ] ) ) ? $settings[ $page . '_page_id' ] : '';

        if ( ! $page_id ) {
            $page_id = get_option( 'delicious_recipes_' . $page . '_page_id' );
        }

        $page_id = apply_filters( 'delicious_recipes_get_' . $page . '_page_id', $page_id );

        return $page_id ? absint( $page_id ) : -1;
    }

    /**
     * Insert demo recipe post type data.
     *
     * @return void
     */
    public static function insert_demo_recipe() {

        $demo_import_settings = get_option( 'delicious_recipes_demo_imports', array() );

        $demo_recipe_created = isset(  $demo_import_settings['delicious_demo_imports']['created_demo_recipe'] ) &&  $demo_import_settings['delicious_demo_imports']['created_demo_recipe'] ? true : false;

        if ( $demo_recipe_created ) {
            return true;
        }

        $recipe_metas = array( 
            'recipeSubtitle'    => "The recipe subtitle text goes here. A brief note on the recipe will look great in this section.",
            'recipeDescription' => "You may have made a cinnamon roll french toast casserole or french toast bake, but those can take a long time to make. With the instant pot craze going on, we thought we would try to shorten the preparation time needed for traditional french.",
            'recipeKeywords'    => "home made, vegan, gluten free",
            'difficultyLevel'   => "beginner",
            'prepTime'          => "15",
            'prepTimeUnit'      => "min",
            'cookTime'          => "15",
            'cookTimeUnit'      => "min",
            'restTime'          => "15",
            'restTimeUnit'      => "min",
            'totalDuration'     => "",
            'totalDurationUnit' => "min",
            'bestSeason'        => "summer",
            'recipeCalories'    => "240 kcal",
            'noOfServings'      => "8",
            'ingredientTitle'   => "Ingredients",
            'recipeIngredients' => [
                '0' => [
                    'sectionTitle' => "",
                    'ingredients'  => [
                        '0' => [
                            'quantity'   => "4",
                            'unit'       => "cup",
                            'ingredient' => "French bread",
                            'notes'      => "cut into bite-size pieces and dried overnight",
                        ],
                        '1' => [
                            'quantity'   => "3",
                            'unit'       => "",
                            'ingredient' => "eggs",
                            'notes'      => "",
                        ],
                        '2' =>  [
                            'quantity'   => "3/4",
                            'unit'       => "cup",
                            'ingredient' => "milk",
                            'notes'      => "",
                        ],
                        '3' => [
                            'quantity'   => "1",
                            'unit'       => "tsp",
                            'ingredient' => "vanilla",
                            'notes'      => "",
                        ],
                        '4' => [
                            'quantity'   => "1/4",
                            'unit'       => "cup",
                            'ingredient' => "sugar",
                            'notes'      => "",
                        ],
                        '5' =>  [
                            'quantity'   => "1/4",
                            'unit'       => "cup",
                            'ingredient' => "brown sugar",
                            'notes'      => "",
                        ],
                        '6' => [
                            'quantity'   => "2",
                            'unit'       => "tsp",
                            'ingredient' => "cinnamon",
                            'notes'      => "",
                        ],
                    ],   
                ]
            ],
            'instructionsTitle'  => "Instructions",
            'recipeInstructions' => [
                '0' => [
                    'sectionTitle' => "",
                    'instruction'  => [
                        '0' => [
                            'instructionTitle' => "",
                            'instruction'      => "Cut the loaf into bite-size pieces and let it dry overnight on a cookie sheet or pan.",
                            'image'            => "",
                            'image_preview'    => "",
                            'videoURL'         => "",
                            'instructionNotes' => "You can use parchment paper in the bottom too for easy release. (you can use the special pan made for IP too if you have it).",
                        ],
                        '1' => [
                            'instructionTitle' => "",
                            'instruction'      => "Spray the bottom and sides of your dish with nonstick baking spray.",
                            'image'            => "",
                            'image_preview'    => "",
                            'videoURL'         => "",
                            'instructionNotes' => "",
                        ],
                        '2' => [
                            'instructionTitle' => "",
                            'instruction'      => "Whisk the eggs and then add the milk, vanilla, sugar, brown sugar and cinnamon.",
                            'image'            => "",
                            'image_preview'    => "",
                            'videoURL'         => "",
                            'instructionNotes' => "",
                        ],
                        '3' => [
                            'instructionTitle' => "",
                            'instruction'      => "Add the dried bread pieces. Let them soak for about 10 minutes. Stirring occasionally to make sure every piece of bread gets into the milk mixture.",
                            'image'            => "",
                            'image_preview'    => "",
                            'videoURL'         => "",
                            'instructionNotes' => "",
                        ],
                        '4' => [
                            'instructionTitle' => "",
                            'instruction'      => "Dump the mixture into your prepared dish. Cover the dish really well with foil.",
                            'image'            => "",
                            'image_preview'    => "",
                            'videoURL'         => "",
                            'instructionNotes' => "I put a piece over the top and then also placed the dish on a piece of foil and brought the edges up around it. (This helps seal out the moisture while it is steaming).",
                        ],
                        '5' => [
                            'instructionTitle' => "",
                            'instruction'      => "Place your trivet that came with the Instant Pot in the bottom of the pot and pour 1 cup of water into the pot. Place your baking dish on top of the trivet. Place the lid on the instant pot and make sure the vent is turned to “seal”.",
                            'image'            => "",
                            'image_preview'    => "",
                            'videoURL'         => "",
                            'instructionNotes' => "",
                        ],
                        '6' => [
                            'instructionTitle' => "",
                            'instruction'      => "Set manual and pressure to high for 40 minutes.",
                            'image'            => "",
                            'image_preview'    => "",
                            'videoURL'         => "",
                            'instructionNotes' => "",
                        ],
                        '7' => [
                            'instructionTitle' => "",
                            'instruction'      => "When the timer goes off cover the vent with a towel and quick release the steam.",
                            'image'            => "",
                            'image_preview'    => "",
                            'videoURL'         => "",
                            'instructionNotes' => "",
                        ],
                        '8' => [
                            'instructionTitle' => "",
                            'instruction'      => "Remove the dish carefully as it will be hot and remove the foil from the dish draining.",
                            'image'            => "",
                            'image_preview'    => "",
                            'videoURL'         => "",
                            'instructionNotes' => "Top with powdered sugar, cinnamon, homemade candied pecans, and whipped cream to make it extra yummy!",
                        ],
                    ]
                ]
            ],
            'imageGalleryImages' => [],
            'videoGalleryVids'   => [],
            'servingSize'       => "5",
            'servings'          => "8",
            'calories'          => "730",
            'caloriesFromFat'   => "250",
            'totalFat'          => "27",
            'saturatedFat'      => "9",
            'transFat'          => "",
            'cholesterol'       => "195",
            'sodium'            => "730",
            'potassium'         => "",
            'totalCarbohydrate' => "19",
            'dietaryFiber'      => "5",
            'sugars'            => "10",
            'protein'           => "97",
            'vitaminA'          => "",
            'vitaminC'          => "10",
            'calcium'           => "",
            'iron'              => "12",
            'vitaminD'          => "",
            'vitaminE'          => "8",
            'vitaminK'          => "5",
            'thiamin'           => "",
            'riboflavin'        => "",
            'niacin'            => "",
            'vitaminB6'         => "",
            'folate'            => "",
            'vitaminB12'        => "",
            'biotin'            => "",
            'pantothenicAcid'   => "",
            'phosphorus'        => "",
            'iodine'            => "",
            'magnesium'         => "",
            'zinc'              => "",
            'selenium'          => "",
            'copper'            => "",
            'manganese'         => "",
            'chromium'          => "",
            'molybdenum'        => "",
            'chloride'          => "",
            'recipeNotes'       => "<ul><li>Cook this recipe at low heat.</li><li>Use fresh ingredients for better taste</li>
            <li>After cooking, wait for at least 15 minutes for better taste</li></ul>"
        );

        $demo_recipe = array(
            'post_title'   => "The Best Instant Pot French Toast recipe (vegan &amp; Gluten Free!)",
            'post_content' => '<p>Love French Toast Casserole, but don’t time to let it sit overnight? Not to worry, with this <a href="#">Instant PotFrench</a> Toast will be ready in no time!</p>
            <p><b><em>Note –</em></b> this may look like different pictures than what got you here. We were finding that readers<br>were having issues with the recipe so we totally reworked it! Please give us your feedback in the comments. We welcome constructive comments that will help us serve you better!</p>
            <h3>Instant Pot French Toast Recipe</h3>
            <p>You may have made a cinnamon roll french toast casserole or french toast bake, but those can take a long time to make. With the instant pot craze going on, we thought we would try to shorten the preparation time needed for traditional french toast bakes! We love our instant pot and use it for dinners all the time. We have even used it for dessert with our Instant Pot Monkey Bread. The ease and quickness of it are perfect for our busy lifestyle.</p>
            <p>I honestly had no idea that you could even make french toast in the instant pot! But it is really easy!Feel free to add in some fun additions like pecans, walnuts or chocolate chips! They are all great.</p>
            <p>This will make the perfect dish for your Christmas Morning Brunch! Looking for some other great Christmas Morning Recipes? We have tons!!</p>',
            'post_status'  => 'draft',
            'post_type'    => DELICIOUS_RECIPE_POST_TYPE,
            'post_author'  => get_current_user_id(),
            'post_excerpt' => "Love French Toast Casserole, but don’t time to let it sit overnight? Not to worry, with this Instant Pot French Toast will be ready in no time!",
        );

        $recipe_demo_id = wp_insert_post( $demo_recipe );
        update_post_meta( $recipe_demo_id, 'delicious_recipes_metadata', $recipe_metas );

        // Insert meta value for search.
        update_post_meta( $recipe_demo_id, '_dr_difficulty_level', $recipe_metas['difficultyLevel'] );
        update_post_meta( $recipe_demo_id, '_dr_best_season', $recipe_metas['bestSeason'] );

        // update ingredients
		$ingredients = delicious_recipes_get_single_ingredients( $recipe_demo_id );
		if( ! empty( $ingredients ) ) {
            $ingredients = array_map( 'sanitize_text_field', $ingredients );
            update_post_meta( $recipe_demo_id, '_dr_recipe_ingredients', $ingredients );

            $ingredient_count = count( $ingredients );
            update_post_meta( $recipe_demo_id, '_dr_ingredient_count', absint( $ingredient_count ) );
		}

        //Set Featured Image for Demo Recipe
        $url = plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/images/dummy-recipe-img.jpg';
        media_sideload_image( $url, $recipe_demo_id );

        $attachments = get_posts( array(
            'numberposts'    => '1',
            'post_parent'    => $recipe_demo_id,
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => 'ASC')
        );

        if( sizeof( $attachments ) > 0 ) {
            set_post_thumbnail( $recipe_demo_id, $attachments[0]->ID );
        }

        // Update option value.
        $demo_import_settings['delicious_demo_imports']['created_demo_recipe'] = true;

        update_option( 'delicious_recipes_demo_imports', $demo_import_settings );
    }

    /**
     * Insert demo recipe page-templates data.
     *
     * @return void
     */
    public static function insert_initial_page_templates() {
        $template_pages = array(
			'recipe-courses'	=> array(
                'title' 	=> __( 'Recipe Courses', 'delicious-recipes' ),
                'template' 	=> 'templates/pages/recipe-courses.php'
            ),
            'recipe-cooking-methods'	=> array(
                'title' 	=> __( 'Recipe Cooking Methods', 'delicious-recipes' ),
                'template' 	=> 'templates/pages/recipe-cooking-methods.php'
            ),
            'recipe-cuisines'	=> array(
                'title' 	=> __( 'Recipe Cuisines', 'delicious-recipes' ),
                'template' 	=> 'templates/pages/recipe-cuisines.php'
            ),
            'recipe-keys'	=> array(
                'title' 	=> __( 'Recipe Keys', 'delicious-recipes' ),
                'template' 	=> 'templates/pages/recipe-keys.php'
            ),
            'recipe-tags'	=> array(
                'title' 	=> __( 'Recipe Tags', 'delicious-recipes' ),
                'template' 	=> 'templates/pages/recipe-tags.php'
            ),
            'recipe-badges'	=> array(
                'title' 	=> __( 'Recipe Badges', 'delicious-recipes' ),
                'template' 	=> 'templates/pages/recipe-badges.php'
            ),
		);
		foreach ( $template_pages as $key => $value) {			
            
            $existing_page = get_page_by_title( $value['title'] );

			if ( ! empty( $existing_page ) && 'page' === $existing_page->post_type && ($existing_page->post_status == 'publish') ) {
				$val = get_post_meta($existing_page->ID,'_wp_page_template',true);
				if ( $val == $value['template'] ) {
					continue;
				}
			} else {
				$new_page = array(
					'post_title'   => $value['title'],
					'post_content' => '',
					'post_status'  => 'publish',
					'post_type'    => 'page',
				);
				$page_ID = wp_insert_post( $new_page );
				update_post_meta( $page_ID, '_wp_page_template', sanitize_title( $value['template'] ) );
			}
		}
    }

    /**
     * check if its first activation.
     *
     * @return void
     */
    public static function check_first_activation() {

        $global_settings = get_option( 'delicious_recipe_settings', array() );

		if ( isset( $global_settings ) && ! empty( $global_settings ) ) {
            
			update_option( 'delicious_recipes_first_time_activation_flag', 'true' );
			return;
		}
    }

}
