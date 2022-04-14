<?php
/**
 * Filter by recipe cooking methods.
 */
$categories = get_categories( array(
    'taxonomy'   => 'recipe-cooking-method',
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => true,
)  );

$categories_sorted = array();
delicious_recipes_sort_terms_hierarchicaly( $categories, $categories_sorted ); ?>
<select class="js-select2" multiple="multiple"  name='recipe_cooking_methods'> 
    <?php delicious_recipes_search_taxonomy_render( $categories_sorted, false, 'recipe_cooking_methods' ); ?>
</select>
<?php
