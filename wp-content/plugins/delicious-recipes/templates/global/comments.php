<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Delicious_Recipes
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
global $recipe;
?>

<div id="comments-<?php echo esc_attr($recipe->ID); ?>" class="comments-area">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments( $recipe->ID ) ) : ?>
		<h2 class="comments-title">
			<?php
				printf( // WPCS: XSS OK.
					/* translators: 1: comment count number. */
					esc_html( _nx( '%1$s Comment', '%1$s Comments', get_comments_number(), 'comments title', 'delicious-recipes' ) ),
					number_format_i18n( get_comments_number() )
				);
			?>
		</h2><!-- .comments-title -->

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'      => 'ol',
					'short_ping' => true,
                    'callback'   => 'delicious_recipes_comments_callback',
                    'avatar_size' => 50,
				) );
			?>
		</ol><!-- .comment-list -->

		<?php the_comments_navigation( $recipe->ID );

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open( $recipe->ID ) ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'delicious-recipes' ); ?></p>
		<?php
		endif;

	endif; // Check for have_comments().

	comment_form();
	?>

</div><!-- #comments -->
