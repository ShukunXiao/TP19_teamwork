<?php
/**
 * Init dynamic blocks.
 * 
 * @package Delicious_Recipes/SRC/BLOCKS
 */
// ===== Block Dynamic recipe card. ======== //
require_once dirname( __FILE__ ) . '/class-delicious-dynamic-recipe-card.php';
$dynm_recipe_card = new Delicious_Dynamic_Recipe_Card();
$dynm_recipe_card->register_hooks();

// ===== Block Dynamic Ingredients. ======== //
require_once dirname( __FILE__ ) . '/class-delicious-dynamic-ingredients.php';
$dynm_ingredients = new Delicious_Dynamic_Ingredients();
$dynm_ingredients->register_hooks();

// ===== Block Dynamic Instructions. ======== //
require_once dirname( __FILE__ ) . '/class-delicious-dynamic-instructions.php';
$dynm_instructions = new Delicious_Dynamic_Instructions();
$dynm_instructions->register_hooks();

// ===== Block Dynamic Details. ======== //
require_once dirname( __FILE__ ) . '/class-delicious-dynamic-details.php';
$dynm_details = new Delicious_Dynamic_Details();
$dynm_details->register_hooks();

// ===== Block Dynamic Nutrition. ======== //
require_once dirname( __FILE__ ) . '/class-delicious-dynamic-nutrition.php';
$dynm_nutrition = new Delicious_Dynamic_Nutrition();
$dynm_nutrition->register_hooks();

// ===== Block Recipe Buttons. ======== //
require_once dirname( __FILE__ ) . '/class-delicious-recipe-buttons.php';
$dynm_buttons = new Delicious_Recipe_Buttons();
$dynm_buttons->register_hooks();