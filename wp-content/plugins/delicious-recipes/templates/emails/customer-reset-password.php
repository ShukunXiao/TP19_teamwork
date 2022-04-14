<?php 
/**
 * Customer Lost Password email Template
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipes/emails/customer-reset-password.php.
 *
 * HOWEVER, on occasion Delicious Recipes will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php /* translators: %s: Customer username */ ?>
<p style="margin-top: 0;margin-bottom: 22px;"><?php printf( esc_html__( 'Hi %s,', 'delicious-recipes' ), '{username}' ); ?></p>
<p style="margin-top: 0;"><?php esc_html_e( "We're sending you this email because you requested a password reset. Click on this link to create a new password:", 'delicious-recipes' );?></p>
<a href="{reset_password_link}"	style="text-decoration: none;padding: 20px;display: block;background-color: #2DB68D;color: #fff;font-size: 20px;font-weight: normal;line-height: 1;text-align: center;margin-top: 45px;margin-bottom: 45px;"><?php esc_html_e( 'Set a New Password', 'delicious-recipes' ); ?></a>
<p style="margin-top: 0;"><?php esc_html_e( 'If this was a mistake, ignore this email and nothing will happen.', 'delicious-recipes');?></p>
<?php /* translators: %s: Site Title */ ?>
<p style="margin-top: 60px;margin-bottom: 0;"><?php printf( esc_html__( 'Thank you, %s Team', 'delicious-recipes' ), '<br> {site_title}' ); ?></p>
