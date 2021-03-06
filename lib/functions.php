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

	/** @todo Cleanup beta data. Delete these 3 lines near launch */
	$addon_settings['deleted-core-presets']['ithemes-colors'] = true;
	if ( $colors_id = $GLOBALS['wpdb']->get_var('SELECT ID FROM ' . $GLOBALS['wpdb']->posts . ' WHERE post_name = "ithemes-colors"') )
		wp_delete_post( $colors_id, true );

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
			'title'    => __( 'Dropdown', 'LION' ),
			'values'  => array(
				0 => array(
					'slug'    => 'new_default_1',
					'title'   => __( 'Option 1', 'LION' ),
					'order'   => 0,
				),
				1 => array(
					'slug'    => 'new_default_2',
					'title'   => __( 'Option 2', 'LION' ),
					'order'   => 1,
				),
			),
			'default'  => 'new_default_1',
			'order'    => 0,
			'core'     => true,
			'ui-type'  => 'select',
			'version'  => '0.0.31',
		),
		'template-radio' => array(
			'slug'     => 'template-radio',
			'title'    => __( 'Radio', 'LION' ),
			'values'  => array(
				0 => array(
					'slug'    => 'new_default_1',
					'title'   => __( 'Radio 1', 'LION' ),
					'order'   => 0,
				),
				1 => array(
					'slug'    => 'new_default_2',
					'title'   => __( 'Radio 2', 'LION' ),
					'order'   => 1,
				),
			),
			'default'  => 'new_default_1',
			'order'    => 3,
			'core'     => true,
			'ui-type'  => 'radio',
			'version'  => '0.0.31',
		),
		'tempalte-hex'   => array(
			'slug'     => 'template-hex',
			'title'    => __( 'Color', 'LION' ),
			'values'  => array(
				0 => array(
					'slug'    => 'new_default_1',
					'title'   => __( 'Color 1', 'LION' ),
					'color'   => '#0029BA',
					'order'   => 0,
				),
				1 => array(
					'slug'    => 'new_default_2',
					'title'   => __( 'Color 2', 'LION' ),
					'color'   => '#0082CA',
					'order'   => 1,
				),
			),
			'default'  => 'new_default_1',
			'order'    => 5,
			'core'     => true,
			'ui-type'  => 'color',
			'version'  => '0.0.31',
		),
		'tempalte-image' => array(
			'slug'     => 'template-image',
			'title'    => __( 'Image', 'LION' ),
			'values'  => array(
				0 => array(
					'slug'    => 'new_default_1',
					'title'   => __( 'Image 1', 'LION' ),
					'order'   => 0,
				),
				1 => array(
					'slug'    => 'new_default_2',
					'title'   => __( 'Image 2', 'LION' ),
					'order'   => 1,
				),
			),
			'default'  => 'new_default_1',
			'order'    => 8,
			'core'     => true,
			'ui-type'  => 'image',
			'version'  => '0.0.31',
		),
		'ithemes-sizes'  => array(
			'slug'    => 'ithemes-sizes',
			'title'   => __( 'Sizes', 'LION' ),
			'values'  => array(
				's'   => array(
					'slug'  => 's',
					'title' => __( 'S', 'LION' ),
					'order' => 1,
				),
				'm'   => array(
					'slug'  => 'm',
					'title' => __( 'M', 'LION' ),
					'order' => 2,
				),
				'l'   => array(
					'slug'  => 'l',
					'title' => __( 'L', 'LION' ),
					'order' => 3,
				),
				'xl'  => array(
					'slug'  => 'xl',
					'title' => __( 'XL', 'LION' ),
					'order' => 4,
				),
			),
			'default' => 's',
			'order'   => 0,
			'core'    => true,
			'ui-type' => 'select',
			'version' => '0.0.31',
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
		'post_status'    => 'publish',
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
	$post_args['post_status']  = $args['post_status'];
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

	$return_variants = array();
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
function it_exchange_variants_addon_get_variant( $post, $break_cache=false ) {

	$post_id = empty( $post->ID ) ? (int) $post : $post->ID;

	if ( ! isset( $GLOBALS['it_exchange']['variants_cache'][$post_id] ) || ! empty( $break_cache ) ) {
		include_once( 'class.variant.php' );
		$variant = new IT_Exchange_Variants_Addon_Variant( $post );
		if ( $variant->ID )
			$result = apply_filters( 'it_exchange_variants_addon_get_variant', $variant, $post );
		else
			$result = apply_filters( 'it_exchange_variants_addon_get_variant', false, $post );

		$GLOBALS['it_exchange']['variants_cache'][$post_id] = $result;
	}

	return $GLOBALS['it_exchange']['variants_cache'][$post_id];
}

function it_exchange_addon_get_selected_variant_alts( $selected_combo, $product_id ) {
	$alts = array();
	$all_possible = it_exchange_variants_addon_get_all_variant_combos_for_product( $product_id, true, $selected_combo );
	foreach( $all_possible as $combo ) {
		$atts = it_exchange_get_variant_combo_attributes( $combo );
		if ( ! empty( $atts['hash'] ) )
			$alts[] = $atts['hash'];
	}
	return $alts;
}

/**
 * Shows the nag when needed.
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_variants_addon_show_version_nag() {
	if ( version_compare( $GLOBALS['it_exchange']['version'], '1.8.0', '<' ) ) {
        ?>
        <div id="it-exchange-add-on-min-version-nag" class="it-exchange-nag">
            <?php printf( __( 'The Product Variants add-on requires ExchangeWP version 1.8.0 or greater. %sPlease upgrade Exchange%s.', 'LION' ), '<a href="' . admin_url( 'update-core.php' ) . '">', '</a>' ); ?>
        </div>
        <script type="text/javascript">
            jQuery( document ).ready( function() {
                if ( jQuery( '.wrap > h2' ).length == '1' ) {
                    jQuery("#it-exchange-add-on-min-version-nag").insertAfter('.wrap > h2').addClass( 'after-h2' );
                }
            });
        </script>
        <?php
    }
}
add_action( 'admin_notices', 'it_exchange_variants_addon_show_version_nag' );
