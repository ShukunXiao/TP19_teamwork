<?php
/**
 * Cookery Lite Widget Areas
 * 
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 * @package Cookery_Lite
 */

function cookery_lite_widgets_init(){    
    $sidebars = array(
        'sidebar'   => array(
            'name'        => __( 'Sidebar', 'cookery-lite' ),
            'id'          => 'sidebar', 
            'description' => __( 'Default Sidebar', 'cookery-lite' ),
        ),
        'promo' => array(
            'name'        => __( 'Promotional Section', 'cookery-lite' ),
            'id'          => 'promo', 
            'description' => __( 'Add "Blossom: Image Text" widget for promotional section. The recommended image size for this section is 366px by 231px.', 'cookery-lite' ),
        ),
        'about' => array(
            'name'        => __( 'About Section', 'cookery-lite' ),
            'id'          => 'about', 
            'description' => __( 'Add "Blossom: Featured Page Widget" for about section. The recommended image size for this section is 952px by 708px.', 'cookery-lite' ),
        ),
        'cta' => array(
            'name'        => __( 'Call To Action Section', 'cookery-lite' ),
            'id'          => 'cta', 
            'description' => __( 'Add "Blossom: Call To Action" widget for call to action section.', 'cookery-lite' ),
        ),
        'client' => array(
            'name'        => __( 'Client Section', 'cookery-lite' ),
            'id'          => 'client', 
            'description' => __( 'Add "Blossom: Client Logo Widget" for client section. The recommended logo size is 330x190px. Please upload logo of atleast width 330px to avoid cropping.', 'cookery-lite' ),
        ),
        'footer-one'=> array(
            'name'        => __( 'Footer One', 'cookery-lite' ),
            'id'          => 'footer-one', 
            'description' => __( 'Add footer one widgets here.', 'cookery-lite' ),
        ),
        'footer-two'=> array(
            'name'        => __( 'Footer Two', 'cookery-lite' ),
            'id'          => 'footer-two', 
            'description' => __( 'Add footer two widgets here.', 'cookery-lite' ),
        ),
        'footer-three'=> array(
            'name'        => __( 'Footer Three', 'cookery-lite' ),
            'id'          => 'footer-three', 
            'description' => __( 'Add footer three widgets here.', 'cookery-lite' ),
        ),
        'footer-four'=> array(
            'name'        => __( 'Footer Four', 'cookery-lite' ),
            'id'          => 'footer-four', 
            'description' => __( 'Add footer four widgets here.', 'cookery-lite' ),
        )
    );
    
    foreach( $sidebars as $sidebar ){
        register_sidebar( array(
    		'name'          => esc_html( $sidebar['name'] ),
    		'id'            => esc_attr( $sidebar['id'] ),
    		'description'   => esc_html( $sidebar['description'] ),
    		'before_widget' => '<section id="%1$s" class="widget %2$s">',
    		'after_widget'  => '</section>',
    		'before_title'  => '<h2 class="widget-title" itemprop="name">',
    		'after_title'   => '</h2>',
    	) );
    }
}
add_action( 'widgets_init', 'cookery_lite_widgets_init' );