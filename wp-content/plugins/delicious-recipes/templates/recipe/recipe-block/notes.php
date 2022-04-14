<?php
/**
 * Notes template
 */
global $recipe;

// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();

    if ( ! empty( $recipe->notes ) && $global_toggles['enable_notes'] ) :
        ?>
            <div class="dr-note">
                <h3 class="dr-title"><?php echo esc_html( $global_toggles['notes_lbl'] ); ?></h3>
                <?php echo wp_kses_post( $recipe->notes ); ?>
            </div>
        <?php
    endif;

    if ( ! empty( $recipe->keywords ) && $global_toggles['enable_keywords'] ) :
        ?>
            <div class="dr-keywords">
                <span class="dr-meta-title"><?php echo esc_html( $global_toggles['keywords_lbl'] ); ?>:</span>
                <?php echo wp_kses_post( $recipe->keywords ); ?>
            </div>
        <?php
    endif;