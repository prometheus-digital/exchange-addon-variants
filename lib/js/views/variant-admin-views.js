var itExchangeVariants = itExchangeVariants || {};

(function($){

	'use strict';

	/**
	 * Core admin variants view
	*/
	itExchangeVariants.AdminMetaBoxView = Backbone.View.extend({

		// Metabox container
		el : function() {
			return $('.inner', '#it-exchange-product-variants');
		},

		template: wp.template( 'it-exchange-admin-variants-container' ), 

		initialize : function() {

			this.productVariants     = new itExchangeVariants.ProductVariants;

			this.listenTo(this.productVariants, 'reset', this.addAllVariants);
			this.listenTo(this.productVariants, 'add', this.addOneVariant);
		},

		/**
		 * Render the subviews
		*/
		render : function(){
			// Empty container
			this.$el.empty();

			// Render
			this.$el.html( this.template );

			// Fetch and append Product Variants (when variants are reset above, we add it)
			this.productVariants.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=product-variants&product-id=' + itExchangeVariants.productFeatureSettings.productId;
			this.productVariants.fetch({reset:true});

			// Set initial states
			if ( ! itExchangeVariants.productFeatureSettings.variantsEnabled ) {
				this.$el.find('.it-exchange-product-variants-inner').addClass('hide-if-js');
			} else {
				this.$el.find('#it-exchange-enable-product-variants').prop('checked', true);
			}
			if ( ! itExchangeVariants.productFeatureSettings.hasVariants ) {
				this.$el.find('.it-exchange-existing-variants').addClass('no-variants');
			}
		},

		events : {
			'change #it-exchange-enable-product-variants': 'toggleEnableVariants',
			'click .it-exchange-new-variant-add-button a': 'toggleAddVariantDiv',
		},

		toggleEnableVariants : function() {
			$('.it-exchange-product-variants-inner').toggleClass('hide-if-js');
		},

		toggleAddVariantDiv : function(event) {
			if ( $(event.target).hasClass( 'toggle-open') ) { 
				$(event.target).removeClass( 'toggle-open' );
				$( '.it-exchange-new-variant-presets' ).stop().fadeOut();
			} else {
				$(event.target).addClass( 'toggle-open' );
				$( '.it-exchange-new-variant-presets' ).stop().fadeIn();
			}   
		},

		/**
		 * Reprint all variants (fires on resest of variants collections)
		*/
		addAllVariants : function() {

			this.$variants = $( '.it-exchange-existing-variants' );
			this.$variants.html( '' );
			if ( this.productVariants.length ){
				this.$variants.removeClass('no-variants');
			}
			this.productVariants.each( this.addOneVariant, this );
		},

		/**
		 * Receive an instance of a ProductVariant model, pass it to the ProductVariant View
		 * and grab its rendered $el and append it to the list of product variants
		*/
		addOneVariant: function ( variant ) {
			console.log(variant);
			var view = new itExchangeVariants.ProductVariantView( { model: variant } );
			this.$variants.append( view.render().$el );
		}

	});

	itExchangeVariants.ProductVariantView = Backbone.View.extend({

		tagName : 'div',

		className : 'variant',

		template : wp.template( 'it-exchange-admin-variant' ),

		initialize : function() {
		},

		render : function () {
			this.id = 'variant-' + this.model.get('id');
			this.$el.html( this.template( this.model.toJSON() ) );
			return this;
		},

		// Reprint all variant valuess (fires on resest of variants collections)
		addAllVariantValues : function() {
			this.$variantValues = $('.variant-values-list', this.$el);
			this.$variantValues.html('');

			itExchangeVariantsAdmin.productVariantValues.each( this.addOneVariantValue, this );
		},

		addOneVariantValue: function ( variantValue ) {
			var view = new itExchangeVariantsAdmin.addEditVariantValueView( { model: variantValue } );
			this.$variantValues.append( view.render().$el );
		}

	});

	itExchangeVariants.VariantValueView = Backbone.View.extend({

		render : function () {
			this.id = 'variant-' + this.model.get('id');
			this.$el.html( 'THIS IS A VALUE FOR '+this.model.get('title') );
			//this.$el.html( this.template( this.model.toJSON() ) );
			return this;
		}
		
	})
}(jQuery));
