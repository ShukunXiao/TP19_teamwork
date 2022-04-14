<?php

namespace passster;

class PS_Admin
{
    /**
     * Contains instance or null
     *
     * @var object|null
     */
    private static  $instance = null ;
    /**
     * Returns instance of PS_Admin.
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
     * Setup the passster admin area
     *
     * @return void
     */
    public function __construct()
    {
        // Check permissions before include settings.
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }
        add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
        add_action( 'init', array( $this, 'register_password_areas' ) );
        add_filter( 'manage_protected_areas_posts_columns', array( $this, 'set_area_columns' ) );
        add_action(
            'manage_protected_areas_posts_custom_column',
            array( $this, 'set_area_columns_content' ),
            10,
            2
        );
        $settings = new PS_Settings();
        $settings->add_section( array(
            'id'    => 'passster_general_settings',
            'title' => __( 'Options', 'content-protector' ),
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'   => 'passster_advanced_cookie_title',
            'type' => 'title',
            'name' => '<h3>' . __( 'Cookie', 'content-protector' ) . '</h3>',
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'      => 'toggle_cookie',
            'type'    => 'toggle',
            'default' => 'on',
            'name'    => __( 'Use Cookie', 'content-protector' ),
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'      => 'passster_cookie_duration',
            'type'    => 'number',
            'name'    => __( 'Cookie Duration', 'content-protector' ),
            'desc'    => __( 'Duration (in days) for your cookie. Once a cookie expires, the user will have to enter the password again.', 'content-protector' ),
            'default' => '2',
            'min'     => 1,
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'   => 'passster_advanced_compatibility_mode',
            'type' => 'title',
            'name' => '<h3>' . __( 'Compatibility Mode', 'content-protector' ) . '</h3>',
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'      => 'toggle_ajax',
            'type'    => 'toggle',
            'default' => 'off',
            'name'    => __( 'Reload after successful validation', 'content-protector' ),
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'   => 'passster_advanced_amp_title',
            'type' => 'title',
            'name' => '<h3>' . __( 'AMP', 'content-protector' ) . '</h3>',
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'      => 'toggle_amp',
            'type'    => 'toggle',
            'default' => 'off',
            'name'    => __( 'Activate AMP Support', 'content-protector' ),
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'   => 'passster_advanced_third_party_title',
            'type' => 'title',
            'name' => '<h3>' . __( 'Third-Party Support', 'content-protector' ) . '</h3>',
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'   => 'third_party_shortcodes',
            'type' => 'textarea',
            'name' => __( 'Third-Party Shortcodes', 'content-protector' ),
            'desc' => __( 'Add a comma separated list of shortcodes you want to use inside of Passster. <br>You can also use <b>{post-id}</b> to add the ID of the current page/post dynamically. ', 'content-protector' ),
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'   => 'passster_advanced_delete_title',
            'type' => 'title',
            'name' => '<h3>' . __( 'Uninstall', 'content-protector' ) . '</h3>',
        ) );
        $settings->add_field( 'passster_general_settings', array(
            'id'   => 'passster_advanced_delete_options',
            'type' => 'checkbox',
            'name' => __( 'Delete Plugin Options On Uninstall', 'content-protector' ),
            'desc' => __( 'If checked, all plugin options will be deleted if the plugin is unstalled.', 'content-protector' ),
        ) );
        $settings->add_section( array(
            'id'    => 'passster_advanced_settings',
            'title' => __( 'External Services', 'content-protector' ),
        ) );
        $settings->add_field( 'passster_advanced_settings', array(
            'id'   => 'passster_recaptcha_title',
            'type' => 'title',
            'name' => '<h3>' . __( 'Google Recaptcha', 'content-protector' ) . '</h3>',
        ) );
        $settings->add_field( 'passster_advanced_settings', array(
            'id'      => 'passster_recaptcha_type',
            'type'    => 'select',
            'name'    => __( 'Recaptcha Version', 'content-protector' ),
            'options' => array(
            'v3' => 'V3 (invisible Captcha)',
            'v2' => 'V2 (Checkbox)',
        ),
        ) );
        $settings->add_field( 'passster_advanced_settings', array(
            'id'      => 'passster_recaptcha_site_key',
            'type'    => 'text',
            'name'    => __( 'Site Key', 'content-protector' ),
            'desc'    => __( 'Add your Google ReCaptcha Site Key', 'content-protector' ),
            'premium' => 'premium',
        ) );
        $settings->add_field( 'passster_advanced_settings', array(
            'id'      => 'passster_recaptcha_secret',
            'type'    => 'text',
            'name'    => __( 'Secret', 'content-protector' ),
            'desc'    => __( 'Add your Google ReCaptcha Secret', 'content-protector' ),
            'premium' => 'premium',
        ) );
        $settings->add_field( 'passster_advanced_settings', array(
            'id'      => 'passster_recaptcha_language',
            'type'    => 'text',
            'name'    => __( 'Language', 'content-protector' ),
            'desc'    => __( 'Add your language shortcode. For example "en" for english or "de" for german. ', 'content-protector' ),
            'default' => 'en',
            'premium' => 'premium',
        ) );
        $settings->add_field( 'passster_advanced_settings', array(
            'id'   => 'passster_bitly_title',
            'type' => 'title',
            'name' => '<h3>' . __( 'Bitly', 'content-protector' ) . '</h3>',
        ) );
        $settings->add_field( 'passster_advanced_settings', array(
            'id'      => 'passster_bitly_access_key',
            'type'    => 'text',
            'name'    => __( 'Bitly Access Token', 'content-protector' ),
            'desc'    => __( 'Add your bitly access token. You can get one here: <a target="_blank" href="https://bitly.com/">bitly.com</a>', 'content-protector' ),
            'premium' => 'premium',
        ) );
    }
    
    /**
     * Add admin assets
     *
     * @return void
     */
    public function add_admin_scripts()
    {
        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        wp_enqueue_style(
            'passster-admin-css',
            PASSSTER_URL . '/assets/admin/passster-admin.css',
            '1.0',
            'all'
        );
        wp_enqueue_script(
            'passster-admin-js',
            PASSSTER_URL . '/assets/admin/passster-admin' . $suffix . '.js',
            array( 'jquery' ),
            '1.0',
            false
        );
        wp_localize_script( 'passster-admin-js', 'ps_admin_ajax', array(
            'ajax_url'      => admin_url( 'admin-ajax.php' ),
            'nonce'         => wp_create_nonce( 'passster-admin-nonce' ),
            'reset_message' => __( 'Usage data resetted.', 'content-protector' ),
        ) );
    }
    
    /**
     * Register post type "password lists"
     *
     * @return void
     */
    public function register_password_lists()
    {
    }
    
    /**
     * Register post type "protected areas"
     *
     * @return void
     */
    public function register_password_areas()
    {
        $labels = array(
            'name'               => _x( 'Protected Areas', 'post type general name', 'content-protector' ),
            'singular_name'      => _x( 'Protected Area', 'post type singular name', 'content-protector' ),
            'menu_name'          => _x( 'Protected Areas', 'admin menu', 'content-protector' ),
            'name_admin_bar'     => _x( 'Protected Area', 'add new on admin bar', 'content-protector' ),
            'add_new'            => _x( 'Add New', 'content-protector' ),
            'add_new_item'       => __( 'Add New Protected Area', 'content-protector' ),
            'new_item'           => __( 'New Protected Area', 'content-protector' ),
            'edit_item'          => __( 'Edit Protected Area', 'content-protector' ),
            'view_item'          => __( 'View Protected Area', 'content-protector' ),
            'all_items'          => __( 'Protected Areas', 'content-protector' ),
            'search_items'       => __( 'Search Protected Areas', 'content-protector' ),
            'parent_item_colon'  => __( 'Parent Protected Area', 'content-protector' ),
            'not_found'          => __( 'No Protected Areas found.', 'content-protector' ),
            'not_found_in_trash' => __( 'No Protected Areas found in Trash.', 'content-protector' ),
        );
        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Manageable protected areas', 'content-protector' ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => 'passster',
            'query_var'          => true,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor' ),
            'show_in_rest'       => true,
        );
        
        if ( current_user_can( 'administrator' ) ) {
            $args['public'] = true;
            $args['publicly_queryable'] = true;
        }
        
        register_post_type( 'protected_areas', $args );
    }
    
    /**
     * Set column headers password lists post type
     *
     * @param  array $columns array of columns.
     * @return array
     */
    public function set_columns( $columns )
    {
    }
    
    /**
     * Add content to registered columns for password list post type.
     *
     * @param  string $column name of the column.
     * @param  int    $post_id current id.
     * @return void
     */
    public function set_columns_content( $column, $post_id )
    {
    }
    
    /**
     * Set column headers password lists post type
     *
     * @param  array $columns array of columns.
     * @return array
     */
    public function set_area_columns( $columns )
    {
        $columns['shortcode'] = __( 'Shortcode', 'content-protector' );
        unset( $columns['date'] );
        return $columns;
    }
    
    /**
     * Add content to registered columns for password list post type.
     *
     * @param  string $column name of the column.
     * @param  int    $post_id current id.
     * @return void
     */
    public function set_area_columns_content( $column, $post_id )
    {
        switch ( $column ) {
            case 'shortcode':
                $shortcode = get_post_meta( $post_id, 'passster_area_shortcode', true );
                echo  esc_html( $shortcode ) ;
                break;
        }
    }
    
    /**
     * Return default based on option name.
     *
     * @param string $option_name name of the option.
     * @return array
     */
    public static function get_defaults( $option_name )
    {
        switch ( $option_name ) {
            case 'passster_general_settings':
                $settings = array(
                    'toggle_cookie'                    => 'on',
                    'toggle_ajax'                      => 'off',
                    'passster_cookie_duration'         => 2,
                    'toggle_amp'                       => 'off',
                    'passster_advanced_delete_options' => 'off',
                );
                return $settings;
                break;
            case 'passster_advanced_settings':
                $settings = array(
                    'passster_recaptcha_site_key' => '',
                    'passster_recaptcha_type'     => 'v3',
                    'passster_recaptcha_secret'   => '',
                    'passster_recaptcha_language' => '',
                    'passster_bitly_access_key'   => '',
                );
                return $settings;
                break;
        }
    }
    
    /**
     * Decrease remaining downloads in post meta.
     */
    public function reset_usage_data()
    {
    }
    
    /**
     * Add Gutenberg support for editing areas.
     *
     * @param  bool   $can_edit bool indicates if editable or not.
     * @param  string $post_type given post type.
     * @return bool
     */
    public function activate_areas_block_editor( $can_edit, $post_type )
    {
        if ( 'protected_areas' == $post_type ) {
            $can_edit = true;
        }
        return $can_edit;
    }

}