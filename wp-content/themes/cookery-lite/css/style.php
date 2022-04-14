<?php
/**
 * Cookery Lite Dynamic Styles
 * 
 * @package Cookery_Lite
*/

function cookery_lite_dynamic_css(){
    
    $primary_font    = get_theme_mod( 'primary_font', 'Questrial' );
    $primary_fonts   = cookery_lite_get_fonts( $primary_font, 'regular' );
    $secondary_font  = get_theme_mod( 'secondary_font', 'Noto Serif' );
    $secondary_fonts = cookery_lite_get_fonts( $secondary_font, 'regular' );

    $font_size       = get_theme_mod( 'font_size', 18 );
    
    $site_title_font      = get_theme_mod( 'site_title_font', array( 'font-family'=>'Noto Serif', 'variant'=>'regular' ) );
    $site_title_fonts     = cookery_lite_get_fonts( $site_title_font['font-family'], $site_title_font['variant'] );
    $site_title_font_size = get_theme_mod( 'site_title_font_size', 30 );
    
    $primary_color    = get_theme_mod( 'primary_color', '#2db68d' ); 
	$secondary_color  = get_theme_mod( 'secondary_color', '#e84e3b' ); 
	$logo_width       = get_theme_mod( 'logo_width', 150 );
    $static_cta_one_color  = get_theme_mod( 'static_cta_one_color', '#ffffff' );
    $enable_typography = '';

    $featured_recipe_bg = get_theme_mod( 'featured_recipe_bg' );
    $client_section_bg  = get_theme_mod( 'client_section_bg' );
    $footer_newsletter_bg = get_theme_mod( 'newsletter_section_bg' );

    if( cookery_lite_is_delicious_recipe_activated() ){
        $global_settings = delicious_recipes_get_global_settings();
        $enable_typography = ( isset( $global_settings['enablePluginTypography']['0'] ) && 'yes' === $global_settings['enablePluginTypography']['0'] ) ? true : false;
    }
    
    $rgb = cookery_lite_hex2rgb( cookery_lite_sanitize_hex_color( $primary_color ) );
    $rgb2 = cookery_lite_hex2rgb( cookery_lite_sanitize_hex_color( $secondary_color ) );
     
    echo "<style type='text/css' media='all'>"; ?>
     
    .content-newsletter .blossomthemes-email-newsletter-wrapper.bg-img:after,
    .widget_blossomthemes_email_newsletter_widget .blossomthemes-email-newsletter-wrapper:after{
        <?php echo 'background: rgba(' . $rgb[0] . ', ' . $rgb[1] . ', ' . $rgb[2] . ', 0.8);'; ?>
    }

    section.featured-recipe-section::after {
        background-image: url('<?php echo esc_url( $featured_recipe_bg ); ?>');
    }

    .client-section::after {
        background-image: url('<?php echo esc_url( $client_section_bg ); ?>');
    }

    section.footer-newsletter-section::after {
        background-image: url('<?php echo esc_url( $footer_newsletter_bg ); ?>');
    }
    
   /*Typography*/

    :root {
		--primary-color: <?php echo cookery_lite_sanitize_hex_color( $primary_color ); ?>;
		--primary-color-rgb: <?php printf('%1$s, %2$s, %3$s', $rgb[0], $rgb[1], $rgb[2] ); ?>;
        --secondary-color: <?php echo cookery_lite_sanitize_hex_color( $secondary_color ); ?>;
        --secondary-color-rgb: <?php printf('%1$s, %2$s, %3$s', $rgb2[0], $rgb2[1], $rgb2[2] ); ?>;
		--primary-font: <?php echo esc_html( $primary_fonts['font'] ); ?>;
        --secondary-font: <?php echo esc_html( $secondary_fonts['font'] ); ?>;
        <?php if( ! $enable_typography ) { ?> --dr-primary-font: <?php echo esc_html( $primary_fonts['font'] ); ?>; <?php } ?>
        <?php if( ! $enable_typography ) { ?> --dr-secondary-font: <?php echo esc_html( $secondary_fonts['font'] ); ?>; <?php } ?>
	}

    body {
        font-size   : <?php echo absint( $font_size ); ?>px;        
    }
    
    .site-title{
        font-size   : <?php echo absint( $site_title_font_size ); ?>px;
        font-family : <?php echo esc_html( $site_title_fonts['font'] ); ?>;
        font-weight : <?php echo esc_html( $site_title_fonts['weight'] ); ?>;
        font-style  : <?php echo esc_html( $site_title_fonts['style'] ); ?>;
    }

	.custom-logo-link img{
        width    : <?php echo absint( $logo_width ); ?>px;
        max-width: 100%;
    }

    .site-banner.static-cta.style-one .banner-caption .item-title,
    .site-banner.static-cta.style-one .banner-caption .item-desc{
        color: <?php echo cookery_lite_sanitize_hex_color( $static_cta_one_color ); ?>;
    }

	blockquote::before {
		background-image: url('data:image/svg+xml; utf-8, <svg xmlns="http://www.w3.org/2000/svg" width="64" height="40.062" viewBox="0 0 64 40.062"><path d="M68.871,47.073A12.886,12.886,0,0,0,56.71,36.191c1.494-5.547,5.121-7.752,9.53-9.032a.515.515,0,0,0,.356-.569l-.711-4.409s-.071-.356-.64-.284C50.024,23.6,39.712,35.2,41.632,49.277,43.41,59.021,51.02,62.79,58.061,61.794a12.968,12.968,0,0,0,10.81-14.722ZM20.3,36.191c1.422-5.547,5.192-7.752,9.53-9.032a.515.515,0,0,0,.356-.569l-.64-4.409s-.071-.356-.64-.284C13.682,23.532,3.441,35.124,5.219,49.206c1.849,9.815,9.53,13.584,16.5,12.588A12.865,12.865,0,0,0,32.458,47.073,12.693,12.693,0,0,0,20.3,36.191Z" transform="translate(-5.018 -21.887)" fill="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>"/></svg>');
	}
	
	.comments-area .comment-list li .comment-body .reply .comment-reply-link::after {
		background-image: url('data:image/svg+xml;utf-8, <svg xmlns="http://www.w3.org/2000/svg" width="14.796" height="10.354" viewBox="0 0 14.796 10.354"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l4.117,4.116-4.117,4.116" transform="translate(-7811.241 1126.021)" fill="none" stroke="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>" stroke-linecap="round" stroke-width="1.5"></path><path d="M6555.283-354.415h-12.624" transform="translate(-6542.659 358.532)" fill="none" stroke="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>" stroke-linecap="round" stroke-width="1.5"></path></g></svg>');
	}

	.static-search .item .search-form-wrap .search-submit {
		background-image: url('data:image/svg+xml; utf-8, <svg xmlns="http://www.w3.org/2000/svg" width="29.434" height="29.434" viewBox="0 0 29.434 29.434"><g transform="translate(-663.027 -502.431)"><g transform="translate(692.461 517.148) rotate(135)" fill="none" stroke="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>" stroke-width="2.5"><circle cx="10.406" cy="10.406" r="10.406" stroke="none"/><circle cx="10.406" cy="10.406" r="9.156" fill="none"/></g><path d="M0,6.907V0" transform="translate(689.718 529.122) rotate(135)" fill="none" stroke="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>" stroke-linecap="round" stroke-width="2.5"/></g></svg>');
	}

	.newsletter .blossomthemes-email-newsletter-wrapper form [type="submit"]:hover::after, 
	.widget_blossomthemes_email_newsletter_widget form [type="submit"]:hover::after {
		background-image: url('data:image/svg+xml; utf-8, <svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none" stroke="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>" stroke-linecap="round" stroke-width="1.5"/><path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>" stroke-linecap="round" stroke-width="1.5"/></g></svg>');
	}

	.search .page-header .search-form .search-submit, 
	.error404 .error-404-search .search-form .search-submit {
		background-image: url('data:image/svg+xml; utf-8, <svg xmlns="http://www.w3.org/2000/svg" width="21.863" height="22" viewBox="0 0 21.863 22"><path d="M24.863,1170.255l-2.045,2.045L18,1167.482v-1.091l-.409-.409a8.674,8.674,0,0,1-5.727,2.046,8.235,8.235,0,0,1-6.273-2.591A8.993,8.993,0,0,1,3,1159.164a8.235,8.235,0,0,1,2.591-6.273,8.993,8.993,0,0,1,6.273-2.591,8.441,8.441,0,0,1,6.273,2.591,8.993,8.993,0,0,1,2.591,6.273,8.675,8.675,0,0,1-2.045,5.727l.409.409h.955ZM7.5,1163.664a5.76,5.76,0,0,0,4.364,1.773,5.969,5.969,0,0,0,4.364-1.773,6.257,6.257,0,0,0,0-8.727,5.76,5.76,0,0,0-4.364-1.773,5.969,5.969,0,0,0-4.364,1.773,5.76,5.76,0,0,0-1.773,4.364A6.308,6.308,0,0,0,7.5,1163.664Z" transform="translate(-3 -1150.3)" fill="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>"/></svg>');
    }
    
    .posts-navigation .nav-links a:hover::before {
        background-image: url('data:image/svg+xml; utf-8, <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>" d="M20.2 247.5L167 99.5c4.7-4.7 12.3-4.7 17 0l19.8 19.8c4.7 4.7 4.7 12.3 0 17L85.3 256l118.5 119.7c4.7 4.7 4.7 12.3 0 17L184 412.5c-4.7 4.7-12.3 4.7-17 0l-146.8-148c-4.7-4.7-4.7-12.3 0-17zm160 17l146.8 148c4.7 4.7 12.3 4.7 17 0l19.8-19.8c4.7-4.7 4.7-12.3 0-17L245.3 256l118.5-119.7c4.7-4.7 4.7-12.3 0-17L344 99.5c-4.7-4.7-12.3-4.7-17 0l-146.8 148c-4.7 4.7-4.7 12.3 0 17z"></path></svg>');
    }

    .posts-navigation .nav-links .nav-next a:hover::before {
        background-image: url('data:image/svg+xml; utf-8, <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>" d="M363.8 264.5L217 412.5c-4.7 4.7-12.3 4.7-17 0l-19.8-19.8c-4.7-4.7-4.7-12.3 0-17L298.7 256 180.2 136.3c-4.7-4.7-4.7-12.3 0-17L200 99.5c4.7-4.7 12.3-4.7 17 0l146.8 148c4.7 4.7 4.7 12.3 0 17zm-160-17L57 99.5c-4.7-4.7-12.3-4.7-17 0l-19.8 19.8c-4.7 4.7-4.7 12.3 0 17L138.7 256 20.2 375.7c-4.7 4.7-4.7 12.3 0 17L40 412.5c4.7 4.7 12.3 4.7 17 0l146.8-148c4.7-4.7 4.7-12.3 0-17z"></path></svg>');
    }

	.search-form .search-submit:hover {
		background-image: url('data:image/svg+xml; utf-8, <svg xmlns="http://www.w3.org/2000/svg" width="21.863" height="22" viewBox="0 0 21.863 22"><path d="M24.863,1170.255l-2.045,2.045L18,1167.482v-1.091l-.409-.409a8.674,8.674,0,0,1-5.727,2.046,8.235,8.235,0,0,1-6.273-2.591A8.993,8.993,0,0,1,3,1159.164a8.235,8.235,0,0,1,2.591-6.273,8.993,8.993,0,0,1,6.273-2.591,8.441,8.441,0,0,1,6.273,2.591,8.993,8.993,0,0,1,2.591,6.273,8.675,8.675,0,0,1-2.045,5.727l.409.409h.955ZM7.5,1163.664a5.76,5.76,0,0,0,4.364,1.773,5.969,5.969,0,0,0,4.364-1.773,6.257,6.257,0,0,0,0-8.727,5.76,5.76,0,0,0-4.364-1.773,5.969,5.969,0,0,0-4.364,1.773,5.76,5.76,0,0,0-1.773,4.364A6.308,6.308,0,0,0,7.5,1163.664Z" transform="translate(-3 -1150.3)" fill="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $secondary_color ) ); ?>"/></svg>');
    }

    .woocommerce .woocommerce-ordering select,
    .woocommerce-page .woocommerce-ordering select{
       background-image: url('data:image/svg+xml; utf-8, <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8"><path d="M7.5,0,15,8H0Z" transform="translate(15 8) rotate(180)" fill="<?php echo cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ); ?>"/></svg>');
    }
           
    <?php echo "</style>";
}
add_action( 'wp_head', 'cookery_lite_dynamic_css', 99 );

/**
 * Function for sanitizing Hex color 
 */
function cookery_lite_sanitize_hex_color( $color ){
	if ( '' === $color )
		return '';

    // 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
		return $color;
}

/**
 * convert hex to rgb
 * @link http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
*/
function cookery_lite_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}

/**
 * Convert '#' to '%23'
*/
function cookery_lite_hash_to_percent23( $color_code ){
    $color_code = str_replace( "#", "%23", $color_code );
    return $color_code;
}

if ( ! function_exists( 'cookery_lite_gutenberg_inline_style' ) ) : 
/**
 * Gutenberg Dynamic Style
 */
function cookery_lite_gutenberg_inline_style(){
 
    /* Get Link Color */
    $primary_font    = get_theme_mod( 'primary_font', 'Questrial' );
    $primary_fonts   = cookery_lite_get_fonts( $primary_font, 'regular' );
    $secondary_font  = get_theme_mod( 'secondary_font', 'Noto Serif' );
    $secondary_fonts = cookery_lite_get_fonts( $secondary_font, 'regular' );

    $primary_color    = get_theme_mod( 'primary_color', '#2db68d' ); 
    $secondary_color  = get_theme_mod( 'secondary_color', '#e84e3b' ); 

    $rgb = cookery_lite_hex2rgb( cookery_lite_sanitize_hex_color( $primary_color ) );
    $rgb2 = cookery_lite_hex2rgb( cookery_lite_sanitize_hex_color( $secondary_color ) );
 
    $custom_css = ':root .block-editor-page {
        --primary-color: ' . cookery_lite_sanitize_hex_color( $primary_color ) . ';
        --primary-color-rgb: ' . sprintf( '%1$s, %2$s, %3$s', $rgb[0], $rgb[1], $rgb[2] ) . ';
        --secondary-color: ' . cookery_lite_sanitize_hex_color( $secondary_color ) . ';
        --secondary-color-rgb: ' . sprintf('%1$s, %2$s, %3$s', $rgb2[0], $rgb2[1], $rgb2[2] ) . ';
        --primary-font: ' . esc_html( $primary_fonts['font'] ) . ';
        --secondary-font: ' . esc_html( $secondary_fonts['font'] ) . ';
    }

    blockquote.wp-block-quote::before {
        background-image: url(\'data:image/svg+xml; utf-8, <svg xmlns="http://www.w3.org/2000/svg" width="64" height="40.062" viewBox="0 0 64 40.062"><path d="M68.871,47.073A12.886,12.886,0,0,0,56.71,36.191c1.494-5.547,5.121-7.752,9.53-9.032a.515.515,0,0,0,.356-.569l-.711-4.409s-.071-.356-.64-.284C50.024,23.6,39.712,35.2,41.632,49.277,43.41,59.021,51.02,62.79,58.061,61.794a12.968,12.968,0,0,0,10.81-14.722ZM20.3,36.191c1.422-5.547,5.192-7.752,9.53-9.032a.515.515,0,0,0,.356-.569l-.64-4.409s-.071-.356-.64-.284C13.682,23.532,3.441,35.124,5.219,49.206c1.849,9.815,9.53,13.584,16.5,12.588A12.865,12.865,0,0,0,32.458,47.073,12.693,12.693,0,0,0,20.3,36.191Z" transform="translate(-5.018 -21.887)" fill="' . cookery_lite_hash_to_percent23( cookery_lite_sanitize_hex_color( $primary_color ) ) . '"/></svg>\');
    }';

    return $custom_css;
}
endif;