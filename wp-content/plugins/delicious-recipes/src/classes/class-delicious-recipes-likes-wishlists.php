<?php
/**
 * Recipes Likes / wishlists Class
 *
 * @package Delicious_Recipes
*/
class Delicious_Recipes_Likes_Wishlists {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_ajax_recipe_likes', [ $this, 'recipe_like_cb' ] );
        add_action( 'wp_ajax_nopriv_recipe_likes', [ $this, 'recipe_like_cb' ] );
        add_action( 'delicious_recipes_like_button', [ $this, 'recipe_like_button' ], 10 );

        //Recipe Wishlists
        add_action( 'delicious_recipes_wishlist_button', [ $this, 'recipe_wishlist_button' ], 10, 1 );
		add_action( 'wp_ajax_delicious_recipes_wishlist', array(&$this,'recipe_wishlist_cb') );
        add_action( 'delicious_recipes_badges', array(&$this,'recipe_badges') );

    }

    /**
     * Get Real IP Address from client.
     *
     * @return void
     */
    public function get_real_ip_address(){
        if( getenv( 'HTTP_CLIENT_IP' ) ){
            $ip = getenv( 'HTTP_CLIENT_IP' );
        }elseif( getenv( 'HTTP_X_FORWARDED_FOR' ) ){
            $ip = getenv('HTTP_X_FORWARDED_FOR' );
        }elseif( getenv( 'HTTP_X_FORWARDED' ) ){
            $ip = getenv( 'HTTP_X_FORWARDED' );
        }elseif( getenv( 'HTTP_FORWARDED_FOR' ) ){
            $ip = getenv( 'HTTP_FORWARDED_FOR' );
        }elseif( getenv( 'HTTP_FORWARDED' ) ){
            $ip = getenv( 'HTTP_FORWARDED' );
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * Check if user can like the recipes post
     *
     * @param integer $id
     * @return boolean
     */
    public function can_like( $id = 0 ) {
        // Return early if $id is not set.
        if( ! $id ){
            return false;
        }

        $ip_list = ( $ip = get_post_meta( $id, '_recipe_likes_ip', true ) ) ? $ip  : array();

        if( ( $ip_list == '' ) || ( is_array( $ip_list ) && ! in_array( $this->get_real_ip_address(), $ip_list ) ) ){
            return true;
        }

        return false;
    }

    /**
     * Like button template
     *
     * @return void
     */
    public function recipe_like_button(){
        global $recipe;
        $like_count = $this->get_recipe_like_count( $recipe->ID );

        delicious_recipes_get_template( 'recipe/recipe-like.php', [
            'like_count' => $like_count,
            'id'         => $recipe->ID,
            'can_like'   => $this->can_like( $recipe->ID )
        ] );
    }

    /**
     * AJAX callback for recipe likes.
     *
     * @return void
     */
    public function recipe_like_cb(){

        if ( isset( $_POST['id'] ) && isset( $_POST['add_remove'] ) ) :

            $post_id = intval( $_POST['id'] );
            $likes   = ( $count = get_post_meta( $post_id, '_recipe_likes', true ) ) ? absint( $count ) : 0;
            $ip_list = ( $ip = get_post_meta( $post_id, '_recipe_likes_ip', true ) ) ? $ip : array();

            $add_remove    = sanitize_title( $_POST['add_remove'] );
            if ( $add_remove === 'add' && $this->can_like( $post_id ) ):
                $ip_list[] = $this->get_real_ip_address();
                $ip_list = array_map( 'trim', $ip_list );
                $likes++;
                update_post_meta( $post_id, '_recipe_likes', absint( $likes ) );
                update_post_meta( $post_id, '_recipe_likes_ip', $ip_list );
            elseif ( $add_remove === 'remove' ):
                if ( ( $key = array_search( $this->get_real_ip_address(), $ip_list ) ) !== false ):
                    $likes--;
                    if ( $likes <= 0 ): $likes = 0; endif;
                    unset( $ip_list[$key] );
                    update_post_meta( $post_id, '_recipe_likes', absint( $likes ) );
                    update_post_meta( $post_id, '_recipe_likes_ip', $ip_list );
                endif;
            else:
                wp_send_json_error();
            endif;

            $like_count = $this->get_recipe_like_count( $post_id );
            /* translators: %s: number of likes */
            wp_send_json_success( [ 'likes' => sprintf( _nx( '%s Like', '%s Likes', $like_count, 'number of likes', 'delicious-recipes' ), $like_count ),
                                    'likes_count' => $like_count ] );

        else:
            wp_send_json_error();
		endif;
		wp_die();
    }

    /**
     * Get recipe likes count
     *
     * @param [type] $post_id
     * @return void
     */
    public function get_recipe_like_count( $post_id ){
        $count = get_post_meta( $post_id, '_recipe_likes', true );
        if( $count ){
            return $count;
        }else{
            return 0;
        }
    }

    /**
     * Wishlist button template
     *
     * @return void
     */
    public function recipe_wishlist_button( $disable_wishlist = false ){

        if( $disable_wishlist )
            return;

        $global_toggles = delicious_recipes_get_global_toggles_and_labels();

        if ( $global_toggles['enable_add_to_wishlist'] ) {

            global $recipe, $wp;
            $current_user          = wp_get_current_user();
            $dashboard_page_exists = delicious_recipes_get_dashboard_page_id() > 0;

            $_user_meta             = get_user_meta( $current_user->ID, 'delicious_recipes_user_meta', true );
            $bookmarked             = isset( $_user_meta['wishlists'] ) && in_array( $recipe->ID, $_user_meta['wishlists'] ) ? 'dr-wishlist-is-bookmarked' : '';
            $recipe_wishlists_count = isset( $recipe->wishlists_count ) && $recipe->wishlists_count ? number_format( $recipe->wishlists_count, 0 ) : 0;
            $add_to_wishlist_lbl    = $bookmarked ? __( 'Added to Favorites', 'delicious-recipes' ) : $global_toggles['add_to_wishlist_lbl'];

            if( $recipe && $dashboard_page_exists ) {

                delicious_recipes_get_template( 'recipe/recipe-wishlist.php', [
                    'id'                  => $recipe->ID,
                    'user_meta'           => $_user_meta,
                    'bookmarked'          => $bookmarked,
                    'wishlists_count'     => $recipe_wishlists_count,
                    'logged_in'           => is_user_logged_in(),
                    'recipe_single'       => is_recipe(),
                    'add_to_wishlist_lbl' => $add_to_wishlist_lbl,
                ] );

            }

        }

    }

    /**
     * AJAX callback for recipe wishlist.
     *
     * @return void
     */
    public function recipe_wishlist_cb(){

        global $wp;
        $current_user = wp_get_current_user();

        $_user_meta = get_user_meta( $current_user->ID, 'delicious_recipes_user_meta', true );
        if ( ! is_array( $_user_meta ) || empty( $_user_meta ) ): $_user_meta = array(); endif;

        if ( $current_user && isset( $_POST['recipe_id'] ) && isset( $_POST['add_remove'] ) ):

			$add_remove    = sanitize_title( $_POST['add_remove'] );
			$recipe_id     = absint( $_POST['recipe_id'] );
			$wishlists     = isset( $_user_meta['wishlists'] ) && !empty( $_user_meta['wishlists'] ) ? $_user_meta['wishlists'] : array();
			$current_total = get_post_meta( $recipe_id, '_delicious_recipes_wishlists', true );

			if ( $add_remove === 'add' ):
				$wishlists[] = $recipe_id;
				$current_total++;
            elseif ( $add_remove === 'remove' ):
                if ( ( $key = array_search( $recipe_id, $wishlists ) ) !== false ):
                    $current_total--;
                    if ( $current_total <= 0 ): $current_total = ''; update_post_meta( $recipe_id, '_delicious_recipes_wishlists', 0 ); endif;
                    unset( $wishlists[$key] );
                endif;
			else:
				wp_send_json_error();
			endif;

			$wishlists = array_unique( $wishlists );
            $wishlists = array_map( 'sanitize_text_field', $wishlists );
			$_user_meta['wishlists'] = $wishlists;

			update_user_meta( $current_user->ID, 'delicious_recipes_user_meta', $_user_meta );
			update_post_meta( $recipe_id, '_delicious_recipes_wishlists', absint( $current_total ) );

            $global_toggles = delicious_recipes_get_global_toggles_and_labels();
            $message        = $add_remove === 'add' ? __( 'Added to Favorites', 'delicious-recipes' ) : $global_toggles['add_to_wishlist_lbl'];

            wp_send_json_success( [ 'wishlists' => $current_total, 'message' => $message ] );

		else:
			wp_send_json_error();
		endif;
		wp_die();

    }

    /**
     * Recipe Badge template
     *
     * @return void
     */
    public function recipe_badges(){
        global $recipe;

        if ( ! empty( $recipe->badges ) ) : ?>
            <span class="dr-badge">
                <?php
                    $badge       = get_term_by( 'name', $recipe->badges[0], 'recipe-badge' );
                    $badge_metas = get_term_meta( $badge->term_id, 'dr_taxonomy_metas', true );
                    $tax_color   = isset( $badge_metas['taxonomy_color'] )  && ! empty( $badge_metas['taxonomy_color'] ) ? $badge_metas['taxonomy_color'] : '#E84E3B';
                ?>

                <a href="<?php echo esc_url( get_term_link( $badge, 'recipe-badge' ) ); ?>" title="<?php echo esc_attr( $recipe->badges[0] ); ?>" style="background-color: <?php echo  esc_attr( $tax_color ); ?>">
                    <?php  echo esc_html( $recipe->badges[0] ); ?>
                </a>
            </span>
        <?php endif;
    }
}

new Delicious_Recipes_Likes_Wishlists();
