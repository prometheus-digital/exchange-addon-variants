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
	 * @var array $product_variants the variants product feature
	 * @since 1.0.0
	*/
	var $product_variants = null;

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
	 * @var string $product_feature_variants_version
	 * @since 1.0.0
	*/
	var $product_feature_variants_version;

	/**
	 * @var mixed $post_meta  the entire post meta entry for this product_feature
	 * @since 1.0.0
	*/
	var $post_meta;

	/**
	 * @var array all possible variant combos for this product
	 * @since 1.0.0
	*/
	var $all_variant_combos_for_product;

	var $post_cache;

	/**
	 * Constructor. Loads post data and variant preset data
	 *
	 * @since 1.0.0
	 * @param mixed  $post  wp post id or post object. optional.
	 * @param string $product_feature this is the slug of a product feature. eg: inventory
	 * @param array  $product_feature_options options
	 * @return void
	*/
	function __construct( $product_id, $product_feature, $product_feature_options=array() ) {
		$this->set_product_id( $product_id );
		$this->set_product_feature( $product_feature );
		$this->set_product_feature_options( $product_feature_options );
		$this->set_post_meta();
		$this->set_product_feature_variants_version();
	}

	/**
	 * Constructor. Loads post data and variant preset data
	 *
	 * @since 1.0.0
	 * @param mixed  $post  wp post id or post object. optional.
	 * @param string $product_feature this is the slug of a product feature. eg: inventory
	 * @param array  $product_feature_options options
	 * @return void
	*/
	function IT_Exchange_Variants_Addon_Product_Feature_Combos( $product_id, $product_feature, $product_feature_options=array() ) {
		self::__construct();
	}

	/**
	 * Sets the product_id property
	 *
	 * @since 1.0.0
	 *
	 * @param int $product_id the product ID from the posts table
	 * @return void
	*/
	function set_product_id( $product_id ) {
		$this->product_id = $product_id;
	}

	/**
	 * Sets the product_feature property
	 *
	 * @since 1.0.0
	 *
	 * @param string $product_feature the product ID from the posts table. eg: inventory
	 * @return void
	*/
	function set_product_feature( $product_feature ) {
		$this->product_feature = $product_feature;
	}

	/**
	 * Sets the product_variants property
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_product_variants() {
		$this->product_variants = it_exchange_get_product_feature( $this->product_id, 'variants' );
	}

	/**
	 * Sets the product_feature property
	 *
	 * @since 1.0.0
	 *
	 * @param array $product_feature_options options
	 * @return void
	*/
	function set_product_feature_options( $product_feature_options ) {
		$this->product_feature_options = $product_feature_options;
	}

	/**
	 * Grabs the post meta asked for by the controller and loads in the post_meta property
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_post_meta() {
		$this->post_meta = it_exchange_get_product_feature( $this->product_id, $this->product_feature, $this->product_feature_options );
	}

	function set_product_feature_variants_version() {
		$this->product_feature_variants_version = it_exchange_get_product_feature( $this->product_id, $this->product_feature, array( 'setting' => 'variants-version' ) );
	}

	/**
	 * Grabs all possible variant combos for a product's variants and saves to the all_variant_combos_for_product property
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_all_variant_combos_for_product( $include_alls=true ) {
		$this->all_variant_combos_for_product = it_exchange_variants_addon_get_all_variant_combos_for_product( $this->product_id, $include_alls );
	}

	/**
	 * Sets the value of attribute
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value the value to be used
	 * @return void
	*/
	function set_value( $value ) {
		$this->value = $value;
	}

	/**
	 * Grab the post_meta property
	 *
	 * @since 1.0.0
	 *
	 * @return array
	*/
	function get_post_meta() {
		return $this->post_meta;
	}

	function clear_post_meta() {
		$this->post_meta = array();
	}

	function variants_were_updated() {
		if ( is_null( $this->product_variants ) )
			$this->set_product_variants();

		if ( empty( $this->product_variants['variants_version'] ) )
			return false;

		if ( empty( $this->product_feature_variants_version ) )
			$this->product_feature_variants_version = $this->product_variants['variants_version'];

		return $this->product_feature_variants_version != $this->product_variants['variants_version'];
	}

	function load_existing_from_hash( $hash ) {
		$this->reset_current_combo();

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

			// Set variants title array
			$this->variants_title_array = $this->generate_variants_title_array( $this->combos_to_hash );
		}
	}

	function load_existing_from_combos( $combos ) {
		$this->reset_current_combo();

		foreach( (array) $this->post_meta as $hash => $props ) {
			if ( $combos === $props['raw_combos'] ) {
				$this->load_existing_from_hash( $hash );
				break;
			}
		}
	}

	function load_new_from_raw_combos( $combos ) {
		$this->reset_current_combo();

		$combos_to_hash = $this->convert_raw_combos_to_combos_for_hash( $combos );

		$this->combo_hash           = $this->hash_combos( $combos_to_hash );
		$this->raw_combos           = $combos;
		$this->combos_to_hash       = $combos_to_hash;
		$this->combos_title         = $this->generate_title_from_combos( $combos_to_hash );
		$this->is_parent            = $this->set_is_parent();
		$this->variants_title_array = $this->generate_variants_title_array( $combos_to_hash );
	}

	function load_new_from_combos_to_hash( $array ) {
		$this->reset_current_combo();
		$this->raw_combos           = array_values( $array );
		$this->combos_to_hash       = $array;
		$this->combo_hash           = $this->hash_combos( $array );
		$this->combos_title         = $this->generate_title_from_combos( $array );
		$this->is_parent            = $this->set_is_parent();
		$this->variants_title_array = $this->generate_variants_title_array( $array );
	}

	function load_new_from_hash( $hash ) {
		$this->reset_current_combo();
		foreach( (array) $this->all_variant_combos_for_product as $combo ) {
			$combo_to_hash = $this->convert_raw_combos_to_combos_for_hash( $combo );
			if ( $hash == $this->hash_combos( $combo_to_hash ) ) {
				$this->load_new_from_combos_to_hash(  $combo_to_hash );
			}
		}
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

	function reset_current_combo() {
		$this->raw_combos     = array();
		$this->combos_to_hash = array();
		$this->combo_hash     = false;
		$this->combos_title   = '';
		$this->is_parent      = false;
		$this->found_combo    = false;
	}

	function convert_raw_combos_to_combos_for_hash( $combos ) {
		$combos_to_hash = array();
		foreach( $combos as $combo_id ) {
			if ( empty( $this->post_cache[$combo_id] ) ) {
				$variant = it_exchange_variants_addon_get_variant( $combo_id );
				$this->post_cache[$combo_id] = $variant;
			} else {
				$variant = $this->post_cache[$combo_id];
			}
			$combos_to_hash[empty( $variant->post_parent ) ? $combo_id : $variant->post_parent] = $combo_id;
		}
		return $combos_to_hash;
	}

	function generate_variants_title_array( $combos_to_hash ) {
		$variants_title_array = array();
		foreach( $combos_to_hash as $key => $variant_id ) { 
			$parent_title                        = get_the_title( $key );
			$child_title                         = get_the_title( $variant_id );
			$variants_title_array[$parent_title] = $child_title;
		} 
		return $variants_title_array;
	}

	function generate_title_from_combos( $combo, $include_alls=false ) {
		$combo_title   = array();
		foreach( (array) $combo as $combo_id) {
			if ( empty( $this->post_cache[$combo_id] ) ) {
				$variant = it_exchange_variants_addon_get_variant( $combo_id );
				$this->post_cache[$combo_id] = $variant;
			} else {
				$variant = $this->post_cache[$combo_id];
			}
			if ( empty( $variant->post_parent ) )
				$combo_title[] = __( 'Any ', 'LION' ) . $variant->post_title;
			else
				$combo_title[] = $variant->post_title;
		}

		return implode( ' - ', $combo_title );
	}

	function update_meta_value_for_current_combo() {
		if ( empty( $this->combo_hash ) )
			return false;

		$props = array(
			'raw_combos'           => $this->raw_combos,
			'combos_to_hash'       => $this->combos_to_hash,
			'combos_title'         => $this->combos_title,
			'variants_title_array' => $this->variants_title_array,
			'value'                => $this->value,
		);

		$this->post_meta[$this->combo_hash] = $props;
		return true;
	}

	function save_post_meta() {
		it_exchange_update_product_feature( $this->product_id, $this->product_feature, $this->post_meta, array( 'setting' => 'variants' ) );
	}

}
