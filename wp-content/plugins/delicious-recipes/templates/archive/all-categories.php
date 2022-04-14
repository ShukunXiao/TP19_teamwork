<?php
/**
 * Recipe All Categories
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipe/archive/all-categories.php.
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

$categories = get_categories( array (
    'taxonomy' => 'recipe-course',
    'orderby'  => 'name',
) );

if( empty( $categories ) ) {
    return;
}

?>
<div class="dr-archive-all-categories">
    <div class="dr-title">
        <?php 
            $title = __( 'All Categories:', 'delicious-recipes' ); 
            echo esc_html( apply_filters( 'wp_delicious_all_categories_title', $title ) ); 
        ?>
    </div>
    <div class="dr-archive-cat-wrap">
        <ul>
        <?php foreach( $categories as $category ) : ?>
            <li>
                <a href="<?php echo esc_url( get_category_link( $category->term_id ) ) ?>"
                    alt="<?php 
                        /* translators: %s: category name */
                        echo esc_attr( sprintf( __( 'View all recipes in %s', 'delicious-recipes' ), $category->name ) ); 
                    ?>">
                    <?php echo esc_html( $category->name ); ?>
                </a>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
