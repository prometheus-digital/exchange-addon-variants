<?php
/**
 * Inits the add-on when enabled by exchange
 *
 * @since 1.0.0
 * @package IT_Exchange_Variants_Addon
*/

/**
 * Sets up our post types. We have two post types. One for Variants and one for Variant Values
*/
include( 'lib/post-types.php' );

/**
 * Includes all of our internal helper functions
*/
include( 'lib/functions.php' );

/**
 * Includes the code for the product feature
*/
include( 'lib/product-features/class.variants.php' );

/**
 * WP Hooks
*/
include( 'lib/hooks.php' );

/**
 * API functions
*/
include( 'api/load.php' );

/**
 * Template functions
*/
include( 'lib/template-functions.php' );

/**
 * Variant Theme API
*/
if ( ! is_admin() ) {
	include( 'api/theme/variant.php' );
	include( 'api/theme/variant-value.php' );
}

/**
 * Exchange will build your add-on's settings page for you and link to it from our add-on
 * screen. You are free to link from it elsewhere as well if you'd like... or to not use our API
 * at all. This file has all the functions related to registering the page, printing the form, and saving
 * the options. This includes the wizard settings. Additionally, we use the Exchange storage API to
 * save / retreive options. Add-ons are not required to do this.
*/
include( 'lib/addon-settings.php' );
