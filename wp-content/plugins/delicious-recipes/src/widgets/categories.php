<?php
/**
 * Adds Recipe Categories Widget.
 * @package Delicious_Recipes
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class Delicious_Recipe_Categories_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'delicious_recipe_categories_widget', // Base ID
            'Delicious: Recipe Categories', // Name
            array( 
                'description' => __( 'A list or dropdown of recipe categories for Delicious Recipes.', 'delicious-recipes' ),
                'classname' => 'dr-categories' 
            ) // Args
        );
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
        $title       = ! empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : __( 'Recipe Categories', 'delicious-recipes' ) ;
        $taxonomy    = isset( $instance[ 'taxonomy' ] ) && $instance[ 'taxonomy' ] != '' ? $instance[ 'taxonomy' ] : 'recipe-course';
        $categories  = isset( $instance[ 'categories' ] ) &&  $instance[ 'categories' ] != '' ? $instance[ 'categories' ] : array();
        $show_drpdwn = isset( $instance[ 'show_drpdwn' ] ) ? (bool) $instance[ 'show_drpdwn' ] : false;
        $show_counts = isset( $instance[ 'show_counts' ] ) ? (bool) $instance[ 'show_counts' ] : false;

        echo $before_widget;

        ob_start();

        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }

        $terms = get_terms( array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ) );

        if( 0 != sizeof( $categories ) ) {
            foreach( $categories as $key => $category ) {
                $exists = term_exists( absint($category), $taxonomy );
                if( ! $exists ) {
                    unset( $categories[$key] );
                }
            }
        }

        if( 0 == sizeof( $categories ) ) {
            $categories = $terms;
        }

        $data          = array(
			'show_counts' => $show_counts,
			'taxonomy'    => $taxonomy,
			'categories'  => $categories,
			'title'       => $title
        );

        if( $show_drpdwn ) {
            delicious_recipes_get_template( 'widgets/categories-dropdown.php', $data );
        } else {
            delicious_recipes_get_template( 'widgets/categories-list.php', $data );
        }
        

        $html = ob_get_clean();
        echo apply_filters( 'wp_delicious_recipe_categories_widget', $html, $args, $instance );
        
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
        include plugin_dir_path( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/src/admin/partials/dr-category-widget-form.php';       
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
        $instance = array();
        $instance[ 'title' ]       = ! empty( $new_instance[ 'title' ] ) ? strip_tags( $new_instance[ 'title' ] ) : '';
        $instance[ 'taxonomy' ]    = ! empty( $new_instance[ 'taxonomy' ] ) ? strip_tags( $new_instance[ 'taxonomy' ] ) : '' ;
        $instance[ 'categories' ]  = isset( $new_instance[ 'categories' ] ) && $new_instance[ 'categories' ] != '' ? $new_instance[ 'categories' ] : '';
        $instance[ 'show_drpdwn' ] = ! empty( $new_instance[ 'show_drpdwn' ] ) ? 1 : 0;
        $instance[ 'show_counts' ] = ! empty( $new_instance[ 'show_counts' ] ) ? 1 : 0;

        return $instance;
    }

} // class Delicious_Recipe_Categories_Widget
