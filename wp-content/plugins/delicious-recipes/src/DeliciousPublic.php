<?php
/**
 * Delecious recipes public functions handler class.
 *
 * @package Delicious_Recipes
 */
namespace WP_Delicious;

defined( 'ABSPATH' ) || exit;

/**
 * Handle the public functions for frontend of Delicious_Recipes plugin
 *
 * @since 1.0.0
 */
class DeliciousPublic {
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
		$this->includes();


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
		add_action( 'init', array( 'Delicious_Recipes_Shortcodes', 'init' ), 99999999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_scripts' ) );

		// Comments section.
		add_action( 'comment_form_logged_in_after', array( $this, 'dr_comment_form_rating_fields' ) );
		add_action( 'comment_form_after_fields', array( $this, 'dr_comment_form_rating_fields' ) );
		add_action( 'comment_post', array( $this, 'dr_save_comment_rating' ) );
		add_filter( 'comment_text', array( $this, 'dr_add_comment_review_after_text' ) );
		// register comment meta.
		add_action( 'init', array( $this, 'register_comment_metas' ) );

		// Posts per page value for recipe archives.
		add_filter( 'pre_get_posts', array( $this, 'recipe_archive_posts_per_page' ) );

		// Display Recipe posts on Home Page.
		add_action( 'pre_get_posts', array( $this, 'recipe_posts_on_homepage' ) );

		// Display Recipe posts on author archive.
		add_action( 'pre_get_posts', array( $this, 'recipe_posts_on_archive' ) );

		// Display Archive title.
		add_filter( 'get_the_archive_title', array( $this, 'recipe_archive_title' ), 99 );

		// Display Archive Description.
		add_filter( 'get_the_archive_description', array( $this, 'recipe_archive_description' ), 99 );
		// Add dynamic CSS.
		add_action( 'wp_head', array( $this, 'load_dynamic_css' ), 99 );

		// Add random links for surprise me nav menu
		add_filter( 'wp_nav_menu_objects', array( $this, 'surprise_me_nav_menu_objects' ) );

		// Handle the Dynamic Recipe Card block printing.
		add_action( 'init', array( $this, 'print_block_page' ) );

		// Adds the post type information in the search form
		add_filter( 'get_search_form', array( $this, 'get_search_form' ), 99 );

		// Add meta description for SEO.
		add_action( 'wp_head', array( $this, 'get_meta_description' ), 99 );

		// Prevent lazy image loading on print page.
		add_action( 'wp', array( $this, 'deactivate_lazyload_on_print' ) );

		// Adds the login/registration form for popup display
		add_action( 'wp_footer', array( $this, 'get_login_registration_form' ) );

		add_action( 'plugins_loaded', function() {
			// Allow 3rd party to remove hooks.
			do_action( 'wp_delicious_public_unhook', $this );
		}, 999);
	}

	function deactivate_lazyload_on_print() {
		if ( is_singular( DELICIOUS_RECIPE_POST_TYPE ) && isset( $_GET['print_recipe'] ) ) {
			// Prevent WP Rocket lazy image loading on print page.
			add_filter( 'do_rocket_lazyload', '__return_false' );

			// Prevent Avada lazy image loading on print page.
			if ( class_exists( 'Fusion_Images' ) && property_exists( 'Fusion_Images', 'lazy_load' ) ) {
				Fusion_Images::$lazy_load = false;
			}
		}
	}

	/**
	 * Modifies the random recipe link for Surprise Me nav menu
	 *
	 * @since 1.1.1
	 *
	 * @param array $items
	 * @return array modified menu items
	 */
	public function surprise_me_nav_menu_objects( $items ) {

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

		if ( ! empty( $items ) && is_array( $items ) ) {
			foreach ( $items as $item ) {
				if( "#dr_surprise_me"  === $item->url ) {
					if ( $options = get_post_meta( $item->ID, '_dr_menu_item', true ) ) {
						$title = $item->post_title;
						$icon  = "fas fa-random";
						if( ! $options['show_text_icon'] && $options['show_icon'] ) {
							$title = sprintf( '<i class="%1$s"></i>', $icon );
						}
						if( $options['show_text_icon'] || ( $options['show_icon'] && $options['show_text'] )
						|| ( $options['show_text_icon'] && $options['show_icon'] && $options['show_text'] )
						|| ( $options['show_text_icon'] && $options['show_icon'] ) ) {
							$title = sprintf( '%1$s<span style="margin-%2$s:0.3em;">%3$s</span>', '<i class="'.$icon.'"></i>', is_rtl() ? 'right' : 'left', esc_html( $title ) );
						}

						if( $options['show_posts'] ) {
							$args['post_type'] = array( DELICIOUS_RECIPE_POST_TYPE, 'post' );
						}

						$random_recipes = get_posts( $args );

						if( ! empty( $random_recipes ) ) {
							$item->title = $title;
							$item->url   = get_permalink( $random_recipes[0]->ID );
						}
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Includes
	 *
	 * @return void
	 */
	private function includes() {
		if ( $this->is_request( 'frontend' ) ) {
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-templates-loader.php';
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-shortcodes.php';
			require plugin_dir_path( __FILE__ ) . '/classes/class-delicious-recipes-template-hooks.php';
			require plugin_dir_path( __FILE__ ) . '/dashboard/class-delicious-recipes-user-account.php';
			require plugin_dir_path( __FILE__ ) . '/dashboard/delicious-recipes-user-dashboard-functions.php';
			require plugin_dir_path( __FILE__ ) . '/dashboard/class-delicious-recipes-form-handler.php';
			require plugin_dir_path( __FILE__ ) . '/dashboard/class-delicious-recipes-query.php';
		}
	}

	/**
	 * Load Frontend Scripts
	 *
	 * @return void
	 */
	public function load_frontend_scripts() {
		$global_settings   = get_option( 'delicious_recipe_settings', true );
		$asset_script_path = '/min/';
		$min_prefix        = '.min';

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$asset_script_path = '/';
			$min_prefix    = '';
		}

		// Fonts Enqueue.
		wp_enqueue_style( 'google-font-questrial', 'https://fonts.googleapis.com/css2?family=Questrial&display=swap' );
		wp_enqueue_style( 'google-font-noto-serif', 'https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;700&display=swap' );
		wp_enqueue_style( 'google-font-cookie', 'https://fonts.googleapis.com/css2?family=Cookie&display=swap' );

		wp_register_script( 'select2', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/select2/select2.min.js', array( 'jquery' ), '4.0.13', true );
		wp_enqueue_style( 'select2', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/select2/select2.min.css', array(), '4.0.13', 'all' );

		wp_enqueue_style( 'light-gallery', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/lightGallery/css/lightgallery-bundle.min.css', array(), '2.0.1', 'all' );

		wp_enqueue_style( 'delicious-recipes-single', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/public/css' . $asset_script_path . 'delicious-recipes-public' . $min_prefix . '.css', array(), DELICIOUS_RECIPES_VERSION, 'all' );

		wp_enqueue_style( 'jquery-rateyo', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/jquery-rateyo/jquery.rateyo.min.css', array(), '2.3.2', 'all' );
		wp_enqueue_script( 'jquery-rateyo',  plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/jquery-rateyo/jquery.rateyo.min.js', array( 'jquery' ), '2.3.2', true );

		wp_enqueue_style( 'owl-carousel', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/owl-carousel/owl.carousel.min.css', array(), '2.3.4', 'all' );
		wp_enqueue_script( 'owl-carousel',  plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/owl-carousel/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );

		wp_enqueue_script( 'light-gallery', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/lightGallery/lightgallery.min.js', array( 'jquery' ), '2.0.1', true );

		wp_enqueue_script( 'math-min', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/public/js/math.min.js', array( 'jquery' ), '5.1.2', true );

		wp_register_script( 'parsley', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/parsley/parsley.min.js', array( 'jquery' ), '2.9.2', true );

		wp_enqueue_script( 'delicious-recipes-single', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/public/js' . $asset_script_path . 'delicious-recipes-public' . $min_prefix . '.js', array( 'jquery', 'wp-util', 'select2', 'parsley' ), DELICIOUS_RECIPES_VERSION, true );
		
		$enable_autoload  = isset( $global_settings['autoloadRecipes']['0'] ) && 'yes' === $global_settings['autoloadRecipes']['0'] ? true : false;
		$delicious_recipes = array(
			'ajax_url'             => admin_url( 'admin-ajax.php' ),
			'search_placeholder'   => __( 'Select filters', 'delicious-recipes' ),
			'edit_profile_pic_msg' => __( 'Click here or Drop new image to update your profile picture', 'delicious-recipes' ),
			'enable_autoload'      => $enable_autoload,
		);

		wp_localize_script( 'delicious-recipes-single', 'delicious_recipes', $delicious_recipes );

		wp_enqueue_style('delicious-recipe-styles', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/build/publicCSS.css');

		// Enable/Disable FA Icons JS
		$disable_fa_icons_js = isset( $global_settings['disableFAIconsJS']['0'] ) && 'yes' === $global_settings['disableFAIconsJS']['0'] ? true : false;
		if( $disable_fa_icons_js ) {
			wp_enqueue_style( 'fontawesome', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/fontawesome/fontawesome.min.css' );
			wp_enqueue_style( 'all', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/fontawesome/all.min.css' );
			wp_enqueue_style( 'v4-shims', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/fontawesome/v4-shims.min.css' );
		} else {
			wp_enqueue_script( 'all', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/fontawesome/all.min.js', array( 'jquery' ), '5.14.0', true );
			wp_enqueue_script( 'v4-shims', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/fontawesome/v4-shims.min.js', array( 'jquery' ), '5.14.0', true );
		}

		wp_enqueue_script( 'pintrest', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/pintrest/pintrest.min.js', array( 'jquery' ), '5.14.0', true  );

		if ( is_recipe_dashboard() ) {
			wp_enqueue_style( 'dropzone', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/dropzone/dropzone.min.css', array(), '5.9.2', 'all' );
			wp_register_script( 'dropzone', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/dropzone/dropzone.min.js', array(), '5.9.2', true );
			wp_add_inline_script( 'dropzone', 'Dropzone.autoDiscover = false;' );

			wp_enqueue_style( 'delicious-recipes-user-dashboard', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/dashboard/css/main.css', array(), DELICIOUS_RECIPES_VERSION, 'all' );

			wp_enqueue_script( 'delicious-recipes-user-dashboard', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/dashboard/js/main.js', array( 'jquery', 'dropzone', 'parsley' ), DELICIOUS_RECIPES_VERSION, true );
		}
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Add rating field to comment form;
	 *
	 * @return void
	 */
	public function dr_comment_form_rating_fields() {
		if ( is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) {
			$global_toggles  = delicious_recipes_get_global_toggles_and_labels();

			if( $global_toggles['enable_ratings'] ) :
				?>
					<label for="rating"><?php echo esc_html( $global_toggles['ratings_lbl'] ); ?></label>
					<fieldset id="dr-comment-rating-field" class="dr-comment-rating">
						<div class="dr-comment-form-rating"></div>
						<input type="hidden" required="required" name="rating" value="0" >
					</fieldset>
				<?php
			endif;
		}
	}

	/**
	 * Save comment form.
	 *
	 * @return void
	 */
	public function dr_save_comment_rating( $comment_id ) {
		if ( isset( $_POST['comment_post_ID'] ) && ( get_post_type( $_POST['comment_post_ID'] ) == DELICIOUS_RECIPE_POST_TYPE ) ) {
			$global_toggles  = delicious_recipes_get_global_toggles_and_labels();

			if( $global_toggles['enable_ratings'] ) :
				if ( ( isset( $_POST['rating'] ) ) && ( '' !== $_POST['rating'] ) ) {
					$rating = floatval( $_POST['rating'] );
		
					add_comment_meta( $comment_id, 'rating', $rating );
				}
			endif;
			
		}
	}

	/**
	 * Comment Text.
	 *
	 * @param [type] $comment_text
	 * @return void
	 */
	public function dr_add_comment_review_after_text( $comment_text ) {
		if ( ! is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) {
			return $comment_text;
		}
		$global_toggles  = delicious_recipes_get_global_toggles_and_labels();
		$rating          = get_comment_meta( get_comment_ID(), 'rating', true );
		if ( $rating && $global_toggles['enable_ratings'] ) {
			$stars = '<p class="dr-star-rating">';
			$stars .= '<div data-rateyo-read-only="true" data-rateyo-rating="'. esc_attr( $rating ) .'" class="dr-comment-form-rating"></div>';
			$stars .= '</p>';
			$comment_text = $comment_text . $stars;
			return $comment_text;
		} else {
			return $comment_text;
		}
	}

	/**
	 * Register comment metas.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_comment_metas() {

		register_meta('comment', 'rating', [
			'type'         => 'number',
			'description'  => __('Rating', 'delicious-recipes'),
			'single'       => true,
			'show_in_rest' => true
		]);

	}

	/**
	 * Filter posts per page value for recipe.
	 *
	 * @param [type] $query
	 * @return void
	 */
	public function recipe_archive_posts_per_page( $query ) {
		if ( ! is_admin() && ( is_post_type_archive( DELICIOUS_RECIPE_POST_TYPE ) || is_recipe_taxonomy() ) ) {

			$options                = delicious_recipes_get_global_settings();
			$default_posts_per_page = ( isset ( $options['recipePerPage'] ) && ( !empty( $options['recipePerPage'] ) ) ) ? $options['recipePerPage'] : get_option( 'posts_per_page' );

			if ( $query->is_main_query() ) {
				$query->set( 'posts_per_page', $default_posts_per_page );
				return $query;
			}
		}
	}

	/**
	 * Display recipe posts as post on Homepage.
	 *
	 * @param [type] $query
	 * @return void
	 */
	public function recipe_posts_on_homepage( $query ) {
		if ( ! is_admin() && $query->is_main_query() ) {
			if ( $query->is_home() ) {

				// Get global toggles.
				$global_toggles = delicious_recipes_get_global_toggles_and_labels();

				if( ! $global_toggles['display_recipes_on_home_page'] ) {
					return;
				}

				$post_type = $query->get( 'post_type' );
				if( $post_type == '' || $post_type == 'post' ) {
					$post_type = array( 'post', DELICIOUS_RECIPE_POST_TYPE );
				}
				else if( is_array( $post_type ) ) {
					if( in_array( 'post', $post_type ) && ! in_array( DELICIOUS_RECIPE_POST_TYPE, $post_type ) ) {
						$post_type[] = DELICIOUS_RECIPE_POST_TYPE;
					}
				}

				$query->set( 'post_type', $post_type );

			}
			remove_action( 'pre_get_posts', 'recipe_posts_on_homepage' );
		}
	}

	/**
	 * Recipe posts only in archive.
	 *
	 * @param [type] $query
	 * @return void
	 */
	public function recipe_posts_on_archive( $query ) {
		$global_settings = delicious_recipes_get_global_settings();
		$recipe_author   = isset( $global_settings['recipeAuthor'] ) && ! empty( $global_settings['recipeAuthor'] ) ? $global_settings['recipeAuthor'] : false;

		if( $recipe_author && is_author() && empty( $query->query_vars['suppress_filters'] ) ) {
			$query->set( 'post_type', array(
					'post',
					DELICIOUS_RECIPE_POST_TYPE
				));
			return $query;
		}
	}

	/**
	 * Recipe archive title.
	 *
	 * @param [type] $title
	 * @return void
	 */
	public function recipe_archive_title( $title ) {
		$global_settings = delicious_recipes_get_global_settings();
		$archive_title = isset( $global_settings['archiveTitle'] ) && ! empty( $global_settings['archiveTitle'] ) ? $global_settings['archiveTitle'] : __('Recipe Index', 'delicious-recipes');

		if( is_post_type_archive( DELICIOUS_RECIPE_POST_TYPE ) ) {
			$title = sprintf( esc_html( $archive_title ), post_type_archive_title( '', false ) );
		}
		return $title;
	}

	/**
	 * Recipe archive description.
	 *
	 * @param [type] $title
	 * @return void
	 */
	public function recipe_archive_description( $description ) {
		$global_settings = delicious_recipes_get_global_settings();
		$archive_description = isset( $global_settings['archiveDescription'] ) && ! empty( $global_settings['archiveDescription'] ) ? $global_settings['archiveDescription'] : '';

		if( is_post_type_archive( DELICIOUS_RECIPE_POST_TYPE ) ) {
			$description = $archive_description;
		}
		return wpautop( wp_kses_post( $description ) );
	}

	/**
	 * Load Dynamic CSS values.
	 *
	 * @return void
	 */
	public function load_dynamic_css() {
		$recipe_templates = [
			'templates/pages/recipe-courses.php',
			'templates/pages/recipe-cooking-methods.php',
			'templates/pages/recipe-cuisines.php',
			'templates/pages/recipe-keys.php',
			'templates/pages/recipe-tags.php'
		];

		if ( is_recipe() || is_recipe_taxonomy() || is_archive( DELICIOUS_RECIPE_POST_TYPE )
		|| is_page_template( $recipe_templates ) || is_recipe_search() || is_recipe_block() || is_recipe_shortcode() ) {
			delicious_recipes_get_template( 'global/dynamic-css.php' );
		}
	}

	/**
	 * Handle the recipe printing.
	 *
	 * @return void
	 */
	public static function print_block_page() {
        preg_match( '/[\/\?]delrecipes_block_print[\/=](\d+)([\/\?\&].*)?$/', $_SERVER['REQUEST_URI'], $print_url );
        $recipe_id = isset( $print_url[1] ) ? $print_url[1] : false;

        // We have some params, let's check
        // extract params (e.g. /?servings=4&prep-time=15)
        if ( isset( $print_url[2] ) && is_string( $print_url[2] ) ) {
            preg_match_all( '/[\?|\&]([^=]+)\=([^&]+)/', $print_url[2], $params );

            if ( isset( $params[1] ) ) {
                foreach ( $params[1] as $key => $value ) {

                    if ( 'block-type' === $value ) {
                        $blockType = isset( $params[2][ $key ] ) ? $params[2][ $key ] : 'recipe-card';
                    }
                    elseif ( 'servings' === $value ) {
                        $servings = isset( $params[2][ $key ] ) ? $params[2][ $key ] : 0;
                    }
                    elseif ( 'block-id' === $value ) {
                        $blockId = isset( $params[2][ $key ] ) ? $params[2][ $key ] : '';
                    }

                }
            }
        }

        if ( $recipe_id && isset( $blockType ) ) {
            // Prevent WP Rocket lazy image loading on print page.
            add_filter( 'do_rocket_lazyload', '__return_false' );

            // Prevent Avada lazy image loading on print page.
            if ( class_exists( 'Fusion_Images' ) && property_exists( 'Fusion_Images', 'lazy_load' ) ) {
                Fusion_Images::$lazy_load = false;
            }

            $recipe_id = intval( $recipe_id );
            $recipe = get_post( $recipe_id );

            $has_delicious_recipes_block = false;
            $attributes = array();
            $content = $recipe->post_content;

            $whitelistBlocks = array(
                'recipe-card' => 'delicious-recipes/dynamic-recipe-card'
            );

            if ( 'publish' !== $recipe->post_status ) {
                wp_redirect( home_url() );
                exit();
            }

            if ( has_blocks( $recipe->post_content ) ) {
                $blocks = parse_blocks( $recipe->post_content );

                foreach ( $blocks as $key => $block ) {
                    $is_block_in_list = isset( $whitelistBlocks[ $blockType ] );
                    $needle_block_id = isset( $block['attrs']['id'] ) ? $block['attrs']['id'] : 'dr-dynamic-recipe-card';
                    $needle_block = $is_block_in_list && $block['blockName'] === $whitelistBlocks[ $blockType ];
                    $block_needed = $blockId == $needle_block_id && $needle_block;

                    if ( $block_needed ) {
                        $has_delicious_recipes_block = true;
                        $attributes = $block['attrs'];
                    }
                }
            }

            if ( $has_delicious_recipes_block ) {
                header( 'HTTP/1.1 200 OK' );
                require( plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/templates/global/block-print.php' );
                flush();
                exit;
            }
        }
    }

	/**
	 * Adds the post type information in the search form.
	 *
	 * @param string $form The search form HTML.
	 * @return string Modified search form.
	 */
	public function get_search_form( $form ) {
		if ( is_recipe_search() && $form ) {
			$form = str_replace( '</form>', '<input type="hidden" name="post_type" value="' . esc_attr( DELICIOUS_RECIPE_POST_TYPE ) . '" /></form>', $form );
		}

		return $form;
	}

	/**
	 * Add meta description.
	 *
	 * @return void
	 */
	public function get_meta_description() {
		global $post;
		if (  is_recipe() ) {
			$excerpt         = wp_strip_all_tags( $post->post_excerpt, true );
			$description     = $excerpt === "" ? wp_strip_all_tags( $post->post_content, true ) : $excerpt;
			$description     = mb_substr( $description, 0, 300, 'utf8' );
			$seo_description = class_exists( 'WPSEO_Meta' ) ? \WPSEO_Meta::get_value( 'metadesc' ) : "";
			$description     = $seo_description === "" ? $description : $seo_description;
			echo '<meta name="description" content="' . wp_kses_post( $description ) . '" />' . "\n";
		}
	}
	/*
	 * Add the login/registration form for logged out users
	 */
	function get_login_registration_form() {

		if ( isset( $_GET['print_recipe'] ) && 'true' == $_GET['print_recipe'] ) {
			return;
		}

		$global_toggles = delicious_recipes_get_global_toggles_and_labels();

		if( $global_toggles['enable_add_to_wishlist'] && ! is_user_logged_in() ) {

			ob_start();

			?>
				<div id="dr-user__registration-login-popup" class="dr-popup-user__registration-open" style="display:none;">
					<div class="dr-popup-container">
						<span class="dr-user__registration-login-popup-close">&times;</span>
						<?php
							$data = array(
								'popup'  => true
							);
							delicious_recipes_get_template( 'account/form-login.php', $data );
						?>
					</div>
				</div>
			<?php

			echo ob_get_clean();

		}
	}
}
