<?php
/*
 * Plugin Name: ExchangeWP - Product Variants
 * Version: 1.5.5
 * Description: Allows store owners to add variant options to ExchangeWP product types.
 * Plugin URI: https://exchangewp.com/downloads/product-variants/
 * Author: ExchangeWP
 * Author URI: https://exchangewp.com
 * ExchangeWP Package: exchange-addon-variants

 * Installation:
 * 1. Download and unzip the latest release zip file.
 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
 * 3. Upload the entire plugin directory to your `/wp-content/plugins/` directory.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
 *
*/

/**
 * Define the version number
 *
 * @since 1.0.0
*/
define( 'IT_Exchange_Variants_Addon_Version', '1.5.4' );

/**
 * This registers our plugin as an exchange addon
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_register_variants_addon() {
	$options = array(
		'name'              => __( 'Product Variants', 'LION' ),
		'description'       => __( 'Allows store owners to add variant options to product types.', 'LION' ),
		'author'            => 'ExchangeWP',
		'author_url'        => 'http://ithemes.com/purchase/product-variants/',
		'icon'              => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/lib/images/variants50.png' ),
		'file'              => dirname( __FILE__ ) . '/init.php',
		'category'          => 'product-feature',
		'basename'          => plugin_basename( __FILE__ ),
		'settings-callback' => 'it_exchange_product_variants_addon_settings_callback',
	);
	it_exchange_register_addon( 'product-variants', $options );
}
add_action( 'it_exchange_register_addons', 'it_exchange_register_variants_addon' );

/**
 * Loads the translation data for WordPress
 *
 * @uses load_plugin_textdomain()
 * @since 1.0.0
 * @return void
*/
function it_exchange_variants_addon_set_textdomain() {
	load_plugin_textdomain( 'LION', false, dirname( plugin_basename( __FILE__  ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'it_exchange_variants_addon_set_textdomain' );

/**
 * Registers Plugin with iThemes updater class
 *
 * @since 1.0.0
 *
 * @param object $updater ithemes updater object
 * @return void
*/
function it_exchange_variants_addon_register( $updater ) {
	    $updater->register( 'exchange-addon-variants', __FILE__ );
}
add_action( 'ithemes_updater_register', 'it_exchange_variants_addon_register' );
// require( dirname( __FILE__ ) . '/lib/updater/load.php' );

if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) )  {
 	require_once 'EDD_SL_Plugin_Updater.php';
 }

 function exchange_product_variants_plugin_updater() {

 	// retrieve our license key from the DB
 	// this is going to have to be pulled from a seralized array to get the actual key.
 	// $license_key = trim( get_option( 'exchange_product_variants_license_key' ) );
 	$exchangewp_product_variants_options = get_option( 'it-storage-exchange_product_variants-addon' );
 	$license_key = $exchangewp_product_variants_options['product_variants-license-key'];

 	// setup the updater
 	$edd_updater = new EDD_SL_Plugin_Updater( 'https://exchangewp.com', __FILE__, array(
 			'version' 		=> '1.5.5', 				// current version number
 			'license' 		=> $license_key, 		// license key (used get_option above to retrieve from DB)
 			'item_name' 	=> 'product-variants', 	  // name of this plugin
 			'author' 	  	=> 'ExchangeWP',    // author of this plugin
 			'url'       	=> home_url(),
 			'wp_override' => true,
 			'beta'		  	=> false
 		)
 	);
 	// var_dump($edd_updater);
 	// die();

 }

 add_action( 'admin_init', 'exchange_product_variants_plugin_updater', 0 );
