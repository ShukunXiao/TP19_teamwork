<?php
/**
 * Recipe page tags template
 * 
 * @package Delicious_Recipes
 */
global $recipe;

// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();

if ( ! empty( $recipe->tags ) && $global_toggles['enable_file_under'] ) :
    ?>
        <div class="dr-tags">
            <span class="dr-meta-title"><?php echo esc_html( $global_toggles['file_under_lbl'] ); ?></span>
            <?php the_terms( $recipe->ID, 'recipe-tag', '', '', '' ); ?>
        </div>
    <?php 
endif;
