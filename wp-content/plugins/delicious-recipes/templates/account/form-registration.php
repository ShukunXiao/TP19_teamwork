<?php
/**
 * Registration Form template.
 *
 * @package Delicious_Recipes
 */

// Print Errors / Notices.
delicious_recipes_print_notices();

$global_toggles     = delicious_recipes_get_global_toggles_and_labels();
$global_settings    = delicious_recipes_get_global_settings();
$registration_image = isset( $global_settings['registrationImage'] ) && ! empty( $global_settings['registrationImage'] ) ? $global_settings['registrationImage'] : false;
$terms_n_conditions_text = isset( $global_settings['termsNConditionsText'] ) && ! empty( $global_settings['termsNConditionsText'] ) ? $global_settings['termsNConditionsText'] : false;
?>
<div class="dr-container">

    <div class="dr-form-wrapper dr-form__sign-up">
        <div class="dr-form__inner-wrapper">
            <div class="dr-form__grid">
                <form class="dr-form__fields-wrapper" method="post" name="dr-form__sign-up">
                    <div class="dr-form__heading">
                        <h2 class="dr-form__title"><?php esc_html_e( 'Sign Up', 'delicious-recipes' ); ?></h2>
                    </div>

                    <?php do_action( 'delicious_recipes_registration_fields_before' ); ?>

                    <?php if( ! $global_toggles['generate_username'] ) : ?>
                        <div class="dr-form__field">
                            <label for="username"><?php esc_html_e( 'Username', 'delicious-recipes' ); ?></label>
                            <input required data-parsley-required-message="<?php esc_attr_e( 'Please enter your desired username', 'delicious-recipes' ) ?>" type="text" id="username" name="username" class="dr-form__field-input" placeholder="e.g.deliciousrecipes">
                        </div>
                    <?php endif; ?>
                    <div class="dr-form__field">
                        <label for="email"><?php esc_html_e( 'Email', 'delicious-recipes' ); ?></label>
                        <input required data-parsley-required-message="<?php esc_attr_e( 'Please enter a valid email address', 'delicious-recipes' ) ?>" type="email" id="email" name="email" class="dr-form__field-input" placeholder="e.g.deliciousrecipes@gmail.com">
                    </div>
                    <?php if( ! $global_toggles['generate_password'] ) : ?>
                        <div class="dr-form__field">
                            <label for="password"><?php esc_html_e( 'Password', 'delicious-recipes' ); ?></label>
                            <input required data-parsley-required-message="<?php esc_attr_e( 'Please enter a valid password', 'delicious-recipes' ) ?>" type="password" id="password" name="password" class="dr-form__field-input" placeholder="Create a password">
                        </div>
                        <div class="dr-form__field">
                            <label for="c-password"><?php esc_html_e( 'Confirm Password', 'delicious-recipes' ); ?></label>
                            <input required data-parsley-required-message="<?php esc_attr_e( 'Please enter a valid password', 'delicious-recipes' ) ?>" type="password" id="c-password" name="c-password" class="dr-form__field-input" placeholder="Confirm password">
                        </div>
                    <?php endif; ?>
                    <?php  
                        // Nonce security.
                        wp_nonce_field( 'delicious-recipes-user-register', 'delicious-recipes-user-register-nonce' ); 
                    ?>
                    <div class="dr-form__field-submit">
                        <?php if( $global_toggles['terms_n_conditions'] && $terms_n_conditions_text ) : ?>
                            <div class="dr-form__checkbox">
                                <input required data-parsley-required-message="<?php esc_attr_e( 'Please check the terms and conditions', 'delicious-recipes' ) ?>" type="checkbox" id="terms-conditions" name="termsnconditions">
                                <label for="terms-conditions">
                                    <?php echo esc_html( $terms_n_conditions_text ); ?>
                                </label>
                            </div>
                        <?php endif; ?>

                        <input type="submit" name="register" value="Register Now" class="dr-form__submit w-100">
                    </div>
                    <div class="dr-form__footer">
                        <p><?php esc_html_e( 'Already have an account?', 'delicious-recipes' ); ?> <a href="<?php echo esc_url( delicious_recipes_get_page_permalink_by_id( delicious_recipes_get_dashboard_page_id() ) ); ?>"><?php esc_html_e( 'Sign In', 'delicious-recipes' ); ?></a></p>
                    </div>

                    <?php do_action( 'delicious_recipes_registration_fields_after' ); ?>

                </form>
                <div class="dr-form__img-wrapper">
                    <div class="dr-img-holder">
                        <?php 
                            if( $registration_image ) : 
                                echo wp_get_attachment_image( $registration_image, 'full' ); 
                            else: ?>
                                <img src="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>/src/dashboard/img/dr-sign-up-img.svg" alt="">
                            <?php endif; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php
