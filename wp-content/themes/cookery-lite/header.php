<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Cookery_Lite
 */
    /**
     * Doctype Hook
     * 
     * @hooked cookery_lite_doctype
    */
    do_action( 'cookery_lite_doctype' );
?>
<head itemscope itemtype="http://schema.org/WebSite">
	<?php 
    /**
     * Before wp_head
     * 
     * @hooked cookery_lite_head
    */
    do_action( 'cookery_lite_before_wp_head' );
    
    wp_head(); ?>
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

<?php
    wp_body_open();
    
    /**
     * Before Header
     * 
     * @hooked cookery_lite_page_start - 20 
    */
    do_action( 'cookery_lite_before_header' );
    
    /**
     * Header
     * 
     * @hooked cookery_lite_header              - 20     
    */
    do_action( 'cookery_lite_header' );
    
    /**
     * Before Content
     * 
     * @hooked cookery_lite_banner                - 15
     * @hooked cookery_lite_promotional_section   - 25
     * @hooked cookery_lite_about_section         - 30
    */
    do_action( 'cookery_lite_after_header' );
    
    /**
     * Content
     * 
     * @hooked cookery_lite_content_start
    */
    do_action( 'cookery_lite_content' );