<?php
/**
 * Email Header Template.
 */

$global_settings = get_option( 'delicious_recipe_settings', array() ); 
$printLogoImage = isset( $global_settings['printLogoImage'] ) && ! empty( $global_settings['printLogoImage'] ) ? $global_settings['printLogoImage'] : false;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
	</head>

	<body itemscope itemtype="http://schema.org/EmailMessage">

		<!-- email template start from here -->
		<div style="max-width:600px;margin:0 auto;">
			<div style="background-color: rgba(45, 182, 141, .05);padding:40px 20px 20px;font-size: 18px;line-height: 1.8;">
				<?php if( $printLogoImage ) : ?>
					<div style="width: 100%;margin:0 auto 40px;">
						<img style="width:100%;height:auto;"
							src="<?php echo esc_url( wp_get_attachment_image_url( $printLogoImage, 'full' ) ); ?>"
							alt="<?php echo get_bloginfo( 'name', 'display' ); ?>">
					</div>
				<?php endif; ?>
				<div style="padding: 60px 45px 30px;background-color: #fff;font-family: 'arial';color: #374757;">
<?php
