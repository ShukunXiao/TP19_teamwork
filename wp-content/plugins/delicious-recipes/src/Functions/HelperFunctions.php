<?php
/**
 * Helper functions for our plugin.
 * 
 * @package Delicious_Recipes
 */

/**
 * Get time format.
 *
 * @param int $minutes.
 * @param string $format.
 * 
 * @return void
 */
function delicious_recipes_time_format( $minutes, $format ){

    ob_start();

    if ( $minutes < 60 ):
        if ( $format === 'iso' ):
            return 'PT0H'.$minutes.'M';
        endif;

    elseif ( $minutes < 1440 ):
        $hours = floor( $minutes / 60 );
        $minutes_left = $minutes - ( $hours * 60 );
        if ( $format === 'iso' ):
            return 'PT'.$hours.'H'.( $minutes_left ? $minutes_left : 0 ).'M';
        endif;

    else:
        $days = floor( $minutes / 24 / 60 );
        $minutes_left = $minutes - ( $days * 24 * 60 );
        if ( $minutes_left > 60 ):
            $hours_left = floor( $minutes_left / 60 );
            $minutes_left = $minutes_left - ( $hours_left * 60 );
        endif;
        if ( $format === 'iso' ):
            return 'P'.$days.'DT'.( $hours_left ? $hours_left : 0 ).'H'.( $minutes_left ? $minutes_left : 0 ).'M';
        endif;

    endif;

    return ob_get_clean();

}

/**
 * Get information about the SVG icon.
 *
 * @param string $svg_name The name of the icon.
 * @param string $group The group the icon belongs to.
 * @param string $color Color code.
 */
function delicious_recipes_get_svg( $svg_name, $group = 'ui', $color = '' ) {

    // Make sure that only our allowed tags and attributes are included.
    $svg = wp_kses(
        Delicious_Recipes_SVG::get_svg( $svg_name, $group, $color ),
        array(
            'svg'     => array(
                'class'       => true,
                'xmlns'       => true,
                'width'       => true,
                'height'      => true,
                'viewbox'     => true,
                'aria-hidden' => true,
                'role'        => true,
                'focusable'   => true,
            ),
            'path'    => array(
                'id'        => true,
                'class'     => true,
                'data-name' => true,
                'fill'      => true,
                'fill-rule' => true,
                'd'         => true,
                'transform' => true,
            ),
            'polygon' => array(
                'fill'      => true,
                'fill-rule' => true,
                'points'    => true,
                'transform' => true,
                'focusable' => true,
            ),
            'style' => array(
                'fill' => true,
            ),
            'defs'   => true,
            'circle' => array(
                'class'     => true,
                'id'        => true,
                'cx'        => true,
                'cy'        => true,
                'r'         => true,
                'transform' => true,
                'fill'      => true,
                'data-name' => true,
            ),
            'rect' => array(
                'id'        => true,
                'data-name' => true,
                'width'     => true,
                'height'    => true,
                'rx'        => true,
                'transform' => true,
                'fill'      => true,
            ),
            'ellipse' => array(
                'class'     => true,
                'id'        => true,
                'cx'        => true,
                'cy'        => true,
                'rx'        => true,
                'ry'        => true,
                'r'         => true,
                'transform' => true,
                'fill'      => true,
                'data-name' => true,
            ),
            'g' => array(
                'id'        => true,
                'opacity'   => true,
                'transform' => true,
                'data-name' => true,
            ),
            'line'  => array(
                'id'           => true,
                'x2'           => true,
                'y2'           => true,
                'transform'    => true,
                'fill'         => true,
                'stroke'       => true,
                'stroke-width' => true,
                'data-name'    => true,
            )
        )
    );

    if ( ! $svg ) {
        return false;
    }
    return $svg;
}

/**
 * Check if a recipe is featured recipe.
 *
 * @param [type] $recipe_id
 * @return boolean
 */
function delicious_recipes_is_recipe_featured( $recipe_id ) {
	if ( ! $recipe_id ) {
		return false;
	}
	$featured = get_post_meta( $recipe_id, 'wp_delicious_featured_recipe', true );
	return ! empty( $featured ) && 'yes' === $featured;
}

/**
 * Format date to WP saved date format.
 *
 * @param [string] $date_str
 * @return void
 */
function delicious_recipes_get_formated_date( $date_str ) {

    if ( empty( $date_str ) ) {
        return false;
    }

    $saved_date_format = get_option( 'date_format', 'Y/m/d' );

    return date_i18n( $saved_date_format, strtotime( $date_str ) );
}

/**
 * Get a list of ingredients.
 */
function delicious_recipes_get_all_ingredients() {
	$args = array(
		'post_type'   => DELICIOUS_RECIPE_POST_TYPE,
		'numberposts' => -1,
	);

	$recipes           = get_posts( $args );
	$ingredients_array = array();
    
	foreach ( $recipes as $recipe ) {
        $recipe_meta        = get_post_meta( $recipe->ID, 'delicious_recipes_metadata', true );
        $recipe_ingredients = isset( $recipe_meta['recipeIngredients'] ) && $recipe_meta['recipeIngredients'] ? $recipe_meta['recipeIngredients'] : '';
        $ingres_per_recipe  = array();

        if ( isset( $recipe_ingredients ) && ! empty( $recipe_ingredients ) ) {
            foreach ( $recipe_ingredients as $recipe_ingredient ) {
                if( isset( $recipe_ingredient['ingredients'] ) && ! empty( $recipe_ingredient['ingredients'] ) ) {
                    foreach ( $recipe_ingredient['ingredients'] as $ingredients ) {
                        
                        $ingredient = strip_tags( preg_replace( "~(?:\[/?)[^/\]]+/?\]~s", '', $ingredients['ingredient'] ) );
                        if( ! in_array( $ingredient, array_values( $ingres_per_recipe ) ) ) {
                            $ingres_per_recipe[] = ucfirst( $ingredient );
                            $ingredients_array[] = ucfirst( $ingredient );
                        }
                    }
                }
            }
        }
    }

	return apply_filters( 'wp_delicious_ingredients', array_count_values( $ingredients_array ), $args );
}

/**
 * Get a list of ingredients of a single recipe.
 */
function delicious_recipes_get_single_ingredients( $recipe_id ) {
    if ( ! $recipe_id ) {
		return false;
    }
    
	$ingredients_array = array();
    
    $recipe_meta        = get_post_meta( $recipe_id, 'delicious_recipes_metadata', true );
    $recipe_ingredients = isset( $recipe_meta['recipeIngredients'] ) && $recipe_meta['recipeIngredients'] ? $recipe_meta['recipeIngredients'] : '';
    $ingres_per_recipe  = array();

    if ( isset( $recipe_ingredients ) && ! empty( $recipe_ingredients ) ) {
        foreach ( $recipe_ingredients as $recipe_ingredient ) {
            if( isset( $recipe_ingredient['ingredients'] ) && ! empty( $recipe_ingredient['ingredients'] ) ) {
                foreach ( $recipe_ingredient['ingredients'] as $ingredients ) {
                    
                    $ingredient = strip_tags( preg_replace( "~(?:\[/?)[^/\]]+/?\]~s", '', $ingredients['ingredient'] ) );
                    if( ! in_array( $ingredient, array_values( $ingres_per_recipe ) ) ) {
                        $ingres_per_recipe[] = ucfirst( $ingredient );
                    }
                }
            }
        }
    }

	return apply_filters( 'wp_delicious_single_ingredients', $ingres_per_recipe );
}

/**
 * Get information about available image sizes
 */
function delicious_recipes_get_image_sizes( $size = '' ) {

    global $_wp_additional_image_sizes;

    $sizes = array();
    $get_intermediate_image_sizes = get_intermediate_image_sizes();

    // Create the full array with sizes and crop info
    foreach( $get_intermediate_image_sizes as $_size ) {
        if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
            $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
            $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
            $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
        } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
            $sizes[ $_size ] = array( 
                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
            );
        }
    } 
    // Get only 1 size if found
    if ( $size ) {
        if( isset( $sizes[ $size ] ) ) {
            return $sizes[ $size ];
        } else {
            return false;
        }
    }
    return $sizes;
}

/**
 * Get Fallback SVG
 */
function delicious_recipes_get_fallback_svg( $size, $buffer = false ) {

    if( ! $size ){
        return;
    }

    $size = "full" == $size ? 'large' : $size;

    $image_size = delicious_recipes_get_image_sizes( $size );
    $svg_fill   = apply_filters( 'delicious_recipes_fallback_svg_fill', 'fill:#2db68d;' );

    if ( $buffer ) {
        ob_start();
    }

    if( $image_size ){ 
    ?>
        <svg class="dr-fallback-svg" width="<?php echo esc_attr( $image_size['width'] ); ?>" height="<?php echo esc_attr( $image_size['height'] ); ?>" viewBox="0 0 <?php echo esc_attr( $image_size['width'] ); ?> <?php echo esc_attr( $image_size['height'] ); ?>" preserveAspectRatio="none">
            <rect width="<?php echo esc_attr( $image_size['width'] ); ?>" height="<?php echo esc_attr( $image_size['height'] ); ?>" style="<?php echo $svg_fill;?> opacity:0.1;"></rect>
        </svg>
    <?php
    }

    if ( $buffer ) {
        $data = ob_get_clean();
        return $data;
    }
}

/**
 * Get permalink settings for Delicious Recipes.
 *
 * @since  1.0.0
 * @return array
 */
function delicious_recipes_get_permalink_structure() {

    // Get global settings.
    $global_settings = delicious_recipes_get_global_settings();

    $permalinks = array();

	$permalinks['recipeBase']        = isset( $global_settings['recipeBase'] ) && ! empty( $global_settings['recipeBase'] ) ? trim( $global_settings['recipeBase'], '/\\' ) : 'recipe';
	$permalinks['courseBase']        = isset( $global_settings['courseBase'] ) && ! empty( $global_settings['courseBase'] ) ? trim( $global_settings['courseBase'], '/\\' ) : 'recipe-course';
	$permalinks['cuisineBase']       = isset( $global_settings['cuisineBase'] ) && ! empty( $global_settings['cuisineBase'] ) ? trim( $global_settings['cuisineBase'], '/\\' ) : 'recipe-cuisine';
	$permalinks['cookingMethodBase'] = isset( $global_settings['cookingMethodBase'] ) && ! empty( $global_settings['cookingMethodBase'] ) ? trim( $global_settings['cookingMethodBase'], '/\\' ) : 'recipe-cooking-method';
	$permalinks['keyBase']           = isset( $global_settings['keyBase'] ) && ! empty( $global_settings['keyBase'] ) ? trim( $global_settings['keyBase'], '/\\' ) : 'recipe-key';
	$permalinks['tagBase']           = isset( $global_settings['tagBase'] ) && ! empty( $global_settings['tagBase'] ) ? trim( $global_settings['tagBase'], '/\\' ) : 'recipe-tag';
	$permalinks['badgeBase']         = isset( $global_settings['badgeBase'] ) && ! empty( $global_settings['badgeBase'] ) ? trim( $global_settings['badgeBase'], '/\\' ) : 'recipe-badge';

	return $permalinks;
}

/**
 * Given a string containing any combination of YouTube and Vimeo video URLs in
 * a variety of formats (iframe, shortened, etc), each separated by a line break,
 * parse the video string and determine it's valid embeddable URL for usage in
 * popular JavaScript lightbox plugins.
 *
 * In addition, this handler grabs both the maximize size and thumbnail versions
 * of video images for your general consumption. In the case of Vimeo, you must
 * have the ability to make remote calls using file_get_contents(), which may be
 * a problem on shared hosts.
 *
 * Data gets returned in the format:
 *
 * array(
 *   array(
 *     'url' => 'http://path.to/embeddable/video',
 *     'thumbnail' => 'http://path.to/thumbnail/image.jpg',
 *     'fullsize' => 'http://path.to/fullsize/image.jpg',
 *   )
 * )
 *
 * @param       string  $videoString
 * @return      array   An array of video metadata if found
 *
 * @author      Corey Ballou http://coreyballou.com
 * @copyright   (c) 2012 Skookum Digital Works http://skookum.com
 * @license     
 */
function delicious_recipes_parse_videos($videoString = null)
{
    // return data
    $videos = array();

    if (!empty($videoString)) {

        // split on line breaks
        $videoString = stripslashes(trim($videoString));
        $videoString = explode("\n", $videoString);
        $videoString = array_filter($videoString, 'trim');

        // check each video for proper formatting
        foreach ($videoString as $video) {

            // check for iframe to get the video url
            if (strpos($video, 'iframe') !== FALSE) {
                // retrieve the video url
                $anchorRegex = '/src="(.*)?"/isU';
                $results = array();
                if (preg_match($anchorRegex, $video, $results)) {
                    $link = trim($results[1]);
                }
            } else {
                // we already have a url
                $link = $video;
            }

            // if we have a URL, parse it down
            if (!empty($link)) {

                // initial values
                $video_id = NULL;
                $videoIdRegex = NULL;
                $results = array();

                // check for type of youtube link
                if (strpos($link, 'youtu') !== FALSE) {
                    if (strpos($link, 'youtube.com') !== FALSE) {
                        // works on:
                        // http://www.youtube.com/embed/VIDEOID
                        // http://www.youtube.com/embed/VIDEOID?modestbranding=1&amp;rel=0
                        // http://www.youtube.com/v/VIDEO-ID?fs=1&amp;hl=en_US
                        $videoIdRegex = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
                    } else if (strpos($link, 'youtu.be') !== FALSE) {
                        // works on:
                        // http://youtu.be/daro6K6mym8
                        $videoIdRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
                    }

                    if ($videoIdRegex !== NULL) {
                        if (preg_match($videoIdRegex, $link, $results)) {
                            $video_str     = 'http://www.youtube.com/v/%s';
                            $thumbnail_str = 'http://i3.ytimg.com/vi/%s/hqdefault.jpg';
                            $fullsize_str  = 'http://i3.ytimg.com/vi/%s/maxresdefault.jpg';
                            $video_id      = $results[1];
                            $vid_type      = 'youtube';
                        }
                    }
                }

                // handle vimeo videos
                else if (strpos($video, 'vimeo') !== FALSE) {
                    if (strpos($video, 'player.vimeo.com') !== FALSE) {
                        // works on:
                        // http://player.vimeo.com/video/37985580?title=0&amp;byline=0&amp;portrait=0
                        $videoIdRegex = '/player.vimeo.com\/video\/([0-9]+)\??/i';
                    } else {
                        // works on:
                        // http://vimeo.com/37985580
                        $videoIdRegex = '/vimeo.com\/([0-9]+)\??/i';
                    }

                    if ($videoIdRegex !== NULL) {
                        if (preg_match($videoIdRegex, $link, $results)) {
                            $video_id = $results[1];

                            // get the thumbnail
                            try {
                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$video_id.php"));
                                if (!empty($hash) && is_array($hash)) {
                                    $video_str     = 'http://vimeo.com/moogaloop.swf?clip_id=%s';
                                    $thumbnail_str = $hash[0]['thumbnail_small'];
                                    $fullsize_str  = $hash[0]['thumbnail_large'];
                                    $vid_type      = 'vimeo';
                                } else {
                                    // don't use, couldn't find what we need
                                    unset($video_id);
                                }
                            } catch (Exception $e) {
                                unset($video_id);
                            }
                        }
                    }
                }

                // check if we have a video id, if so, add the video metadata
                if (!empty($video_id)) {
                    // add to return
                    $videos[] = array(
                        'url'       => sprintf($video_str, $video_id),
                        'thumbnail' => sprintf($thumbnail_str, $video_id),
                        'fullsize'  => sprintf($fullsize_str, $video_id),
                        'id'        => $video_id,
                        'type'      => $vid_type
                    );
                }
            }

        }

    }

    // return array of parsed videos
    return $videos;
}

/**
 * Get nutrition defination arrays.
 *
 * @return void
 */
function delicious_recipes_get_nutrition_facts(){
    // Use the "delicious_recipes_get_nutrition_facts" filter to add your own nutrition facts.
    $nutrition_facts = apply_filters('delicious_recipes_nutrition_facts',array(

        'top' => array(
            'servingSize' => array(
                'name' => esc_html__('Serving Size','delicious-recipes'),
                'type' => 'text'
            ),
            'servings' => array(
                'name' => esc_html__('Servings','delicious-recipes'),
                'type' => 'number'
            )
        ),

        'mid' => array(
            'calories' => array(
                'name' => esc_html__('Calories','delicious-recipes'),
                'type' => 'number'
            ),
            'caloriesFromFat' => array(
                'name' => esc_html__('Calories from Fat','delicious-recipes'),
                'type' => 'number'
            )
        ),

        'main' => array(
            'totalFat' => array(
                'name' => esc_html__('Total Fat','delicious-recipes'),
                'type' => 'number',
                'measurement' => 'g',
                'pdv' => apply_filters( 'wp_delicious_pdv_fat', 65 ),
                'subs' => array(
                    'saturatedFat' => array(
                        'name' => esc_html__('Saturated Fat','delicious-recipes'),
                        'type' => 'number',
                        'measurement' => 'g',
                        'pdv' => apply_filters( 'wp_delicious_pdv_satfat', 20 )
                    ),
                    'transFat' => array(
                        'name' => esc_html__('Trans Fat','delicious-recipes'),
                        'type' => 'number',
                        'measurement' => 'g'
                    )
                )
            ),
            'cholesterol' => array(
                'name' => esc_html__('Cholesterol','delicious-recipes'),
                'type' => 'number',
                'measurement' => 'mg',
                'pdv' => apply_filters( 'wp_delicious_pdv_cholesterol', 300 )
            ),
            'sodium' => array(
                'name' => esc_html__('Sodium','delicious-recipes'),
                'type' => 'number',
                'measurement' => 'mg',
                'pdv' => apply_filters( 'wp_delicious_pdv_sodium', 2400 )
            ),
            'potassium' => array(
                'name' => esc_html__('Potassium','delicious-recipes'),
                'type' => 'number',
                'measurement' => 'mg',
                'pdv' => apply_filters( 'wp_delicious_pdv_potassium', 3500 )
            ),
            'totalCarbohydrate' => array(
                'name' => esc_html__('Total Carbohydrate','delicious-recipes'),
                'type' => 'number',
                'measurement' => 'g',
                'pdv' => apply_filters( 'wp_delicious_pdv_carbs', 300 ),
                'subs' => array(
                    'dietaryFiber' => array(
                        'name' => esc_html__('Dietary Fiber','delicious-recipes'),
                        'type' => 'number',
                        'measurement' => 'g',
                        'pdv' => apply_filters( 'wp_delicious_pdv_fiber', 25 )
                    ),
                    'sugars' => array(
                        'name' => esc_html__('Sugars','delicious-recipes'),
                        'type' => 'number',
                        'measurement' => 'g'
                    )
                )
            ),
            'protein' => array(
                'name' => esc_html__('Protein','delicious-recipes'),
                'type' => 'number',
                'measurement' => 'g',
                'pdv' => apply_filters( 'wp_delicious_pdv_protein', 50 )
            )
        ),

        'bottom' => array(
            'vitaminA' => array(
                'name' => esc_html__('Vitamin A','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'vitaminC' => array(
                'name' => esc_html__('Vitamin C','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'calcium' => array(
                'name' => esc_html__('Calcium','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'iron' => array(
                'name' => esc_html__('Iron','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'vitaminD' => array(
                'name' => esc_html__('Vitamin D','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'vitaminE' => array(
                'name' => esc_html__('Vitamin E','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'vitaminK' => array(
                'name' => esc_html__('Vitamin K','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'thiamin' => array(
                'name' => esc_html__('Thiamin','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'riboflavin' => array(
                'name' => esc_html__('Riboflavin','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'niacin' => array(
                'name' => esc_html__('Niacin','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'vitaminB6' => array(
                'name' => esc_html__('Vitamin B6','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'folate' => array(
                'name' => esc_html__('Folate','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'vitaminB12' => array(
                'name' => esc_html__('Vitamin B12','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'biotin' => array(
                'name' => esc_html__('Biotin','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'pantothenicAcid' => array(
                'name' => esc_html__('Pantothenic Acid','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'phosphorus' => array(
                'name' => esc_html__('Phosphorus','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'iodine' => array(
                'name' => esc_html__('Iodine','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'magnesium' => array(
                'name' => esc_html__('Magnesium','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'zinc' => array(
                'name' => esc_html__('Zinc','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'selenium' => array(
                'name' => esc_html__('Selenium','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'copper' => array(
                'name' => esc_html__('Copper','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'manganese' => array(
                'name' => esc_html__('Manganese','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'chromium' => array(
                'name' => esc_html__('Chromium','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'molybdenum' => array(
                'name' => esc_html__('Molybdenum','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            ),
            'chloride' => array(
                'name' => esc_html__('Chloride','delicious-recipes'),
                'type' => 'number',
                'measurement' => '%'
            )
        )
    ));

    return $nutrition_facts;

}

/**
 * Get fontawesome icons list.
 *
 * @param string $type
 * @return void
 */
function delicious_recipes_get_fontawesome_icons( $type = "all" ) {
    $fa 		= array();
	$brands		= array( "fa-500px","fa-accessible-icon","fa-accusoft","fa-acquisitions-incorporated","fa-adn","fa-adobe","fa-adversal","fa-affiliatetheme","fa-airbnb","fa-algolia","fa-alipay","fa-amazon","fa-amazon-pay","fa-amilia","fa-android","fa-angellist","fa-angrycreative","fa-angular","fa-app-store","fa-app-store-ios","fa-apper","fa-apple","fa-apple-pay","fa-artstation","fa-asymmetrik","fa-atlassian","fa-audible","fa-autoprefixer","fa-avianex","fa-aviato","fa-aws","fa-bandcamp","fa-battle-net","fa-behance","fa-behance-square","fa-bimobject","fa-bitbucket","fa-bitcoin","fa-bity","fa-black-tie","fa-blackberry","fa-blogger","fa-blogger-b","fa-bluetooth","fa-bluetooth-b","fa-bootstrap","fa-btc","fa-buffer","fa-buromobelexperte","fa-buy-n-large","fa-buysellads","fa-canadian-maple-leaf","fa-cc-amazon-pay","fa-cc-amex","fa-cc-apple-pay","fa-cc-diners-club","fa-cc-discover","fa-cc-jcb","fa-cc-mastercard","fa-cc-paypal","fa-cc-stripe","fa-cc-visa","fa-centercode","fa-centos","fa-chrome","fa-chromecast","fa-cloudscale","fa-cloudsmith","fa-cloudversify","fa-codepen","fa-codiepie","fa-confluence","fa-connectdevelop","fa-contao","fa-cotton-bureau","fa-cpanel","fa-creative-commons","fa-creative-commons-by","fa-creative-commons-nc","fa-creative-commons-nc-eu","fa-creative-commons-nc-jp","fa-creative-commons-nd","fa-creative-commons-pd","fa-creative-commons-pd-alt","fa-creative-commons-remix","fa-creative-commons-sa","fa-creative-commons-sampling","fa-creative-commons-sampling-plus","fa-creative-commons-share","fa-creative-commons-zero","fa-critical-role","fa-css3","fa-css3-alt","fa-cuttlefish","fa-d-and-d","fa-d-and-d-beyond","fa-dashcube","fa-delicious","fa-deploydog","fa-deskpro","fa-dev","fa-deviantart","fa-dhl","fa-diaspora","fa-digg","fa-digital-ocean","fa-discord","fa-discourse","fa-dochub","fa-docker","fa-draft2digital","fa-dribbble","fa-dribbble-square","fa-dropbox","fa-drupal","fa-dyalog","fa-earlybirds","fa-ebay","fa-edge","fa-elementor","fa-ello","fa-ember","fa-empire","fa-envira","fa-erlang","fa-ethereum","fa-etsy","fa-evernote","fa-expeditedssl","fa-facebook","fa-facebook-f","fa-facebook-messenger","fa-facebook-square","fa-fantasy-flight-games","fa-fedex","fa-fedora","fa-figma","fa-firefox","fa-firefox-browser","fa-first-order","fa-first-order-alt","fa-firstdraft","fa-flickr","fa-flipboard","fa-fly","fa-font-awesome","fa-font-awesome-alt","fa-font-awesome-flag","fa-fonticons","fa-fonticons-fi","fa-fort-awesome","fa-fort-awesome-alt","fa-forumbee","fa-foursquare","fa-free-code-camp","fa-freebsd","fa-fulcrum","fa-galactic-republic","fa-galactic-senate","fa-get-pocket","fa-gg","fa-gg-circle","fa-git","fa-git-alt","fa-git-square","fa-github","fa-github-alt","fa-github-square","fa-gitkraken","fa-gitlab","fa-gitter","fa-glide","fa-glide-g","fa-gofore","fa-goodreads","fa-goodreads-g","fa-google","fa-google-drive","fa-google-play","fa-google-plus","fa-google-plus-g","fa-google-plus-square","fa-google-wallet","fa-gratipay","fa-grav","fa-gripfire","fa-grunt","fa-gulp","fa-hacker-news","fa-hacker-news-square","fa-hackerrank","fa-hips","fa-hire-a-helper","fa-hooli","fa-hornbill","fa-hotjar","fa-houzz","fa-html5","fa-hubspot","fa-ideal","fa-imdb","fa-instagram","fa-intercom","fa-internet-explorer","fa-invision","fa-ioxhost","fa-itch-io","fa-itunes","fa-itunes-note","fa-java","fa-jedi-order","fa-jenkins","fa-jira","fa-joget","fa-joomla","fa-js","fa-js-square","fa-jsfiddle","fa-kaggle","fa-keybase","fa-keycdn","fa-kickstarter","fa-kickstarter-k","fa-korvue","fa-laravel","fa-lastfm","fa-lastfm-square","fa-leanpub","fa-less","fa-line","fa-linkedin","fa-linkedin-in","fa-linode","fa-linux","fa-lyft","fa-magento","fa-mailchimp","fa-mandalorian","fa-markdown","fa-mastodon","fa-maxcdn","fa-mdb","fa-medapps","fa-medium","fa-medium-m","fa-medrt","fa-meetup","fa-megaport","fa-mendeley","fa-microblog","fa-microsoft","fa-mix","fa-mixcloud","fa-mizuni","fa-modx","fa-monero","fa-napster","fa-neos","fa-nimblr","fa-node","fa-node-js","fa-npm","fa-ns8","fa-nutritionix","fa-odnoklassniki","fa-odnoklassniki-square","fa-old-republic","fa-opencart","fa-openid","fa-opera","fa-optin-monster","fa-orcid","fa-osi","fa-page4","fa-pagelines","fa-palfed","fa-patreon","fa-paypal","fa-penny-arcade","fa-periscope","fa-phabricator","fa-phoenix-framework","fa-phoenix-squadron","fa-php","fa-pied-piper","fa-pied-piper-alt","fa-pied-piper-hat","fa-pied-piper-pp","fa-pied-piper-square","fa-pinterest","fa-pinterest-p","fa-pinterest-square","fa-playstation","fa-product-hunt","fa-pushed","fa-python","fa-qq","fa-quinscape","fa-quora","fa-r-project","fa-raspberry-pi","fa-ravelry","fa-react","fa-reacteurope","fa-readme","fa-rebel","fa-red-river","fa-reddit","fa-reddit-alien","fa-reddit-square","fa-redhat","fa-renren","fa-replyd","fa-researchgate","fa-resolving","fa-rev","fa-rocketchat","fa-rockrms","fa-safari","fa-salesforce","fa-sass","fa-schlix","fa-scribd","fa-searchengin","fa-sellcast","fa-sellsy","fa-servicestack","fa-shirtsinbulk","fa-shopware","fa-simplybuilt","fa-sistrix","fa-sith","fa-sketch","fa-skyatlas","fa-skype","fa-slack","fa-slack-hash","fa-slideshare","fa-snapchat","fa-snapchat-ghost","fa-snapchat-square","fa-soundcloud","fa-sourcetree","fa-speakap","fa-speaker-deck","fa-spotify","fa-squarespace","fa-stack-exchange","fa-stack-overflow","fa-stackpath","fa-staylinked","fa-steam","fa-steam-square","fa-steam-symbol","fa-sticker-mule","fa-strava","fa-stripe","fa-stripe-s","fa-studiovinari","fa-stumbleupon","fa-stumbleupon-circle","fa-superpowers","fa-supple","fa-suse","fa-swift","fa-symfony","fa-teamspeak","fa-telegram","fa-telegram-plane","fa-tencent-weibo","fa-the-red-yeti","fa-themeco","fa-themeisle","fa-think-peaks","fa-trade-federation","fa-trello","fa-tripadvisor","fa-tumblr","fa-tumblr-square","fa-twitch","fa-twitter","fa-twitter-square","fa-typo3","fa-uber","fa-ubuntu","fa-uikit","fa-umbraco","fa-uniregistry","fa-unity","fa-untappd","fa-ups","fa-usb","fa-usps","fa-ussunnah","fa-vaadin","fa-viacoin","fa-viadeo","fa-viadeo-square","fa-viber","fa-vimeo","fa-vimeo-square","fa-vimeo-v","fa-vine","fa-vk","fa-vnv","fa-vuejs","fa-waze","fa-weebly","fa-weibo","fa-weixin","fa-whatsapp","fa-whatsapp-square","fa-whmcs","fa-wikipedia-w","fa-windows","fa-wix","fa-wizards-of-the-coast","fa-wolf-pack-battalion","fa-wordpress","fa-wordpress-simple","fa-wpbeginner","fa-wpexplorer","fa-wpforms","fa-wpressr","fa-xbox","fa-xing","fa-xing-square","fa-y-combinator","fa-yahoo","fa-yammer","fa-yandex","fa-yandex-international","fa-yarn","fa-yelp","fa-yoast","fa-youtube","fa-youtube-square","fa-zhihu" );
	$duotones 	= array( "fa-ad","fa-address-book","fa-address-card","fa-adjust","fa-air-freshener","fa-align-center","fa-align-justify","fa-align-left","fa-align-right","fa-allergies","fa-ambulance","fa-american-sign-language-interpreting","fa-anchor","fa-angle-double-down","fa-angle-double-left","fa-angle-double-right","fa-angle-double-up","fa-angle-down","fa-angle-left","fa-angle-right","fa-angle-up","fa-angry","fa-ankh","fa-apple-alt","fa-archive","fa-archway","fa-arrow-alt-circle-down","fa-arrow-alt-circle-left","fa-arrow-alt-circle-right","fa-arrow-alt-circle-up","fa-arrow-circle-down","fa-arrow-circle-left","fa-arrow-circle-right","fa-arrow-circle-up","fa-arrow-down","fa-arrow-left","fa-arrow-right","fa-arrow-up","fa-arrows-alt","fa-arrows-alt-h","fa-arrows-alt-v","fa-assistive-listening-systems","fa-asterisk","fa-at","fa-atlas","fa-atom","fa-audio-description","fa-award","fa-baby","fa-baby-carriage","fa-backspace","fa-backward","fa-bacon","fa-bahai","fa-balance-scale","fa-balance-scale-left","fa-balance-scale-right","fa-ban","fa-band-aid","fa-barcode","fa-bars","fa-baseball-ball","fa-basketball-ball","fa-bath","fa-battery-empty","fa-battery-full","fa-battery-half","fa-battery-quarter","fa-battery-three-quarters","fa-bed","fa-beer","fa-bell","fa-bell-slash","fa-bezier-curve","fa-bible","fa-bicycle","fa-biking","fa-binoculars","fa-biohazard","fa-birthday-cake","fa-blender","fa-blender-phone","fa-blind","fa-blog","fa-bold","fa-bolt","fa-bomb","fa-bone","fa-bong","fa-book","fa-book-dead","fa-book-medical","fa-book-open","fa-book-reader","fa-bookmark","fa-border-all","fa-border-none","fa-border-style","fa-bowling-ball","fa-box","fa-box-open","fa-boxes","fa-braille","fa-brain","fa-bread-slice","fa-briefcase","fa-briefcase-medical","fa-broadcast-tower","fa-broom","fa-brush","fa-bug","fa-building","fa-bullhorn","fa-bullseye","fa-burn","fa-bus","fa-bus-alt","fa-business-time","fa-calculator","fa-calendar","fa-calendar-alt","fa-calendar-check","fa-calendar-day","fa-calendar-minus","fa-calendar-plus","fa-calendar-times","fa-calendar-week","fa-camera","fa-camera-retro","fa-campground","fa-candy-cane","fa-cannabis","fa-capsules","fa-car","fa-car-alt","fa-car-battery","fa-car-crash","fa-car-side","fa-caravan","fa-caret-down","fa-caret-left","fa-caret-right","fa-caret-square-down","fa-car" );
	$lights 	= array( "fa-ad","fa-address-book","fa-address-card","fa-adjust","fa-air-freshener","fa-align-center","fa-align-justify","fa-align-left","fa-align-right","fa-allergies","fa-ambulance","fa-american-sign-language-interpreting","fa-anchor","fa-angle-double-down","fa-angle-double-left","fa-angle-double-right","fa-angle-double-up","fa-angle-down","fa-angle-left","fa-angle-right","fa-angle-up","fa-angry","fa-ankh","fa-apple-alt","fa-archive","fa-archway","fa-arrow-alt-circle-down","fa-arrow-alt-circle-left","fa-arrow-alt-circle-right","fa-arrow-alt-circle-up","fa-arrow-circle-down","fa-arrow-circle-left","fa-arrow-circle-right","fa-arrow-circle-up","fa-arrow-down","fa-arrow-left","fa-arrow-right","fa-arrow-up","fa-arrows-alt","fa-arrows-alt-h","fa-arrows-alt-v","fa-assistive-listening-systems","fa-asterisk","fa-at","fa-atlas","fa-atom","fa-audio-description","fa-award","fa-baby","fa-baby-carriage","fa-backspace","fa-backward","fa-bacon","fa-bahai","fa-balance-scale","fa-balance-scale-left","fa-balance-scale-right","fa-ban","fa-band-aid","fa-barcode","fa-bars","fa-baseball-ball","fa-basketball-ball","fa-bath","fa-battery-empty","fa-battery-full","fa-battery-half","fa-battery-quarter","fa-battery-three-quarters","fa-bed","fa-beer","fa-bell","fa-bell-slash","fa-bezier-curve","fa-bible","fa-bicycle","fa-biking","fa-binoculars","fa-biohazard","fa-birthday-cake","fa-blender","fa-blender-phone","fa-blind","fa-blog","fa-bold","fa-bolt","fa-bomb","fa-bone","fa-bong","fa-book","fa-book-dead","fa-book-medical","fa-book-open","fa-book-reader","fa-bookmark","fa-border-all","fa-border-none","fa-border-style","fa-bowling-ball","fa-box","fa-box-open","fa-boxes","fa-braille","fa-brain","fa-bread-slice","fa-briefcase","fa-briefcase-medical","fa-broadcast-tower","fa-broom","fa-brush","fa-bug","fa-building","fa-bullhorn","fa-bullseye","fa-burn","fa-bus","fa-bus-alt","fa-business-time","fa-calculator","fa-calendar","fa-calendar-alt","fa-calendar-check","fa-calendar-day","fa-calendar-minus","fa-calendar-plus","fa-calendar-times","fa-calendar-week","fa-camera","fa-camera-retro","fa-campground","fa-candy-cane","fa-cannabis","fa-capsules","fa-car","fa-car-alt","fa-car-battery","fa-car-crash","fa-car-side","fa-caravan","fa-caret-down","fa-caret-left","fa-caret-right","fa-caret-square-down","fa-car" );
	$regulars 	= array( "fa-ad","fa-address-book","fa-address-card","fa-adjust","fa-air-freshener","fa-align-center","fa-align-justify","fa-align-left","fa-align-right","fa-allergies","fa-ambulance","fa-american-sign-language-interpreting","fa-anchor","fa-angle-double-down","fa-angle-double-left","fa-angle-double-right","fa-angle-double-up","fa-angle-down","fa-angle-left","fa-angle-right","fa-angle-up","fa-angry","fa-ankh","fa-apple-alt","fa-archive","fa-archway","fa-arrow-alt-circle-down","fa-arrow-alt-circle-left","fa-arrow-alt-circle-right","fa-arrow-alt-circle-up","fa-arrow-circle-down","fa-arrow-circle-left","fa-arrow-circle-right","fa-arrow-circle-up","fa-arrow-down","fa-arrow-left","fa-arrow-right","fa-arrow-up","fa-arrows-alt","fa-arrows-alt-h","fa-arrows-alt-v","fa-assistive-listening-systems","fa-asterisk","fa-at","fa-atlas","fa-atom","fa-audio-description","fa-award","fa-baby","fa-baby-carriage","fa-backspace","fa-backward","fa-bacon","fa-bahai","fa-balance-scale","fa-balance-scale-left","fa-balance-scale-right","fa-ban","fa-band-aid","fa-barcode","fa-bars","fa-baseball-ball","fa-basketball-ball","fa-bath","fa-battery-empty","fa-battery-full","fa-battery-half","fa-battery-quarter","fa-battery-three-quarters","fa-bed","fa-beer","fa-bell","fa-bell-slash","fa-bezier-curve","fa-bible","fa-bicycle","fa-biking","fa-binoculars","fa-biohazard","fa-birthday-cake","fa-blender","fa-blender-phone","fa-blind","fa-blog","fa-bold","fa-bolt","fa-bomb","fa-bone","fa-bong","fa-book","fa-book-dead","fa-book-medical","fa-book-open","fa-book-reader","fa-bookmark","fa-border-all","fa-border-none","fa-border-style","fa-bowling-ball","fa-box","fa-box-open","fa-boxes","fa-braille","fa-brain","fa-bread-slice","fa-briefcase","fa-briefcase-medical","fa-broadcast-tower","fa-broom","fa-brush","fa-bug","fa-building","fa-bullhorn","fa-bullseye","fa-burn","fa-bus","fa-bus-alt","fa-business-time","fa-calculator","fa-calendar","fa-calendar-alt","fa-calendar-check","fa-calendar-day","fa-calendar-minus","fa-calendar-plus","fa-calendar-times","fa-calendar-week","fa-camera","fa-camera-retro","fa-campground","fa-candy-cane","fa-cannabis","fa-capsules","fa-car","fa-car-alt","fa-car-battery","fa-car-crash","fa-car-side","fa-caravan","fa-caret-down","fa-caret-left","fa-caret-right","fa-caret-square-down","fa-car" );
	$solids 	= array( "fa-ad","fa-address-book","fa-address-card","fa-adjust","fa-air-freshener","fa-align-center","fa-align-justify","fa-align-left","fa-align-right","fa-allergies","fa-ambulance","fa-american-sign-language-interpreting","fa-anchor","fa-angle-double-down","fa-angle-double-left","fa-angle-double-right","fa-angle-double-up","fa-angle-down","fa-angle-left","fa-angle-right","fa-angle-up","fa-angry","fa-ankh","fa-apple-alt","fa-archive","fa-archway","fa-arrow-alt-circle-down","fa-arrow-alt-circle-left","fa-arrow-alt-circle-right","fa-arrow-alt-circle-up","fa-arrow-circle-down","fa-arrow-circle-left","fa-arrow-circle-right","fa-arrow-circle-up","fa-arrow-down","fa-arrow-left","fa-arrow-right","fa-arrow-up","fa-arrows-alt","fa-arrows-alt-h","fa-arrows-alt-v","fa-assistive-listening-systems","fa-asterisk","fa-at","fa-atlas","fa-atom","fa-audio-description","fa-award","fa-baby","fa-baby-carriage","fa-backspace","fa-backward","fa-bacon","fa-bahai","fa-balance-scale","fa-balance-scale-left","fa-balance-scale-right","fa-ban","fa-band-aid","fa-barcode","fa-bars","fa-baseball-ball","fa-basketball-ball","fa-bath","fa-battery-empty","fa-battery-full","fa-battery-half","fa-battery-quarter","fa-battery-three-quarters","fa-bed","fa-beer","fa-bell","fa-bell-slash","fa-bezier-curve","fa-bible","fa-bicycle","fa-biking","fa-binoculars","fa-biohazard","fa-birthday-cake","fa-blender","fa-blender-phone","fa-blind","fa-blog","fa-bold","fa-bolt","fa-bomb","fa-bone","fa-bong","fa-book","fa-book-dead","fa-book-medical","fa-book-open","fa-book-reader","fa-bookmark","fa-border-all","fa-border-none","fa-border-style","fa-bowling-ball","fa-box","fa-box-open","fa-boxes","fa-braille","fa-brain","fa-bread-slice","fa-briefcase","fa-briefcase-medical","fa-broadcast-tower","fa-broom","fa-brush","fa-bug","fa-building","fa-bullhorn","fa-bullseye","fa-burn","fa-bus","fa-bus-alt","fa-business-time","fa-calculator","fa-calendar","fa-calendar-alt","fa-calendar-check","fa-calendar-day","fa-calendar-minus","fa-calendar-plus","fa-calendar-times","fa-calendar-week","fa-camera","fa-camera-retro","fa-campground","fa-candy-cane","fa-cannabis","fa-capsules","fa-car","fa-car-alt","fa-car-battery","fa-car-crash","fa-car-side","fa-caravan","fa-caret-down","fa-caret-left","fa-caret-right","fa-caret-square-down","fa-car" );
	switch( $type ) {
		default:
		case "all": {
			foreach( $brands as $brand ) {
				$fa[] = "fab ".$brand;
			}
			foreach( $solids as $solid ) {
				$fa[] = "fas ".$solid;
			}
			break;
		}
		
		case "brands": {
			$fa = $duotone;
			break;
		}
		
		case "duotone": {
			$fa = $duotone;
			break;
		}
		
		case "light": {
			$fa = $light;
			break;
		}
		
		case "regular": {
			$fa = $regular;
			break;
		}
		
		case "solid": {
			$fa = $solid;
			break;
		}
	}
	
	return $fa;
}

/**
 * Get PNG icons.
 *
 * @return void
 */
function delicious_recipes_get_png_icons() {
    $icons_path = plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . 'assets/images/png-icons/';
    $png_icons = apply_filters( 'delicious_recipes_png_icons', [
        'vegetarian-png'     => $icons_path . '1.png',
        'non-vegetarian-png' => $icons_path . '2.png',
        'dairy-png'          => $icons_path . '3.png',
        'dessert-png'        => $icons_path . '4.png',
        'bread-png'          => $icons_path . '5.png',
        'fruits-png'         => $icons_path . '6.png'
    ] );
    return $png_icons;

}

add_filter('delicious_recipes_png_icons', 'delicious_recipes_get_custom_icons');

function delicious_recipes_get_custom_icons( $png_icons ) {
    $global_settings = delicious_recipes_get_global_settings();
    $custom_icons    = isset( $global_settings['customIcons'] ) && $global_settings['customIcons'] ? $global_settings['customIcons'] : '';
    $new_icons       = array();

    if ( isset( $custom_icons ) && ! empty( $custom_icons ) ) {
        foreach( $custom_icons as $key => $img ) {
            $image_id = isset( $img['imageID'] ) ? $img['imageID'] : false;
            if ( $image_id ) {
                $image_url   = wp_get_attachment_image_url( $image_id, 'full' );
                $image_name  = basename( get_attached_file( $image_id ) );
                $new_icons[$image_name] = $image_url;
            }
        }
    }
    
    $png_icons = array_merge( $png_icons, $new_icons );
    return $png_icons;
}

/**
 * Callback function for Comment List *
 * 
 * @link https://codex.wordpress.org/Function_Reference/wp_list_comments 
 */
function delicious_recipes_comments_callback( $comment, $args, $depth ){
	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
	<<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
	
    <?php if ( 'div' != $args['style'] ) : ?>
    <div id="div-comment-<?php comment_ID() ?>" itemscope itemtype="http://schema.org/UserComments">
	<?php endif; ?>
        <article class="comment-body">
            <footer class="comment-meta">
                <div class="comment-author vcard">
                    <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
                    <?php
                        /* translators: %s: comment author link */ 
                        printf( __( '<b class="fn" itemprop="creator" itemscope itemtype="https://schema.org/Person">%s</b> <span class="says">says:</span>', 'delicious-recipes' ), get_comment_author_link() ); 
                    ?>
                </div><!-- .comment-author vcard -->
                <div class="comment-metadata commentmetadata">
                    <a href="<?php echo esc_url( htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ); ?>">
                        <time itemprop="commentTime" datetime="<?php echo esc_attr( get_gmt_from_date( get_comment_date() . get_comment_time(), 'Y-m-d H:i:s' ) ); ?>"><?php 
                            /* translators: %1$s: comment date %2$s: comment time */
                            printf( esc_html__( '%1$s at %2$s', 'delicious-recipes' ), get_comment_date(),  get_comment_time() ); 
                        ?></time>
                    </a>
                </div>
                <?php if ( $comment->comment_approved == '0' ) : ?>
                    <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'delicious-recipes' ); ?></p>
                    <br />
                <?php endif; ?>
                <div class="reply">
                    <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                </div>
            </footer>            
            <div class="comment-content" itemprop="commentText"><?php comment_text(); ?></div>    
        </article>
	<?php if ( 'div' != $args['style'] ) : ?>
    </div><!-- .comment-body -->
	<?php endif; ?>
<?php
}

/**
 * Get average rating.
 *
 * @param [type] $id - Recipe ID
 * @return void
 */
function delicious_recipes_get_average_rating( $id, $return_total_votes = false, $return_total_ratings = false ) {
    $args = array(
		'meta_key'    => 'rating',
		'orderby'     => 'meta_value_num',
		'order'       => 'DESC',
		'meta_query'  => array(
			array(
				'key'     => 'rating',
				'compare' => 'EXISTS',
			),
		),
	);
	$comments = get_approved_comments( $id, $args );

	if ( $comments ) {
		$i     = 0;
		$total = 0;
		foreach( $comments as $comment ){
			$rate = get_comment_meta( $comment->comment_ID, 'rating', true );
			if( isset( $rate ) && '' !== $rate && 0 != $rate ) {
				$i++;
				$total += $rate;
			}
		}

		if ( 0 === $i ) {
			return false;
		} else {
            if ( $return_total_votes ) {
                return $i;
            }
            if ( $return_total_ratings ) {
                return $total;
            }
			return round( $total / $i, 1 );
		}
	} else {
		return false;
	}
}

/**
 * Get unit text.
 *
 * @param [type] $unit
 * @param [type] $qty
 * @return void
 */
function delicious_recipes_get_unit_text( $unit, $qty ) {
    if ( empty( $unit ) || empty( $qty ) ) {
        return false;
    }
    $ingredien_units = delicious_recipes_get_ingredient_units();

    if ( isset( $ingredien_units[$unit] ) ) {
        return _nx( $ingredien_units[$unit]['singular'], $ingredien_units[$unit]['plural'], $qty, 'ingredient', 'delicious-recipes' );
    }

    return false;
}

/**
* Sort tax parent child hierarchy
*
* @param Array $cats
* @param Array $into
* @param integer $parentId
* @return void
*/
function delicious_recipes_sort_terms_hierarchicaly(Array &$cats, Array &$into, $parentId = 0) {
    foreach ( $cats as $i => $cat ) {
        if ( $cat->parent == $parentId ) {
            $into[$cat->term_id] = $cat;
            unset( $cats[$i] );
        }
    }

    foreach ( $into as $topCat ) {
        $topCat->children = array();
        delicious_recipes_sort_terms_hierarchicaly( $cats, $topCat->children, $topCat->term_id );
    }
}

/**
 * Get search layout.
 *
 * @param [type] $taxonomy
 * @param boolean $has_children
 * @return void
 */
function delicious_recipes_search_taxonomy_render( $taxonomy_array, $has_children = false, $name = 'recipe_cooking_methods' ) {
    $show_count = apply_filters( 'delicious_recipes_search_filters_show_count', true );
    
    foreach( $taxonomy_array as $ky => $tax ) :
        $term_slug    = $tax->slug;
        $term_name    = $tax->name;
        $has_children = isset( $tax->children ) && ! empty( $tax->children ) ? true : false;
        $tax_count    = $tax->category_count;
    ?>
        <option class="<?php echo $has_children ? esc_attr( 'has-children' ) : ''; ?>" value='<?php esc_attr_e( $tax->term_id, 'delicious-recipes' ); ?>' id="<?php echo esc_attr( sanitize_title( $term_slug ) ); ?>" name='<?php esc_attr_e( $name, 'delicious-recipes' ); ?>'>
            
            <?php 
                esc_html_e( $term_name, 'delicious-recipes' );                
                if ( $show_count ) : ?>
                    <span class='count'>(<?php echo esc_html( $tax_count ); ?>)</span>
                <?php endif;

                if ( $has_children ) :
                    delicious_recipes_search_taxonomy_render( $tax->children, $has_children, $name );
                endif;
            ?>
        </option>
    <?php 
    endforeach; 
}

/**
 * Get default archive layout for recipes.
 *
 * @return void
 */
function delicious_recipes_get_archive_layout() {
    $view = 'grid';
    $global_settings = delicious_recipes_get_global_settings();

    $view = isset( $global_settings['defaultArchiveLayout'] ) && ! empty( $global_settings['defaultArchiveLayout'] ) ? $global_settings['defaultArchiveLayout'] : $view;

    return apply_filters( 'delicious_recipes_default_archive_layout', $view );
}

/**
 * Function for sanitizing Hex color 
 */
function delicious_recipes_sanitize_hex_color( $color ){
	if ( '' === $color )
		return '';

    // 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
		return $color;
}

/**
 * Taxonomy icon.
 *
 * @param [type] $tax_id
 * @return void
 */
function delicious_recipes_get_tax_icon( $term, $buffer = false ) {

    if ( ! $term ) {
        return false;
    }

    $tax_id   = $term->term_id;
    $tax_name = $term->name;

    $dr_taxonomy_metas = get_term_meta( $tax_id, 'dr_taxonomy_metas', true );
    $tax_color         = isset( $dr_taxonomy_metas['taxonomy_color'] )  && ! empty( $dr_taxonomy_metas['taxonomy_color'] ) ? $dr_taxonomy_metas['taxonomy_color'] : '';
    $tax_svg           = isset( $dr_taxonomy_metas['taxonomy_svg'] ) && ! empty( $dr_taxonomy_metas['taxonomy_svg'] ) ? $dr_taxonomy_metas['taxonomy_svg'] : false;

    if ( $buffer ) {
        ob_start();
    }

    if( $tax_svg ) {
        $svg       = delicious_recipes_get_svg( $tax_svg, 'recipe-keys', '#000000' );
        $png_array = delicious_recipes_get_png_icons();
        $png       = isset( $png_array[$tax_svg] ) ? $png_array[$tax_svg] : false;
        if( $svg ) {
            echo $svg; 
        } elseif( $png ) {
            echo '<img src="' . esc_url( $png ) . '" />';
        } else {
            echo '<i class="' . esc_attr( $tax_svg ) . '" style="background-color:'. esc_attr( $tax_color ) . '"></i>';
        }
    } else {
        $acronym = mb_substr( strtoupper( $tax_name ), 0, 1 );
        echo '<span style="background-color:'. esc_attr( $tax_color ) . '">'. esc_html( $acronym ) .'</span>';
    }

    if ( $buffer ) {
        $data = ob_get_clean();
        return $data;
    }
}

/**
 * Social Sharing Options
*/
function delicious_recipes_social_share() { 
    global $post;
    $social_share = apply_filters( 'delicious_recipes_social_share', array( 'facebook', 'twitter', 'pinterest', 'linkedin', 'reddit', 'email' ) );
    
    if( $social_share ) { 
    ?>
		<div class="post-share">
            <ul class="social-networks">
                <?php foreach( $social_share as $share ) {
                    switch( $share ) {
                        case 'facebook':
                        echo '<li><a href="' . esc_url( 'https://www.facebook.com/sharer.php?u=' . get_the_permalink( $post->ID ) ) . '" rel="nofollow noopener" target="_blank"><i class="fab fa-facebook-f" aria-hidden="true"></i></a></li>';        
                        break;
                        
                        case 'twitter':
                        echo '<li><a href="' . esc_url( 'https://twitter.com/intent/tweet?text=' . get_the_title( $post->ID ) ) . '&nbsp;' . get_the_permalink( $post->ID ) . '" rel="nofollow noopener" target="_blank"><i class="fab fa-twitter" aria-hidden="true"></i></a></li>';       
                        break;
                        
                        case 'linkedin':
                        echo '<li><a href="' . esc_url( 'https://www.linkedin.com/shareArticle?mini=true&url=' . get_the_permalink( $post->ID ) . '&title=' . get_the_title( $post->ID ) ) . '" rel="nofollow noopener" target="_blank"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a></li>';        
                        break;
                        
                        case 'pinterest':
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID) ); 
                        if( $image ) echo '<li><a href="' . esc_url( 'https://pinterest.com/pin/create/button/?url=' . get_the_permalink( $post->ID ) . ' &media='. $image[0] .'&description=' . get_the_title( $post->ID )  ) . '" rel="nofollow noopener" target="_blank" data-pin-do="none" data-pin-custom="true"><i class="fab fa-pinterest" aria-hidden="true"></i></a></li>';      
                        break;
                        
                        case 'email':
                        echo '<li><a href="' . esc_url( 'mailto:?Subject=' . get_the_title( $post->ID ) . '&Body=' . get_the_permalink( $post->ID ) ) . '" rel="nofollow noopener" target="_blank"><i class="fas fa-envelope" aria-hidden="true"></i></a></li>';        
                        break;
                        
                        case 'reddit':
                        echo '<li><a href="' . esc_url( 'http://www.reddit.com/submit?url=' . get_the_permalink( $post->ID ) . '&title=' . get_the_title( $post->ID ) ) . '" rel="nofollow noopener" target="_blank"><i class="fab fa-reddit" aria-hidden="true"></i></a></li>';        
                        break;

                    }
                } ?>
            </ul>
            <a href="Javascript:void(0);" class="meta-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="17.358" height="17.345" viewBox="0 0 17.358 17.345">
                    <path
                        d="M157.37,340.59a2.208,2.208,0,0,1-.486-.054,2.15,2.15,0,0,1-1.71-2.092,1.171,1.171,0,0,0-.814-1.236c-1.823-.966-3.705-1.987-5.587-3.011a.77.77,0,0,0-.443-.128.845.845,0,0,0-.669.326,3.028,3.028,0,0,1-3.308.5,3.18,3.18,0,0,1-1.853-3.052,2.944,2.944,0,0,1,1.859-2.637,2.985,2.985,0,0,1,1.294-.3,2.934,2.934,0,0,1,2.058.837.8.8,0,0,0,.622.275.841.841,0,0,0,.5-.165c1.917-1.038,3.9-2.1,5.879-3.162a.8.8,0,0,0,.449-.954,2.193,2.193,0,0,1,2.063-2.237l.141-.005c.076,0,.151,0,.226.011a2.214,2.214,0,0,1-.2,4.419l-.118,0a2.061,2.061,0,0,1-1.217-.46.752.752,0,0,0-.521-.2.783.783,0,0,0-.458.147c-1.958,1.057-3.969,2.142-5.983,3.222a.739.739,0,0,0-.417.847c0,.183,0,.445,0,.708,0,.19-.005.38,0,.57a.809.809,0,0,0,.284.6l.04.035,6.181,3.338a.754.754,0,0,0,.364.1.767.767,0,0,0,.491-.193,2.16,2.16,0,0,1,2.766.042,2.216,2.216,0,0,1-1.428,3.91Zm.01-3.611a1.4,1.4,0,0,0-.043,2.8h0a1.422,1.422,0,0,0,1.442-1.373,1.4,1.4,0,0,0-1.4-1.426Zm-11.751-7.248a2.3,2.3,0,1,0,.006,0Zm11.743-5.42a1.515,1.515,0,1,0,.027,0Z"
                        transform="translate(-142.37 -323.37)" fill="#374757" stroke="#374757" stroke-width="0.25" /></svg>
            </a>
        </div>
    <?php 
    }
}

/*
* Get Taxonomies registered for Delicious Recipes
*/
function delicious_recipes_get_taxonomies() {
    $taxonomies = array(
        'recipe-course'         => __( 'Recipe Courses', 'delicious-recipes' ),
        'recipe-cuisine'        => __( 'Recipe Cuisines', 'delicious-recipes' ),
        'recipe-cooking-method' => __( 'Recipe Cooking Methods', 'delicious-recipes' ),
        'recipe-key'            => __( 'Recipe Keys', 'delicious-recipes' ),
        'recipe-tag'            => __( 'Recipe Tags', 'delicious-recipes' )
    );
    return $taxonomies;
}

/**
 * Check block is registered.
 *
 * @since 1.0.3
 */
if ( ! function_exists('delicious_recipes_block_is_registered') ) {
	function delicious_recipes_block_is_registered( $name ) {
		$WP_Block_Type_Registry = new WP_Block_Type_Registry();
		return $WP_Block_Type_Registry->is_registered( $name );
	}
}

/**
 * Returns options available for the surprise_me - menu
 * either strings to display the options or default values
 *
 * @since 0.7
 *
 * @param string $type optional either 'menu', 'widget' or 'block', defaults to 'widget'
 * @param string $key  optional either 'string' or 'default', defaults to 'string'
 * @return array list of surprise_me options strings or default values
 */
function get_surprise_me_options( $type = 'widget', $key = 'string' ) {
    $options = array(
        'show_icon'      => array( 'string' => __( 'Displays icon', 'delicious-recipes' ), 'default' => 1 ),
        'show_text'      => array( 'string' => __( 'Displays text', 'delicious-recipes' ), 'default' => 1 ),
        'show_text_icon' => array( 'string' => __( 'Displays text & icon', 'delicious-recipes' ), 'default' => 1 ),
        'show_posts'     => array( 'string' => __( 'Randomize posts', 'delicious-recipes' ), 'default' => 0 ),
    );
    return wp_list_pluck( $options, $key );
}

/**
 * Compatibility with multilingual plugins for home URL.
 *
 * @since 2.6.3
 */
function delicious_recipes_get_home_url() {
	$home_url = home_url();

	// Polylang Compatibility.
	if ( function_exists( 'pll_home_url' ) ) {
		$home_url = pll_home_url();
	}

	// Add trailing slash unless there are query parameters.
	if ( false === strpos( $home_url, '?' ) ) {
		$home_url = trailingslashit( $home_url );
	}

	return $home_url;
}

/**
 * Get Max Image Upload size.
 *
 * @since 1.1.2
 */
function delicious_recipes_get_max_upload_size() {
    $max_upload_size = wp_max_upload_size();
    if ( ! $max_upload_size ) {
        $max_upload_size = 0;
    }
    return size_format( $max_upload_size );
}

/**
 * Get time units.
 */
function delicious_recipes_get_time_units(){
    $time_units = array(
        "min"  => __("min", "delicious-recipes"),
        "hour" => __("hour", "delicious-recipes"),
    );

    $time_units = apply_filters( 'wp_delicious_time_units_options', $time_units );

    return $time_units;
}

/**
 * Sort array by priority.
 *
 * @return array $array
 */
function delicious_recipes_sort_array_by_priority( $array, $priority_key = 'priority' ) {
	$priority = array();
	if ( is_array( $array ) && count( $array ) > 0 ) {
		foreach ( $array as $key => $row ) {
			$priority[ $key ] = isset( $row[ $priority_key ] ) ? $row[ $priority_key ] : 1;
		}
		array_multisort( $priority, SORT_ASC, $array );
	}
	return $array;
}

/**
 * Get dashboard page ID or resort to default.
 *
 * @return void
 */
function delicious_recipes_get_dashboard_page_id() {
	$settings = delicious_recipes_get_global_settings();

	$dashboard_id = isset( $settings['dashboardPage'] ) ? esc_attr( $settings['dashboardPage'] ) : delicious_recipes_get_page_id( 'recipe-dashboard' );

	return $dashboard_id;
}

/**
 * Retrieve page permalink by id.
 *
 * @param string $page page id.
 * @return string
 */
function delicious_recipes_get_page_permalink_by_id( $page_id ) {
	$permalink = 0 < $page_id ? get_permalink( $page_id ) : get_home_url();
	return apply_filters( 'delicious_recipes_get_' . $page_id . '_permalink', $permalink );
}

if ( ! function_exists( 'delicious_recipes_is_account_page' ) ) {

	/**
	 * delicious_recipes_is_account_page - Returns true when viewing an account page.
	 *
	 * @return bool
	 */
	function delicious_recipes_is_account_page() {
		return is_page( delicious_recipes_get_dashboard_page_id() ) || delicious_recipes_post_content_has_shortcode( 'dr_user_dashboard' ) || apply_filters( 'delicious_recipes_is_account_page', false );
	}
}

/**
 * Retrieves unvalidated referer from '_wp_http_referer' or HTTP referer.
 *
 * Do not use for redirects, use {@see wp_get_referer()} instead.
 *
 * @since 1.3.3
 * @return string|false Referer URL on success, false on failure.
 */
function delicious_recipes_get_raw_referer() {
	if ( function_exists( 'wp_get_raw_referer' ) ) {
		return wp_get_raw_referer();
	}

	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		return wp_unslash( $_REQUEST['_wp_http_referer'] );
	} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		return wp_unslash( $_SERVER['HTTP_REFERER'] );
	}
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function delicious_recipes_clean_vars( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'delicious_recipes_clean_vars', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Add notices for WP Errors.
 *
 * @param WP_Error $errors Errors.
 */
function delicious_recipes_add_wp_error_notices( $errors ) {
	if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
		foreach ( $errors->get_error_messages() as $error ) {
			DEL_RECIPE()->notices->add( $error, 'error' );
		}
	}
}

/**
 * Get the count of notices added, either for all notices (default) or for one.
 * particular notice type specified by $notice_type.
 *
 * @param  string $notice_type Optional. The name of the notice type - either error, success or notice.
 * @return int
 */
function delicious_recipes_get_notice_count( $notice_type = '' ) {

	$notice_count = 0;
	$all_notices  = DEL_RECIPE()->notices->get( $notice_type, false );

	if ( ! empty( $all_notices ) && is_array( $all_notices ) ) {

		foreach ( $all_notices as $key => $notices ) {
			$notice_count++;
		}
	}

	return $notice_count;
}

/**
 * Print success and error notices set by Delicious Recipes.
 */
function delicious_recipes_print_notices() {
	// Print Errors / Notices.
	DEL_RECIPE()->notices->print_notices( 'error', true );
	DEL_RECIPE()->notices->print_notices( 'success', true );
}

/**
 * Get Email Templates content.
 */
function delicious_recipes_get_template_content( $email_template_type = 'new_account', $template = '', $sendto = 'admin', $default_content = false ) {
    $settings = get_option( 'delicious_recipe_settings', array() );
    $templates = array(
        'new_account' => array(
            'customer' => delicious_recipes_get_array_values_by_index( $settings, 'newAccountContent', '' ),
        ),
        'reset_password' => array(
            'customer' => delicious_recipes_get_array_values_by_index( $settings, 'resetPasswordContent', '' ),
        ),
    );

    $content = empty( $templates[ $email_template_type ][ $sendto ] ) || ( $default_content ) 
                ? '' : $templates[ $email_template_type ][ $sendto ];

    if ( ! empty( $content ) ) {
        return $content;
    }

    if ( empty( $template ) ) {
        switch ( $email_template_type ) {
            case 'new_account':
                $template = 'emails/customer-new-account.php';
                break;
            case 'reset_password':
                $template = 'emails/customer-reset-password.php';
                break;
            default:
                $template = 'emails/customer-new-account.php';
                break;
        }
    }

    return delicious_recipes_get_template_html( $template );
}

/**
 * Added display style tag as allowed CSS attributes for Email Templates.
 */
add_filter( 'safe_style_css', function( $styles ) {
    $styles[] = 'display';
    return $styles;
} );

function delicious_recipes_is_pro_activated() {
	$pro_activated = class_exists("DR_PRO\Delicious_Recipes_Pro");

	return $pro_activated;
}
