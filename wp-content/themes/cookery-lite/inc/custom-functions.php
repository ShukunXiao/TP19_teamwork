<?php
/**
 * Cookery Lite Custom functions and definitions
 *
 * @package Cookery_Lite
 */

if ( ! function_exists( 'cookery_lite_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function cookery_lite_setup() {
	// Use minified libraries if SCRIPT_DEBUG is false
    $build  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '/build' : '';
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    /*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Cookery, use a find and replace
	 * to change 'cookery-lite' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'cookery-lite', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary'   => esc_html__( 'Primary', 'cookery-lite' ),
        'secondary' => esc_html__( 'Secondary', 'cookery-lite' ),
        'footer'    => esc_html__( 'Footer', 'cookery-lite' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-list',
		'gallery',
		'caption',
	) );
    
    // Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'cookery_lite_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
    
	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support( 
        'custom-logo', 
        array( 
            'header-text' => array( 'site-title', 'site-description' ) 
        )
    );
    
    /**
     * Add support for custom header.
    */
    add_theme_support( 
        'custom-header', 
        apply_filters( 
            'cookery_lite_custom_header_args', 
            array(
                'default-image' => '',
                'video'         => true,
                'width'         => 1920,
                'height'        => 760,
                'flex-height'   => true,
                'flex-width'    => true,
                'header-text'   => false
            ) 
        ) 
    );

    /**
     * Add support for Delicious Recipes Plugin.
    */
    add_theme_support('delicious_recipes');
 
    /**
     * Add Custom Images sizes.
    */    
    add_image_size( 'cookery-lite-slider', 480, 640 , true );
    add_image_size( 'cookery-lite-featured-recipe', 384, 512, true );
    add_image_size( 'cookery-lite-blog', 286, 382, true );
    add_image_size( 'cookery-lite-blog-list-lg', 768, 768, true );
    add_image_size( 'cookery-lite-blog-large', 1214, 1214, true );
    add_image_size( 'cookery-lite-blog-archive', 307, 410, true );
    add_image_size( 'cookery-lite-promo', 366, 231, true );
    add_image_size( 'cookery-lite-single', 768, 500, true );
    add_image_size( 'cookery-lite-single-two', 1220, 600, true );
    add_image_size( 'cookery-lite-related', 384, 512, true );
    
    // Add theme support for Responsive Videos.
    add_theme_support( 'jetpack-responsive-videos' );

    // Add excerpt support for pages
    add_post_type_support( 'page', 'excerpt' );

    // Add support for full and wide align images.
    add_theme_support( 'align-wide' );

    // Add support for editor styles.
    add_theme_support( 'editor-styles' );
        
    /*
     * This theme styles the visual editor to resemble the theme style,
     * specifically font, colors, and column width.
     *
     */
    add_editor_style( array(
            'css' . $build . '/editor-style' . $suffix . '.css',
            cookery_lite_fonts_url()
        )
    );

    // Add support for block editor styles.
    add_theme_support( 'wp-block-styles' );

    /** Starter Content */
    $starter_content = array(
        // Specify the core-defined pages to create and add custom thumbnails to some of them.
        'posts' => array( 
            'home', 
            'blog',
        ),
    );
    
    $starter_content = apply_filters( 'cookery_lite_starter_content', $starter_content );

    add_theme_support( 'starter-content', $starter_content );

    /** Remove widget block */
    remove_theme_support( 'widgets-block-editor' );

}
endif;
add_action( 'after_setup_theme', 'cookery_lite_setup' );

if( ! function_exists( 'cookery_lite_content_width' ) ) :
/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function cookery_lite_content_width() {
	
    $GLOBALS['content_width'] = apply_filters( 'cookery_lite_content_width', 814 );
}
endif;
add_action( 'after_setup_theme', 'cookery_lite_content_width', 0 );

if( ! function_exists( 'cookery_lite_template_redirect_content_width' ) ) :
/**
* Adjust content_width value according to template.
*
* @return void
*/
function cookery_lite_template_redirect_content_width(){
	$sidebar = cookery_lite_sidebar();
    if( $sidebar ){	   
        $GLOBALS['content_width'] = 814;      
	}else{
        if( is_singular() ){
            if( cookery_lite_sidebar( true ) === 'full-width centered' ){
                $GLOBALS['content_width'] = 814;
            }else{
                $GLOBALS['content_width'] = 1218;                
            }                
        }else{
            $GLOBALS['content_width'] = 1218;
        }
	}
}
endif;
add_action( 'template_redirect', 'cookery_lite_template_redirect_content_width' );

if( ! function_exists( 'cookery_lite_scripts' ) ) :
/**
 * Enqueue scripts and styles.
 */
function cookery_lite_scripts() {
	// Use minified libraries if SCRIPT_DEBUG is false
    $build  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '/build' : '';
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
    
    if( cookery_lite_is_woocommerce_activated() )
    wp_enqueue_style( 'cookery-lite-woocommerce', get_template_directory_uri(). '/css' . $build . '/woocommerce' . $suffix . '.css', array(), COOKERY_LITE_THEME_VERSION );
    
    wp_enqueue_style( 'cookery-lite-google-fonts', cookery_lite_fonts_url(), array(), null );

    wp_enqueue_style( 'owl-carousel', get_template_directory_uri(). '/css' . $build . '/owl.carousel' . $suffix . '.css', array(), '2.3.4' );
    wp_enqueue_style( 'animate', get_template_directory_uri(). '/css' . $build . '/animate' . $suffix . '.css', array(), '3.5.2' );
    wp_enqueue_style( 'cookery-lite', get_stylesheet_uri(), array(), COOKERY_LITE_THEME_VERSION );
    
    if( cookery_lite_is_elementor_activated() ){
        wp_enqueue_style( 'cookery-lite-elementor', get_template_directory_uri(). '/css' . $build . '/elementor' . $suffix . '.css', array(), COOKERY_LITE_THEME_VERSION );
    }
    
    wp_enqueue_style( 'cookery-lite-gutenberg', get_template_directory_uri(). '/css' . $build . '/gutenberg' . $suffix . '.css', array(), COOKERY_LITE_THEME_VERSION );    
    
    wp_enqueue_script( 'all', get_template_directory_uri() . '/js' . $build . '/all' . $suffix . '.js', array( 'jquery' ), '5.6.3', true );
    wp_enqueue_script( 'v4-shims', get_template_directory_uri() . '/js' . $build . '/v4-shims' . $suffix . '.js', array( 'jquery', 'all' ), '5.6.3', true );
	wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/js' . $build . '/owl.carousel' . $suffix . '.js', array( 'jquery' ), '2.3.4', true );
    wp_enqueue_script( 'owlcarousel2-a11ylayer', get_template_directory_uri() . '/js' . $build . '/owlcarousel2-a11ylayer' . $suffix . '.js', array( 'jquery', 'owl-carousel' ), '0.2.1', true );
    wp_enqueue_script( 'cookery-lite', get_template_directory_uri() . '/js' . $build . '/custom' . $suffix . '.js', array( 'jquery', 'masonry' ), COOKERY_LITE_THEME_VERSION, true );
    wp_enqueue_script( 'cookery-lite-modal', get_template_directory_uri() . '/js' . $build . '/modal-accessibility' . $suffix . '.js', array( 'jquery' ), COOKERY_LITE_THEME_VERSION, true );
    
    $array = array( 
        'rtl'           => is_rtl(),
        'auto'          => (bool) get_theme_mod( 'slider_auto', true ),
        'loop'          => (bool) get_theme_mod( 'slider_loop', true ),
        'ajax_url'      => admin_url( 'admin-ajax.php' ),
    );
    
    wp_localize_script( 'cookery-lite', 'cookery_lite_data', $array );
    
    if ( cookery_lite_is_jetpack_activated( true ) ) {
        wp_enqueue_style( 'tiled-gallery', plugins_url() . '/jetpack/modules/tiled-gallery/tiled-gallery/tiled-gallery.css' );            
    }
    
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
endif;
add_action( 'wp_enqueue_scripts', 'cookery_lite_scripts' );

if( ! function_exists( 'cookery_lite_admin_scripts' ) ) :
/**
 * Enqueue admin scripts and styles.
*/
function cookery_lite_admin_scripts( $hook ){
    wp_enqueue_style( 'cookery-admin', get_template_directory_uri() . '/inc/css/admin.css', '', COOKERY_LITE_THEME_VERSION );
}
endif; 
add_action( 'admin_enqueue_scripts', 'cookery_lite_admin_scripts' );

if( ! function_exists( 'cookery_lite_block_editor_styles' ) ) :
/**
 * Enqueue editor styles for Gutenberg
 */
function cookery_lite_block_editor_styles() {
    // Use minified libraries if SCRIPT_DEBUG is false
    $build  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '/build' : '';
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
    
    // Block styles.
    wp_enqueue_style( 'cookery-lite-block-editor-style', get_template_directory_uri() . '/css' . $build . '/editor-block' . $suffix . '.css' );

    wp_add_inline_style( 'cookery-lite-block-editor-style', cookery_lite_gutenberg_inline_style() );

    // Add custom fonts.
    wp_enqueue_style( 'cookery-lite-google-fonts', cookery_lite_fonts_url(), array(), null );
}
endif;
add_action( 'enqueue_block_editor_assets', 'cookery_lite_block_editor_styles' );

if( ! function_exists( 'cookery_lite_body_classes' ) ) :
/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function cookery_lite_body_classes( $classes ) {
    $editor_options = get_option( 'classic-editor-replace' );
    $allow_users_options = get_option( 'classic-editor-allow-users' );
    
    // Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
    
    // Adds a class of custom-background-image to sites with a custom background image.
    if ( get_background_image() ) {
        $classes[] = 'custom-background-image';
    }
    
    // Adds a class of custom-background-color to sites with a custom background color.
    if ( get_background_color() != 'ffffff' ) {
        $classes[] = 'custom-background-color';
    }
    
    if ( is_singular() && ( !cookery_lite_is_classic_editor_activated() || ( cookery_lite_is_classic_editor_activated() && $editor_options == 'block' ) || ( cookery_lite_is_classic_editor_activated() && $allow_users_options == 'allow' && has_blocks() ) ) ) {
        $classes[] = 'cookery-lite-has-blocks';
    }

    if( is_home() ) {
        $classes[] = 'list-with-first-lg';
    }

    if( cookery_lite_is_delicious_recipe_activated() && is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) {
        $classes[] = 'single-recipe-one';
    }

    if( is_singular( 'post' ) ) {
        $classes[] = 'single-post-one';
    }
    
    if( is_archive() || is_search() ) {
        $classes[] = 'list-with-all-tall-img';
    }

    $classes[] = cookery_lite_sidebar( true );
    
	return $classes;
}
endif;
add_filter( 'body_class', 'cookery_lite_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function cookery_lite_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'cookery_lite_pingback_header' );

if( ! function_exists( 'cookery_lite_change_comment_form_default_fields' ) ) :
/**
 * Change Comment form default fields i.e. author, email & url.
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function cookery_lite_change_comment_form_default_fields( $fields ){    
    // get the current commenter if available
    $commenter = wp_get_current_commenter();
 
    // core functionality
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );    
 
    // Change just the author field
    $fields['author'] = '<p class="comment-form-author"><label for="author">' . esc_html__( 'Name', 'cookery-lite' ) . '<span class="required">*</span></label><input id="author" name="author" placeholder="' . esc_attr__( 'Name*', 'cookery-lite' ) . '" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>';
    
    $fields['email'] = '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'cookery-lite' ) . '<span class="required">*</span></label><input id="email" name="email" placeholder="' . esc_attr__( 'Email*', 'cookery-lite' ) . '" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>';
    
    $fields['url'] = '<p class="comment-form-url"><label for="url">' . esc_html__( 'Website', 'cookery-lite' ) . '</label><input id="url" name="url" placeholder="' . esc_attr__( 'Website', 'cookery-lite' ) . '" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>'; 
    
    return $fields;    
}
endif;
add_filter( 'comment_form_default_fields', 'cookery_lite_change_comment_form_default_fields' );

if( ! function_exists( 'cookery_lite_change_comment_form_defaults' ) ) :
/**
 * Change Comment Form defaults
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function cookery_lite_change_comment_form_defaults( $defaults ){    
    $defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Comment', 'cookery-lite' ) . '</label><textarea id="comment" name="comment" placeholder="' . esc_attr__( 'Comment', 'cookery-lite' ) . '" cols="45" rows="8" aria-required="true"></textarea></p>';
    
    return $defaults;    
}
endif;
add_filter( 'comment_form_defaults', 'cookery_lite_change_comment_form_defaults' );

if ( ! function_exists( 'cookery_lite_excerpt_more' ) ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... * 
 */
function cookery_lite_excerpt_more( $more ) {
	return is_admin() ? $more : ' &hellip; ';
}

endif;
add_filter( 'excerpt_more', 'cookery_lite_excerpt_more' );

if ( ! function_exists( 'cookery_lite_excerpt_length' ) ) :
/**
 * Changes the default 35 character in excerpt 
*/
function cookery_lite_excerpt_length( $length ) {
	$excerpt_length = get_theme_mod( 'excerpt_length', 35 );
    return is_admin() ? $length : absint( $excerpt_length );    
}
endif;
add_filter( 'excerpt_length', 'cookery_lite_excerpt_length', 999 );

if( ! function_exists( 'cookery_lite_exclude_cat' ) ) :
/**
 * Exclude post with Category from blog and archive page. 
*/
function cookery_lite_exclude_cat( $query ){

    $ed_banner        = get_theme_mod( 'ed_banner_section', 'slider_banner' );
    $slider_type      = get_theme_mod( 'slider_type', 'latest_posts' );
    $slider_cat       = get_theme_mod( 'slider_cat' );
    $posts_per_page   = get_theme_mod( 'no_of_slides', 3 );
    $repetitive_posts = get_theme_mod( 'include_repetitive_posts', false );
    
    if( ! is_admin() && $query->is_main_query() && $query->is_home() && ( $ed_banner == 'slider_banner' ) && ! $repetitive_posts ){
        if( $slider_type === 'cat' && $slider_cat  ){            
 			$query->set( 'category__not_in', array( $slider_cat ) );    		
        }elseif( $slider_type == 'latest_posts' ){
            $args = array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => $posts_per_page,
                'ignore_sticky_posts' => true
            );
            $latest = get_posts( $args );
            $excludes = array();
            foreach( $latest as $l ){
                array_push( $excludes, $l->ID );
            }
            $query->set( 'post__not_in', $excludes );
        }  
    }      
}
endif;
add_filter( 'pre_get_posts', 'cookery_lite_exclude_cat' );

if( ! function_exists( 'cookery_lite_get_the_archive_title' ) ) :
/**
 * Filter Archive Title
*/
function cookery_lite_get_the_archive_title( $title ){
    
    $ed_prefix = get_theme_mod( 'ed_prefix_archive', true );
    if( is_post_type_archive( 'product' ) ){
        $title = '<h1 class="page-title">' . get_the_title( get_option( 'woocommerce_shop_page_id' ) ) . '</h1>';
    }else{
        if( is_category() ){
            if( $ed_prefix ) {
                $title = '<h1 class="page-title">' . esc_html( single_cat_title( '', false ) ) . '</h1>';
            }else{
                $title = '<span class="page-subtitle">'. esc_html__( 'Category', 'cookery-lite' ) . '</span><h1 class="page-title">' . esc_html( single_cat_title( '', false ) ) . '</h1>';
            }
        }
        elseif( is_tag() ){
            if( $ed_prefix ) {
                $title = '<h1 class="page-title">' . esc_html( single_tag_title( '', false ) ) . '</h1>';
            }else{
                $title = '<span class="page-subtitle">'. esc_html__( 'Tagged In', 'cookery-lite' ) . '</span><h1 class="page-title">' . esc_html( single_tag_title( '', false ) ) . '</h1>';
            }
        }elseif( is_year() ){
            if( $ed_prefix ){
                $title = '<h1 class="page-title">' . get_the_date( _x( 'Y', 'yearly archives date format', 'cookery-lite' ) ) . '</h1>';                   
            }else{
                $title = '<span class="page-subtitle">'. esc_html__( 'Year', 'cookery-lite' ) . '</span><h1 class="page-title">' . get_the_date( _x( 'Y', 'yearly archives date format', 'cookery-lite' ) ) . '</h1>';
            }
        }elseif( is_month() ){
            if( $ed_prefix ){
                $title = '<h1 class="page-title">' . get_the_date( _x( 'F Y', 'monthly archives date format', 'cookery-lite' ) ) . '</h1>';                                   
            }else{
                $title = '<span class="page-subtitle">'. esc_html__( 'Month', 'cookery-lite' ) . '</span><h1 class="page-title">' . get_the_date( _x( 'F Y', 'monthly archives date format', 'cookery-lite' ) ) . '</h1>';
            }
        }elseif( is_day() ){
            if( $ed_prefix ){
                $title = '<h1 class="page-title">' . get_the_date( _x( 'F j, Y', 'daily archives date format', 'cookery-lite' ) ) . '</h1>';                                   
            }else{
                $title = '<span class="page-subtitle">'. esc_html__( 'Day', 'cookery-lite' ) . '</span><h1 class="page-title">' . get_the_date( _x( 'F j, Y', 'daily archives date format', 'cookery-lite' ) ) .  '</h1>';
            }
        }elseif( is_post_type_archive() ) {
            if( $ed_prefix ){
                $title = '<h1 class="page-title">'  . post_type_archive_title( '', false ) . '</h1>';                            
            }else{
                $title = '<span class="page-subtitle">'. esc_html__( 'Archives', 'cookery-lite' ) . '</span><h1 class="page-title">'  . post_type_archive_title( '', false ) . '</h1>';
            }
        }elseif( is_tax() ) {
            $tax = get_taxonomy( get_queried_object()->taxonomy );
            if( $ed_prefix ){
                $title = '<h1 class="page-title">' . single_term_title( '', false ) . '</h1>';                                   
            }else{
                $title = '<span class="page-subtitle">' . $tax->labels->singular_name . '</span><h1 class="page-title">' . single_term_title( '', false ) . '</h1>';
            }
        }
    }  
    return $title;
}
endif;
add_filter( 'get_the_archive_title', 'cookery_lite_get_the_archive_title' );

if( ! function_exists( 'cookery_lite_remove_archive_description' ) ) :
/**
 * filter the_archive_description & get_the_archive_description to show post type archive
 * @param  string $description original description
 * @return string post type description if on post type archive
 */
function cookery_lite_remove_archive_description( $description ){
    $ed_shop_archive_description = get_theme_mod( 'ed_shop_archive_description', false );
    if( is_post_type_archive( 'product' ) ) {
        if( ! $ed_shop_archive_description ){
            $description = '';
        }
    }
    return $description;
}
endif;
add_filter( 'get_the_archive_description', 'cookery_lite_remove_archive_description' );

if( ! function_exists( 'cookery_lite_get_comment_author_link' ) ) :
/**
 * Filter to modify comment author link
 * @link https://developer.wordpress.org/reference/functions/get_comment_author_link/
 */
function cookery_lite_get_comment_author_link( $return, $author, $comment_ID ){
    $comment = get_comment( $comment_ID );
    $url     = get_comment_author_url( $comment );
    $author  = get_comment_author( $comment );
 
    if ( empty( $url ) || 'http://' == $url )
        $return = '<span itemprop="name">'. esc_html( $author ) .'</span>';
    else
        $return = '<span itemprop="name"><a href=' . esc_url( $url ) . ' rel="external nofollow noopener" class="url" itemprop="url">' . esc_html( $author ) . '</a></span>';

    return $return;
}
endif;
add_filter( 'get_comment_author_link', 'cookery_lite_get_comment_author_link', 10, 3 );

if( ! function_exists( 'cookery_lite_filter_post_gallery' ) ) :
/**
 * Filter the output of the gallery. 
*/ 
function cookery_lite_filter_post_gallery( $output, $attr, $instance ){
    global $post, $wp_locale;

    $html5 = current_theme_supports( 'html5', 'gallery' );
    $atts = shortcode_atts( array(
    	'order'      => 'ASC',
    	'orderby'    => 'menu_order ID',
    	'id'         => $post ? $post->ID : 0,
    	'itemtag'    => $html5 ? 'figure'     : 'dl',
    	'icontag'    => $html5 ? 'div'        : 'dt',
    	'captiontag' => $html5 ? 'figcaption' : 'dd',
    	'columns'    => 3,
    	'size'       => 'thumbnail',
    	'include'    => '',
    	'exclude'    => '',
    	'link'       => ''
    ), $attr, 'gallery' );
    
    $id = intval( $atts['id'] );
    
    if ( ! empty( $atts['include'] ) ) {
    	$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
    
    	$attachments = array();
    	foreach ( $_attachments as $key => $val ) {
    		$attachments[$val->ID] = $_attachments[$key];
    	}
    } elseif ( ! empty( $atts['exclude'] ) ) {
    	$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
    } else {
    	$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
    }
    
    if ( empty( $attachments ) ) {
    	return '';
    }
    
    if ( is_feed() ) {
    	$output = "\n";
    	foreach ( $attachments as $att_id => $attachment ) {
    		$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
    	}
    	return $output;
    }
    
    $itemtag = tag_escape( $atts['itemtag'] );
    $captiontag = tag_escape( $atts['captiontag'] );
    $icontag = tag_escape( $atts['icontag'] );
    $valid_tags = wp_kses_allowed_html( 'post' );
    if ( ! isset( $valid_tags[ $itemtag ] ) ) {
    	$itemtag = 'dl';
    }
    if ( ! isset( $valid_tags[ $captiontag ] ) ) {
    	$captiontag = 'dd';
    }
    if ( ! isset( $valid_tags[ $icontag ] ) ) {
    	$icontag = 'dt';
    }
    
    $columns = intval( $atts['columns'] );
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';
    
    $selector = "gallery-{$instance}";
    
    $gallery_style = '';
    
    /**
     * Filter whether to print default gallery styles.
     *
     * @since 3.1.0
     *
     * @param bool $print Whether to print default gallery styles.
     *                    Defaults to false if the theme supports HTML5 galleries.
     *                    Otherwise, defaults to true.
     */
    if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
    	$gallery_style = "
    	<style type='text/css'>
    		#{$selector} {
    			margin: auto;
    		}
    		#{$selector} .gallery-item {
    			float: {$float};
    			margin-top: 10px;
    			text-align: center;
    			width: {$itemwidth}%;
    		}
    		#{$selector} img {
    			border: 2px solid #cfcfcf;
    		}
    		#{$selector} .gallery-caption {
    			margin-left: 0;
    		}
    		/* see gallery_shortcode() in wp-includes/media.php */
    	</style>\n\t\t";
    }
    
    $size_class = sanitize_html_class( $atts['size'] );
    $gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
    
    /**
     * Filter the default gallery shortcode CSS styles.
     *
     * @since 2.5.0
     *
     * @param string $gallery_style Default CSS styles and opening HTML div container
     *                              for the gallery shortcode output.
     */
    $output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );
    
    $i = 0; 
    foreach ( $attachments as $id => $attachment ) {
            
    	$attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
    	if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
    		//$image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr ); // for attachment url 
            $image_output = "<a href='" . wp_get_attachment_url( $id ) . "' data-fancybox='group{$columns}' data-caption='" . esc_attr( $attachment->post_excerpt ) . "'>";
            $image_output .= wp_get_attachment_image( $id, $atts['size'], false, $attr );
            $image_output .= "</a>";
    	} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
    		$image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
    	} else {
    		$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr ); //for attachment page
    	}
    	$image_meta  = wp_get_attachment_metadata( $id );
    
    	$orientation = '';
    	if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
    		$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
    	}
    	$output .= "<{$itemtag} class='gallery-item'>";
    	$output .= "
    		<{$icontag} class='gallery-icon {$orientation}'>
    			$image_output
    		</{$icontag}>";
    	if ( $captiontag && trim($attachment->post_excerpt) ) {
    		$output .= "
    			<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
    			" . wptexturize($attachment->post_excerpt) . "
    			</{$captiontag}>";
    	}
    	$output .= "</{$itemtag}>";
    	if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
    		$output .= '<br style="clear: both" />';
    	}
    }
    
    if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
    	$output .= "
    		<br style='clear: both' />";
    }
    
    $output .= "
    	</div>\n";
    
    return $output;
}
endif;
if( class_exists( 'Jetpack' ) ){
    if( !Jetpack::is_module_active( 'carousel' ) ){
        add_filter( 'post_gallery', 'cookery_lite_filter_post_gallery', 10, 3 );
    }
}else{
    add_filter( 'post_gallery', 'cookery_lite_filter_post_gallery', 10, 3 );
}

if( ! function_exists( 'cookery_lite_admin_notice' ) ) :
/**
 * Addmin notice for getting started page
*/
function cookery_lite_admin_notice(){
    global $pagenow;
    $theme_args      = wp_get_theme();
    $meta            = get_option( 'cookery_lite_admin_notice' );
    $name            = $theme_args->__get( 'Name' );
    $current_screen  = get_current_screen();
    
    if( 'themes.php' == $pagenow && !$meta ){
        
        if( $current_screen->id !== 'dashboard' && $current_screen->id !== 'themes' ){
            return;
        }

        if( is_network_admin() ){
            return;
        }

        if( ! current_user_can( 'manage_options' ) ){
            return;
        } ?>

        <div class="welcome-message notice notice-info">
            <div class="notice-wrapper">
                <div class="notice-text">
                    <h3><?php esc_html_e( 'Congratulations!', 'cookery-lite' ); ?></h3>
                    <p><?php printf( __( '%1$s is now installed and ready to use. Click below to see theme documentation, plugins to install and other details to get started.', 'cookery-lite' ), esc_html( $name ) ); ?></p>
                    <p><a href="<?php echo esc_url( admin_url( 'themes.php?page=cookery-lite-getting-started' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Go to the getting started.', 'cookery-lite' ); ?></a></p>
                    <p class="dismiss-link"><strong><a href="?cookery_lite_admin_notice=1"><?php esc_html_e( 'Dismiss', 'cookery-lite' ); ?></a></strong></p>
                </div>
            </div>
        </div>
    <?php }
}
endif;
add_action( 'admin_notices', 'cookery_lite_admin_notice' );

if( ! function_exists( 'cookery_lite_update_admin_notice' ) ) :
/**
 * Updating admin notice on dismiss
*/
function cookery_lite_update_admin_notice(){
    if ( isset( $_GET['cookery_lite_admin_notice'] ) && $_GET['cookery_lite_admin_notice'] = '1' ) {
        update_option( 'cookery_lite_admin_notice', true );
    }
}
endif;
add_action( 'admin_init', 'cookery_lite_update_admin_notice' );

if ( ! function_exists( 'cookery_lite_get_fontawesome_ajax' ) ) :
/**
 * Return an array of all icons.
 */
function cookery_lite_get_fontawesome_ajax() {
    // Bail if the nonce doesn't check out
    if ( ! isset( $_POST['cookery_lite_customize_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['cookery_lite_customize_nonce'] ), 'cookery_lite_customize_nonce' ) ) {
        wp_die();
    }

    // Do another nonce check
    check_ajax_referer( 'cookery_lite_customize_nonce', 'cookery_lite_customize_nonce' );

    // Bail if user can't edit theme options
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        wp_die();
    }

    // Get all of our fonts
    $fonts = cookery_lite_get_fontawesome_list();
    
    ob_start();
    if( $fonts ){ ?>
        <ul class="font-group">
            <?php 
                foreach( $fonts as $font ){
                    echo '<li data-font="' . esc_attr( $font ) . '"><i class="' . esc_attr( $font ) . '"></i></li>';                        
                }
            ?>
        </ul>
        <?php
    }
    echo ob_get_clean();

    // Exit
    wp_die();
}
endif;
add_action( 'wp_ajax_cookery_lite_get_fontawesome_ajax', 'cookery_lite_get_fontawesome_ajax' );

if ( ! function_exists( 'cookery_lite_dynamic_mce_css' ) ) :
/**
 * Add Editor Style 
 * Add Link Color Option in Editor Style (MCE CSS)
 */
function cookery_lite_dynamic_mce_css( $mce_css ){
    $mce_css .= ', ' . add_query_arg( array( 'action' => 'cookery_lite_dynamic_mce_css', '_nonce' => wp_create_nonce( 'cookery_lite_dynamic_mce_nonce', __FILE__ ) ), admin_url( 'admin-ajax.php' ) );
    return $mce_css;
}
endif;
add_filter( 'mce_css', 'cookery_lite_dynamic_mce_css' );
 
if ( ! function_exists( 'cookery_lite_dynamic_mce_css_ajax_callback' ) ) : 
/**
 * Ajax Callback
 */
function cookery_lite_dynamic_mce_css_ajax_callback(){
 
    /* Check nonce for security */
    $nonce = isset( $_REQUEST['_nonce'] ) ? $_REQUEST['_nonce'] : '';
    if( ! wp_verify_nonce( $nonce, 'cookery_lite_dynamic_mce_nonce' ) ){
        die(); // don't print anything
    }
 
    /* Get Link Color */
    $primary_font    = get_theme_mod( 'primary_font', 'Questrial' );
    $primary_fonts   = cookery_lite_get_fonts( $primary_font, 'regular' );
    $secondary_font  = get_theme_mod( 'secondary_font', 'Noto Serif' );
    $secondary_fonts = cookery_lite_get_fonts( $secondary_font, 'regular' );

    $primary_color    = get_theme_mod( 'primary_color', '#2db68d' ); 
    $secondary_color  = get_theme_mod( 'secondary_color', '#e84e3b' ); 

    $rgb = cookery_lite_hex2rgb( cookery_lite_sanitize_hex_color( $primary_color ) );
    $rgb2 = cookery_lite_hex2rgb( cookery_lite_sanitize_hex_color( $secondary_color ) );
 
    /* Set File Type and Print the CSS Declaration */
    header( 'Content-type: text/css' );
    echo ':root .mce-content-body {
        --primary-color: ' . cookery_lite_sanitize_hex_color( $primary_color ) . ';
        --primary-color-rgb: ' . sprintf( '%1$s, %2$s, %3$s', $rgb[0], $rgb[1], $rgb[2] ) . ';
        --secondary-color: ' . cookery_lite_sanitize_hex_color( $secondary_color ) . ';
        --secondary-color-rgb: ' . sprintf('%1$s, %2$s, %3$s', $rgb2[0], $rgb2[1], $rgb2[2] ) . ';
        --primary-font: ' . esc_html( $primary_fonts['font'] ) . ';
        --secondary-font: ' . esc_html( $secondary_fonts['font'] ) . ';
    }

    .mce-content-body blockquote::before, 
    .mce-content-body q::before {
        content: "";
        background-image: url(\'data:image/svg+xml; utf-8, <svg xmlns="http://www.w3.org/2000/svg" width="64" height="40.062" viewBox="0 0 64 40.062"><path d="M68.871,47.073A12.886,12.886,0,0,0,56.71,36.191c1.494-5.547,5.121-7.752,9.53-9.032a.515.515,0,0,0,.356-.569l-.711-4.409s-.071-.356-.64-.284C50.024,23.6,39.712,35.2,41.632,49.277,43.41,59.021,51.02,62.79,58.061,61.794a12.968,12.968,0,0,0,10.81-14.722ZM20.3,36.191c1.422-5.547,5.192-7.752,9.53-9.032a.515.515,0,0,0,.356-.569l-.64-4.409s-.071-.356-.64-.284C13.682,23.532,3.441,35.124,5.219,49.206c1.849,9.815,9.53,13.584,16.5,12.588A12.865,12.865,0,0,0,32.458,47.073,12.693,12.693,0,0,0,20.3,36.191Z" transform="translate(-5.018 -21.887)" fill="' . cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ) . '"/></svg>\');
    }';
    die(); // end ajax process.
}
endif;
add_action( 'wp_ajax_cookery_lite_dynamic_mce_css', 'cookery_lite_dynamic_mce_css_ajax_callback' );
add_action( 'wp_ajax_no_priv_cookery_lite_dynamic_mce_css', 'cookery_lite_dynamic_mce_css_ajax_callback' );

if ( ! function_exists( 'cookery_lite_check_hide_title' ) ) {
    /**
     * Check hide title.
     *
     * @param bool $val default value.
     *
     * @return bool
     */
    function cookery_lite_check_hide_title( $val ) {
        if ( cookery_lite_is_elementor_activated() && cookery_lite_is_elementor_activated_post() ) {
            $current_doc = \Elementor\Plugin::instance()->documents->get( get_the_ID() );
            if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
                $val = false;
            }
        }
        return $val;
    }
}
add_filter( 'cookery_lite_page_title', 'cookery_lite_check_hide_title' );