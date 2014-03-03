var itExchangeVariantsAdmin = itExchangeVariantsAdmin || {};

(function($){
	'use strict';

	/**
	 * Variants Collection
	 * Does not include variant values
	*/
	var productVariants = Backbone.Collection.extend({
		model: itExchangeVariantsAdmin.productVariant,

		initialize: function() {
			console.log('inside collection');
			//this.on('reset', this.getValues, this);
		},
		getValues: function() {
			this.each(function( productVariant ) {
				productVariant.values = new productVariantValues( [], { productVariant: productVariant } ); 
				productVariant.values.fetch();
			});
		}
	}),

	/**
	 * Variant Values Collection
	*/
	productVariantValues = itExchangeVariantsAdmin.ProductVariantValues = Backbone.Collection.extend({
		model: itExchangeVariantsAdmin.productVariantValue,

		// Set the parent variant ID on init
		initialize: function( models, options ) {
			this.productVariant = options.productVariant;
		},

		// Set the URL used to fetch variant values for the variant
		url: function() {
			var productId        = $('#post_ID').val();
			var productVariantId = this.productVariant.get('id');
			return ajaxurl + '?action=it-exchange-variants-json-api&endpoint=product-variant-values&product-variant=' + productVariantId;
		}
	});

	itExchangeVariantsAdmin.productVariants = new productVariants();

}(jQuery));
