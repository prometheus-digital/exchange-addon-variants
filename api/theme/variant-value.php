<?php
/**
 * Variant-value class for THEME API
 *
 * @since 0.4.0
*/

class IT_Theme_API_Variant_Value implements IT_Theme_API {

	/**
	 * API context
	 * @var string $_context
	 * @since 0.4.0
	*/
	private $_context = 'variant-value';

	/**u
	 * Maps api tags to methods
	 * @var array $_tag_map
	 * @since 0.4.0
	*/
	var $_tag_map = array(
		'title'     => 'title',
		'id'        => 'id',
		'image'     => 'image',
		'color'     => 'color',
		'isdefault' => 'is_default',
	);

	/**
	 * Current product in ExchangeWP Global
	 * @var object $product
	 * @since 0.4.0
	*/
	public $variant_value;

	/**
	 * Constructor
	 *
	 * @since 0.4.0
	 *
	 * @return void
	*/
	function __construct() {
		// Set the current global product as a property
		$this->variant_value = empty( $GLOBALS['it_exchange']['variant_value'] ) ? false : $GLOBALS['it_exchange']['variant_value'];
	}

	/**
	 * Deprecated Constructor
	 *
	 * @since 0.4.0
	 *
	 * @return void
	*/
	function IT_Theme_API_Variant_Value() {
		self::__construct();
	}

	/**
	 * Returns the context. Also helps to confirm we are an ExchangeWP theme API class
	 *
	 * @since 0.4.0
	 *
	 * @return string
	*/
	function get_api_context() {
		return $this->_context;
	}

	/**
	 * The product title
	 *
	 * @since 0.4.0
	 * @return string
	*/
	function title( $options=array() ) {

		// Return boolean if has flag was set
		if ( $options['supports'] )
			return true;

		// Return boolean if has flag was set
		if ( $options['has'] )
			return true;

		$result   = '';
		$title    = $this->variant_value->title;

		$defaults = array(
			'wrap'   => 'span',
			'format' => 'html',
		);
		$options = wp_parse_args( $options, $defaults );

		if ( 'html' == $options['format'] )
			$result .= '<' . $options['wrap'] . ' class="variant-value-title">';

		$result .= $title;

		if ( 'html' == $options['format'] )
			$result .= '</' . $options['wrap'] . '>';

		return $result;
	}

	function id( $options=array() ) {
		return empty( $this->variant_value->ID ) ? false : $this->variant_value->ID;
	}

	function image( $options=array() ) {
		return empty( $this->variant_value->image ) ? '' : $this->variant_value->image;
	}

	function color( $options=array() ) {
		return empty( $this->variant_value->color ) ? '' : $this->variant_value->color;
	}

	function is_default( $options=array() ) {
		return ! empty( $GLOBALS['it_exchange']['variant']->default ) && ! empty( $this->variant_value->ID ) && $GLOBALS['it_exchange']['variant']->default == $this->variant_value->ID;
	}
}
