<?php
/**
 * Cookery Lite Customizer Partials
 *
 * @package Cookery_Lite
 */

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function cookery_lite_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function cookery_lite_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

if( ! function_exists( 'cookery_lite_get_slider_readmore' ) ) :
/**
 * Slider Read More
*/
function cookery_lite_get_slider_readmore(){
    return esc_html( get_theme_mod( 'slider_readmore', __( 'Continue Reading', 'cookery-lite' ) ) );
}
endif;

if( ! function_exists( 'cookery_lite_get_banner_title' ) ) :
/**
 * Banner Title
*/
function cookery_lite_get_banner_title(){
    return esc_html( get_theme_mod( 'banner_title' ) );
}
endif;

if( ! function_exists( 'cookery_lite_get_banner_sub_title' ) ) :
/**
 * Banner Subtitle
*/
function cookery_lite_get_banner_sub_title(){
    return wp_kses_post( wpautop( get_theme_mod( 'banner_subtitle' ) ) );
}
endif;

if( ! function_exists( 'cookery_lite_get_banner_button_one' ) ) :
/**
 * Banner One
*/
function cookery_lite_get_banner_button_one(){
    return esc_html( get_theme_mod( 'button_one' ) );
}
endif;

if( ! function_exists( 'cookery_lite_get_banner_button_two' ) ) :
/**
 * Banner Two
*/
function cookery_lite_get_banner_button_two(){
    return esc_html( get_theme_mod( 'button_two' ) );
}
endif;

if( ! function_exists( 'cookery_lite_get_feature_recipe_title' ) ) :
/**
 * Featured Recipe Title
*/
function cookery_lite_get_feature_recipe_title(){
    return esc_html( get_theme_mod( 'feature_recipe_title', __( 'Featured Recipes', 'cookery-lite' ) ) );
}
endif;

if( ! function_exists( 'cookery_lite_get_feature_recipe_subtitle' ) ) :
/**
 * Featured Recipe Content
*/
function cookery_lite_get_feature_recipe_subtitle(){
    return esc_html( get_theme_mod( 'feature_recipe_subtitle' ) );
}
endif;


if( ! function_exists( 'cookery_lite_get_read_more' ) ) :
/**
 * Display blog readmore button
*/
function cookery_lite_get_read_more(){
    return get_theme_mod( 'read_more_text', __( 'Read More', 'cookery-lite' ) );    
}
endif;

if( ! function_exists( 'cookery_lite_get_related_title' ) ) :
/**
 * Display blog readmore button
*/
function cookery_lite_get_related_title(){
    return get_theme_mod( 'related_post_title', __( 'You may also like...', 'cookery-lite' ) );
}
endif;

if( ! function_exists( 'cookery_lite_get_footer_copyright' ) ) :
/**
 * Footer Copyright
*/
function cookery_lite_get_footer_copyright(){
    $copyright = get_theme_mod( 'footer_copyright' );
    echo '<span class="copyright">';
    if( $copyright ){
        echo wp_kses_post( $copyright );
    }else{
        esc_html_e( '&copy; Copyright ', 'cookery-lite' );
        echo date_i18n( esc_html__( 'Y', 'cookery-lite' ) );
        echo ' <a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>. ';
        esc_html_e( 'All Rights Reserved. ', 'cookery-lite' );
    }
    echo '</span>'; 
}
endif;