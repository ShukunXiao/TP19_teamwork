<?php
/**
 * Filter by recipe cuisines.
 */
$categories = get_categories( array(
    'taxonomy'   => 'recipe-cuisine',
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => true,
)  );

$categories_sorted = array();
delicious_recipes_sort_terms_hierarchicaly( $categories, $categories_sorted ); ?>
<select class="js-select2" multiple="multiple"  name='recipe_cuisines'> 
    <?php delicious_recipes_search_taxonomy_render( $categories_sorted, false, 'recipe_cuisines' ); ?>
</select>
<?php
