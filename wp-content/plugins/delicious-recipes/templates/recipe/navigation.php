<?php
/**
 * Recipe navigation block.
 */

// Get global settings.
$global_settings = delicious_recipes_get_global_settings();

$enable_navigation = isset( $global_settings['enableNavigation']['0'] ) && 'yes' === $global_settings['enableNavigation']['0'] ? true : false;
$enable_autoload = isset( $global_settings['autoloadRecipes']['0'] ) && 'yes' === $global_settings['autoloadRecipes']['0'] ? true : false;

if( ! $enable_navigation && ! $enable_autoload ) {
    return;
}

$nav_style = ! $enable_navigation ? 'display:none;' : 'display:block;';

$prev_post = get_previous_post();
$next_post = get_next_post();

if( ! empty( $prev_post ) || ! empty( $next_post ) ) {
?>
    <nav class="post-navigation pagination" style="<?php echo esc_attr( $nav_style ); ?>">
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
