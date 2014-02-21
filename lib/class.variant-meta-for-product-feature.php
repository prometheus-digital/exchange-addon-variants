<?php
/**
 * This file holds the class responsible for formatting and retreiving variant meta data
 * for product features integrating with variants.
 *
 * @package IT_Exchange
 * @since 1.0.0
*/

/**
 * Data for metadata
 *
 * @since 1.0.0
*/
class IT_Exchange_Variants_Addon_Product_Feature_Combos{

	/**
	 * @var boolean $found_combo  Did we find the combo we were looking for?
	 * @since 1.0.0
	*/
	var $found_combo = false;

	/**
	 * @var string $product_feature  the WP post_meta key
	 * @since 1.0.0
	*/
	var $product_feature;

	/**
	 * @var integer $product_id the WP product_id for the product
	 * @since 1.0.0
	*/
	var $product_id;

	/**
	 * @var string $combo_hash  a unique MD5 hash of the combination of variants this value is for
	 * @since 1.0.0
	*/
	var $combo_hash;

	/**
	 * @var string $combo_title  the name / description of the combination of variants
	 * @since 1.0.0
	*/
	var $combo_title = '';

	/**
	 * @var array $raw_combos  an array of all the variant values associated with the value
	 * @since 1.0.0
	*/
	var $raw_combos;

	/**
	 * @var array $combos_to_hash  the properly formated array of combos that uses parents for array key.
	 * @since 1.0.0
	*/
	var $combos_to_hash;

	/**
	 * @var mixed $value  the data that we're saving for this combination
	 * @since 1.0.0
	*/
	var $value;

	/**
	 * @var mixed $post_meta  the entire post meta entry for this product_feature
	 * @since 1.0.0
	*/
	var $post_meta;

	/**
	 * Constructor. Loads post data and variant preset data
	 *
	 * @since 1.0.0
	 * @param mixed $post  wp post id or post object. optional.
	 * @return void
	*/
	function IT_Exchange_Variants_Addon_Product_Feature_Combos( $product_id, $product_feature, $product_feature_options=array() ) {
		$this->set_product_id( $product_id );
		$this->set_product_feature( $product_feature );
		$this->set_product_feature_options( $product_feature_options );
		$this->set_post_meta();
	}

	function set_product_id( $product_id ) {
		$this->product_id = $product_id;
	}

	function set_product_feature( $product_feature ) {
		$this->product_feature = $product_feature;
	}

	function set_product_feature_options( $product_feature_options ) {
		$this->product_feature_options = $product_feature_options;
	}

	function set_post_meta() {
		$this->post_meta = it_exchange_get_product_feature( $this->product_id, $this->product_feature, $this->product_feature_options );
	}

	function get_post_meta() {
		return $this->post_meta;
	}

	function load_existing_from_hash( $hash ) {
		$this->found_combo = isset( $this->post_meta[$hash] );

		if ( $this->found_combo ) {
			// Set the hash
			$this->combo_hash = $hash;

			// Set the value
			$this->value = empty( $this->post_meta[$hash]['value'] ) ? false : $this->post_meta[$hash]['value'];

			// Set the actual combos
			$this->raw_combos = empty( $this->post_meta[$hash]['raw_combos'] ) ? array() : $this->post_meta[$hash]['raw_combos'];

			// Set the combo title 
			$this->combos_title = empty( $this->post_meta[$hash]['title'] ) ? $this->generate_title_from_combos( $this->raw_combos ) : $this->post_meta[$hash]['title'];

			// Set the combos array like it looks when ready to hash
			$this->combos_to_hash = empty( $this->post_meta[$hash]['combos_to_hash'] ) ? array() : $this->post_meta[$hash]['combos_to_hash'];

			// Set is_parent
			$this->is_parent      = $this->set_is_parent();
		}
	}

	function load_existing_from_combos( $combos ) {
		foreach( (array) $this->post_meta as $hash => $props ) {
			if ( $combos === $props['raw_combos'] ) {
				$this->load_existing_from_hash( $hash );
				break;
			}
		}
	}

	function load_new_from_raw_combos( $combos ) {

		$combos_to_hash = array();
		foreach( $combos as $combo_id ) {
			$variant = it_exchange_variants_addon_get_variant( $combo_id );
			$combos_to_hash[empty( $variant->post_parent ) ? $combo_id : $variant->post_parent] = $combo_id;
		}


		$this->combos_hash    = $this->hash_combos( $combos_to_hash );
		$this->raw_combos     = $combos;
		$this->combos_to_hash = $combos_to_hash;
		$this->combos_title   = $this->generate_title_from_combos( $combos_to_hash );
		$this->is_parent      = $this->set_is_parent();
	}

	function load_new_from_combos_to_hash( $array ) {
		$this->raw_combos     = array_values( $array );
		$this->combos_to_hash = $array;
		$this->combos_hash    = $this->hash_combos( $array );
		$this->combos_title   = $this->generate_title_from_combos( $array );
		$this->is_parent      = $this->set_is_parent();
	}

	function set_is_parent() {
		$combos_to_hash = reset( $this->combos_to_hash );
		return empty( $this->combos_to_hash[$combos_to_hash] );
	}

	function hash_combos( $combos ) {
		// Make sure they're all ints
		$combos_to_hash = array();
		foreach( $combos as $key => $value ) {
			$key   = (int) $key;
			$value = (int) $value;
			$combos_to_hash[$key] = $value;
		}

		// Sort array by ID so that its always in the same order for a variant combination
		ksort( $combos_to_hash );
		return md5( serialize( $combos_to_hash ) ); 
	}

	function generate_title_from_combos( $combo, $include_alls=false ) {
		$combo_title   = array();
		foreach( (array) $combo as $combo_member ) {
			$variant                    = it_exchange_variants_addon_get_variant( $combo_member );
			if ( empty( $variant->post_parent ) && $include_alls )
				$combo_title[] = __( 'All ', 'LION' ) . $variant->post_title;
			else
				$combo_title[] = $variant->post_title;
		}

		return implode( ' - ', $combo_title );
	}

	function update_value_for_combo() {
		if ( empty( $this->combo_hash ) || empty( $this->product_id ) || empty( $this->product_feature ) )
			return false;

		$props = array(
			'raw_combos'     => $this->raw_combos,
			'combos_to_hash' => $this->combos_to_hash,
			'combos_title'   => $this->combos_title,
			'value'          => $this->value,
		);

		$pm_values = $this->post_meta();
		$pm_values[$this->combo_hash] = $props;

		it_exchange_update_product_feature( $this->product_id, $this->product_feature, $pm_values );
		return true;
	}
}
