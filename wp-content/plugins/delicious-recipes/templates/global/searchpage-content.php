<?php
/**
 * Search page content template
 * 
 * @package Delicious_Recipes
 */
$global_settings        = delicious_recipes_get_global_settings();
$default_posts_per_page = isset ( $global_settings['recipePerPage'] ) && ( ! empty( $global_settings['recipePerPage'] ) ) ? $global_settings['recipePerPage'] : get_option( 'posts_per_page' );
$enable_search_bar      = isset( $global_settings['displaySearchBar']['0'] ) && 'yes' === $global_settings['displaySearchBar']['0'] ? true : false;
$dashboard_page         = isset( $dashboard_page ) && $dashboard_page ? true : false;

?>
<div class="dr-advance-search">
    <?php if( $enable_search_bar ) : 
        ?>
        <header class="page-header">
            <div class="container">
                <?php get_search_form(); ?>
            </div>
        </header>
    <?php endif; ?>
    <?php 
        /**
         * Search page top filters.
         */
        do_action( 'delicious_recipes_search_top_filters' );
    ?>
    <?php 
        $recipe_search_args = array(
            'post_type'      => DELICIOUS_RECIPE_POST_TYPE,
            'posts_per_page' => absint( $default_posts_per_page ),
            'paged'          => get_query_var( 'paged', 1 ),
            'post_status'    => 'publish'
        );

        if ( isset( $_GET['ingredient'] ) && ! empty( $_GET['ingredient'] ) ) {
            $recipe_search_args['meta_query'] = [
                [
                    'key'     => '_dr_recipe_ingredients',
                    'value'   => sanitize_text_field( $_GET['ingredient'] ),
                    'compare' => 'LIKE'
                ]
            ];
        }

        $recipe_search = new WP_Query( $recipe_search_args );
    ?>
    <div class="container">
        <?php
            if ( $recipe_search->have_posts() ) :
        ?>
                <div class="dr-search-item-wrap" itemscope itemtype="http://schema.org/ItemList">
                    <?php 
                        $position = 1;
                        while( $recipe_search->have_posts() ) : $recipe_search->the_post();
                            /**
                             * Get search page single block - recipe.
                             */
                            $data = array(
                                'position'  => $position,
                                'tax_page'  => $dashboard_page
                            );
                                delicious_recipes_get_template( 'recipes-grid.php', $data );
                            $position++;
                        endwhile;
                        wp_reset_postdata();
                    ?>
                </div><!-- .dr-search-item-wrap -->
        <?php 
            else :
        ?>
                <span class="no-result">
                    <?php esc_html_e( 'Recipes not found.', 'delicious-recipes' ); ?>
                </span>
        <?php
            endif;
        ?>
        
        <nav class="navigation pagination" role="navigation" aria-label="Posts">
            <h2 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'delicious-recipes' ); ?></h2>
            <div class="nav-links">
                <?php 
                    /**
                     * Get Pagination.
                     */
                    $total_pages = $recipe_search->max_num_pages;
                    $big         = 999999999;                      // need an unlikely integer
            
                    if ( $total_pages > 1 ){
                        $current_page = max( 1, get_query_var('paged') );
            
                        echo paginate_links(array(
                            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                            'format'    => '?paged=%#%',
                            'current'   => absint( $current_page ),
                            'total'     => absint( $total_pages ),
                            'prev_text' => __( 'Prev', 'delicious-recipes' ) .
                            '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689">
                                <g transform="translate(17.729 11.628) rotate(180)">
                                    <path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none"
                                        stroke="#374757" stroke-linecap="round" stroke-width="1.5" />
                                    <path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#374757"
                                        stroke-linecap="round" stroke-width="1.5" />
                                </g>
                            </svg>',
                            'next_text' => __( "Next", 'delicious-recipes' ) .
                            '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(0.75 1.061)">
                                    <path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none"
                                        stroke="#374757" stroke-linecap="round" stroke-width="1.5" />
                                    <path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#374757"
                                        stroke-linecap="round" stroke-width="1.5" />
                                </g>
                            </svg>',
                        ));
                    }
                ?>
            </div>
        </nav>
	</div>
</div><!-- .dr-advance-search -->

<script type="text/html" id="tmpl-search-block-tmp">
    <#
    if ( data.length > 0 ) {
        _.each( data, function( val ){
    #>
    <div class="dr-archive-single">
        <figure>
            <a href="{{val.permalink}}">
                <# 
                if ( val.thumbnail ) { 
                    #>
                        {{{val.thumbnail}}}
                    <# 
                } else { 
                    #>
                    <?php delicious_recipes_get_fallback_svg( 'recipe-archive-grid' ); ?>
                    <# 
                } 
                #>
            </a>
            <# 
                if ( val.thumbnail && val.enable_pinit ) { 
                    #>
                        <span class="post-pinit-button">
                            <a data-pin-do="buttonPin" href="https://www.pinterest.com/pin/create/button/?url={{val.permalink}}/&media={{val.thumbnail_url}}&description=So%20delicious!" data-pin-custom="true">
                                <img src="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>/assets/images/pinit-sm.png" alt="pinit">
                            </a>
                        </span>
                    <# 
                }
            #>
            <# 
                if ( val.badges ) { 
                    #>
                        <span class="dr-badge">
                            <a href="{{val.badges.link}}" title="{{val.badges.badge}}" style="background-color:{{val.badges.color}}">
                                {{{val.badges.badge}}}
                            </a>
                        </span> 
                    <#
                }
            #>
            <# 
                if ( val.recipe_keys.length > 0 ) { 
                    #>
                        <span class="dr-category">
                    <#
                        _.each( val.recipe_keys, function( recipe_key ) {
                        #>
                            <a href="{{recipe_key.link}}" title="{{recipe_key.key}}">
                                {{{recipe_key.icon}}}
                                <span class="cat-name">{{recipe_key.key}}</span>
                            </a>
                        <# 
                        });
                    #>
                        </span> 
                    <#
                }
            #>
        </figure>
        <div class="dr-archive-details">
            <h2 class="dr-archive-list-title">
                <a href="{{val.permalink}}">
                {{{val.title}}}
                </a>
            </h2>
            <div class="dr-entry-meta">
                <# 
                if ( val.total_time ) { 
                    #>
                    <span class="dr-time">
                        <svg class="icon">
                            <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#time"></use>
                        </svg>
                        <span class="dr-meta-title">{{val.total_time}}</span>
                    </span>
                    <# 
                }
                if ( val.difficulty_level ) { 
                    #>
                    <span class="dr-level">
                        <svg class="icon">
                            <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#difficulty"></use>
                        </svg>
                        <span class="dr-meta-title">{{val.difficulty_level}}</span>
                    </span>
                    <# 
                } 
                #>
            </div>
        </div>
    </div>
    <#
        });
    } else { 
        #>
        <span class="no-result">
            <?php esc_html_e( 'Recipes not found.', 'delicious-recipes' ); ?>
        </span>
        <# 
    }
    #>
</script>
