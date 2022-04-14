<?php
/**
 * Cookery Lite Fonts
 *
 * Used for compiling the standard and Google fonts, along with all
 * the variants (weights).
 *
 * @package Cookery_Lite
 */

if ( class_exists( 'Cookery_Lite_Fonts' ) ) {
	return;
}

final class Cookery_Lite_Fonts {

	/**
	 * One instance of Cookery_Lite_Fonts
	 *
	 * @var Cookery_Lite_Fonts
	 */
	private static $instance = null;

	/**
	 * Array of all Google Fonts
	 *
	 * @var array|null
	 */
	public static $google_fonts = null;

	/**
	 * Key used in transient name
	 *
	 * @var string
	 * @access public
	 */
	public static $transient_key = '';

	/**
	 * Time in seconds to cache the results for
	 *
	 * @var int
	 * @access public
	 */
	public static $cache_time = 0;

	/**
	 * Whether or not to cache the Google response.
	 * Only set this to true if debugging.
	 *
	 * @var bool
	 * @access public
	 */
	public static $cache = true;

	/**
	 * Cookery_Lite_Fonts constructor.
	 */
	private function __construct() {
	}

	/**
	 * Get the one, true instance of this class.
	 * Prevents performance issues since this is only loaded once.
	 *
	 * @return object Cookery_Lite_Fonts
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Compile font options from different sources.
	 *
	 * @return array All available fonts.
	 */
	public static function get_all_fonts() {
		$standard_fonts = self::get_standard_fonts();
		$google_fonts   = self::get_google_fonts();

		return apply_filters( 'cookery_lite_all_fonts', array_merge( $standard_fonts, $google_fonts ) );
	}

	/**
	 * Return an array of standard websafe fonts.
	 *
	 * @return array Standard websafe fonts.
	 */
	public static function get_standard_fonts() {
		return apply_filters( 'cookery_lite_fonts_standard_fonts', array(
    		'georgia-serif' => array(
    			'label' => _x( 'Georgia', 'font style', 'cookery-lite' ),
    			'stack' => 'georgia-serif',
    		),
            'palatino-serif' => array(
    			'label' => _x( 'Palatino Linotype, Book Antiqua, Palatino', 'font style', 'cookery-lite' ),
    			'stack' => 'palatino-serif',
    		),
            'times-serif' => array(
    			'label' => _x( 'Times New Roman, Times', 'font style', 'cookery-lite' ),
    			'stack' => 'times-serif',
    		),
            'arial-helvetica' => array(
    			'label' => _x( 'Arial, Helvetica', 'font style', 'cookery-lite' ),
    			'stack' => 'arial-helvetica',
    		),
            'arial-gadget' => array(
    			'label' => _x( 'Arial Black, Gadget', 'font style', 'cookery-lite' ),
    			'stack' => 'arial-gadget',
    		),
    		'comic-cursive' => array(
    			'label' => _x( 'Comic Sans MS, cursive', 'font style', 'cookery-lite' ),
    			'stack' => 'comic-cursive',
    		),
    		'impact-charcoal'  => array(
    			'label' => _x( 'Impact, Charcoal', 'font style', 'cookery-lite' ),
    			'stack' => 'impact-charcoal',
    		),
            'lucida' => array(
    			'label' => _x( 'Lucida Sans Unicode, Lucida Grande', 'font style', 'cookery-lite' ),
    			'stack' => 'lucida',
    		),
            'tahoma-geneva' => array(
    			'label' => _x( 'Tahoma, Geneva', 'font style', 'cookery-lite' ),
    			'stack' => 'tahoma-geneva',
    		),
    		'trebuchet-helvetica' => array(
    			'label' => _x( 'Trebuchet MS, Helvetica', 'font style', 'cookery-lite' ),
    			'stack' => 'trebuchet-helvetica',
    		),
    		'verdana-geneva'  => array(
    			'label' => _x( 'Verdana, Geneva', 'font style', 'cookery-lite' ),
    			'stack' => 'verdana-geneva',
    		),
            'courier' => array(
    			'label' => _x( 'Courier New, Courier', 'font style', 'cookery-lite' ),
    			'stack' => 'courier',
    		),
            'lucida-monaco' => array(
    			'label' => _x( 'Lucida Console, Monaco', 'font style', 'cookery-lite' ),
    			'stack' => 'lucida-monaco',
    		)
    	));
	}

	/**
	 * Return an array of all available Google Fonts.
	 *
	 * @return array All Google Fonts.
	 */
	public static function get_google_fonts(){
		if( null === self::$google_fonts || empty( self::$google_fonts ) ){
			$fonts = array( 'body' => '' );
            $fonts = include wp_normalize_path( get_template_directory() . '/inc/custom-controls/typography/google-fonts.php' );
			$google_fonts = array();

			if ( is_array( $fonts ) ) {
				foreach ( $fonts['items'] as $font ) {
					$google_fonts[ $font['family'] ] = array(
						'label'    => $font['family'],
						'variants' => $font['variants'],						
						'category' => $font['category'],
					);
				}
			}

			self::$google_fonts = apply_filters( 'cookery_lite_google_fonts', $google_fonts );
		}
		return self::$google_fonts;
	}

	/**
	 * Google Font Variants
	 *
	 * @return array
	 */
	public static function get_all_variants() {
		return apply_filters( 'cookery_lite_font_variants', array(
			'100'       => esc_attr__( 'Ultra-Light 100', 'cookery-lite' ),
			'100italic' => esc_attr__( 'Ultra-Light 100 Italic', 'cookery-lite' ),
			'200'       => esc_attr__( 'Light 200', 'cookery-lite' ),
			'200italic' => esc_attr__( 'Light 200 Italic', 'cookery-lite' ),
			'300'       => esc_attr__( 'Book 300', 'cookery-lite' ),
			'300italic' => esc_attr__( 'Book 300 Italic', 'cookery-lite' ),
			'regular'   => esc_attr__( 'Normal 400', 'cookery-lite' ),
			'italic'    => esc_attr__( 'Normal 400 Italic', 'cookery-lite' ),
			'500'       => esc_attr__( 'Medium 500', 'cookery-lite' ),
			'500italic' => esc_attr__( 'Medium 500 Italic', 'cookery-lite' ),
			'600'       => esc_attr__( 'Semi-Bold 600', 'cookery-lite' ),
			'600italic' => esc_attr__( 'Semi-Bold 600 Italic', 'cookery-lite' ),
			'700'       => esc_attr__( 'Bold 700', 'cookery-lite' ),
			'700italic' => esc_attr__( 'Bold 700 Italic', 'cookery-lite' ),
			'800'       => esc_attr__( 'Extra-Bold 800', 'cookery-lite' ),
			'800italic' => esc_attr__( 'Extra-Bold 800 Italic', 'cookery-lite' ),
			'900'       => esc_attr__( 'Ultra-Bold 900', 'cookery-lite' ),
			'900italic' => esc_attr__( 'Ultra-Bold 900 Italic', 'cookery-lite' ),
		) );
	}

	/**
	 * Is Google Font
	 *
	 * @param string $fontname
	 */
	public static function is_google_font( $fontname ) {
		return ( array_key_exists( $fontname, self::$google_fonts ) );
	}

	/**
	 * Returns an array of all font choices
	 *
	 * @return array
	 */
	public static function get_font_choices() {
		$fonts       = self::get_all_fonts();
		$fonts_array = array();
		foreach ( $fonts as $key => $args ) {
			$fonts_array[ $key ] = $key;
		}

		return $fonts_array;
	}

	/**
	 * Sanitize Typography Control
	 *
	 * @param array $value
	 *
	 * @access public
	 * @return array
	 */
	public static function sanitize_typography( $value ) {

		if ( ! is_array( $value ) ) {
			return array();
		}

		$sanitized_value = array();

		// Sanitize font family.
		if ( isset( $value['font-family'] ) ) {
			$sanitized_value['font-family'] = sanitize_text_field( $value['font-family'] );
		}

		// Use a valid variant.
		if ( isset( $value['variant'] ) ) {
			$valid_variants = array(
				'regular',
				'italic',
				'100',
				'200',
				'300',
				'500',
				'600',
				'700',
				'700italic',
				'900',
				'900italic',
				'100italic',
				'300italic',
				'500italic',
				'800',
				'800italic',
				'600italic',
				'200italic',
			);

			$sanitized_value['variant'] = ( in_array( $value['variant'], $valid_variants ) ) ? sanitize_text_field( $value['variant'] ) : 'regular';
		}

		return $sanitized_value;

	}
}