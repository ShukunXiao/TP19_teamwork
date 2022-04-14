<?php
/**
 * Recipe print button.
 * 
 * @package Delicious_Recipes
 */
global $recipe;

$the_permalink       = get_the_permalink();
$recipe_servimgs     = isset( $recipe->no_of_servings ) && ! empty( $recipe->no_of_servings ) ? $recipe->no_of_servings : 1;
$the_print_permalink = add_query_arg( array( 'print_recipe' => 'true', 'recipe_servings' => absint( $recipe_servimgs ) ), $the_permalink );

// Get global toggles.
$global_toggles  = delicious_recipes_get_global_toggles_and_labels();
?>
    <a id="dr-single-recipe-print-<?php echo esc_attr($recipe->ID); ?>" href="<?php echo esc_url( $the_print_permalink ); ?>" class="dr-print-trigger dr-btn-link dr-btn2"><svg class="icon"><use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#print"></use></svg><?php echo esc_html( $global_toggles['print_recipe_lbl'] ); ?></a>
<?php
