<?php
/**
 * Filter by Season.
 */
$seasons = array(
    "fall"      => __("Fall", 'delicious-recipes'),
    "winter"    => __("Winter", 'delicious-recipes'),
    "summer"    => __("Summer", 'delicious-recipes'),
    "spring"    => __("Spring", 'delicious-recipes'),
    "available" => __("Suitable throughout the year", 'delicious-recipes')
);
$show_count = apply_filters( 'delicious_recipes_search_filters_show_count', true );

$args = array(
    'post_type' => DELICIOUS_RECIPE_POST_TYPE,
);
?>
<select class="js-select2" multiple="multiple"  name='seasons'>
    <?php foreach( $seasons as $key => $value ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( sanitize_title( $value ) ); ?>" name='seasons'>
            <?php echo esc_html( $value ); 
                if ( $show_count ) : 
                    $args['meta_query'] = array(
                        array(
                            'key'     => '_dr_best_season',
                            'value'   => $key,
                            'compare' => 'LIKE',
                        )
                    );
                    $results = get_posts( $args );
                    $count   = count( $results ); ?>
                    <span class='count'>(<?php echo esc_html( $count ); ?>)</span>
                <?php endif; 
            ?>
        </option>
    <?php endforeach; ?>
</select>
<?php
