<?php
/**
 * This file holds the class for an iThemes Exchange Variant Preset 
 *
 * @package IT_Exchange
 * @since 1.0.0
*/

/**
 * Merges a WP Post with iThemes Exchange Variant Addon Preset data
 *
 * @since 1.0.0
*/
class IT_Exchange_Variants_Addon_Preset {

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
	 * @var string $title Alias of post_title. The 'title' arg passed to it_exchange_variants_addon_create_variant_preset()
	 * @since 1.0.0
	*/
	var $title;

	/**
	 * @var array $values Contains data for all initial variant values. The 'values' arg passed to it_exchange_variants_addon_create_variant_preset()
	 * @since 1.0.0
	*/
	var $values = array();

	/**
	 * @var mixed $default Which variant value (by slug) will be default. The 'default' arg passed to it_exchange_variants_addon_create_variant_preset()
	 * @since 1.0.0
	*/
	var $default = false;

	/**
	 * @var integer $order An alias of menu_order. The 'order' arg passed to it_exchange_variants_addon_create_variant_preset()
	 * @since 1.0.0
	*/
	var $order = 0;

	/**
	 * @var string $ui_type What type of variant UI is this?
	 * @since 1.0.0
	*/
	var $ui_type = 'select';

	/**
	 * @var boolean $core Is this a core preset? The 'core' arg passed to it_exchange_variants_addon_create_variant_preset()
	 * @since 1.0.0
	*/
	var $core = false;

	/**
	 * @var string $version The 'version' arg passed to it_exchange_variants_addon_create_variant_preset()
	 * @since 1.0.0
	*/
	var $version;

	/**
	 * @var array cache of postmeta for this wp post
	 * @since 1.0.0
	*/
	var $postmeta = false;

	/**
	 * @var boolean $is_template Is this preset a template or an actual preset variant (left or right column for new variant button)
	 * @since 1.0.0
	*/
	var $is_template;

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
		if ( 'it_exng_varnt_preset' != get_post_type( $post ) )
			$post = false;

		// Return a WP Error if we don't have the $post object by this point
		if ( ! $post )
			return new WP_Error( 'it-exchange-variant-preset-not-a-wp-post', __( 'The IT_Exchange_Variants_Addon_Preset class must have a WP post object or ID passed to its constructor', 'LION' ) );

		// Grab the $post object vars and populate this objects vars
		foreach( (array) get_object_vars( $post ) as $var => $value ) {
			$this->$var = $value;
		}

		// Setup the properties
		$this->init_properties();
	}

	/**
	 * Constructor. Loads post data and variant preset data
	 *
	 * @since 1.0.0
	 * @param mixed $post  wp post id or post object. optional.
	 * @return void
	*/
	function IT_Exchange_Variants_Addon_Preset( $post=false ) {
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
		$this->set_slug();
		$this->set_image();
		$this->set_title();
		$this->set_values();
		$this->set_default();
		$this->set_order();
		$this->set_ui_type();
		$this->set_core();
		$this->set_version();
		$this->set_is_template();

		unset( $this->postmeta );
	}

	/**
	 * Sets the postmeta var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_variant_postmeta() {
		$this->postmeta = get_post_meta( $this->ID, '_it_exchange_variants_addon_preset_meta', true );
	}

	/**
	 * Sets the slug var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_slug() {
		$this->slug = empty( $this->postmeta['slug'] ) ? $this->post_name : $this->postmeta['slug'];
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
	 * Sets the title var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_title() {
		$this->title = $this->post_title;
	}

	/**
	 * Sets the values var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_values() {
		$this->values = empty( $this->postmeta['values'] ) ? array() : $this->postmeta['values'];
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
	 * Sets the order var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_order() {
		$this->order = $this->menu_order;
	}

	/**
	 * Sets the core var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_core() {
		$this->core = ! empty( $this->postmeta['core'] );
	}

	/**
	 * Sets the version var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_version() {
		$this->version = empty( $this->postmeta['version'] ) ? IT_Exchange_Variants_Addon_Version : $this->postmeta['version'];
	}

	/**
	 * Sets the is_template var
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function set_is_template() {
		return $this->is_template = substr( $this->slug, 0, 9 ) == 'template-';
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
		if ( ! property_exists( 'IT_Exchange_Variants_Addon_Preset', $property ) )
			return new WP_Error( 'property-not-found', __( 'Coding Error: You requested a property that does not exist from a IT_Exchange_Variants_Addon_Preset object.', 'LION' ) );

		return apply_filters( 'it_exchange_variants_addon_get_variant_preset_property', $this->$property, $property, $this );
	}
}
