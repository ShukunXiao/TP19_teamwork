<?php
/**
 * Recipe Share block template
 * 
 * @package Delicious_Recipes
 */
global $recipe;
$global_settings = delicious_recipes_get_global_settings();
$enable_social_section = isset( $global_settings['enableSocialShare']['0'] ) && 'yes' === $global_settings['enableSocialShare']['0'] ? true : false;

$socials_enabled = ( isset( $global_settings['socialShare']['0']['enable']['0'] ) 
&& 'yes' === $global_settings['socialShare']['0']['enable']['0'] ) 
|| ( isset( $global_settings['socialShare']['1']['enable']['0'] ) 
&& 'yes' === $global_settings['socialShare']['1']['enable']['0'] ) ? true : false;

$section_title = isset( $global_settings['recipeShareTitle'] ) && ! empty( $global_settings['recipeShareTitle'] ) ? $global_settings['recipeShareTitle'] : '';
if ( $enable_social_section && $socials_enabled ) :
?>
    <div class="dr-recipe-share">
        <?php if ( ! empty( $section_title ) ) : ?>
            <h3 class="dr-title"><?php echo esc_html( $section_title ); ?></h3>
        <?php endif; 
            if ( isset( $global_settings['socialShare'] ) && ! empty( $global_settings['socialShare'] ) ) :
        ?>
                <div class="dr-share-wrap">
                    <?php foreach( $global_settings['socialShare'] as $key => $share ) : 
                        if ( ! isset( $share['enable']['0'] ) || 'yes' !== $share['enable']['0'] ) {
                            continue;
                        }    
                    ?>
                        <div class="dr-share-block dr-<?php echo esc_attr( sanitize_title( $share['social'] ) ); ?>">
                            <div class="dr-share-bl-inn">
                                <span class="dr-share-icon">
                                    <?php if( 'Instagram' === $share['social'] ) : ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24.739" height="24.738" viewBox="0 0 24.739 24.738"><g transform="translate(0)"><path d="M17.369,0h-10A7.378,7.378,0,0,0,0,7.369v10a7.378,7.378,0,0,0,7.37,7.369h10a7.378,7.378,0,0,0,7.37-7.369v-10A7.378,7.378,0,0,0,17.369,0ZM22.25,17.368a4.881,4.881,0,0,1-4.881,4.881h-10a4.881,4.881,0,0,1-4.881-4.881v-10A4.881,4.881,0,0,1,7.37,2.489h10A4.881,4.881,0,0,1,22.25,7.369v10Z" transform="translate(0 0)" fill="#3f729b"/><path d="M139.4,133a6.4,6.4,0,1,0,6.4,6.4A6.405,6.405,0,0,0,139.4,133Zm0,10.307a3.909,3.909,0,1,1,3.91-3.909A3.909,3.909,0,0,1,139.4,143.307Z" transform="translate(-127.029 -127.029)" fill="#3f729b"/><ellipse cx="1.533" cy="1.533" rx="1.533" ry="1.533" transform="translate(17.247 4.488)" fill="#3f729b"/></g></svg>
                                    <?php elseif( 'Pinterest' === $share['social'] ) : ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="19.404" height="25" viewBox="0 0 19.404 25"><path d="M33.963,17.59c-.8,2.574-1.688,5.227-3.456,7.285a.329.329,0,0,1-.443.069.5.5,0,0,1-.5-.482,32.22,32.22,0,0,1,1.26-11.036,19.914,19.914,0,0,1,.748-2,3.721,3.721,0,0,1,.94-4.568c1.216-.92,2.83-.783,3.323.783.586,1.861-.044,4.322-.743,6.493.305.172.556.409.9.581a3.1,3.1,0,0,0,2.747.064,2.965,2.965,0,0,0,.763-.482,6.655,6.655,0,0,0,1.132-1.89c.118-.354.222-.724.325-1.093a5.953,5.953,0,0,0-4.12-7.093c-2.437-.738-5.326-.325-6.773,1.979a7.075,7.075,0,0,0-.694,5.045,3.322,3.322,0,0,1,.453,2.053,1.008,1.008,0,0,1-.172.34,1.389,1.389,0,0,1-1.905.128,2.162,2.162,0,0,1-.561-.679c-2.392-2.338-.492-7.61,1.309-9.722A10.079,10.079,0,0,1,40.835,1.218C44.891,3.409,46.54,9.1,44.729,13.3,43.08,17.122,37.783,19.579,33.963,17.59Z" transform="translate(-26.056)" fill="#c8232c"/></svg>
                                    <?php endif; ?>
                                </span>
                                <?php if ( isset( $share['content'] ) && ! empty( $share['content'] ) ) : ?>
                                    <div class="dr-share-content">
                                        <?php echo wp_kses_post( $share['content'] ); ?>
                                        <?php if( 'Pinterest' === $share['social'] ) : ?>
                                            <span class="post-pinit-button">
                                                <a data-pin-do="buttonPin" href="https://www.pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>/&media=<?php echo esc_url( $recipe->thumbnail ); ?>&description=So%20delicious!" data-pin-custom="true">
                                                <img src="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>/assets/images/pinit.png" alt="pinit">
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                    </div>                                    
                                <?php endif; ?>
                            </div>
                        </div> 
                    <?php endforeach; ?>
                </div>
        <?php 
            endif;
        ?>
    </div>
<?php
endif;
