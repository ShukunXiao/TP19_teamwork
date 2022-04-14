<?php
/**
 * Filter by recipe badges.
 */
$badges = get_categories( array(
    'taxonomy'   => 'recipe-badge',
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => true,
)  );

$badges_sorted = array();
delicious_recipes_sort_terms_hierarchicaly( $badges, $badges_sorted ); ?>
<select class="js-select2" multiple="multiple"  name='recipe_badges'> 
    <?php delicious_recipes_search_taxonomy_render( $badges_sorted, false, 'recipe_badges' ); ?>
</select>
<?php
