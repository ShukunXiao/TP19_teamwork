<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Cookery_Lite
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

    		<?php
    		while ( have_posts() ) : the_post();

    			get_template_part( 'template-parts/content', 'single' );

    		endwhile; // End of the loop.
    		?>

		</main><!-- #main -->
        
        <?php
        /**
         * @hooked cookery_lite_navigation           - 15
         * @hooked cookery_lite_author               - 25
         * @hooked cookery_lite_newsletter           - 30
         * @hooked cookery_lite_related_posts        - 35
         * @hooked cookery_lite_comment              - 45
        */
        do_action( 'cookery_lite_after_post_content' );
        ?>
        
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
