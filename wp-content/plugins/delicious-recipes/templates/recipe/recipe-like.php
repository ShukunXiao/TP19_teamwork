<?php
/**
 * Recipe Like button.
 * 
 * @package Delicious_Recipes
 */
$title_text = $args['can_like'] ? __( 'Like Recipe', 'delicious-recipes' ) : sprintf( _nx( '%s Like', '%s Likes', $args['like_count'], 'number of likes', 'delicious-recipes' ), $args['like_count'] );
$liked      = $args['can_like'] ? 'dr_like__recipe like-recipe' : 'dr_like__recipe recipe-liked';
?>
<div class="post-like">
    <span class="favourite single-like like">
        <span id="recipe-<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $liked ); ?>" title="<?php echo esc_attr( $title_text ); ?>" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="17.928" height="17.058" viewBox="0 0 17.928 17.058"><path d="M24.445,20A4.434,4.434,0,0,0,20,24.445c0,5,5.038,6.3,8.445,11.223,3.26-4.889,8.482-6.408,8.482-11.223a4.453,4.453,0,0,0-8.482-1.889A4.39,4.39,0,0,0,24.445,20Z" transform="translate(-19.5 -19.5)" fill="none" stroke="#374757" stroke-width="1" /></svg>
            <span class="dr-likes-total"><?php echo esc_html( $like_count ); ?></span>
        </span>
    </span>
</div>
<?php
