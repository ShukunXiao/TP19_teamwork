<?php

namespace passster;

class PS_Public
{
    /**
     * Contains instance or null
     *
     * @var object|null
     */
    private static  $instance = null ;
    /**
     * Constructor for PS_Public
     */
    public function __construct()
    {
        add_shortcode( 'content_protector', array( $this, 'render_shortcode' ) );
        add_shortcode( 'passster', array( $this, 'render_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'add_public_scripts' ) );
        add_action( 'wp_ajax_validate_input', array( $this, 'validate_input' ) );
        add_action( 'wp_ajax_nopriv_validate_input', array( $this, 'validate_input' ) );
        add_filter( 'the_content', array( $this, 'filter_the_content' ) );
        add_filter( 'acf_the_content', array( $this, 'filter_the_content' ) );
        add_filter( 'get_the_excerpt', array( $this, 'filter_the_content' ) );
        add_filter(
            'passster_compatibility_actions',
            array( $this, 'add_compatibilities' ),
            10,
            2
        );
        add_filter( 'et_builder_load_actions', array( $this, 'add_divi_support' ) );
        add_action( 'template_redirect', array( $this, 'check_global_proctection' ) );
        // WooCommerce support.
        add_action( 'woocommerce_before_single_product_summary', array( $this, 'protect_product' ) );
        add_action( 'pre_get_posts', array( $this, 'protect_shop' ) );
        remove_action( 'woocommerce_no_products_found', 'wc_no_products_found' );
        add_action( 'woocommerce_no_products_found', array( $this, 'modify_shop_message' ) );
    }
    
    /**
     * Returns instance of PS_Public.
     *
     * @return object
     */
    public static function get_instance()
    {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Validate ajax given input.
     *
     * @return void
     */
    public function validate_input()
    {
        // check nonce.
        
        if ( !wp_verify_nonce( $_POST['nonce'], 'ps-password-nonce' ) ) {
            $response = array(
                'error' => 'Security check failed.',
            );
            print wp_json_encode( $response );
            exit;
        }
        
        // Get general options.
        $options = get_option( 'passster_general_settings' );
        $remove_spaces = apply_filters( 'passster_remove_spaces_from_list', true );
        // prepare validation.
        $type = sanitize_text_field( $_POST['type'] );
        $post_id = sanitize_text_field( $_POST['post_id'] );
        $protection = sanitize_text_field( $_POST['protection'] );
        // check protection.
        if ( !isset( $protection ) || empty($protection) ) {
            $protection = false;
        }
        // prepare content.
        $post = get_post( $post_id );
        $content = apply_filters( 'passster_compatibility_actions', $post->post_content, $post_id );
        // if it's an ACF Field.
        
        if ( !empty($_POST['acf']) ) {
            $acf = esc_html( $_POST['acf'] );
            $content = \get_field( $acf, $post_id );
        }
        
        // Check if redirection.
        $redirect = '';
        if ( !empty($_POST['redirect']) ) {
            $redirect = esc_url( $_POST['redirect'] );
        }
        // Default response.
        $response = array(
            'error'   => get_theme_mod( 'passster_form_error_text', __( 'Invalid password.', 'content-protector' ) ),
            'content' => '',
        );
        switch ( $type ) {
            case 'password':
                // Check if input exists.
                
                if ( empty($_POST['input']) ) {
                    print wp_json_encode( $response );
                    exit;
                }
                
                $input = sanitize_text_field( $_POST['input'] );
                // Check protection type.
                switch ( $protection ) {
                    case 'full':
                        $password = get_post_meta( $post_id, 'passster_password', true );
                        
                        if ( !empty($password) && $input === $password ) {
                            $response = array(
                                'success' => true,
                                'content' => $content,
                            );
                            do_action( 'passster_validation_success', $input );
                        }
                        
                        break;
                    case 'area':
                        $area_id = esc_html( $_POST['area'] );
                        $password = get_post_meta( $area_id, 'passster_password', true );
                        
                        if ( !empty($area_id) && !empty($password) && $input === $password ) {
                            $area = get_post( $area_id );
                            $content = apply_filters( 'passster_compatibility_actions', $area->post_content, $area_id );
                            $response = array(
                                'success' => true,
                                'content' => $content,
                            );
                            do_action( 'passster_validation_success', $input );
                        }
                        
                        break;
                    default:
                        $content = apply_filters( 'passster_compatibility_actions', PS_Helper::get_shortcode_content( $content, $input ) );
                        
                        if ( !empty($content) ) {
                            $response = array(
                                'success' => true,
                                'content' => $content,
                            );
                            do_action( 'passster_validation_success', $input );
                        } elseif ( 'on' === $options['toggle_ajax'] ) {
                            $response = array(
                                'success' => true,
                            );
                            do_action( 'passster_validation_success', $input );
                        }
                
                }
                print wp_json_encode( $response );
                exit;
                break;
            case 'captcha':
                $captcha = sanitize_text_field( $_POST['captcha'] );
                
                if ( isset( $captcha ) && !empty($captcha) ) {
                    if ( 'full' !== $protection ) {
                        
                        if ( 'area' === $protection ) {
                            $area_id = esc_html( $_POST['area'] );
                            
                            if ( isset( $area_id ) && !empty($area_id) ) {
                                $area = get_post( $area_id );
                                $content = apply_filters( 'passster_compatibility_actions', $area->post_content, $area_id );
                            }
                        
                        } else {
                            $content = apply_filters( 'passster_compatibility_actions', PS_Helper::get_shortcode_content( $content, 'captcha' ) );
                        }
                    
                    }
                    $response = array(
                        'success' => true,
                        'content' => $content,
                    );
                    print wp_json_encode( $response );
                    exit;
                }
                
                break;
        }
        print wp_json_encode( $response );
        exit;
    }
    
    /**
     * Render the passster shortcode
     *
     * @param  array  $atts array of attributes.
     * @param  string $content the current content.
     * @return string
     */
    public function render_shortcode( $atts, $content = null )
    {
        // check if valid before restrict anything.
        $valid = PS_Conditional::is_valid( $atts );
        if ( $valid ) {
            
            if ( isset( $atts['area'] ) && !empty($atts['area']) ) {
                $area_id = esc_html( $atts['area'] );
                $area = get_post( $area_id );
                $content = apply_filters( 'passster_compatibility_actions', $area->post_content, $area_id );
                return apply_filters( 'the_content', str_replace( '{post-id}', get_the_id(), $content ) );
            } else {
                $content = apply_filters( 'the_content', $content );
                return apply_filters( 'passster_content', $content );
            }
        
        }
        // do nothing if no atts.
        if ( !isset( $atts ) || empty($atts) ) {
            return;
        }
        // Password.
        
        if ( isset( $atts['password'] ) ) {
            $form = PS_Form::get_password_form();
            $form = str_replace( '[PASSSTER_TYPE]', 'password', $form );
        }
        
        // Captcha.
        if ( isset( $atts['captcha'] ) ) {
            $form = PS_Form::get_captcha_form();
        }
        // Area.
        if ( isset( $atts['area'] ) ) {
            $form = str_replace( '[PASSSTER_AREA]', $atts['area'], $form );
        }
        // Page.
        
        if ( isset( $atts['protection'] ) ) {
            $form = str_replace( '[PASSSTER_PROTECTION]', 'full', $form );
        } else {
            if ( isset( $atts['area'] ) ) {
                $form = str_replace( '[PASSSTER_PROTECTION]', 'area', $form );
            }
        }
        
        // Redirect.
        
        if ( isset( $atts['redirect'] ) ) {
            $form = str_replace( '[PASSSTER_REDIRECT]', $atts['redirect'], $form );
        } else {
            $form = str_replace( '[PASSSTER_REDIRECT]', '', $form );
        }
        
        // headline.
        
        if ( isset( $atts['headline'] ) ) {
            $form = str_replace( '[PASSSTER_FORM_HEADLINE]', esc_html( $atts['headline'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_FORM_HEADLINE]', get_theme_mod( 'passster_form_instructions_headline', __( 'Protected Area', 'content-protector' ) ), $form );
        }
        
        // instruction.
        
        if ( isset( $atts['instruction'] ) ) {
            $form = str_replace( '[PASSSTER_FORM_INSTRUCTIONS]', esc_html( $atts['instruction'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_FORM_INSTRUCTIONS]', get_theme_mod( 'passster_form_instructions_text', __( 'This content is password-protected. Please verify with a password to unlock the content.', 'content-protector' ) ), $form );
        }
        
        // placeholder.
        
        if ( isset( $atts['placeholder'] ) ) {
            $form = str_replace( '[PASSSTER_PLACEHOLDER]', esc_html( $atts['placeholder'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_PLACEHOLDER]', get_theme_mod( 'passster_form_instructions_placeholder', __( 'Enter your password..', 'content-protector' ) ), $form );
        }
        
        // button.
        
        if ( isset( $atts['button'] ) ) {
            $form = str_replace( '[PASSSTER_BUTTON_LABEL]', esc_html( $atts['button'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_BUTTON_LABEL]', get_theme_mod( 'passster_form_button_label', __( 'Submit', 'content-protector' ) ), $form );
        }
        
        // modify id.
        
        if ( isset( $atts['id'] ) ) {
            $form = str_replace( '[PASSSTER_ID]', 'ps-' . esc_html( $atts['id'] ), $form );
        } else {
            $form = str_replace( '[PASSSTER_ID]', 'ps-' . wp_rand( 10, 1000 ), $form );
        }
        
        // hide or not.
        
        if ( isset( $atts['hide'] ) && true == $atts['hide'] ) {
            $form = str_replace( '[PASSSTER_HIDE]', ' passster-hide', $form );
        } else {
            $form = str_replace( '[PASSSTER_HIDE]', '', $form );
        }
        
        // ACF field.
        
        if ( isset( $atts['acf'] ) && true == $atts['acf'] ) {
            $form = str_replace( '[PASSSTER_ACF]', ' data-acf="' . esc_html( $atts['acf'] ) . '"', $form );
        } else {
            $form = str_replace( '[PASSSTER_ACF]', '', $form );
        }
        
        // set AMP header.
        if ( isset( $atts['amp'] ) ) {
            PS_Helper::set_amp_headers( $atts['amp'], esc_html( $atts['password'] ) );
        }
        return $form;
    }
    
    /**
     * Filters the_content with Passster.
     *
     * @param string $content given content.
     * @return string
     */
    public function filter_the_content( $content )
    {
        $post_id = get_the_id();
        $activate_protection = get_post_meta( $post_id, 'passster_activate_protection', true );
        // user restriction.
        $user_restriction_type = get_post_meta( $post_id, 'passster_user_restriction_type', true );
        $user_restriction = get_post_meta( $post_id, 'passster_user_restriction', true );
        // Redirection.
        $redirection = get_post_meta( $post_id, 'passster_redirect_url', true );
        // texts.
        $headline = get_post_meta( $post_id, 'passster_headline', true );
        $instruction = get_post_meta( $post_id, 'passster_instruction', true );
        $placeholder = get_post_meta( $post_id, 'passster_placeholder', true );
        $button = get_post_meta( $post_id, 'passster_button', true );
        $id = get_post_meta( $post_id, 'passster_id', true );
        if ( !$activate_protection ) {
            return $content;
        }
        // build atts array to validate.
        $atts = array();
        $protection_type = get_post_meta( $post_id, 'passster_protection_type', true );
        switch ( $protection_type ) {
            case 'password':
                $password = get_post_meta( $post_id, 'passster_password', true );
                $atts['password'] = $password;
                $shortcode = '[passster password="' . $password . '" protection="full" ';
                break;
            case 'captcha':
                $atts['captcha'] = true;
                $shortcode = '[passster captcha="true" protection="full" ';
                break;
        }
        $shortcode .= ']{content}[/passster]';
        // check if valid before restrict anything.
        $valid = PS_Conditional::is_valid( $atts );
        if ( $valid ) {
            return $content;
        }
        // replace placeholder with content.
        $shortcode = str_replace( '{content}', $content, $shortcode );
        return do_shortcode( $shortcode );
    }
    
    /**
     * Enqueue scripts for shortcode
     *
     * @return void
     */
    public function add_public_scripts()
    {
        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        $advanced_options = wp_parse_args( get_option( 'passster_advanced_settings' ), PS_ADMIN::get_defaults( 'passster_advanced_settings' ) );
        $general_options = wp_parse_args( get_option( 'passster_general_settings' ), PS_ADMIN::get_defaults( 'passster_general_settings' ) );
        wp_enqueue_style(
            'passster-public',
            PASSSTER_URL . '/assets/public/passster-public' . $suffix . '.css',
            '3.5.5',
            'all'
        );
        wp_enqueue_script(
            'passster-cookie',
            PASSSTER_URL . '/assets/public/cookie.js',
            array( 'jquery' ),
            '3.5.5',
            false
        );
        wp_enqueue_script(
            'passster-captcha',
            PASSSTER_URL . '/assets/public/captcha.js',
            array(),
            '3.5.5',
            false
        );
        wp_enqueue_script(
            'passster-public',
            PASSSTER_URL . '/assets/public/passster-public' . $suffix . '.js',
            array( 'jquery', 'passster-cookie', 'passster-captcha' ),
            '3.5.5',
            false
        );
        // pre-render shortcodes if options set.
        $shortcodes = array();
        
        if ( isset( $general_options['third_party_shortcodes'] ) && !empty($general_options['third_party_shortcodes']) ) {
            $shortcodes_in_options = explode( ',', $general_options['third_party_shortcodes'] );
            if ( is_array( $shortcodes_in_options ) ) {
                foreach ( $shortcodes_in_options as $shortcode ) {
                    $shortcodes[$shortcode] = do_shortcode( str_replace( '{post-id}', get_the_id(), $shortcode ) );
                }
            }
        }
        
        wp_localize_script( 'passster-public', 'ps_ajax', array(
            'ajax_url'           => admin_url() . 'admin-ajax.php',
            'days'               => $general_options['passster_cookie_duration'],
            'use_cookie'         => $general_options['toggle_cookie'],
            'no_ajax'            => $general_options['toggle_ajax'],
            'nonce'              => wp_create_nonce( 'ps-password-nonce' ),
            'post_id'            => get_the_id(),
            'captcha_error'      => get_theme_mod( 'passster_form_error_text', __( 'Sorry, your captcha solution was wrong.', 'content-protector' ) ),
            'recaptcha_key'      => '',
            'shortcodes'         => $shortcodes,
            'disable_encryption' => apply_filters( 'passster_disable_base_encryption', false ),
        ) );
        // if amp used.
        if ( isset( $general_options['toggle_amp'] ) && 'on' == $general_options['toggle_amp'] ) {
            wp_enqueue_script(
                'passster-amp',
                'https://cdn.ampproject.org/v0/amp-form-0.1.js',
                array( 'jquery' ),
                '3.2',
                false
            );
        }
        // if password type hint used.
        $password_typing = get_theme_mod( 'passster_form_instructions_password_typing' );
        if ( isset( $password_typing ) && true === $password_typing ) {
            wp_enqueue_script(
                'password-typing',
                PASSSTER_URL . '/assets/public/password-typing.js',
                array( 'jquery' ),
                '3.2',
                false
            );
        }
    }
    
    /**
     * Adding compatibility modifications before ajax output.
     *
     * @param  string $content current content.
     * @return string
     */
    public function add_compatibilities( $content, $post_id = null )
    {
        if ( is_null( $post_id ) ) {
            $post_id = get_the_id();
        }
        // use meta field instead.
        $use_meta = apply_filters( 'passster_use_meta_field', false );
        
        if ( $use_meta ) {
            $meta_key = apply_filters( 'passster_meta_key', 'field_name' );
            $content = get_post_meta( $post_id, $meta_key, true );
        }
        
        // use meta field instead.
        $use_acf = apply_filters( 'passster_use_acf_field', false );
        
        if ( $use_acf ) {
            $meta_key = apply_filters( 'passster_acf_key', 'field_name' );
            $content = get_field( $meta_key, $post_id );
        }
        
        // Tablepress.
        
        if ( class_exists( 'TablePress' ) ) {
            \TablePress::$controller = \TablePress::load_controller( 'frontend' );
            \TablePress::$controller->init_shortcodes();
        }
        
        // prepare for Visual Composer.
        if ( class_exists( 'WPBMap' ) ) {
            \WPBMap::addAllMappedShortcodes();
        }
        // prepare for Elementor.
        if ( class_exists( '\\Elementor\\Plugin' ) ) {
            if ( \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id ) ) {
                $content = \Elementor\Plugin::$instance->frontend->get_builder_content( $post_id, true );
            }
        }
        // Skip shortcodes.
        remove_filter( 'the_content', 'do_shortcode', 11 );
        $content = apply_filters( 'the_content', $content );
        add_filter( 'the_content', 'do_shortcode', 11 );
        return $content;
    }
    
    /**
     * Add ajax support for Divi builder.
     *
     * @param array $actions array of allowed actions.
     * @return array
     */
    public function add_divi_support( $actions )
    {
        $actions[] = 'validate_input';
        return $actions;
    }
    
    /**
     * Redirect if global protection is activated and no password is set.
     *
     * @return void
     */
    public function check_global_proctection()
    {
        $post_id = get_option( 'passster_global_id' );
        $atts = array();
        // Build $atts array based on protection settings.
        $protection_type = get_post_meta( $post_id, 'passster_protection_type', true );
        switch ( $protection_type ) {
            case 'password':
                $password = get_post_meta( $post_id, 'passster_password', true );
                $atts['password'] = $password;
                break;
            case 'captcha':
                $atts['captcha'] = true;
                break;
        }
        
        if ( !empty($post_id) ) {
            $global_protection_active = get_post_meta( $post_id, 'passster_activate_global_protection', true );
            
            if ( true == $global_protection_active ) {
                if ( is_page( $post_id ) || is_single( $post_id ) ) {
                    return;
                }
                // Check if cookie is set.
                
                if ( empty($_COOKIE['passster']) || !PS_Conditional::is_valid( $atts ) ) {
                    $global_protection_url = get_permalink( $post_id );
                    wp_redirect( esc_url_raw( $global_protection_url ) );
                    exit;
                }
            
            }
        
        }
    
    }
    
    /**
     * Protect a WooCommerce product.
     *
     * @return void
     */
    public function protect_product()
    {
        $post_id = get_the_id();
        $activate_protection = get_post_meta( $post_id, 'passster_activate_protection', true );
        // Redirection.
        $redirection = get_post_meta( $post_id, 'passster_redirect_url', true );
        // user restriction.
        $user_restriction_type = get_post_meta( $post_id, 'passster_user_restriction_type', true );
        $user_restriction = get_post_meta( $post_id, 'passster_user_restriction', true );
        // texts.
        $headline = get_post_meta( $post_id, 'passster_headline', true );
        $instruction = get_post_meta( $post_id, 'passster_instruction', true );
        $placeholder = get_post_meta( $post_id, 'passster_placeholder', true );
        $button = get_post_meta( $post_id, 'passster_button', true );
        $id = get_post_meta( $post_id, 'passster_id', true );
        if ( !$activate_protection ) {
            return;
        }
        // build atts array to validate.
        $atts = array();
        $protection_type = get_post_meta( $post_id, 'passster_protection_type', true );
        switch ( $protection_type ) {
            case 'password':
                $password = get_post_meta( $post_id, 'passster_password', true );
                $atts['password'] = $password;
                $shortcode = '[passster password="' . $password . '" protection="full" ';
                break;
            case 'captcha':
                $atts['captcha'] = true;
                $shortcode = '[passster captcha="true" protection="full" ';
                break;
        }
        $shortcode .= ']{content}[/passster]';
        // check if valid before restrict anything.
        $valid = PS_Conditional::is_valid( $atts );
        
        if ( !$valid ) {
            // replace placeholder with content.
            $shortcode = str_replace( '{content}', $content, $shortcode );
            echo  do_shortcode( $shortcode ) ;
            add_filter( 'woocommerce_is_purchasable', '__return_false' );
            // unhook allwoocommerce_before_single_product_summary.
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
            // unhook woocommerce_single_product_summary.
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
            // unhook woocommerce_after_single_product_summary.
            remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
            remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
            remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
            // Unhook German Market strings.
            add_filter(
                'wgm_product_summary_parts_after',
                function ( $output_parts, $product, $hook ) {
                unset( $output_parts['shipping'] );
                unset( $output_parts['tax'] );
                unset( $output_parts['ppu'] );
                return $output_parts;
            },
                50,
                3
            );
            // Unhook Germanized strings.
            add_filter( 'pre_option_woocommerce_gzd_display_product_detail_tax_info', function () {
                return 'no';
            } );
            add_filter( 'pre_option_woocommerce_gzd_display_single_product_unit_price', function () {
                return 'no';
            } );
            add_filter( 'pre_option_woocommerce_gzd_display_product_detail_shipping_costs_info', function () {
                return 'no';
            } );
            add_filter( 'pre_option_woocommerce_gzd_display_single_product_delivery_time', function () {
                return 'no';
            } );
            add_filter( 'pre_option_woocommerce_gzd_display_single_product_units', function () {
                return 'no';
            } );
        }
    
    }
    
    /**
     * Protect a WooCommerce product.
     *
     * @return void
     */
    public function protect_shop( $q )
    {
        if ( !$q->is_main_query() ) {
            return;
        }
        if ( !$q->is_post_type_archive() ) {
            return;
        }
        $post_id = get_option( 'woocommerce_shop_page_id' );
        $activate_protection = get_post_meta( $post_id, 'passster_activate_protection', true );
        // user restriction.
        $user_restriction_type = get_post_meta( $post_id, 'passster_user_restriction_type', true );
        $user_restriction = get_post_meta( $post_id, 'passster_user_restriction', true );
        // texts.
        $headline = get_post_meta( $post_id, 'passster_headline', true );
        $instruction = get_post_meta( $post_id, 'passster_instruction', true );
        $placeholder = get_post_meta( $post_id, 'passster_placeholder', true );
        $button = get_post_meta( $post_id, 'passster_button', true );
        $id = get_post_meta( $post_id, 'passster_id', true );
        if ( !$activate_protection ) {
            return;
        }
        // build atts array to validate.
        $atts = array();
        $protection_type = get_post_meta( $post_id, 'passster_protection_type', true );
        switch ( $protection_type ) {
            case 'password':
                $password = get_post_meta( $post_id, 'passster_password', true );
                $atts['password'] = $password;
                break;
            case 'captcha':
                $atts['captcha'] = true;
                break;
        }
        // check if valid before restrict anything.
        $valid = PS_Conditional::is_valid( $atts );
        if ( !$valid ) {
            if ( !is_admin() && is_shop() ) {
                $q->set( 'post__in', array( 0 ) );
            }
        }
        return $q;
    }
    
    /**
     * Protect a WooCommerce product.
     *
     * @return void
     */
    public function modify_shop_message()
    {
        if ( !is_shop() ) {
            return;
        }
        $post_id = get_option( 'woocommerce_shop_page_id' );
        $activate_protection = get_post_meta( $post_id, 'passster_activate_protection', true );
        // user restriction.
        $user_restriction_type = get_post_meta( $post_id, 'passster_user_restriction_type', true );
        $user_restriction = get_post_meta( $post_id, 'passster_user_restriction', true );
        // Redirection.
        $redirection = get_post_meta( $post_id, 'passster_redirect_url', true );
        // texts.
        $headline = get_post_meta( $post_id, 'passster_headline', true );
        $instruction = get_post_meta( $post_id, 'passster_instruction', true );
        $placeholder = get_post_meta( $post_id, 'passster_placeholder', true );
        $button = get_post_meta( $post_id, 'passster_button', true );
        $id = get_post_meta( $post_id, 'passster_id', true );
        if ( !$activate_protection ) {
            return;
        }
        // build atts array to validate.
        $atts = array();
        $protection_type = get_post_meta( $post_id, 'passster_protection_type', true );
        switch ( $protection_type ) {
            case 'password':
                $password = get_post_meta( $post_id, 'passster_password', true );
                $atts['password'] = $password;
                $shortcode = '[passster password="' . $password . '" ';
                break;
            case 'captcha':
                $atts['captcha'] = true;
                $shortcode = '[passster captcha="true" ';
                break;
        }
        $shortcode .= '][/passster]';
        // check if valid before restrict anything.
        $valid = PS_Conditional::is_valid( $atts );
        if ( !$valid ) {
            echo  do_shortcode( $shortcode ) ;
        }
    }

}