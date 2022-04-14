<?php
/**
 * Filter by recipe tags.
 */
$tags = get_categories( array(
    'taxonomy'   => 'recipe-tag',
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => true,
)  );

$tags_sorted = array();
delicious_recipes_sort_terms_hierarchicaly( $tags, $tags_sorted ); ?>
<select class="js-select2" multiple="multiple"  name='recipe_tags'> 
    <?php delicious_recipes_search_taxonomy_render( $tags_sorted, false, 'recipe_tags' ); ?>
</select>
<?php
