<?php
/**
 * Recipe page header.
 */
global $recipe;

// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();
$recipe_global  = delicious_recipes_get_global_settings();
$comment_id     = $recipe->is_pro_active ? '#comments-' . esc_attr( $recipe->ID ) : '#comments';
?>
<header class="dr-entry-header">
    <?php 
        $enableRecipeSingleHead = isset( $recipe_global['enableRecipeSingleHead'][0] ) && 'yes' === $recipe_global['enableRecipeSingleHead'][0] ? true : false;

        if ( $enableRecipeSingleHead ) :
    ?>
        <div class="dr-category">
            <?php the_terms( $recipe->ID, 'recipe-course', '', '', '' ); ?>
        </div>
        <h1 class="dr-entry-title"><?php echo esc_html( $recipe->name ); ?></h1>
        <div class="dr-entry-meta">
            <?php if ( $global_toggles['enable_recipe_author'] ): ?>
                <span class="dr-byline">
                    <?php echo get_avatar( $recipe->author_id, 32 ); ?>
                    <a href="<?php echo esc_url( get_author_posts_url( $recipe->author_id ) ) ?>" class="fn"><?php echo esc_html( $recipe->author ); ?></a>
                </span>
            <?php endif; ?>

            <?php if ( $global_toggles['enable_published_date'] ): ?>
                <span class="dr-posted-on">
                    <svg class="icon"><use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#calendar"></use></svg>
                    <time><?php echo esc_html( delicious_recipes_get_formated_date( $recipe->date_published ) ); ?></time>
                </span>
            <?php endif; ?>

            <?php if ( $global_toggles['enable_comments'] ): ?>
                <span class="dr-comment">
                    <svg class="icon"><use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#comment"></use></svg>
                    <a href="<?php echo esc_attr($comment_id); ?>"><?php 
                        /* translators: %s: total comments count */
                        echo sprintf( _nx( '%s Comment', '%s Comments', number_format_i18n( $recipe->comments_number ), 'number of comments', 'delicious-recipes' ), number_format_i18n( $recipe->comments_number ) ); 
                    ?></a>
                </span>
            <?php endif; ?>

            <?php if ( $recipe->rating && $global_toggles['enable_ratings'] ): ?>
                <div class="dr-star-ratings-wrapper">
                    <div class="dr-star-ratings">
                        <div data-rateyo-read-only="true" data-rateyo-rating="<?php echo esc_attr( $recipe->rating ) ?>" class="dr-comment-form-rating"></div>
                    </div>
                    <span class="dr-rating">
                        <?php 
                            echo esc_html( $recipe->rating ); 
                            /* translators: %$s: rating count*/
                            printf( _nx( ' / %s Review', ' / %s Reviews', absint( $recipe->rating_count ), 'number of comments', 'delicious-recipes' ),  absint( $recipe->rating_count ) );  
                        ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if ( isset( $recipe->recipe_subtitle )  && ! empty( $recipe->recipe_subtitle ) ) : ?>
        <div class="dr-info">
            <?php echo wp_kses_post( $recipe->recipe_subtitle ) ?>
        </div>
    <?php endif; ?>
    <div class="dr-buttons">
        <?php if ( $global_toggles['enable_jump_to_recipe'] ) : ?>
            <a href="#dr-recipe-meta-main-<?php echo esc_attr($recipe->ID); ?>" class="dr-btn-link dr-btn1 dr-smooth-scroll"><?php echo esc_html( $global_toggles['jump_to_recipe_lbl'] ); ?> <svg class="icon"><use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#go-to"></use></svg></a>
        <?php endif; ?>

        <?php if ( ! empty( $recipe->video_gallery ) && $global_toggles['enable_jump_to_video'] ) : ?>
            <a href="#dr-video-gallery-<?php echo esc_attr($recipe->ID); ?>" class="dr-btn-link dr-btn1 dr-smooth-scroll"><i class="fas fa-play"></i><?php echo esc_html( $global_toggles['jump_to_video_lbl'] ); ?></a>
        <?php endif; ?>

        <?php
            if ( $global_toggles['enable_print_recipe'] ) {
                delicious_recipes_get_template_part( 'recipe/print', 'btn' );
            }
        ?>

        <?php 
            /**
             * Recipe Wishlist button
             */
            do_action( 'delicious_recipes_wishlist_button' );
        ?>
    </div>
</header>
<?php
