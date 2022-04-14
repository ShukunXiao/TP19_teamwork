<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Cookery_Lite
 */
    
    /**
     * After Content
     * 
     * @hooked cookery_lite_content_end               - 20
     * @hooked cookery_lite_featured_recipe_section   - 35
     * @hooked cookery_lite_client_section            - 45
     * @hooked cookery_lite_footer_newsletter_section - 50
     * @hooked cookery_lite_instagram_section         - 55
    */
    do_action( 'cookery_lite_before_footer' );
    
    /**
     * Footer
     * 
     * @hooked cookery_lite_footer_start  - 20
     * @hooked cookery_lite_footer_top    - 30
     * @hooked cookery_lite_footer_bottom - 40
     * @hooked cookery_lite_footer_end    - 50
    */
    do_action( 'cookery_lite_footer' );
    
    /**
     * After Footer
     * 
     * @hooked cookery_lite_page_end    - 20
    */
    do_action( 'cookery_lite_after_footer' );

    wp_footer(); ?>

</body>
</html>
