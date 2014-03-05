var itExchangeVariants = itExchangeVariants || {};

(function($){

	'use strict';

	/**
	 * Product Variants Product Feature
	*/
	itExchangeVariants.AdminMetaBox = Backbone.Model.extend({
		/**
		 * Defaults for every metabox
		*/
		defaults: {
			productId:       false,
			variantsEnabled: false,
			hasVariants:     false,
			productVariants: []
		},

		/**
		 * Runs when the AdminMetaBox Model is created
		*/
		initialize: function(){
			console.log('metabox model init');
			this.productVariants     = new itExchangeVariants.ProductVariants;
			this.productVariants.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=product-variants&product-id=' + this.get('productId');
			this.productVariants.on('reset', this.updateHasVariants);
			console.log(this.productVariants);
		},

		/**
		 * Sets the hasVariants property
		*/
		updateHasVariants: function() {
			this.set('hasVariants', !this.productVariants.isEmpty());
		},

	});

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
			/*
			this.values     = new itExchangeVariants.VariantValues([], {productVariant: this});
			this.values.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=variant-values&variant-id=' + this.get('id');
			this.productVariants.on('reset add remove', this.updateValuesPreview);
			*/
			console.log('made it to ProductVariant init for '+this.get('id'));
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
