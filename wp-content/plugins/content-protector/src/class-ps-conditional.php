<?php

namespace passster;

class PS_Conditional
{
    /**
     * Check if valid authentication exists.
     *
     * @param  array $atts array of attributes.
     * @return boolean
     */
    public static function is_valid( $atts )
    {
        // valid user.
        if ( self::is_user_valid( $atts ) ) {
            return true;
        }
        // valid link.
        if ( self::is_link_valid( $atts ) ) {
            return true;
        }
        // is Cookie set?
        if ( !isset( $_COOKIE['passster'] ) || empty($_COOKIE['passster']) ) {
            return false;
        }
        $input = base64_decode( $_COOKIE['passster'] );
        // Deactivate encryption?
        $base_encryption = apply_filters( 'passster_disable_base_encryption', false );
        if ( $base_encryption ) {
            $input = $_COOKIE['passster'];
        }
        // password.
        if ( isset( $atts['password'] ) ) {
            if ( $input == $atts['password'] ) {
                return true;
            }
        }
        // passwords.
        
        if ( isset( $atts['passwords'] ) ) {
            $passwords = explode( ',', str_replace( ' ', '', $atts['passwords'] ) );
            if ( !empty($passwords) && in_array( $input, $passwords, true ) ) {
                return true;
            }
        }
        
        // password lists.
        
        if ( isset( $atts['password_list'] ) ) {
            $passwords_in_list = get_post_meta( $atts['password_list'], 'passster_passwords', true );
            $passwords_in_list = explode( ',', $passwords_in_list );
            $expire = get_post_meta( $atts['password_list'], 'passster_expire_passwords', true );
            
            if ( in_array( $input, $passwords_in_list ) ) {
                self::maybe_expire_password_from_list( $input, $passwords_in_list, $atts['password_list'] );
                return true;
            }
        
        }
        
        // captcha.
        if ( isset( $atts['captcha'] ) ) {
            if ( 'captcha' == $input ) {
                return true;
            }
        }
        // recaptcha.
        if ( isset( $atts['recaptcha'] ) ) {
            if ( 'recaptcha' == $input ) {
                return true;
            }
        }
        // if nothing was correct.
        return false;
    }
    
    /**
     * Return user name and role as array
     *
     * @param array $atts added attributes.
     * @return boolean
     */
    public static function is_user_valid( $atts )
    {
    }
    
    /**
     * Check if link is valid.
     *
     * @param  array $atts array of attributes.
     * @return boolean
     */
    public static function is_link_valid( $atts )
    {
    }
    
    /**
     * Maybe expire an password from a list by usage or time.
     *
     * @param string $password given password.
     * @param array  $passwords_in_list passwords from list.
     * @return void
     */
    public static function maybe_expire_password_from_list( $password, $passwords_in_list, $list_id )
    {
    }

}