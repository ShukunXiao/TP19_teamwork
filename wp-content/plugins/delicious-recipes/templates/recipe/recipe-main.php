<?php
/**
 * Recipe main meta content block template.
 * 
 * @package Delicious_Recipes
 */
global $recipe;
$global_settings = delicious_recipes_get_global_settings();
$ingredientTitle = isset( $recipe->ingredient_title ) ? $recipe->ingredient_title : __( 'Ingredients', 'delicious-recipes' );
$layout          = isset( $layout ) ? $layout : '';

?>
<div id="dr-recipe-meta-main-<?php echo esc_attr($recipe->ID); ?>" class="dr-summary-holder">
    <?php 
        /**
         * Recipe before main summary hook.
         */
        do_action( 'delicious_recipes_before_main_summary' );
    ?>
    
    <?php 
        /**
         * Recipe main summary hook.
         */
        do_action( 'delicious_recipes_main_summary', $layout );
    ?>

    <?php 
        /**
         * Recipe after main summary hook.
         */
        do_action( 'delicious_recipes_after_main_summary' );
    ?>

    <?php 
        /**
         * Recipe ingredients hook.
         */
        do_action( 'delicious_recipes_ingredients' );
    ?>

    <?php 
        /**
         * Recipes instructions hook.
         */
        do_action( 'delicious_recipes_instructions' );
    ?>

    <?php 
        /**
         * Recipe after ingredients hook.
         */
        do_action( 'delicious_recipes_after_instructions' );
    ?>

    <?php 
        /**
         * Recipe nutrition hooks.
         */
        do_action( 'delicious_recipes_nutrition' );
    ?>

    <?php 
        /**
         *  Recipe after nutrition hooks.
         */
        do_action( 'delicious_recipes_after_nutrition' );
    ?>

    <?php 
        /**
         *  Recipe notes hooks.
         */
        do_action( 'delicious_recipes_notes' );
    ?>

    <?php 
        /**
         *  Recipe after nutrition hooks.
         */
        do_action( 'delicious_recipes_after_notes' );
    ?>
</div>