<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipes/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion Delicious Recipes will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php /* translators: %s: Customer username */ ?>
<p style="margin-top: 0;margin-bottom: 22px;"><?php printf( esc_html__( 'Hi %s,', 'delicious-recipes' ), '{username}' ); ?></p>
<?php /* translators: %1$s: Site title, %2$s: Username */ ?>
<p style="margin-top: 0;margin-bottom: 40px"><?php printf( esc_html__( 'Thanks for creating an account on %1$s. Your username is %2$s.', 'delicious-recipes' ), '{site_title}', '<b>{username}</b>' );?></p>
<?php /* translators: %s: Password */ ?>
<p style="margin-top: 0;margin-bottom: 40px"><?php printf( esc_html__( 'Your password is: %s', 'delicious-recipes' ), '<b>{password}</b>' );?></p>
<p style="margin-top: 0;margin-bottom: 40px"><?php esc_html_e( 'Check your account to access your user dashboard to view your favorite lists and edit your profile:', 'delicious-recipes' );?></p>
<a href="{dashboard_page}" style="text-decoration: none;padding: 20px;display: block;background-color: #2DB68D;color: #fff;font-size: 20px;font-weight: normal;line-height: 1;text-align: center;margin-top: 45px;margin-bottom: 45px;"><?php esc_html_e( 'My Account', 'delicious-recipes'); ?></a>
<p style="margin-top: 0;"><?php esc_html_e( "If you have any questions, just reply to this email- we're always happy to help out.", 'delicious-recipes'); ?></p>
<?php /* translators: %s: Site Title */ ?>
<p style="margin-top: 60px;margin-bottom: 0;"><?php printf( esc_html__( 'Thank you, %s Team', 'delicious-recipes' ), '<br> {site_title}' ); ?></p>

<?php
