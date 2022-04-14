<?php
/**
 * Recipe Archive Pagination
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipe/archive/pagination.php.
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

if( is_post_type_archive( DELICIOUS_RECIPE_POST_TYPE ) ) {
	echo '</div>';
}
?>
<div class="dr-archive-pagination">
	<?php
		$pagination = get_the_posts_pagination( array(
			'mid_size'           => 2,
			'prev_text'          => __( 'Previous', 'delicious-recipes' ),
			'next_text'          => __( 'Next', 'delicious-recipes' ),
			'before_page_number' => '<span class="meta-nav screen-reader-text dr-meta-nav">' . __( 'Page', 'delicious-recipes' ) . ' </span>',
		) );

		$class_arrays = array(
			'class="navigation pagination"' => 'class="dr-navigation pagination"',
			'class="nav-links"'             => 'class="dr-nav-links"',
			'class="page-numbers"'          => 'class="dr-page-numbers"',
			'class="page-numbers current"'  => 'class="dr-page-numbers current"',
			'class="prev page-numbers"'     => 'class="dr-page-numbers prev"',
			'class="page-numbers next"'     => 'class="dr-page-numbers next"',
			'class="next page-numbers"'     => 'class="dr-page-numbers next"',
		);

		$pagination = str_replace( array_keys( $class_arrays ), $class_arrays, $pagination );

		echo wp_kses_post( $pagination );
	?>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
