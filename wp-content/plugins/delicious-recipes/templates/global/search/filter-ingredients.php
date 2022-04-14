<?php
/**
 * Filter by ingredient.
 */
$ingredients_array = delicious_recipes_get_all_ingredients();
ksort( $ingredients_array );
$show_count = apply_filters( 'delicious_recipes_search_filters_show_count', true );
?>
<select class="js-select2" multiple="multiple"  name='recipe_ingredients'>
    <?php foreach( $ingredients_array as $ingredient => $count ) : ?>
        <option value="<?php echo esc_attr( $ingredient ); ?>" id="<?php echo esc_attr( sanitize_title( $ingredient ) ); ?>" name='recipe_ingredients'>
            <?php echo esc_html( $ingredient ); 
                if ( $show_count ) : ?>
                    <span class='count'>(<?php echo esc_html( $count ); ?>)</span>
                <?php endif; 
            ?>
        </option>
    <?php endforeach; ?>
</select>
<?php
