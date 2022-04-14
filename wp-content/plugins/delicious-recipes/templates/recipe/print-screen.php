<?php
/**
 * Print Recipe Screen file.
 * 
 * @package Delicious_Recipes/Templates
 */
global $recipe;
$recipe_global = delicious_recipes_get_global_settings();

$embedRecipeLink = isset( $recipe_global['embedRecipeLink']['0'] ) && 'yes' === $recipe_global['embedRecipeLink']['0'] ? true : false;
$displaySocialSharingInfo = isset( $recipe_global['displaySocialSharingInfo']['0'] ) && 'yes' === $recipe_global['displaySocialSharingInfo']['0'] ? true : false;
$embedAuthorInfo = isset( $recipe_global['embedAuthorInfo']['0'] ) && 'yes' === $recipe_global['embedAuthorInfo']['0'] ? true : false;
$socials_enabled = ( isset( $recipe_global['socialShare']['0']['enable']['0'] ) 
&& 'yes' === $recipe_global['socialShare']['0']['enable']['0'] ) 
|| ( isset( $recipe_global['socialShare']['1']['enable']['0'] ) 
&& 'yes' === $recipe_global['socialShare']['1']['enable']['0'] ) ? true : false;
// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();

$asset_script_path = '/min/';
$min_prefix    = '.min';

if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
	$asset_script_path = '/';
	$min_prefix    = '';
}

?><!DOCTYPE html>
<html>
<head>
	<title><?php the_title(); ?></title>
	<link rel="stylesheet" href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ) . '/assets/public/css' . $asset_script_path . 'recipe-print' . $min_prefix . '.css'; ?>" media="screen,print">
	<?php delicious_recipes_get_template( 'global/dynamic-css.php' ); ?>
	<meta name="robots" content="noindex">
</head>
<body>
	<?php 
		$allowPrintCustomization = isset( $recipe_global['allowPrintCustomization']['0'] ) && 'yes' === $recipe_global['allowPrintCustomization']['0'] ? true : false;

		if ( $allowPrintCustomization ) :
			$printOptions = isset( $recipe_global['printOptions'] ) ? $recipe_global['printOptions'] : array();
			if ( ! empty( $printOptions ) ) :
				?>
					<div id="dr-print-options" class="dr-clearfix">
						<h3><?php esc_html_e( 'Print Options:', 'delicious-recipes' ) ?></h3>
						<?php foreach( $printOptions as $key => $printOPT ) : 
							$name   =  isset( $printOPT['key'] ) ? $printOPT['key'] : '';
							$enable = isset( $printOPT['enable']['0'] ) && 'yes' === $printOPT['enable']['0'] ? true : false;	
						?>
							<div class="dr-print-block">
								<input id="print_options_<?php echo esc_attr( sanitize_title( $name ) ); ?>" type="checkbox" name="print_options" value="1" <?php checked( $enable, true ); ?> />
								<label for="print_options_<?php echo esc_attr( sanitize_title( $name ) ); ?>"><?php esc_html_e( $name, 'delicious-recipes' ); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				<?php 
			endif;
		endif;
	?>
	<button class="dr-button" onclick="window.print();"><?php esc_html_e( 'Print', 'delicious-recipes' ) ?></button>
	<div class="dr-print-outer-wrap">
		<div id="dr-page1" class="dr-print-header">
		<?php 
			$printLogoImage = isset( $recipe_global['printLogoImage'] ) && ! empty( $recipe_global['printLogoImage'] ) ? $recipe_global['printLogoImage'] : false;

			if ( $printLogoImage ) :
		?>
				<div class="dr-logo">
					<?php echo wp_get_attachment_image( $printLogoImage, 'full' ); ?>
				</div>
		<?php 
			endif;
		?>
			<h1 id="dr-print-title" class="dr-print-title"><?php the_title();?></h1>
			<div class="dr-print-img">
				<?php the_post_thumbnail( 'recipe-feat-print' ); ?>
			</div>
		</div><!-- #dr-page1 -->

		<div id="dr-page2" class="dr-print-page dr-print-ingredients">
			<div class="dr-ingredient-meta-wrap">
				<?php if ( $recipe->rating_count ) : ?>
					<div class="dr-ingredient-meta">
						<svg class="icon">
							<use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#star-gray"></use>
						</svg>
						<b><?php esc_html_e( 'Ratings', 'delicious-recipes' ); ?></b>
						<span><?php 
							/* translators: %1$s: rating %2$s: total ratings count */
							echo esc_html( sprintf( __( '%1$s from %2$s votes', 'delicious-recipes' ), $recipe->rating, $recipe->rating_count ) ); 
						?></span>
					</div>
				<?php endif; ?>
				<?php if( ! empty( $recipe->cooking_method ) && $global_toggles['enable_cooking_method'] ) : ?>
					<div class="dr-ingredient-meta">
						<svg class="icon">
							<use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#cooking-method"></use>
						</svg>
						<b><?php echo esc_html( $global_toggles['cooking_method_lbl'] ); ?></b>
						<?php the_terms( $recipe->ID, 'recipe-cooking-method', '<span>', ', ', '</span>' ); ?>
					</div>
				<?php endif; ?>
				<?php if( ! empty( $recipe->recipe_cuisine ) && $global_toggles['enable_cuisine'] ) : ?>
					<div class="dr-ingredient-meta">
						<svg class="icon">
                            <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#cuisine"></use>
                        </svg>
						<b><?php echo esc_html( $global_toggles['cuisine_lbl'] ); ?></b>
						<?php the_terms( $recipe->ID, 'recipe-cuisine', '<span>', ', ', '</span>' ); ?>
					</div>
				<?php endif; ?>
				<?php if( ! empty( $recipe->recipe_course ) && $global_toggles['enable_category'] ) : ?>
					<div class="dr-ingredient-meta">
						<svg class="icon">
                            <use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#category"></use>
                        </svg>
						<b><?php echo esc_html( $global_toggles['category_lbl'] ); ?></b>
						<?php the_terms( $recipe->ID, 'recipe-course', '<span>', ', ', '</span>' ); ?>
					</div>
				<?php endif; ?>
				<?php if( ! empty( $recipe->difficulty_level ) && $global_toggles['enable_difficulty_level'] ) : ?>
					<div class="dr-ingredient-meta">
						<svg class="icon">
							<use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#difficulty"></use>
						</svg>
						<b><?php echo esc_html( $global_toggles['difficulty_level_lbl'] ); ?></b>
						<?php echo esc_html( $recipe->difficulty_level ); ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $recipe->prep_time ) || ! empty( $recipe->cook_time ) || ! empty( $recipe->rest_time ) ) : ?>
					<div class="dr-ingredient-meta dr-ingredient-time">
						<div class="meta-title-wrap">
							<svg class="icon">
								<use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#time"></use>
							</svg>
							<b><?php esc_html_e( 'Time', 'delicious-recipes' ) ?></b>
						</div>
						<div class="meta-wrap">
							<?php if ( ! empty( $recipe->prep_time ) && $global_toggles['enable_prep_time'] ) : ?>
								<span><?php echo esc_html( $global_toggles['prep_time_lbl'] ); ?>: <?php echo esc_html( $recipe->prep_time ) ?> <?php echo esc_html( $recipe->prep_time_unit ) ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $recipe->cook_time )  && $global_toggles['enable_cook_time'] ) : ?>
								<span><?php echo esc_html( $global_toggles['cook_time_lbl'] ); ?>: <?php echo esc_html( $recipe->cook_time ) ?> <?php echo esc_html( $recipe->cook_time_unit ) ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $recipe->rest_time ) && $global_toggles['enable_rest_time'] ) : ?>
								<span><?php echo esc_html( $global_toggles['rest_time_lbl'] ); ?>: <?php echo esc_html( $recipe->rest_time ) ?> <?php echo esc_html( $recipe->rest_time_unit ) ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $recipe->total_time ) && $global_toggles['enable_total_time'] ) : ?>
								<span class="total-time"><?php echo esc_html( $global_toggles['total_time_lbl'] ); ?>: <?php echo esc_html( $recipe->total_time ) ?></span>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $recipe->no_of_servings ) && $global_toggles['enable_servings'] ) : ?>
					<div class="dr-ingredient-meta">
						<svg class="icon">
							<use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#yield"></use>
						</svg>
						<b><?php echo esc_html( $global_toggles['servings_lbl'] ); ?></b>
						<span id="dr-servings"><?php echo esc_html( $recipe->no_of_servings ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $recipe->recipe_calories ) && $global_toggles['enable_calories'] ) : ?>
					<div class="dr-ingredient-meta">
						<svg class="icon">
							<use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#calories"></use>
						</svg>
						<b><?php echo esc_html( $global_toggles['calories_lbl'] ); ?></b>
						<span><?php echo esc_html( $recipe->recipe_calories ); ?></span>
					</div>
				<?php endif; ?>
				<?php if( ! empty( $recipe->best_season ) && $global_toggles['enable_seasons'] ) : ?>
					<div class="dr-ingredient-meta">
						<svg class="icon">
							<use xlink:href="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>assets/images/sprite.svg#season"></use>
						</svg>
						<b><?php echo esc_html( $global_toggles['seasons_lbl'] ); ?></b>
						<span><?php echo esc_html( $recipe->best_season ); ?></span>
					</div>
				<?php endif; ?>
			</div>
			<div class="dr-print-block-wrap">
				<?php if( $recipe->recipe_description ) : ?>
					<div class="dr-print-block dr-description-wrap">
						<div class="dr-pring-block-header">
							<div class="dr-print-block-title">
								<span><?php esc_html_e( 'Description', 'delicious-recipes' ); ?></span>
							</div>
						</div>
						<div class="dr-pring-block-content">
							<?php echo wp_kses_post( $recipe->recipe_description ); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if( isset( $recipe->ingredients ) && ! empty( $recipe->ingredients ) ) : ?>
					<div class="dr-print-block dr-ingredients-wrap">
						<div class="dr-pring-block-header">
							<div class="dr-print-block-title">
								<span><?php echo esc_html( $recipe->ingredient_title ); ?></span>
							</div>
						</div>
						<div class="dr-pring-block-content">
							<?php 
							echo '<ul>';
							$ingredient_string_format = isset( $global_settings['ingredientStringFormat'] ) ? $global_settings['ingredientStringFormat'] : '{qty} {unit} {ingredient} {notes}';
								
								foreach( $recipe->ingredients as $key => $ingre_section ) {
									$section_title = isset( $ingre_section['sectionTitle'] ) ? $ingre_section['sectionTitle'] : '';
									$ingre         = isset( $ingre_section['ingredients'] ) ? $ingre_section['ingredients'] : array();
									
									if( $section_title ) {
										echo '<div class="dr-subtitle">'. esc_html( $section_title ) .'</div>';
									}
									foreach( $ingre as $ingre_key => $ingredient ) { 			
										$ingredient_qty  = isset( $ingredient['quantity'] ) ? $ingredient['quantity'] : 0;
										$ingredient_unit = isset( $ingredient['unit'] ) ? $ingredient['unit'] : '';
										$unit_text       = ! empty( $ingredient_unit ) ? delicious_recipes_get_unit_text( $ingredient_unit, $ingredient_qty ) : '';
				
										$ingredient_keys = array(
											'{qty}'        => isset( $ingredient['quantity'] ) ? '<span class="ingredient_quantity" data-original="'. $ingredient['quantity'] .'" data-recipe="'. $recipe->ID .'">' . $ingredient['quantity'] . '</span>' : '',
											'{unit}'       => $unit_text,
											'{ingredient}' => isset( $ingredient['ingredient'] ) ? $ingredient['ingredient'] : '',
											'{notes}'      => isset( $ingredient['notes'] ) && ! empty( $ingredient['notes'] ) ? '<span class="ingredient-notes" >(' . $ingredient['notes'] . ')</span>' : '',
										);
										$ingre_string = str_replace( array_keys( $ingredient_keys ), $ingredient_keys, $ingredient_string_format );
									
										echo '<li>';
											echo wp_kses_post( $ingre_string );
										echo '</li>';
									}
								}
								echo '</ul>';
							?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if( isset( $recipe->instructions ) && ! empty( $recipe->instructions ) ) : ?>
			<div id="dr-page3" class="dr-print-page dr-print-instructions">
				<div class="dr-print-block">
					<div class="dr-pring-block-header">
						<div class="dr-print-block-title">
							<span><?php echo esc_html( $recipe->instruction_title ); ?></span>
						</div>
					</div>
					<?php 
						echo '<div class="dr-pring-block-content">';
							echo '<ol>';
								foreach( $recipe->instructions as $key => $intruct_section ) {

									if( isset( $intruct_section['sectionTitle'] ) && $intruct_section['sectionTitle'] ) {
										echo '<div class="dr-subtitle">'. esc_html( $intruct_section['sectionTitle'] ) .'</div>';
									}
									if( isset( $intruct_section['instruction'] ) && ! empty( $intruct_section['instruction'] ) ) {
										foreach( $intruct_section['instruction'] as $inst_key => $instruct ) { 
											$instruction_title = isset( $instruct['instructionTitle'] ) ? $instruct['instructionTitle'] : '';
											$instruction       = isset( $instruct['instruction'] ) ? $instruct['instruction'] : '';
											$instruction_notes = isset( $instruct['instructionNotes'] ) ? $instruct['instructionNotes'] : '';
											$instruction_image = isset( $instruct['image'] ) && ! empty( $instruct['image'] ) ? $instruct['image'] : false;
											$instruction_video = isset( $instruct['videoURL'] ) && ! empty( $instruct['videoURL'] ) ? $instruct['videoURL'] : false;
										
											echo '<li>';
												echo esc_html( $instruction_title );
												
												if ( $instruction_image ) {
													$instruct_image = wp_get_attachment_image( $instruction_image, 'full' );
														echo wp_kses_post( $instruct_image );
												}
												
												echo wp_kses_post( $instruction );
												if ( ! empty( $instruction_notes ) ) {
													echo '<div class="dr-list-tips">';
														echo esc_html( $instruction_notes );
													echo '</div>';
												}
											echo '</li>';
										}
									}
								}
							echo '</ol>';
						echo '</div>';
					?>
				</div>
			</div>
		<?php endif; ?>

		<div id="dr-page5" class="dr-print-page dr-print-nutrition">
			<div class="dr-print-block dr-wrp-only-nut">
				<?php delicious_recipes_get_template( 'recipe/recipe-block/nutrition.php' ); ?>
			</div>
			<div class="dr-print-block dr-wrap-notes-keywords">
				<?php if ( ! empty( $recipe->notes ) && $global_toggles['enable_notes'] ) :
					?>
						<div class="dr-note">
							<div class="dr-print-block-title">
								<span><?php echo esc_html( $global_toggles['notes_lbl'] ); ?></span>
							</div>
							<?php echo wp_kses_post( $recipe->notes ); ?>
						</div>
					<?php
				endif;

				if ( ! empty( $recipe->keywords ) && $global_toggles['enable_keywords'] ) :
					?>
						<div class="dr-keywords">
							<span class="dr-meta-title"><?php echo esc_html( $global_toggles['keywords_lbl'] ); ?>:</span>
							<?php echo wp_kses_post( $recipe->keywords ); ?>
						</div>
					<?php
				endif; ?>
			</div>
			<?php if ( $displaySocialSharingInfo && $socials_enabled ) : 
				$recipeShareTitle = isset( $recipe_global['recipeShareTitle'] ) ? $recipe_global['recipeShareTitle'] : '';	
			?>
				<div class="dr-print-cta dr-wrap-social-share">
					<div class="dr-cta-title"><?php echo esc_html( $recipeShareTitle ); ?></div>
					<?php if ( isset( $recipe_global['socialShare'] ) && ! empty( $recipe_global['socialShare'] ) ) : ?>
						<?php foreach( $recipe_global['socialShare'] as $key => $share ) : 
							if ( ! isset( $share['enable']['0'] ) || 'yes' !== $share['enable']['0'] ) {
								continue;
							}    
								?>
								<?php if ( isset( $share['content'] ) && ! empty( $share['content'] ) ) : ?>
                                    <div class="dr-share-content">
                                        <?php echo wp_kses_post( $share['content'] ); ?>
                                    </div>
                                <?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( $embedRecipeLink )  :
				$recipe_link_label = isset( $recipe_global['recipeLinkLabel'] ) ? $recipe_global['recipeLinkLabel'] : '';	
			?>
				<div class="dr-print-block-footer">
					<b><?php echo esc_html( $recipe_link_label ); ?></b>
					<span>
						<a href="<?php the_permalink(); ?>" target="_blank"><?php the_permalink(); ?></a>
					</span>
				</div>
			<?php endif; ?>
		</div>

		<div id="dr-page6" class="dr-print-page dr-print-author">
			<?php  
				$authorImage       = isset( $recipe_global['authorImage'] ) ? $recipe_global['authorImage'] : false;
				$authorName        = isset( $recipe_global['authorName'] ) ? $recipe_global['authorName'] : '';
				$authorSubtitle    = isset( $recipe_global['authorSubtitle'] ) ? $recipe_global['authorSubtitle'] : '';
				$authorDescription = isset( $recipe_global['authorDescription'] ) ? $recipe_global['authorDescription'] : '';

				// Social Profiles.
				$author_social_links = apply_filters( 'delicious_recipes_author_social_links', ['facebook','instagram','pinterest','twitter','youtube','snapchat','linkedin'] );

			?>

			<?php if ( $embedAuthorInfo && $authorName ) :
				?>
				<div class="dr-print-block">
					<div class="dr-wrap-author-profile">
						<div class="dr-pring-block-img-wrap">
							<?php if ( $authorImage ) : ?>
								<div class="dr-print-block-img">
									<?php echo wp_kses_post( wp_get_attachment_image( $authorImage ) ); ?>
								</div>
							<?php endif; ?>
							<div class="dr-print-block-header">
								<div class="dr-print-block-title">
									<span><?php echo esc_html( $authorName ); ?></span>
								</div>
								<span class="dr-print-block-subtitle"><?php echo esc_html( $authorSubtitle ); ?></span>
								<div class="dr-print-block-desc">
									<p><?php echo wp_kses_post( $authorDescription ); ?></p>
								</div>
							</div>
						</div>
						<ul class="dr-author-social">
							<?php foreach( $author_social_links as $social ) : 
								$social_link = isset( $recipe_global[$social.'Link'] ) ? trim( $recipe_global[$social.'Link'], '/\\' ) : false;

								if ( $social_link ) :
							?>
									<li>
										<a href="<?php echo esc_url( $social_link ); ?>" target="_blank" rel="nofollow noopener">
											<img src="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>/assets/images/print-img/<?php echo esc_html( $social ) ?>.png" alt="">
											<span class="social-name"><?php echo esc_url( $social_link ); ?>/</span>
										</a>
									</li>
							<?php 
								endif;
							endforeach; ?>
						</ul>
					</div>					
				</div>
			<?php endif;

			$thankyouMessage = isset( $recipe_global['thankyouMessage'] ) ? $recipe_global['thankyouMessage'] : false;

			if ( $thankyouMessage ) :
			?>
				<div class="dr-pring-block-content dr-wrap-thankyou">
					<?php echo wp_kses_post( $thankyouMessage ); ?>
				</div>
			<?php endif; 
			
			/**
			 * action hook for additionals.
			 */
			do_action( 'delicious_recipes_print_additionals' );
			?>
		</div>
	</div><!-- .dr-print-outer-wrap -->
	<script type="text/javascript">
		var print_options = document.getElementsByTagName('input');
		for (var i = 0, len = print_options.length; i < len; i++) {
			if ( print_options[i].getAttribute("name") == "print_options"){
				update_print_options( print_options[i] );
			}
		}

		document.addEventListener("click", function (e) {
			update_print_options( e.target );
		});

		function update_print_options( printOpt ){

			if (printOpt.id == "print_options_title" && typeof document.getElementById('dr-print-title') != 'undefined') {
				if ( printOpt.checked ){
					document.getElementById('dr-print-title').style.display = 'block';
				} else {
					document.getElementById('dr-print-title').style.display = 'none';
				}
			}

			if (printOpt.id == "print_options_nutrition" && typeof document.getElementsByClassName('dr-wrp-only-nut')[0] != 'undefined') {
				if ( printOpt.checked ){
					document.getElementsByClassName('dr-wrp-only-nut')[0].style.display = 'block';
				} else {
					document.getElementsByClassName('dr-wrp-only-nut')[0].style.display = 'none';
				}
			}

			if (printOpt.id == "print_options_info" && typeof document.getElementsByClassName('dr-ingredient-meta-wrap')[0] != 'undefined') {
				if ( printOpt.checked ){
					document.getElementsByClassName('dr-ingredient-meta-wrap')[0].style.display = 'flex';
				} else {
					document.getElementsByClassName('dr-ingredient-meta-wrap')[0].style.display = 'none';
				}
			}

			if (printOpt.id == "print_options_description" && typeof document.getElementsByClassName('dr-description-wrap')[0] != 'undefined') {
				if ( printOpt.checked ){
					document.getElementsByClassName('dr-description-wrap')[0].style.display = 'block';
				} else {
					document.getElementsByClassName('dr-description-wrap')[0].style.display = 'none';
				}
			}

			if (printOpt.id == "print_options_images" && typeof document.getElementsByClassName('dr-print-img')[0] != 'undefined') {
				if ( printOpt.checked ){
					document.getElementsByClassName('dr-print-img')[0].style.display = 'block';
					var print_images = document.getElementsByTagName('img');
					for (var i = 0, len = print_images.length; i < len; i++) {
						print_images[i].style.display = 'inline-block';
					}
				} else {
					document.getElementsByClassName('dr-print-img')[0].style.display = 'none';
					var print_images = document.getElementsByTagName('img');
					for (var i = 0, len = print_images.length; i < len; i++) {
						print_images[i].style.display = 'none';
					}
				}
			}

			if (printOpt.id == "print_options_ingredients" && typeof document.getElementsByClassName('dr-ingredients-wrap')[0] != 'undefined') {
				if ( printOpt.checked ){
					document.getElementsByClassName('dr-ingredients-wrap')[0].style.display = 'block';
				} else {
					document.getElementsByClassName('dr-ingredients-wrap')[0].style.display = 'none';
				}
			}

			if (printOpt.id == "print_options_instructions" && typeof document.getElementsByClassName('dr-print-instructions')[0] != 'undefined' ) {
				if ( printOpt.checked ){
					document.getElementsByClassName('dr-print-instructions')[0].style.display = 'block';
				} else {
					document.getElementsByClassName('dr-print-instructions')[0].style.display = 'none';
				}
			}

			if (printOpt.id == "print_options_notes" && typeof document.getElementsByClassName('dr-wrap-notes-keywords')[0] != 'undefined') {
				if ( printOpt.checked ){
					document.getElementsByClassName('dr-wrap-notes-keywords')[0].style.display = 'block';
				} else {
					document.getElementsByClassName('dr-wrap-notes-keywords')[0].style.display = 'none';
				}
			}

			if (printOpt.id == "print_options_social-share" && typeof document.getElementsByClassName('dr-wrap-social-share')[0] != 'undefined') {
				if ( printOpt.checked ){
					document.getElementsByClassName('dr-wrap-social-share')[0].style.display = 'block';
				} else {
					document.getElementsByClassName('dr-wrap-social-share')[0].style.display = 'none';
				}
			}

			if (printOpt.id == "print_options_author-bio" && typeof document.getElementsByClassName('dr-wrap-author-profile')[0] != 'undefined') {
				if ( printOpt.checked ){
					document.getElementsByClassName('dr-wrap-author-profile')[0].style.display = 'block';
				} else {
					document.getElementsByClassName('dr-wrap-author-profile')[0].style.display = 'none';
				}
			}

			if (printOpt.id == "print_options_thank-you-note" && typeof document.getElementsByClassName('dr-wrap-thankyou')[0] != 'undefined') {
				if ( printOpt.checked ){
					document.getElementsByClassName('dr-wrap-thankyou')[0].style.display = 'block';
				} else {
					document.getElementsByClassName('dr-wrap-thankyou')[0].style.display = 'none';
				}
			}
		}
		const print_props = {
			original_servings: "<?php echo ! empty( $recipe->no_of_servings )  ? esc_attr( $recipe->no_of_servings ) : 1; ?>",
			recipe: "<?php echo esc_attr( $recipe->ID ); ?>"
		}
	</script>
	<?php 
		wp_register_script( 'math-min', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/public/js/math.min.js' , ['jquery'], true );
		wp_enqueue_script( 'recipe-print', plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) . '/assets/public/js/recipe-print.js' , ['jquery', 'math-min'], true );
		
		wp_footer();
	?>
</body>
</html>