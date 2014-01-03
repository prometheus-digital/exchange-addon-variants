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
	$existing_presets    = it_exchange_variants_addon_get_presets( array( 'core' => true ) );

	// Loop through preset args and add, update or skip each preset
	foreach( $core_presets_args as $preset ) {
		// Don't create it if it was already deleted by the store owner
		if ( ! empty( $addon_settings['deleted-core-presets'][$preset['slug']] ) )
			continue;

		// Don't create it if it already exists, see if we need to update it
		if ( ! empty( $existing_presets[$preset['slug'] ) ) {
			if ( it_exchange_variants_addon_core_preset_needs_updated( $preset ) )
				it_exchange_variants_addon_update_core_preset( $preset );

			// Move on to next preset
			continue;
		}

		// If we made it here, we need to add the preset
		it_exchange_variants_addon_create_core_preset( $preset );
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
		'blank'       => array(
			'slug'    => 'blank',
			'image'   => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/images/presets/blank.png' ),
			'title'   => __( 'Blank', 'LION' ),
			'values'  => array(),
			'default' => false,
			'order'   => 0,
			'core'    => true,
			'version' => '1.0.0',
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
			),
			'default' => false,
			'order'   => 5,
			'core'    => true,
			'version' => '1.0.0',
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
			'version' => '1.0.0',
		),
	);
}
