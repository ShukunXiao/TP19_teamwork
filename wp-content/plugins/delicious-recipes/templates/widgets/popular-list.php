<?php
/**
 * The template for displaying recipe content in list layout for widgets.
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipes/widgets/popular-list.php.
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

global $recipe;
// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();
$img_size       = $global_toggles['enable_recipe_archive_image_crop'] ? 'recipe-feat-thumbnail' : 'full';
$img_size       = apply_filters( 'popular_list_img_size', $img_size );

?>
<li class="dr-mst-pop-wrap">
    <div class="dr-mst-pop-fig">
        <a href="<?php echo esc_url( $recipe->permalink ); ?>">
            <?php if( $recipe->thumbnail ) : 
                the_post_thumbnail( $img_size ); 
            else:
                delicious_recipes_get_fallback_svg( 'recipe-feat-thumbnail' );
            endif; ?>
        </a>
    </div>
    <div class="dr-mst-pop-details">
        <h3 class="dr-mst-pop-title">
            <a href="<?php echo esc_url( $recipe->permalink ); ?>">
                <?php echo esc_html( $recipe->name ); ?>
            </a>
        </h3>
        <div class="dr-mst-pop-meta">

            <?php if( $recipe->total_time ) : ?>
                <span class="dr-mst-pop-time">
                    <svg class="icon">
                        <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#time"></use>
                    </svg>
                    <span class="dr-mst-pop-timedsc">
                        <?php 
                            echo esc_html( $recipe->total_time );
                        ?>
                    </span>
                </span>
            <?php endif; ?>

            <?php if( $recipe->difficulty_level ) : ?>
                <span class="dr-mst-pop-diffic">
                    <svg class="icon">
                        <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#difficulty"></use>
                    </svg>
                    <span class="dr-mst-pop-difffdsc">
                        <?php echo esc_html( $recipe->difficulty_level ); ?>
                    </span>
                </span>
            <?php endif; ?>

        </div>
    </div>
</li>
<?php

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */