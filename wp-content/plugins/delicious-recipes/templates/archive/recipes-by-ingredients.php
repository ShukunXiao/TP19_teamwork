<?php
/**
 * Recipes By Ingredients
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipe/archive/recipes-by-ingredients.php.
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

$global_settings        = delicious_recipes_get_global_settings();
$recipe_search_page_url = isset( $global_settings['searchPage'] ) && ! empty( $global_settings['searchPage'] ) ? get_the_permalink( $global_settings['searchPage'] ) : '#';

$ingredients_array = delicious_recipes_get_all_ingredients();
// Sort ingredients alphabetically.
ksort( $ingredients_array );

if( empty( $ingredients_array ) ) {
    return;
}

// Set defaults.
$ingredients = array();
$index       = 0;

$intl_extension_enabled = extension_loaded("intl");

if( $intl_extension_enabled ) {
    $locale    = get_locale();
    $collator  = new \Collator( $locale );
    $sortarray = array_keys( $ingredients_array );
    $collator->asort( $sortarray );


    // Loop through ingredients array to prepare alphabetical collection.
    foreach( $sortarray as $key => $ingredient ) {
        if( array_key_exists( $ingredient, $ingredients_array ) ){
            $acronym = mb_substr( remove_accents( $ingredient ), 0, 1 );
            $ingredients[ strtoupper( $acronym ) ][$index] = array (
                'ingredient' => $ingredient,
                'count'      => $ingredients_array[$ingredient]
            );
        }
        $index++;
    }
} 
else {
    foreach ( $ingredients_array as $ingredient => $count ) {
        if ( isset($ingredient[ 0 ] ) ) {
            $ingredients[ $ingredient[ 0 ] ][$index] = array (
                'ingredient' => $ingredient,
                'count'      => $count
            );
        }
        $index++;
    }
}

$alphabets     = array_keys( $ingredients );
$all_alphabets = array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' );
?>
<div class="dr-archive-recipe-ingrd">
    <div class="dr-title">
        <?php esc_html_e( 'Recipes by Ingredient:', 'delicious-recipes' ); ?>
    </div>
    <ul>
        <?php 
            foreach( $all_alphabets as $alphabet ) {
                $link_class = in_array( $alphabet, array_values( $alphabets ) ) ? 'dr-link' : 'dr-link-disabled';
                $link_href  = in_array( $alphabet, array_values( $alphabets ) ) ? '#alphabet-'. esc_attr( $alphabet ) : '#';

                echo '<li><a class="'. esc_attr( $link_class ) .'" href="'. esc_attr( $link_href ) .'">' . esc_html( $alphabet ) . '</a></li>';
            } 
        ?>
    </ul>
</div>

<?php foreach( $ingredients as $alphabet => $ingredient_by_alphabet ) {
    ?>
    <div class="dr-archive-recipe-by-alph">
        <div id="alphabet-<?php echo esc_attr( $alphabet ); ?>" class="dr-title-alpha">
            <?php echo esc_html( $alphabet ); ?>
        </div>
        <ul>
            <?php foreach( $ingredient_by_alphabet as $ingredient ) { 
                    $ingre_search_url = add_query_arg( 'ingredient', $ingredient['ingredient'], $recipe_search_page_url );
                ?>
                    <li>
                        <a href="<?php echo esc_url( $ingre_search_url ); ?>"><?php echo esc_html( $ingredient['ingredient'] ) ?>
                            <span class="dr-recp-count"><?php echo sprintf( '(%1$s)', esc_html( $ingredient['count'] ) ); ?></span>
                        </a>
                    </li>
            <?php } ?>
        </ul>
    </div>
    <?php 
} ?>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
