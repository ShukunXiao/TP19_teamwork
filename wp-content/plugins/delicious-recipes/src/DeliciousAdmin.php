<?php
/**
 * Admin area settings and hooks.
 *
 * @package Delicious_Recipes
 * @subpackage  Delicious_Recipes
 */

namespace WP_Delicious;

defined( 'ABSPATH' ) || exit;

/**
 * Global Settings.
 */
class DeliciousAdmin {

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

		// Add Image Sizes
		$this->add_image_sizes();

		// Allow 3rd party to remove hooks.
		do_action( 'wp_delicious_admin_unhook', $this );
	}

	/**
	 * Add image sizes.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function add_image_sizes() {

		add_image_size( 'recipe-feat-thumbnail', 124, 166, true ); // 124 pixels wide by 166 pixels tall, hard crop mode
		add_image_size( 'recipe-feat-tall', 290, 386, true ); // 290 pixels wide by 386 pixels tall, hard crop mode
		add_image_size( 'recipe-feat-gallery', 768, 1024, true ); // 768 pixels wide by 1024 pixels tall, hard crop mode
		add_image_size( 'recipe-feat-print', 595, 595, true ); // 595 pixels wide by 595 pixels tall, hard crop mode
		add_image_size( 'recipe-archive-list', 481, 640, true ); // 481 pixels wide by 640 pixels tall, hard crop mode
		add_image_size( 'recipe-archive-grid', 345, 460, true ); // 345 pixels wide by 460 pixels tall, hard crop mode
		add_image_size( 'recipe-author-image', 156, 207, true ); // 156 pixels wide by 207 pixels tall, hard crop mode

		// Add image size for recipe Schema.org markup
		add_image_size( 'delrecpe-structured-data-1_1', 	500, 500, true );
		add_image_size( 'delrecpe-structured-data-4_3', 	500, 375, true );
		add_image_size( 'delrecpe-structured-data-16_9', 	480, 270, true );
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

		// register post type.
		add_action( 'init', array( $this, 'register_post_types' ) );

		// register post type.
		add_action( 'init', array( $this, 'register_taxonomies' ) );

		// Insert default taxonomies.
		add_action( 'init', array( $this, 'insert_default_taxonomies' ), 20 );

		// Add meta box.
		add_action( 'add_meta_boxes', array( $this, 'add_new_recipe_metabox' ) );

		// Admin Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999999999 );

		// Block assets
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_ed_assets' ) );

		// Admin Script Translations
		add_action( 'admin_enqueue_scripts', array( $this, 'set_script_translations' ) , 99999999999 );

		$taxonomies_for_metas = array( 'recipe-key', 'recipe-tag', 'recipe-cooking-method', 'recipe-cuisine', 'recipe-course', 'recipe-badge' );

		foreach ( $taxonomies_for_metas as $taxonomy ) {
			// Add {$taxonomy}s additional fields.
			add_action( "{$taxonomy}_add_form_fields", array( $this, 'add_recipe_keys_meta' ) );
			add_action( "{$taxonomy}_edit_form_fields", array( $this, 'edit_recipe_keys_meta' ) );

			// Save the taxonomy meta fields.
			add_action( "edited_{$taxonomy}", array( $this, 'dr_save_taxonomy_custom_fields' ), 10, 2 );
			add_action( "create_{$taxonomy}", array( $this, 'dr_save_taxonomy_custom_fields' ), 10, 2 );
		}

		// Add ADMIN COLUMN - HEADERS
		add_filter( 'manage_edit-recipe_columns', array( $this, 'recipe_columns' ) );

		// ADMIN COLUMN - Featured CONTENT
		add_action( 'manage_recipe_posts_custom_column', array( $this, 'featured_recipes' ), 10, 2 );

		add_action( 'widgets_init', array( $this, 'register_recipe_sidebar' ) );

		add_filter( 'page_template', array( $this, 'recipe_listing_template' ) );
		add_filter( 'theme_page_templates', array( $this, 'recipe_admin_page_templates' ) );

		// Add recipe clone action
		add_filter( 'post_row_actions', array( $this, 'duplicate_recipe_action_row' ), 10, 2 );

		add_action( 'admin_notices', array( $this, 'permalink_structure_message' ) );

		// Add plugin action links
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta_links' ), 10, 2 );

		// Set default hidden columns.
		add_filter( 'default_hidden_columns', array( $this, 'hide_recipe_taxonomy_columns' ), 10, 2 );

		// Add Delicious Recipes in Appearance > Menus.
		add_action( 'admin_head-nav-menus.php', array( $this, 'add_nav_menu_meta_boxes' ) );
		add_action( 'wp_update_nav_menu_item', array( $this, 'wp_update_nav_menu_item' ), 10, 2 );

		add_filter( 'allowed_block_types_all', array( $this, 'allowed_block_types' ), 10, 2 );

		add_filter( 'manage_users_columns', array( $this, 'add_user_recipes_count_column' ) );
		add_action( 'manage_users_custom_column',  array( $this, 'show_user_recipes_count_column_content' ), 10, 3 );
		add_action( 'admin_head', array( $this, 'user_recipes_column_admin_css' ) );
	}

	/**
	 * Add custom nav meta box.
	 *
	 * Adapted from http://www.johnmorrisonline.com/how-to-add-a-fully-functional-custom-meta-box-to-wordpress-navigation-menus/.
	 */
	public function add_nav_menu_meta_boxes() {
		add_meta_box( 'dr_surprise_me_nav_link', __( 'Delicious Recipes', 'delicious-recipes' ), array( $this, 'nav_menu_links' ), 'nav-menus', 'side', 'low' );
	}

	/**
	 * Output menu links.
	 */
	public function nav_menu_links() {
		global $_nav_menu_placeholder, $nav_menu_selected_id;
		$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
		?>
		<div id="posttype-delicious-recipes" class="posttypediv">
			<div id="tabs-panel-delicious-recipes" class="tabs-panel tabs-panel-active">
				<ul id="delicious-recipes-checklist" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo (int) $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php esc_html_e( 'Surprise Me', 'delicious-recipes' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo (int) $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo (int) $_nav_menu_placeholder; ?>][menu-item-url]" value="#dr_surprise_me">
						<p id="menu-item-name-wrap" class="wp-clearfix">
							<label class="regular-text menu-item-textbox" for="custom-menu-item-name<?php echo (int) $_nav_menu_placeholder; ?>"><?php esc_html_e( 'Link Text', 'delicious-recipes' ) ?></label>
							<input id="custom-menu-item-name<?php echo (int) $_nav_menu_placeholder; ?>" name="menu-item[<?php echo (int) $_nav_menu_placeholder; ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox" />
						</p>
					</li>
				</ul>
			</div>
			<p class="button-controls">
				<span class="add-to-menu">
					<input type="submit" <?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'delicious-recipes' ); ?>" name="add-custom-menu-item" id="submit-posttype-delicious-recipes">
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Save our menu item options
	 *
	 * @since 1.1
	 *
	 * @param int $menu_id not used
	 * @param int $menu_item_db_id
	 */
	public function wp_update_nav_menu_item( $menu_id = 0, $menu_item_db_id = 0 ) {
		if ( empty( $_POST['menu-item-url'][ $menu_item_db_id ] ) || '#dr_surprise_me' !== $_POST['menu-item-url'][ $menu_item_db_id ] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		// Security check as 'wp_update_nav_menu_item' can be called from outside WP admin
		if ( current_user_can( 'edit_theme_options' ) ) {
			check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

			$options = array( 'show_icon' => 1, 'show_text' => 1, 'show_text_icon' => 1, 'show_posts' => 0 ); // Default values
			// Our jQuery form has not been displayed
			if ( empty( $_POST['menu-item-dr-detect'][ $menu_item_db_id ] ) ) {
				if ( ! get_post_meta( $menu_item_db_id, '_dr_menu_item', true ) ) { // Our options were never saved
					update_post_meta( $menu_item_db_id, '_dr_menu_item', $options );
				}
			}
			else {
				foreach ( array_keys( $options ) as $opt ) {
					$options[ $opt ] = empty( $_POST[ 'menu-item-' . $opt ][ $menu_item_db_id ] ) ? 0 : 1;
				}
				update_post_meta( $menu_item_db_id, '_dr_menu_item', $options ); // Allow us to easily identify our nav menu item
			}
		}
	}

	/**
	 * Register Recipe Sidebar.
	 *
	 * @return void
	 */
	public function register_recipe_sidebar() {
		$args = array(
			'name'          => __( 'Delicious Recipes Sidebar', 'delicious-recipes' ),
			'id'            => 'delicious-recipe-sidebar',
			'description'   => __( 'This is the widget area for single recipe page.', 'delicious-recipes' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title" itemprop="name">',
			'after_title'   => '</h2>',
		);
		// Register.
		register_sidebar( $args );
	}

	/**
	 * Default hidden columns recipe post type.
	 *
	 * @param [type] $hidden
	 * @param [type] $screen
	 * @return void
	 */
	public function hide_recipe_taxonomy_columns( $hidden, $screen ) {
		$screens_to_hide = array('author', 'taxonomy-recipe-tag', 'comments');
		if( isset( $screen->id ) && 'edit-recipe' === $screen->id ){
			foreach( $screens_to_hide as $screen ) {
				$hidden[] = $screen;
			}
		}
		return $hidden;
	}

	/**
	 * Add links to plugin's description in plugins table
	 *
	 * @param array  $links  Initial list of links.
	 * @param string $file   Basename of current plugin.
	 *
	 * @return array
	 */
	function plugin_meta_links( $links, $file ) {
		if ( $file !== plugin_basename( DELICIOUS_RECIPES_PLUGIN_FILE ) ) {
			return $links;
		}

		$support_link = '<a target="_blank" href="https://wpdelicious.com/support-ticket/" title="' . __( 'Get help', 'delicious-recipes' ) . '">' . __( 'Support', 'delicious-recipes' ) . '</a>';
		$docs_link    = '<a target="_blank" href="https://wpdelicious.com/docs" title="' . __( 'Docs', 'delicious-recipes' ) . '">' . __( 'Docs', 'delicious-recipes' ) . '</a>';
		$rate_link    = '<a target="_blank" href="https://wordpress.org/support/plugin/delicious-recipes/reviews/#new-post" title="' . __( 'Rate the plugin', 'delicious-recipes' ) . '">' . __( 'Rate the plugin ★★★★★', 'delicious-recipes' ) . '</a>';

		$links[] = $docs_link;
		$links[] = $support_link;
		$links[] = $rate_link;

		return $links;
	} // plugin_meta_links

	/**
	 * Add recipe post type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_post_types() {

		$permalink = delicious_recipes_get_permalink_structure();

		// Post Type labels.
		$labels = array(
			'name'               => _x( 'Recipes', 'post type general name', 'delicious-recipes' ),
			'singular_name'      => _x( 'Recipe', 'post type singular name', 'delicious-recipes' ),
			'menu_name'          => _x( 'Recipes', 'admin menu', 'delicious-recipes' ),
			'name_admin_bar'     => _x( 'Recipe', 'add new on admin bar', 'delicious-recipes' ),
			'add_new'            => _x( 'Add New', 'Recipe', 'delicious-recipes' ),
			'add_new_item'       => __( 'Add New Recipe', 'delicious-recipes' ),
			'new_item'           => __( 'New Recipe', 'delicious-recipes' ),
			'edit_item'          => __( 'Edit Recipe', 'delicious-recipes' ),
			'view_item'          => __( 'View Recipe', 'delicious-recipes' ),
			'all_items'          => __( 'All Recipes', 'delicious-recipes' ),
			'search_items'       => __( 'Search Recipes', 'delicious-recipes' ),
			'parent_item_colon'  => __( 'Parent Recipes:', 'delicious-recipes' ),
			'not_found'          => __( 'No Recipes found.', 'delicious-recipes' ),
			'not_found_in_trash' => __( 'No Recipes found in Trash.', 'delicious-recipes' ),
		);

		$DR_ADMIN_ICON = base64_encode( '<svg id="Group_5555" data-name="Group 5555" xmlns="http://www.w3.org/2000/svg" width="22" height="23.436" viewBox="0 0 22 23.436"><g id="Group_1484" data-name="Group 1484" transform="translate(0)"><path id="Path_23588" data-name="Path 23588" d="M74.82,374.892c.106-.022.184-.033.258-.055a5.21,5.21,0,0,0,2.152-1.12,1.936,1.936,0,0,0,.582-.883,1.432,1.432,0,0,0-.178-1.2,2.887,2.887,0,0,0-.7-.735.448.448,0,1,1,.559-.7,3.368,3.368,0,0,1,1.022,1.186,2.247,2.247,0,0,1,.21,1.408,2.858,2.858,0,0,1-1.113,1.759,5.081,5.081,0,0,1-1.265.765,8.207,8.207,0,0,1-1.173.4c-.49.12-.991.2-1.491.275a17.071,17.071,0,0,1-3.17.124c-.564-.02-1.126-.089-1.687-.152-.436-.049-.873-.107-1.3-.189s-.84-.179-1.251-.3c-.47-.138-.935-.294-1.392-.47a4.626,4.626,0,0,1-2.553-2.229,3.309,3.309,0,0,1-.347-2.042,3.813,3.813,0,0,1,.879-1.875,6.213,6.213,0,0,1,1.9-1.525,14.333,14.333,0,0,1,1.49-.678,9.616,9.616,0,0,1,1.255-.381c.579-.139,1.164-.259,1.751-.363a16.659,16.659,0,0,1,3.32-.227.27.27,0,0,0,.236-.094c.483-.555.979-1.1,1.449-1.663a2.7,2.7,0,0,0,.3-.592c.221-.473.438-.948.656-1.422q.369-.8.739-1.605c.241-.52.485-1.039.727-1.56.212-.456.421-.915.636-1.37a2.476,2.476,0,0,1,2.59-1.351,2.5,2.5,0,0,1,1.649.962,2.465,2.465,0,0,1,.51,1.828,2.81,2.81,0,0,1-.528,1.279c-.635.928-1.257,1.865-1.886,2.8-.613.907-1.23,1.812-1.841,2.72a2.02,2.02,0,0,0-.293.687c-.077.338-.2.665-.3,1s-.214.671-.321,1.006c-.1.317-.2.633-.3.95-.131.418-.258.837-.389,1.255-.122.39-.247.779-.371,1.169l-.4,1.279-.293.924Zm-2.913-8.282-.02-.04c-.046,0-.093,0-.14,0-.3.018-.6.028-.9.058-.571.058-1.142.116-1.709.2a10.56,10.56,0,0,0-1.09.234c-.456.117-.913.235-1.358.386a8.487,8.487,0,0,0-1.749.818,5.424,5.424,0,0,0-1.454,1.251,2.932,2.932,0,0,0-.652,1.549,2.23,2.23,0,0,0,.2,1.11,3.664,3.664,0,0,0,2.1,1.971c.357.143.72.27,1.085.392.2.066.408.106.638.165-.021-.164-.045-.29-.051-.417a4.36,4.36,0,0,1,.235-1.65,4.022,4.022,0,0,1,.768-1.354c.438-.505.88-1.007,1.321-1.51q1.2-1.364,2.4-2.726C71.658,366.9,71.782,366.756,71.907,366.61Zm3.355-2.669c.053.035.1.069.148.1.536.3,1.075.6,1.608.911.1.057.146.045.205-.044q.917-1.369,1.84-2.736.887-1.316,1.778-2.629a1.777,1.777,0,0,0,.35-.905,1.578,1.578,0,0,0-.322-1.071,1.636,1.636,0,0,0-2.367-.282,2.934,2.934,0,0,0-.65,1.064c-.219.453-.42.914-.632,1.37-.249.538-.5,1.074-.752,1.611s-.486,1.051-.729,1.578Zm.65,1.408a1.532,1.532,0,0,1-.165-.1c-.11-.088-.164-.034-.23.065q-.782,1.171-1.574,2.336-.937,1.384-1.878,2.766c-.419.616-.843,1.228-1.255,1.848-.283.426-.576.848-.815,1.3s-.431.961-.644,1.443c-.049.11.01.137.1.143.536.032,1.071.067,1.607.091a.231.231,0,0,0,.174-.084c.2-.3.4-.6.564-.92.228-.437.427-.89.636-1.337q.374-.8.745-1.6c.249-.539.494-1.08.742-1.619s.486-1.051.729-1.578q.283-.613.565-1.227C75.446,366.37,75.674,365.87,75.912,365.349Zm-1.095-.594-.029-.034a1.272,1.272,0,0,0-.126.111c-.472.539-.941,1.081-1.414,1.619q-1.321,1.5-2.645,3c-.6.68-1.191,1.364-1.795,2.039a4.036,4.036,0,0,0-1,1.682,3.469,3.469,0,0,0-.023,1.632c.01.044.051.108.087.116a3.645,3.645,0,0,0,.467.068.132.132,0,0,0,.1-.069c.09-.235.159-.478.259-.708a9.33,9.33,0,0,1,.552-1.167c.464-.755.963-1.488,1.456-2.224.621-.925,1.251-1.845,1.877-2.767q.719-1.058,1.436-2.117Q74.421,365.346,74.817,364.755Zm1.929,1.066-.05-.018c-1.483,3.127-2.857,6.3-4.384,9.417a.333.333,0,0,0,.085.012c.448-.042.9-.084,1.342-.134a.18.18,0,0,0,.113-.106c.074-.206.137-.416.2-.624.1-.308.193-.616.29-.924.132-.417.267-.834.4-1.252.125-.4.246-.8.372-1.2.134-.426.273-.851.408-1.277.125-.394.246-.79.371-1.184.134-.422.27-.842.4-1.264q.151-.475.3-.951C76.649,366.152,76.7,365.986,76.746,365.821Z" transform="translate(-61.943 -356)" fill="#17bfed"/><path id="Path_23589" data-name="Path 23589" d="M115.488,394.587a4.993,4.993,0,0,1-.165,1.49,6.24,6.24,0,0,1-1.8,2.934,8.881,8.881,0,0,1-2.3,1.581c-.5.24-1.016.444-1.529.655a.456.456,0,0,1-.567-.173.43.43,0,0,1,.059-.539,1.718,1.718,0,0,1,.413-.21c.535-.245,1.084-.463,1.6-.737a7.555,7.555,0,0,0,1.717-1.255,5.636,5.636,0,0,0,1.406-2.047,4.031,4.031,0,0,0,.251-2.013,4.643,4.643,0,0,0-.77-1.945,5.488,5.488,0,0,0-1.911-1.776c-.519-.291-1.052-.557-1.584-.824-.212-.106-.438-.185-.657-.276a.444.444,0,0,1-.248-.587.467.467,0,0,1,.557-.243c.492.225,1,.41,1.482.661.524.274,1.029.588,1.524.913a6.015,6.015,0,0,1,1.475,1.477,5.31,5.31,0,0,1,.84,1.688,12.214,12.214,0,0,1,.237,1.218Z" transform="translate(-93.523 -377.849)" fill="#17bfed"/></g></svg>' );

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'delicious-recipes' ),
			'public'             => true,
			'menu_icon'          => 'data:image/svg+xml;base64,' . $DR_ADMIN_ICON,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'rewrite'            => array(
				'slug'       => $permalink['recipeBase'],
				'with_front' => true,
			),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 30,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
		);

		register_post_type( DELICIOUS_RECIPE_POST_TYPE, $args );

		if ( 'yes' === get_option( 'delicious_recipes_queue_flush_rewrite_rules' ) ) {
			update_option( 'delicious_recipes_queue_flush_rewrite_rules', 'no' );
			flush_rewrite_rules();
		}

	}

	/**
	 * Register taxonomies for the recipe.
	 *
	 * @return void
	 */
	public function register_taxonomies() {

		$permalink = delicious_recipes_get_permalink_structure();

		// Add recipe category.
		$labels = array(
			'name'              => _x( 'Courses', 'taxonomy general name', 'delicious-recipes' ),
			'singular_name'     => _x( 'Course', 'taxonomy singular name', 'delicious-recipes' ),
			'search_items'      => __( 'Search Courses', 'delicious-recipes' ),
			'all_items'         => __( 'All Courses', 'delicious-recipes' ),
			'parent_item'       => __( 'Parent Course', 'delicious-recipes' ),
			'parent_item_colon' => __( 'Parent Course', 'delicious-recipes' ),
			'edit_item'         => __( 'Edit Course', 'delicious-recipes' ),
			'update_item'       => __( 'Update Course', 'delicious-recipes' ),
			'add_new_item'      => __( 'Add New Course', 'delicious-recipes' ),
			'new_item_name'     => __( 'New Course Name', 'delicious-recipes' ),
			'menu_name'         => __( 'Courses', 'delicious-recipes' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => $permalink['courseBase'],
				'hierarchical' => true,
			),
		);

		register_taxonomy( 'recipe-course', array( DELICIOUS_RECIPE_POST_TYPE ), $args );

		// Add recipe Cuisine.
		$labels = array(
			'name'              => _x( 'Cuisines', 'taxonomy general name', 'delicious-recipes' ),
			'singular_name'     => _x( 'Cuisine', 'taxonomy singular name', 'delicious-recipes' ),
			'search_items'      => __( 'Search Cuisines', 'delicious-recipes' ),
			'all_items'         => __( 'All Cuisines', 'delicious-recipes' ),
			'parent_item'       => __( 'Parent Cuisine', 'delicious-recipes' ),
			'parent_item_colon' => __( 'Parent Cuisine', 'delicious-recipes' ),
			'edit_item'         => __( 'Edit Cuisine', 'delicious-recipes' ),
			'update_item'       => __( 'Update Cuisine', 'delicious-recipes' ),
			'add_new_item'      => __( 'Add New Cuisine', 'delicious-recipes' ),
			'new_item_name'     => __( 'New Cuisine Name', 'delicious-recipes' ),
			'menu_name'         => __( 'Cuisines', 'delicious-recipes' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => $permalink['cuisineBase'],
				'hierarchical' => true,
			),
		);

		register_taxonomy( 'recipe-cuisine', array( DELICIOUS_RECIPE_POST_TYPE ), $args );

		// Add recipe Cooking Methods.
		$labels = array(
			'name'              => _x( 'Cooking Methods', 'taxonomy general name', 'delicious-recipes' ),
			'singular_name'     => _x( 'Cooking Method', 'taxonomy singular name', 'delicious-recipes' ),
			'search_items'      => __( 'Search Cooking Methods', 'delicious-recipes' ),
			'all_items'         => __( 'All Cooking Methods', 'delicious-recipes' ),
			'parent_item'       => __( 'Parent Cooking Method', 'delicious-recipes' ),
			'parent_item_colon' => __( 'Parent Cooking Method', 'delicious-recipes' ),
			'edit_item'         => __( 'Edit Cooking Method', 'delicious-recipes' ),
			'update_item'       => __( 'Update Cooking Method', 'delicious-recipes' ),
			'add_new_item'      => __( 'Add New Cooking Method', 'delicious-recipes' ),
			'new_item_name'     => __( 'New Cooking Method Name', 'delicious-recipes' ),
			'menu_name'         => __( 'Cooking Methods', 'delicious-recipes' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => $permalink['cookingMethodBase'],
				'hierarchical' => true,
			),
		);

		register_taxonomy( 'recipe-cooking-method', array( DELICIOUS_RECIPE_POST_TYPE ), $args );

		// Add recipe Tags.
		$labels = array(
			'name'              => _x( 'Tags', 'taxonomy general name', 'delicious-recipes' ),
			'singular_name'     => _x( 'Tag', 'taxonomy singular name', 'delicious-recipes' ),
			'search_items'      => __( 'Search Tags', 'delicious-recipes' ),
			'all_items'         => __( 'All Tags', 'delicious-recipes' ),
			'parent_item'       => __( 'Parent Tag', 'delicious-recipes' ),
			'parent_item_colon' => __( 'Parent Tag', 'delicious-recipes' ),
			'edit_item'         => __( 'Edit Tag', 'delicious-recipes' ),
			'update_item'       => __( 'Update Tag', 'delicious-recipes' ),
			'add_new_item'      => __( 'Add New Tag', 'delicious-recipes' ),
			'new_item_name'     => __( 'New Tag Name', 'delicious-recipes' ),
			'menu_name'         => __( 'Tags', 'delicious-recipes' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => $permalink['tagBase'],
				'hierarchical' => false,
			),
		);

		register_taxonomy( 'recipe-tag', array( DELICIOUS_RECIPE_POST_TYPE ), $args );

		// Add recipe Keys.
		$labels = array(
			'name'              => _x( 'Recipe Keys', 'taxonomy general name', 'delicious-recipes' ),
			'singular_name'     => _x( 'Recipe Key', 'taxonomy singular name', 'delicious-recipes' ),
			'search_items'      => __( 'Search Recipe Keys', 'delicious-recipes' ),
			'all_items'         => __( 'All Recipe Keys', 'delicious-recipes' ),
			'parent_item'       => __( 'Parent Recipe Key', 'delicious-recipes' ),
			'parent_item_colon' => __( 'Parent Recipe Key', 'delicious-recipes' ),
			'edit_item'         => __( 'Edit Recipe Key', 'delicious-recipes' ),
			'update_item'       => __( 'Update Recipe Key', 'delicious-recipes' ),
			'add_new_item'      => __( 'Add New Recipe Key', 'delicious-recipes' ),
			'new_item_name'     => __( 'New Recipe Key Name', 'delicious-recipes' ),
			'menu_name'         => __( 'Recipe Keys', 'delicious-recipes' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => $permalink['keyBase'],
				'hierarchical' => false,
			),
		);

		register_taxonomy( 'recipe-key', array( DELICIOUS_RECIPE_POST_TYPE ), $args );

		// Add recipe Badge.
		$labels = array(
			'name'              => _x( 'Recipe Badges', 'taxonomy general name', 'delicious-recipes' ),
			'singular_name'     => _x( 'Recipe Badge', 'taxonomy singular name', 'delicious-recipes' ),
			'search_items'      => __( 'Search Recipe Badges', 'delicious-recipes' ),
			'all_items'         => __( 'All Recipe Badges', 'delicious-recipes' ),
			'parent_item'       => __( 'Parent Recipe Badge', 'delicious-recipes' ),
			'parent_item_colon' => __( 'Parent Recipe Badge', 'delicious-recipes' ),
			'edit_item'         => __( 'Edit Recipe Badge', 'delicious-recipes' ),
			'update_item'       => __( 'Update Recipe Badge', 'delicious-recipes' ),
			'add_new_item'      => __( 'Add New Recipe Badge', 'delicious-recipes' ),
			'new_item_name'     => __( 'New Recipe Badge Name', 'delicious-recipes' ),
			'menu_name'         => __( 'Recipe Badges', 'delicious-recipes' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => $permalink['badgeBase'],
				'hierarchical' => false,
			),
		);

		register_taxonomy( 'recipe-badge', array( DELICIOUS_RECIPE_POST_TYPE ), $args );
	}

	/**
	 * Insert default plugin taxonomies.
	 *
	 * @return void
	 */
	public function insert_default_taxonomies() {
		$demo_import_settings = get_option( 'delicious_recipes_demo_imports', array() );

		$demo_tax_created = isset( $demo_import_settings['delicious_demo_imports']['created_demo_taxonomies'] ) && $demo_import_settings['delicious_demo_imports']['created_demo_taxonomies'] ? true : false;

		if ( $demo_tax_created ) {
			return true;
		}

		$initial_taxonomies_array = apply_filters(
			'delicious_recipes_default_taxonomies',
			array(
				'recipe-key' => array(
					'vegetarian-meals' => array(
						'name'     => __( 'Vegetarian Meals', 'delicious-recipes' ),
						'slug'     => 'vegetarian-meals',
						'svg_icon' => 'vegetarian-meals',
					),
					'gluten-free'      => array(
						'name'     => __( 'Gluten Free', 'delicious-recipes' ),
						'slug'     => 'gluten-free',
						'svg_icon' => 'gluten-free',
					),
					'paleo'            => array(
						'name'     => __( 'Paleo', 'delicious-recipes' ),
						'slug'     => 'paleo',
						'svg_icon' => 'paleo',
					),
					'freezer-meals'    => array(
						'name'     => __( 'Freezer Meals', 'delicious-recipes' ),
						'slug'     => 'freezer-meals',
						'svg_icon' => 'freezer-meals',
					),
					'low-carb'         => array(
						'name'     => __( 'Low Carb', 'delicious-recipes' ),
						'slug'     => 'low-carb',
						'svg_icon' => 'low-carb',
					),
					'slow-cooker'      => array(
						'name'     => __( 'Slow Cooker Recipes', 'delicious-recipes' ),
						'slug'     => 'slow-cooker',
						'svg_icon' => 'slow-cooker',
					),
					'dairy'            => array(
						'name'     => __( 'Dairy', 'delicious-recipes' ),
						'slug'     => 'dairy',
						'svg_icon' => 'dairy',
					),
					'kids'             => array(
						'name'     => __( 'Kids', 'delicious-recipes' ),
						'slug'     => 'kids',
						'svg_icon' => 'kids',
					),
					'mixing'           => array(
						'name'     => __( 'Mixing', 'delicious-recipes' ),
						'slug'     => 'mixing',
						'svg_icon' => 'mixing',
					),
					'non-vegetarian'   => array(
						'name'     => __( 'Non Vegetarian', 'delicious-recipes' ),
						'slug'     => 'non-vegetarian',
						'svg_icon' => 'non-vegetarian',
					),
					'cold'             => array(
						'name'     => __( 'Cold', 'delicious-recipes' ),
						'slug'     => 'cold',
						'svg_icon' => 'cold',
					),
					'spicy'            => array(
						'name'     => __( 'Spicy', 'delicious-recipes' ),
						'slug'     => 'spicy',
						'svg_icon' => 'spicy',
					),
					'raw'              => array(
						'name'     => __( 'Raw', 'delicious-recipes' ),
						'slug'     => 'raw',
						'svg_icon' => 'raw',
					),
					'dairy-free'       => array(
						'name'     => __( 'Dairy Free', 'delicious-recipes' ),
						'slug'     => 'dairy-free',
						'svg_icon' => 'dairy-free',
					),
					'nut-free'         => array(
						'name'     => __( 'Nut Free', 'delicious-recipes' ),
						'slug'     => 'nut-free',
						'svg_icon' => 'nut-free',
					),
					'pescetarian'      => array(
						'name'     => __( 'Pescetarian', 'delicious-recipes' ),
						'slug'     => 'pescetarian',
						'svg_icon' => 'pescetarian',
					),
					'quick-meals'      => array(
						'name'     => __( 'Quick Meals', 'delicious-recipes' ),
						'slug'     => 'quick-meals',
						'svg_icon' => 'quick-meals',
					),
					'whole-30'         => array(
						'name'     => __( 'Whole30', 'delicious-recipes' ),
						'slug'     => 'whole-30',
						'svg_icon' => 'whole-30',
					),
					'vegan'            => array(
						'name'     => __( 'Vegan', 'delicious-recipes' ),
						'slug'     => 'vegan',
						'svg_icon' => 'vegan',
					),
					'keto'             => array(
						'name'     => __( 'Keto', 'delicious-recipes' ),
						'slug'     => 'keto',
						'svg_icon' => 'keto',
					),
					'high-protein'     => array(
						'name'     => __( 'High Protein', 'delicious-recipes' ),
						'slug'     => 'high-protein',
						'svg_icon' => 'high-protein',
					),
					'organic'          => array(
						'name'     => __( 'Organic', 'delicious-recipes' ),
						'slug'     => 'organic',
						'svg_icon' => 'organic',
					),
					'corn-free'        => array(
						'name'     => __( 'Corn Free', 'delicious-recipes' ),
						'slug'     => 'corn-free',
						'svg_icon' => 'corn-free',
					),
					'soy-free'         => array(
						'name'     => __( 'Soy Free', 'delicious-recipes' ),
						'slug'     => 'soy-free',
						'svg_icon' => 'soy-free',
					),
					'sugar-free'       => array(
						'name'     => __( 'Sugar Free', 'delicious-recipes' ),
						'slug'     => 'sugar-free',
						'svg_icon' => 'sugar-free',
					),
					'egg-free'         => array(
						'name'     => __( 'Egg Free', 'delicious-recipes' ),
						'slug'     => 'egg-free',
						'svg_icon' => 'egg-free',
					),
				),
			)
		);

		foreach ( $initial_taxonomies_array as $tax => $terms ) {
			foreach ( $terms as $slug => $atts ) {

				if ( ! term_exists( $atts['name'], $tax ) ) {
					$inserted_term = wp_insert_term(
						$atts['name'],
						$tax,
						array(
							'slug' => $atts['slug'],
						)
					);
					if ( ! is_wp_error( $inserted_term ) ) {
						$dr_taxonomy_metas = array();

						$inserted_term_id                  = $inserted_term['term_id'];
						$dr_taxonomy_metas['taxonomy_svg'] = $atts['svg_icon'];

						update_term_meta( $inserted_term_id, 'dr_taxonomy_metas', $dr_taxonomy_metas );
					}
				}
			}
		}

		// Update option value.
		$demo_import_settings['delicious_demo_imports']['created_demo_taxonomies'] = true;

		update_option( 'delicious_recipes_demo_imports', $demo_import_settings );

	}

	/**
	 * Add new recipe metabox.
	 *
	 * @return void
	 */
	public function add_new_recipe_metabox() {
		add_meta_box(
			'delicious_recipes_metabox',
			__( 'Recipe Settings', 'delicious-recipes' ),
			array( $this, 'delicious_recipes_mb_callback' ),
			DELICIOUS_RECIPE_POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Delicious Recipes Metabox Callback.
	 *
	 * @return void
	 */
	public function delicious_recipes_mb_callback( $post ) {
		?>
			<div id="delicious-recipe-app" data-rest-nonce="<?php echo wp_create_nonce( 'wp_rest' ); ?>" data-post-id="<?php echo esc_attr( $post->ID ); ?>"></div>
		<?php
	}

	/**
	 * Add meta fields for recipe keys.
	 *
	 * @param [type] $taxonomy
	 * @return void
	 */
	public function add_recipe_keys_meta( $taxonomy ) {
		include plugin_dir_path( __FILE__ ) . '/admin/partials/dr-taxonomy-metas-add.php';
	}

	/**
	 * Add meta fields for recipe keys.
	 *
	 * @param [type] $taxonomy
	 * @return void
	 */
	public function edit_recipe_keys_meta( $taxonomy ) {
		include plugin_dir_path( __FILE__ ) . '/admin/partials/dr-taxonomy-metas-edit.php';
	}

	/**
	 * Save taxonmoy metas.
	 */
	public function dr_save_taxonomy_custom_fields( $term_id ) {
		if ( isset( $_POST['dr_taxonomy_metas'] ) ) {
			// Sanitization of data.
			$taxonnomy_metas['taxonomy_color'] = isset( $_POST['dr_taxonomy_metas']['taxonomy_color'] ) ? sanitize_hex_color( $_POST['dr_taxonomy_metas']['taxonomy_color'] ) : '';
			$taxonnomy_metas['taxonomy_image'] = isset( $_POST['dr_taxonomy_metas']['taxonomy_image'] ) ? absint( $_POST['dr_taxonomy_metas']['taxonomy_image'] ) : '';
			$taxonnomy_metas['taxonomy_svg']   = isset( $_POST['dr_taxonomy_metas']['taxonomy_svg'] ) ? sanitize_text_field( $_POST['dr_taxonomy_metas']['taxonomy_svg'] ) : '';

			$dr_taxonomy_metas = stripslashes_deep( $taxonnomy_metas );
			// Save the option array.
			update_term_meta( $term_id, 'dr_taxonomy_metas', $dr_taxonomy_metas );
		}
	}

	/**
	 * Enqueue Admin Scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		$post_types = array( DELICIOUS_RECIPE_POST_TYPE );
		$page_ids   = array( 'recipe_page_delicious_recipes_global_settings', 'widgets', 'customize' );

		if ( in_array( $screen->post_type, $post_types ) || in_array( $screen->id, $page_ids ) ) {

			// Add the color picker css file
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_media();
			wp_enqueue_editor();

			wp_register_script( 'mCustomScrollbar', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/mcustomscrollbar/jquery.mCustomScrollbar.min.js', array( 'jquery' ), '3.1.5', true );

			wp_register_script( 'select2', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/select2/select2.min.js', array( 'jquery' ), '4.0.13', true );

			wp_enqueue_script( 'datatable', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/datatables/datatables.min.js', array( 'jquery' ), '1.10.22', true );
			wp_enqueue_style( 'datatable', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/datatables/datatables.min.css', array(), '1.10.22', 'all' );

			wp_enqueue_script( 'delicious-recipe-admin-common', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/common.js', array( 'jquery', 'wp-color-picker', 'mCustomScrollbar', 'select2' ), DELICIOUS_RECIPES_VERSION, true );

			wp_enqueue_script( 'all', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/fontawesome/all.min.js', array( 'jquery' ), '5.14.0', true );

			wp_enqueue_script( 'v4-shims', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/fontawesome/v4-shims.min.js', array( 'jquery' ), '5.14.0', true );

			wp_enqueue_style( 'toastr', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/toastr/toastr.min.css', array(), '2.1.3', 'all' );

			wp_enqueue_script( 'toastr', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/toastr/toastr.min.js', array( 'jquery' ), '2.1.3', true );

			$max_upload_size = delicious_recipes_get_max_upload_size();
			$global_settings = delicious_recipes_get_global_settings();
			$default_templates = array(
				'newAccountContent'    => delicious_recipes_get_template_content( 'new_account', 'emails/customer-new-account.php', 'customer', true ),
				'resetPasswordContent' => delicious_recipes_get_template_content( 'reset_password', 'emails/customer-reset-password.php', 'customer', true )
			);

			$recipe_deps = include_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/build/recipe.asset.php';

			if ( 'recipe' === $screen->id ) {

				// Recipe edit screen assets.
				wp_register_script( 'delicious-recipe-edit', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . 'assets/admin/build/recipe.js', $recipe_deps['dependencies'], $recipe_deps['version'], true );

				// Add localization vars.
				wp_localize_script(
					'delicious-recipe-edit',
					'DeliciousRecipes',
					array(
						'siteURL'        => esc_url( home_url( '/' ) ),
						'pluginUrl'      => esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ),
						'maxUploadSize'  => esc_html( $max_upload_size ),
						'globalSettings' => $global_settings,
					)
				);

				wp_enqueue_script( 'delicious-recipe-edit' );
			}

			$global_deps = include_once plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/build/global.asset.php';

			if ( 'recipe_page_delicious_recipes_global_settings' === $screen->id ) {

				// Recipe global screen assets.
				wp_register_script( 'delicious-recipe-global-settings', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . 'assets/admin/build/global.js', $global_deps['dependencies'], $global_deps['version'], true );

				// Add localization vars.
				wp_localize_script(
					'delicious-recipe-global-settings',
					'DeliciousRecipes',
					array(
						'siteURL'          => esc_url( home_url( '/' ) ),
						'pluginUrl'        => esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ),
						'maxUploadSize'    => esc_html( $max_upload_size ),
						'defaultTemplates' => $default_templates
					)
				);
				wp_enqueue_script( 'delicious-recipe-global-settings' );
			}

			wp_enqueue_style( 'delicious-recipe-admin-common', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/common.css', array('wp-components'), DELICIOUS_RECIPES_VERSION, 'all' );

			wp_enqueue_style( 'delicious-recipe-admin', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/build/adminCSS.css' );

			wp_enqueue_style( 'mCustomScrollbar', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/mcustomscrollbar/jquery.mCustomScrollbar.min.css', array(), '3.1.5', 'all' );

			wp_enqueue_style( 'select2', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/lib/select2/select2.min.css', array(), '4.0.13', 'all' );

		}

		$screen = get_current_screen();
		if ( 'nav-menus' != $screen->base ) {
			return;
		}

		wp_enqueue_script( 'delicious_recipes_nav_menu', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . 'assets/admin/nav-menu.js', array( 'jquery' ), 'all' );

		$data = array(
			'strings' => get_surprise_me_options( 'menu', 'string' ), // The strings for the options
			'title'   => __( 'Surprise Me', 'delicious-recipes' ), // The title
			'val'     => array(),
		);

		// Get all surprise me menu items
		$items = get_posts(
			array(
				'numberposts' => -1,
				'nopaging'    => true,
				'post_type'   => 'nav_menu_item',
				'fields'      => 'ids',
				'meta_key'    => '_dr_menu_item',
			)
		);

		// The options values for the surprise me
		foreach ( $items as $item ) {
			$data['val'][ $item ] = get_post_meta( $item, '_dr_menu_item', true );
		}

		// Send all these data to javascript
		wp_localize_script( 'delicious_recipes_nav_menu', 'delicious_recipes_data', $data );
	}

	/**
	 * Set Script Translations
	 *
	 * @return void
	 */
	public function set_script_translations() {
		wp_set_script_translations( 'delicious-recipes-gb-block-js', 'delicious-recipes' ); // Blocks.
		wp_set_script_translations( 'delicious-recipe-global-settings', 'delicious-recipes' ); // Global Settings.
		wp_set_script_translations( 'delicious-recipe-edit', 'delicious-recipes' ); // Recipe Settings.
	}

	/**
	 * ED Block Assets
	 *
	 * @return void
	 */
	function enqueue_block_ed_assets() {
		// Here you can also check several conditions,
		// for example if you want to add this link only on CPT  you can
		$screen = get_current_screen();
		// and then
		if ( DELICIOUS_RECIPE_POST_TYPE === $screen->post_type ) {
			wp_register_script( 'gutenberg-header', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/admin/gutenberg-header.js', array(), '1.0.0', true );
		}

		wp_localize_script(
			'gutenberg-header',
			'GutenHead',
			array(
				'JUMPTXT'   => esc_html__( 'Jump to recipe settings', 'delicious-recipes' ),
			)
		);

		wp_enqueue_script( 'gutenberg-header' );

	}

	/**
	 * Customize Admin column.
	 *
	 * @param  Array $booking_columns List of columns.
	 * @return Array                  [description]
	 */
	public function recipe_columns( $recipe_columns ) {
		$recipe_columns['featured'] = __( 'Featured', 'delicious-recipes' );
		return $recipe_columns;
	}

	/**
	 * Add data to custom column.
	 *
	 * @param  String $column_name Custom column name.
	 * @param  int    $id          Post ID.
	 */
	public function featured_recipes( $column_name, $id ) {
		switch ( $column_name ) {
			case 'featured':
				$featured = get_post_meta( $id, 'wp_delicious_featured_recipe', true );
				$featured = ( isset( $featured ) && '' != $featured ) ? $featured : 'no';

				$icon_class = ' dashicons-star-empty ';
				if ( ! empty( $featured ) && 'yes' === $featured ) {
					$icon_class = ' dashicons-star-filled ';
				}
				$nonce = wp_create_nonce( 'wp_delicious_featured_recipe_nonce' );
				printf( '<a href="#" class="dr-featured-recipe dashicons %s" data-post-id="%d"  data-nonce="%s"></a>', $icon_class, $id, $nonce );
				break;
			default:
				break;
		} // end switch
	}

	/**
	 * Templates loader
	 *
	 * @return void
	 */
	public function recipe_listing_template( $template ) {
		$post          = get_post();
		$page_template = get_post_meta( $post->ID, '_wp_page_template', true );

		if ( $page_template == 'templates/pages/recipe-courses.php' ) {
			return delicious_recipes_locate_template( 'pages/recipe-courses.php' );
		}

		if ( $page_template == 'templates/pages/recipe-cooking-methods.php' ) {
			return delicious_recipes_locate_template( 'pages/recipe-cooking-methods.php' );
		}

		if ( $page_template == 'templates/pages/recipe-cuisines.php' ) {
			return delicious_recipes_locate_template( 'pages/recipe-cuisines.php' );
		}

		if ( $page_template == 'templates/pages/recipe-keys.php' ) {
			return delicious_recipes_locate_template( 'pages/recipe-keys.php' );
		}

		if ( $page_template == 'templates/pages/recipe-tags.php' ) {
			return delicious_recipes_locate_template( 'pages/recipe-tags.php' );
		}

		if ( $page_template == 'templates/pages/recipe-badges.php' ) {
			return delicious_recipes_locate_template( 'pages/recipe-badges.php' );
		}
		return $template;
	}

	/**
	 * Template definations.
	 *
	 * @return void
	 */
	public function recipe_admin_page_templates( $templates ) {

		$templates['templates/pages/recipe-courses.php']         = __( 'Recipe Courses', 'delicious-recipes' );
		$templates['templates/pages/recipe-cooking-methods.php'] = __( 'Recipe Cooking Methods', 'delicious-recipes' );
		$templates['templates/pages/recipe-cuisines.php']        = __( 'Recipe Cuisines', 'delicious-recipes' );
		$templates['templates/pages/recipe-keys.php']            = __( 'Recipe Keys', 'delicious-recipes' );
		$templates['templates/pages/recipe-tags.php']            = __( 'Recipe Tags', 'delicious-recipes' );
		$templates['templates/pages/recipe-badges.php']          = __( 'Recipe Badges', 'delicious-recipes' );

		return $templates;
	}

	/**
	 * Recipe Post Duplicator.
	 *
	 * @param   Array  $actions    Action.
	 * @param   Object $post       Post Object.
	 *
	 * @since   1.0.0
	 *
	 * @return  Array $actions;
	 */
	function duplicate_recipe_action_row( $actions, $post ) {
		// Get the post type object
		$post_type = get_post_type_object( $post->post_type );
		if ( DELICIOUS_RECIPE_POST_TYPE === $post_type->name && method_exists( $this, 'duplicate_recipe_action_row_link' ) ) {
			$actions['dr_clone_recipe'] = call_user_func( array( $this, 'duplicate_recipe_action_row_link' ), $post );
		}
		return $actions;
	}

	/**
	 * Duplication action
	 *
	 * @param [type] $post
	 * @return void
	 */
	function duplicate_recipe_action_row_link( $post ) {

		// Get the post type object
		$post_type = get_post_type_object( $post->post_type );

		if ( DELICIOUS_RECIPE_POST_TYPE !== $post_type->name ) {
			return;
		}

		// Set the button label
		$label = __( 'Clone', 'delicious-recipes' );

		// Create a nonce & add an action
		$nonce = wp_create_nonce( 'dr_clone_recipe_nonce' );

		// Return the link
		return '<a title="' . __( 'Clone ', 'delicious-recipes' ) . esc_attr( get_the_title( $post->ID ) ) . '" class="dr-clone-recipe" data-security="' . esc_attr( $nonce ) . '" href="#" data-post_id="' . esc_attr( $post->ID ) . '">' . esc_html( $label ) . '</a>';
	}

	/**
	 * Add notice related to the permalink structure.
	 *
	 * @return void
	 */
	public function permalink_structure_message() {
		$structure = get_option( 'permalink_structure' );

		if ( empty( $structure ) ) {
			?>
				<div class="notice notice-error">
					<p><?php
						/* translators: %1$s: permalink options page link */
						echo sprintf( __( '<strong>Delicious Recipes</strong> plugin uses <b>WordPress Core REST API</b> interface for creating and managing recipes and does not support the plain permalink structure. Please <a href="%1$s" >change your permalinks settings</a> to other structure to use Delicious Recipes plugin.', 'delicious-recipes' ), admin_url( 'options-permalink.php' ) );
					?></p>
				</div>
			<?php
		}

	}
	/**
	 * Disallow dynamic recipe blocks in widgets and customizer screen.
	 *
	 * @return Array $allowed_block_types
	 */
	function allowed_block_types( $allowed_block_types, $editor_context ) {

		$registered_blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();

		if ( empty( $editor_context->post ) ) {

			// specify all the blocks you would like to disable here
			unset( $registered_blocks[ 'delicious-recipes/dynamic-recipe-card' ] );
			unset( $registered_blocks[ 'delicious-recipes/dynamic-details' ] );
			unset( $registered_blocks[ 'delicious-recipes/dynamic-ingredients' ] );
			unset( $registered_blocks[ 'delicious-recipes/dynamic-instructions' ] );
			unset( $registered_blocks[ 'delicious-recipes/block-nutrition' ] );
			unset( $registered_blocks[ 'delicious-recipes/block-recipe-buttons' ] );
			unset( $registered_blocks[ 'delicious-recipes/handpicked-recipes' ] );
			unset( $registered_blocks[ 'delicious-recipes/tax-type' ] );
			unset( $registered_blocks[ 'delicious-recipes/recipe-card' ] );

			// now $registered_blocks contains only blocks registered by plugins, but we need keys only
			$registered_blocks = array_keys( $registered_blocks );

			return $registered_blocks;
		}
		return $allowed_block_types;
	}

	/**
	 * Add Recipe Count Column in Users List Table.
	 */
	public function add_user_recipes_count_column( $columns ) {
		$columns['recipe_count'] = __( 'Recipes', 'delicious-recipes' );
		return $columns;
	}

	/**
	 * Recipe Count Column Value.
	 */
	public function show_user_recipes_count_column_content( $value, $column_name, $user_id ) {
		if ( 'recipe_count' === $column_name ) {
			$value = count_user_posts( $user_id, DELICIOUS_RECIPE_POST_TYPE );
			$value = $value ? '<a href="/wp-admin/edit.php?post_status=publish&amp;post_type='.DELICIOUS_RECIPE_POST_TYPE.'&amp;author='.$user_id.'" target="_blank">'.$value.'</a>' : 0;
		}
		return $value;
	}

	public function user_recipes_column_admin_css() {
		echo '<style>
			.column-recipe_count, #recipe_count {text-align: center;}
			</style>';
	}

}
