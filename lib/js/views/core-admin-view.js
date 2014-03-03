var itExchangeVariantsAdmin = itExchangeVariantsAdmin || {};

(function($){

	'use strict';

	// Core admin variants view
	itExchangeVariantsAdmin.coreAdminView = Backbone.View.extend({

		// Metabox container
		el : function() {
			return $('.inner', '#it-exchange-product-variants');
		},

		template: wp.template( 'it-exchange-admin-variants-container' ), 

		initialize : function() {
			this.listenTo( itExchangeVariantsAdmin.productVariants, 'reset', this.addAllVariants );
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

		render : function(){
			// Empty container
			this.$el.empty();

			// Render
			this.$el.html( this.template );

			console.log( itExchangeVariantsAdmin.productFeatureSettings);
			// Set initial states
			if ( ! itExchangeVariantsAdmin.productFeatureSettings.variantsEnabled ) {
				this.$el.find('.it-exchange-product-variants-inner').addClass('hide-if-js');
			} else {
				this.$el.find('#it-exchange-enable-product-variants').prop('checked', true);
			}
			if ( ! itExchangeVariantsAdmin.hasVariants ) {
				this.$el.find('.it-exchange-existing-variants').addClass('no-variants');
			}

			/* @todo Move this to somewher else. Maybe add a custom event here and listen for it elsewhere */
			itExchangeVariantsAdmin.productVariants.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=product-variants&product-id=' + itExchangeVariantsAdmin.productFeatureSettings.productId;
            itExchangeVariantsAdmin.productVariants.fetch( { reset : true } );
		},

		// Reprint all variants (fires on resest of variants collections)
		addAllVariants : function() {
			this.$variants = $( '.it-exchange-existing-variants' );
			this.$variants.html( '' );
			if ( itExchangeVariantsAdmin.productVariants.length ){
				console.log(this.$variants.length);
				this.$variants.removeClass('no-variants');
			}
			itExchangeVariantsAdmin.productVariants.each( this.addOneVariant, this );
		},

		addOneVariant: function ( variant ) {
			var view = new itExchangeVariantsAdmin.addEditVariantView( { model: variant } );
			this.$variants.append( view.render().el );
		}

	});

}(jQuery));
