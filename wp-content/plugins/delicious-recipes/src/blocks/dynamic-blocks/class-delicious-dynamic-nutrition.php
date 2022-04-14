<?php
/**
 * Nutrition Block
 *
 * @since   1.2.0
 * @package Delicious_Recipes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Delicious_Dynamic_Nutrition Class.
 */
class Delicious_Dynamic_Nutrition {
	/**
	 * Class instance Helpers.
	 *
	 * @var Delicious_Recipes_Helpers
	 * @since 1.0.3
	 */
	public static $helpers;

	/**
	 * Block attributes.
	 *
	 * @since 1.1.0
	 */
	public static $attributes;

	/**
	 * Block data.
	 *
	 * @since 2.3.2
	 */
	public static $data;

	/**
	 * Nutrition facts labels
	 *
	 * @since 2.3.2
	 */
	public static $labels;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		self::$helpers = new Delicious_Recipes_Helpers();
		self::set_labels();
	}

	/**
	 * Registers the nutrition block as a server-side rendered block.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		if ( delicious_recipes_block_is_registered( 'delicious-recipes/block-nutrition' ) ) {
			return;
		}

		$attributes = array(
			'id' => array(
				'type' => 'string',
				'default' => 'dr-block-nutrition'
			),
			'data' => array(
                'type' => 'object',
            ),
		);

		// Hook server side rendering into render callback
		register_block_type(
			'delicious-recipes/block-nutrition', array(
				'attributes' => $attributes,
				'render_callback' => array( $this, 'render' ),
		) );
	}

	/**
	 * Renders the block.
	 *
	 * @param array  $attributes The attributes of the block.
	 * @param string $content    The HTML content of the block.
	 *
	 * @return string The block preceded by its JSON-LD script.
	 */
	public function render( $attributes, $content ) {
		if ( ! is_array( $attributes ) ) {
			return $content;
		}

		if ( ! isset( $attributes['data'] ) ) {
			return $content;
		}

		// Import variables into the current symbol table from an array
		extract( $attributes );

		// Store variables
		self::$data       = $data;
		self::$attributes = $attributes;

		$class = 'dr-nutrition-facts';

		$recipe_global       = delicious_recipes_get_global_settings();
		$nutri_title         = isset( $recipe_global['nutritionFactsLabel'] ) ? $recipe_global['nutritionFactsLabel'] : __( 'Nutrition Facts', 'delicious-recipes' );
		$displayStandardMode = isset( $recipe_global['displayStandardMode']['0'] ) && 'yes' === $recipe_global['displayStandardMode']['0'] ? true : false;
		$style               = $displayStandardMode ? 'style=background:#000000;' : '';

		$fetched_nutrition_facts = self::get_nutrition_facts();

		$block_content = sprintf(
			'<div id="%1$s" class="%2$s">
				<div class="dr-title-wrap" %3$s>
					<div class="dr-title dr-print-block-title">
						<b>%4$s</b>
					</div>
				</div>
				<div class="dr-nutrition-list">
					%5$s
				</div>
			</div>',
			esc_attr( $id ),
			esc_attr( $class ),
			esc_attr( $style ),
			esc_html( $nutri_title ),
			$fetched_nutrition_facts
		);

		return $block_content;
	}

	public static function get_nutrition_facts() {
		$nutrition_facts = self::$data;
		$_nf_fields      = delicious_recipes_get_nutrition_facts();

		$recipe_global          = delicious_recipes_get_global_settings();
		$daily_value_disclaimer = isset( $recipe_global['dailyValueDisclaimer'] ) && '' != $recipe_global['dailyValueDisclaimer'] ? $recipe_global['dailyValueDisclaimer'] : __( 'Percent Daily Values are based on a 2,000 calorie diet. Your daily value may be higher or lower depending on your calorie needs.', 'delicious-recipes' );
		$enable_nutrition_facts = isset( $recipe_global['showNutritionFacts']['0'] ) && 'yes' === $recipe_global['showNutritionFacts']['0'] ? true : false;

		$displayNutritionZeroValues = isset( $recipe_global['displayNutritionZeroValues']['0'] ) && 'yes' === $recipe_global['displayNutritionZeroValues']['0'] ? true : false;

		$nutri_filtered = array_filter( $nutrition_facts, function( $nut ) {
			return ! empty( $nut ) && false !== $nut;
		} );

		if ( empty( $nutri_filtered ) ) {
			return;
		}

		if( ! $enable_nutrition_facts ) {
			return;
		}

		$displayStandardMode = isset( $recipe_global['displayStandardMode']['0'] ) && 'yes' === $recipe_global['displayStandardMode']['0'] ? true : false;
		$style = $displayStandardMode ? 'style=background:#000000;' : '';
		$style_hr = $displayStandardMode ? 'style=border-color:#000000;' : '';

		ob_start();
		if ( $nutrition_facts ) :
			$top_facts = $_nf_fields['top'];
			if ( ! empty( $top_facts ) ):

				// Start output buffer for top facts.
				ob_start();

				foreach( $top_facts as $slug => $nf ):
					$nutriZero_condition = $displayNutritionZeroValues ? isset( $nutrition_facts[$slug] ) && $nutrition_facts[$slug] == 0 : false;
					if ( isset( $nutrition_facts[$slug] ) && $nutrition_facts[$slug] || $nutriZero_condition ):
					echo '<p>' . esc_html( $nf['name'] ) . ' <strong class="dr-nut-label" data-labeltype="' . esc_attr( $slug ) . '">' . ( esc_attr( $nutrition_facts[$slug] ) ) . '</strong></p>';
					endif;
				endforeach;

				// Get top facts content from buffer.
				$top_facts_content = ob_get_clean();

			endif;

			$mid_facts = $_nf_fields['mid'];
			if (!empty($mid_facts)):

				// Start output buffer for mid-facts.
				ob_start();

				foreach( $mid_facts as $slug => $nf ):
					$nutriZero_condition = $displayNutritionZeroValues ? isset( $nutrition_facts[$slug] ) && $nutrition_facts[$slug] == 0 : false;
					if ( isset( $nutrition_facts[$slug] ) && $nutrition_facts[$slug] || $nutriZero_condition ):
						if ( $slug != 'calories_fat' ):
							echo '<dt><strong>' . esc_html( $nf['name'] ) . '</strong> <strong class="dr-nut-label">' . esc_attr( $nutrition_facts[$slug] ) . '</strong>';
								if ( isset($nutrition_facts['calories_fat']) && $nutrition_facts['calories_fat'] ):
									echo '<span class="dr-calories-fat dr-right">' . esc_attr($mid_facts['calories_fat']['name']) . ' ' . esc_attr( $nutrition_facts['calories_fat'] ) . '</span>';
								endif;
							echo '</dt>';
						endif;
					endif;
				endforeach;

				// Get mid facts content from buffer.
				$mid_facts_content = ob_get_clean();

			endif;

			$main_facts = $_nf_fields['main'];
			$nut_loops = 0;

			if (!empty($main_facts)):

				// Start output buffer for main facts.
				ob_start();

				foreach( $main_facts as $slug => $nf ):
					$nutriZero_condition = $displayNutritionZeroValues ? isset( $nutrition_facts[$slug] ) && $nutrition_facts[$slug] == 0 : false;
					if ( isset( $nutrition_facts[$slug] ) && $nutrition_facts[$slug] || $nutriZero_condition ):

					echo '<dt>';
					echo '<strong>' . esc_html( $nf['name'] ) . '</strong> <strong class="dr-nut-label">' . esc_attr( $nutrition_facts[$slug] ) . '</strong>' . ( isset($nf['measurement']) ? '<strong class="dr-nut-label dr-nut-measurement">' . esc_attr( $nf['measurement'] ) . '</strong>' : '' );
					echo ( isset( $nf['pdv'] ) && $nutrition_facts[$slug] ? '<strong class="dr-nut-right"><span class="dr-nut-percent">' . ceil( ( esc_attr( $nutrition_facts[$slug] ) / $nf['pdv'] ) * 100 ) . '</span>%</strong>' : '' );

					if ( isset($nf['subs']) ):
						foreach( $nf['subs'] as $sub_slug => $sub_nf ):
							$nutriZero_condition = $displayNutritionZeroValues ? isset( $nutrition_facts[$sub_slug] ) && $nutrition_facts[$sub_slug] == 0 : false;
							if ( isset( $nutrition_facts[$sub_slug] ) && $nutrition_facts[$sub_slug] || $nutriZero_condition ):
							echo '<dl><dt>';
								echo '<strong>' . esc_html( $sub_nf['name'] ) . '</strong> <strong class="dr-nut-label">' . $nutrition_facts[$sub_slug] . '</strong>' . ( isset($sub_nf['measurement']) ? '<strong class="dr-nut-label dr-nut-measurement">' . $sub_nf['measurement'] . '</strong>' : '' );
								echo ( isset( $sub_nf['pdv'] ) && $nutrition_facts[$sub_slug] ? '<strong class="dr-nut-right"><span class="dr-nut-percent">' . ceil( ( esc_attr( $nutrition_facts[$sub_slug] ) / $sub_nf['pdv'] ) * 100 ) . '</span>%</strong>' : '' );
							echo '</dt></dl>';
							endif;
						endforeach;
					endif;

					echo '</dt>';

					endif;

				endforeach;

				// Get main facts content from buffer.
				$main_facts_content = ob_get_clean();

			endif;

			$bottom_facts = $_nf_fields['bottom'];

			if (!empty($bottom_facts)):

				// Start output buffer for bottom facts.
				ob_start();

				foreach( $bottom_facts as $slug => $nf ):
					$nutriZero_condition = $displayNutritionZeroValues ? isset( $nutrition_facts[$slug] ) && $nutrition_facts[$slug] == 0 : false;
					if ( isset( $nutrition_facts[$slug] ) && $nutrition_facts[$slug] || $nutriZero_condition ):
						echo '<dt>';
							echo '<strong>' . esc_html( $nf['name'] ) . ' <span class="dr-nut-percent dr-nut-label">' . esc_attr( $nutrition_facts[$slug] ) . '</span>%</strong>';
						echo '</dt>';
					endif;
				endforeach;

				// Get bottom facts content from buffer.
				$bottom_facts_content = ob_get_clean();

			endif;

			// Start a buffer for all nutrition facts content
			ob_start();

			if ( isset($top_facts_content) && $top_facts_content ):
				echo $top_facts_content;
			endif;

			if ( isset($mid_facts_content) && $mid_facts_content || isset($main_facts_content) && $main_facts_content ):

				echo '<hr class="dr-nut-hr" ' . esc_attr( $style_hr ) . ' />';
				echo '<dl>';

					echo '<dt><strong class="dr-nut-heading">' . esc_html__('Amount Per Serving','delicious-recipes') . '</strong></dt>';

					if ( isset($mid_facts_content) && $mid_facts_content ):
						echo '<section class="dr-clearfix">';
							echo $mid_facts_content;
						echo '</section>';
					endif;

					if ( isset($main_facts_content) && $main_facts_content ):
						echo '<dt class="dr-nut-spacer" '. esc_attr( $style ) .'></dt>';
						echo '<dt class="dr-nut-no-border"><strong class="dr-nut-heading dr-nut-right">' . esc_html__('% Daily Value *','delicious-recipes'). '</strong></dt>';
						echo '<section class="dr-clearfix">';
							echo $main_facts_content;
						echo '</section>';
					endif;

				echo '</dl>';
				echo '<hr class="dr-nut-hr" ' . esc_attr( $style_hr ) . ' />';

			endif;

			if ( isset($bottom_facts_content) && $bottom_facts_content ):
				echo '<dl class="dr-nut-bottom dr-clearfix">';
					echo $bottom_facts_content;
				echo '</dl>';
			endif;

			$nutrition_facts_content = ob_get_clean();

			if ( isset($nutrition_facts_content) && $nutrition_facts_content ):

				echo '<div class="dr-nutrition-label">';
					echo $nutrition_facts_content;
					if ( isset( $main_facts_content ) && $main_facts_content || isset( $bottom_facts_content ) && $bottom_facts_content ):
						echo '<p class="dr-daily-value-text">* ' . esc_html( $daily_value_disclaimer ) . '</p>';
					endif;
				echo '</div>';

			endif;

		endif;
		$content = ob_get_clean();
		return $content;

	}

	public static function get_labels() {
		$labels = array(
			array(
				'id'    => 'servingSize',
				'label' => __( 'Serving Size', "delicious-recipes" ),
				'type'  => 'text'
			),
			array(
				'id' 	=> 'servings',
			    'label' => __( 'Servings', "delicious-recipes" )
			),
			array(
				'id' 	=> 'calories',
			    'label' => __( 'Calories', "delicious-recipes" )
			),
			array(
				'id' 	=> 'caloriesFromFat',
			    'label' => __( 'Calories from Fat', "delicious-recipes" )
			),
			array(
				'id' 	=> 'totalFat',
			    'label' => __( 'Total Fat', "delicious-recipes" ),
			    'pdv' 		=> 65
			),
			array(
				'id' 	=> 'saturatedFat',
			    'label' => __( 'Saturated Fat', "delicious-recipes" ),
			    'pdv' 		=> 20
			),
			array(
				'id' 	=> 'transFat',
			    'label' => __( 'Trans Fat', "delicious-recipes" )
			),
			array(
				'id' 	=> 'cholesterol',
			    'label' => __( 'Cholesterol', "delicious-recipes" ),
			    'pdv' 		=> 300
			),
			array(
				'id' 	=> 'sodium',
			    'label' => __( 'Sodium', "delicious-recipes" ),
			    'pdv' 		=> 2400
			),
			array(
				'id' 	=> 'potassium',
			    'label' => __( 'Potassium', "delicious-recipes" ),
			    'pdv' 		=> 3500
			),
			array(
				'id' 	=> 'totalCarbohydrate',
			    'label' => __( 'Total Carbohydrate', "delicious-recipes" ),
			    'pdv' 		=> 300
			),
			array(
				'id' 	=> 'dietaryFiber',
			    'label' => __( 'Dietary Fiber', "delicious-recipes" ),
			    'pdv' 		=> 25
			),
			array(
				'id' 	=> 'sugars',
			    'label' => __( 'Sugars', "delicious-recipes" )
			),
			array(
				'id' 	=> 'protein',
			    'label' => __( 'Protein', "delicious-recipes" ),
			    'pdv' 		=> 50
			),
			array(
				'id' 	=> 'vitaminA',
			    'label' => __( 'Vitamin A', "delicious-recipes" )
			),
			array(
				'id' 	=> 'vitaminC',
			    'label' => __( 'Vitamin C', "delicious-recipes" )
			),
			array(
				'id' 	=> 'calcium',
			    'label' => __( 'Calcium', "delicious-recipes" )
			),
			array(
				'id' 	=> 'iron',
			    'label' => __( 'Iron', "delicious-recipes" )
			),
			array(
				'id' 	=> 'vitaminD',
			    'label' => __( 'Vitamin D', "delicious-recipes" )
			),
			array(
				'id' 	=> 'vitaminE',
			    'label' => __( 'Vitamin E', "delicious-recipes" )
			),
			array(
				'id' 	=> 'vitaminK',
			    'label' => __( 'Vitamin K', "delicious-recipes" )
			),
			array(
				'id' 	=> 'thiamin',
			    'label' => __( 'Thiamin', "delicious-recipes" )
			),
			array(
				'id' 	=> 'riboflavin',
			    'label' => __( 'Riboflavin', "delicious-recipes" )
			),
			array(
				'id' 	=> 'niacin',
			    'label' => __( 'Niacin', "delicious-recipes" )
			),
			array(
				'id' 	=> 'vitaminB6',
			    'label' => __( 'Vitamin B6', "delicious-recipes" )
			),
			array(
				'id' 	=> 'vitaminB12',
			    'label' => __( 'Vitamin B12', "delicious-recipes" )
			),
			array(
				'id' 	=> 'folate',
			    'label' => __( 'Folate', "delicious-recipes" )
			),
			array(
				'id' 	=> 'biotin',
			    'label' => __( 'Biotin', "delicious-recipes" )
			),
			array(
				'id' 	=> 'pantothenicAcid',
			    'label' => __( 'Pantothenic Acid', "delicious-recipes" )
			),
			array(
				'id' 	=> 'phosphorus',
			    'label' => __( 'Phosphorus', "delicious-recipes" )
			),
			array(
				'id' 	=> 'iodine',
			    'label' => __( 'Iodine', "delicious-recipes" )
			),
			array(
				'id' 	=> 'magnesium',
			    'label' => __( 'Magnesium', "delicious-recipes" )
			),
			array(
				'id' 	=> 'zinc',
			    'label' => __( 'Zinc', "delicious-recipes" )
			),
			array(
				'id' 	=> 'selenium',
			    'label' => __( 'Selenium', "delicious-recipes" )
			),
			array(
				'id' 	=> 'copper',
			    'label' => __( 'Copper', "delicious-recipes" )
			),
			array(
				'id' 	=> 'manganese',
			    'label' => __( 'Manganese', "delicious-recipes" )
			),
			array(
				'id' 	=> 'chromium',
			    'label' => __( 'Chromium', "delicious-recipes" )
			),
			array(
				'id' 	=> 'molybdenum',
			    'label' => __( 'Molybdenum', "delicious-recipes" )
			),
			array(
				'id' 	=> 'chloride',
			    'label' => __( 'Chloride', "delicious-recipes" )
			)
		);
		
		return $labels;
	}

	public static function set_labels() {
		self::$labels = self::get_labels();
	}

	public static function get_label_title( $label ) {
		$key = array_search( $label, array_column( self::$labels, 'id' ) );

		return self::$labels[ $key ]['label'];
	}

	public static function get_label_pdv( $label ) {
		$key = array_search( $label, array_column( self::$labels, 'id' ) );

		return self::$labels[ $key ]['pdv'];
	}

}