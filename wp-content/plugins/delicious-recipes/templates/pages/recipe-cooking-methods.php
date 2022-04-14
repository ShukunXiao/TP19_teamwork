<?php
/**
 * Template Name: Recipe Cooking Methods.
 */
get_header();
$recipe_cook_methods_terms = get_terms( array(
    'taxonomy'   => 'recipe-cooking-method',
    'hide_empty' => true,
) );
?>
<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="dr-recipe-post-wrap">
            <?php 
                if ( ! is_wp_error( $recipe_cook_methods_terms ) && ! empty( $recipe_cook_methods_terms ) ) {
                    /**
                     * Get taxonomy terms search box.
                     */
                    delicious_recipes_get_template( 'pages/taxonomy/terms-box.php', [ 'terms' => $recipe_cook_methods_terms ] );

                    /**
                     * Get terms slider template
                     */
                    delicious_recipes_get_template( 'pages/taxonomy/terms-carousal.php', [ 'terms' => $recipe_cook_methods_terms ] );
                } else {
                    esc_html_e( 'Terms not found for recipe cooking methods.', 'delicious-recipes' );
                }
            ?>
        </div>
    </main>
</div><!-- #primary -->
<?php

get_sidebar();
get_footer();
