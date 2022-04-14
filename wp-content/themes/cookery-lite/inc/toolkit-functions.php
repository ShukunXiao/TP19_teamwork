<?php
/**
 * Toolkit Filters
 *
 * @package Cookery_Lite
 */

if( ! function_exists( 'cookery_lite_default_image_text_size' ) ) :
    function cookery_lite_default_image_text_size(){
        return 'cookery-lite-promo';
    }
endif;
add_filter( 'bttk_it_img_size', 'cookery_lite_default_image_text_size' );

if( ! function_exists( 'cookery_lite_author_image' ) ) :
    function cookery_lite_author_image(){
       return 'full';
    }
endif;
add_filter( 'author_bio_img_size', 'cookery_lite_author_image' );

if( ! function_exists( 'cookery_lite_featured_page_alignment' ) ) :
    function cookery_lite_featured_page_alignment(){
        
        $array = array(
            'right'     => __( 'Right', 'cookery-lite' ),
            'left'      => __( 'Left', 'cookery-lite' ),
        );

        return $array;
    }
endif;
add_filter( 'blossomthemes_cta_button_alignment', 'cookery_lite_featured_page_alignment' );

if( ! function_exists( 'cookery_lite_featured_page_widget_filter' ) ) :
/**
 * Filter for Featured page widget
*/
function cookery_lite_featured_page_widget_filter( $html, $args, $instance ){ 
    $read_more         = !empty( $instance['readmore'] ) ? $instance['readmore'] : __( 'Read More', 'cookery-lite' );      
    $show_feat_img     = !empty( $instance['show_feat_img'] ) ? $instance['show_feat_img'] : '' ;  
    $show_page_content = !empty( $instance['show_page_content'] ) ? $instance['show_page_content'] : '' ;        
    $show_readmore     = !empty( $instance['show_readmore'] ) ? $instance['show_readmore'] : '' ;        
    $page_list         = !empty( $instance['page_list'] ) ? $instance['page_list'] : 1 ;
    $image_alignment   = !empty( $instance['image_alignment'] ) ? $instance['image_alignment'] : 'left' ;
    if( !isset( $page_list ) || $page_list == '' ) return;
    
    $post_no = get_post( $page_list ); 
    
    $target = 'target="_blank"';
    if( isset($instance['target']) && $instance['target']!='' ) {
        $target = 'target="_self"';
    }
    
    if( $post_no ){
        setup_postdata( $post_no );
        ob_start(); ?>
        <div class="widget-featured-holder <?php echo esc_attr($image_alignment);?>">
                <?php
                if( ( has_post_thumbnail( $post_no ) ) && $show_feat_img ){ ?>
                <div class="img-holder">
                    <a <?php echo $target;?> href="<?php the_permalink( $post_no ); ?>">
                        <?php 
                        $featured_img_size = 'full';
                        if( has_post_thumbnail( $post_no ) ) echo get_the_post_thumbnail( $post_no, $featured_img_size ); ?>
                    </a>
                </div>
                <?php } ?>
                <div class="text-holder">
                    <?php 
                    echo $args['before_title']; //Done for SEO
                    echo esc_html( $post_no->post_title );
                    echo $args['after_title'];
                    ?>
                    <div class="featured_page_content">
                        <?php 
                        if( isset( $show_page_content ) && $show_page_content!='' ){
                            echo apply_filters( 'the_content', $post_no->post_content );                                
                        }else{
                            echo apply_filters( 'the_excerpt', get_the_excerpt( $post_no ) );                                
                        }
                        
                        if( isset( $show_readmore ) && $show_readmore!='' ){ ?>
                            <a href="<?php the_permalink( $post_no ); ?>" <?php echo $target;?> class="btn-readmore"><?php echo esc_html( $read_more );?></a>
                            <?php
                        }
                        ?>
                    </div>
                </div>                    
        </div>                    
        <?php    
        $html = ob_get_clean();
        wp_reset_postdata();
        return $html;
    }
}
endif;
add_filter( 'blossom_featured_page_widget_filter', 'cookery_lite_featured_page_widget_filter', 10, 3 );