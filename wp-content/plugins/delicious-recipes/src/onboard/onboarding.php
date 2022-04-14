<?php
/**
 * User onboarding process.
 *  
 * @package DELICIOUS_RECIPES
 */

defined( 'ABSPATH' ) || exit;

/**
 * Setup wizard header template.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta name="viewport" content="width=device-width"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php esc_html_e( 'Delicious Recipes - User Onboarding', 'delicious-recipes' ); ?></title>
        <?php wp_print_head_scripts(); ?>
        <?php wp_print_styles( 'delicious-recipes-onboard' ); ?>
    </head>

    <?php
        /**
         * Setup body template.
        */
    ?>
    <body class="dr-user-onboarding-wizrad dr-user-onboarding-wizrad-body <?php echo is_rtl() ? ' rtl' : ''; ?>">
        <div id="delicious-recipe-onboarding" class="dr-recipes-main-wrap" data-rest-nonce="<?php echo wp_create_nonce( 'wp_rest' ); ?>">
        </div>
    </body>
    
    <?php
        /**
         * Setup wizard footer template.
         */
        if ( function_exists( 'wp_print_media_templates' ) ) {
            wp_print_media_templates();
        }
        wp_print_footer_scripts();
        wp_print_scripts( 'delicious-recipes-onboard' );
    ?>
</html>