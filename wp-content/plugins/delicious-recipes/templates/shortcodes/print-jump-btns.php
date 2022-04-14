<?php
/**
 * The template for displaying recipe print and jump to recipe block.
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipes/print-recipe.php.
 *
 * HOWEVER, on occasion WP Delicious will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://wpdelicious.com/docs/template-structure/
 * @package Delicious_Recipes/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();
?>
    <div class="dr-buttons">
        <?php if ( $global_toggles['enable_jump_to_recipe'] ) : ?>
            <a href="#dr-recipe-meta-main" class="dr-btn-link dr-btn1 dr-smooth-scroll"><?php echo esc_html( $global_toggles['jump_to_recipe_lbl'] ); ?><svg class="icon"><use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#go-to"></use></svg></a>
        <?php endif; ?>

        <?php if ( $global_toggles['enable_jump_to_video'] ) : ?>
            <a href="#dr-video-gallery" class="dr-btn-link dr-btn1 dr-smooth-scroll"><i class="fas fa-play"></i><?php echo esc_html( $global_toggles['jump_to_video_lbl'] ); ?></a>
        <?php endif; ?>

        <?php 
            if ( $global_toggles['enable_print_recipe'] ) {
                delicious_recipes_get_template_part( 'recipe/print', 'btn' ); 
            }
        ?>
    </div>
<?php

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */