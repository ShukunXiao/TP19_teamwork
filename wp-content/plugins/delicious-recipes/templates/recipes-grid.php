<?php
/**
 * The template for displaying recipe content in archive.
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipes/recipes-grid.php.
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

$img_size = $global_toggles['enable_recipe_archive_image_crop'] ? 'recipe-archive-grid' : 'full';
$img_size = apply_filters( 'recipes_grid_img_size', $img_size );
$h_tag_open  = isset( $tax_page ) && $tax_page ? '<h3 class="dr-archive-list-title" itemprop="name">' 
                : '<h2 class="dr-archive-list-title" itemprop="name">';
$h_tag_close = isset( $tax_page ) && $tax_page ? '</h3>' : '</h2>';
$disable_wishlist = isset( $disable_wishlist ) && $disable_wishlist ? true : false;

?>
<div class="dr-archive-single" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
    <figure>
        <a href="<?php echo esc_url( $recipe->permalink ); ?>">
            <?php if( $recipe->thumbnail ) : 
                the_post_thumbnail( $img_size ); 
            else:
                delicious_recipes_get_fallback_svg( 'recipe-archive-grid' );
            endif; ?>
        </a>
        <?php if ( $recipe->thumbnail && $global_toggles['enable_pintit'] ) : ?>
            <span class="post-pinit-button">
                <a data-pin-do="buttonPin" href="https://www.pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>/&media=<?php echo esc_url( $recipe->thumbnail ); ?>&description=So%20delicious!" data-pin-custom="true">
                    <img src="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>/assets/images/pinit-sm.png" alt="pinit">
                </a>
            </span>
        <?php endif; ?>

        <?php 
            /**
             * Recipe Wishlist button
             */
            do_action( 'delicious_recipes_wishlist_button', $disable_wishlist );
        ?>

        <?php
            /**
             * Recipe Badges
             */
            do_action( 'delicious_recipes_badges' );
        ?>

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
    </figure>
    <div class="dr-archive-details">
        <?php echo $h_tag_open; ?>
            <a itemprop="url" href="<?php echo esc_url( $recipe->permalink ); ?>">
                <?php echo esc_html( $recipe->name ); ?>
            </a>
        <?php echo $h_tag_close; ?>

        <?php if( isset( $position ) && ! empty( $position ) ) : ?>
            <meta itemprop="position" content="<?php echo $position; ?>"/>
        <?php endif; ?>

        <div class="dr-entry-meta">
        <?php if( $recipe->total_time ) : ?>
            <span class="dr-time">
                <svg class="icon">
                    <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#time"></use>
                </svg>
                <span class="dr-meta-title"><?php echo sprintf( '%1$s', esc_html( $recipe->total_time ) ); ?></span>
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
    </div>
</div>
<?php
