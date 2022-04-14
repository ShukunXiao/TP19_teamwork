<?php
/**
 * Filter by recipe courses.
 */
$courses = get_categories( array(
    'taxonomy'   => 'recipe-course',
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => true,
)  );

$courses_sorted = array();
delicious_recipes_sort_terms_hierarchicaly( $courses, $courses_sorted ); ?>
<select class="js-select2" multiple="multiple"  name='recipe_courses'> 
    <?php delicious_recipes_search_taxonomy_render( $courses_sorted, false, 'recipe_courses' ); ?>
</select>
<?php
