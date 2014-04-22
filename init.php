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
