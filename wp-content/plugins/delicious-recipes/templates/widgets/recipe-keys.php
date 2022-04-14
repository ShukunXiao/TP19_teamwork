<?php
/**
 * Recipe Keys
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipe/widgets/recipe-keys.php.
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

if( $taxonomy != '' && is_array( $recipe_keys ) && ! empty( $recipe_keys ) ) {
    
    echo '<ul>';
    foreach( $recipe_keys as $key ) {
        $recipe_key_metas = get_term_meta( $key->term_id, 'dr_taxonomy_metas', true );
        $key_svg          = isset( $recipe_key_metas['taxonomy_svg'] ) ? $recipe_key_metas['taxonomy_svg'] : '';

        ?>
        <li>
            <a href="<?php echo esc_url( get_term_link( $key->term_id ) ); ?>">
                <span class="dr-svg-icon">
                    <?php delicious_recipes_get_tax_icon( $key ); ?>
                </span>
                <span class="dr-icon-title">
                    <?php echo esc_html( $key->name ); ?>
                </span>
            </a>
        </li>
        <?php
    }
    echo '</ul>';
}

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */