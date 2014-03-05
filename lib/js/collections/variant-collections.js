var itExchangeVariants = itExchangeVariants || {};

(function($){
	'use strict';

	/**
	 * Variants Collection
	 * Does not include variant values
	*/
	itExchangeVariants.ProductVariants = Backbone.Collection.extend({
		model: itExchangeVariants.ProductVariant,
		initialize: function() {
			console.log('made it to ProductVariants collection init');
		}
	}),

	/**
	 * Variant Values Collection
	*/
	itExchangeVariants.ProductVariantValues = Backbone.Collection.extend({
		model: itExchangeVariants.VariantValue,
	});
}(jQuery));
