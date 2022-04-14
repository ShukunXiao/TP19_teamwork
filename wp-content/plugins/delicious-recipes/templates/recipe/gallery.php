<?php
/**
 * Recipe single page gallery images.
 */
global $recipe;

// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();

// Check for images.
if ( ! isset( $recipe->thumbnail_id ) || empty( $recipe->thumbnail_id ) || ! $global_toggles['enable_recipe_featured_image'] ) {
    return;
}

// Image size.
$img_size = $global_toggles['enable_recipe_image_crop'] ? 'recipe-feat-gallery' : 'full';

?>
<figure class="dr-feature-image">
    <?php
        if ( has_post_thumbnail() ) {
            the_post_thumbnail( $img_size ); 
        } else {
            echo wp_get_attachment_image( $recipe->thumbnail_id, $img_size );
        }
    ?>
    <?php if( $global_toggles['enable_pintit'] ) : ?>
        <span class="post-pinit-button">
            <a data-pin-do="buttonPin" href="https://www.pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>/&media=<?php echo esc_url( $recipe->thumbnail ); ?>&description=So%20delicious!" data-pin-custom="true">
                <img src="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>/assets/images/pinit-sm.png" alt="pinit">
            </a>
        </span>
    <?php endif; ?>
    <?php if ( isset( $recipe->image_gallery ) && ! empty( $recipe->image_gallery ) ) : ?>
        <a type="button" class="view-gallery-btn del-recipe-gallery-link" data-lg-settings="<?php echo esc_attr( wp_json_encode( $recipe->image_gallery ) ); ?>">
            <b><?php echo esc_html__( 'View Gallery', 'delicious-recipes' ); ?></b>
            <span><?php 
                /* translators: %1$s: gallery images count */
                echo sprintf( _nx( '%1$s photo', '%1$s photos', count( $recipe->image_gallery ), 'gallery images count', 'delicious-recipes' ), number_format_i18n( count( $recipe->image_gallery ) ) ); 
            ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14.796" height="10.354" viewBox="0 0 14.796 10.354"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l4.117,4.116-4.117,4.116" transform="translate(-7811.241 1126.021)" fill="none" stroke="#374757" stroke-linecap="round" stroke-width="1.5"/><path d="M6555.283-354.415h-12.624" transform="translate(-6542.659 358.532)" fill="none" stroke="#374757" stroke-linecap="round" stroke-width="1.5"/></g></svg>
        </a>
    <?php endif; ?>
</figure>
<?php
