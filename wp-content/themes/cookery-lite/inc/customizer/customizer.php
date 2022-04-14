<?php
/**
 * Cookery Lite Theme Customizer
 *
 * @package Cookery_Lite
 */

/**
 * Requiring customizer panels & sections
*/
$cookery_lite_sections     = array( 'info', 'site', 'appearance', 'layout', 'home', 'general', 'footer' );

foreach( $cookery_lite_sections as $cookery_lite_section ){
    require get_template_directory() . '/inc/customizer/' . $cookery_lite_section . '.php';
}

/**
 * Sanitization Functions
*/
require get_template_directory() . '/inc/customizer/sanitization-functions.php';

/**
 * Active Callbacks
*/
require get_template_directory() . '/inc/customizer/active-callback.php';

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function cookery_lite_customize_preview_js() {
	wp_enqueue_script( 'cookery-lite-customizer', get_template_directory_uri() . '/inc/js/customizer.js', array( 'customize-preview' ), COOKERY_LITE_THEME_VERSION, true );
}
add_action( 'customize_preview_init', 'cookery_lite_customize_preview_js' );

function cookery_lite_customize_script(){
    $array = array(
        'home'    => get_permalink( get_option( 'page_on_front' ) ),
    );
    
    wp_enqueue_style( 'cookery-lite-customize', get_template_directory_uri() . '/inc/css/customize.css', array(), COOKERY_LITE_THEME_VERSION );
    wp_enqueue_script( 'cookery-lite-customize', get_template_directory_uri() . '/inc/js/customize.js', array( 'jquery', 'customize-controls' ), COOKERY_LITE_THEME_VERSION, true );
    wp_localize_script( 'cookery-lite-customize', 'cookery_lite_cdata', $array );

    wp_localize_script( 'cookery-lite-repeater', 'cookery_lite_customize',
        array(
            'nonce' => wp_create_nonce( 'cookery_lite_customize_nonce' )
        )
    );
}
add_action( 'customize_controls_enqueue_scripts', 'cookery_lite_customize_script' );

/*
 * Notifications in customizer
 */
require get_template_directory() . '/inc/customizer-plugin-recommend/plugin-install/class-plugin-install-helper.php';

require get_template_directory() . '/inc/customizer-plugin-recommend/plugin-install/class-plugin-recommend.php';