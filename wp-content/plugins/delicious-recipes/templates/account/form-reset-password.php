<?php
/**
 * Customer Lost Password Reset Form.
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipes/account/form-reset-password.php.
 *
 * HOWEVER, on occasion Delicious Recipes will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
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
					<h2 class="dr-form__title"><?php esc_html_e( 'Enter your New Password.', 'delicious-recipes' ); ?></h2>
				</div>

				<?php do_action( 'delicious_recipes_resetpassword_fields_before' ); ?>

				<div class="dr-form__field">
					<label for="password_1"><?php esc_html_e( 'New Password', 'delicious-recipes' ); ?></label>
					<input required type="password" id="password_1" name="password_1" class="dr-form__field-input" placeholder="New Password">
				</div>

				<div class="dr-form__field">
					<label for="password_2"><?php esc_html_e( 'Re-enter New Password', 'delicious-recipes' ); ?></label>
					<input required type="password" id="password_2" name="password_2" class="dr-form__field-input" placeholder="Re-enter New Password">
				</div>

				<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
				<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />
				<?php wp_nonce_field( 'delicious_recipes_reset_password_nonce' ); ?>

				<div class="dr-form__field-submit">
					<input type="hidden" name="delicious_recipes_reset_password" value="true" />
					<input type="submit" name="delicious_recipes_reset_password_submit" value="Save" class="dr-form__submit w-100">
				</div>

				<?php do_action( 'delicious_recipes_resetpassword_fields_after' ); ?>

			</form>
		</div>
	</div>

</div>
<?php
