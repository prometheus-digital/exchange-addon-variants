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

	//die( ITUtility::print_r($existing_presets) );
	/*
	foreach( $existing_presets as $preset ) {
		wp_delete_post( $preset->ID, true );
	}
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
		'template-select' => array(
			'slug'     => 'template-select',
			'title'    => __( 'Select', 'LION' ),
			'values'  => array(
				0 => array(
					'slug'    => 'new_default_1',
					'title'   => __( 'Select 1', 'LION' ),
				),
				1 => array(
					'slug'    => 'new_default_2',
					'title'   => __( 'Select 2', 'LION' ),
				),
			),
			'default'  => false,
			'order'    => 0,
			'core'     => true,
			'ui-type'  => 'select',
			'version'  => '0.0.23',
		),
		'template-radio' => array(
			'slug'     => 'template-radio',
			'title'    => __( 'Radio', 'LION' ),
			'values'  => array(
				0 => array(
					'slug'    => 'new_default_1',
					'title'   => __( 'Radio 1', 'LION' ),
				),
				1 => array(
					'slug'    => 'new_default_2',
					'title'   => __( 'Radio 2', 'LION' ),
				),
			),
			'default'  => false,
			'order'    => 3,
			'core'     => true,
			'ui-type'  => 'radio',
			'version'  => '0.0.23',
		),
		'tempalte-hex'   => array(
			'slug'     => 'template-hex',
			'title'    => __( 'Color', 'LION' ),
			'values'  => array(
				0 => array(
					'slug'    => 'new_default_1',
					'title'   => __( 'Color 1', 'LION' ),
					'color'   => '#0029BA',
				),
				1 => array(
					'slug'    => 'new_default_2',
					'title'   => __( 'Color 2', 'LION' ),
					'color'   => '#0082CA',
				),
			),
			'default'  => false,
			'order'    => 5,
			'core'     => true,
			'ui-type'  => 'color',
			'version'  => '0.0.23',
		),
		'tempalte-image' => array(
			'slug'     => 'template-image',
			'title'    => __( 'Image', 'LION' ),
			'values'  => array(
				0 => array(
					'slug'    => 'new_default_1',
					'title'   => __( 'Image 1', 'LION' ),
				),
				1 => array(
					'slug'    => 'new_default_2',
					'title'   => __( 'Image 2', 'LION' ),
				),
			),
			'default'  => false,
			'order'    => 8,
			'core'     => true,
			'ui-type'  => 'image',
			'version'  => '0.0.23',
		),
		'ithemes-colors'       => array(
			'slug'    => 'ithemes-colors',
			'title'   => __( 'iThemes Colors', 'LION' ),
			'values'  => array(
				'88C53E' => array(
					'slug'    => '88C53E',
					'title'   => __( 'iThemes Green', 'LION' ),
					'color'   => '#88C53E',
				),
				'0082CA' => array(
					'slug'    => '0082CA',
					'title'   => __( 'iThemes Blue', 'LION' ),
					'color'   => '#0082CA',
				),
				'F1FFDE' => array(
					'slug'    => 'F1FFDE',
					'title'   => __( 'Exchange Green', 'LION' ),
					'color'   => '#F1FFDE',
				),
				'334940' => array(
					'slug'    => '334940',
					'title'   => __( 'Exchange Dark Green', 'LION' ),
					'color'   => '#334940',
				),
			),
			'default' => false,
			'order'   => 5,
			'core'    => true,
			'ui-type' => 'color',
			'version' => '0.0.23',
		),
		'ithemes-sizes'  => array(
			'slug'    => 'ithemes-sizes',
			'title'   => __( 'Sizes', 'LION' ),
			'values'  => array(
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
			),
			'default' => false,
			'order'   => 0,
			'core'    => true,
			'ui-type' => 'select',
			'version' => '0.0.23',
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
	$post_args['menu_order']   = empty( $args['menu_order'] ) ? 0 : $args['menu_order'];

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
		if ( ! empty( $args['ui-type'] ) )
			$meta['ui-type'] = $args['ui-type'];
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
		'post_type'      => 'it_exng_varnt_preset',
		'core_only'      => false,
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
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

/**
 * Inits the AJAX response for add/edit page.
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_variants_addon_process_add_edit_variant_ajax() {
	$action = empty( $_POST['itevAction'] ) ? false : $_POST['itevAction'];
	$return = new stdClass();
	$return->status = 0;

	if ( ! $action || ! check_ajax_referer( 'it-exchange-variants-addon-add-preset-template', '_itEVAddTemplateNonce' ) )
		die( json_encode( $return ) );

	switch( $action ) {
		case 'addVariantFromTemplate' :
			$return->status  = 1;
			$return->message = false;
			$return->html    = it_exchange_variants_addon_get_add_edit_variant_form_field( 'template', $_POST['itevTemplateID'] );
			break;
		case 'addVariantFromSaved' :
			$return->status  = 1;
			$return->message = false;
			$return->html    = it_exchange_variants_addon_get_add_edit_variant_form_field( 'saved', $_POST['itevSavedID'] );
			break;

	}
	die( json_encode( $return ) );

}
add_action( 'wp_ajax_ite_add_edit_variants', 'it_exchange_variants_addon_process_add_edit_variant_ajax' );

/**
 * Builds a new variant form field from a template
 *
 * @since 1.0.0
 *
 * @param int template_id
*/
function it_exchange_variants_addon_get_add_edit_variant_form_field( $type, $id ) {
	include_once( 'class.variant-form-field.php' );
	$field = new IT_Exchange_Variants_Addon_Form_Field( $type, $id );
	return $field->div;
}

/**
 * Returns an object that mimicks the variant preset object for saved variant preset values.
 *
 * @since 1.0.0
*/
function it_exchange_variants_addon_get_saved_preset_value( $args ) {
	include_once( 'class.variant-saved-preset-value.php' );
    $preset = new IT_Exchange_Variants_Addon_Saved_Preset_Value( $args );
    if ( $preset->title && $preset->slug )
        return apply_filters( 'it_exchange_variants_addon_get_saved_preset_value', $preset, $args );
    return apply_filters( 'it_exchange_variants_addon_get_saved_preset_value', false, $args );
}

/**
 * Creates an Exchange Variant
 *
 * @since 1.0.0
 *
 * @param array $args the args passed to wp_insert_post
 * @return mixed id or false
*/
function it_exchange_variants_addon_create_variant( $args ) {
	$defaults = array(
		'post_status'    => 'publish',
		'ping_status'    => 'closed',
		'comment_status' => 'closed',
	);
	$defaults = apply_filters( 'it_exchange_add_variant_defaults', $defaults );

	$args = ITUtility::merge_defaults( $args, $defaults );

	// Set our Post Type
	$args['post_type'] = 'it_exchange_variant';

	// Insert Post and get ID
	if ( $variant_id = wp_insert_post( $args ) ) {

		// Setup metadata
		$meta = array();
		if ( ! empty( $args['image'] ) )
			$meta['image']   = $args['image'];
		if ( ! empty( $args['color'] ) )
			$meta['color']   = $args['color'];
		if ( ! empty( $args['default'] ) )
			$meta['default'] = $args['default'];
		if ( ! empty( $args['ui_type'] ) )
			$meta['ui-type'] = $args['ui_type'];
		if ( ! empty( $args['preset_slug'] ) )
			$meta['preset-slug'] = $args['preset_slug'];

		// Save metadata
		update_post_meta( $variant_id, '_it_exchange_variants_addon_variant_meta', $meta );

		// Return the ID
		return $variant_id;
	}
	return false;
}

/**
 * Updates an existing variant
 *
 * Only update post_meta if that's all we're changing
 *
 * @since 1.0.0
 *
 * @param integer  $id    the WP post id for the variant
 * @param array    $args  what we're updating
 * @return boolean
*/
function it_exchange_variants_addon_update_variant( $id, $args ) {
	$defaults = array(
		'post_status'    => 'publish',
		'ping_status'    => 'closed',
		'comment_status' => 'closed',
	);
	$defaults = apply_filters( 'it_exchange_add_variant_defaults', $defaults );

	$args = ITUtility::merge_defaults( $args, $defaults );

	// Set our Post Type and post ID
	$args['post_type'] = 'it_exchange_variant';
	$args['ID']        = $id;

	// Insert Post and get ID
	wp_update_post( $args );

	// Get existing meta
	$meta = get_post_meta( $id, '_it_exchange_variants_addon_variant_meta', true );
	if ( ! empty( $args['image'] ) )
		$meta['image']   = $args['image'];
	if ( ! empty( $args['color'] ) )
		$meta['color']   = $args['color'];
	if ( ! empty( $args['default'] ) )
		$meta['default'] = $args['default'];
	if ( ! empty( $args['ui_type'] ) )
		$meta['ui-type'] = $args['ui_type'];
	if ( ! empty( $args['preset_slug'] ) )
		$meta['preset-slug'] = $args['preset_slug'];

	// Save metadata
	update_post_meta( $id, '_it_exchange_variants_addon_variant_meta', $meta );

}

/**
 * Get IT_Exchange_Variant_Addon_Variant(s)
 *
 * @since 1.0.0
 * @return array  an array of IT_Exchange_Variant_Preset objects
*/
function it_exchange_variants_addon_get_variants( $args=array() ) {
	$defaults = array(
		'include_values' => true,
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	);
	$args = wp_parse_args( $args, $defaults );

	// If we are only include post_parents, set post_parent to 0
	if ( isset( $args['include_parents'] ) && false === $args['include_parents'] ) // isset is used rather than empty() because it could be set to 0
		$args['post_parent'] = 0;

	$args['post_type']  = 'it_exchange_variant';
	$args['meta_query'] = empty( $args['meta_query'] ) ? array() : $args['meta_query'];

	$variant_presets = false;

	if ( $variants = get_posts( $args ) ) {
		$return_variants = array();
		foreach( $variants as $key => $variant ) {
			$variant_object = it_exchange_variants_addon_get_variant( $variant );

			$return_variants[$variant_object->get_property( 'ID' )] = $variant_object;
		}
	}
	return apply_filters( 'it_exchange_variants_addon_get_variants', $return_variants, $args );
}

/**
 * Retreives a variant object by passing it the WP post object or post id
 *
 * @since 1.0.0
 * @param mixed $post  post object or post id
 * @rturn object IT_Exchange_Variant_Addon_Variant object for passed post
*/
function it_exchange_variants_addon_get_variant( $post ) {
	include_once( 'class.variant.php' );
    $variant = new IT_Exchange_Variants_Addon_Variant( $post );
    if ( $variant->ID )
        return apply_filters( 'it_exchange_variants_addon_get_variant', $variant, $post );
    return apply_filters( 'it_exchange_variants_addon_get_variant', false, $post );
}
