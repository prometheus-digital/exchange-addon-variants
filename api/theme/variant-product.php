<?php
/**
 * Product Variant class extended from the Product THEME API
 *
 * @since 1.0.0
*/
class IT_Theme_API_Product_Extension_For_Variants extends IT_Theme_API_Product {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	*/
	function IT_Theme_API_Product_Extension_For_Variants() {
		parent::__construct();

		// Set GLOBAL variant
		$this->variant = empty( $GLOBALS['it_exchange']['variant'] ) ? false : $GLOBALS['it_exchange']['variant'];
	}   

	/**
	 * Returns product variants
	 *
	 * @since 1.0.0
	*/
	function variants( $options=array() ) { 

		// Return boolean if has flag was set.
		if ( $options['supports'] )
			return it_exchange_product_supports_feature( $this->product->ID, 'variants' );

		// Return boolean if has flag was set
		if ( $options['has'] )
			return it_exchange_product_has_feature( $this->product->ID, 'variants' );

		// If we made it here, we're doing a loop of variants for the current product.
		// This will init/reset the variants global and loop through them. the /api/theme/product-variant.php file will handle individual products.
		if ( empty( $GLOBALS['it_exchange']['variant'] ) ) { 
			$GLOBALS['it_exchange']['variants'] = it_exchange_get_variants_for_product( $this->product->ID );
			//$GLOBALS['it_exchange']['variants'] = it_exchange_get_products( apply_filters( 'it_exchange_store_get_products_args',  array( 'posts_per_page' => -1, 'order' => $settings['store-product-order'], 'orderby' => $settings['store-product-order-by'] ) ) );
			$GLOBALS['it_exchange']['variant'] = reset( $GLOBALS['it_exchange']['variants'] );
			return true;
		} else {
			if ( next( $GLOBALS['it_exchange']['variants'] ) ) { 
				$GLOBALS['it_exchange']['variant'] = current( $GLOBALS['it_exchange']['variants'] );
				return true;
			} else {
				$GLOBALS['it_exchange']['variants'] = array();
				end( $GLOBALS['it_exchange']['variants'] );
				$GLOBALS['it_exchange']['variant'] = false;
				return false;
			}   
		}   
	}   
}
