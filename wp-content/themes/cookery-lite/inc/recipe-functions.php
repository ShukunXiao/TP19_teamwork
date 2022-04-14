<?php
/**
 * Delicious Recipes Functions.
 *
 * @package Cookery_Lite
 */

if( ! function_exists( 'cookery_lite_recipe_keywords' ) ) :
/**
 * Recipe Keys.
 */
function cookery_lite_recipe_keywords(){
    global $recipe;
    if ( ! empty( $recipe->recipe_keys ) ) : ?>
        <span class="dr-category">
            <?php
            foreach( $recipe->recipe_keys as $recipe_key ) {
                $key              = get_term_by( 'name', $recipe_key, 'recipe-key' );
                $recipe_key_metas = get_term_meta( $key->term_id, 'dr_taxonomy_metas', true );
                $key_svg          = isset( $recipe_key_metas['taxonomy_svg'] ) ? $recipe_key_metas['taxonomy_svg'] : ''; 
                ?>
                <a href="<?php echo esc_url( get_term_link( $key, 'recipe-key' ) ); ?>" title="<?php echo esc_attr( $recipe_key ); ?>">
                    <?php delicious_recipes_get_tax_icon( $key ); ?>
                    <span class="cat-name"><?php echo esc_html( $recipe_key ); ?></span>
                </a>
            <?php } ?>
        </span>
    <?php endif;
}
endif;

if( ! function_exists( 'cookery_lite_prep_time' ) ) :
/**
 * Prep Time.
 */
function cookery_lite_prep_time(){
    global $recipe;
    if( $recipe->total_time ) : ?>
        <span class="cook-time">
            <svg class="icon">
                <use xlink:href="<?php echo plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ?>assets/images/sprite.svg#time"></use>
            </svg>
            <span class="meta-text"><?php echo esc_html( $recipe->total_time ); ?></span>
        </span>
    <?php endif;
}
endif;

if( ! function_exists( 'cookery_lite_difficulty_level' ) ) :
/**
 * Difficulty Level.
 */
function cookery_lite_difficulty_level(){
    global $recipe;
    if( $recipe->difficulty_level ) : ?>
        <span class="cook-difficulty">
            <svg class="icon">
                <use xlink:href="<?php echo plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ?>assets/images/sprite.svg#difficulty"></use>
            </svg>
            <span class="meta-text"><?php echo esc_html( ucfirst( $recipe->difficulty_level ) ); ?></span>
        </span>
    <?php endif;
}
endif;

if( ! function_exists( 'cookery_lite_recipe_category' ) ) :
/**
 * Difficulty Level.
 */
function cookery_lite_recipe_category(){
    global $recipe;
    if ( ! empty( $recipe->ID ) ) : ?>
        <span class="post-cat">
            <?php the_terms( $recipe->ID, 'recipe-course', '', '', '' ); ?>
        </span>
    <?php endif;
}
endif;

if( ! function_exists( 'cookery_lite_recipe_rating' ) ) :
/**
 * Rating
 */
function cookery_lite_recipe_rating(){
    global $recipe;
    if ( $recipe->rating ): ?>
        <span class="post-rating">
            <svg class="icon">
                <use xlink:href="<?php echo plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ?>assets/images/sprite.svg#rating"></use>
            </svg>
            <span class="meta-text"><?php echo esc_html( $recipe->rating ); ?></span>
        </span>
    <?php endif;
}
endif;

if( ! function_exists( 'cookery_lite_recipe_pinit' ) ) :
/**
 * Pin it
 */
function cookery_lite_recipe_pinit(){
    global $recipe;
    ?>
    <span class="post-pinit-button">
        <a data-pin-do="buttonPin" href="https://www.pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>/&media=<?php echo esc_url( $recipe->thumbnail ); ?>&description=So%20delicious!" data-pin-custom="true">
            <img src="<?php echo plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ?>/assets/images/pinit-sm.png" alt="pinit">
        </a>
    </span>
    <?php
}
endif;