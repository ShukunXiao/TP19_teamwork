<?php
/**
 * General Settings
 *
 * @package Cookery_Lite
 */

function cookery_lite_customize_register_general( $wp_customize ){
    
    /** General Settings */
    $wp_customize->add_panel( 
        'general_settings',
         array(
            'priority'    => 60,
            'capability'  => 'edit_theme_options',
            'title'       => __( 'General Settings', 'cookery-lite' ),
            'description' => __( 'Customize Banner, Featured, Social, Sharing, SEO, Post/Page, Newsletter & Instagram, Shop, Performance and Miscellaneous settings.', 'cookery-lite' ),
        ) 
    );

    /** Social Media Settings */
    $wp_customize->add_section(
        'social_media_settings',
        array(
            'title'    => __( 'Social Media Settings', 'cookery-lite' ),
            'priority' => 35,
            'panel'    => 'general_settings',
        )
    );
    
    /** Enable Social Links */
    $wp_customize->add_setting( 
        'ed_social_links', 
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_social_links',
            array(
                'section'     => 'social_media_settings',
                'label'       => __( 'Enable Social Links', 'cookery-lite' ),
                'description' => __( 'Enable to show social links at header.', 'cookery-lite' ),
            )
        )
    );
    
    $wp_customize->add_setting( 
        new Cookery_Lite_Repeater_Setting( 
            $wp_customize, 
            'social_links', 
            array(
                'default' => '',
                'sanitize_callback' => array( 'Cookery_Lite_Repeater_Setting', 'sanitize_repeater_setting' ),
            ) 
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Control_Repeater(
            $wp_customize,
            'social_links',
            array(
                'section' => 'social_media_settings',               
                'label'   => __( 'Social Links', 'cookery-lite' ),
                'fields'  => array(
                    'font' => array(
                        'type'        => 'font',
                        'label'       => __( 'Font Awesome Icon', 'cookery-lite' ),
                        'description' => __( 'Example: fab fa-facebook-f', 'cookery-lite' ),
                    ),
                    'link' => array(
                        'type'        => 'url',
                        'label'       => __( 'Link', 'cookery-lite' ),
                        'description' => __( 'Example: https://facebook.com', 'cookery-lite' ),
                    )
                ),
                'row_label' => array(
                    'type' => 'field',
                    'value' => __( 'links', 'cookery-lite' ),
                    'field' => 'link'
                )                        
            )
        )
    );
    /** Social Media Settings Ends */

    /** SEO Settings */
    $wp_customize->add_section(
        'seo_settings',
        array(
            'title'    => __( 'SEO Settings', 'cookery-lite' ),
            'priority' => 45,
            'panel'    => 'general_settings',
        )
    );
    
    /** Enable Social Links */
    $wp_customize->add_setting( 
        'ed_post_update_date', 
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_post_update_date',
            array(
                'section'     => 'seo_settings',
                'label'       => __( 'Enable Last Update Post Date', 'cookery-lite' ),
                'description' => __( 'Enable to show last updated post date on listing as well as in single post.', 'cookery-lite' ),
            )
        )
    );
    
    /** Enable Social Links */
    $wp_customize->add_setting( 
        'ed_breadcrumb', 
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_breadcrumb',
            array(
                'section'     => 'seo_settings',
                'label'       => __( 'Enable Breadcrumb', 'cookery-lite' ),
                'description' => __( 'Enable to show breadcrumb in inner pages.', 'cookery-lite' ),
            )
        )
    );
    
    /** Breadcrumb Home Text */
    $wp_customize->add_setting(
        'home_text',
        array(
            'default'           => __( 'Home', 'cookery-lite' ),
            'sanitize_callback' => 'sanitize_text_field' 
        )
    );
    
    $wp_customize->add_control(
        'home_text',
        array(
            'type'    => 'text',
            'section' => 'seo_settings',
            'label'   => __( 'Breadcrumb Home Text', 'cookery-lite' ),
        )
    );  
    /** SEO Settings Ends */

    /** Posts(Blog) & Pages Settings */
    $wp_customize->add_section(
        'post_page_settings',
        array(
            'title'    => __( 'Posts(Blog) & Pages Settings', 'cookery-lite' ),
            'priority' => 50,
            'panel'    => 'general_settings',
        )
    );
    
    /** Prefix Archive Page */
    $wp_customize->add_setting( 
        'ed_prefix_archive', 
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_prefix_archive',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Hide Prefix in Archive Page', 'cookery-lite' ),
                'description' => __( 'Enable to hide prefix in archive page.', 'cookery-lite' ),
            )
        )
    );
        
    /** Blog Post Image Crop */
    $wp_customize->add_setting( 
        'ed_crop_blog', 
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_crop_blog',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Blog Post Image Crop', 'cookery-lite' ),
                'description' => __( 'Enable to avoid automatic cropping of featured image in home, archive and search posts.', 'cookery-lite' ),
            )
        )
    );
    
    /** Blog Excerpt */
    $wp_customize->add_setting( 
        'ed_excerpt', 
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_excerpt',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Enable Blog Excerpt', 'cookery-lite' ),
                'description' => __( 'Enable to show excerpt or disable to show full post content.', 'cookery-lite' ),
            )
        )
    );
    
    /** Excerpt Length */
    $wp_customize->add_setting( 
        'excerpt_length', 
        array(
            'default'           => 35,
            'sanitize_callback' => 'cookery_lite_sanitize_number_absint'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Slider_Control( 
            $wp_customize,
            'excerpt_length',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Excerpt Length', 'cookery-lite' ),
                'description' => __( 'Automatically generated excerpt length (in words).', 'cookery-lite' ),
                'choices'     => array(
                    'min'   => 10,
                    'max'   => 100,
                    'step'  => 5,
                )                 
            )
        )
    );

    $wp_customize->add_setting(
        'blog_main_title',
        array(
            'default'           => __( 'Latest Recipes', 'cookery-lite' ),
            'sanitize_callback' => 'sanitize_text_field', 
        )
    );
    
    $wp_customize->add_control(
        'blog_main_title',
        array(
            'label'   => __( 'Blog Title', 'cookery-lite' ),
            'section' => 'post_page_settings',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'blog_main_content',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_textarea_field', 
        )
    );
    
    $wp_customize->add_control(
        'blog_main_content',
        array(
            'label'   => __( 'Blog Description', 'cookery-lite' ),
            'section' => 'post_page_settings',
            'type'    => 'textarea',
        )
    );
    
    /** Read More Text */
    $wp_customize->add_setting(
        'read_more_text',
        array(
            'default'           => __( 'Read More', 'cookery-lite' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage' 
        )
    );
    
    $wp_customize->add_control(
        'read_more_text',
        array(
            'type'    => 'text',
            'section' => 'post_page_settings',
            'label'   => __( 'Read More Text', 'cookery-lite' ),
        )
    );
    
    $wp_customize->selective_refresh->add_partial( 'read_more_text', array(
        'selector' => '.entry-footer .btn-readmore',
        'render_callback' => 'cookery_lite_get_read_more',
    ) );

    $wp_customize->add_setting(
        'ed_post_like',
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_post_like',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Show Like Button In Blog', 'cookery-lite' ),
                'description' => __( 'Enable to show like button in blog.', 'cookery-lite' ),
            )
        )
    );
    
    /** Note */
    $wp_customize->add_setting(
        'post_note_text',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post' 
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Note_Control( 
            $wp_customize,
            'post_note_text',
            array(
                'section'     => 'post_page_settings',
                'description' => sprintf( __( '%s These options affect your individual posts.', 'cookery-lite' ), '<hr/>' ),
            )
        )
    );
    
    /** Single Post Image Crop */
    $wp_customize->add_setting( 
        'ed_crop_single', 
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_crop_single',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Single Post Image Crop', 'cookery-lite' ),
                'description' => __( 'Enable to avoid automatic cropping of featured image in single post.', 'cookery-lite' ),
            )
        )
    );

    /** Hide Author Section */
    $wp_customize->add_setting( 
        'ed_author', 
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_author',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Hide Author Section', 'cookery-lite' ),
                'description' => __( 'Enable to hide author section.', 'cookery-lite' ),
            )
        )
    );
    
    /** Show Related Posts */
    $wp_customize->add_setting( 
        'ed_related', 
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_related',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Show Related Posts', 'cookery-lite' ),
                'description' => __( 'Enable to show related posts in single page.', 'cookery-lite' ),
            )
        )
    );
    
    /** Related Posts section title */
    $wp_customize->add_setting(
        'related_post_title',
        array(
            'default'           => __( 'You may also like...', 'cookery-lite' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage' 
        )
    );
    
    $wp_customize->add_control(
        'related_post_title',
        array(
            'type'            => 'text',
            'section'         => 'post_page_settings',
            'label'           => __( 'Related Posts Section Title', 'cookery-lite' ),
            'active_callback' => 'cookery_lite_post_page_ac'
        )
    );
    
    $wp_customize->selective_refresh->add_partial( 'related_post_title', array(
        'selector' => '.related-posts .title',
        'render_callback' => 'cookery_lite_get_related_title',
    ) );
    
    $wp_customize->add_setting(
        'related_portfolio_title',
        array(
            'default'           => __( 'Related Projects', 'cookery-lite' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage' 
        )
    );
    
    $wp_customize->add_control(
        'related_portfolio_title',
        array(
            'type'            => 'text',
            'section'         => 'post_page_settings',
            'label'           => __( 'Related Portfolio Title', 'cookery-lite' ),
        )
    );

    /** Comments */
    $wp_customize->add_setting(
        'ed_comments',
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_comments',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Show Comments', 'cookery-lite' ),
                'description' => __( 'Enable to show Comments in Single Post/Page.', 'cookery-lite' ),
            )
        )
    );
    
    /** Comments Below Post Content */
    $wp_customize->add_setting(
        'toggle_comments',
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'toggle_comments',
            array(
                'section'         => 'post_page_settings',
                'label'           => __( 'Comments Below Post Content', 'cookery-lite' ),
                'description'     => __( 'Enable to show comment section right after post content. Refresh site for changes.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_post_page_ac'
            )
        )
    );
    
    /** Hide Category */
    $wp_customize->add_setting( 
        'ed_category', 
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_category',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Hide Category', 'cookery-lite' ),
                'description' => __( 'Enable to hide category.', 'cookery-lite' ),
            )
        )
    );
    
    /** Hide Post Author */
    $wp_customize->add_setting( 
        'ed_post_author', 
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_post_author',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Hide Post Author', 'cookery-lite' ),
                'description' => __( 'Enable to hide post author.', 'cookery-lite' ),
            )
        )
    );
    
    /** Hide Posted Date */
    $wp_customize->add_setting( 
        'ed_post_date', 
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_post_date',
            array(
                'section'     => 'post_page_settings',
                'label'       => __( 'Hide Posted Date', 'cookery-lite' ),
                'description' => __( 'Enable to hide posted date.', 'cookery-lite' ),
            )
        )
    );
    
    /** Show Featured Image */
    $wp_customize->add_setting( 
        'ed_featured_image', 
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_featured_image',
            array(
                'section'         => 'post_page_settings',
                'label'           => __( 'Show Featured Image', 'cookery-lite' ),
                'description'     => __( 'Enable to show featured image in post detail (single post).', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_post_page_ac'
            )
        )
    );

    /** Posts(Blog) & Pages Settings Ends */
    
    /** Newsletter Settings */
    $wp_customize->add_section(
        'newsletter_settings',
        array(
            'title'    => __( 'Newsletter Settings', 'cookery-lite' ),
            'priority' => 60,
            'panel'    => 'general_settings',
        )
    );
    
    if( cookery_lite_is_btnw_activated() ){
        
        /** Enable Newsletter Section */
        $wp_customize->add_setting( 
            'ed_newsletter', 
            array(
                'default'           => false,
                'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
            new Cookery_Lite_Toggle_Control( 
                $wp_customize,
                'ed_newsletter',
                array(
                    'section'     => 'newsletter_settings',
                    'label'       => __( 'Newsletter Section', 'cookery-lite' ),
                    'description' => __( 'Enable to show Newsletter Section', 'cookery-lite' ),
                )
            )
        );
    
        /** Newsletter Shortcode */
        $wp_customize->add_setting(
            'newsletter_shortcode',
            array(
                'default'           => '',
                'sanitize_callback' => 'wp_kses_post',
            )
        );
        
        $wp_customize->add_control(
            'newsletter_shortcode',
            array(
                'type'        => 'text',
                'section'     => 'newsletter_settings',
                'label'       => __( 'Newsletter Shortcode', 'cookery-lite' ),
                'description' => __( 'Enter the BlossomThemes Email Newsletters Shortcode. Ex. [BTEN id="356"]', 'cookery-lite' ),
            )
        ); 

        $wp_customize->add_setting( 
            'ed_bottom_newsletter', 
            array(
                'default'           => true,
                'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
            new Cookery_Lite_Toggle_Control( 
                $wp_customize,
                'ed_bottom_newsletter',
                array(
                    'section'     => 'newsletter_settings',
                    'label'       => __( 'Enable at Bottom', 'cookery-lite' ),
                    'description' => __( 'Enable to show newsletter in the bottom of single recipe.', 'cookery-lite' ),
                )
            )
        );
    } else {
        $wp_customize->add_setting(
            'newsletter_recommend',
            array(
                'sanitize_callback' => 'wp_kses_post',
            )
        );

        $wp_customize->add_control(
            new Cookery_Lite_Plugin_Recommend_Control(
                $wp_customize,
                'newsletter_recommend',
                array(
                    'section'     => 'newsletter_settings',
                    'label'       => __( 'Newsletter Shortcode', 'cookery-lite' ),
                    'capability'  => 'install_plugins',
                    'plugin_slug' => 'blossomthemes-email-newsletter',//This is the slug of recommended plugin.
                    'description' => sprintf( __( 'Please install and activate the recommended plugin %1$sBlossomThemes Email Newsletter%2$s. After that option related with this section will be visible.', 'cookery-lite' ), '<strong>', '</strong>' ),
                )
            )
        );
    }

    /** Instagram Settings */
    $wp_customize->add_section(
        'instagram_settings',
        array(
            'title'    => __( 'Instagram Settings', 'cookery-lite' ),
            'priority' => 85,
            'panel'    => 'general_settings',
        )
    );
    
    if( is_btif_activated() ){
        /** Enable Instagram Section */
        $wp_customize->add_setting( 
            'ed_instagram', 
            array(
                'default'           => false,
                'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
            new Cookery_Lite_Toggle_Control( 
                $wp_customize,
                'ed_instagram',
                array(
                    'section'     => 'instagram_settings',
                    'label'       => __( 'Instagram Section', 'cookery-lite' ),
                    'description' => __( 'Enable to show Instagram Section', 'cookery-lite' ),
                )
            )
        );
        
        /** Note */
        $wp_customize->add_setting(
            'instagram_text',
            array(
                'default'           => '',
                'sanitize_callback' => 'wp_kses_post' 
            )
        );
        
        $wp_customize->add_control(
            new Cookery_Lite_Note_Control( 
                $wp_customize,
                'instagram_text',
                array(
                    'section'     => 'instagram_settings',
                    'description' => sprintf( __( 'You can change the setting BlossomThemes Social Feed %1$sfrom here%2$s.', 'cookery-lite' ), '<a href="' . esc_url( admin_url( 'admin.php?page=class-blossomthemes-instagram-feed-admin.php' ) ) . '" target="_blank">', '</a>' )
                )
            )
        );        
    }else{
        $wp_customize->add_setting(
            'instagram_recommend',
            array(
                'sanitize_callback' => 'wp_kses_post',
            )
        );

        $wp_customize->add_control(
            new Cookery_Lite_Plugin_Recommend_Control(
                $wp_customize,
                'instagram_recommend',
                array(
                    'section'     => 'instagram_settings',
                    'capability'  => 'install_plugins',
                    'plugin_slug' => 'blossomthemes-instagram-feed',//This is the slug of recommended plugin.
                    'description' => sprintf( __( 'Please install and activate the recommended plugin %1$sBlossomThemes Social Feed%2$s. After that option related with this section will be visible.', 'cookery-lite' ), '<strong>', '</strong>' ),
                )
            )
        );
    }

    /** Miscellaneous Settings */
    $wp_customize->add_section(
        'misc_settings',
        array(
            'title'    => __( 'Misc Settings', 'cookery-lite' ),
            'priority' => 95,
            'panel'    => 'general_settings',
        )
    );
    
    /** Shop Section */
    $wp_customize->add_setting( 
        'ed_shopping_cart', 
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_shopping_cart',
            array(
                'section'     => 'shop_settings',
                'label'       => __( 'Shopping Cart', 'cookery-lite' ),
                'description' => __( 'Enable to show Shopping cart in the header.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_is_woocommerce_activated'
            )
        )
    );
    
    /** Header Search */
    $wp_customize->add_setting(
        'ed_header_search',
        array(
            'default'           => true,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_header_search',
            array(
                'section'       => 'misc_settings',
                'label'         => __( 'Header Search', 'cookery-lite' ),
                'description'   => __( 'Enable to display search form in header.', 'cookery-lite' ),
            )
        )
    );
        
    /** Shop Page Description */
    $wp_customize->add_setting( 
        'ed_shop_archive_description', 
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_shop_archive_description',
            array(
                'section'         => 'shop_settings',
                'label'           => __( 'Shop Page Description', 'cookery-lite' ),
                'description'     => __( 'Enable to show Shop Page Description.', 'cookery-lite' ),
                'active_callback' => 'cookery_lite_is_woocommerce_activated'
            )
        )
    );

    $wp_customize->add_setting( 
        'ed_portfolio_crop', 
        array(
            'default'           => false,
            'sanitize_callback' => 'cookery_lite_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Cookery_Lite_Toggle_Control( 
            $wp_customize,
            'ed_portfolio_crop',
            array(
                'section'     => 'misc_settings',
                'label'       => __( 'Portfolio Image Crop', 'cookery-lite' ),
                'description' => __( 'Enable to avoid automatic cropping of featured image in portfolio template.', 'cookery-lite' ),
            )
        )
    );
}
add_action( 'customize_register', 'cookery_lite_customize_register_general' );