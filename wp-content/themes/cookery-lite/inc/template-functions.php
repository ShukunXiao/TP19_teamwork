<?php
/**
 * Cookery Lite Template Functions which enhance the theme by hooking into WordPress
 *
 * @package Cookery_Lite
 */

if( ! function_exists( 'cookery_lite_doctype' ) ) :
/**
 * Doctype Declaration
*/
function cookery_lite_doctype(){ ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <?php
}
endif;
add_action( 'cookery_lite_doctype', 'cookery_lite_doctype' );

if( ! function_exists( 'cookery_lite_head' ) ) :
/**
 * Before wp_head 
*/
function cookery_lite_head(){ ?>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php
}
endif;
add_action( 'cookery_lite_before_wp_head', 'cookery_lite_head' );

if( ! function_exists( 'cookery_lite_page_start' ) ) :
/**
 * Page Start
*/
function cookery_lite_page_start(){ ?>
    <div id="page" class="site">
        <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content (Press Enter)', 'cookery-lite' ); ?></a>
    <?php
}
endif;
add_action( 'cookery_lite_before_header', 'cookery_lite_page_start', 20 );

if( ! function_exists( 'cookery_lite_header' ) ) :
/**
 * Header Start
*/
function cookery_lite_header(){ 
    $ed_cart   = get_theme_mod( 'ed_shopping_cart', true );
    $ed_search = get_theme_mod( 'ed_header_search', true ); ?>

    <?php cookery_lite_mobile_header(); ?>
        
    <header id="masthead" class="site-header style-one" itemscope itemtype="http://schema.org/WPHeader">
        <div class="header-top">
            <div class="container">
                <?php cookery_lite_secondary_navigation(); ?>
                <?php if( cookery_lite_social_links( false ) ) {
                    echo '<div class="header-social">';
                    cookery_lite_social_links();
                    echo '</div>';
                } ?>
            </div>
        </div>
        <div class="header-main">
            <div class="container">
                <?php cookery_lite_site_branding(); ?>
            </div>
        </div>
        <div class="header-bottom">
            <div class="container">
                <?php cookery_lite_primary_navigation(); ?>
                <div class="header-right">
                    <?php if( cookery_lite_is_woocommerce_activated() && $ed_cart ) {
                        echo '<div class="header-cart">';
                        cookery_lite_wc_cart_count();
                        echo '</div>';
                    } ?>
                    <?php if( $ed_search ) cookery_lite_header_search(); ?>
                </div>
            </div>
        </div>
    </header>
    <?php
}
endif;
add_action( 'cookery_lite_header', 'cookery_lite_header', 20 );

if( ! function_exists( 'cookery_lite_banner' ) ) :
/**
 * Banner Section 
*/
function cookery_lite_banner(){
    if( is_front_page() || is_home() ) {
        $ed_banner         = get_theme_mod( 'ed_banner_section', 'slider_banner' );
        $slider_type       = get_theme_mod( 'slider_type', 'latest_posts' );
        $slider_cat        = get_theme_mod( 'slider_cat' );
        $posts_per_page    = get_theme_mod( 'no_of_slides', 6 );
        $ed_full_image     = get_theme_mod( 'slider_full_image', false );
        $ed_caption        = get_theme_mod( 'slider_caption', true );
        $read_more         = get_theme_mod( 'slider_readmore', __( 'Continue Reading', 'cookery-lite' ) );
        
        
        $image_size = ( $ed_full_image ) ? 'full' : 'cookery-lite-slider';
        
        if( $ed_banner == 'static_banner' && has_custom_header() ){ 
            cookery_lite_static_cta_banner();
        }elseif( $ed_banner == 'slider_banner' ){
            if( $slider_type == 'latest_posts' || $slider_type == 'cat' || ( cookery_lite_is_delicious_recipe_activated() && $slider_type == 'latest_recipes' ) ) {
            
                $args = array(
                    'post_status'         => 'publish',            
                    'ignore_sticky_posts' => true
                );
                
                if( cookery_lite_is_delicious_recipe_activated() && $slider_type == 'latest_recipes' ){
                    $args['post_type']      = DELICIOUS_RECIPE_POST_TYPE;
                    $args['posts_per_page'] = $posts_per_page;          
                }elseif( $slider_type === 'cat' && $slider_cat ){
                    $args['post_type']      = 'post';
                    $args['cat']            = $slider_cat; 
                    $args['posts_per_page'] = -1;  
                }else{
                    $args['post_type']      = 'post';
                    $args['posts_per_page'] = $posts_per_page;
                }
                    
                $qry = new WP_Query( $args );
            
                if( $qry->have_posts() ){ ?>
                <div id="banner_section" class="site-banner banner-slider style-one">
                    <div class="item-wrapper owl-carousel">
                        <?php while( $qry->have_posts() ){ $qry->the_post(); ?>
                            <div class="item">
                                <?php 
                                echo '<div class="item-img">';
                                if( has_post_thumbnail() ){
                                    the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );    
                                }else{ 
                                    cookery_lite_get_fallback_svg( $image_size );//fallback
                                }
                                echo '</div>';
                                if( $ed_caption ){ ?>                        
                                    <div class="banner-caption">
                                        <?php cookery_lite_slider_meta_contents(); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>                        
                    </div>
                </div>
                <?php
                }
                wp_reset_postdata();
            
            }            
        }  
    }  
}
endif;
add_action( 'cookery_lite_after_header', 'cookery_lite_banner', 15 );

if( ! function_exists( 'cookery_lite_promotional_section' ) ) :
/**
 * Promotional Section 
*/
function cookery_lite_promotional_section(){
    if( ( is_front_page() || is_home() ) && is_active_sidebar( 'promo' ) ){ ?>
        <section id="promo_section" class="promo-section hide-element">
            <div class="container">
                <?php dynamic_sidebar( 'promo' ); ?>
            </div>
        </section> <!-- .promo-section -->
    <?php
    }  
}
endif;
add_action( 'cookery_lite_after_header', 'cookery_lite_promotional_section', 25 );

if( ! function_exists( 'cookery_lite_about_section' ) ) :
/**
 * About Section 
*/
function cookery_lite_about_section(){
    if( ( is_front_page() || is_home() ) && is_active_sidebar( 'about' ) ){ ?>
        <section id="about_section" class="about-section">
            <div class="section-grid">
                <?php dynamic_sidebar( 'about' ); ?>
            </div>
        </section><!-- .about-section -->
    <?php
    }
}
endif;
add_action( 'cookery_lite_after_header', 'cookery_lite_about_section', 30 );

if( ! function_exists( 'cookery_lite_content_start' ) ) :
/**
 * Content Start
 * 
*/
function cookery_lite_content_start(){
                                          
    $background_image  = '';
    if( is_archive() ){
        if( is_category() ) {
            $taxid             = get_queried_object_id();
            $cat_image_id      = get_term_meta( $taxid, 'category-image-id', true );
            $get_thumb_id      = isset( $cat_image_id ) ? $cat_image_id : false;
            $get_thumb_image   = wp_get_attachment_image_src( $get_thumb_id, 'full' );
            if( $get_thumb_image ) $background_image  = ' style="background-image: url(' . esc_url( $get_thumb_image[0] ) . ');"';
            
        }elseif( cookery_lite_is_delicious_recipe_activated() && ( is_post_type_archive( 'recipe' ) || is_tax( 'recipe-course' ) || is_tax( 'recipe-cuisine' ) || is_tax( 'recipe-cooking-method' ) || is_tax( 'recipe-key' ) || is_tax( 'recipe-tag' ) ) ){
            $taxid             = get_queried_object_id();
            $dr_taxonomy_metas = get_term_meta( $taxid, 'dr_taxonomy_metas', true );
            $get_thumb_id      = isset( $dr_taxonomy_metas['taxonomy_image'] ) ? $dr_taxonomy_metas['taxonomy_image'] : false;
            $get_thumb_image   = wp_get_attachment_image_src( $get_thumb_id, 'full' );
            if( $get_thumb_image ) $background_image  = ' style="background-image: url(' . esc_url( $get_thumb_image[0] ) . ');"';
        }
    }
    ?>

    <div id="content" class="site-content">
        <?php 
        if( ! is_home() ) : 
            if( ! cookery_lite_is_elementor_activated_post() ){
                cookery_lite_breadcrumb(); 
            }         
            if( is_archive() || is_search() || ( is_page() && apply_filters( 'cookery_lite_page_title', true ) ) ) { ?>
                <header class="page-header<?php if( $background_image ) echo ' has-bg'; ?>" <?php echo $background_image; ?>>
                    <div class="container">
                        <?php        
                            
                            if( is_archive() && ! is_search() ){ 
                                if( is_author() ){
                                    $author_title = get_the_author();
                                    $author_description = get_the_author_meta( 'description' ); ?>
                                    <div class="author-block">
                                        <div class="author-img-wrap">
                                            <figure class="author-img"><?php echo get_avatar( get_the_author_meta( 'ID' ), 280 ); ?></figure>
                                            <?php 
                                                echo '<h1 class="author-name">' . esc_html( $author_title ) . '</h1>';
                                            ?>      
                                        </div>
                                        <?php if( $author_description ) echo '<div class="author-desc">' . wp_kses_post( wpautop( $author_description ) ) . '</div>'; ?>
                                    </div>
                                    <?php 
                                }else{
                                    if( is_post_type_archive( 'recipe' ) ) {
                                        the_archive_title( '<h1 class="page-title">', '</h1>' );
                                    }else{
                                        the_archive_title();
                                    }
                                    the_archive_description( '<div class="archive-description">', '</div>' );
                                }
                            }
                            
                            if( is_search() ){ 
                                echo '<span class="page-subtitle">' . esc_html__( 'Search Results for', 'cookery-lite' ) . '</span>';
                                get_search_form();
                            }

                            if( is_page() ){
                                the_title( '<h1 class="entry-title">', '</h1>' );
                            }
                            if( ! is_author() ) cookery_lite_posts_per_page_count();
                        ?>
                    </div>
                </header>
            <?php }
        endif; ?>

        
        <div class="container">
        <?php
}
endif;
add_action( 'cookery_lite_content', 'cookery_lite_content_start' );

if( ! function_exists( 'cookery_lite_page_header' ) ) :
/**
 * Home Page header
 * 
*/
function cookery_lite_page_header(){ 
    $blog_main_title = get_theme_mod( 'blog_main_title', __( 'Latest Recipes', 'cookery-lite' ) );
    $blog_main_content = get_theme_mod( 'blog_main_content' );

    if( $blog_main_title || $blog_main_content ) : 
    
        echo '<header class="page-header">';
            if( $blog_main_title ) echo '<h2 class="page-title">' . esc_html( $blog_main_title ) . '</h2>';
            if( $blog_main_content ) echo '<div class="page-content">' . wpautop( wp_kses_post( $blog_main_content ) ) . '</div>';
        echo '</header>';
    endif; 
    
}
endif;
add_action( 'cookery_lite_before_posts_content', 'cookery_lite_page_header', 10 );

if ( ! function_exists( 'cookery_lite_post_thumbnail' ) ) :
/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 */
function cookery_lite_post_thumbnail() {
    if( cookery_lite_is_delicious_recipe_activated() && is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) return false;
    
    global $wp_query;
    $image_size     = 'thumbnail';
    $ed_featured    = get_theme_mod( 'ed_featured_image', true );
    $ed_crop_blog   = get_theme_mod( 'ed_crop_blog', false );
    $ed_crop_single = get_theme_mod( 'ed_crop_single', false );
    $sidebar        = cookery_lite_sidebar();
    
    if( is_home() ){      
        if( $wp_query->current_post == 0 ) {
            $image_size = ( $sidebar ) ? 'cookery-lite-blog-list-lg' : 'cookery-lite-blog-large';
        }else{
            $image_size = ( $sidebar ) ? 'cookery-lite-blog' : 'cookery-lite-slider';
        }

        if( has_post_thumbnail() ){                        
            echo '<figure class="post-thumbnail"><a href="' . esc_url( get_permalink() ) . '">';
            if( $ed_crop_blog ){
                the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) );
            }else{
                the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );    
            }
            echo '</a>';       
            if( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE == get_post_type() ) cookery_lite_recipe_pinit();       
            if( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE == get_post_type() ) cookery_lite_recipe_keywords();       
            echo '</figure>';
        }
    }elseif( is_archive() || is_search() ){
        if( has_post_thumbnail() ){
            echo '<figure  class="post-thumbnail"><a href="' . esc_url( get_permalink() ) . '">';
            if( $ed_crop_blog ){
                the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) );
            }else{
                the_post_thumbnail( 'cookery-lite-blog-archive', array( 'itemprop' => 'image' ) );    
            }
            echo '</a>';
            if( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE == get_post_type() ) cookery_lite_recipe_pinit();       
            if( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE == get_post_type() ) cookery_lite_recipe_keywords();
            echo '</figure>';
        }
    }elseif( is_singular() ){
        $image_size = ( $sidebar ) ? 'cookery-lite-single' : 'cookery-lite-single-two';
        if( is_single() ){
            if( $ed_featured && has_post_thumbnail() ){
                echo '<div class="post-thumbnail">';
                if( $ed_crop_single ){
                    the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) );
                }else{
                    the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );
                }
                echo '</div>';    
            }
        }else{
            if( has_post_thumbnail() ){
                echo '<div class="post-thumbnail">';
                the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );
                echo '</div>';    
            }            
        }
    }
}
endif;
add_action( 'cookery_lite_before_page_entry_content', 'cookery_lite_post_thumbnail' );
add_action( 'cookery_lite_before_post_entry_content', 'cookery_lite_post_thumbnail', 10 );
add_action( 'cookery_lite_before_single_entry_content', 'cookery_lite_post_thumbnail', 10 );

if( ! function_exists( 'cookery_lite_entry_header' ) ) :
/**
 * Entry Header
*/
function cookery_lite_entry_header(){ 

    if( cookery_lite_is_delicious_recipe_activated() && is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) return false; ?>
    
    <header class="entry-header">
		<?php 
            if( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE == get_post_type() ) {
                cookery_lite_recipe_category();
                the_title( '<h3 class="item-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
                
                echo '<div class="entry-meta">';
                cookery_lite_posted_on();
                cookery_lite_comment_count();
                cookery_lite_recipe_rating();
                echo '</div>';
            }else{

                cookery_lite_category();

                if( is_singular() ) {
                    the_title( '<h1 class="entry-title">', '</h1>' );
                }else{
                    the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
                }
            
                if( 'post' === get_post_type() ){
                    echo '<div class="entry-meta">';
                    cookery_lite_posted_by();
                    cookery_lite_posted_on();
                    cookery_lite_comment_count();
                    echo '</div>';
                }
            }
		?>
	</header>         
    <?php    
}
endif;
add_action( 'cookery_lite_post_entry_content', 'cookery_lite_entry_header', 10 );
add_action( 'cookery_lite_before_single_entry_content', 'cookery_lite_entry_header', 5 );

if( ! function_exists( 'cookery_lite_entry_content' ) ) :
/**
 * Entry Content
*/
function cookery_lite_entry_content(){ 
    $ed_excerpt = get_theme_mod( 'ed_excerpt', true ); 

    ?>
    <div class="entry-content" itemprop="text">
		<?php
			if( is_singular() || ! $ed_excerpt ){
                the_content();    
    			wp_link_pages( array(
    				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'cookery-lite' ),
    				'after'  => '</div>',
    			) );
            }else{
                the_excerpt();
            }
		?>
	</div><!-- .entry-content -->
    <?php
}
endif;
add_action( 'cookery_lite_page_entry_content', 'cookery_lite_entry_content', 15 );
add_action( 'cookery_lite_post_entry_content', 'cookery_lite_entry_content', 15 );
add_action( 'cookery_lite_single_entry_content', 'cookery_lite_entry_content', 15 );

if( ! function_exists( 'cookery_lite_entry_footer' ) ) :
/**
 * Entry Footer
*/
function cookery_lite_entry_footer(){ 
    $readmore = get_theme_mod( 'read_more_text', __( 'Read More', 'cookery-lite' ) );
    if( cookery_lite_is_delicious_recipe_activated() && is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) return false; ?>
	<footer class="entry-footer">
		<?php

            if( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE == get_post_type() ) {
                cookery_lite_posted_by();
                cookery_lite_prep_time();
                cookery_lite_difficulty_level();
            }

            if( is_singular( 'post' ) ){
                cookery_lite_tag();
            }
                
            if( is_home() || is_archive() || is_search() ){
                if( !( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE == get_post_type() ) ) {
                    echo '<a href="' . esc_url( get_the_permalink() ) . '" class="btn-readmore">' . esc_html( $readmore ) . '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none" stroke="#374757" stroke-linecap="round" stroke-width="1.5"/><path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#374757" stroke-linecap="round" stroke-width="1.5"/></g></svg></a>';
                }   
            }
            
            if( get_edit_post_link() ){
            edit_post_link(
            sprintf(
            	wp_kses(
            		/* translators: %s: Name of current post. Only visible to screen readers */
            		__( 'Edit <span class="screen-reader-text">%s</span>', 'cookery-lite' ),
            		array(
            			'span' => array(
            				'class' => array(),
            			),
            		)
            	),
            	get_the_title()
            ),
            '<span class="edit-link">',
            '</span>'
            );
            }
		?>
	</footer><!-- .entry-footer -->
	<?php 
}
endif;
add_action( 'cookery_lite_page_entry_content', 'cookery_lite_entry_footer', 20 );
add_action( 'cookery_lite_post_entry_content', 'cookery_lite_entry_footer', 20 );
add_action( 'cookery_lite_single_entry_content', 'cookery_lite_entry_footer', 20 );

if( ! function_exists( 'cookery_lite_navigation' ) ) :
/**
 * Navigation
*/
function cookery_lite_navigation(){
    
    if( cookery_lite_is_delicious_recipe_activated() && is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) return false;
    
    if( is_single() ){
        
        $next_post = get_next_post();
        $prev_post = get_previous_post();

        if( $prev_post || $next_post ) { ?>            
            <nav class="navigation post-navigation" role="navigation">
    			<h2 class="screen-reader-text"><?php esc_html_e( 'Post Navigation', 'cookery-lite' ); ?></h2>
    			<div class="nav-links">
    				<?php if( $prev_post ){ ?>
                        <div class="nav-previous">
                            <figure class="post-img">
                                <?php
                                $prev_img = get_post_thumbnail_id( $prev_post->ID );
                                if( $prev_img ){
                                    $prev_url = wp_get_attachment_image_url( $prev_img, 'thumbnail' );
                                    echo '<img src="' . esc_url( $prev_url ) . '" alt="' . the_title_attribute( 'echo=0', $prev_post ) . '">';                                        
                                }else{
                                    cookery_lite_get_fallback_svg( 'thumbnail' );
                                }
                                ?>
                            </figure>
                            <div class="nav-block">
                                <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" rel="prev">
                                    <span class="post-title"><?php echo esc_html( get_the_title( $prev_post->ID ) ); ?></span>
                                    <span class="meta-nav"><svg xmlns="http://www.w3.org/2000/svg" width="14.796" height="10.354" viewBox="0 0 14.796 10.354"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l4.117,4.116-4.117,4.116" transform="translate(-7811.241 1126.021)" fill="none" stroke="#374757" stroke-linecap="round" stroke-width="1.5"></path><path d="M6555.283-354.415h-12.624" transform="translate(-6542.659 358.532)" fill="none" stroke="#374757" stroke-linecap="round" stroke-width="1.5"></path></g></svg><?php esc_html_e( 'Previous Post', 'cookery-lite' ); ?></span>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if( $next_post ){ ?>
                        <div class="nav-next">
                            <figure class="post-img">
                                <?php
                                $next_img = get_post_thumbnail_id( $next_post->ID );
                                if( $next_img ){
                                    $next_url = wp_get_attachment_image_url( $next_img, 'thumbnail' );
                                    echo '<img src="' . esc_url( $next_url ) . '" alt="' . the_title_attribute( 'echo=0', $next_post ) . '">';                                        
                                }else{
                                    cookery_lite_get_fallback_svg( 'thumbnail' );
                                }
                                ?>
                            </figure>
                            <div class="nav-block">
                                <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" rel="next">
                                    <span class="post-title"><?php echo esc_html( get_the_title( $next_post->ID ) ); ?></span>
                                    <span class="meta-nav"><?php esc_html_e( 'Next Post', 'cookery-lite' ); ?><svg xmlns="http://www.w3.org/2000/svg" width="14.796" height="10.354" viewBox="0 0 14.796 10.354"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l4.117,4.116-4.117,4.116" transform="translate(-7811.241 1126.021)" fill="none" stroke="#374757" stroke-linecap="round" stroke-width="1.5"></path><path d="M6555.283-354.415h-12.624" transform="translate(-6542.659 358.532)" fill="none" stroke="#374757" stroke-linecap="round" stroke-width="1.5"></path></g></svg></span>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
    			</div>
    		</nav>        
            <?php
        }
    }else{ 
        the_posts_pagination( array(
            'prev_text'          => __( 'Prev', 'cookery-lite' ) . '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689">
                                <g transform="translate(17.729 11.628) rotate(180)">
                                    <path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none"
                                        stroke="#374757" stroke-linecap="round" stroke-width="1.5" />
                                    <path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#374757"
                                        stroke-linecap="round" stroke-width="1.5" />
                                </g>
                            </svg>',
            'next_text'          => __( 'Next', 'cookery-lite' ) . '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(0.75 1.061)">
                                    <path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none"
                                        stroke="#374757" stroke-linecap="round" stroke-width="1.5" />
                                    <path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#374757"
                                        stroke-linecap="round" stroke-width="1.5" />
                                </g>
                            </svg>',
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'cookery-lite' ) . ' </span>',
        ) );    
    }
}
endif;
add_action( 'cookery_lite_after_post_content', 'cookery_lite_navigation', 15 );
add_action( 'cookery_lite_after_posts_content', 'cookery_lite_navigation' );

if( ! function_exists( 'cookery_lite_author' ) ) :
/**
 * Author Section
*/
function cookery_lite_author(){ 
    
    if( cookery_lite_is_delicious_recipe_activated() && is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) return false;
    
    $ed_author    = get_theme_mod( 'ed_author', false );
    $author_title = get_the_author();
    if( ! $ed_author && get_the_author_meta( 'description' ) ){ ?>
    <div class="author-block">
		<div class="author-img-wrap">
            <figure class="author-img"><?php echo get_avatar( get_the_author_meta( 'ID' ), 280 ); ?></figure>
            <?php 
                if( $author_title ) echo '<h2 class="author-name">' . esc_html( $author_title ) . '</h2>';    
            ?>
        </div>
		<div class="author-desc">
			<?php 
                echo wpautop( wp_kses_post( get_the_author_meta( 'description' ) ) );
            ?>		
		</div>
	</div>
    <?php
    }
}
endif;
add_action( 'cookery_lite_after_post_content', 'cookery_lite_author', 25 );

if( ! function_exists( 'cookery_lite_newsletter' ) ) :
/**
 * Newsletter
*/
function cookery_lite_newsletter(){ 
    $ed_newsletter = get_theme_mod( 'ed_newsletter', false );
    $newsletter    = get_theme_mod( 'newsletter_shortcode' );
    $enable_at_bottom = get_theme_mod( 'ed_bottom_newsletter', true );
    if( $ed_newsletter && $newsletter && $enable_at_bottom ){ ?>
        <div class="newsletter">
            <?php echo do_shortcode( $newsletter ); ?>
        </div>
        <?php
    }
}
endif;
add_action( 'cookery_lite_after_post_content', 'cookery_lite_newsletter', 30 );

if( ! function_exists( 'cookery_lite_related_posts' ) ) :
/**
 * Related Posts 
*/
function cookery_lite_related_posts(){ 
    $ed_related_post = get_theme_mod( 'ed_related', true );
    
    if( $ed_related_post ){
        cookery_lite_get_posts_list( 'related' );    
    }
}
endif;                                                                               
add_action( 'cookery_lite_after_post_content', 'cookery_lite_related_posts', 35 );

if( ! function_exists( 'cookery_lite_latest_posts' ) ) :
/**
 * Latest Posts
*/
function cookery_lite_latest_posts(){ 
    cookery_lite_get_posts_list( 'latest' );
}
endif;
add_action( 'cookery_lite_latest_posts', 'cookery_lite_latest_posts' );

if( ! function_exists( 'cookery_lite_comment' ) ) :
/**
 * Comments Template 
*/
function cookery_lite_comment(){
    // If comments are open or we have at least one comment, load up the comment template.
	if( get_theme_mod( 'ed_comments', true ) && ( comments_open() || get_comments_number() ) ) :
		comments_template();
	endif;
}
endif;
add_action( 'cookery_lite_after_post_content', 'cookery_lite_comment', cookery_lite_comment_toggle() );
add_action( 'cookery_lite_after_page_content', 'cookery_lite_comment' );

if( ! function_exists( 'cookery_lite_content_end' ) ) :
/**
 * Content End
*/
function cookery_lite_content_end(){ ?>           
        </div>      
    </div><!-- .site-content -->
    <?php
}
endif;
add_action( 'cookery_lite_before_footer', 'cookery_lite_content_end', 20 );

if( ! function_exists( 'cookery_lite_featured_recipe_section' ) ) :
/**
 * Featured Recipe Section 
*/
function cookery_lite_featured_recipe_section(){
    
    $fr_enable     = get_theme_mod( 'featured_recipe_enable', true );

    if( $fr_enable && cookery_lite_is_delicious_recipe_activated() && ( is_front_page() || is_home() ) ) :

        $fr_title     = get_theme_mod( 'feature_recipe_title', __( 'Featured Recipes', 'cookery-lite' ) );
        $fr_content   = get_theme_mod( 'feature_recipe_subtitle' );
        $number_posts   = get_theme_mod( 'feature_recipe_post', 5 );

        $args = array(
            'post_type'           => DELICIOUS_RECIPE_POST_TYPE,
            'posts_per_page'      => $number_posts,
            'ignore_sticky_posts' => true,
            'meta_query'     => array(
                array(
                    'key'   => 'wp_delicious_featured_recipe',
                    'value' => 'yes'
                )
            ),
        );

        $qry = new WP_Query( $args );

        if( $qry->have_posts() ) { ?>
            <section id="featured_recipe_section" class="featured-recipe-section style-one">
                <?php if( $fr_title || $fr_content ) : ?>
                    <header class="section-header">
                        <div class="container">
                            <?php if( $fr_title ) echo '<h2 class="section-title">' . esc_html( $fr_title ) . '</h2>';
                            if( $fr_content ) echo '<div class="section-desc">' . esc_html( $fr_content ) . '</div>'; ?>
                        </div>
                    </header>
                <?php endif; ?>
                <div class="section-grid owl-carousel">
                    <?php while( $qry->have_posts() ) { $qry->the_post(); ?>
                        <div class="section-block">
                            <div class="block-img">
                                <?php if( has_post_thumbnail() ) : 
                                    the_post_thumbnail( 'cookery-lite-featured-recipe' ); 
                                else:
                                    cookery_lite_get_fallback_svg( 'cookery-lite-featured-recipe' );
                                endif; ?>
                            </div>
                            <div class="block-content">
                                <?php 
                                the_title( '<h3 class="block-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
                                echo '<div class="block-meta">';
                                cookery_lite_recipe_keywords();
                                cookery_lite_prep_time();
                                cookery_lite_difficulty_level();
                                echo '</div>';
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </section> <!-- .video-section -->
        <?php
        }
        wp_reset_postdata();
    endif;
}
endif;
add_action( 'cookery_lite_before_footer', 'cookery_lite_featured_recipe_section', 35 );

if( ! function_exists( 'cookery_lite_client_section' ) ) :
/**
 * Client Section 
*/
function cookery_lite_client_section(){
    if( ( is_front_page() || is_home() ) && is_active_sidebar( 'client' ) ){ ?>
        <section id="client_section" class="client-section">
            <div class="container">
                <?php dynamic_sidebar( 'client' ); ?>
            </div>
        </section> <!-- .client-section -->
    <?php
    }  
}
endif;
add_action( 'cookery_lite_before_footer', 'cookery_lite_client_section', 45 );

if( ! function_exists( 'cookery_lite_footer_newsletter_section' ) ) :
/**
 * Footer Newsletter Section 
*/
function cookery_lite_footer_newsletter_section(){
    $ed_newsletter = get_theme_mod( 'ed_newsletter_section', false );
    $newsletter    = get_theme_mod( 'newsletter_section_shortcode' );
    if( !is_single() && $ed_newsletter && $newsletter ){ ?>
        <section id="footer_newsletter_section" class="footer-newsletter-section">
            <?php echo do_shortcode( wp_kses_post( $newsletter ) ); ?>
        </section> <!-- .client-section -->
    <?php
    }  
}
endif;
add_action( 'cookery_lite_before_footer', 'cookery_lite_footer_newsletter_section', 50 );

if( ! function_exists( 'cookery_lite_instagram_section' ) ) :
/**
 * Instagram Section
*/
function cookery_lite_instagram_section(){ 
    if( is_btif_activated() ){
        $ed_instagram = get_theme_mod( 'ed_instagram', false );
        if( $ed_instagram ){
            echo '<div id="instagram_section" class="instagram-section">';
            echo do_shortcode( '[blossomthemes_instagram_feed]' );
            echo '</div>';    
        }
    }
}
endif;
add_action( 'cookery_lite_before_footer', 'cookery_lite_instagram_section', 55 );

if( ! function_exists( 'cookery_lite_footer_start' ) ) :
/**
 * Footer Start
*/
function cookery_lite_footer_start(){
    ?>
    <footer id="colophon" class="site-footer" itemscope itemtype="http://schema.org/WPFooter">
    <?php
}
endif;
add_action( 'cookery_lite_footer', 'cookery_lite_footer_start', 20 );

if( ! function_exists( 'cookery_lite_footer_top' ) ) :
/**
 * Footer Top
*/
function cookery_lite_footer_top(){    
    $footer_sidebars = array( 'footer-one', 'footer-two', 'footer-three', 'footer-four' );
    $active_sidebars = array();
    $sidebar_count   = 0;
    
    foreach ( $footer_sidebars as $sidebar ) {
        if( is_active_sidebar( $sidebar ) ){
            array_push( $active_sidebars, $sidebar );
            $sidebar_count++ ;
        }
    }
                 
    if( $active_sidebars ){ ?>
        <div class="footer-top">
    		<div class="container">
    			<div class="grid column-<?php echo esc_attr( $sidebar_count ); ?>">
                <?php foreach( $active_sidebars as $active ){ ?>
    				<div class="col">
    				   <?php dynamic_sidebar( $active ); ?>	
    				</div>
                <?php } ?>
                </div>
    		</div>
    	</div>
        <?php 
    }   
}
endif;
add_action( 'cookery_lite_footer', 'cookery_lite_footer_top', 30 );

if( ! function_exists( 'cookery_lite_footer_bottom' ) ) :
/**
 * Footer Bottom
*/
function cookery_lite_footer_bottom(){ ?>
    <div class="footer-bottom">
		<div class="container">
			<div class="site-info">            
            <?php
                cookery_lite_get_footer_copyright();
                echo esc_html__( ' Cookery Lite | Developed By ', 'cookery-lite' ); 
                echo '<a href="' . esc_url( 'https://blossomthemes.com/' ) .'" rel="nofollow" target="_blank">' . esc_html__( 'Blossom Themes', 'cookery-lite' ) . '</a>.';                
                printf( esc_html__( ' Powered by %s. ', 'cookery-lite' ), '<a href="'. esc_url( __( 'https://wordpress.org/', 'cookery-lite' ) ) .'" target="_blank">WordPress</a>' );
                if( function_exists( 'the_privacy_policy_link' ) ){
                    the_privacy_policy_link();
                }
            ?>               
            </div>
            <div class="footer-menu">
                <?php cookery_lite_footer_navigation(); ?>
            </div>
            <button class="back-to-top">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path fill="currentColor" d="M6.101 359.293L25.9 379.092c4.686 4.686 12.284 4.686 16.971 0L224 198.393l181.13 180.698c4.686 4.686 12.284 4.686 16.971 0l19.799-19.799c4.686-4.686 4.686-12.284 0-16.971L232.485 132.908c-4.686-4.686-12.284-4.686-16.971 0L6.101 342.322c-4.687 4.687-4.687 12.285 0 16.971z"></path>
                </svg>
            </button><!-- .back-to-top -->
		</div>
	</div>
    <?php
}
endif;
add_action( 'cookery_lite_footer', 'cookery_lite_footer_bottom', 40 );

if( ! function_exists( 'cookery_lite_footer_end' ) ) :
/**
 * Footer End 
*/
function cookery_lite_footer_end(){ ?>
    </footer><!-- #colophon -->
    <?php
}
endif;
add_action( 'cookery_lite_footer', 'cookery_lite_footer_end', 50 );

if( ! function_exists( 'cookery_lite_page_end' ) ) :
/**
 * Page End
*/
function cookery_lite_page_end(){ ?>
    </div><!-- #page -->
    <?php
}
endif;
add_action( 'cookery_lite_after_footer', 'cookery_lite_page_end', 20 );