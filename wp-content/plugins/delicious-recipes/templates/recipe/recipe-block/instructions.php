<?php
/**
 * Instructions template.
 */
global $recipe;

$recipe_instructions = isset( $recipe->instructions ) ? $recipe->instructions : array();
$instructionTitle    = isset( $recipe->instruction_title ) ? $recipe->instruction_title : __( 'Instructions', 'delicious-recipes' );
$videoGalleryVids    = isset( $recipe->video_gallery ) ? $recipe->video_gallery : array();

// Get global toggles.
$global_toggles = delicious_recipes_get_global_toggles_and_labels();

if ( ! empty( $recipe_instructions ) ) : 
    ?>
        <div class="dr-instructions">
            <div class="dr-instrc-title-wrap">
                <h3 class="dr-title"><?php echo esc_html( $instructionTitle ); ?></h3>
                <?php if( ! empty( $videoGalleryVids ) && $global_toggles['enable_video'] ) : ?>
                    <div class="dr-instructions-toggle">
                        <span class="dr-inst-label"><?php echo esc_html( $global_toggles['video_lbl'] ); ?></span>
                        <div class="dr-toggle-inputs">
                            <input type="checkbox" name="" checked="checked" value="yes" id="dr-vdo-toggle" class="dr-video-toggle" data-target=".dr-instruction-video-<?php echo esc_attr($recipe->ID); ?>">
                            <div class="knobs"><span></span></div>
                            <span class="dr-toggle-on"><?php echo esc_html__( 'Off', 'delicious-recipes' ); ?></span>
                            <span class="dr-toggle-off"><?php echo esc_html__( 'On', 'delicious-recipes' ); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php foreach( $recipe_instructions as $sec_key => $intruct_section ) : 
                echo '<h4 class="dr-title">'. esc_html( $intruct_section['sectionTitle'] ) .'</h4>';
                if( isset( $intruct_section['instruction'] ) && ! empty( $intruct_section['instruction'] ) ) :
            ?>
                <ol class="dr-ordered-list">
                    <?php foreach( $intruct_section['instruction'] as $inst_key => $instruct ) : 
                        $rand_key          = rand(10000, 100000);
                        $instruction_title = isset( $instruct['instructionTitle'] ) ? $instruct['instructionTitle'] : '';
                        $instruction       = isset( $instruct['instruction'] ) ? apply_filters( 'wp_delicious_single_instruction', $instruct['instruction'] ) : '';
                        $instruction_notes = isset( $instruct['instructionNotes'] ) ? $instruct['instructionNotes'] : '';
                        $instruction_image = isset( $instruct['image'] ) && ! empty( $instruct['image'] ) ? $instruct['image'] : false;
                        $instruction_video = isset( $instruct['videoURL'] ) && ! empty( $instruct['videoURL'] ) ? $instruct['videoURL'] : false;
                    ?>
                        <li>
                            <?php if( $instruction_title ) : ?>
                                <h5>
                                    <?php echo esc_html( $instruction_title ); ?>
                                </h5>
                            <?php endif; ?>
                            <p><?php echo do_shortcode( $instruction ); ?></p>
                            <!-- Place for gallery image  -->
                            <?php 
                                if ( $instruction_image ) :
                                    $instruct_image = wp_get_attachment_image( $instruction_image, 'full' );
                                    echo wp_kses_post( $instruct_image );
                                endif; 
                            ?>
                            <?php if ( ! empty( $instruction_notes ) ) : ?>
                                <div class="dr-list-tips">
                                    <?php echo esc_html( $instruction_notes ); ?>
                                </div>
                            <?php endif; ?>
                            <?php 
                                if ( $instruction_video && $global_toggles['enable_video'] ) :
                                    $instruction_video_data = delicious_recipes_parse_videos( $instruction_video );
                                    $instruction_video_attr = isset( $instruction_video_data['0'] ) && ! empty( $instruction_video_data['0'] ) ? $instruction_video_data['0'] : array();
                                    if( ! empty( $instruction_video_attr ) ) :
                                        ?>
                                            <div class="dr-instructions-video dr-instruction-video-<?php echo esc_attr($recipe->ID); ?>">
                                                <div class="dr-vdo-thumbnail">
                                                    <img src="<?php echo esc_url( $instruction_video_attr['fullsize'] ) ?>" alt="<?php echo esc_attr( 'Instruction video' ); ?>">
                                                    <a class="dr-instruction-videopop" data-iframe="true" data-src="<?php echo esc_url( $instruction_video ); ?>">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18.095" height="20.894" viewBox="0 0 18.095 20.894"><path id="Path_26366" data-name="Path 26366" d="M107.992,76.108l18.095,10.447L107.992,97Z" transform="translate(-107.992 -76.108)" fill="#fff"/></svg>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php
                                    endif;
                                endif; 
                            ?>
                            <?php if( $global_toggles['enable_mark_as_complete'] ) : ?>
                                <div class="dr-inst-mark-read">
                                    <input type="checkbox" id="dr-instruct-<?php echo esc_attr( $inst_key ); ?>-<?php echo esc_attr( $rand_key ); ?>">
                                    <label for="dr-instruct-<?php echo esc_attr( $inst_key ); ?>-<?php echo esc_attr( $rand_key ); ?>"><?php echo esc_html( $global_toggles['mark_as_complete_lbl'] ); ?></label>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php 
                endif;
            endforeach;
                if ( ! empty( $videoGalleryVids ) && $global_toggles['enable_video'] ) : 
                    ?>
                    <div id="dr-video-gallery-<?php echo esc_attr($recipe->ID); ?>">
                    <?php
                    foreach( $videoGalleryVids as $key => $video ) :
                        if ( 'youtube' === $video['vidType'] ) {
                            $vid_url    = 'https://www.youtube.com/embed/' . $video['vidID'];
                            $image_url  = "https://i3.ytimg.com/vi/{$video['vidID']}/maxresdefault.jpg";
                        } elseif( 'vimeo' === $video['vidType'] ) {
                            $vid_url    = 'https://player.vimeo.com/video/' . $video['vidID'];
                            $image_url  = $video['vidThumb'];
                        }
                        ?>
                            <div class="dr-instructions-video dr-instruction-video-<?php echo esc_attr($recipe->ID); ?>" >
                                <div class="dr-vdo-thumbnail">
                                    <img src="<?php echo esc_url( $image_url ); ?>">
                                    <a class="dr-instruction-videopop" data-src="<?php echo esc_url( $vid_url ); ?>" data-iframe="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18.095" height="20.894" viewBox="0 0 18.095 20.894"><path id="Path_26366" data-name="Path 26366" d="M107.992,76.108l18.095,10.447L107.992,97Z" transform="translate(-107.992 -76.108)" fill="#fff"/></svg>
                                    </a>
                                </div>
                            </div>
                        <?php
                    endforeach;
                    ?>
                    </div>
                    <?php
                endif; ?>
        </div>
    <?php 
endif;
