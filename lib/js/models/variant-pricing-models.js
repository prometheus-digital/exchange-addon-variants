var itExchangeVariants = itExchangeVariants || {};

(function($){
	'use strict';

	/**
	 * Product Variant
	 * This represents a product variant (without its values/options)
	*/
	itExchangeVariants.ProductPricingExistingVariantCombo = Backbone.Model.extend({
		defaults: {
			id:       '',
			hash:     false,
			variants: [],
			value:    '',
			version:  false
		}
	});
}(jQuery));
