<?php
/**
 * Wishlist Template.
 */

global $wp;
$current_user = wp_get_current_user();
$_user_meta = get_user_meta( $current_user->ID, 'delicious_recipes_user_meta', true );
?>
<div class="dr-archive-list-wrapper" id="wishlist">
    <header class="dr-archive-header">
        <h2 class="dr-archive-title"><?php esc_html_e( 'Favorites', 'delicious-recipes' ); ?></h2>

        <?php if ( isset( $_user_meta['wishlists'] ) && ! empty( $_user_meta['wishlists'] ) ) : ?>
            <div class="dr-archive-filter-area">
                <div class="dr-archive-filter-top">
                    <div class="view-layout-buttons">
                        <span class="view-layout-title"><?php esc_html_e( 'View by:', 'delicious-recipes' ); ?></span>
                        <button type="button" id="grid-view" class="view-layout-btn grid-view active">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                <g id="Group_5889" data-name="Group 5889" transform="translate(0.188 -0.325)">
                                    <rect id="Rectangle_1809" data-name="Rectangle 1809" width="4" height="4" transform="translate(-0.188 0.325)" fill="#374757"/>
                                    <rect id="Rectangle_1812" data-name="Rectangle 1812" width="4" height="4" transform="translate(-0.188 6.325)" fill="#374757"/>
                                    <rect id="Rectangle_1815" data-name="Rectangle 1815" width="4" height="4" transform="translate(-0.188 12.325)" fill="#374757"/>
                                    <rect id="Rectangle_1810" data-name="Rectangle 1810" width="4" height="4" transform="translate(5.812 0.325)" fill="#374757"/>
                                    <rect id="Rectangle_1819" data-name="Rectangle 1819" width="4" height="4" transform="translate(11.812 0.325)" fill="#374757"/>
                                    <rect id="Rectangle_1811" data-name="Rectangle 1811" width="4" height="4" transform="translate(5.812 6.325)" fill="#374757"/>
                                    <rect id="Rectangle_1817" data-name="Rectangle 1817" width="4" height="4" transform="translate(11.812 6.325)" fill="#374757"/>
                                    <rect id="Rectangle_1816" data-name="Rectangle 1816" width="4" height="4" transform="translate(5.812 12.325)" fill="#374757"/>
                                    <rect id="Rectangle_1818" data-name="Rectangle 1818" width="4" height="4" transform="translate(11.812 12.325)" fill="#374757"/>
                                </g>
                            </svg>
                        </button>
                        <button type="button" id="list-view" class="view-layout-btn list-view">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="17" viewBox="0 0 24 17">
                                <g id="Group_5890" data-name="Group 5890" transform="translate(0.231 0.295)">
                                    <rect id="Rectangle_1809" data-name="Rectangle 1809" width="4" height="4" transform="translate(-0.231 -0.295)" fill="#374757"/>
                                    <rect id="Rectangle_1812" data-name="Rectangle 1812" width="4" height="4" transform="translate(-0.231 6.705)" fill="#374757"/>
                                    <rect id="Rectangle_1813" data-name="Rectangle 1813" width="4" height="4" transform="translate(-0.231 12.705)" fill="#374757"/>
                                    <rect id="Rectangle_1810" data-name="Rectangle 1810" width="16" height="1" transform="translate(7.769 1.705)" fill="#374757"/>
                                    <rect id="Rectangle_1811" data-name="Rectangle 1811" width="16" height="1" transform="translate(7.769 7.705)" fill="#374757"/>
                                    <rect id="Rectangle_1814" data-name="Rectangle 1814" width="16" height="1" transform="translate(7.769 14.705)" fill="#374757"/>
                                </g>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </header>
    <div class="dr-archive-list grid-view">
        <?php 
            if ( isset( $_user_meta['wishlists'] ) && ! empty( $_user_meta['wishlists'] ) ) : 
        
                $global_settings        = delicious_recipes_get_global_settings();
                $default_posts_per_page = isset ( $global_settings['recipePerPage'] ) && ( ! empty( $global_settings['recipePerPage'] ) ) ? $global_settings['recipePerPage'] : get_option( 'posts_per_page' );

                $cat = get_theme_mod( 'exclude_categories' );
                if( $cat ) $cat = array_diff( array_unique( $cat ), array('') );

                $args = array(
                    'post_type'           => DELICIOUS_RECIPE_POST_TYPE,
                    'post_status'         => 'publish',
                    'posts_per_page'      => absint( $default_posts_per_page ),
                    'post__in'            => $_user_meta['wishlists'],
                    'ignore_sticky_posts' => true,
                    'category__not_in'    => $cat
                );

                $query = new WP_Query( $args );

                if( $query->have_posts() ) :
                    while( $query->have_posts() ) :
                        $query->the_post();

                        $data = array(
                            'disable_wishlist' => true,
                            'tax_page'         => true
						);

                        delicious_recipes_get_template( 'recipes-grid.php', $data );
                    endwhile;
                    wp_reset_postdata();
                endif;

            else : ?>
                
                <div class="dr-archive-desc">
                    <?php 
                        /* translators: %1$s: <a> tag open %2$s: </a> tag close */
                        echo sprintf( esc_html__( 'No recipes found in your wishlist. You can add recipes from %1$s Recipe Archive. %2$s', 'delicious-recipes' ), '<a href="'.get_post_type_archive_link( DELICIOUS_RECIPE_POST_TYPE ).'">', '</a>' );
                    ?>
                </div>
                
            <?php endif;
        ?>
    </div>
</div>
<?php
