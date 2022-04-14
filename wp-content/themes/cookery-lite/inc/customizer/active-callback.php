<?php
/**
 * Active Callback
 * 
 * @package Cookery_Lite
*/

/**
 * Active Callback for Banner Slider
*/
function cookery_lite_banner_ac( $control ){
    $banner        = $control->manager->get_setting( 'ed_banner_section' )->value();
    $slider_type   = $control->manager->get_setting( 'slider_type' )->value();
    $control_id    = $control->id;
    
    if ( $control_id == 'header_image' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'header_video' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'external_header_video' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'banner_title' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'banner_subtitle' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'button_one' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'button_one_url' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'button_one_tab_new' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'button_two' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'button_two_url' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'button_two_tab_new' && $banner == 'static_banner' ) return true;
    if ( $control_id == 'static_cta_one_color' && $banner == 'static_banner' ) return true;
    
    if ( $control_id == 'slider_type' && $banner == 'slider_banner' ) return true;
    if ( $control_id == 'include_repetitive_posts' && $banner == 'slider_banner' && ( $slider_type == 'latest_posts' || $slider_type == 'cat' ) ) return true;
    if ( $control_id == 'slider_show_date' && $banner == 'slider_banner' && ( $slider_type == 'latest_posts' || $slider_type == 'cat' ) ) return true;
    if ( $control_id == 'slider_auto' && $banner == 'slider_banner' ) return true;
    if ( $control_id == 'slider_loop' && $banner == 'slider_banner' ) return true;
    if ( $control_id == 'slider_caption' && $banner == 'slider_banner' ) return true;              
    if ( $control_id == 'slider_cat' && $banner == 'slider_banner' && $slider_type == 'cat' ) return true;
    if ( $control_id == 'no_of_slides' && $banner == 'slider_banner' && $slider_type == 'latest_posts' ) return true;
    if ( $control_id == 'slider_full_image' && $banner == 'slider_banner' ) return true;
    if ( $control_id == 'slider_animation' && $banner == 'slider_banner' ) return true;
    if ( $control_id == 'slider_speed' && $banner == 'slider_banner' ) return true;
    if ( $control_id == 'banner_hr' && $banner == 'slider_banner' ) return true; 
    
    return false;
}

/**
 * Active Callback for post/page
*/
function cookery_lite_post_page_ac( $control ){
    
    $ed_related    = $control->manager->get_setting( 'ed_related' )->value();
    $ed_comment    = $control->manager->get_setting( 'ed_comments' )->value();
    $control_id    = $control->id;
    
    if ( $control_id == 'related_post_title' && $ed_related == true ) return true;
    if ( $control_id == 'toggle_comments' && $ed_comment == true ) return true;
    if ( $control_id == 'ed_featured_image' ) return true;
    
    return false;
}

/**
 * Active Callback for homepage featured recipe
*/
function cookery_lite_featured_recipe_ac( $control ){
    $featured_recipe_enable = $control->manager->get_setting( 'featured_recipe_enable' )->value();
    
    if ( $featured_recipe_enable ) return true;
    
    return false;
}