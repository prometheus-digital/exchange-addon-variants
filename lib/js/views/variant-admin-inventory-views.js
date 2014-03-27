var itExchangeVariants = itExchangeVariants || {};

(function($){

	'use strict';

	/**
	 * Core admin variants view
	*/
	itExchangeVariants.ProductInventoryVariantsMetaBoxView = Backbone.View.extend({

		// Metabox container
		el : function() {
			return $('.inner', '#it-exchange-product-inventory');
		},

		template: wp.template( 'it-exchange-product-inventory-variants-container' ),

		initialize : function() {

			// Init the productVariants collection property. (We'll fetch the variants in the render)
			this.inventoryCombos = new itExchangeVariants.ProductInventoryVariantCombos;
			this.inventoryCombos.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=inventory-combos&product-id=72';

			// Register some listners to fire when productVariants collection is altered
			this.listenTo(this.inventoryCombos, 'reset', this.addAllCombos);
			/*
			this.listenTo(this.productVariants, 'reset', this.makeVariantsSortable);
			this.listenTo(this.productVariants, 'add', this.addOneVariant);
			this.listenTo(this.productVariants, 'add remove', this.toggleEmptyVariants);

			// Register some listners to fire when preset collections are altered
			this.listenTo(this.corePresets, 'reset', this.addAllCorePresets);
			this.listenTo(this.savedPresets, 'reset', this.addAllSavedPresets);

			this.listenTo(this.productVariants, 'remove', this.variantsDataUpdated);
			*/
		},

		/**
		 * Event Handlers
		*/
		events : {
			'change #it-exchange-enable-product-variant-inventory': 'toggleEnableVariantInventory',
			/*
			'click .it-exchange-new-variant-add-button a': 'toggleAddVariantDiv',
			'mouseleave .it-exchange-new-variant-presets': 'fadeOutVariantPresetsPopup',
			'click .it-exchange-variants-preset-template': 'addNewVariantFromTemplate',
			'click .it-exchange-variants-preset-saved'   : 'addNewVariantFromSaved',

			// Only save on product save if we changed something in Variants.
			'click #it-exchange-enable-product-variants' : 'variantsDataUpdated',
			'click .it-exchange-existing-variant'        : 'variantsDataUpdated',
			'change :input'                              : 'variantsDataUpdated',
			'mousedown .variant-title-move'              : 'variantsDataUpdated',
			'click .it-exchange-variants-preset'         : 'variantsDataUpdated',
			*/
		},

		/**
		 * Render the subviews
		*/
		render : function(){
			// Render
			this.$el.append( this.template );
			this.inventoryCombos.fetch({reset:true});
			//console.log(this.inventoryCombos);
		},

		// Shows/Hids the product variants div
		toggleEnableVariantInventory : function() {
			$('.it-exchange-product-inventory-variants-inner').toggleClass('hide-if-js');
		},

		/**
		 * Reprint all variants (fires on resest of variants collections)
		*/
		addAllCombos : function() {
			this.$combos = $('.it-exchange-product-inventory-variants-table');
			this.$combos.html('');

			this.headerTemplate = wp.template('it-exchange-product-inventory-table-header');
			this.footerTemplate = wp.template('it-exchange-product-inventory-table-footer');
			var parents = this.inventoryCombos.first().get('variants');
			parents = _.keys(parents);
			//console.clear();
			//console.log( parents );
			this.$combos.append( this.headerTemplate( { variants: parents} ) );

			if ( this.inventoryCombos.length ){
				//this.$variants.removeClass('no-variants');
				this.inventoryCombos.each( this.addOneCombo, this );
			}
			this.$combos.append( this.footerTemplate() );
		},

		/**
		 * Receive an instance of a ProductVariant model, pass it to the ProductVariant View
		 * and grab its rendered $el and append it to the list of product variants
		*/
		addOneCombo: function ( combo ) {
			var view = new itExchangeVariants.ProductInventoryVariantComboView( { model: combo } );
			this.$combos.append( view.render().$el );
		}

	});

	itExchangeVariants.ProductInventoryVariantComboView = Backbone.View.extend({

		tagName : 'div',

		className : 'inventory-variant-row',

		template : wp.template( 'it-exchange-product-inventory-variants-combo' ),

		/*
		initialize : function() {
			this.listenTo(this.model.values, 'reset', this.addAllVariantValues);
			this.listenTo(this.model.values, 'add', this.addOneVariantValue);
			this.listenTo(this.model.values, 'reset', this.makeVariantValuesSortable);
			this.listenTo(this.model.values, 'reset add remove', this.updateVariantValuesPreview);
			this.listenTo(this.model.values, 'reset add', this.initIThemesColorPicker);
		},
		*/

		render : function () {
			this.$el.html( this.template( this.model.toJSON() ) );
			/*
			this.$el
				.data('variant-id', this.model.get('id'))
				.attr('data-variant-id', this.model.get('id'))
				.data('variant-open', openDiv)
				.attr('data-variant-open', openDiv);
			*/

			return this;
		},

	});
}(jQuery));
