<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipes/account/lost-password-confirmation.php.
 *
 * HOWEVER, on occasion Delicious Recipes will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Print Errors / Notices.
delicious_recipes_print_notices();
?>
<div class="dr-container">

	<div class="dr-form-wrapper dr-form-reset-info" style="max-width: 860px;margin:0 auto;">
		<div class="dr-form__inner-wrapper">
			<div class="dr-form__fields-wrapper">

				<div style="margin-bottom: 30px;">
					<svg xmlns="http://www.w3.org/2000/svg" width="56.486" height="56.486"
						viewBox="0 0 56.486 56.486">
						<g id="Group_6104" data-name="Group 6104" transform="translate(-903 -854)">
							<g id="Union_2" data-name="Union 2" transform="translate(909.926 854)" fill="#fff">
								<path
									d="M18.612,40.255,0,27.486V0H42.635V27.486L24.023,40.255a5.279,5.279,0,0,1-2.786.811A4.5,4.5,0,0,1,18.612,40.255Z"
									stroke="none" />
								<path
									d="M 21.23640060424805 37.066162109375 C 21.43899345397949 37.066162109375 21.65599250793457 37.00473403930664 21.8532600402832 36.89231491088867 L 38.63529968261719 25.37899017333984 L 38.63529968261719 4.000000953674316 L 3.999999761581421 4.000000953674316 L 3.999999761581421 25.37900543212891 L 20.87512969970703 36.9563102722168 L 20.90622901916504 36.97764205932617 L 20.93691062927246 36.99956130981445 C 20.97992897033691 37.03028106689453 21.08417892456055 37.066162109375 21.23640060424805 37.066162109375 M 21.23640060424805 41.066162109375 C 20.28955078125 41.066162109375 19.36974906921387 40.79566955566406 18.61224937438965 40.25469970703125 L -1.5869140668201e-07 27.48563194274902 L -1.5869140668201e-07 1.054687459145498e-06 L 42.63529968261719 1.054687459145498e-06 L 42.63529968261719 27.48563194274902 L 24.02285957336426 40.25469970703125 C 23.15715026855469 40.79566955566406 22.18325042724609 41.066162109375 21.23640060424805 41.066162109375 Z"
									stroke="none" fill="rgba(45,182,141,0.2)" />
							</g>
							<g id="Group_6102" data-name="Group 6102" transform="translate(903 873.262)">
								<path id="Path_30794" data-name="Path 30794"
									d="M56.486,114v27.053a4.8,4.8,0,0,1-4.761,4.761H4.761A4.8,4.8,0,0,1,0,141.053V114l24.672,16.881a6.18,6.18,0,0,0,7.142,0Z"
									transform="translate(0 -108.589)" fill="#2db68d" fill-rule="evenodd" />
								<path id="Path_30795" data-name="Path 30795" d="M7.545,96.142,3,92.9,7.545,89Z"
									transform="translate(-2.351 -89)" fill="#2db68d" fill-rule="evenodd" />
								<path id="Path_30796" data-name="Path 30796" d="M241.545,92.9,237,96.142V89Z"
									transform="translate(-185.708 -89)" fill="#2db68d" fill-rule="evenodd" />
							</g>
							<rect id="Rectangle_1860" data-name="Rectangle 1860" width="18" height="3"
								transform="translate(917 865)" fill="#2db68d" />
							<rect id="Rectangle_1861" data-name="Rectangle 1861" width="26" height="3"
								transform="translate(917 872)" fill="#2db68d" />
						</g>
					</svg>
				</div>

				<div class="dr-form__heading no-border">
					<h2 class="dr-form__title"><?php esc_html_e( "Check Your Email", 'delicious-recipes' ); ?></h2>
				</div>
				<div class="dr-form__desc">
					<?php echo apply_filters( 'delicious_recipes_lost_password_message', __( 'A password reset email has been sent to the email address for your account, but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.', 'delicious-recipes' ) ); ?>
				</div>
			</div>
		</div>
	</div>

</div>
<?php
