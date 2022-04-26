<?php
/**
 * Delecious recipes SEO handler class.
 * 
 * @package Delicious_Recipes
 */
namespace WP_Delicious;

defined( 'ABSPATH' ) || exit;

/**
 * Handle the SEO for frontend of Delicious_Recipes plugin
 * 
 * @since 1.0.0
 */
class Delicious_SEO {
    /**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
    }
    
    /**
	 * Initialization.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init() {
		
		// Initialize hooks.
		$this->init_hooks();

		// Allow 3rd party to remove hooks.
		do_action( 'wp_delicious_seo_unhook', $this );
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init_hooks() {

		//json ld recipe schema for display on Google Search and as a Guided Recipe on the Assistant.
        add_action( 'wp_delicious_guided_recipe_schema', array( $this, 'json_ld' ) );

	}

	/**
	 * json ld for single recipe.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function json_ld(){

		$schema_values_json     = json_encode( $this->schema_values() );
		$faq_schema_values_json = json_encode( $this->faq_schema_values() );

		$schema_html = '<script type="application/ld+json">';
        $schema_html .= $schema_values_json;
		$schema_html .= '</script>';

		echo apply_filters( 'wp_delicious_guided_recipe_schema_html', $schema_html );

        if( $faq_schema_values_json != "false" ) {
            $faq_schema_html = '<script type="application/ld+json">';
            $faq_schema_html .= $faq_schema_values_json;
            $faq_schema_html .= '</script>';

            echo apply_filters( 'wp_delicious_recipe_faq_schema_html', $faq_schema_html );
        }
    }
    
    /**
     * Get schema values.
     *
     * @param boolean $recipe
     * @return void
     */
	private function schema_values(){

        global $recipe;
        $recipe_global = delicious_recipes_get_global_settings();
        $recipe_meta   = get_post_meta(  $recipe->ID, 'delicious_recipes_metadata', true );

        $preptime_unit = isset( $recipe_meta['prepTimeUnit'] ) && $recipe_meta['prepTimeUnit'] ? $recipe_meta['prepTimeUnit'] : '';
		$cooktime_unit = isset( $recipe_meta['cookTimeUnit'] ) && $recipe_meta['cookTimeUnit'] ? $recipe_meta['cookTimeUnit'] : '';
		$resttime_unit = isset( $recipe_meta['restTimeUnit'] ) && $recipe_meta['restTimeUnit'] ? $recipe_meta['restTimeUnit'] : '';

		$PrepTimeMins = "min" === $preptime_unit ? $recipe->prep_time : $recipe->prep_time * 60;
		$CookTimeMins = "min" === $cooktime_unit ? $recipe->cook_time : $recipe->cook_time * 60;
		$RestTimeMins = "min" === $resttime_unit ? $recipe->rest_time : $recipe->rest_time * 60;

		$total_time = absint( $PrepTimeMins ) + absint( $CookTimeMins ) + absint( $RestTimeMins );

        $cook_time           = delicious_recipes_time_format( $CookTimeMins, 'iso' );
        $prep_time           = delicious_recipes_time_format( $PrepTimeMins, 'iso' );
        $total_time          = delicious_recipes_time_format( $total_time, 'iso' );
        $description         = $recipe->recipe_description ? wp_strip_all_tags( $recipe->recipe_description, true ) : $recipe->name;
        $description         = strip_shortcodes( $description );
        $recipe_instructions = array();
        $recipe_ingredients  = array();
        $images              = array();
        
        if( has_post_thumbnail( $recipe->ID ) ) :
            $size1  = get_the_post_thumbnail_url( $recipe->ID, 'delrecpe-structured-data-1_1' );
            $size2  = get_the_post_thumbnail_url( $recipe->ID, 'delrecpe-structured-data-4_3' );
            $size3  = get_the_post_thumbnail_url( $recipe->ID, 'delrecpe-structured-data-16_9' );
            $images = array( $size1, $size2, $size3 );
        endif;


        if ( isset( $recipe->ingredients ) && ! empty( $recipe->ingredients ) ):
            foreach ( $recipe->ingredients as $ingredients ):
                if ( isset( $ingredients['ingredients'] ) && ! empty( $ingredients['ingredients'] ) ):
                    foreach ( $ingredients['ingredients'] as $ing ):
                        if( isset( $ing['ingredient'] )  && ! empty( $ing['ingredient'] ) ):
                            unset( $ing['notes'] );
                            $ingredient_qty       = isset( $ing['quantity'] ) ? $ing['quantity'] : 0;
                            $ing['unit']          = ! empty( $ing['unit'] ) ? delicious_recipes_get_unit_text( $ing['unit'], $ingredient_qty ) : '';
                            $ingredient           = implode(' ', $ing );
							$ingredient           = strip_tags( preg_replace( "~(?:\[/?)[^/\]]+/?\]~s", '', $ingredient ) );
							$recipe_ingredients[] = $ingredient;
                        endif;
                    endforeach;
                endif;
            endforeach;
        endif;

        if ( isset( $recipe->instructions ) && ! empty( $recipe->instructions ) ):
            foreach( $recipe->instructions as $section ) :
                foreach ( $section['instruction'] as $dir ):
                    if( isset( $dir['instruction'])  && ! empty( $dir['instruction'] ) ):
                        $direction = strip_tags( preg_replace( "~(?:\[/?)[^/\]]+/?\]~s", '', $dir['instruction'] ) );
                        $name      = isset( $dir['instructionTitle'] ) ? strip_tags( preg_replace( "~(?:\[/?)[^/\]]+/?\]~s", '', $dir['instructionTitle'] ) ) : '';
                        $image     = isset( $dir['image_preview'] ) && '' != $dir['image_preview'] ? $dir['image_preview'] : '';
                        $recipe_instructions[] = array(
                            "@type" => "HowToStep",
                            "name"  => esc_html( $name ),
                            "text"  => $direction,
                            // "url"   => ,
                            "image" => $image
                        );
                    endif;
                endforeach;
            endforeach;
        endif;

        if( $recipe->rating != 0 ):
            $aggregateRating = array(
                '@type'       => 'AggregateRating',
                'ratingValue' => $recipe->rating,
                'ratingCount' => $recipe->rating_count
            );
        else:
            $aggregateRating = 0;
        endif;

        $nutrition = array();
        $nutrition_facts = $recipe->nutrition;
        $nutri_filtered = array_filter( $nutrition_facts, function( $nut ) {
            return ! empty( $nut ) && false !== $nut;
        } );
        $enable_nutrition_facts = isset( $recipe_global['showNutritionFacts']['0'] ) && 'yes' === $recipe_global['showNutritionFacts']['0'] ? true : false;

		if( $enable_nutrition_facts && ! empty( $nutri_filtered ) ) {
			$calories          = $recipe->nutrition['calories'] ? $recipe->nutrition['calories'] . ' calories' : '';
			$totalCarbohydrate = $recipe->nutrition['totalCarbohydrate'] ? $recipe->nutrition['totalCarbohydrate'] . ' grams' : '';
			$cholesterol       = $recipe->nutrition['cholesterol'] ? $recipe->nutrition['cholesterol'] . ' milligrams' : '';
			$totalFat          = $recipe->nutrition['totalFat'] ? $recipe->nutrition['totalFat'] . ' grams' : '';
			$dietaryFiber      = $recipe->nutrition['dietaryFiber'] ? $recipe->nutrition['dietaryFiber'] . ' grams' : '';
			$protein           = $recipe->nutrition['protein'] ? $recipe->nutrition['protein'] . ' grams' : '';
			$saturatedFat      = $recipe->nutrition['saturatedFat'] ? $recipe->nutrition['saturatedFat'] . ' grams' : '';
			$servingSize       = $recipe->nutrition['servingSize'] ? $recipe->nutrition['servingSize'] : '';
			$sodium            = $recipe->nutrition['sodium'] ? $recipe->nutrition['sodium'] . ' milligrams' : '';
			$sugars            = $recipe->nutrition['sugars'] ? $recipe->nutrition['sugars'] . ' grams' : '';
			$transFat          = $recipe->nutrition['transFat'] ? $recipe->nutrition['transFat'] . ' grams' : '';

            $nutrition          = array(
                '@type'               => 'NutritionInformation',
                'calories'            => $calories,
                'carbohydrateContent' => $totalCarbohydrate,
                'cholesterolContent'  => $cholesterol,
                'fatContent'          => $totalFat,
                'fiberContent'        => $dietaryFiber,
                'proteinContent'      => $protein,
                'saturatedFatContent' => $saturatedFat,
                'servingSize'         => $servingSize,
                'sodiumContent'       => $sodium,
                'sugarContent'        => $sugars,
                'transFatContent'     => $transFat
            );
		} elseif( ! empty( $recipe->recipe_calories ) ) {
            $nutrition          = array(
                '@type'    => 'NutritionInformation',
                'calories' => $recipe->recipe_calories,
            );
        }

        $video = array();

        if( $recipe->enable_video_gallery && isset( $recipe->video_gallery['0'] ) ) :
            $video_obj = $recipe->video_gallery['0'];

            if ( 'youtube' === $video_obj['vidType'] ) {
                $vid_url   = 'https://www.youtube.com/watch?v=' . $video_obj['vidID'];
                $image_url = "https://i3.ytimg.com/vi/{$video_obj['vidID']}/maxresdefault.jpg";
            } elseif( 'vimeo' === $video_obj['vidType'] ) {
                $vid_url   = 'https://vimeo.com/moogaloop.swf?clip_id=' . $video_obj['vidID'];
                $image_url = $video_obj['vidThumb'];
            }

            $video = array(
                '@type'        => 'VideoObject',
                'name'         => $recipe->name,
                'description'  => $description,
                'thumbnailUrl' => $image_url,
                'contentUrl'   => $vid_url,
                'uploadDate'   => $recipe->date_published
            );
        endif;

        $schema_array = false;

        $schema_array = apply_filters( 'wp_delicious_guided_recipe_schema_array', array(
            "@context" => "https://schema.org",
            "@type"    => "Recipe",
            "name"     => $recipe->name,
            "url"      => $recipe->permalink,
            "image"    => $images,
            "author"   => array(
                "@type" => "Person",
                "name"  => $recipe->author),
			"datePublished"      => $recipe->date_published,
			"description"        => $description,
			"prepTime"           => $prep_time,
			"cookTime"           => $cook_time,
			"totalTime"          => $total_time,
			"recipeYield"        => $recipe->no_of_servings,
			"recipeCategory"     => $recipe->recipe_course,
			"recipeCuisine"      => $recipe->recipe_cuisine,
			"cookingMethod"      => $recipe->cooking_method,
			"keywords"           => $recipe->keywords,
			"recipeIngredient"   => $recipe_ingredients,
			"recipeInstructions" => $recipe_instructions,
			"aggregateRating"    => $aggregateRating,
			"nutrition"          => $nutrition,
			"video"              => $video,
			"@id"                => $recipe->permalink . "#recipe",
			"isPartOf"           => array(
                "@id" => $recipe->permalink . "#webpage"
            ),
            "mainEntityOfPage" => array(
                "@type" => "WebPage",
                "@id"   => $recipe->permalink . "#webpage",
            )
        ), $recipe );

        return $schema_array;

    }

    /**
     * Get the FAQ schema for the recipe.
     *
     * @param  object $recipe The recipe object.
     * @return array          The recipe schema.
     */
    private function faq_schema_values(){
        global $recipe;
        $recipe_faqs = array();
        
        if ( isset( $recipe->faqs ) && ! empty( $recipe->faqs ) ):
            foreach( $recipe->faqs as $faq ) :
                if( isset( $faq['question'])  && ! empty( $faq['question'] ) && isset( $faq['answer'])  && ! empty( $faq['answer'] ) ):
                    $question = isset( $faq['question'] ) ? strip_tags( preg_replace( "~(?:\[/?)[^/\]]+/?\]~s", '', $faq['question'] ) ) : '';
                    $answer   = strip_tags( preg_replace( "~(?:\[/?)[^/\]]+/?\]~s", '', $faq['answer'] ) );

                    $recipe_faqs[] = array(
                        "@type"          => "Question",
                        "name"           => esc_html( $question ),
                        "acceptedAnswer" => array(
                            "@type" => "Answer",
                            "text"  => esc_html( $answer )
                        )
                    );
                endif;
            endforeach;
        endif;

        $faq_schema_array = false;

        if ( ! empty( $recipe_faqs ) ) {
            $faq_schema_array = apply_filters(
                'wp_delicious_recipe_faq_schema_array',
                array(
                    '@context'   => 'http://schema.org',
                    '@type'      => 'FAQPage',
                    'mainEntity' => $recipe_faqs,
                ),
                $recipe
            );  
        }

        return $faq_schema_array;
    }

}
