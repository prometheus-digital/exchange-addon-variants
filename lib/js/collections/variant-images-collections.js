var itExchangeVariants = itExchangeVariants || {};

(function($){
	'use strict';
	/**
	 * Product Images Variant Combination Collection
	*/
	itExchangeVariants.ProductImagesExistingVariantCombos = Backbone.Collection.extend({
		model: itExchangeVariants.ProductImagesExistingVariantCombo
	});
}(jQuery));
