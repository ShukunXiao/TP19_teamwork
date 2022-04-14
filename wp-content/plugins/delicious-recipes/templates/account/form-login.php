<?php
/**
 * Login Form template.
 *
 * @package Delicious_Recipes
 */

// Print Errors / Notices.
delicious_recipes_print_notices();

$global_toggles  = delicious_recipes_get_global_toggles_and_labels();
$global_settings = delicious_recipes_get_global_settings();
$login_image     = isset( $global_settings['loginImage'] ) && ! empty( $global_settings['loginImage'] ) ? $global_settings['loginImage'] : false;
?>
<?php if( isset( $popup ) && $popup ) : ?>
	<div class="delicious-recipes-success-msg" style="display:none"></div>
	<div class="delicious-recipes-error-msg" style="display:none"></div>
<?php endif; ?>

<div class="dr-container">
	<div class="dr-form-wrapper dr-form__log-in">
		<div class="dr-form__inner-wrapper">
			<div class="dr-form__grid">
				<form class="dr-form__fields-wrapper" method="post" name="dr-form__log-in">
					<div class="dr-form__heading">
						<h2 class="dr-form__title"><?php esc_html_e( 'Log In', 'delicious-recipes' ); ?></h2>
					</div>

					<?php do_action( 'delicious_recipes_login_fields_before' ); ?>

					<div class="dr-form__field">
						<label for="user-email"><?php esc_html_e( 'Email Or Username', 'delicious-recipes' ); ?></label>
						<input required data-parsley-required-message="<?php esc_attr_e( 'Please enter your valid email or username', 'delicious-recipes' ) ?>" type="text" id="user-email" name="username" class="dr-form__field-input" placeholder="e.g.deliciousrecipes">
					</div>
					<div class="dr-form__field">
						<label for="password"><?php esc_html_e( 'Password', 'delicious-recipes' ); ?></label>
						<input required data-parsley-required-message="<?php esc_attr_e( 'Please enter your password', 'delicious-recipes' ) ?>" type="password" id="password" name="password" class="dr-form__field-input" placeholder="enter your password here">
					</div>
					
					<?php wp_nonce_field( 'delicious_recipes_user_login', 'delicious_recipes_user_login_nonce' ); ?>

					<div class="dr-form__field-submit">
						<input type="submit" name="login" value="Sign In" class="dr-form__submit w-100">
						<div class="dr-form__checkbox">
							<input type="checkbox" id="remember-me" name="rememberme">
							<label for="remember-me">
								<?php esc_html_e( 'Remember Me', 'delicious-recipes' ); ?>
							</label>
						</div>
						<a href="<?php echo esc_url( delicious_recipes_lostpassword_url() ); ?>" class="dr-other-link"><?php esc_html_e( 'Forgot Password?', 'delicious-recipes' ); ?></a>
					</div>
					<?php if( $global_toggles['enable_user_registration'] ) : ?>
						<div class="dr-form__footer">
							<p><?php esc_html_e( "Not registered yet?", 'delicious-recipes' ); ?> <a href="<?php echo esc_url( add_query_arg( 'register', true, delicious_recipes_get_page_permalink_by_id( delicious_recipes_get_dashboard_page_id() ) ) ); ?>"><?php esc_html_e( 'Create an Account', 'delicious-recipes' ); ?></a></p>
						</div>
					<?php endif; ?>

					<?php do_action( 'delicious_recipes_login_fields_after' ); ?>

				</form>

				<?php if( ! ( isset( $popup ) && $popup ) ) : ?>
					<div class="dr-form__img-wrapper">
						<div class="dr-img-holder">
							<?php 
								if( $login_image ) : 
									echo wp_get_attachment_image( $login_image, 'full' ); 
								else: ?>
									<img src="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>/src/dashboard/img/dr-login-img.svg" alt="">
								<?php endif; 
							?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php
