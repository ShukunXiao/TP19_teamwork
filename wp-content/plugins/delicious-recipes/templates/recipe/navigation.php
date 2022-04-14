<?php
/**
 * Recipe navigation block.
 */

// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();

if( ! $global_toggles['enable_navigation'] ) {
    return;
}

$prev_post = get_previous_post();
$next_post = get_next_post();

if( ! empty( $prev_post ) || ! empty( $next_post ) ) {
?>
    <nav class="post-navigation pagination">
        <div class="nav-links">
<?php
    if( ! empty( $prev_post ) ) { ?>
        <div class="nav-previous">
            <?php
                $data          = array(
                    'recipe_id' => absint( $prev_post->ID ),
                    'title'     => esc_html( $prev_post->post_title ),
                    'a_rel'     => 'prev',
                    'nav_text'  => __( 'Previous', 'delicious-recipes' )
                );
                /**
                * Recipe content template load.
                */
                delicious_recipes_get_template( 'recipe/navigation-content.php', $data );
            ?>
        </div>
    <?php }
    if( ! empty( $next_post ) ) { ?>
        <div class="nav-next">
            <?php
                $data          = array(
                    'recipe_id' => absint( $next_post->ID ),
                    'title'     => esc_html( $next_post->post_title ),
                    'a_rel'     => 'next',
                    'nav_text'  => __( 'Next', 'delicious-recipes' )
                );
                /**
                * Recipe content template load.
                */
                delicious_recipes_get_template( 'recipe/navigation-content.php', $data );
            ?>
        </div>
    <?php }
?>
        </div>
    </nav>
<?php
}
