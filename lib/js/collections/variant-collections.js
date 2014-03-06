var itExchangeVariants = itExchangeVariants || {};

(function($){
	'use strict';

	/**
	 * Variants Collection
	 * Does not include variant values
	*/
	itExchangeVariants.ProductVariants = Backbone.Collection.extend({
		model: itExchangeVariants.ProductVariant
	}),

	/**
	 * Variant Values Collection
	*/
	itExchangeVariants.VariantValues = Backbone.Collection.extend({
		model: itExchangeVariants.VariantValue
	});
}(jQuery));
