<?php
/**
 * Search filters template.
 */

$recipe_global = delicious_recipes_get_global_settings();
$searchFilters = isset( $recipe_global['searchFilters'] ) ? $recipe_global['searchFilters'] : array();

$search_page_filters = apply_filters( 'delicious_recipes_search_page_filters', $searchFilters );
?>
<div class="advance-search-options">
    <div class="container">
    <?php foreach( $search_page_filters as $key => $filter ) : 
        $enable = isset( $filter['enable'][0] ) && 'yes' === $filter['enable'][0] ? true : false;
        
        if ( ! $enable ) {
            continue;
        }

        $key = isset( $filter['key'] ) ? sanitize_title( $filter['key'] ) : '';
    ?>
        <div class="advance-search-block">
            <label class="advance-search-title"><?php echo esc_html( $filter['label'] ); ?></label>
            <div class="advance-search-field  dr-search-field">
                <?php
                    /**
                     * Get respective filters template.
                     */
                    delicious_recipes_get_template_part( 'global/search/filter', $key );
                ?>
            </div>
        </div>
    <?php endforeach; ?>
    <input type="hidden" name="dr-search-nonce" id="dr-search-nonce" value="<?php echo wp_create_nonce( 'dr-search-nonce' );?>">

    </div>
</div>
<?php
