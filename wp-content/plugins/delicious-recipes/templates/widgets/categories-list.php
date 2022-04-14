<?php
/**
 * Recipe Categories List
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipe/widgets/categories-list.php.
 *
 * HOWEVER, on occasion WP Delicious will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://wpdelicious.com/docs/template-structure/
 * @package     Delicious_Recipes/Templates
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( $taxonomy != '' && is_array( $categories ) && ! empty( $categories ) ) {
    
    echo '<ul>';
    foreach ( $categories as $key => $value ) {
        
        $category = get_term( $value, $taxonomy );
        
        if( empty( $category ) && is_wp_error( $category ) ) {
            return;
        }

        ?>
        <li>
            <figure>
                <a href="<?php echo esc_url( get_term_link( $category->term_id ) ); ?>">
                    <?php delicious_recipes_get_tax_icon( $category ); ?>

                    <?php if( $show_counts ) { ?>
                            <span class="dr-cat-count"><?php echo esc_html( $category->count ); ?></span>
                    <?php } ?>
                </a>
            </figure>
                
            <h3>
                <a href="<?php echo esc_url( get_term_link( $category->term_id ) ); ?>">
                    <?php echo esc_html( $category->name ); ?>
                </a>
            </h3>
        </li>
        <?php
    }
    echo '</ul>';
}

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */