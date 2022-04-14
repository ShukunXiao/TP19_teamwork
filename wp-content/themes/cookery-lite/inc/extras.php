<?php
/**
 * Cookery Lite Standalone Functions.
 *
 * @package Cookery_Lite
 */

if ( ! function_exists( 'cookery_lite_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time.
 */
function cookery_lite_posted_on() {
    $ed_post_date   = get_theme_mod( 'ed_post_date', false );

    if( $ed_post_date ) return false;
	$ed_updated_post_date = get_theme_mod( 'ed_post_update_date', true );
    
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		if( $ed_updated_post_date ){
            $time_string = '<time class="entry-date published updated" datetime="%3$s" itemprop="dateModified">%4$s</time><time class="updated" datetime="%1$s" itemprop="datePublished">%2$s</time>';		  
		}else{
            $time_string = '<time class="entry-date published" datetime="%1$s" itemprop="datePublished">%2$s</time><time class="updated" datetime="%3$s" itemprop="dateModified">%4$s</time>';  
		}        
	}else{
	   $time_string = '<time class="entry-date published updated" datetime="%1$s" itemprop="datePublished">%2$s</time><time class="updated" datetime="%3$s" itemprop="dateModified">%4$s</time>';   
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);
    
    $posted_on = sprintf( '%1$s', '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>' );
	
	echo '<span class="posted-on"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="33" height="31" viewBox="0 0 33 31"><defs><filter id="Rectangle_1344" x="0" y="0" width="33" height="31" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"/><feGaussianBlur stdDeviation="3" result="blur"/><feFlood flood-color="#e84e3b" flood-opacity="0.102"/><feComposite operator="in" in2="blur"/><feComposite in="SourceGraphic"/></filter></defs><g id="Group_5559" data-name="Group 5559" transform="translate(-534.481 -811)"><g transform="matrix(1, 0, 0, 1, 534.48, 811)" filter="url(#Rectangle_1344)"><rect id="Rectangle_1344-2" data-name="Rectangle 1344" width="15" height="13" transform="translate(9 6)" fill="#fff"/></g><path id="Path_30675" data-name="Path 30675" d="M5.84,23.3a2.279,2.279,0,0,1-2.277-2.277V10.1A2.279,2.279,0,0,1,5.84,7.821H7.206V6.455a.455.455,0,0,1,.911,0V7.821h6.375V6.455a.455.455,0,0,1,.911,0V7.821h1.366A2.28,2.28,0,0,1,19.044,10.1V21.026A2.279,2.279,0,0,1,16.767,23.3ZM4.474,21.026A1.367,1.367,0,0,0,5.84,22.392H16.767a1.368,1.368,0,0,0,1.366-1.366V12.374H4.474ZM5.84,8.732A1.367,1.367,0,0,0,4.474,10.1v1.366h13.66V10.1a1.368,1.368,0,0,0-1.366-1.366Z" transform="translate(539.437 808)" fill="#abadb4"/><g id="Group_5542" data-name="Group 5542" transform="translate(547.149 822.506)"><path id="Path_30676" data-name="Path 30676" d="M1036.473-439.908a.828.828,0,0,1,.831.814.832.832,0,0,1-.833.838.831.831,0,0,1-.825-.822A.826.826,0,0,1,1036.473-439.908Z" transform="translate(-1035.646 439.908)" fill="#374757"/><path id="Path_30677" data-name="Path 30677" d="M1105.926-439.908a.826.826,0,0,1,.831.826.832.832,0,0,1-.821.826.831.831,0,0,1-.836-.823A.827.827,0,0,1,1105.926-439.908Z" transform="translate(-1099.534 439.908)" fill="#374757"/><path id="Path_30678" data-name="Path 30678" d="M1071.255-439.909a.821.821,0,0,1,.81.844.825.825,0,0,1-.847.809.825.825,0,0,1-.8-.851A.821.821,0,0,1,1071.255-439.909Z" transform="translate(-1067.628 439.909)" fill="#374757"/><path id="Path_30679" data-name="Path 30679" d="M1036.473-439.908a.828.828,0,0,1,.831.814.832.832,0,0,1-.833.838.831.831,0,0,1-.825-.822A.826.826,0,0,1,1036.473-439.908Z" transform="translate(-1035.646 443.397)" fill="#374757"/><path id="Path_30680" data-name="Path 30680" d="M1105.926-439.908a.826.826,0,0,1,.831.826.832.832,0,0,1-.821.826.831.831,0,0,1-.836-.823A.827.827,0,0,1,1105.926-439.908Z" transform="translate(-1099.534 443.397)" fill="#374757"/><path id="Path_30681" data-name="Path 30681" d="M1071.255-439.909a.821.821,0,0,1,.81.844.825.825,0,0,1-.847.809.825.825,0,0,1-.8-.851A.821.821,0,0,1,1071.255-439.909Z" transform="translate(-1067.628 443.397)" fill="#374757"/></g></g></svg>' . $posted_on . '</span>'; // WPCS: XSS OK.

}
endif;

if ( ! function_exists( 'cookery_lite_posted_by' ) ) :
/**
 * Prints HTML with meta information for the current author.
 */
function cookery_lite_posted_by() {

    $ed_post_author = get_theme_mod( 'ed_post_author', false );
    if( $ed_post_author ) return false;

	$byline = sprintf(
		/* translators: %s: post author. */
		esc_html_x( '%s', 'post author', 'cookery-lite' ),
		'<span itemprop="name"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" itemprop="url"><img class="avatar" src="' . esc_url( get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => 40 ) ) ) . '" alt="' . esc_attr( get_the_author() ) . '" /> <b class="fn">' . esc_html( get_the_author() ) . '</b></a></span>' 
    );
	echo '<span class="byline" itemprop="author" itemscope itemtype="https://schema.org/Person">' . $byline . '</span>';
}
endif;

if( ! function_exists( 'cookery_lite_comment_count' ) ) :
/**
 * Comment Count
*/
function cookery_lite_comment_count(){
    if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="35.556"
                                            height="36.263" viewBox="0 0 35.556 36.263">
                <defs>
                    <filter id="a" x="0" y="0" width="35.556" height="36.263" filterUnits="userSpaceOnUse">
                        <feOffset dy="3" input="SourceAlpha" />
                        <feGaussianBlur stdDeviation="3" result="b" />
                        <feFlood flood-color="#e84e3b" flood-opacity="0.102" />
                        <feComposite operator="in" in2="b" />
                        <feComposite in="SourceGraphic" />
                    </filter>
                </defs>
                <g transform="translate(-867.5 -4569.5)">
                    <g transform="matrix(1, 0, 0, 1, 867.5, 4569.5)" filter="url(#a)">
                        <path
                            d="M14.191,128H2.365A2.574,2.574,0,0,0,0,130.365v7.1a2.316,2.316,0,0,0,2.365,2.365H3.548v4.73l4.73-4.73h5.913a2.638,2.638,0,0,0,2.365-2.365v-7.1A2.574,2.574,0,0,0,14.191,128Z"
                            transform="translate(9.5 -121.5)" fill="#fff" stroke="rgba(55,71,87,0.42)"
                            stroke-width="1" />
                    </g>
                    <path
                        d="M1036.824-439.908a1.181,1.181,0,0,1,1.185,1.161,1.186,1.186,0,0,1-1.187,1.2,1.184,1.184,0,0,1-1.176-1.172A1.177,1.177,0,0,1,1036.824-439.908Z"
                        transform="translate(-155.676 5020.165)" fill="#374757" />
                    <path
                        d="M1106.277-439.908a1.178,1.178,0,0,1,1.185,1.178,1.186,1.186,0,0,1-1.171,1.178,1.184,1.184,0,0,1-1.193-1.173A1.179,1.179,0,0,1,1106.277-439.908Z"
                        transform="translate(-217.195 5020.165)" fill="#374757" />
                    <path
                        d="M1071.613-439.909a1.171,1.171,0,0,1,1.155,1.2,1.177,1.177,0,0,1-1.207,1.153,1.177,1.177,0,0,1-1.146-1.214A1.171,1.171,0,0,1,1071.613-439.909Z"
                        transform="translate(-186.473 5020.166)" fill="#374757" />
                </g>
            </svg>';
		comments_popup_link(
			sprintf(
				wp_kses(
					/* translators: %s: post title */
					__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'cookery-lite' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
		echo '</span>';
	}    
}
endif;

if ( ! function_exists( 'cookery_lite_category' ) ) :
/**
 * Prints categories
 */
function cookery_lite_category(){
    $ed_cat_single  = get_theme_mod( 'ed_category', false );
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() && ! $ed_cat_single ) {
		$categories_list = get_the_category_list( esc_html__( ' ', 'cookery-lite' ) );
		if ( $categories_list ) {
			echo '<span class="post-cat" itemprop="about">' . $categories_list . '</span>';
		}
	}
}
endif;

if ( ! function_exists( 'cookery_lite_tag' ) ) :
/**
 * Prints tags
 */
function cookery_lite_tag(){
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		$tags_list = get_the_tag_list( '', ' ' );
		if ( $tags_list ) {
			/* translators: 1: list of tags. */
			printf( '<div class="tags" itemprop="about">' . esc_html__( '%1$sTags:%2$s %3$s', 'cookery-lite' ) . '</div>', '<span>', '</span>', $tags_list );
		}
	}
}
endif;

if( ! function_exists( 'cookery_lite_get_posts_list' ) ) :
/**
 * Returns Latest, Related & Popular Posts
*/
function cookery_lite_get_posts_list( $status ){
    global $post;
    
    $args = array(
        'post_type'           => 'post',
        'posts_status'        => 'publish',
        'ignore_sticky_posts' => true
    );
    
    switch( $status ){
        case 'latest':        
        $args['post_type']      = ( cookery_lite_is_delicious_recipe_activated() ) ? DELICIOUS_RECIPE_POST_TYPE : 'post';
        $args['posts_per_page'] = 3;
        $title                  = __( 'You Might Also Enjoy This...', 'cookery-lite' );
        $class                  = 'additional-post';
        $image_size             = 'cookery-lite-related';
        break;
        
        case 'related':
        $args['post_type']      = ( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE === get_post_type() ) ? DELICIOUS_RECIPE_POST_TYPE : 'post';
        $args['posts_per_page'] = 3;
        $args['post__not_in']   = array( $post->ID );
        $args['orderby']        = 'rand';
        $title                  = get_theme_mod( 'related_post_title', __( 'You may also like...', 'cookery-lite' ) );
        $class                  = 'additional-post';
        $image_size             = 'cookery-lite-related';
        if( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE === get_post_type() ) {
            $cats = get_the_terms( $post->ID, 'recipe-course' );
            if( !$cats ) return false;       
            $c = array();
            foreach( $cats as $cat ){
                $c[] = $cat->term_id; 
            }
            $args['tax_query']    = array( array( 'taxonomy' => 'recipe-course', 'terms' => $c ) );
        }else{
            $cats                   = get_the_category( $post->ID );        
            if( $cats ){
                $c = array();
                foreach( $cats as $cat ){
                    $c[] = $cat->term_id; 
                }
                $args['category__in'] = $c;
            }   
        }
        break;        
    }
    
    $qry = new WP_Query( $args );
    
    if( $qry->have_posts() ){ ?>    
        <div class="<?php echo esc_attr( $class ); ?>">
    		<?php 
            if( $title ) echo '<h3 class="title">' . esc_html( $title ) . '</h3>'; ?>
            <section class="section-grid">        
                <?php while( $qry->have_posts() ){ $qry->the_post(); ?>
                <article class="post">
                    <figure class="post-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php
                                if( has_post_thumbnail() ){
                                    the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );
                                }else{ 
                                    cookery_lite_get_fallback_svg( $image_size );//fallback
                                }
                            ?>
                        </a>
                        <?php if( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE == get_post_type() ) {cookery_lite_recipe_pinit();
                            cookery_lite_recipe_keywords(); 
                        } ?>
                    </figure>
                    <div class="content-wrap">
                        <?php if( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE == get_post_type() ) { ?>
                            <header class="entry-header">
                            	<?php the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
                            </header>
                            <footer class="item-footer">
                                <?php cookery_lite_prep_time(); ?>
                                <?php cookery_lite_difficulty_level(); ?>
                            </footer>
                        <?php }else{
                            echo '<header class="entry-header">';
                                the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
                                if( 'post'  == get_post_type() ) {
                                    echo '<div class="entry-meta">';
                                    cookery_lite_posted_on();
                                    echo '</div>';
                                }
                            echo '</header>';
                        } ?>
                    </div>
                </article>
                <?php } ?> 
            </section>   		
    	</div>
        <?php
    }
    wp_reset_postdata();
}
endif;

if( ! function_exists( 'cookery_lite_site_branding' ) ) :
/**
 * Site Branding
*/
function cookery_lite_site_branding( $mobile = false ){
    $site_title       = get_bloginfo( 'name' );
    $site_description = get_bloginfo( 'description', 'display' );
    $header_text      = get_theme_mod( 'header_text', 1 );

    if( has_custom_logo() || $site_title || $site_description || $header_text ) :
        if( has_custom_logo() && ( $site_title || $site_description ) && $header_text ) {
            $branding_class = ' has-image-text';
        }else{
            $branding_class = '';
        }?>
        <div class="site-branding<?php echo esc_attr( $branding_class ); ?>" itemscope itemtype="http://schema.org/Organization">  
            <div class="site-logo">
                <?php 
                if( function_exists( 'has_custom_logo' ) && has_custom_logo() ){
                    the_custom_logo();
                }  ?>
            </div>

            <?php 
            if( $site_title || $site_description ) :
                echo '<div class="site-title-wrap">';
                if( is_front_page() && ! $mobile ){ ?>
                    <h1 class="site-title" itemprop="name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1>
                    <?php 
                }else{ ?>
                    <p class="site-title" itemprop="name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url"><?php bloginfo( 'name' ); ?></a></p>
                <?php }
                
                $description = get_bloginfo( 'description', 'display' );
                if ( $description || is_customize_preview() ){ ?>
                    <p class="site-description" itemprop="description"><?php echo $description; ?></p>
                <?php }
                echo '</div>';
            endif; ?>
        </div>    
    <?php endif;
}
endif;

if( ! function_exists( 'cookery_lite_social_links' ) ) :
/**
 * Social Links 
*/
function cookery_lite_social_links( $echo = true ){ 

    $social_links = get_theme_mod( 'social_links', '' );
    $ed_social    = get_theme_mod( 'ed_social_links', false ); 
    
    if( $ed_social && $social_links && $echo ){ ?>
    <ul class="social-networks">
    	<?php 
        foreach( $social_links as $link ){
    	   if( $link['link'] ){ ?>
            <li>
                <a href="<?php echo esc_url( $link['link'] ); ?>" target="_blank" rel="nofollow noopener">
                    <i class="<?php echo esc_attr( $link['font'] ); ?>"></i>
                </a>
            </li>    	   
            <?php
            } 
        } 
        ?>
	</ul>
    <?php    
    }elseif( $ed_social && $social_links ){
        return true;
    }else{
        return false;
    }
    ?>
    <?php                                
}
endif;

if( ! function_exists( 'cookery_lite_header_search' ) ) :
/**
 * Form 
*/
function cookery_lite_header_search(){ ?>

    <div class="header-search">
        <button class="search-toggle" data-toggle-target=".search-modal" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="22.691" height="21.932" viewBox="0 0 22.691 21.932">
                <g id="Group_258" data-name="Group 258" transform="matrix(0.966, -0.259, 0.259, 0.966, -1515.787, 248.902)">
                    <g id="Ellipse_9" data-name="Ellipse 9" transform="translate(1525.802 162.18) rotate(-30)" fill="none" stroke="#6a6a6a" stroke-width="2.5">
                        <circle cx="7.531" cy="7.531" r="7.531" stroke="none"></circle>
                        <circle cx="7.531" cy="7.531" r="6.281" fill="none"></circle>
                    </g>
                    <path id="Path_4339" data-name="Path 4339" d="M0,0V7" transform="translate(1540.052 170.724) rotate(-30)" fill="none" stroke="#6a6a6a" stroke-linecap="round" stroke-width="2.5"></path>
                </g>
            </svg>
        </button>
        <div class="header-search-wrap search-modal cover-modal" data-modal-target-string=".search-modal">
            <div class="header-search-inner">
                <?php get_search_form(); ?>
                <button class="close" data-toggle-target=".search-modal" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field" aria-expanded="false"></button>
            </div>
        </div>
    </div>
    <?php
}
endif;

if( ! function_exists( 'cookery_lite_mobile_header' ) ) :
/**
 * Mobile Header
*/
function cookery_lite_mobile_header(){  
    $ed_cart   = get_theme_mod( 'ed_shopping_cart', true );
    $ed_search = get_theme_mod( 'ed_header_search', true );?>
    <div class="mobile-header">
        <div class="container">
            <?php cookery_lite_site_branding( true ); ?>
            <div class="mbl-header-right">
                <button class="toggle-btn" data-toggle-target=".main-menu-modal" data-toggle-body-class="showing-main-menu-modal" aria-expanded="false" data-set-focus=".close-main-nav-toggle">
                    <span class="toggle-bar"></span>
                    <span class="toggle-bar"></span>
                    <span class="toggle-bar"></span>
                </button>
                <?php if( cookery_lite_is_woocommerce_activated() && $ed_cart ) {
                    echo '<div class="header-cart">';
                    cookery_lite_wc_cart_count();
                    echo '</div>';
                } ?>
                <?php if( $ed_search ) 
                    echo '<div class="header-search">
                        <button class="search-toggle" data-toggle-target=".mob-search-modal" data-toggle-body-class="showing-mob-search-modal" data-set-focus=".mob-search-modal .search-field" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22.691" height="21.932" viewBox="0 0 22.691 21.932">
                                <g id="Group_258" data-name="Group 258" transform="matrix(0.966, -0.259, 0.259, 0.966, -1515.787, 248.902)">
                                    <g id="Ellipse_9" data-name="Ellipse 9" transform="translate(1525.802 162.18) rotate(-30)" fill="none" stroke="#6a6a6a" stroke-width="2.5">
                                        <circle cx="7.531" cy="7.531" r="7.531" stroke="none"></circle>
                                        <circle cx="7.531" cy="7.531" r="6.281" fill="none"></circle>
                                    </g>
                                    <path id="Path_4339" data-name="Path 4339" d="M0,0V7" transform="translate(1540.052 170.724) rotate(-30)" fill="none" stroke="#6a6a6a" stroke-linecap="round" stroke-width="2.5"></path>
                                </g>
                            </svg>
                        </button>
                        <div class="header-search-wrap mob-search-modal cover-modal" data-modal-target-string=".mob-search-modal">
                            <div class="header-search-inner">';
                                get_search_form();
                                echo '<button class="close" data-toggle-target=".mob-search-modal" data-toggle-body-class="showing-mob-search-modal" data-set-focus=".mob-search-modal .search-field" aria-expanded="false"></button>
                            </div>
                        </div>
                    </div>';
                ?>
                <div class="primary-menu-list main-menu-modal cover-modal" data-modal-target-string=".main-menu-modal">
                    <button class="close close-main-nav-toggle" data-toggle-target=".main-menu-modal" data-toggle-body-class="showing-main-menu-modal" aria-expanded="false" data-set-focus=".main-menu-modal"></button>
                    <div class="mobile-header-popup mobile-menu" aria-label="<?php esc_attr_e( 'Mobile', 'cookery-lite' ); ?>">
                        <div class="mbl-header-inner main-menu-modal">
                            <div class="mbl-header-mid">
                                <?php cookery_lite_primary_navigation(); ?>
                                <?php cookery_lite_secondary_navigation(); ?>
                            </div>
                            <div class="mbl-header-bottom">
                                <?php if( cookery_lite_social_links( false ) ) {
                                    echo '<div class="header-social">';
                                    cookery_lite_social_links();
                                    echo '</div>';
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
endif;

if( ! function_exists( 'cookery_lite_primary_navigation' ) ) :
/**
 * Primary Navigation.
*/
function cookery_lite_primary_navigation(){ ?>
    
    <nav id="site-navigation" class="main-navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
        <?php if( has_nav_menu( 'primary' ) ) : ?>
            <button class="toggle-btn">
                <span class="toggle-text">Menu</span>
                <span class="toggle-bar"></span>
                <span class="toggle-bar"></span>
                <span class="toggle-bar"></span>
            </button>
        <?php endif; ?>
        <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'menu_class'     => 'nav-menu',
                'fallback_cb'    => 'cookery_lite_primary_menu_fallback',
            ) );
        ?>
    </nav><!-- #site-navigation -->
    <?php
}
endif;

if( ! function_exists( 'cookery_lite_primary_menu_fallback' ) ) :
/**
 * Fallback for primary menu
*/
function cookery_lite_primary_menu_fallback(){
    if( current_user_can( 'manage_options' ) ){
        echo '<ul id="primary-menu" class="nav-menu">';
        echo '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Click here to add a menu', 'cookery-lite' ) . '</a></li>';
        echo '</ul>';
    }
}
endif;

if( ! function_exists( 'cookery_lite_secondary_navigation' ) ) :
/**
 * Secondary Navigation
*/
function cookery_lite_secondary_navigation(){ 
    ?>
    <nav id="secondary-nav" class="secondary-menu">
        <?php if( has_nav_menu( 'secondary' ) ) : ?>
            <button class="toggle-btn">
                <span class="toggle-bar"></span>
                <span class="toggle-bar"></span>
                <span class="toggle-bar"></span>
            </button>
        <?php endif; ?>
        <?php
            wp_nav_menu( array(
                'theme_location' => 'secondary',
                'menu_id'        => 'secondary-menu',
                'menu_class'     => 'nav-menu',
                'fallback_cb'    => 'cookery_lite_secondary_menu_fallback',
            ) );
        ?>
    </nav>
    <?php
}
endif;

if( ! function_exists( 'cookery_lite_secondary_menu_fallback' ) ) :
/**
 * Fallback for secondary menu
*/
function cookery_lite_secondary_menu_fallback(){
    if( current_user_can( 'manage_options' ) ){
        echo '<ul id="secondary-menu" class="nav-menu">';
        echo '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Click here to add a menu', 'cookery-lite' ) . '</a></li>';
        echo '</ul>';
    }
}
endif;

if( ! function_exists( 'cookery_lite_footer_navigation' ) ) :
/**
 * footer Navigation
*/
function cookery_lite_footer_navigation(){ ?>
    <nav class="footer-navigation">
        <?php
            wp_nav_menu( array(
                'theme_location' => 'footer',
                'menu_class'     => 'nav-menu',
                'menu_id'        => 'footer-menu',
                'fallback_cb'    => 'cookery_lite_footer_menu_fallback',
            ) );
        ?>
    </nav>
    <?php
}
endif;

if( ! function_exists( 'cookery_lite_footer_menu_fallback' ) ) :
/**
 * Fallback for secondary menu
*/
function cookery_lite_footer_menu_fallback(){
    if( current_user_can( 'manage_options' ) ){
        echo '<ul id="footer-menu" class="nav-menu">';
        echo '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Click here to add a menu', 'cookery-lite' ) . '</a></li>';
        echo '</ul>';
    }
}
endif;

if( ! function_exists( 'cookery_lite_sticky_navigation' ) ) :
/**
 * Sticky Navigation
*/
function cookery_lite_sticky_navigation(){ 
    if( current_user_can( 'manage_options' ) || has_nav_menu( 'primary' ) ) { ?>
        <nav id="sticky-navigation" class="main-navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
            <button class="toggle-btn">
                <span class="toggle-bar"></span>
                <span class="toggle-bar"></span>
                <span class="toggle-bar"></span>
            </button>
            <?php
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'menu_class'     => 'nav-menu',
                    'fallback_cb'    => 'cookery_lite_primary_menu_fallback',
                ) );
            ?>
        </nav><!-- #site-navigation -->
    <?php
    }
}
endif;

if( ! function_exists( 'cookery_lite_posts_per_page_count' ) ):
/**
*   Counts the Number of total posts in Archive, Search and Author
*/
function cookery_lite_posts_per_page_count(){
    global $wp_query;
    if( is_archive() || is_search() && $wp_query->found_posts > 0 ) {
        $posts_per_page = get_option( 'posts_per_page' );
        $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
        $start_post_number = 0;
        $end_post_number   = 0;

        if( $wp_query->found_posts > 0 && !( cookery_lite_is_woocommerce_activated() && is_shop() ) ):                
            $start_post_number = 1;
            if( $wp_query->found_posts < $posts_per_page  ) {
                $end_post_number = $wp_query->found_posts;
            }else{
                $end_post_number = $posts_per_page;
            }

            if( $paged > 1 ){
                $start_post_number = $posts_per_page * ( $paged - 1 ) + 1;
                if( $wp_query->found_posts < ( $posts_per_page * $paged )  ) {
                    $end_post_number = $wp_query->found_posts;
                }else{
                    $end_post_number = $paged * $posts_per_page;
                }
            }

            printf( esc_html__( '%1$s Showing:  %2$s - %3$s of %4$s Articles %5$s', 'cookery-lite' ), '<span class="showing-results">', absint( $start_post_number ), absint( $end_post_number ), esc_html( number_format_i18n( $wp_query->found_posts ) ), '</span>' );
        endif;
    }
}
endif; 

if( ! function_exists( 'cookery_lite_breadcrumb' ) ) :
/**
 * Breadcrumbs
*/
function cookery_lite_breadcrumb(){ 
    global $post;
    $post_page  = get_option( 'page_for_posts' ); //The ID of the page that displays posts.
    $show_front = get_option( 'show_on_front' ); //What to show on the front page    
    $home       = get_theme_mod( 'home_text', __( 'Home', 'cookery-lite' ) ); // text for the 'Home' link
    $delimiter  = '<span class="separator">/</span>';
    $before     = '<span class="current" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">'; // tag before the current crumb
    $after      = '</span>'; // tag after the current crumb
    
    if( get_theme_mod( 'ed_breadcrumb', true ) ){
        $depth = 1;
        echo '<div id="crumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
        <div class="container">
                <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a href="' . esc_url( home_url() ) . '" itemprop="item"><span itemprop="name">' . esc_html( $home ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
        
        if( is_home() ){ 
            $depth = 2;                       
            echo $before . '<a itemprop="item" href="'. esc_url( get_the_permalink() ) .'"><span itemprop="name">' . esc_html( single_post_title( '', false ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;            
        }elseif( is_category() ){  
            $depth = 2;          
            $thisCat = get_category( get_query_var( 'cat' ), false );            
            if( $show_front === 'page' && $post_page ){ //If static blog post page is set
                $p = get_post( $post_page );
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_permalink( $post_page ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $p->post_title ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
                $depth++;  
            }            
            if( $thisCat->parent != 0 ){
                $parent_categories = get_category_parents( $thisCat->parent, false, ',' );
                $parent_categories = explode( ',', $parent_categories );
                foreach( $parent_categories as $parent_term ){
                    $parent_obj = get_term_by( 'name', $parent_term, 'category' );
                    if( is_object( $parent_obj ) ){
                        $term_url  = get_term_link( $parent_obj->term_id );
                        $term_name = $parent_obj->name;
                        echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( $term_url ) . '"><span itemprop="name">' . esc_html( $term_name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
                        $depth++;
                    }
                }
            }
            echo $before . '<a itemprop="item" href="' . esc_url( get_term_link( $thisCat->term_id) ) . '"><span itemprop="name">' .  esc_html( single_cat_title( '', false ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;       
        }elseif( cookery_lite_is_woocommerce_activated() && ( is_product_category() || is_product_tag() ) ){ //For Woocommerce archive page
            $depth = 2;
            $current_term = $GLOBALS['wp_query']->get_queried_object();            
            if( wc_get_page_id( 'shop' ) ){ //Displaying Shop link in woocommerce archive page
                $_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
                if ( ! $_name ) {
                    $product_post_type = get_post_type_object( 'product' );
                    $_name = $product_post_type->labels->singular_name;
                }
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $_name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
                $depth++;
            }
            if( is_product_category() ){
                $ancestors = get_ancestors( $current_term->term_id, 'product_cat' );
                $ancestors = array_reverse( $ancestors );
                foreach ( $ancestors as $ancestor ) {
                    $ancestor = get_term( $ancestor, 'product_cat' );    
                    if ( ! is_wp_error( $ancestor ) && $ancestor ) {
                        echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_term_link( $ancestor ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $ancestor->name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
                        $depth++;
                    }
                }
            }
            echo $before . '<a itemprop="item" href="' . esc_url( get_term_link( $current_term->term_id ) ) . '"><span itemprop="name">' . esc_html( $current_term->name ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;
        }elseif( cookery_lite_is_woocommerce_activated() && is_shop() ){ //Shop Archive page
            $depth = 2;
            if( get_option( 'page_on_front' ) == wc_get_page_id( 'shop' ) ){
                return;
            }
            $_name    = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
            $shop_url = ( wc_get_page_id( 'shop' ) && wc_get_page_id( 'shop' ) > 0 )  ? get_the_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop' );
            if( ! $_name ){
                $product_post_type = get_post_type_object( 'product' );
                $_name             = $product_post_type->labels->singular_name;
            }
            echo $before . '<a itemprop="item" href="' . esc_url( $shop_url ) . '"><span itemprop="name">' . esc_html( $_name ) . '</span></a><meta itemprop="position" content="' . absint( $depth ) . '" />' . $after;
        }elseif( is_tag() ){ 
            $depth          = 2;
            $queried_object = get_queried_object();
            echo $before . '<a itemprop="item" href="' . esc_url( get_term_link( $queried_object->term_id ) ) . '"><span itemprop="name">' . esc_html( single_tag_title( '', false ) ) . '</span></a><meta itemprop="position" content="' . absint( $depth ). '" />'. $after;
        }elseif( is_author() ){  
            global $author;
            $depth    = 2;
            $userdata = get_userdata( $author );
            echo $before . '<a itemprop="item" href="' . esc_url( get_author_posts_url( $author ) ) . '"><span itemprop="name">' . esc_html( $userdata->display_name ) .'</span></a><meta itemprop="position" content="' . absint( $depth ) . '" />' . $after;     
        }elseif( is_search() ){ 
            $depth       = 2;
            $request_uri = $_SERVER['REQUEST_URI'];
            echo $before . '<a itemprop="item" href="'. esc_url( $request_uri ) . '"><span itemprop="name">' . sprintf( __( 'Search Results for "%s"', 'cookery-lite' ), esc_html( get_search_query() ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;        
        }elseif( is_day() ){            
            $depth = 2;
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_year_link( get_the_time( __( 'Y', 'cookery-lite' ) ) ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( get_the_time( __( 'Y', 'cookery-lite' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
            $depth++;
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_month_link( get_the_time( __( 'Y', 'cookery-lite' ) ), get_the_time( __( 'm', 'cookery-lite' ) ) ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( get_the_time( __( 'F', 'cookery-lite' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
            $depth++;
            echo $before . '<a itemprop="item" href="' . esc_url( get_day_link( get_the_time( __( 'Y', 'cookery-lite' ) ), get_the_time( __( 'm', 'cookery-lite' ) ), get_the_time( __( 'd', 'cookery-lite' ) ) ) ) . '"><span itemprop="name">' . esc_html( get_the_time( __( 'd', 'cookery-lite' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;        
        }elseif( is_month() ){            
            $depth = 2;
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_year_link( get_the_time( __( 'Y', 'cookery-lite' ) ) ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( get_the_time( __( 'Y', 'cookery-lite' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
            $depth++;
            echo $before . '<a itemprop="item" href="' . esc_url( get_month_link( get_the_time( __( 'Y', 'cookery-lite' ) ), get_the_time( __( 'm', 'cookery-lite' ) ) ) ) . '"><span itemprop="name">' . esc_html( get_the_time( __( 'F', 'cookery-lite' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;        
        }elseif( is_year() ){ 
            $depth = 2;
            echo $before .'<a itemprop="item" href="' . esc_url( get_year_link( get_the_time( __( 'Y', 'cookery-lite' ) ) ) ) . '"><span itemprop="name">'. esc_html( get_the_time( __( 'Y', 'cookery-lite' ) ) ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;  
        }elseif( is_single() && !is_attachment() ){   
            $depth = 2;         
            if( cookery_lite_is_woocommerce_activated() && 'product' === get_post_type() ){ //For Woocommerce single product
                if( wc_get_page_id( 'shop' ) ){ //Displaying Shop link in woocommerce archive page
                    $_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
                    if ( ! $_name ) {
                        $product_post_type = get_post_type_object( 'product' );
                        $_name = $product_post_type->labels->singular_name;
                    }
                    echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $_name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
                    $depth++;                    
                }           
                if( $terms = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) ) ){
                    $main_term = apply_filters( 'woocommerce_breadcrumb_main_term', $terms[0], $terms );
                    $ancestors = get_ancestors( $main_term->term_id, 'product_cat' );
                    $ancestors = array_reverse( $ancestors );
                    foreach ( $ancestors as $ancestor ) {
                        $ancestor = get_term( $ancestor, 'product_cat' );    
                        if ( ! is_wp_error( $ancestor ) && $ancestor ) {
                            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_term_link( $ancestor ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $ancestor->name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
                            $depth++;
                        }
                    }
                    echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_term_link( $main_term ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $main_term->name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
                    $depth++;
                }
                echo $before . '<a href="' . esc_url( get_the_permalink() ) . '" itemprop="item"><span itemprop="name">' . esc_html( get_the_title() ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;
            }elseif( get_post_type() != 'post' ){                
                $post_type = get_post_type_object( get_post_type() );                
                if( $post_type->has_archive == true ){// For CPT Archive Link                   
                   // Add support for a non-standard label of 'archive_title' (special use case).
                   $label = !empty( $post_type->labels->archive_title ) ? $post_type->labels->archive_title : $post_type->labels->name;
                   echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_post_type_archive_link( get_post_type() ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $label ) . '</span></a><meta itemprop="position" content="' . absint( $depth ) . '" />' . $delimiter . '</span>';
                   $depth++;    
                }
                echo $before . '<a href="' . esc_url( get_the_permalink() ) . '" itemprop="item"><span itemprop="name">' . esc_html( get_the_title() ) . '</span></a><meta itemprop="position" content="' . absint( $depth ) . '" />' . $after;
            }else{ //For Post                
                $cat_object       = get_the_category();
                $potential_parent = 0;
                
                if( $show_front === 'page' && $post_page ){ //If static blog post page is set
                    $p = get_post( $post_page );
                    echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_permalink( $post_page ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $p->post_title ) . '</span></a><meta itemprop="position" content="' . absint( $depth ) . '" />' . $delimiter . '</span>';  
                    $depth++; 
                }
                
                if( $cat_object ){ //Getting category hierarchy if any        
                    //Now try to find the deepest term of those that we know of
                    $use_term = key( $cat_object );
                    foreach( $cat_object as $key => $object ){
                        //Can't use the next($cat_object) trick since order is unknown
                        if( $object->parent > 0  && ( $potential_parent === 0 || $object->parent === $potential_parent ) ){
                            $use_term         = $key;
                            $potential_parent = $object->term_id;
                        }
                    }                    
                    $cat  = $cat_object[$use_term];              
                    $cats = get_category_parents( $cat, false, ',' );
                    $cats = explode( ',', $cats );
                    foreach ( $cats as $cat ) {
                        $cat_obj = get_term_by( 'name', $cat, 'category' );
                        if( is_object( $cat_obj ) ){
                            $term_url  = get_term_link( $cat_obj->term_id );
                            $term_name = $cat_obj->name;
                            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( $term_url ) . '"><span itemprop="name">' . esc_html( $term_name ) . '</span></a><meta itemprop="position" content="' . absint( $depth ). '" />' . $delimiter . '</span>';
                            $depth++;
                        }
                    }
                }
                echo $before . '<a itemprop="item" href="' . esc_url( get_the_permalink() ) . '"><span itemprop="name">' . esc_html( get_the_title() ) . '</span></a><meta itemprop="position" content="' . absint( $depth ) . '" />' . $after;   
            }        
        }elseif( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ){ //For Custom Post Archive
            $depth     = 2;
            $post_type = get_post_type_object( get_post_type() );
            if( get_query_var('paged') ){
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_post_type_archive_link( $post_type->name ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $post_type->label ) . '</span></a><meta itemprop="position" content="' . absint( $depth ) . '" />' . $delimiter . '/</span>';
                echo $before . sprintf( __('Page %s', 'cookery-lite'), get_query_var('paged') ) . $after; //@todo need to check this
            }else{
                echo $before . '<a itemprop="item" href="' . esc_url( get_post_type_archive_link( $post_type->name ) ) . '"><span itemprop="name">' . esc_html( $post_type->label ) . '</span></a><meta itemprop="position" content="' . absint( $depth ). '" />' . $after;
            }    
        }elseif( is_attachment() ){ 
            $depth = 2;           
            echo $before . '<a itemprop="item" href="' . esc_url( get_the_permalink() ) . '"><span itemprop="name">' . esc_html( get_the_title() ) . '</span></a><meta itemprop="position" content="' . absint( $depth ) . '" />' . $after;
        }elseif( is_page() && !$post->post_parent ){            
            $depth = 2;
            echo $before . '<a itemprop="item" href="' . esc_url( get_the_permalink() ) . '"><span itemprop="name">' . esc_html( get_the_title() ) . '</span></a><meta itemprop="position" content="' . absint( $depth ) . '" />' . $after;
        }elseif( is_page() && $post->post_parent ){            
            $depth       = 2;
            $parent_id   = $post->post_parent;
            $breadcrumbs = array();
            while( $parent_id ){
                $current_page  = get_post( $parent_id );
                $breadcrumbs[] = $current_page->ID;
                $parent_id     = $current_page->post_parent;
            }
            $breadcrumbs = array_reverse( $breadcrumbs );
            for ( $i = 0; $i < count( $breadcrumbs) ; $i++ ){
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_permalink( $breadcrumbs[$i] ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( get_the_title( $breadcrumbs[$i] ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $delimiter . '</span>';
                $depth++;
            }
            echo $before . '<a href="' . get_permalink() . '" itemprop="item"><span itemprop="name">' . esc_html( get_the_title() ) . '</span></a><meta itemprop="position" content="' . absint( $depth ) . '" /></span>' . $after;
        }elseif( is_404() ){
            $depth = 2;
            echo $before . '<a itemprop="item" href="' . esc_url( home_url() ) . '"><span itemprop="name">' . esc_html__( '404 Error - Page Not Found', 'cookery-lite' ) . '</span></a><meta itemprop="position" content="' . absint( $depth ). '" />' . $after;
        }
        
        if( get_query_var('paged') ) printf( __( ' (Page %s)', 'cookery-lite' ), get_query_var('paged') );
        
        echo '</div></div><!-- .crumbs -->';
        
    }                
}
endif;

if( ! function_exists( 'cookery_lite_theme_comment' ) ) :
/**
 * Callback function for Comment List *
 * 
 * @link https://codex.wordpress.org/Function_Reference/wp_list_comments 
 */
function cookery_lite_theme_comment( $comment, $args, $depth ){
	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}?>
	<<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
	
    <?php if ( 'div' != $args['style'] ) : ?>
    <div id="div-comment-<?php comment_ID() ?>" class="comment-body" itemscope itemtype="http://schema.org/UserComments">
	<?php endif; ?>
    	
        <footer class="comment-meta">
            <div class="comment-author vcard">
        	   <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
        	</div><!-- .comment-author vcard -->
        </footer>
        
        <div class="text-holder">
        	<div class="top">
                <div class="left">
                    <?php if ( $comment->comment_approved == '0' ) : ?>
                		<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'cookery-lite' ); ?></em>
                		<br />
                	<?php endif; ?>
                    <?php printf( __( '<b class="fn" itemprop="creator" itemscope itemtype="https://schema.org/Person">%s</b> <span class="says">says:</span>', 'cookery-lite' ), get_comment_author_link() ); ?>
                	<div class="comment-metadata commentmetadata">
                        <?php esc_html_e( 'Posted on', 'cookery-lite' );?>
                        <a href="<?php echo esc_url( htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ); ?>">
                    		<time itemprop="commentTime" datetime="<?php echo esc_attr( get_gmt_from_date( get_comment_date() . get_comment_time(), 'Y-m-d H:i:s' ) ); ?>"><?php printf( esc_html__( '%1$s at %2$s', 'cookery-lite' ), get_comment_date(),  get_comment_time() ); ?></time>
                        </a>
                	</div>
                </div>
                <div class="reply">
                    <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
            	</div>
            </div>            
            <div class="comment-content" itemprop="commentText"><?php comment_text(); ?></div>        
        </div><!-- .text-holder -->
        
	<?php if ( 'div' != $args['style'] ) : ?>
    </div><!-- .comment-body -->
	<?php endif;
}
endif;

if( ! function_exists( 'cookery_lite_sidebar' ) ) :
/**
 * Return sidebar layouts for pages/posts
*/
function cookery_lite_sidebar( $class = false ){
    global $post;
    $return = false;
    $page_layout = get_theme_mod( 'page_sidebar_layout', 'right-sidebar' ); //Default Layout Style for Pages
    $post_layout = get_theme_mod( 'post_sidebar_layout', 'right-sidebar' ); //Default Layout Style for Posts
    $layout      = get_theme_mod( 'layout_style', 'right-sidebar' ); //Default Layout Style for Styling Settings
    
    if( is_singular( array( 'page', 'post' ) ) ){         
        if( get_post_meta( $post->ID, '_cookery_lite_sidebar_layout', true ) ){
            $sidebar_layout = get_post_meta( $post->ID, '_cookery_lite_sidebar_layout', true );
        }else{
            $sidebar_layout = 'default-sidebar';
        }
        
        if( is_page() ){
            if( is_active_sidebar( 'sidebar' ) ){
                if( $sidebar_layout == 'no-sidebar' || ( $sidebar_layout == 'default-sidebar' && $page_layout == 'no-sidebar' ) ){
                    $return = $class ? 'full-width' : false;
                }elseif( $sidebar_layout == 'centered' || ( $sidebar_layout == 'default-sidebar' && $page_layout == 'centered' ) ){
                    $return = $class ? 'full-width centered' : false;
                }elseif( ( $sidebar_layout == 'default-sidebar' && $page_layout == 'right-sidebar' ) || ( $sidebar_layout == 'right-sidebar' ) ){
                    $return = $class ? 'rightsidebar' : 'sidebar';
                }elseif( ( $sidebar_layout == 'default-sidebar' && $page_layout == 'left-sidebar' ) || ( $sidebar_layout == 'left-sidebar' ) ){
                    $return = $class ? 'leftsidebar' : 'sidebar';
                }
            }else{
                $return = $class ? 'full-width' : false;
            }
        }elseif( is_single() ){
            if( is_active_sidebar( 'sidebar' ) ){
                if( $sidebar_layout == 'no-sidebar' || ( $sidebar_layout == 'default-sidebar' && $post_layout == 'no-sidebar' ) ){
                    $return = $class ? 'full-width' : false;
                }elseif( $sidebar_layout == 'centered' || ( $sidebar_layout == 'default-sidebar' && $post_layout == 'centered' ) ){
                    $return = $class ? 'full-width centered' : false;
                }elseif( ( $sidebar_layout == 'default-sidebar' && $post_layout == 'right-sidebar' ) || ( $sidebar_layout == 'right-sidebar' ) ){
                    $return = $class ? 'rightsidebar' : 'sidebar';
                }elseif( ( $sidebar_layout == 'default-sidebar' && $post_layout == 'left-sidebar' ) || ( $sidebar_layout == 'left-sidebar' ) ){
                    $return = $class ? 'leftsidebar' : 'sidebar';
                }
            }else{
                $return = $class ? 'full-width' : false;
            }
        }
    }elseif( cookery_lite_is_woocommerce_activated() && ( is_shop() || is_product_category() || is_product_tag() || get_post_type() == 'product' ) ){
        if( $layout == 'no-sidebar' ){
            $return = $class ? 'full-width' : false;
        }elseif( is_active_sidebar( 'shop-sidebar' ) ){            
            if( $class ){
                if( $layout == 'right-sidebar' ) $return = 'rightsidebar'; //With Sidebar
                if( $layout == 'left-sidebar' ) $return = 'leftsidebar';
            }         
        }else{
            $return = $class ? 'full-width' : false;
        } 
    }elseif( is_404() ){
        $return = $class ? 'full-width' : false;
    }else{
        if( $layout == 'no-sidebar' ){
            $return = $class ? 'full-width' : false;
        }elseif( is_active_sidebar( 'sidebar' ) ){            
            if( $class ){
                if( $layout == 'right-sidebar' ) $return = 'rightsidebar'; //With Sidebar
                if( $layout == 'left-sidebar' ) $return = 'leftsidebar';
            }else{
                $return = 'sidebar';    
            }                         
        }else{
            $return = $class ? 'full-width' : false;
        } 
    }    
    return $return; 
}
endif;

if( ! function_exists( 'cookery_lite_get_posts' ) ) :
/**
 * Fuction to list Custom Post Type
*/
function cookery_lite_get_posts( $post_type = 'post', $slug = false ){    
    $args = array(
    	'posts_per_page'   => -1,
    	'post_type'        => $post_type,
    	'post_status'      => 'publish',
    	'suppress_filters' => true 
    );
    $posts_array = get_posts( $args );
    
    // Initate an empty array
    $post_options = array();
    $post_options[''] = __( ' -- Choose -- ', 'cookery-lite' );
    if ( ! empty( $posts_array ) ) {
        foreach ( $posts_array as $posts ) {
            if( $slug ){
                $post_options[ $posts->post_title ] = $posts->post_title;
            }else{
                $post_options[ $posts->ID ] = $posts->post_title;    
            }
        }
    }
    return $post_options;
    wp_reset_postdata();
}
endif;

if( ! function_exists( 'cookery_lite_get_categories' ) ) :
/**
 * Function to list post categories in customizer options
*/
function cookery_lite_get_categories( $select = true, $taxonomy = 'category', $slug = false, $hide_empty = false ){    
    /* Option list of all categories */
    $categories = array();
    
    $args = array( 
        'hide_empty' => $hide_empty,
        'taxonomy'   => $taxonomy 
    );
    
    $catlists = get_terms( $args );
    if( $select ) $categories[''] = __( 'Choose Category', 'cookery-lite' );
    foreach( $catlists as $category ){
        if( $slug ){
            $categories[$category->slug] = $category->name;
        }else{
            $categories[$category->term_id] = $category->name;    
        }        
    }
    
    return $categories;
}
endif;

if( ! function_exists( 'cookery_lite_get_image_sizes' ) ) :
/**
 * Get information about available image sizes
 */
function cookery_lite_get_image_sizes( $size = '' ) {
 
    global $_wp_additional_image_sizes;
 
    $sizes = array();
    $get_intermediate_image_sizes = get_intermediate_image_sizes();
 
    // Create the full array with sizes and crop info
    foreach( $get_intermediate_image_sizes as $_size ) {
        if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
            $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
            $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
            $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
        } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
            $sizes[ $_size ] = array( 
                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
            );
        }
    } 
    // Get only 1 size if found
    if ( $size ) {
        if( isset( $sizes[ $size ] ) ) {
            return $sizes[ $size ];
        } else {
            return false;
        }
    }
    return $sizes;
}
endif;

if ( ! function_exists( 'cookery_lite_get_fallback_svg' ) ) :    
/**
 * Get Fallback SVG
*/
function cookery_lite_get_fallback_svg( $post_thumbnail ) {
    if( ! $post_thumbnail ){
        return;
    }
    
    $image_size = cookery_lite_get_image_sizes( $post_thumbnail );
     
    if( $image_size ){ ?>
        <div class="svg-holder">
            <svg class="fallback-svg" viewBox="0 0 <?php echo esc_attr( $image_size['width'] ); ?> <?php echo esc_attr( $image_size['height'] ); ?>" preserveAspectRatio="none">
                    <rect width="<?php echo esc_attr( $image_size['width'] ); ?>" height="<?php echo esc_attr( $image_size['height'] ); ?>" style="fill:#f2f2f2;"></rect>
            </svg>
        </div>
        <?php
    }
}
endif;

if ( ! function_exists( 'cookery_lite_comment_toggle' ) ):
/**
 * Function toggle comment section position
*/
function cookery_lite_comment_toggle(){
    $comment_postion = get_theme_mod( 'toggle_comments', false );

    if ( $comment_postion ) {
        $priority = 5;
    }else{
        $priority = 45;
    }
    return absint( $priority ) ;
}
endif;

if( ! function_exists( 'wp_body_open' ) ) :
/**
 * Fire the wp_body_open action.
 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
*/
function wp_body_open() {
	/**
	 * Triggered after the opening <body> tag.
    */
	do_action( 'wp_body_open' );
}
endif;

/**
 * Is Blossom Theme Toolkit active or not
*/
function cookery_lite_is_bttk_activated(){
    return class_exists( 'Blossomthemes_Toolkit' ) ? true : false;
}

/**
 * Is BlossomThemes Email Newsletters active or not
*/
function cookery_lite_is_btnw_activated(){
    return class_exists( 'Blossomthemes_Email_Newsletter' ) ? true : false;        
}

/**
 * Is BlossomThemes Social Feed active or not
*/
function is_btif_activated(){
    return class_exists( 'Blossomthemes_Instagram_Feed' ) ? true : false;
}

/**
 * Query WooCommerce activation
 */
function cookery_lite_is_woocommerce_activated() {
	return class_exists( 'woocommerce' ) ? true : false;
}

/**
 * Check if Delicious Recipe Plugin is installed
*/
function cookery_lite_is_delicious_recipe_activated(){
    return class_exists( 'WP_Delicious\DeliciousRecipes' ) ? true : false;
}

/**
 * Check if Contact Form 7 Plugin is installed
*/
function cookery_lite_is_cf7_activated(){
    return class_exists( 'WPCF7' ) ? true : false;
}

/**
 * Query Jetpack activation
*/
function cookery_lite_is_jetpack_activated( $gallery = false ){
	if( $gallery ){
        return ( class_exists( 'jetpack' ) && Jetpack::is_module_active( 'tiled-gallery' ) ) ? true : false;
	}else{
        return class_exists( 'jetpack' ) ? true : false;
    }           
}

/**
 * Checks if elementor is active or not
*/
function cookery_lite_is_elementor_activated(){
    return class_exists( 'Elementor\\Plugin' ) ? true : false; 
}

/**
 * Checks if elementor has override that particular page/post or not
*/
function cookery_lite_is_elementor_activated_post(){
    if( cookery_lite_is_elementor_activated() ){
        global $post;
        $post_id = $post->ID;
        return \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id ) ? true : false;
    }else{
        return false;
    }
}

/**
 * Checks if classic editor is active or not
*/
function cookery_lite_is_classic_editor_activated(){
    return class_exists( 'Classic_Editor' ) ? true : false; 
}

if ( ! function_exists( 'cookery_lite_static_cta_banner' ) ) :
/**
 * Static Cta Banner
*/
function cookery_lite_static_cta_banner() {  
    $banner_title     = get_theme_mod( 'banner_title' );
    $banner_subtitle  = get_theme_mod( 'banner_subtitle' );
    $button_one       = get_theme_mod( 'button_one' );
    $button_one_url   = get_theme_mod( 'button_one_url' );
    $button_two       = get_theme_mod( 'button_two' );
    $button_two_url   = get_theme_mod( 'button_two_url' );
    $button_one_new   = get_theme_mod( 'button_one_tab_new', false );
    $button_two_new   = get_theme_mod( 'button_two_tab_new', false );
    $static_banner_bg = get_theme_mod( 'static_banner_bg' );
    $target_one = $target_two = '';

    ?>
    <div id="banner_section" class="site-banner static-cta style-one<?php if( has_header_video() ) echo esc_attr( ' video-banner' ); ?>">
        <div class="item right" <?php if( $static_banner_bg ) { ?> style="background-image: url('<?php echo esc_url ( $static_banner_bg ); ?>');"<?php } ?>>
            <?php 
                echo '<div class="item-img">';
                the_custom_header_markup(); 
                echo '</div>';

                if( $banner_title || $banner_subtitle || ( $button_one && $button_one_url ) || ( $button_two && $button_two_url ) ){
                    echo '<div class="banner-caption">';
                        if( $banner_title ) echo '<h2 class="item-title">' . esc_html( $banner_title ) . '</h2>';
                        if( $banner_subtitle ) echo '<div class="item-desc">' . wp_kses_post( $banner_subtitle ) . '</div>';
                        if( ( $button_one && $button_one_url ) || ( $button_two && $button_two_url ) ) {
                            echo '<div class="button-wrap">';
                            if( $button_one && $button_one_url ) {
                                if( $button_one_new ) $target_one = ' target="_blank"';
                                echo '<a href="' . esc_url( $button_one_url ) . '" class="btn-readmore btn1"' . $target_one . '>' . esc_html( $button_one ) . '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689">
                                <g transform="translate(0.75 1.061)">
                                    <path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none"
                                        stroke="#232323" stroke-linecap="round" stroke-width="1.5"></path>
                                    <path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#232323"
                                        stroke-linecap="round" stroke-width="1.5"></path>
                                </g>
                            </svg></a>';                              
                            }
                            if( $button_two && $button_two_url ) {
                                if( $button_two_new ) $target_two = ' target="_blank"';
                                echo '<a href="' . esc_url( $button_two_url  ) . '" class="btn-readmore btn2"' . $target_two . '>' . esc_html( $button_two ) . '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689">
                                <g transform="translate(0.75 1.061)">
                                    <path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none"
                                        stroke="#232323" stroke-linecap="round" stroke-width="1.5"></path>
                                    <path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#232323"
                                        stroke-linecap="round" stroke-width="1.5"></path>
                                </g>
                            </svg></a>';
                            }  
                            echo '</div>';                            
                        }
                    echo '</div>';
                }

            ?>
        </div>
    </div>
<?php
}
endif;

if( ! function_exists( 'cookery_lite_slider_meta_contents' ) ):
/**
 * Slider Meta
*/
function cookery_lite_slider_meta_contents(){
    $show_date      = get_theme_mod( 'slider_show_date', true );
    
    if( cookery_lite_is_delicious_recipe_activated() && DELICIOUS_RECIPE_POST_TYPE == get_post_type() ) {
        the_title( '<h2 class="item-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        echo '<div class="item-meta">';
        cookery_lite_recipe_keywords();
        cookery_lite_prep_time();
        cookery_lite_difficulty_level();
        echo '</div>';
    }else{
        cookery_lite_category();
        the_title( '<h2 class="item-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );

        if( 'post' == get_post_type() && $show_date ) :
            echo '<div class="item-meta">';
                cookery_lite_posted_on();
            echo '</div>';
        endif;
    }
}
endif;