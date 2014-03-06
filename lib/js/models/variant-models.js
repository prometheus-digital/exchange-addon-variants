var itExchangeVariants = itExchangeVariants || {};

(function($){

	'use strict';

	/**
	 * Product Variant
	 * This represents a product variant (without its values/options)
	*/
	itExchangeVariants.ProductVariant = Backbone.Model.extend({
		defaults: {
			id:            '',
			title:         '',
			order:         0,
			uiType:        '',
			presetSlug:    '',
			valuesPreview: '',
			values:        []
		},

		initialize: function() {
			this.values     = new itExchangeVariants.VariantValues;
			this.values.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=variant-values&product-variant=' + this.get('id');
			//this.productVariants.on('reset add remove', this.updateValuesPreview);
		},

		/**
		 * Updates the preview
		*/
		updateValuesPreview: function() {
			this.set('valuesPreview', 'updated values preview');
		}
	});

	/**
	 * This represents variant option for a specific product variant
	*/
	itExchangeVariants.VariantValue = Backbone.Model.extend({});

}(jQuery));
