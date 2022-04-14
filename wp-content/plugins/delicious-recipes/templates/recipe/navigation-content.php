<?php
/**
 * Recipe navigation block content.
 */

$recipe      = get_post( $recipe_id );
$recipe_meta = delicious_recipes_get_recipe( $recipe );

// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();

$img_size = $global_toggles['enable_recipe_archive_image_crop'] ? 'recipe-feat-gallery' : 'full';
$img_size = apply_filters( 'recipes_navigation_img_size', $img_size );

?>
<a class="next-recipe" href="<?php echo esc_url( get_permalink( $recipe_id ) ); ?>" rel="<?php echo esc_attr( $a_rel ); ?>">
    <article>
        <div class="dr-recipe-fig">
            <?php if( has_post_thumbnail( $recipe_id ) ) : 
                echo get_the_post_thumbnail( $recipe_id, $img_size ); 
            else:
                delicious_recipes_get_fallback_svg( 'recipe-feat-gallery' );
            endif; ?>
            <?php if ( ! empty( $recipe_meta->recipe_keys ) ) : ?>
                <span class="dr-category">
                    <?php
                        foreach( $recipe_meta->recipe_keys as $recipe_key ) {
                            $key              = get_term_by( 'name', $recipe_key, 'recipe-key' );
                            $recipe_key_metas = get_term_meta( $key->term_id, 'dr_taxonomy_metas', true );
                            $key_svg          = isset( $recipe_key_metas['taxonomy_svg'] ) ? $recipe_key_metas['taxonomy_svg'] : '';
                    ?>
                    <span>
                        <span class="dr-svg-icon"><?php delicious_recipes_get_tax_icon( $key ); ?></span>
                        <span class="cat-name"><?php echo esc_attr( $recipe_key ); ?></span>
                    </span>
                    <?php } ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="dr-recipe-details">
            <h3 class="dr-recipe-title"><?php echo esc_html( $title ); ?></h3>
            <div class="dr-recipe-meta">
                <?php if( $recipe_meta->total_time ) : ?>
                    <span class="dr-recipe-time">
                        <svg class="icon">
                            <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#time"></use>
                        </svg>
                        <span class="dr-meta-title"><?php echo esc_html( $recipe_meta->total_time ); ?></span>
                    </span>
                <?php endif; ?>
                
                <?php if( $recipe_meta->difficulty_level ) : ?>
                    <span class="dr-recipe-diffic">
                        <svg class="icon">
                            <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#difficulty"></use>
                        </svg>
                        <span class="dr-meta-title"><?php echo esc_html($recipe_meta->difficulty_level ); ?></span>
                    </span>
                <?php endif; ?>
            </div>
            <span class="meta-nav">
                <?php echo esc_html( $nav_text ); ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="14.796" height="10.354" viewBox="0 0 14.796 10.354"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l4.117,4.116-4.117,4.116" transform="translate(-7811.241 1126.021)" fill="none" stroke="#374757" stroke-linecap="round" stroke-width="1.5"/><path d="M6555.283-354.415h-12.624" transform="translate(-6542.659 358.532)" fill="none" stroke="#374757" stroke-linecap="round" stroke-width="1.5"/></g></svg>
            </span>
        </div>
    </article>
</a>
        