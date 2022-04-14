<?php
/**
 * Adds Popular Tags Widget.
 * @package Delicious_Recipes
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class Delicious_Popular_Tags_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'delicious_popular_tags_widget', // Base ID
            'Delicious: Popular Tags', // Name
            array( 
                'description' => __( 'A Popular Tags Widget for Delicious Recipes.', 'delicious-recipes' ),
                'classname'   => 'dr-popular-tags'
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
        $title       = ! empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : __( 'Popular Tags', 'delicious-recipes' ) ;
        $show_counts = isset( $instance[ 'show_counts' ] ) ? (bool) $instance[ 'show_counts' ] : false;
        $taxonomy    = 'recipe-tag';

        echo $before_widget;

        ob_start();

        if ( ! empty( $title ) ) {
            echo $before_title . esc_html( $title ) . $after_title;
        }

        $tag_cloud = wp_tag_cloud(
			apply_filters(
				'wp_delicious_popular_tags_args',
				array(
					'taxonomy'   => $taxonomy,
					'echo'       => false,
					'show_count' => $show_counts,
					'format'     => 'list'
				),
				$instance
			)
		);

		echo $tag_cloud;

        $html = ob_get_clean();
        echo apply_filters( 'wp_delicious_popular_tags_widget', $html, $args, $instance );
        
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
        $title       = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Popular Tags', 'delicious-recipes' );
        $show_counts = isset( $instance[ 'show_counts' ] ) ? (bool) $instance[ 'show_counts' ] : false;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php _e( 'Title:', 'delicious-recipes' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_counts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_counts' ) ); ?>" type="checkbox" value="true" <?php checked( $show_counts, 1 ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_counts' ) ); ?>"><?php _e( 'Show tag counts', 'delicious-recipes' ); ?></label>
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
        $instance = array();
        $instance[ 'title' ]       = ! empty( $new_instance[ 'title' ] ) ? strip_tags( $new_instance[ 'title' ] ) : '';
        $instance[ 'show_counts' ] = ! empty( $new_instance[ 'show_counts' ] ) ? 1 : 0;

        return $instance;
    }

} // class Delicious_Popular_Tags_Widget
