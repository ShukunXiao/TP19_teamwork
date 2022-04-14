<?php
/**
 * Recipe Like button.
 * 
 * @package Delicious_Recipes
 */

$classes = array( 'dr_wishlist__recipe' );

if ( $bookmarked ): $classes[] = $bookmarked; endif;
if ( $logged_in ): 
    $classes[] = 'dr-bookmark-wishlist'; 
else: 
    $classes[] = 'dr-popup-user__registration'; 
endif;

$wishlist_classes = implode( ' ', $classes );

if( $recipe_single ) : echo '<div class="dr-add-to-wishlist-single">'; endif;
?>
    <div class="dr-recipe-wishlist">
        <span id="dr-wishlist-id-<?php echo esc_attr( $id ); ?>" data-recipe-id="<?php echo esc_attr( $id ) ?>" class="<?php echo esc_attr( $wishlist_classes ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <g id="bookmark-icon" transform="translate(-599.281 -1176)">
                    <circle id="Ellipse_104" data-name="Ellipse 104" cx="12" cy="12" r="12" transform="translate(599.281 1176)" fill="#fff"/>
                    <path id="Path_30691" data-name="Path 30691" d="M16.308,5h-9.9A.412.412,0,0,0,6,5.412V16.957a.412.412,0,0,0,.676.317l4.684-3.9,4.684,3.9a.412.412,0,0,0,.264.1.418.418,0,0,0,.175-.039.412.412,0,0,0,.237-.374V5.412A.412.412,0,0,0,16.308,5ZM15.9,16.078l-4.272-3.56a.412.412,0,0,0-.528,0l-4.272,3.56V5.825H15.9Z" transform="translate(600.281 1177)" fill="#232323"/>
                </g>
            </svg>
            <span class="dr-wishlist-total"><?php echo esc_html( $wishlists_count ); ?></span>
            <span class="dr-wishlist-info"><?php echo esc_html( $add_to_wishlist_lbl ); ?></span>
        </span>
    </div>
<?php
if( $recipe_single ) : echo '</div>'; endif;
