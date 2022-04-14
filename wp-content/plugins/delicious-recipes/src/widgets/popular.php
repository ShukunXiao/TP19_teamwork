<?php
/**
 * Adds Popular Recipe Widget.
 * @package Delicious_Recipes
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class Delicious_Popular_Recipes_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        if( ! is_customize_preview() ) add_action( 'wp', array( $this, 'delicious_recipes_set_views' ) );

        parent::__construct(
            'delicious_popular_recipes_widget', // Base ID
            __( 'Delicious: Popular Recipes', 'delicious-recipes' ), // Name
            array( 
                'description' => __( 'A Popular Recipes Widget for Delicious Recipes.', 'delicious-recipes' ),
            ) // Args
        );
    }

    /**
     * Function to add the recipe posts view count 
     */
    function delicious_recipes_set_views( $post_id ) {
        if ( in_the_loop() ) {
            $post_id = get_the_ID();
        } else {
            global $wp_query;
            $post_id = $wp_query->get_queried_object_id();
        }

        if( is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) {
            $count_key = '_delicious_recipes_view_count';
            $count     = get_post_meta( $post_id, $count_key, true );
            if( $count == '' ) {
                $count = 0;
                delete_post_meta( $post_id, $count_key );
                add_post_meta( $post_id, $count_key, '1' );
            } else {
                $count++;
                update_post_meta( $post_id, $count_key, absint( $count ) );
            }
        }
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        if ( is_admin() ) {
            // Display nothing if called in backend.
            echo '<div class="wp-block-legacy-widget__edit-no-preview">
                    <h3>'. $args['widget_name'] .'</h3>
                    <p>'. esc_html__( "No preview available.", "delicious-recipes" ) .'</p>
                </div>';
            return;
        }
        
        extract( $args );
        $title    = ! empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : __( 'Popular Recipes', 'delicious-recipes' ) ;
        $num_post = ! empty( $instance[ 'num_post' ] ) ? $instance[ 'num_post' ] : 4 ;
        $based_on = ! empty( $instance[ 'based_on' ] ) ? $instance[ 'based_on' ] : 'views';
        $style    = ! empty( $instance[ 'style' ] ) ? $instance[ 'style' ] : 'style-one';

        $class  = 'style-one' === $style ? 'dr-favorite-recipe' : 'dr-most-popular-rcp';
        $layout = 'style-one' === $style ? 'card' : 'list';

        echo $before_widget;

        ob_start();

        if ( ! empty( $title ) ) {
            echo $before_title . esc_html( $title ) . $after_title;
        }

        $cat = get_theme_mod( 'exclude_categories' );
        if( $cat ) $cat = array_diff( array_unique( $cat ), array('') );

        $args = array(
            'post_type'             => DELICIOUS_RECIPE_POST_TYPE,
            'post_status'           => 'publish',
            'posts_per_page'        => absint( $num_post ),
            'ignore_sticky_posts'   => true,
            'category__not_in'      => $cat
        );
        
        if( $based_on == 'views' ) {
            $args[ 'orderby' ]  = 'meta_value_num';
            $args[ 'meta_key' ] = '_delicious_recipes_view_count';
            
        } elseif( $based_on == 'comments' ) {
            $args[ 'orderby' ] = 'comment_count';
        }

        $query = new WP_Query( $args );

        if( $query->have_posts() ) {
            echo '<ul class="' . esc_attr( $class ) . '">';
            while( $query->have_posts() ) {
                $query->the_post();

                delicious_recipes_get_template_part( 'widgets/popular', $layout );
            }
            wp_reset_postdata();
            echo '</ul>';
        }

        $html = ob_get_clean();
        echo apply_filters( 'wp_delicious_popular_recipe_widget', $html, $args, $instance );
        
        echo $after_widget;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title    = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Popular Recipes', 'delicious-recipes' );
        $num_post = isset( $instance[ 'num_post' ] ) ? $instance[ 'num_post' ] : 4;
        $based_on = ! empty( $instance[ 'based_on' ] ) ? $instance[ 'based_on' ] : 'views';
        $style    = isset( $instance[ 'style' ] ) ? $instance[ 'style' ] : 'style-one';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'delicious-recipes' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr(  $this->get_field_name( 'title'  )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'num_post' ) ); ?>"><?php esc_html_e( 'Number of Recipes:', 'delicious-recipes' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'num_post' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'num_post' ) ); ?>" type="number" value="<?php echo esc_attr( $num_post ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'based_on' ) ); ?>"><?php esc_html_e( 'Popular based on:', 'delicious-recipes' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'based_on' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'based_on' ) ); ?>" class="widefat">
                <option value="views" <?php selected( $based_on, 'views' ); ?>><?php esc_html_e( 'Post Views', 'delicious-recipes' ); ?></option>
                <option value="comments" <?php selected( $based_on, 'comments' ); ?>><?php esc_html_e( 'Comment Count', 'delicious-recipes' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php esc_html_e( 'Layout:', 'delicious-recipes' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>">
                <?php
                    $styles = delicious_recipes_widget_styles();
                    foreach ( $styles as $key => $value ) { 
                    ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $style, $key );?>><?php echo esc_html( $value );?></option>
                    <?php
                    }
                ?>
            </select>
        </p>
    <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance   = array();
        $instance[ 'title' ]    = ! empty( $new_instance[ 'title' ] ) ? strip_tags( $new_instance[ 'title' ] ) : '';
        $instance[ 'num_post' ] = ! empty( $new_instance[ 'num_post' ] ) ? absint( $new_instance[ 'num_post' ] ) : '';
        $instance[ 'based_on' ] = ! empty( $new_instance[ 'based_on' ] ) ? esc_attr( $new_instance[ 'based_on' ] ) : 'views';
        $instance[ 'style' ]    = ! empty( $new_instance[ 'style' ] ) ? esc_attr( $new_instance[ 'style' ] ) : 'style-one';

        return $instance;
    }

} // class Delicious_Popular_Recipes_Widget
