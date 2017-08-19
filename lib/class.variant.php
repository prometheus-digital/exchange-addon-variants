<?php
/**
 * This file holds the class for an ExchangeWP Variant
 *
 * @package IT_Exchange
 * @since 1.0.0
*/

/**
 * Merges a WP Post with ExchangeWP Variant Addon Variant data
 *
 * @since 1.0.0
*/
class IT_Exchange_Variants_Addon_Variant {

	// WP Post Type Properties
	var $ID;
	var $post_author;
	var $post_date;
	var $post_date_gmt;
	var $post_content;
	var $post_title;
	var $post_excerpt;
	var $post_status;
	var $comment_status;
	var $ping_status;
	var $post_password;
	var $post_name;
	var $to_ping;
	var $pinged;
	var $post_modified;
	var $post_modified_gmt;
	var $post_content_filtered;
	var $post_parent;
	var $guid;
	var $menu_order;
	var $post_type;
	var $post_mime_type;
	var $comment_count;

	var $title;
	/**
	 * @var string $image The variant image used to select it on the frontend. Optional
	 * @since 1.0.0
	*/
	var $image = false;

	/**
	 * @var string $color The variant color used to select it on the frontend. Optional
	 * @since 1.0.0
	*/
	var $color = false;

	/**
	 * @var mixed $default Which variant value (by slug) will be default. The 'default' arg passed to it_exchange_variants_addon_create_variant_preset()
	 * @since 1.0.0
	*/
	var $default = false;

	/**
	 * @var string $ui_type What type of variant UI is this?
	 * @since 1.0.0
	*/
	var $ui_type = false;

	/**
	 * @var string $preset_slug What type of variant UI is this?
	 * @since 1.0.0
	*/
	var $preset_slug = false;

	/**
	 * @var array cache of postmeta for this wp post
	 * @since 1.0.0
	*/
	var $postmeta = false;

	/**
	 * Values
	 * @since 1.0.0
	*/
	var $values = array();

	/**
	 * Constructor. Loads post data and variant preset data
	 *
	 * @since 1.0.0
	 * @param mixed $post  wp post id or post object. optional.
	 * @return void
	*/
	function __construct( $post=false ) {

		// If not an object, try to grab the WP object
		if ( ! is_object( $post ) )
			$post = get_post( (int) $post );

		// Ensure that $post is a WP_Post object
		if ( is_object( $post ) && 'WP_Post' != get_class( $post ) )
			$post = false;

		// Ensure this is a product post type
		if ( 'it_exchange_variant' != get_post_type( $post ) )
			$post = false;

		// Return a WP Error if we don't have the $post object by this point
		if ( ! $post )
			return new WP_Error( 'it-exchange-variant-not-a-wp-post', __( 'The IT_Exchange_Variants_Addon_Variant class must have a WP post object or ID passed to its constructor', 'LION' ) );

		// Grab the $post object vars and populate this objects vars
		foreach( (array) get_object_vars( $post ) as $var => $value ) {
			$this->$var = $value;
		}

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
	function IT_Exchange_Variants_Addon_Variant( $post=false ) {
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
		// Set the product type
		$this->set_variant_postmeta();

		// Set various properties
		$this->title = $this->post_title;
		$this->set_is_variant_value();
		$this->set_image();
		$this->set_color();
		$this->set_default();
		$this->set_ui_type();
		$this->set_preset_slug();

		unset( $this->postmeta );
	}

	/**
	 * Sets if a variant is a variant value or a variant (does it have a post_parent)
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_is_variant_value() {
		$this->is_variant_value = ! empty( $this->post_parent );
	}

	/**
	 * Sets the postmeta var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_variant_postmeta() {
		$this->postmeta = get_post_meta( $this->ID, '_it_exchange_variants_addon_variant_meta', true );
	}

	/**
	 * Sets the image var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_image() {
		$this->image = empty( $this->postmeta['image'] ) ? false : $this->postmeta['image'];
	}

	/**
	 * Sets the color var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_color() {
		$this->color = empty( $this->postmeta['color'] ) ? '#ffffff' : $this->postmeta['color'];
	}

	/**
	 * Sets the default var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_default() {
		$this->default = empty( $this->postmeta['default'] ) ? false : $this->postmeta['default'];
	}

	/**
	 * Sets the ui_type var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_ui_type() {
		$this->ui_type = empty( $this->postmeta['ui-type'] ) ? false : $this->postmeta['ui-type'];
	}

	/**
	 * Sets the preset_slug var
	 *
	 * What preset was this created from?
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_preset_slug() {
		$this->preset_slug = empty( $this->postmeta['preset-slug'] ) ? false : $this->postmeta['preset-slug'];
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
		if ( ! property_exists( 'IT_Exchange_Variants_Addon_Variant', $property ) )
			return new WP_Error( 'property-not-found', __( sprintf( 'Coding Error: You requested a property that does not exist from a IT_Exchange_Variants_Addon_Variant object: %s.', $property ), 'LION' ) );

		return apply_filters( 'it_exchange_variants_addon_get_variant_property', $this->$property, $property, $this );
	}
}
