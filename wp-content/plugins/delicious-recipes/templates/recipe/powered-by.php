<?php
/**
 * Powered by and notes block
 * 
 * @package Delicious_Recipes
 */

// Get global settings.
$global_settings = delicious_recipes_get_global_settings();
$enable_poweredby = isset( $global_settings['enablePoweredBy']['0'] ) && 'yes' === $global_settings['enablePoweredBy']['0'] ? true : false;
$affiliate_link = isset( $global_settings['affiliateLink'] ) && $global_settings['affiliateLink'] ? $global_settings['affiliateLink'] : 'https://wpdelicious.com/';

    /**
     * Hook to fire before the recipe card powered by section.
     */
    do_action( 'delicious_recipes_before_recipe_card_powered_by' );

    if( $enable_poweredby ) : ?>
    
        <div class="dr-poweredby">
            <span><?php esc_html_e( 'Recipe Card powered by', 'delicious-recipes' ); ?></span>
            <a href="<?php echo esc_url( $affiliate_link); ?>" target="_blank" rel="nofollow noopener sponsored" ><img src="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>/assets/images/Delicious-Recipes.png" alt="Delicious Recipes"></a>
        </div>

    <?php endif;
    
    /**
     * Hook to fire before the recipe card powered by section.
     */
    do_action( 'delicious_recipes_before_recipe_card_powered_by' );
