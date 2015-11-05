<?php
/**
 * Variant class for THEME API
 *
 * @since 0.4.0
*/

class IT_Theme_API_Variant implements IT_Theme_API {

	/**
	 * API context
	 * @var string $_context
	 * @since 0.4.0
	*/
	private $_context = 'variant';

	/**u
	 * Maps api tags to methods
	 * @var array $_tag_map
	 * @since 0.4.0
	*/
	var $_tag_map = array(
		'title'   => 'title',
		'id'      => 'id',
		'type'    => 'type',
		'values'  => 'values',
		'default' => 'default_value',
	);

	/**
	 * Current product in iThemes Exchange Global
	 * @var object $product
	 * @since 0.4.0
	*/
	public $variant;

	/**
	 * Constructor
	 *
	 * @since 0.4.0
	 *
	 * @return void
	*/
	function __construct() {
		// Set the current global product as a property
		$this->variant = empty( $GLOBALS['it_exchange']['variant'] ) ? false : $GLOBALS['it_exchange']['variant'];
	}

	/**
	 * Deprecated Constructor
	 *
	 * @since 0.4.0
	 *
	 * @return void
	*/
	function IT_Theme_API_Variant() {
		self::__construct();
	}

	/**
	 * Returns the context. Also helps to confirm we are an iThemes Exchange theme API class
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
		$title    = $this->variant->title;

		$defaults = array(
			'wrap'   => 'h3',
			'format' => 'html',
		);
		$options = wp_parse_args( $options, $defaults );

		if ( 'html' == $options['format'] )
			$result .= '<' . $options['wrap'] . ' class="variant-title">';

		$result .= $title;

		if ( 'html' == $options['format'] )
			$result .= '</' . $options['wrap'] . '>';

		return $result;
	}

    function id( $options=array() ) {
        return empty( $this->variant->ID ) ? false : $this->variant->ID;
    }

	/**
	 * The variant typee
	 *
	 * @since 0.4.0
	 * @return string
	*/
	function type( $options=array() ) {
		// Return boolean if has flag was set
		if ( $options['supports'] )
			return true;

		// Return boolean if has flag was set
		if ( $options['has'] )
			return true;

		return $this->variant->ui_type;
	}

	/**
	 * Returns the default value for this variant
	 *
	 * @since 1.0.0
	 * @return string
	*/
	function default_value( $options=array() ) {
		if ( $options['supports'] )
			return true;

		// Find default setting and confirm it exists in values
		$default       = empty( $this->variant->default ) ? false : $this->variant->default;
		$default_value = false;
		foreach( (array) $this->variant->values as $key => $value ) {
			if ( ! empty( $value->ID ) && $value->ID == $default ) {
				$default_value = $value;
				$default_value_index = $key;
				break;
			}
		}

		$defaults = array(
			'format'   => 'text',
			'property' => 'ID',
		);
		$options = wp_parse_args( $options, $defaults );

		switch( $options['property'] ) {
			case 'ID' :
			case 'id' :
				$return = $default_value->ID;
				break;
			case 'title' :
				$return = empty( $default_value->post_title ) ? false : $default_value->post_title;
				break;
			case 'object' :
				$return = empty( $default_value ) ? false : $default_value;
				break;
			case 'values-index' :
				$return = empty( $default_value_index ) ? false : $default_value_index;
				break;
			default :
				$return = false;
		}

		if ( $options['has'] )
			return ! empty( $return );

		return $return;
	}

	function values( $options=array() ) {
        // Return boolean if has flag was set.
        if ( $options['supports'] )
            return true;

        // Return boolean if has flag was set
        if ( $options['has'] )
            return ! empty( $this->variant->values );

		if ( empty( $this->variant->values ) ) {
			$GLOBALS['it_exchange']['variant_values'] = false;
			return false;
		}

        // If we made it here, we're doing a loop of variant-valuess for the current variant.
        // This will init/reset the variant_values global and loop through them. the /api/theme/variant-values.php file will handle individual products.
        if ( empty( $GLOBALS['it_exchange']['variant_value'] ) ) {
            $GLOBALS['it_exchange']['variant_values'] = $this->variant->values;
            $GLOBALS['it_exchange']['variant_value'] = reset( $GLOBALS['it_exchange']['variant_values'] );
            return true;
        } else {
            if ( next( $GLOBALS['it_exchange']['variant_values'] ) ) {
                $GLOBALS['it_exchange']['variant_value'] = current( $GLOBALS['it_exchange']['variant_values'] );
                return true;
            } else {
                $GLOBALS['it_exchange']['variant_values'] = array();
                end( $GLOBALS['it_exchange']['variant_values'] );
                $GLOBALS['it_exchange']['variant_value'] = false;
                return false;
            }
        }
    }
}
