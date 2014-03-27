var itExchangeVariants = itExchangeVariants || {};

(function($){
	'use strict';
	/**
	 * Inventory Variant Combination Collection
	*/
	itExchangeVariants.ProductInventoryVariantCombos = Backbone.Collection.extend({
		model: itExchangeVariants.ProductInventoryVariantCombo
	});
}(jQuery));
