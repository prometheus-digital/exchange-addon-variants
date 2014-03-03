var itExchangeVariantsAdmin = itExchangeVariantsAdmin || {};

(function($){

	'use strict';

	/**
	 * Product Variant
	 * This represents a product variant (without its values/options)
	*/
	itExchangeVariantsAdmin.productVariant = Backbone.Model.extend({
		defaults: {
			id: '',
			title: '',
		},

		initialize: function() {
			//this.values = new itExchangeVariantsAdmin.ProductVariantValues([], { productVariant: this });
		}
	});

	/**
	 * This represents variant option for a specific product variant
	*/
	itExchangeVariantsAdmin.productVariantValue = Backbone.Model.extend({});

}(jQuery));
