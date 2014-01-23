<?php
/**
 * Public function for use by Exchange and other add-ons wishing to interact with  Variants
 *
*/

/**
 * Grab all existing variants for a specific product
 *
 * @since 1.0.0
 *
 * @param  int $product_id the product id
 * @reutrn array an array of variant objects
*/
function it_exchange_get_variants_for_product( $product_id ) {
	if ( ! $product = it_exchange_get_product( $product_id ) )
		return false;

	if ( ! $variant_data = it_exchange_get_product_feature( $product_id, 'variants' ) )
		return false;

	if ( 'yes' != $variant_data['enabled'] || empty( $variant_data['variants'] ) )
		return false;

	$variants = $variant_data['variants'];

	// Setup parents
	$product_variants  = array();
	$orphaned_variants = array();
	foreach( $variants as $variant_id ) {
		if ( $variant = it_exchange_variants_addon_get_variant( $variant_id ) ) {
			if ( ! $variant->is_variant_value ) {
				$product_variants[$variant_id] = $variant;
			} else {
				if ( isset( $product_variants[$variant->post_parent] ) )
					$product_variants[$variant->post_parent]->values[] = $variant;
				else
					$orphaned_variants[$variant->post_parent][$variant->ID] = $variant;
			}

		}
	}

	// Reattach orphaned
	foreach( $orphaned_variants as $parent => $orphan ) {
		if ( isset( $product_variants[$parent] ) )
			$product_variants[$parent]->values[] = $orphan;
	}

	return empty( $product_variants ) ? false : $product_variants;
}
