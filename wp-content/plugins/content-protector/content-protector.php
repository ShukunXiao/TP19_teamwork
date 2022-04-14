<?php
/**
 * Plugin Name:       Passster
 * Plugin URI:        https://patrickposner.dev
 * Description:       A simple plugin to password-protect your complete website, some pages/posts or just parts of your content.
 * Version:           3.5.5.2
 * Author:            Patrick Posner
 * Author URI:        https://patrickposner.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       content-protector
 * Domain Path:       /languages
 *
  */

define( 'PASSSTER_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'PASSSTER_ABSPATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
define( 'PASSSTER_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

// load setup.
require_once( PASSSTER_ABSPATH . 'inc' . DIRECTORY_SEPARATOR . 'setup.php' );

// localize.
$textdomain_dir = plugin_basename( dirname( __FILE__ ) ) . '/languages';
load_plugin_textdomain( 'content-protector', false, $textdomain_dir );

// run plugin.
if ( ! function_exists( 'passster_run_plugin' ) ) {

	// boot autoloader.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) && ! class_exists( 'passster\PS_Admin' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	add_action( 'plugins_loaded', 'passster_run_plugin' );

	/**
	 * Run plugin
	 *
	 * @return void
	 */
	function passster_run_plugin() {
		passster\PS_Admin::get_instance();
		passster\PS_Customizer::get_instance();
		passster\PS_Meta::get_instance();
		passster\PS_Form::get_instance();
		passster\PS_Public::get_instance();
	}
}
