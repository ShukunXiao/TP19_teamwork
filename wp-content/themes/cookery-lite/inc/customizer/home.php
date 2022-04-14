<?php
/**
 * Front Page Settings
 *
 * @package Cookery_Lite
 */

function cookery_lite_customize_register_frontpage( $wp_customize ) {
	
    /** Front Page Settings */
    $wp_customize->add_panel( 
        'frontpage_settings',
         array(
            'priority'    => 57,
            'capability'  => 'edit_theme_options',
            'title'       => __( 'Front Page Settings', 'cookery-lite' ),
            'description' => __( 'Static Home Page settings.', 'cookery-lite' ),
        ) 
    );    

    /** Banner Settings */
    $wp_customize->get_section( 'header_image' )->panel                    = 'frontpage_settings';
    $wp_customize->get_section( 'header_image' )->title                    = __( 'Banner Section', 'cookery-lite' );
    $wp_customize->get_section( 'header_image' )->priority                 = 10;
    $wp_customize->get_control( 'header_image' )->active_callback          = 'cookery_lite_banner_ac';
    $wp_customize->get_control( 'header_video' )->active_callback          = 'cookery_lite_banner_ac';
    $wp_customize->get_control( 'external_header_video' )->active_callback = 'cookery_lite_banner_ac';
    $wp_customize->get_section( 'header_image' )->description              = '';                                               
    $wp_customize->get_setting( 'header_image' )->transport                = 'refresh';
    $wp_customize->get_setting( 'header_video' )->transport                = 'refresh';
    $wp_customize->get_setting( 'external_header_video' )->transport       = 'refresh';
    
    /** Banner Options */
    $wp_customize->add_setting(
        'ed_banner_section',
        array(
            'default'           => 'slider_banner',
            'sanitize_callback' => 'cookery_lite_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Cookery_Lite_Select_Control(
            $wp_customize,
            'ed_banner_section',
            array(
                'label'       => __( 'Banner Options', 'cookery-lite' ),
                'description' => __( 'Choose banner as static image/video or as a slider.', 'cookery-lite' ),
                'section'     => 'header_image',
                'choices'     => array(
                    'no_banner'        => __( 'Disable Banner Section', 'cookery-lite' ),
                    'static_banner'    => __( 'Static/Video CTA Banner', 'cookery-lite' ),
                    'slider_banner'    => __( 'Banner as Slider', 'cookery-lite' ),
                ),
                'priority' => 5 
            )            
        )
    );
    
    /** Title */
    $wp_customize->add_setting(
        'banner_title',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage'
        )
    );
    
    $wp_customize->add_control(
        'banner_title',
        array(
            'label'           => __( 'Title', 'cookery-lite' ),
            'section'         => 'header_image',
            'type'            => 'text',
            'active_callback' => 'cookery_lite_banner_ac'
        )
    );
    
    $wp_customize->selective_refresh->add_partial( 'banner_title', array(
        'selector' => '.site-banner .banner-caption h2.item-title',
        'render_callback' => 'cookery_lite_get_banner_title',
    ) );
    
    /** Sub Title */
    $wp_customize->add_setting(
        'banner_subtitle',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
            'transport'         => 'postMessage'
        )
    );
    
    $wp_customize->add_control(
        'banner_subtitle',
        array(
            'label'           => __( 'Sub Title', 'cookery-lite' ),
            'section'         => 'header_image',
            'type'            => 'textarea',
            'active_callback' => 'cookery_lite_banner_ac'
        )
    );
    
    $wp_customize->selective_refresh->add_partial( 'banner_subtitle', array(
        'selector' => '.site-banner .banner-caption .item-desc',
        'render_callback' => 'cookery_lite_get_banner_sub_title',
    ) );
    
    /** Banner Label */
    $wp_customize->add_setting(
        'button_one',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage'
        )
    );
    
    $wp_customize->add_control(
        'button_one',
        array(
            'label'           => __( 'Button One Label', 'cookery-lite' ),
            'section'         => 'header_image',
            'type'            => 'text',
            'active_callback' => 'cookery_lite_banner_ac'
        )
    );

    $wp_customize->selective_refresh->add_partial( 'button_one', array(
        'selector' => '.site-banner .banner-caption a.btn-one',
        'render_callback' => 'cookery_lite_get_banner_button_one',
    ) );
    
    /** Banner Link */
    $wp_customize->add_setting(
        'button_one_url',
        array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'button_one_url',
        array(
            'label'           => __( 'Banner One Link', 'cookery-lite' ),
            'section'         => 'header_image',
            'type'            => 'url',
            'active_callback' => 'cookery_lite_banner_ac'
        )
    );
    
    $wp_customize->add_setting(
        'button_one_tab_new',
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'button_one_tab_new',
            array(
                'section'       => 'header_image',
                'label'         => __( 'Button One Open in New tab', 'cookery-lite' ),
                'description'   => __( 'Enable to open button one link in new window.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_banner_ac'
            )
        )
    );

    $wp_customize->add_setting(
        'button_two',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage'
        )
    );
    
    $wp_customize->add_control(
        'button_two',
        array(
            'label'           => __( 'Button Two Label', 'cookery-lite' ),
            'section'         => 'header_image',
            'type'            => 'text',
            'active_callback' => 'cookery_lite_banner_ac'
        )
    );

    $wp_customize->selective_refresh->add_partial( 'button_two', array(
        'selector' => '.site-banner .banner-caption a.btn-two',
        'render_callback' => 'cookery_lite_get_banner_button_two',
    ) );

    $wp_customize->add_setting(
        'button_two_url',
        array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'button_two_url',
        array(
            'label'           => __( 'Banner Two Link', 'cookery-lite' ),
            'section'         => 'header_image',
            'type'            => 'url',
            'active_callback' => 'cookery_lite_banner_ac'
        )
    );

    $wp_customize->add_setting(
        'button_two_tab_new',
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'button_two_tab_new',
            array(
                'section'       => 'header_image',
                'label'         => __( 'Button One Open in New tab', 'cookery-lite' ),
                'description'   => __( 'Enable to open button one link in new window.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_banner_ac'
            )
        )
    );
    
    /** Slider Content Style */
    $wp_customize->add_setting(
        'slider_type',
        array(
            'default'           => 'latest_posts',
            'sanitize_callback' => 'cookery_lite_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Cookery_Lite_Select_Control(
            $wp_customize,
            'slider_type',
            array(
                'label'   => __( 'Slider Content Style', 'cookery-lite' ),
                'section' => 'header_image',
                'choices' => cookery_lite_slider_options(),
                'active_callback' => 'cookery_lite_banner_ac'    
            )
        )
    );
    
    /** Slider Category */
    $wp_customize->add_setting(
        'slider_cat',
        array(
            'default'           => '',
            'sanitize_callback' => 'cookery_lite_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Cookery_Lite_Select_Control(
            $wp_customize,
            'slider_cat',
            array(
                'label'           => __( 'Slider Category', 'cookery-lite' ),
                'section'         => 'header_image',
                'choices'         => cookery_lite_get_categories(),
                'active_callback' => 'cookery_lite_banner_ac'    
            )
        )
    );
    
    /** No. of slides */
    $wp_customize->add_setting(
        'no_of_slides',
        array(
            'default'           => 6,
            'sanitize_callback' => 'cookery_lite_sanitize_number_absint'
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Slider_Control( 
            $wp_customize,
            'no_of_slides',
            array(
                'section'     => 'header_image',
                'label'       => __( 'Number of Slides', 'cookery-lite' ),
                'description' => __( 'Choose the number of slides you want to display', 'cookery-lite' ),
                'choices'     => array(
                    'min'   => 1,
                    'max'   => 20,
                    'step'  => 1,
                ),
                'active_callback' => 'cookery_lite_banner_ac'                 
            )
        )
    );
    
    /** HR */
    $wp_customize->add_setting(
        'banner_hr',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post' 
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Note_Control( 
            $wp_customize,
            'banner_hr',
            array(
                'section'     => 'header_image',
                'description' => '<hr/>',
                'active_callback' => 'cookery_lite_banner_ac'
            )
        )
    );
    
    /** Include Repetitive Posts */
    $wp_customize->add_setting(
        'include_repetitive_posts',
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'include_repetitive_posts',
            array(
                'section'         => 'header_image',
                'label'           => __( 'Include Repetitive Posts', 'cookery-lite' ),
                'description'     => __( 'Enable to add posts included in slider in blog page too.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_banner_ac'
            )
        )
    );
    
    $wp_customize->add_setting(
        'slider_show_date',
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'slider_show_date',
            array(
                'section'         => 'header_image',
                'label'           => __( 'Show Date', 'cookery-lite' ),
                'description'     => __( 'Enable to show posted date on slider.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_banner_ac'
            )
        )
    );
    
    /** Slider Auto */
    $wp_customize->add_setting(
        'slider_auto',
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'slider_auto',
            array(
                'section'     => 'header_image',
                'label'       => __( 'Slider Auto', 'cookery-lite' ),
                'description' => __( 'Enable slider auto transition.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_banner_ac'
            )
        )
    );
    
    /** Slider Loop */
    $wp_customize->add_setting(
        'slider_loop',
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'slider_loop',
            array(
                'section'     => 'header_image',
                'label'       => __( 'Slider Loop', 'cookery-lite' ),
                'description' => __( 'Enable slider loop.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_banner_ac'
            )
        )
    );
    
    /** Slider Caption */
    $wp_customize->add_setting(
        'slider_caption',
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'slider_caption',
            array(
                'section'     => 'header_image',
                'label'       => __( 'Slider Caption', 'cookery-lite' ),
                'description' => __( 'Enable slider caption.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_banner_ac'
            )
        )
    );
    
    /** Full Image */
    $wp_customize->add_setting(
        'slider_full_image',
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'slider_full_image',
            array(
                'section'         => 'header_image',
                'label'           => __( 'Full Image', 'cookery-lite' ),
                'description'     => __( 'Enable to use full size image in slider.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_banner_ac'
            )
        )
    );

    $wp_customize->add_setting( 
        'static_cta_one_color', 
        array(
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        ) 
    );

    $wp_customize->add_control( 
        new WP_Customize_Color_Control( 
            $wp_customize, 
            'static_cta_one_color', 
            array(
                'label'       => __( 'Text Color', 'cookery-lite' ),
                'description' => __( 'Change color of the title and description for this layout.', 'cookery-lite' ),
                'section'     => 'header_image',
                'active_callback' => 'cookery_lite_banner_ac',
            )
        )
    );

    /** Banner Settings Ends */
    
    /** Feature Recipe Settings */

    if( cookery_lite_is_delicious_recipe_activated() ) {
    
        $wp_customize->add_section(
            'featured_recipe_settings',
            array(
                'title'    => __( 'Featured Recipe Section', 'cookery-lite' ),
                'priority' => 65,
                'panel'    => 'frontpage_settings',
            )
        );
        
        $wp_customize->add_setting(
            'featured_recipe_enable',
            array(
                'default'           => true,
                'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
            )
        );
        
        $wp_customize->add_control(
            new Cookery_Lite_Toggle_Control( 
                $wp_customize,
                'featured_recipe_enable',
                array(
                    'section'         => 'featured_recipe_settings',
                    'label'           => __( 'Enable Featured Recipe', 'cookery-lite' ),
                    'description'     => __( 'Enable to show featured recipes in homepage.', 'cookery-lite' ),
                )
            )
        );

        /** Title */
        $wp_customize->add_setting(
            'feature_recipe_title',
            array(
                'default'           => __( 'Featured Recipes', 'cookery-lite' ),
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        
        $wp_customize->add_control(
            'feature_recipe_title',
            array(
                'label'           => __( 'Title', 'cookery-lite' ),
                'section'         => 'featured_recipe_settings',
                'type'            => 'text',
                'active_callback' => 'cookery_lite_featured_recipe_ac'
            )
        );
        
        $wp_customize->selective_refresh->add_partial( 'feature_recipe_title', array(
            'selector' => '.featured-recipe-section .section-header h2.section-title',
            'render_callback' => 'cookery_lite_get_feature_recipe_title',
        ) );
        
        /** Sub Title */
        $wp_customize->add_setting(
            'feature_recipe_subtitle',
            array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        
        $wp_customize->add_control(
            'feature_recipe_subtitle',
            array(
                'label'           => __( 'Subtitle', 'cookery-lite' ),
                'section'         => 'featured_recipe_settings',
                'type'            => 'text',
                'active_callback' => 'cookery_lite_featured_recipe_ac'
            )
        );
        
        $wp_customize->selective_refresh->add_partial( 'feature_recipe_subtitle', array(
            'selector' => '.featured-recipe-section .section-header .section-desc',
            'render_callback' => 'cookery_lite_get_feature_recipe_subtitle',
        ) );
        
        /** No. of slides */
        $wp_customize->add_setting(
            'feature_recipe_post',
            array(
                'default'           => 5,
                'sanitize_callback' => 'cookery_lite_sanitize_number_absint'
            )
        );
        
        $wp_customize->add_control(
            new Cookery_Lite_Slider_Control( 
                $wp_customize,
                'feature_recipe_post',
                array(
                    'section'     => 'featured_recipe_settings',
                    'label'       => __( 'Number of Recipes', 'cookery-lite' ),
                    'description' => __( 'Choose the number of recipes you want to display', 'cookery-lite' ),
                    'choices'     => array(
                        'min'   => 1,
                        'max'   => 20,
                        'step'  => 1,
                    ),               
                    'active_callback' => 'cookery_lite_featured_recipe_ac'
                )
            )
        );

        $wp_customize->add_setting(
            'featured_recipe_bg',
            array(
                'default'           => '',
                'sanitize_callback' => 'cookery_lite_sanitize_image',
            )
        );
        
        $wp_customize->add_control(
            new WP_Customize_Image_Control(
            $wp_customize,
                'featured_recipe_bg',
                array(
                    'label'           => __( 'Choose Background Image', 'cookery-lite' ),
                    'description'     => __( 'Choose the background image for featured recipe section. Recommended image format is PNG Format.', 'cookery-lite' ),
                    'section'         => 'featured_recipe_settings',
                    'active_callback' => 'cookery_lite_featured_recipe_ac'
                )
            )
        );
    }
    /** Featured Recipe Settings Ends */

    /** Client Section */
    $wp_customize->add_section(
        'client',
        array(
            'title'    => __( 'Client Section', 'cookery-lite' ),
            'priority' => 75,
            'panel'    => 'frontpage_settings',
        )
    );

    $wp_customize->add_setting(
        'client_section_bg',
        array(
            'default'           => '',
            'sanitize_callback' => 'cookery_lite_sanitize_image',
        )
    );
    
    $wp_customize->add_control(
        new WP_Customize_Image_Control(
        $wp_customize,
            'client_section_bg',
            array(
                'label'           => __( 'Choose Background', 'cookery-lite' ),
                'description'     => __( 'Choose the background image for client section. Recommended image format is PNG Format.', 'cookery-lite' ),
                'section'         => 'client',
                'priority'        => -1
            )
        )
    );

    $wp_customize->add_setting(
        'client_note_text',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post' 
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Note_Control( 
            $wp_customize,
            'client_note_text',
            array(
                'section'     => 'client',
                'description' => __( '<hr/>Add "Blossom: Client Logo Widget" for client section. The recommended logo size is 330x190px. Please upload logo of atleast width 330px to avoid cropping..', 'cookery-lite' ),
                'priority'    => -1
            )
        )
    );

    $client_section = $wp_customize->get_section( 'sidebar-widgets-client' );
    if ( ! empty( $client_section ) ) {

        $client_section->panel     = 'frontpage_settings';
        $client_section->priority  = 75;
        $wp_customize->get_control( 'client_note_text' )->section = 'sidebar-widgets-client';
        $wp_customize->get_control( 'client_section_bg' )->section    = 'sidebar-widgets-client';
    }  
    
    /** Client Section Ends */

    /** Newsletter Settings */
    $wp_customize->add_section(
        'newsletter_section',
        array(
            'title'    => __( 'Footer Newsletter Section', 'cookery-lite' ),
            'priority' => 80,
            'panel'    => 'frontpage_settings',
        )
    );
    
    if( cookery_lite_is_btnw_activated() ){
        
        /** Enable Newsletter Section */
        $wp_customize->add_setting( 
            'ed_newsletter_section', 
            array(
                'default'           => false,
                'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
            new Cookery_Lite_Toggle_Control( 
                $wp_customize,
                'ed_newsletter_section',
                array(
                    'section'     => 'newsletter_section',
                    'label'       => __( 'Newsletter Section', 'cookery-lite' ),
                    'description' => __( 'Enable to show Newsletter Section on footer', 'cookery-lite' ),
                )
            )
        );
    
        /** Newsletter Shortcode */
        $wp_customize->add_setting(
            'newsletter_section_shortcode',
            array(
                'default'           => '',
                'sanitize_callback' => 'wp_kses_post',
            )
        );
        
        $wp_customize->add_control(
            'newsletter_section_shortcode',
            array(
                'type'        => 'text',
                'section'     => 'newsletter_section',
                'label'       => __( 'Newsletter Shortcode', 'cookery-lite' ),
                'description' => __( 'Enter the BlossomThemes Email Newsletters Shortcode. Ex. [BTEN id="356"]', 'cookery-lite' ),
            )
        ); 

        $wp_customize->add_setting(
            'newsletter_section_bg',
            array(
                'default'           => '',
                'sanitize_callback' => 'cookery_lite_sanitize_image',
            )
        );
        
        $wp_customize->add_control(
            new WP_Customize_Image_Control(
            $wp_customize,
                'newsletter_section_bg',
                array(
                    'label'           => __( 'Choose Background Image', 'cookery-lite' ),
                    'description'     => __( 'Choose the background image for newsletter section. Recommended image format is PNG Format.', 'cookery-lite' ),
                    'section'         => 'newsletter_section',
                )
            )
        );
    } else {
        $wp_customize->add_setting(
            'newsletter_section_recommend',
            array(
                'sanitize_callback' => 'wp_kses_post',
            )
        );

        $wp_customize->add_control(
            new Cookery_Lite_Plugin_Recommend_Control(
                $wp_customize,
                'newsletter_section_recommend',
                array(
                    'section'     => 'newsletter_section',
                    'label'       => __( 'Newsletter Shortcode', 'cookery-lite' ),
                    'capability'  => 'install_plugins',
                    'plugin_slug' => 'blossomthemes-email-newsletter',//This is the slug of recommended plugin.
                    'description' => sprintf( __( 'Please install and activate the recommended plugin %1$sBlossomThemes Email Newsletter%2$s. After that option related with this section will be visible.', 'cookery-lite' ), '<strong>', '</strong>' ),
                )
            )
        );
    }
}
add_action( 'customize_register', 'cookery_lite_customize_register_frontpage' );

if ( ! function_exists( 'cookery_lite_slider_options' ) ) :
    /**
     * @return array Content type options
     */
    function cookery_lite_slider_options() {
        $slider_options = array(
            'latest_posts' => __( 'Latest Posts', 'cookery-lite' ),
            'cat'          => __( 'Category', 'cookery-lite' ),
        );
        if ( cookery_lite_is_delicious_recipe_activated() ) {
            $slider_options = array_merge( $slider_options, array( 'latest_recipes' => __( 'Latest Recipes', 'cookery-lite' ) ) );
        }
        $output = apply_filters( 'cookery_lite_slider_options', $slider_options );
        return $output;
    }
endif;