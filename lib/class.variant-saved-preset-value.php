<?php
/**
 * This file holds the class for an iThemes Exchange Variant Saved Preset Value
 *
 * @package IT_Exchange
 * @since 1.0.0
*/

/**
 * Data for preset
 *
 * @since 1.0.0
*/
class IT_Exchange_Variants_Addon_Saved_Preset_Value {

	// WP Post Type Properties
	var $ID;
	var $post_title;
	var $post_name;
	var $menu_order;

	/**
	 * @var string $slug The 'slug' arg passed to it_exchange_variants_addon_create_variant_preset()
	 * @since 1.0.0
	*/
	var $slug;

	/**
	 * @var string $image The variant image used to select it on the frontend. Optional
	 * @since 1.0.0
	*/
	var $image;

	/**
	 * @var string $color The variant color used to select it on the frontend. Optional
	 * @since 1.0.0
	*/
	var $color;

	/**
	 * @var string $title Alias of post_title. The 'title' arg passed to it_exchange_variants_addon_create_variant_preset()
	 * @since 1.0.0
	*/
	var $title;

	/**
	 * @var integer $order An alias of menu_order. The 'order' arg passed to it_exchange_variants_addon_create_variant_preset()
	 * @since 1.0.0
	*/
	var $order = 0;

	/**
	 * Constructor. Loads post data and variant preset data
	 *
	 * @since 1.0.0
	 * @param mixed $post  wp post id or post object. optional.
	 * @return void
	*/
	function __construct( $args ) {
		// Return a WP Error if we don't have the $post object by this point
		if ( empty( $args['title'] ) || empty( $args['slug'] ) )
			return new WP_Error( 'it-exchange-variant-saved-preset-value-formatted-incorrectly', __( 'The IT_Exchange_Variants_Addon_Saved_Preset_Value class must have a slug and title in its constructor args', 'LION' ) );

		$this->args = $args;

		// Setup the properties
		$this->init_properties();
	}

	/**
	 * Deprecated Constructor. Loads post data and variant preset data
	 *
	 * @since 1.0.0
	 * @param mixed $post  wp post id or post object. optional.
	 * @return void
	*/
	function IT_Exchange_Variants_Addon_Saved_Preset_Value( $args ) {
		self::__construct();
	}

	/**
	 * Sets all the variant preset properties
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function init_properties() {
		// Set various properties
		$this->ID = uniqid();
		$this->set_slug();
		$this->set_image();
		$this->set_color();
		$this->set_title();
		$this->set_order();
	}

	/**
	 * Sets the slug var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_slug() {
		$this->slug = $this->post_name = $this->args['slug'];
	}

	/**
	 * Sets the image var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_image() {
		$this->image = empty( $this->args['image'] ) ? false : $this->args['image'];
	}

	/**
	 * Sets the color var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_color() {
		$this->color = empty( $this->args['color'] ) ? false : $this->args['color'];
	}

	/**
	 * Sets the title var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_title() {
		$this->title = $this->post_title = $this->args['title'];
	}

	/**
	 * Sets the order var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_order() {
		$this->order = $this->menu_order = empty( $this->args['order'] ) ? 0 : $this->args['order'];
	}

	/**
	 * Get a property
	 *
	 * @since 1.0.0
	 *
	 * @param string $property
	 * @return mixed
	*/
	function get_property( $property ) {
		if ( empty( $this->$property ) )
			return new WP_Error( 'property-not-found', sprintf( __( 'Coding Error: You requested a property that does not exist from a IT_Exchange_Variants_Addon_Saved_Preset object: %s.', $property ), 'LION' ) );

		return apply_filters( 'it_exchange_variants_addon_get_variant_preset_property', $this->$property, $property, $this );
	}
}
