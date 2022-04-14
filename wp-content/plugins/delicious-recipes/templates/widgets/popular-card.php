<?php
/**
 * The template for displaying recipe content in card layout for widgets.
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipes/widgets/popular-card.php.
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

$img_size = $global_toggles['enable_recipe_archive_image_crop'] ? 'recipe-feat-tall' : 'full';
$img_size = apply_filters( 'popular_card_img_size', $img_size );

?>
<li>
    <div class="dr-fav-recipe-fig">
        <a href="<?php echo esc_url( $recipe->permalink ); ?>">
            <?php if( $recipe->thumbnail ) : 
                the_post_thumbnail( $img_size ); 
            else:
                delicious_recipes_get_fallback_svg( 'recipe-feat-tall' );
            endif; ?>
        </a>
        <?php if ( ! empty( $recipe->recipe_keys ) ) : ?>
            <span class="dr-category">
                <?php
                    foreach( $recipe->recipe_keys as $recipe_key ) {
                        $key              = get_term_by( 'name', $recipe_key, 'recipe-key' );
                        $recipe_key_metas = get_term_meta( $key->term_id, 'dr_taxonomy_metas', true );
                        $key_svg          = isset( $recipe_key_metas['taxonomy_svg'] ) ? $recipe_key_metas['taxonomy_svg'] : '';
                ?>
                <a href="<?php echo esc_url( get_term_link( $key, 'recipe-key' ) ); ?>" title="<?php echo esc_attr( $recipe_key ); ?>">
                <?php delicious_recipes_get_tax_icon( $key ); ?>
                    <span class="cat-name"><?php echo esc_attr( $recipe_key ); ?></span>
                </a>
                <?php } ?>
            </span>
        <?php endif; ?>
    </div>

    <header class="dr-fav-header">
        <h3 class="dr-fav-recipe-title">
            <a href="<?php echo esc_url( $recipe->permalink ); ?>"><?php echo esc_html( $recipe->name ); ?></a>
        </h3>
        <div class="dr-entry-meta">
        <?php if( $recipe->total_time ) : ?>
            <span class="dr-time">
                <svg class="icon">
                    <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#time"></use>
                </svg>
                <span class="dr-meta-title"><?php echo esc_html( $recipe->total_time ); ?></span>
            </span>
        <?php endif; ?>
        <?php if( $recipe->difficulty_level ) : ?>    
            <span class="dr-level">
                <svg class="icon">
                    <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#difficulty"></use>
                </svg>
                <span class="dr-meta-title"><?php echo esc_html( $recipe->difficulty_level ); ?></span>
            </span>
        <?php endif; ?>
        </div>
    </header>
</li>
<?php

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */