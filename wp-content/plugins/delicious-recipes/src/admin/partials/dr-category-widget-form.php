<?php
/**
 * Recipe Categories Widget Form.
 * 
 * @package Delicious_Recipes
 */
$title       = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Recipe Categories', 'delicious-recipes' );
$taxonomy    = isset( $instance[ 'taxonomy' ] ) && '' != $instance[ 'taxonomy' ] ? $instance[ 'taxonomy' ] : 'recipe-course';
$categories  = isset( $instance[ 'categories' ] ) && '' != $instance[ 'categories' ] ? $instance[ 'categories' ] : array();
$show_drpdwn = isset( $instance[ 'show_drpdwn' ] ) ? (bool) $instance[ 'show_drpdwn' ] : false;
$show_counts = isset( $instance[ 'show_counts' ] ) ? (bool) $instance[ 'show_counts' ] : false;

$taxonomies = array(
    'recipe-course'         => __( 'Recipe Course', 'delicious-recipes' ),
    'recipe-cuisine'        => __( 'Recipe Cuisine', 'delicious-recipes' ),
    'recipe-cooking-method' => __( 'Recipe Cooking Method', 'delicious-recipes' )
);
$taxonomies = apply_filters( 'wp_delicious_recipe_categories', $taxonomies );

?>
<p>
    <label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php _e( 'Title:', 'delicious-recipes' ); ?></label>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>"><?php esc_html_e( 'Recipe Taxonomy:', 'delicious-recipes' ); ?></label>
    <select name="<?php echo esc_attr( $this->get_field_name( 'taxonomy' ) );?>" class="dr-recipe-taxonomy-selector widefat" id="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>" >

        <option disabled><?php _e( '--Select Taxonomy--', 'delicious-recipes' );?></option> 
        <?php
            foreach ( $taxonomies as $key => $value ) {
                ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $taxonomy, $key );?>><?php echo esc_html( $value );?></option>
                <?php
            }
        ?>
    </select>
</p>
<p>
    <span class="dr-recipe-categories-terms-holder">
    <?php if( isset( $taxonomy ) && $taxonomy != '' ) {
        $terms = get_terms( array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ) );

        ?>
        <label for="<?php echo esc_attr( $this->get_field_id( 'categories' ) ); ?>"><?php esc_html_e( 'Choose Categories:', 'delicious-recipes' ); ?></label>
        <select name="<?php echo esc_attr( $this->get_field_name( 'categories[]' ) );?>" class="dr-recipe-cat-select widefat" id="<?php echo esc_attr( $this->get_field_id( 'categories' ) ); ?>" multiple>
            <?php
                if ( ! is_wp_error( $terms ) && is_array( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $selected = ( in_array( $term->term_id, $categories ) ? 'selected="selected"' : '');
                        printf( '<option value="%1$s"%2$s>%3$s</option>',
                            esc_html( $term->term_id ),
                            $selected,
                            esc_html( $term->name )
                        );
                    }
                } else {
                    echo "<option disabled selected>". __( 'No Terms available.', 'delicious-recipes' ) . "</option>";
                }                                  
            ?>
        </select>
        <?php
    }
    ?>
    </span>
</p>
<p>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_drpdwn' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_drpdwn' ) ); ?>" type="checkbox" value="true" <?php checked( $show_drpdwn, 1 ); ?> />
    <label for="<?php echo esc_attr( $this->get_field_id( 'show_drpdwn' ) ); ?>"><?php esc_html_e( 'Show as dropdown', 'delicious-recipes' ); ?></label>
</p>
<p>
    <input class="widefat " id="<?php echo esc_attr( $this->get_field_id( 'show_counts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_counts' ) ); ?>" type="checkbox" value="true" <?php checked( $show_counts, 1 ); ?> />
    <label for="<?php echo esc_attr( $this->get_field_id( 'show_counts' ) ); ?>"><?php esc_html_e( 'Show recipe counts', 'delicious-recipes' ); ?></label>
</p>
<?php
