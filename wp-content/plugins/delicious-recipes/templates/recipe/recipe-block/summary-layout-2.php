<?php
/**
 * Recipe Summary tempalte.
 * 
 */
global $recipe;

// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();

// Image size.
$img_size = $global_toggles['enable_recipe_image_crop'] ? 'recipe-feat-gallery' : 'full';
$comment_id = $recipe->is_pro_active ? '#comments-' . esc_attr( $recipe->ID ) : '#comments';
?>
<div class="dr-post-summary layout-2">
    <div class="dr-recipe-summary-inner">
        <div class="dr-recipe-summary-header">
            <div class="dr-title-wrap">
                <h2 class="dr-title"><?php echo esc_html( $recipe->name ); ?></h2>
                <?php if ( $recipe->rating_count && $global_toggles['enable_ratings'] ) : ?>
                    <span class="dr-rating dr-star-ratings-wrapper">
                        <div class="dr-star-ratings">
                            <div data-rateyo-read-only="true" data-rateyo-rating="<?php echo esc_attr( $recipe->rating ) ?>" class="dr-comment-form-rating"></div>
                        </div>
                        <?php 
                            $average_rating = $recipe->rating;

                            if ( $average_rating ) {
                                /* translators: %1$s: average rating */
                                echo esc_html( sprintf( __( '%1$s from', 'delicious-recipes' ), $average_rating ) );
                            }
                        ?>
                        <span class="dr-text-red">
                            <a href="<?php echo esc_attr($comment_id); ?>"><?php 
                                /* translators: %s: number of comments count */
                                printf( _nx( '%s vote', '%s votes', absint( $recipe->rating_count ), 'number of comments', 'delicious-recipes' ),  absint( $recipe->rating_count ) ); 
                            ?></a>
                        </span>
                    </span>
                <?php endif; ?>
                <?php if ( ! empty( $recipe->recipe_description ) && $global_toggles['enable_description'] ) : ?>
                    <div class="dr-summary">
                        <p><?php echo do_shortcode( $recipe->recipe_description ); ?></p>
                    </div>
                <?php endif; ?>
                <div class="dr-entry-meta">
                    <?php if( $global_toggles['enable_author'] ) : ?>
                        <span class="dr-byline">
                            <span class="dr-meta-title">
                                <svg class="icon">
                                    <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#author"></use>
                                </svg>
                                <?php echo esc_html( $global_toggles['author_lbl'] ); ?>:
                            </span>
                            <a href="<?php echo esc_url( get_author_posts_url( $recipe->author_id ) ) ?>" class="fn"><?php echo esc_html( $recipe->author ); ?></a>
                        </span>
                    <?php endif; ?>
                    <?php 
                        if( ! empty( $recipe->cooking_method ) && $global_toggles['enable_cooking_method'] ) :
                    ?>
                        <span class="dr-method">
                            <span class="dr-meta-title">
                                <svg class="icon">
                                    <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#cooking-method"></use>
                                </svg>
                                <?php echo esc_html( $global_toggles['cooking_method_lbl'] ); ?>:
                            </span>
                            <?php the_terms( $recipe->ID, 'recipe-cooking-method', '', ', ', '' ); ?>
                        </span>
                    <?php endif;
                        if( ! empty( $recipe->recipe_cuisine ) && $global_toggles['enable_cuisine'] ) :
                    ?>
                    <span class="dr-cuisine">
                        <span class="dr-meta-title">
                            <svg class="icon">
                                <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#cuisine"></use>
                            </svg>
                            <?php echo esc_html( $global_toggles['cuisine_lbl'] ); ?>:
                        </span>
                        <?php the_terms( $recipe->ID, 'recipe-cuisine', '', ', ', '' ); ?>
                    </span>
                    <?php endif; 
                        if( ! empty( $recipe->recipe_course ) && $global_toggles['enable_category'] ) :
                    ?>
                    <span class="dr-category">
                        <span class="dr-meta-title">
                            <svg class="icon">
                                <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#category"></use>
                            </svg>
                            <?php echo esc_html( $global_toggles['category_lbl'] ); ?>:
                        </span>
                        <?php the_terms( $recipe->ID, 'recipe-course', '', ', ', '' ); ?>
                    </span>
                    <?php endif;
                        if ( ! empty( $recipe->recipe_keys ) && $global_toggles['enable_recipe_keys'] ) :
                    ?>
                    <span class="dr-category dr-recipe-keys">
                        <span class="dr-meta-title">
                            <svg class="icon">
                                <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#recipe-keys"></use>
                            </svg>
                            <?php echo esc_html( $global_toggles['recipe_keys_lbl'] ); ?>:
                        </span>
                        <?php
                            foreach( $recipe->recipe_keys as $recipe_key ) {
                                $key              = get_term_by( 'name', $recipe_key, 'recipe-key' );
                                $recipe_key_metas = get_term_meta( $key->term_id, 'dr_taxonomy_metas', true );
                                $key_svg          = isset( $recipe_key_metas['taxonomy_svg'] ) ? $recipe_key_metas['taxonomy_svg'] : '';
                        ?>
                                <a href="<?php echo esc_url( get_term_link( $key, 'recipe-key' ) ); ?>" title="<?php echo esc_attr( $recipe_key ); ?>">
                                    <span class="dr-svg-icon">
                                        <?php delicious_recipes_get_tax_icon( $key ); ?>
                                    </span>
                                    <span class="cat-name"><?php echo esc_attr( $recipe_key ); ?></span>
                                </a>
                        <?php 
                            }
                        ?>
                    </span>
                <?php endif;  ?> 
                </div>
            </div>
            <div class="dr-image">
                <?php 
                    if ( has_post_thumbnail() ) {
                        the_post_thumbnail( $img_size );
                    } else {
                        delicious_recipes_get_fallback_svg( $img_size );
                    }
                ?>
                <?php 
                if ( $global_toggles['enable_print_recipe'] ) : ?>
                    <div class="dr-buttons">
                        <?php delicious_recipes_get_template_part( 'recipe/print', 'btn' ); ?>
                    </div>
                <?php endif;
                if( $global_toggles['enable_pintit'] && $recipe->thumbnail ) : ?>
                    <span class="post-pinit-button">
                        <a data-pin-do="buttonPin" href="https://www.pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>/&media=<?php echo esc_url( $recipe->thumbnail ); ?>&description=So%20delicious!" data-pin-custom="true">
                            <?php echo esc_html( "Pin Recipe" ); ?>
                        </a>
                    </span> 
                <?php endif;
                    /**
                     * Recipe Wishlist button
                     */
                    do_action( 'delicious_recipes_wishlist_button' );
                ?>
                <?php
                    /**
                     * Recipe Badges
                     */
                    do_action( 'delicious_recipes_badges' );
                ?>
            </div>
        </div>
    </div>
    
    <div class="dr-extra-meta">
        <?php  
        if ( ! empty( $recipe->total_time ) ) : ?>
            <span class="dr-sim-metaa dr-cook-time">
                <span class="dr-meta-title">
                    <svg class="icon">
                        <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#time"></use>
                    </svg>
                </span>
                <?php if( ! empty( $recipe->prep_time ) && $global_toggles['enable_prep_time'] ) : 
                ?>
                    <span class="dr-prep-time">
                        <span class="dr-meta-title"><?php echo esc_html( $global_toggles['prep_time_lbl'] ); ?></span>
                        <b><?php echo esc_html( $recipe->prep_time ) ?> <?php echo esc_html( $recipe->prep_time_unit ); ?></b>
                    </span>
                <?php endif;
                if( ! empty( $recipe->cook_time ) && $global_toggles['enable_cook_time'] ) : 
                ?>
                    <span class="dr-cook-time">
                        <span class="dr-meta-title"><?php echo esc_html( $global_toggles['cook_time_lbl'] ); ?></span>
                        <b><?php echo esc_html( $recipe->cook_time ) ?> <?php echo esc_html( $recipe->cook_time_unit ); ?></b>
                    </span>
                <?php endif;
                if( ! empty( $recipe->rest_time ) && $global_toggles['enable_rest_time'] ) : 
                ?>
                    <span class="dr-Rest-time">
                        <span class="dr-meta-title"><?php echo esc_html( $global_toggles['rest_time_lbl'] ); ?></span>
                        <b><?php echo esc_html( $recipe->rest_time ) ?> <?php echo esc_html( $recipe->rest_time_unit ); ?></b>
                    </span>
                <?php endif;
                if( ! empty( $recipe->total_time ) && $global_toggles['enable_total_time'] ) : 
                ?>
                    <span class="dr-total-time">
                        <span class="dr-meta-title"><?php echo esc_html( $global_toggles['total_time_lbl'] ); ?></span>
                        <b><?php echo esc_html( $recipe->total_time ) ?></b>
                    </span>
                <?php endif; ?>
            </span>
        <?php endif; 
        if( ! empty( $recipe->difficulty_level ) && $global_toggles['enable_difficulty_level'] ) : ?>
            <span class="dr-sim-metaa dr-lavel">
                <span class="dr-meta-title">
                    <svg class="icon">
                        <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#difficulty"></use>
                    </svg>
                    <?php echo esc_html( $global_toggles['difficulty_level_lbl'] ); ?>:
                </span>
                <b><?php echo esc_html( $recipe->difficulty_level ); ?></b>
            </span>
        <?php endif;
        if ( ! empty( $recipe->no_of_servings ) && $global_toggles['enable_servings'] ) :
        ?>
            <span class="dr-sim-metaa dr-yields">
                <span class="dr-meta-title">
                    <svg class="icon">
                        <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#yield"></use>
                    </svg>
                    <?php echo esc_html( $global_toggles['servings_lbl'] ); ?>:
                </span>
                <b><?php echo esc_html( $recipe->no_of_servings ); ?></b>
            </span>
        <?php endif; 
        if( ! empty( $recipe->recipe_calories ) && $global_toggles['enable_calories'] ) :
        ?>
            <span class="dr-sim-metaa dr-calorie">
                <span class="dr-meta-title">
                    <svg class="icon">
                        <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#calories"></use>
                    </svg>
                    <?php echo esc_html( $global_toggles['calories_lbl'] ); ?>:
                </span>
                <b><?php echo esc_html( $recipe->recipe_calories ); ?></b>
            </span>
        <?php endif; 
        if( ! empty( $recipe->best_season ) && $global_toggles['enable_seasons'] ) :
        ?>
            <span class="dr-sim-metaa dr-season">
                <span class="dr-meta-title">
                    <svg class="icon">
                        <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#season"></use>
                    </svg>
                    <?php echo esc_html( $global_toggles['seasons_lbl'] ); ?>:
                </span>
                <b><?php echo esc_html( $recipe->best_season ); ?></b>
            </span>
        <?php endif; ?>
    </div>
</div>
<?php
