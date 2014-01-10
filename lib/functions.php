<?php
/**
 * Supporting functions
 * @package IT_Exchange_Variants_Addon
 * @since 1.0.0
*/

/**
 * This creates the initial product variant presets if they don't exist and haven't been deleted.
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_variants_addon_create_inital_presets() {
	$addon_settings      = it_exchange_get_option( 'variants-addon', true );
	$core_presets_args   = it_exchange_variants_addon_get_core_presets_args();
	$existing_presets    = it_exchange_variants_addon_get_presets( array( 'core_only' => true ) );

	/*
	foreach( $existing_presets as $preset ) {
		wp_delete_post( $preset->ID, true );
	}
	die();
	*/

	// Loop through preset args and add, update or skip each preset
	foreach( $core_presets_args as $preset ) {
		// Don't create it if it was already deleted by the store owner
		if ( ! empty( $addon_settings['deleted-core-presets'][$preset['slug']] ) )
			continue;

		// Don't create it if it already exists, see if we need to update it
		if ( ! empty( $existing_presets[$preset['slug']] ) ) {
			if ( it_exchange_variants_addon_core_preset_needs_updated( $existing_presets[$preset['slug']], $preset['version'] ) )
				it_exchange_variants_addon_update_core_preset( $existing_presets[$preset['slug']]->get_property( 'ID' ), $preset );

			// Move on to next preset
			continue;
		}

		// If we made it here, we need to add the preset
		it_exchange_variants_addon_create_variant_preset( $preset );
	}
	ITUtility::print_r( it_exchange_variants_addon_get_presets( array( 'core_only' => true ) ) );
}

/**
 * Returns the core preset args
 *
 * @since 1.0.0
 *
 * @return array
*/
function it_exchange_variants_addon_get_core_presets_args() {
	$args = array(
		'blank'       => array(
			'slug'    => 'blank',
			'image'   => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/images/presets/blank.png' ),
			'title'   => __( 'Blank', 'LION' ),
			'values'  => array(),
			'default' => false,
			'order'   => 0,
			'core'    => true,
			'version' => '0.0.1',
		),
		'colors'       => array(
			'slug'    => 'colors',
			'image'   => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/images/presets/colors.png' ),
			'title'   => __( 'Colors', 'LION' ),
			'values'  => array(
				'000000' => array(
					'slug'  => '000000',
					'title' => __( 'Black', 'LION' ),
				),
				'ffffff' => array(
					'slug'  => 'ffffff',
					'title' => __( 'White', 'LION' ),
				),
				'ff0000' => array(
					'slug'  => 'ff0000',
					'title' => __( 'Red', 'LION' ),
				),
			),
			'default' => false,
			'order'   => 5,
			'core'    => true,
			'version' => '0.0.2',
		),
		'sizes'  => array(
			'slug'    => 'sizes',
			'image'   => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/images/presets/sizes.png' ),
			'title'   => __( 'Sizes', 'LION' ),
			'values'  => array(
				'xs'  => array(
					'slug'  => 'xs',
					'title' => __( 'XS', 'LION' ),
					'order' => 3,
				),
				's'   => array(
					'slug'  => 's',
					'title' => __( 'S', 'LION' ),
					'order' => 6,
				),
				'm'   => array(
					'slug'  => 'm',
					'title' => __( 'M', 'LION' ),
					'order' => 9,
				),
				'l'   => array(
					'slug'  => 'l',
					'title' => __( 'L', 'LION' ),
					'order' => 12,
				),
				'xl'  => array(
					'slug'  => 'xl',
					'title' => __( 'XL', 'LION' ),
					'order' => 15,
				),
				'xxl'  => array(
					'slug'  => 'xxl',
					'title' => __( 'XXL', 'LION' ),
					'order' => 18,
				),
			),
			'default' => false,
			'order'   => 0,
			'core'    => true,
			'version' => '0.0.4',
		),
	);
	return $args;
}

/**
 * Determines if an already existing core preset needs to be updated
 *
 * @since 1.0.0
 *
 * @param array $existing_preset the preset we're checking
 * @return boolean
*/
function it_exchange_variants_addon_core_preset_needs_updated( $existing_preset, $preset_version ) {
	return version_compare( $existing_preset->get_property( 'version' ), $preset_version, '<' );
}

/**
 * Creates an Exchange Variant Preset
 *
 * @since 1.0.0
 *
 * @param array $args the args passed to wp_insert_post
 * @return mixed id or false
*/
function it_exchange_variants_addon_create_variant_preset( $args ) {
	$defaults = array(
		'status'         => 'publish',
		'ping_status'    => 'closed',
		'comment_status' => 'closed',
	);   
	$defaults = apply_filters( 'it_exchange_add_variant_preset_defaults', $defaults );

	// Convert our API keys to WP keys
	if ( isset( $args['slug'] ) )
		$args['post_name'] = $args['slug'];
	if ( isset( $args['title'] ) )
		$args['post_title'] = $args['title'];
	if ( isset( $args['order'] ) )
		$args['menu_order'] = $args['order'];

	$args = ITUtility::merge_defaults( $args, $defaults );

	// Convert $args to insert post args
	$post_args = array();
	$post_args['post_status']  = $args['status'];
	$post_args['post_type']    = 'it_exng_varnt_preset';
	$post_args['post_title']   = empty( $args['title'] ) ? __( 'New Preset', 'LION' ) : $args['title'];
	$post_args['post_name']    = empty( $args['post_name'] ) ? 'new-preset' : $args['post_name'];
	$post_args['post_content'] = empty( $args['post_content'] ) ? '' : $args['post_content']; 

	// Insert Post and get ID
	if ( $product_id = wp_insert_post( $post_args ) ) {

		// Setup metadata
		$meta = array();
		if ( ! empty( $args['slug'] ) )
			$meta['slug'] = $args['slug'];
		if ( ! empty( $args['image'] ) )
			$meta['image']   = $args['image'];
		if ( ! empty( $args['values'] ) )
			$meta['values']  = $args['values'];
		if ( ! empty( $args['default'] ) )
			$meta['default'] = $args['default'];
		if ( ! empty( $args['core'] ) )
			$meta['core']    = $args['core'];
		if ( ! empty( $args['version'] ) )
			$meta['version'] = $args['version'];

		// Save metadata
		update_post_meta( $product_id, '_it_exchange_variants_addon_preset_meta', $meta );

		// Return the ID
		return $product_id;
	}    
	return false;
}

/**
 * Get IT_Exchange_Variant_Presets
 *
 * @since 1.0.0
 * @return array  an array of IT_Exchange_Variant_Preset objects
*/
function it_exchange_variants_addon_get_presets( $args=array() ) { 
	$defaults = array(
		'post_type' => 'it_exng_varnt_preset',
		'core_only' => false,
	);  
	$args = wp_parse_args( $args, $defaults );
	$args['meta_query'] = empty( $args['meta_query'] ) ? array() : $args['meta_query'];

	$variant_presets = false;
	if ( $presets = get_posts( $args ) ) { 
		foreach( $presets as $key => $preset ) { 
			$preset_object = it_exchange_variants_addon_get_preset( $preset );

			// Don't add if requested only core and variant is not a core.
			if ( ! empty( $args['core_only'] ) && ! $preset_object->get_property( 'core' ) )
				continue;

			$variant_presets[$preset_object->get_property( 'slug' )] = $preset_object;
		}   
	}   

	return apply_filters( 'it_exchange_variants_addon_get_presets', $variant_presets, $args );
}

/**
 * Retreives a variant preset object by passing it the WP post object or post id
 *
 * @since 1.0.0
 * @param mixed $post  post object or post id
 * @rturn object IT_Exchange_Variant_Preset object for passed post
*/
function it_exchange_variants_addon_get_preset( $post ) { 
	include_once( 'class.variant-preset.php' );
    $preset = new IT_Exchange_Variants_Addon_Preset( $post );
    if ( $preset->ID )
        return apply_filters( 'it_exchange_variants_addon_get_preset', $preset, $post );
    return apply_filters( 'it_exchange_variants_addon_get_preset', false, $post );
}

/**
 * If we need to update the core preset, delete the old one and add the new one back.
 *
 * @since 1.0.0
 *
 * @param integer $old_id           old preset ID
 * @param array   $new_preset_args  new values
 * @return void
*/
function it_exchange_variants_addon_update_core_preset( $old_id, $new_preset_args ) {
	wp_delete_post( $old_id, true );
	it_exchange_variants_addon_create_variant_preset( $new_preset_args );
}
