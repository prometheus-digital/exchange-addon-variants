<?php
/*
 * Plugin Name: iThemes Exchange - Product Variants
 * Version: 1.2.0
 * Description: Allows store owners to add variant options to iThemes Exchange product types.
 * Plugin URI: http://ithemes.com/purchase/product-variants/
 * Author: iThemes
 * Author URI: http://ithemes.com
 * iThemes Package: exchange-addon-variants
 
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
define( 'IT_Exchange_Variants_Addon_Version', '1.1.1' );

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
		'author'            => 'iThemes',
		'author_url'        => 'http://ithemes.com/purchase/product-variants/',
		'icon'              => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/lib/images/variants50.png' ),
		'file'              => dirname( __FILE__ ) . '/init.php',
		'category'          => 'product-feature',
		'basename'          => plugin_basename( __FILE__ ),
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
require( dirname( __FILE__ ) . '/lib/updater/load.php' );
