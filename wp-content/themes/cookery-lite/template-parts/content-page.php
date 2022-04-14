<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Cookery_Lite
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
        /**
         * Post Thumbnail
         * 
         * @hooked cookery_lite_post_thumbnail
        */
        do_action( 'cookery_lite_before_page_entry_content' );
    
        /**
         * Entry Content
         * 
         * @hooked cookery_lite_entry_content - 15
         * @hooked cookery_lite_entry_footer  - 20
        */
        do_action( 'cookery_lite_page_entry_content' );    
    ?>
</article><!-- #post-<?php the_ID(); ?> -->
