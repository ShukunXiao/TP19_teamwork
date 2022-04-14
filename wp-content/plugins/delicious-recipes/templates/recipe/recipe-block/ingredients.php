<?php
/**
 * Recipe main ingredients section.
 */
global $recipe;
$global_settings = delicious_recipes_get_global_settings();
$ingredientTitle = isset( $recipe->ingredient_title ) ? $recipe->ingredient_title : __( 'Ingredients', 'delicious-recipes' );

    if( isset( $recipe->ingredients ) && ! empty( $recipe->ingredients ) ) : 
        $servings_value = ! empty( $recipe->no_of_servings ) ? esc_attr( $recipe->no_of_servings ) : 1;
    ?>
        <div class="dr-ingredients-list">
            <div class="dr-ingrd-title-wrap">
                <h3 class="dr-title"><?php echo esc_html( $ingredientTitle ); ?></h3>
                <div class="dr-ingredients-scale">
                    <label for="select"><?php esc_html_e( 'Servings', 'delicious-recipes' ); ?></label>
                    <input type="number" data-original="<?php echo esc_attr( $servings_value ); ?>" data-recipe="<?php echo esc_attr( $recipe->ID ); ?>" value="<?php echo esc_attr( $servings_value ); ?>" step="1" min="1" class="dr-scale-ingredients">
                </div>
            </div>
            <?php
                $ingredient_string_format = isset( $global_settings['ingredientStringFormat'] ) ? $global_settings['ingredientStringFormat'] : '{qty} {unit} {ingredient} {notes}';
                foreach( $recipe->ingredients as $key => $ingre_section ) :
                    $section_title = isset( $ingre_section['sectionTitle'] ) ? $ingre_section['sectionTitle'] : '';
                    $ingre         = isset( $ingre_section['ingredients'] ) ? $ingre_section['ingredients'] : array();
            ?>
                <h4 class="dr-title"><?php echo esc_html( $section_title ); ?></h4>
                <ul class="dr-unordered-list">
                    <?php foreach( $ingre as $ingre_key => $ingredient ) : 

                        $rand_key        = rand(10, 10000);
                        $ingredient_qty  = isset( $ingredient['quantity'] ) ? $ingredient['quantity'] : 0;
                        $ingredient_unit = isset( $ingredient['unit'] ) ? $ingredient['unit'] : '';
                        $unit_text       = ! empty( $ingredient_unit ) ? delicious_recipes_get_unit_text( $ingredient_unit, $ingredient_qty ) : '';

                        $ingredient_keys = array(
                            '{qty}'        => isset( $ingredient['quantity'] ) ? '<span class="ingredient_quantity" data-original="'. $ingredient['quantity'] .'" data-recipe="'. $recipe->ID .'">' . $ingredient['quantity'] . '</span>' : '',
                            '{unit}'       => $unit_text,
                            '{ingredient}' => isset( $ingredient['ingredient'] ) ? $ingredient['ingredient'] : '',
                            '{notes}'      => isset( $ingredient['notes'] ) && ! empty( $ingredient['notes'] ) ? '<span class="ingredient-notes" >(' . $ingredient['notes'] . ')</span>' : '',
                        );
                        $ingre_string = str_replace( array_keys( $ingredient_keys ), $ingredient_keys, $ingredient_string_format );
                    ?>
                        <li>
                            <input type="checkbox" name="" value="" id="dr-ing-<?php echo esc_attr( $ingre_key ); ?>-<?php echo esc_attr( $rand_key ); ?>">
                            <label for="dr-ing-<?php echo esc_attr( $ingre_key ); ?>-<?php echo esc_attr( $rand_key ); ?>"><?php echo wp_kses_post( $ingre_string ); ?></label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php 
                endforeach;
            ?>
        </div>
<?php
    endif;
