<?php
/**
 * Template to be used for the recipe print page.
 *
 * @since       1.0.8
 * 
 * @package     Delicious Recipes
 */

$asset_script_path = '/min/';
$min_prefix    = '.min';

if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
	$asset_script_path = '/';
	$min_prefix    = '';
}
$recipe_card_image = '';
?>
<!DOCTYPE html>
<html <?php echo get_language_attributes(); ?>>
    <head>
        <title><?php echo $recipe->post_title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_bloginfo( 'charset' ); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="robots" content="noindex">
        <?php wp_site_icon(); ?>
        <link rel="stylesheet" href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ) . '/assets/public/css' . $asset_script_path . 'recipe-print' . $min_prefix . '.css'; ?>" media="screen,print">
	    <?php delicious_recipes_get_template( 'global/dynamic-css.php' ); ?>
    </head>
    <body class="delrecipes-block-print" data-recipe-id="<?php echo esc_attr( $recipe_id ); ?>">
        <?php
            if ( ! is_array( $attributes ) ) {
                echo $content;
            } else {
                extract( $attributes );

                // Recipe post variables
                $recipe_id              = $recipe->ID;
                $recipe_title           = get_the_title( $recipe );
                $recipe_thumbnail_url   = get_the_post_thumbnail_url( $recipe );
                $recipe_thumbnail_id    = get_post_thumbnail_id( $recipe );
                $recipe_permalink       = get_the_permalink( $recipe );
                $recipe_author_name     = get_the_author_meta( 'display_name', $recipe->post_author );
                $attachment_id          = isset( $image['id'] ) ? $image['id'] : $recipe_thumbnail_id;

                // Variables from attributes
                // add default value if not exists
                $recipeTitle = isset( $recipeTitle ) ? $recipeTitle : '';
                $summary     = isset( $summary ) ? $summary : '';
                $className   = isset( $className ) ? $className : '';
                $hasImage    = isset( $hasImage ) ? $hasImage : false;
                $course      = isset( $course ) ? $course : array();
                $cuisine     = isset( $cuisine ) ? $cuisine : array();
                $method      = isset( $method ) ? $method : array();
                $recipeKey   = isset( $recipeKey ) ? $recipeKey : array();
                $difficulty  = isset( $difficulty ) ? $difficulty : array();
                $keywords    = isset( $keywords ) ? $keywords : array();
                $details     = isset( $details ) ? $details : array();
                $ingredients = isset( $ingredients ) ? $ingredients : array();
                $steps       = isset( $steps ) ? $steps : array();

                // Store variables
                $helpers  = new Delicious_Recipes_Helpers();
                $settings = $helpers->parse_block_settings( $attributes );

                Delicious_Dynamic_Recipe_Card::$recipeBlockID = isset( $id ) ? esc_attr( $id ) : 'dr-dynamic-recipe-card';
                Delicious_Dynamic_Recipe_Card::$attributes = $attributes;
                Delicious_Dynamic_Recipe_Card::$settings = $settings;

                Delicious_Dynamic_Recipe_Card::$attributes['summaryTitle']     = isset( $summaryTitle ) ? $summaryTitle : __('Description', 'delicious-recipes');
                Delicious_Dynamic_Recipe_Card::$attributes['ingredientsTitle'] = isset( $ingredientsTitle ) ? $ingredientsTitle : __('Ingredients', 'delicious-recipes');
                Delicious_Dynamic_Recipe_Card::$attributes['directionsTitle']  = isset( $directionsTitle ) ? $directionsTitle : __('Instructions', 'delicious-recipes');
                Delicious_Dynamic_Recipe_Card::$attributes['videoTitle']       = isset( $videoTitle ) ? $videoTitle : __('Video', 'delicious-recipes');
                Delicious_Dynamic_Recipe_Card::$attributes['difficultyTitle']  = isset( $difficultyTitle ) ? $difficultyTitle : __('Difficulty', 'delicious-recipes');
                Delicious_Dynamic_Recipe_Card::$attributes['seasonTitle']      = isset( $seasonTitle ) ? $seasonTitle : __('Best Season', 'delicious-recipes');
                Delicious_Dynamic_Recipe_Card::$attributes['notesTitle']       = isset( $notesTitle ) ? $notesTitle : __('Notes', 'delicious-recipes');

                $class = 'dr-summary-holder wp-block-delicious-recipes-block-recipe-card';
                $class .= $hasImage && isset($image['url']) ? '' : ' recipe-card-noimage';
                $RecipeCardClassName = implode( ' ', array( $class, $className ) );

                $custom_author_name = $recipe_author_name;
                if ( ! empty( $settings['custom_author_name'] ) ) {
                    $custom_author_name = $settings['custom_author_name'];
                }

                if ( $hasImage && isset( $image['url'] ) ) {
                    $img_id    = $image['id'];
                    $src       = $image['url'];
                    $alt       = ( $recipeTitle ? strip_tags( $recipeTitle ) : strip_tags( $recipe_title ) );
                    $img_class = ' delicious-recipes-card-image';
        
                    // Check if attachment image is from imported content
                    // in this case we don't have attachment in our upload directory
                    $upl_dir = wp_upload_dir();
                    $findpos = strpos( $src, $upl_dir['baseurl'] );
        
                    if ( $findpos === false ) {
                        $attachment = sprintf(
                            '<img src="%s" alt="%s" class="%s"/>',
                            $src,
                            $alt,
                            trim( $img_class )
                        );
                    }
                    else {
                        $attachment = wp_get_attachment_image(
                            $img_id,
                            'recipe-feat-print',
                            false,
                            array(
                                'alt' => $alt,
                                'id' => $img_id,
                                'class' => trim( $img_class )
                            )
                        );
                    }
        
                    $recipe_card_image = $attachment;
                }
                elseif ( ! $hasImage && ! empty( $recipe_thumbnail_url ) ) {
                    $img_id = $recipe_thumbnail_id;
                    $src 	= $recipe_thumbnail_url;
                    $alt 	= ( $recipeTitle ? strip_tags( $recipeTitle ) : strip_tags( $recipe_title ) );
                    $img_class = ' delicious-recipes-card-image';
        
                    // Check if attachment image is from imported content
                    // in this case we don't have attachment in our upload directory
                    $upl_dir = wp_upload_dir();
                    $findpos = strpos( $src, $upl_dir['baseurl'] );
        
                    if ( $findpos === false ) {
                        $attachment = sprintf(
                            '<img src="%s" alt="%s" class="%s"/>',
                            $src,
                            $alt,
                            trim( $img_class )
                        );
                    }
                    else {
                        $attachment = wp_get_attachment_image(
                            $img_id,
                            'recipe-feat-print',
                            false,
                            array(
                                'alt'   => $alt,
                                'id'    => $img_id,
                                'class' => trim( $img_class )
                            )
                        );
                    }
        
                    $recipe_card_image = $attachment;
                } 

                $details_content = '';

                if( $settings['displayCookingMethod'] && ! empty( $method ) ) {
                    $label     = __( "Cooking Method", "delicious-recipes" );
			        $svg       = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ).'assets/images/sprite.svg#cooking-method"></use></svg>';
                    $details_content .= sprintf(
                        '<div class="%1$s">%2$s<b>%3$s</b><span>%4$s</span></div>',
                        'dr-ingredient-meta',
                        $svg,
                        $label,
                        implode( ', ', $method )
                    );
                }
                if( $settings['displayCuisine'] && ! empty( $cuisine ) ) {
                    $label     = __( "Cuisine", "delicious-recipes" );
			        $svg       = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ).'assets/images/sprite.svg#cuisine"></use></svg>';
                    $details_content .= sprintf(
                        '<div class="%1$s">%2$s<b>%3$s</b><span>%4$s</span></div>',
                        'dr-ingredient-meta',
                        $svg,
                        $label,
                        implode( ', ', $cuisine )
                    );
                }
                if( $settings['displayCourse'] && ! empty( $course ) ) {
                    $label     = __( "Courses", "delicious-recipes" );
			        $svg       = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ).'assets/images/sprite.svg#category"></use></svg>';
                    $details_content .= sprintf(
                        '<div class="%1$s">%2$s<b>%3$s</b><span>%4$s</span></div>',
                        'dr-ingredient-meta',
                        $svg,
                        $label,
                        implode( ', ', $course )
                    );
                }
                if( $settings['displayRecipeKey'] && ! empty( $recipeKey ) ) {
                    $label     = __( "Recipe Keys", "delicious-recipes" );
			        $svg       = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ).'assets/images/sprite.svg#recipe-keys"></use></svg>';
                    $details_content .= sprintf(
                        '<div class="%1$s">%2$s<b>%3$s</b><span>%4$s</span></div>',
                        'dr-ingredient-meta',
                        $svg,
                        $label,
                        implode( ', ', $recipeKey )
                    );
                }
                
                $difficulty     = isset( $difficulty ) && $settings['displayDifficulty'] ? $difficulty : '';
                $difficultyTitle  = isset( $difficultyTitle ) ? $difficultyTitle : __('Difficulty', 'delicious-recipes');
                if( $difficulty) {
                    $svg = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ). 'assets/images/sprite.svg#difficulty"></use></svg>';
                    $details_content .= sprintf(
                        '<div class="%1$s">%2$s<b>%3$s</b><span>%4$s</span></div>',
                        'dr-ingredient-meta',
                        $svg,
                        $difficultyTitle,
                        ucfirst( $difficulty )
                    );
                }

                foreach ( $details as $index => $detail ) {
                    $value    = '';
                    $icon_svg = '';
                    $icon     = ! empty( $detail['icon'] ) ? $detail['icon'] : '';
                    $label    = ! empty( $detail['label'] ) ? $detail['label'] : '';
                    $unit     = ! empty( $detail['unit'] ) ? $detail['unit'] : '';
        
                    if( ! empty( $icon ) ) {
                        $icon_svg = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ). 'assets/images/sprite.svg#'.$icon.'"></use></svg>';
                    }
        
                    if ( ! empty( $detail[ 'value' ] ) ) {
                        if ( ! is_array( $detail['value'] ) ) {
                            $value = $detail['value'];
                        } elseif ( isset( $detail['jsonValue'] ) ) {
                            $value = $detail['jsonValue'];
                        }
                    }
                    
                    if ( 0 === $index && $settings['displayPrepTime'] != '1' ) {
                        continue;
                    } elseif ( 1 === $index && $settings['displayCookingTime'] != '1' ) {
                        continue;
                    } elseif ( 2 === $index && $settings['displayRestTime'] != '1' ) {
                        continue;
                    } elseif ( 3 === $index && $settings['displayTotalTime'] != '1' ) {
                        continue;
                    } elseif ( 4 === $index && $settings['displayServings'] != '1' ) {
                        continue;
                    } elseif ( 5 === $index && $settings['displayCalories'] != '1' ) {
                        continue;
                    }
        
                    // convert minutes to hours for 'prep time', 'cook time' and 'total time'
                    if ( 0 === $index || 1 === $index || 2 === $index || 3 === $index ) {
                        if ( ! empty( $detail['value'] ) ) {
                            $converts = $helpers->convertMinutesToHours( $detail['value'], true );
                            if ( ! empty( $converts ) ) {
                                $value = $unit = '';
                                if ( isset( $converts['hours'] ) ) {
                                    $value .= $converts['hours']['value'];
                                    $value .= ' '. $converts['hours']['unit'];
                                }
                                if ( isset( $converts['minutes'] ) ) {
                                    $unit .= $converts['minutes']['value'];
                                    $unit .= ' '. $converts['minutes']['unit'];
                                }
                            }
                        }
                    }
        
                    $details_content .= sprintf(
                        '<div class="%1$s">%2$s<b>%3$s</b><span>%4$s</span></div>',
                        'dr-ingredient-meta',
                        $icon_svg,
                        $label ,
                        $value .' '. $unit
                    );

                }

                $season      = isset( $season ) && $settings['displayBestSeason'] ? $season : '';
                $seasonTitle = isset( $seasonTitle ) ? $seasonTitle : __('Best Season', 'delicious-recipes');
                if( $season) {
                    $svg = '<svg class="icon"><use xlink:href="'.esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ). 'assets/images/sprite.svg#season"></use></svg>';
                    $details_content .= sprintf(
                        '<div class="%1$s">%2$s<b>%3$s</b><span>%4$s</span></div>',
                        'dr-ingredient-meta',
                        $svg,
                        $seasonTitle,
                        ucfirst( $season )
                    );
                }

                // $details_content     = Delicious_Dynamic_Recipe_Card::get_details_content( $details );
                $ingredients_content = Delicious_Dynamic_Recipe_Card::get_ingredients_content( $ingredients );
                $steps_content       = Delicious_Dynamic_Recipe_Card::get_steps_content( $steps );

                $summary_text = '';
                $summaryTitle = isset( $summaryTitle ) ? $summaryTitle : __('Description', 'delicious-recipes');
                if ( ! empty( $summary ) ) {
                    $summary_class = 'dr-pring-block-header';
                    $summary_text = sprintf(
                        '<div class="%s"><div class="%s"><span>%s</span></div></div>
                        <div class="dr-pring-block-content">%s</div>',
                        esc_attr( $summary_class ),
                        'dr-print-block-title',
                        $summaryTitle,
                        $summary
                    );
                }

                $strip_tags_notes = isset( $notes ) ? strip_tags($notes) : '';
                $notes            = isset( $notes ) ? str_replace('<li></li>', '', $notes) : '';     // remove empty list item
                $notesTitle       = isset( $notesTitle ) ? $notesTitle : __('Notes', 'delicious-recipes');
                $notes_content    = ! empty($strip_tags_notes) ?
                    sprintf(
                        '<div class="dr-note">
                            <div class="dr-print-block-title"><span>%s</span></div>
                            %s
                        </div>',
                        $notesTitle,
                        $notes
                    ) : '';

                $keywords_text = '';
                if ( ! empty( $keywords ) ) {
                    $keywords_class = 'dr-keywords dr-keywords-block';
                    $keywords_text = sprintf(
                        '<div class="%s"><span class="%s">%s</span>%s</div>',
                        esc_attr( $keywords_class ),
                        'dr-meta-title',
                        __("Keywords: ", 'delicious-recipes'),
                        implode( ', ', $keywords )
                    );
                }

                ?>
                <div class="dr-print-outer-wrap">
                    <div id="dr-page1" class="dr-print-header">
                        <h1 id="dr-print-title" class="dr-print-title"><?php echo $recipe->post_title;?></h1>
                        <div class="dr-print-img">
                            <?php echo wp_kses_post( $recipe_card_image ); ?>
                        </div>
                    </div><!-- #dr-page1 -->
                    <div id="dr-page2" class="dr-print-page dr-print-ingredients">
			            <div class="dr-ingredient-meta-wrap">
                            <?php echo $details_content; ?>
                        </div>
			            <div class="dr-print-block-wrap">
                            <div class="dr-print-block dr-ingredients-wrap">
                                <?php echo wp_kses_post( $ingredients_content ); ?>
                            </div>
                            <div class="dr-print-block dr-description-wrap">
                                <?php echo wp_kses_post( $summary_text ); ?>
                            </div>
                        </div>
                    </div><!-- #dr-page2 -->
                    <div id="dr-page3" class="dr-print-page dr-print-instructions">
				        <div class="dr-print-block">
                            <?php echo wp_kses_post( $steps_content ); ?>
                        </div>
                    </div><!-- #dr-page3 -->
                    <div id="dr-page5" class="dr-print-page dr-print-nutrition">
                        <div class="dr-print-block dr-wrap-notes-keywords">
                            <?php echo wp_kses_post( $notes_content ); ?>
                            <?php echo wp_kses_post( $keywords_text ); ?>
                        </div>
                    </div><!-- #dr-page5 -->
                </div>
                <?php
            }
        ?>
    </body>
</html>
