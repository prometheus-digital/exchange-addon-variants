<?php
/**
 * This file contains functions related to variant frontend templating
 * @since 1.0.0
*/

/**
 * Inserts variants template below price
*/
function it_exchange_variants_addon_register_template_loop( $elements ) {
	// Splice our template in after base price if it is first
	if ( 'base-price' == $elements[0] )
		$index = 1;
	else
		$index = 0;

	$index = apply_filters( 'it_exchange_get_content_product_product_info_loop_elements_variants_index', $index );
	array_splice( $elements, $index, 0, 'exchange-variants' );	

	return $elements;
}
add_filter( 'it_exchange_get_content_product_product_info_loop_elements', 'it_exchange_variants_addon_register_template_loop' );

/**
 * Adds our templates directory to the list of directories
 * searched by Exchange
 *
 * @since 1.0.0
 *
 * @param array $template_path existing array of paths Exchange will look in for templates
 * @param array $template_names existing array of file names Exchange is looking for in $template_paths directories
 * @return array
*/
function it_exchange_addon_variants_register_templates( $template_paths, $template_names ) { 
	// Bail if not looking for one of our templates
	$add_path = false;
	$templates = array(
		'content-product/elements/exchange-variants.php',
	);  
	foreach( $templates as $template ) { 
		if ( in_array( $template, (array) $template_names ) ) 
			$add_path = true;
	}   
	if ( ! $add_path )
		return $template_paths;

	$template_paths[] = dirname( __FILE__ ) . '/templates';
	return $template_paths;
}
add_filter( 'it_exchange_possible_template_paths', 'it_exchange_addon_variants_register_templates', 10, 2 );
