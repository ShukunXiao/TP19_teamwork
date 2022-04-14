<?php
/**
 * Recipe author block.
 */

$global_settings = delicious_recipes_get_global_settings();

$author_profile     = isset( $global_settings['enableAuthorProfile']['0'] ) && 'yes' === $global_settings['enableAuthorProfile']['0'] ? true : false;

if( ! $author_profile ) {
    return;
}

$author_name        = isset( $global_settings['authorName'] ) && ! empty( $global_settings['authorName'] ) ? $global_settings['authorName'] : false;
$authorSubtitle     = isset( $global_settings['authorSubtitle'] ) && ! empty( $global_settings['authorSubtitle'] ) ? $global_settings['authorSubtitle'] : false;
$author_description = isset( $global_settings['authorDescription'] ) && ! empty( $global_settings['authorDescription'] ) ? $global_settings['authorDescription'] : false;
$author_image       = isset( $global_settings['authorImage'] ) && ! empty( $global_settings['authorImage'] ) ? $global_settings['authorImage'] : false;
$img_size           = apply_filters( 'author_img_size', 'recipe-author-image' );

// Social Links.
$facebookLink  = isset( $global_settings['facebookLink'] ) && ! empty( $global_settings['facebookLink'] ) ? trim( $global_settings['facebookLink'], '/\\' ) : false;
$instagramLink = isset( $global_settings['instagramLink'] ) && ! empty( $global_settings['instagramLink'] ) ? trim( $global_settings['instagramLink'], '/\\' ) : false;
$pinterestLink = isset( $global_settings['pinterestLink'] ) && ! empty( $global_settings['pinterestLink'] ) ? trim( $global_settings['pinterestLink'], '/\\' ) : false;
$twitterLink   = isset( $global_settings['twitterLink'] ) && ! empty( $global_settings['twitterLink'] ) ? trim( $global_settings['twitterLink'], '/\\' ) : false;
$youtubeLink   = isset( $global_settings['youtubeLink'] ) && ! empty( $global_settings['youtubeLink'] ) ? trim( $global_settings['youtubeLink'], '/\\' ) : false;
$snapchatLink  = isset( $global_settings['snapchatLink'] ) && ! empty( $global_settings['snapchatLink'] ) ? trim( $global_settings['snapchatLink'], '/\\' ) : false;
$linkedinLink  = isset( $global_settings['linkedinLink'] ) && ! empty( $global_settings['linkedinLink'] ) ? trim( $global_settings['linkedinLink'], '/\\' ) : false;

if ( empty( $author_name ) && empty( $author_image ) && empty( $author_description ) ) {
    return;
}
?>
<div class="author-block">
    <div class="author-img-wrap">
    <?php if ( $author_image ) : ?>
        <figure class="author-img">
            <?php echo wp_get_attachment_image( $author_image, $img_size ); ?>
        </figure>
    <?php endif; ?>
            <?php 
                if ( $author_name ) :
                    ?>
                        <h3 class="author-name"><?php echo esc_html( $author_name ); ?></h3>
                    <?php 
                endif;
            ?>
            <?php if ( $authorSubtitle ) : ?>
                <span class="author-subtitle">
                    <?php echo esc_html( $authorSubtitle ); ?>
                </span>
            <?php endif; ?>
        <div class="author-social">
            <ul class="social-networks">
                <?php if ( $youtubeLink ) : ?>
                    <li class="youtube">
                        <a target="_blank" href="<?php echo esc_url( $youtubeLink ); ?>" rel="nofollow noopener"><i class="fab fa-youtube"></i></a>
                    </li>
                <?php endif; ?>
                <?php if ( $facebookLink ) : ?>
                    <li class="facebook">
                        <a target="_blank" href="<?php echo esc_url( $facebookLink ); ?>" rel="nofollow noopener"><i class="fab fa-facebook-f"></i></a>
                    </li>
                <?php endif; ?>
                <?php if ( $instagramLink ) : ?>
                    <li class="instagram">
                        <a target="_blank" href="<?php echo esc_url( $instagramLink ); ?>" rel="nofollow noopener"><i class="fab fa-instagram"></i></a>
                    </li>
                <?php endif; ?>
                <?php if( $pinterestLink ) : ?>
                    <li class="pinterest">
                        <a target="_blank" href="<?php echo esc_url( $pinterestLink ); ?>" rel="nofollow noopener"><i class="fab fa-pinterest-p"></i></a>
                    </li>
                <?php endif; ?>
                <?php if( $twitterLink ) : ?>
                    <li class="twitter">
                        <a target="_blank" href="<?php echo esc_url( $twitterLink ); ?>" rel="nofollow noopener"><i class="fab fa-twitter"></i></a>
                    </li>
                <?php endif; ?>
                <?php if( $snapchatLink ) : ?>
                    <li class="snapchat">
                        <a target="_blank" href="<?php echo esc_url( $snapchatLink ); ?>" rel="nofollow noopener"><i class="fab fa-snapchat"></i></a>
                    </li>
                <?php endif; ?>
                <?php if( $linkedinLink ) : ?>
                    <li class="linkedin">
                        <a target="_blank" href="<?php echo esc_url( $linkedinLink ); ?>" rel="nofollow noopener"><i class="fab fa-linkedin"></i></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php if ( $author_description ) : ?>
        <div class="author-desc">
            <?php echo wp_kses_post( $author_description ); ?>
        </div>
    <?php endif; ?>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */