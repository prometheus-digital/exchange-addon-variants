var itExchangeVariants = itExchangeVariants || {};

(function($){
	'use strict';
	/**
	 * Product Pricing Variant Combination Collection
	*/
	itExchangeVariants.ProductPricingExistingVariantCombos = Backbone.Collection.extend({
		model: itExchangeVariants.ProductPricingExistingVariantCombo
	});
}(jQuery));
