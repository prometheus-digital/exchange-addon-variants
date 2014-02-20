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

	// Order top level variants
	usort( $product_variants, 'it_exchange_variants_addon_sort_product_variants' );

	// Order values
	foreach( $product_variants as $key => $variant ) {
		$values = $variant->values;
		usort( $values, 'it_exchange_variants_addon_sort_product_variants' );
		$product_variants[$key]->values = $values;
	}

	return empty( $product_variants ) ? false : $product_variants;
}

/**
 * usort callback used with it_exchange_get_variants_for_product() for sorting variants by page order
 *
 * @since 1.0.0
 *
 * @param object $a variant object. Should have a page_order property
 * @param object $b variant object. Should have a page_order property
 * @return int
*/
function it_exchange_variants_addon_sort_product_variants( $a, $b ) {
	$a_order = empty( $a->menu_order ) ? 0 : $a->menu_order;
	$b_order = empty( $b->menu_order ) ? 0 : $b->menu_order;

	if ( $a_order == $b_order )
		return 0;

	return ($a_order < $b_order ) ? -1 : 1;
}

/**
 * Gets a hash for a combination of variant/value pairs.
 *
 * @since 1.0.0
*/
function it_exchange_variants_addon_get_selected_variants_id_hash( $array=array() ) {
	// Make sure they're all ints
	$array = array_map( 'intval', $array );

	// Sort array by ID so that its always in the same order for a variant combination
	ksort( $array );

	return md5( serialize( $array ) );	
}

function it_exchange_variants_addon_get_all_variant_combos_for_product( $product_id ) {

	// Grab all 
	if ( ! $product_variants = it_exchange_get_variants_for_product( $product_id ) )
		return array();

	// Build columns array
	$combos = array();
	$i=0;
	foreach( $product_variants as $key => $variant ) {
		$combos[$i] = array();
		$combos[$i][] = $variant->ID;
		foreach( (array) $variant->values as $value ) {
			$combos[$i][] = $value->ID;
		}
		$i++;
	}

	$GLOBALS['it_exchange']['temp_variants']['codes']  = array();
	$GLOBALS['it_exchange']['temp_variants']['pos']    = 0; 
	$GLOBALS['it_exchange']['temp_variants']['combos'] = array();
	generateCodes($combos);
	
	$combos = empty( $GLOBALS['it_exchange']['temp_variants']['combos'] ) ? array() : $GLOBALS['it_exchange']['temp_variants']['combos'];

	foreach( $combos as $key => $combo ) {
		$variant = it_exchange_variants_addon_get_variant( $combo[0] );
		$combo_to_hash[empty( $variant->post_parent ) ? $variant->ID : $variant->post_parent] = $variant->ID;
		
		$combos_to_return[it_exchange_variants_addon_get_selected_variants_id_hash($combo_to_hash)] = $combo;
	}

	if ( isset( $GLOBALS['it_exchange']['temp_variants'] ) )
		unset( $GLOBALS['it_exchange']['temp_variants'] );

	return $combos_to_return;
}

function generateCodes($arr) {
	if(count($arr)) {
		for($i=0; $i<count($arr[0]); $i++) {
			$tmp = $arr;
			$GLOBALS['it_exchange']['temp_variants']['codes'][$GLOBALS['it_exchange']['temp_variants']['pos']] = $arr[0][$i];
			$tarr = array_shift($tmp);
			$GLOBALS['it_exchange']['temp_variants']['pos']++;
			generateCodes($tmp);
		}
	} else {
		//echo join(", ", $GLOBALS['it_exchange']['temp_variants']['codes'])."<br/>";
		$GLOBALS['it_exchange']['temp_variants']['combos'][] = $GLOBALS['it_exchange']['temp_variants']['codes'];
	}
	$GLOBALS['it_exchange']['temp_variants']['pos']--;
}

function it_exchange_get_variant_combo_attributes( $combo ) {
	$combo_title   = array();
	$array_to_hash = array();
	foreach( (array) $combo as $combo_member ) {
		$value                      = it_exchange_variants_addon_get_variant( $combo_member );
		$combo_title[]              = empty( $value->post_parent ) ? __( 'All ', 'LION' ) . $value->post_title: $value->post_title;
		$parent_key                 = empty( $value->post_parent ) ? $value->ID : $value->post_parent;
		$array_to_hash[$parent_key] = $value->ID;

		// If we find a member that's not a variant any longer (it was deleted), return false
		if ( ! $value )
			return false;
	}

	$atts = array(
		'hash'  => it_exchange_variants_addon_get_selected_variants_id_hash( $array_to_hash ),
		'title' => implode( $combo_title, ' - ' ),
		'combo' => $array_to_hash,
	);

	return $atts;
}
