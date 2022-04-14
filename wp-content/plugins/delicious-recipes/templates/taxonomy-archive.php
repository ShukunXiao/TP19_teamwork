<?php
/**
 * The Template for displaying recipe archives.
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipe/archive-recipe.php.
 *
 * HOWEVER, on occasion WP Delicious will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://wpdelicious.com/docs/template-structure/
 * @package     Delicious_Recipes/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$global_settings   = delicious_recipes_get_global_settings();
$showArchiveHeader = isset( $global_settings['enableArchiveHeader']['0'] ) && 'yes' === $global_settings['enableArchiveHeader']['0'] ? true : false;

get_header(); 

if ( $showArchiveHeader ) :
?>
	<header class="page-header">
		<div class="container">
			<?php the_archive_title(); ?>
			<?php the_archive_description( '<div class="archive-description" itemprop="description">', '</div>' ); ?>
			<?php 
				global $wp_query;
					$paged      = !empty( $wp_query->query_vars['paged'] ) ? $wp_query->query_vars['paged'] : 1;
					$prev_posts = ( $paged - 1 ) * $wp_query->query_vars['posts_per_page'];
					$from       = 1 + $prev_posts;
					$to         = count( $wp_query->posts ) + $prev_posts;
					$of         = $wp_query->found_posts;

				if ( 0 < $of ) :
					?>
						<span class="dr-showing-results"><?php 
							/* translators: %1$d: start count %2$d: start count + posts per page %3$d: total recipes count */
							printf( __( '%1$d - %2$d of %3$d Recipes', 'delicious-recipes' ), absint( $from ), absint( $to ), absint( $of ) ); 
						?></span>
					<?php
				endif;
			?>
		</div>
	</header>
<?php 
endif;

?>
	<div class="recipe-archive">
		<div id="primary" class="content-area">
			<main id="main" class="site-main">
				<div id="dr-recipe-archive" class="dr-archive-list-wrapper">
					<div class="dr-archive-list-gridwrap" itemscope itemtype="http://schema.org/ItemList">
						<?php
							$position = 1; 
							$view_type = delicious_recipes_get_archive_layout();
							if ( have_posts() ) {
								while ( have_posts() ) {
									the_post();

									/**
									 * Hook: delicious_recipe_archive_loop.
									 */
									do_action( 'delicious_recipe_archive_loop' );

									$data = array(
										'position'  => $position
									);
					
									delicious_recipes_get_template( 'recipes-' . $view_type . '.php', $data );

									$position++;

								} // end of the loop. 
								
							} else {
								esc_html_e( 'No recipes found.', 'delicious-recipes' );
							}
						?>
					</div>
					<?php
						/**
						 * Get archive pagination.
						 */ 
						delicious_recipes_get_template( 'archive/pagination.php' );
					?>
				</div>
			</main>
		</div>
		<?php
			/**
			 * delicious_recipes_sidebar hook.
			 *
			 * @hooked delicious_recipes_get_sidebar - 10
			 */
			do_action( 'delicious_recipes_sidebar' );
		?>
	</div>
<?php
get_footer();

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
