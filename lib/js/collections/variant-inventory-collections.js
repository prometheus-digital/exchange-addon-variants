var itExchangeVariants = itExchangeVariants || {};

(function($){
	'use strict';

	/**
	 * Inventory Variant Combination Collection
	*/
	itExchangeVariants.ProductInventoryVariantCombos = Backbone.Collection.extend({
		model: itExchangeVariants.ProductInventoryVariantCombo
	});

	/**
	 * Inventory Variant Missing Combination Collection
	*/
	itExchangeVariants.ProductInventoryVariantMissingCombos = Backbone.Collection.extend({
		model: itExchangeVariants.ProductInventoryVariantMissingCombo
	});
}(jQuery));
