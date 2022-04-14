<?php
/**
 * Filter by Season.
 */
$simple_factor = array(
    "10-ingredients-or-less" => __("10 ingredients or less", 'delicious-recipes'),
    "15-minutes-or-less"     => __("15 minutes or less", 'delicious-recipes'),
    "30-minutes-or-less"     => __("30 minutes or less", 'delicious-recipes'),
    "7-ingredients-or-less"  => __("7 ingredients or less", 'delicious-recipes'),
);
$show_count = apply_filters( 'delicious_recipes_search_filters_show_count', true );

$args = array(
    'post_type'   => DELICIOUS_RECIPE_POST_TYPE,
    'post_status' => 'publish'
);
?>
<select class="js-select2" multiple="multiple"  name='simple_factor'>
    <?php foreach( $simple_factor as $key => $value ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( sanitize_title( $value ) ); ?>" name='simple_factor'>
            <?php echo esc_html( $value ); 
                if ( $show_count ) : 
                    switch( $key ) {
                        case '10-ingredients-or-less' :
                            $args['meta_query'] = array(
                                array(
                                    'key'     => '_dr_ingredient_count',
                                    'value'   => 10,
                                    'compare' => '<=',
                                )
                            );
                        break;
                        case '15-minutes-or-less' :
                            $args['meta_query'] = array(
                                array(
                                    'key'     => '_dr_recipe_total_time',
                                    'value'   => 15,
                                    'compare' => '<=',
                                )
                            );
                        break;
                        case '30-minutes-or-less' :
                            $args['meta_query'] = array(
                                array(
                                    'key'     => '_dr_recipe_total_time',
                                    'value'   => 30,
                                    'compare' => '<=',
                                )
                            );
                        break;
                        case '7-ingredients-or-less' :
                            $args['meta_query'] = array(
                                array(
                                    'key'     => '_dr_ingredient_count',
                                    'value'   => 7,
                                    'compare' => '<=',
                                )
                            );
                        break;
                    }
                    $results = get_posts( $args );
                    $count   = count( $results ); ?>
                    <span class='count'>(<?php echo esc_html( $count ); ?>)</span>
                <?php endif;
            ?>
        </option>
    <?php endforeach; ?>
</select>
<?php
