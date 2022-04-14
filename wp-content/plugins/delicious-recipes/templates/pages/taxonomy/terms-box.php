<?php
/**
 * Terms box template.
 * 
 * @package Delicious_Recipes
 */
$recipe_tax_terms      = isset( $args['terms'] ) ? $args['terms'] : array();
$recipe_global         = delicious_recipes_get_global_settings();
$taxPagesTermsBoxTitle = isset( $recipe_global['taxPagesTermsBoxTitle'] ) ? $recipe_global['taxPagesTermsBoxTitle'] : __( 'Narrow Your Search', 'delicious-recipes' );

if ( ! empty( $recipe_tax_terms ) ) :
    ?>
        <div class="dr-archive-all-categories">
            <div class="dr-title"><?php echo esc_html( $taxPagesTermsBoxTitle ); ?></div>
            <div class="dr-archive-cat-wrap">
                <ul>
                    <?php foreach( $recipe_tax_terms as $key => $term ) : 
                        $term_link = get_term_link( $term );
                    ?>
                        <li>
                            <a href="<?php echo esc_url( $term_link ); ?>" alt="<?php echo esc_html( $term->name ); ?>">
                                <?php echo esc_html( $term->name ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php 
endif;
