<?php
/**
 * Edit Profile tab content.
 */

// Print Errors / Notices.
delicious_recipes_print_notices();

global $wp, $post;
$current_user  = wp_get_current_user();
$_user_meta    = get_user_meta( $current_user->ID, 'delicious_recipes_user_meta', true );
$custom_avatar = isset( $_user_meta['profile_image_id'] ) && $_user_meta['profile_image_id'] ? $_user_meta['profile_image_id'] : '';

if ( !empty( $current_user ) ):

    if ( isset( $_user_meta['profile_image_id'] ) && $_user_meta['profile_image_id'] && wp_attachment_is_image( $_user_meta['profile_image_id'] ) ):
        $profile_image = wp_get_attachment_image( $_user_meta['profile_image_id'], 'thumbnail' );
        $profile_image_src = wp_get_attachment_image_src( $_user_meta['profile_image_id'], 'thumbnail' );
        $profile_image_src = ( isset($profile_image_src[0]) && $profile_image_src[0] ? $profile_image_src[0] : false );
    else :
        $profile_image = get_avatar( $current_user->user_email, 'thumbnail' );
        $profile_image_src = get_avatar_url( $current_user->user_email, 'thumbnail' );
    endif;

endif;

?>
<div class="dr-form__edit-profile-wrapper" id="profile">
    <form class="dr-form__fields-wrapper" method="post">
        <div class="dr-form__heading">
            <h2 class="dr-form__title"><?php esc_html_e( 'Edit Profile', 'delicious-recipes' ); ?></h2>
        </div>

        <?php do_action( 'delicious_recipes_edit_profile_fields_before'); ?>

        <div class="dr-form__field dr-form__field-row">
            <div class="dr-input-wrap">
                <div class="dr-input-upload-file">
                    <div id="profile-img" class="dr-profile-img-holder user-male dropzone">
                        <?php $custom = isset( $_user_meta['profile_image_id'] ) && $_user_meta['profile_image_id'] ? 'custom' : ''; ?>
                        <input type="hidden" name="profile_image" value="<?php echo esc_attr( $custom ); ?>">
                        <input type="hidden" name="profile_image_url" value="<?php echo esc_attr( $profile_image_src ); ?>">
                        <input type="hidden" name="profile_image_nonce" value="<?php echo wp_create_nonce( 'delicious-recipes-profile-image-nonce' ); ?>">
                        <div class="img">
                            <?php 
                                if( $custom_avatar ) :
                                    $image_thumb = wp_get_attachment_image( $custom_avatar, 'thumbnail' );
                                    echo wp_kses_post( $image_thumb );
                                else :
                                    echo get_avatar( $current_user->user_email );
                                endif; 
                            ?>
                        </div>
                        <div class="dr-profile-btns">
                            <button type="button" class="dr-profile-img-delete" style="<?php echo $custom_avatar ? 'display:block' : 'display:none'; ?>">X</button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="dr-form__field dr-form__field-row">
            <label for="username"><?php esc_html_e( 'Username', 'delicious-recipes' ); ?></label>
            <div class="dr-input-wrap">
                <input type="text" id="username" name="username" class="dr-form__field-input" value="<?php echo esc_attr( $current_user->user_login ); ?>" placeholder="e.g.deliciousrecipes" readonly />
                <div class="dr-form__info">
                    <p><?php esc_html_e( 'Usernames cannot be changed.', 'delicious-recipes' ); ?></p>
                </div>
            </div>
        </div>

        <div class="dr-form__field dr-form__field-row">
            <label for="email"><?php esc_html_e( 'Email', 'delicious-recipes' ); ?></label>
            <div class="dr-input-wrap">
                <input type="email" id="email" name="email" class="dr-form__field-input" value="<?php echo esc_attr( $current_user->user_email ); ?>" placeholder="deliciousrecipes@gmail.com" />
            </div>
        </div>

        <div class="dr-form__field dr-form__field-row has-info">
            <label for="name"><?php esc_html_e( 'Display Name', 'delicious-recipes' ); ?></label>
            <div class="dr-input-wrap">
                <input type="text" id="name" name="name" class="dr-form__field-input" value="<?php echo esc_attr( $current_user->display_name ); ?>" placeholder="e.g.Matteew Marry">
                <div class="dr-form__info">
                    <p><?php esc_html_e( 'Display name is used to display publicly on dashboard screen.', 'delicious-recipes' ); ?></p>
                </div>
            </div>
        </div>

        <div class="dr-form__field dr-form__field-row">
            <label for="current_password"><?php esc_html_e( 'Current Password', 'delicious-recipes' ); ?></label>
            <div class="dr-input-wrap has-pw-toggle-btn">
                <input type="password" id="current_password" name="current_password" class="dr-form__field-input password" value="" placeholder="<?php esc_html_e( 'Current Password', 'delicious-recipes' ); ?>">
                <button type="button" class="pw-toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="11.323" viewBox="0 0 19 11.323">
                        <g id="Group_5883" data-name="Group 5883" transform="translate(-902 -715)" opacity="0.45">
                            <g id="Path_30668" data-name="Path 30668" transform="translate(902 715)" fill="#fff">
                            <path d="M 9.5 10.57278251647949 C 5.533700942993164 10.57278251647949 2.076350688934326 6.953097343444824 0.9690772294998169 5.661375045776367 C 2.076153755187988 4.369917392730713 5.533700942993164 0.7500022053718567 9.5 0.7500022053718567 C 13.46630764007568 0.7500022053718567 16.92365264892578 4.369687080383301 18.03092384338379 5.661409378051758 C 16.92384910583496 6.952867031097412 13.46630764007568 10.57278251647949 9.5 10.57278251647949 Z" stroke="none"/>
                            <path d="M 9.5 1.500001907348633 C 6.252052307128906 1.500001907348633 3.316156387329102 4.206014156341553 1.972211837768555 5.661322593688965 C 3.317028999328613 7.117557525634766 6.252592086791992 9.822782516479492 9.5 9.822782516479492 C 12.74794769287109 9.822782516479492 15.6838436126709 7.116770267486572 17.02778816223145 5.66146183013916 C 15.68297100067139 4.205226898193359 12.74740791320801 1.500001907348633 9.5 1.500001907348633 M 9.5 1.9073486328125e-06 C 14.74670028686523 1.9073486328125e-06 19 5.661392211914063 19 5.661392211914063 C 19 5.661392211914063 14.74670028686523 11.32278251647949 9.5 11.32278251647949 C 4.253299713134766 11.32278251647949 0 5.661392211914063 0 5.661392211914063 C 0 5.661392211914063 4.253299713134766 1.9073486328125e-06 9.5 1.9073486328125e-06 Z" stroke="none" fill="#374757"/>
                            </g>
                            <path id="Path_30669" data-name="Path 30669" d="M2.175,0A2.175,2.175,0,1,1,0,2.175,2.175,2.175,0,0,1,2.175,0Z" transform="translate(909.325 718.325)" fill="#374757"/>
                        </g>
                    </svg>
                </button>
            </div>
        </div>

        <div class="dr-form__field dr-form__field-row">
            <label for="new_password"><?php esc_html_e( 'New Password', 'delicious-recipes' ); ?></label>
            <div class="dr-input-wrap has-pw-toggle-btn">
                <input type="password" id="new_password" name="new_password" class="dr-form__field-input password" value="" placeholder="<?php esc_html_e( 'New Password', 'delicious-recipes' ); ?>">
                <button type="button" class="pw-toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="11.323" viewBox="0 0 19 11.323">
                        <g id="Group_5883" data-name="Group 5883" transform="translate(-902 -715)" opacity="0.45">
                            <g id="Path_30668" data-name="Path 30668" transform="translate(902 715)" fill="#fff">
                            <path d="M 9.5 10.57278251647949 C 5.533700942993164 10.57278251647949 2.076350688934326 6.953097343444824 0.9690772294998169 5.661375045776367 C 2.076153755187988 4.369917392730713 5.533700942993164 0.7500022053718567 9.5 0.7500022053718567 C 13.46630764007568 0.7500022053718567 16.92365264892578 4.369687080383301 18.03092384338379 5.661409378051758 C 16.92384910583496 6.952867031097412 13.46630764007568 10.57278251647949 9.5 10.57278251647949 Z" stroke="none"/>
                            <path d="M 9.5 1.500001907348633 C 6.252052307128906 1.500001907348633 3.316156387329102 4.206014156341553 1.972211837768555 5.661322593688965 C 3.317028999328613 7.117557525634766 6.252592086791992 9.822782516479492 9.5 9.822782516479492 C 12.74794769287109 9.822782516479492 15.6838436126709 7.116770267486572 17.02778816223145 5.66146183013916 C 15.68297100067139 4.205226898193359 12.74740791320801 1.500001907348633 9.5 1.500001907348633 M 9.5 1.9073486328125e-06 C 14.74670028686523 1.9073486328125e-06 19 5.661392211914063 19 5.661392211914063 C 19 5.661392211914063 14.74670028686523 11.32278251647949 9.5 11.32278251647949 C 4.253299713134766 11.32278251647949 0 5.661392211914063 0 5.661392211914063 C 0 5.661392211914063 4.253299713134766 1.9073486328125e-06 9.5 1.9073486328125e-06 Z" stroke="none" fill="#374757"/>
                            </g>
                            <path id="Path_30669" data-name="Path 30669" d="M2.175,0A2.175,2.175,0,1,1,0,2.175,2.175,2.175,0,0,1,2.175,0Z" transform="translate(909.325 718.325)" fill="#374757"/>
                        </g>
                    </svg>
                </button>
            </div>
        </div>

        <div class="dr-form__field dr-form__field-row">
            <label for="confirm_password"><?php esc_html_e( 'Confirm Password', 'delicious-recipes' ); ?></label>
            <div class="dr-input-wrap has-pw-toggle-btn">
                <input type="password" id="confirm_password" name="confirm_password" class="dr-form__field-input password" value="" placeholder="<?php esc_html_e( 'Re-enter the new password', 'delicious-recipes' ); ?>">
                <button type="button" class="pw-toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="11.323" viewBox="0 0 19 11.323">
                        <g id="Group_5883" data-name="Group 5883" transform="translate(-902 -715)" opacity="0.45">
                            <g id="Path_30668" data-name="Path 30668" transform="translate(902 715)" fill="#fff">
                            <path d="M 9.5 10.57278251647949 C 5.533700942993164 10.57278251647949 2.076350688934326 6.953097343444824 0.9690772294998169 5.661375045776367 C 2.076153755187988 4.369917392730713 5.533700942993164 0.7500022053718567 9.5 0.7500022053718567 C 13.46630764007568 0.7500022053718567 16.92365264892578 4.369687080383301 18.03092384338379 5.661409378051758 C 16.92384910583496 6.952867031097412 13.46630764007568 10.57278251647949 9.5 10.57278251647949 Z" stroke="none"/>
                            <path d="M 9.5 1.500001907348633 C 6.252052307128906 1.500001907348633 3.316156387329102 4.206014156341553 1.972211837768555 5.661322593688965 C 3.317028999328613 7.117557525634766 6.252592086791992 9.822782516479492 9.5 9.822782516479492 C 12.74794769287109 9.822782516479492 15.6838436126709 7.116770267486572 17.02778816223145 5.66146183013916 C 15.68297100067139 4.205226898193359 12.74740791320801 1.500001907348633 9.5 1.500001907348633 M 9.5 1.9073486328125e-06 C 14.74670028686523 1.9073486328125e-06 19 5.661392211914063 19 5.661392211914063 C 19 5.661392211914063 14.74670028686523 11.32278251647949 9.5 11.32278251647949 C 4.253299713134766 11.32278251647949 0 5.661392211914063 0 5.661392211914063 C 0 5.661392211914063 4.253299713134766 1.9073486328125e-06 9.5 1.9073486328125e-06 Z" stroke="none" fill="#374757"/>
                            </g>
                            <path id="Path_30669" data-name="Path 30669" d="M2.175,0A2.175,2.175,0,1,1,0,2.175,2.175,2.175,0,0,1,2.175,0Z" transform="translate(909.325 718.325)" fill="#374757"/>
                        </g>
                    </svg>
                </button>
            </div>
        </div>

        <input type="hidden" name="redirect_id" value="<?php echo esc_attr( $post->ID ); ?>" />
		<input type="hidden" name="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>" />
		<input type="hidden" name="action" value="delicious_recipes_edit_user_profile" />
		<input type="hidden" name="delicious_recipes_edit_profile_nonce" value="<?php echo wp_create_nonce( 'delicious-recipes-edit-profile-nonce' ); ?>" />

        <div class="dr-form__field-submit">
            <input type="submit" value="<?php esc_attr_e( 'Update Your Profile', 'delicious-recipes'); ?>" class="dr-form__submit" name="delicious_recipes_edit_profile">
        </div>

        <?php do_action( 'delicious_recipes_edit_profile_fields_before'); ?>

    </form>
</div>
<?php 
