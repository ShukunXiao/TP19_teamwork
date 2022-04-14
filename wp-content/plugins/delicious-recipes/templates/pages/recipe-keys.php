<?php
/**
 * Template Name: Recipe Keys.
 */
get_header();
$recipe_keys_terms = get_terms( array(
    'taxonomy'   => 'recipe-key',
    'hide_empty' => true,
) );
?>
<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="dr-recipe-post-wrap">
            <?php 
                if ( ! is_wp_error( $recipe_keys_terms ) && ! empty( $recipe_keys_terms ) ) {
                    /**
                     * Get taxonomy terms search box.
                     */
                    delicious_recipes_get_template( 'pages/taxonomy/terms-box.php', [ 'terms' => $recipe_keys_terms ] );

                    /**
                     * Get terms slider template
                     */
                    delicious_recipes_get_template( 'pages/taxonomy/terms-carousal.php', [ 'terms' => $recipe_keys_terms ] );
                } else {
                    esc_html_e( 'Terms not found for recipe keys.', 'delicious-recipes' );
                }
            ?>
        </div>
    </main>
</div><!-- #primary -->
<?php

get_sidebar();
get_footer();
