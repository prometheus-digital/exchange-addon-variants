<?php
/**
 * This file sets up our post types. We have two post types. One for Variants and one for Variant Values
 * @package IT_Exchange_Variants_Addon
 * @since 1.0.0
*/

/**
 * Registers the Variants Post Type
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_variants_addon_register_post_types() {
	// Variant Post Type Args
	$labels    = array(
		'name'          => __( 'Product Variants', 'LION' ),
		'singular_name' => __( 'Product Variant', 'LION' ),
		'edit_item'     => __( 'Edit Variant', 'LION' ),
		'view_item'     => __( 'View Variant', 'LION' ),
	);
	$args = array(
		'labels' => $labels,
		'description'  => __( 'An iThemes Exchange Post Type for storing Product Variants in the system', 'LION' ),
		'public'       => false,
		'show_ui'      => false,
		'hierarchical' => false,
		'supports'     => array( 'title', 'custom-fields', ),
	);

	$args = apply_filters( 'it_exchange_variants_addon_variant_post_type_args', $args );

	// Register the variant post type
	register_post_type( 'it_exchange_variant', $args );

	// Preset Post Type Args
	$labels    = array(
		'name'          => __( 'Variant Presets', 'LION' ),
		'singular_name' => __( 'Variant Preset', 'LION' ),
		'edit_item'     => __( 'Edit Variant Preset', 'LION' ),
		'view_item'     => __( 'View Variant Preset', 'LION' ),
	);
	$args = array(
		'labels' => $labels,
		'description'  => __( 'An iThemes Exchange Post Type for storing Product Variant Preset options in the system', 'LION' ),
		'public'       => false,
		'show_ui'      => false,
		'hierarchical' => true,
		'supports'     => array( 'title', 'custom-fields', ),
	);

	$args = apply_filters( 'it_exchange_variants_addon_variant_preset_post_type_args', $args );

	// Register the post type
	register_post_type( 'it_exng_varnt_preset', $args );
}
add_action( 'init', 'it_exchange_variants_addon_register_post_types' );
