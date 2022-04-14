<?php
/**
 * Filter by recipe keys.
 */
$keys = get_categories( array(
    'taxonomy'   => 'recipe-key',
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => true,
)  );

$keys_sorted = array();
delicious_recipes_sort_terms_hierarchicaly( $keys, $keys_sorted ); ?>
<select class="js-select2" multiple="multiple"  name='recipe_keys'> 
    <?php delicious_recipes_search_taxonomy_render( $keys_sorted, false, 'recipe_keys' ); ?>
</select>
<?php
