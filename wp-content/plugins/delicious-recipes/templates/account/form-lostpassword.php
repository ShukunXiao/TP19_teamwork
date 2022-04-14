<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipes/account/form-lostpassword.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Print Errors / Notices.
delicious_recipes_print_notices();

?>
<div class="dr-container">

	<div class="dr-form-wrapper dr-form__forgot-password">
		<div class="dr-form__inner-wrapper">
			<form class="dr-form__fields-wrapper" method="post">
				<div class="dr-form__heading">
					<h2 class="dr-form__title"><?php esc_html_e( 'Lost Your Password?', 'delicious-recipes' ); ?></h2>
					<div class="dr-form__desc">
						<?php esc_html_e( 'Please enter you username or email address. You will receive a link to create a new password via email.', 'delicious-recipes' ); ?>
					</div>
				</div>

				<?php do_action( 'delicious_recipes_lostpassword_fields_before' ); ?>

				<div class="dr-form__field">
					<label for="user-login"><?php esc_html_e( 'Email Or Username', 'delicious-recipes' ); ?></label>
					<input type="text" id="user-login" name="user_login" class="dr-form__field-input" placeholder="e.g.deliciousrecipes">
				</div>

				<?php wp_nonce_field( 'delicious_recipes_lost_password' ); ?>

				<div class="dr-form__field-submit">
					<input type="hidden" name="delicious_recipes_reset_password" value="true" />
					<input type="submit" name="delicious_recipes_reset_password_submit" value="Reset Password" class="dr-form__submit w-100">
					<a href="<?php echo esc_url( get_permalink() ); ?>" class="dr-other-link"><?php esc_html_e( 'Back to Sign in?', 'delicious-recipes' ); ?></a>
				</div>

				<?php do_action( 'delicious_recipes_lostpassword_fields_after' ); ?>

			</form>
		</div>
	</div>

</div>
<?php
