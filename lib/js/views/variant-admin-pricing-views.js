var itExchangeVariants = itExchangeVariants || {};

(function($){

	'use strict';

	/**
	 * Core admin variants view
	*/
	itExchangeVariants.ProductPricingVariantsMetaBoxView = Backbone.View.extend({

		// Metabox container
		el : function() {
			return $('.inner', '#it-exchange-product-pricing-variants');
		},

		template: wp.template( 'it-exchange-product-pricing-variants-container' ),

		initialize : function() {

			var post_id = $('#post_ID').val();

			// Init the all product's current variants
			this.productVariants = new itExchangeVariants.ProductVariants;
			this.productVariants.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=product-variant-hierarchy&product-id=' + post_id;
			this.productVariants.fetch({reset:true});

			// Init the productVariants collection property. (We'll fetch the variants in the render)
			this.existingPricingCombos     = new itExchangeVariants.ProductPricingExistingVariantCombos;
			this.existingPricingCombos.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=existing-pricing-combos&product-id=' + post_id;

			// Register some listners to fire when productVariants collection is altered
			this.listenTo(this.productVariants, 'reset', this.render);
			this.listenTo(this.existingPricingCombos, 'reset', this.addExistingCombos);
		},

		/**
		 * Event Handlers
		*/
		events : {
			'click #it-exchange-variant-pricing-create-combo-button' : 'addNewPricingComboDiv',
			'click .it-exchange-variant-pricing-item-title' : 'toggleVariantPricingDiv',
			'click .it-exchange-update-variant-pricing-create-combo-button' : 'updateInvalidPricingCombo',
			'click .delete-variant-price' : 'removeVariantPrice',
			'focusout .it-exchange-product-variant-price' : 'formatPricing'
		},

		/**
		 * Render the subviews
		*/
		render : function(){
			// Render
			this.$el.html('');
			this.$el.append( this.template( { productVariants:this.productVariants.models} ) );
			this.existingPricingCombos.fetch({reset:true});
		},

		/**
		 * Formats pricing in the input field
		*/
		formatPricing: function(event) {
			var element = $(event.currentTarget);
			if ( $(element).data( 'symbol-position') == 'before' )
				$(element).val( $(element).data( 'symbol') + this.numberFormat( $(element).val(), 2, $(element).data( 'decimals-separator' ), $(element).data( 'thousands-separator' ) ) );
			else
				$(element).val( this.numberFormat( $(element).val(), 2, $(element).data( 'decimals-separator' ), $(element).data( 'thousands-separator' ) ) + $(element).data( 'symbol' ) );
		},

		numberFormat: function( number, decimals, dec_point, thousands_sep ) {
			number = (number + '').replace(thousands_sep, ''); //remove thousands
			number = (number + '').replace(dec_point, '.'); //turn number into proper float (if it is an improper float)
			number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
			var n = !isFinite(+number) ? 0 : +number;
			var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
			var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
			var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
			var s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};
			// Fix for IE parseFloat(0.55).toFixed(0) = 0;
			s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
			if (s[0].length > 3) {
				s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
			}
			if ((s[1] || '').length < prec) {
				s[1] = s[1] || '';
				s[1] += new Array(prec - s[1].length + 1).join('0');
			}
			return s.join(dec);
		},

		/**
		 * Deletes a variant price div
		*/
		removeVariantPrice: function(event) {
			event.preventDefault();
			// Fade out and remove
			$(event.currentTarget).closest('.it-exchange-variant-pricing-item').fadeOut(400, function(){
				this.remove();
				if ( !$('.it-exchange-variant-pricing-item').length) {
					$('.it-exchange-variant-pricing-label').addClass('hidden');
				}
			});
		},

		/**
		 * Toggle Variant Pricing Divs when title is clicked
		*/
		toggleVariantPricingDiv: function(event) {
			var parent = $(event.currentTarget).parent();
			var view = this;

			if ( parent.hasClass( 'editing' ) ) { 
				parent.find( '.it-exchange-variant-pricing-item-content' ).stop().slideUp(400, function(){
					parent.removeClass( 'editing' );
					view.updatePricePreviews();
				});
			} else {
				$( '.it-exchange-variant-pricing-item-content' ).stop().slideUp(400, function(){
					$( '.it-exchange-variant-pricing-item' ).removeClass( 'editing' );
					view.updatePricePreviews();
				});

				parent.addClass( 'editing' ).find( '.it-exchange-variant-pricing-item-content' ).stop().slideDown();
			} 
		},

		/**
		 * Updates the price preview by copying from input
		*/
		updatePricePreviews: function(event) {
			$('.it-exchange-variant-pricing-item-content').each(function(){
				var value = $(this).find('.it-exchange-product-variant-price').val();
				$(this).siblings('.it-exchange-variant-pricing-item-title').find('.it-exchange-variant-pricing-item-price-preview').text(value);
			});
		},

		/**
		 * Add new combination div
		*/
		addNewPricingComboDiv : function(event) {
			// Prevent link click from refreshing page
			event.preventDefault();

			// Get hash for current selected combos
			var selectedVariantsforCombo = [];
			var view = this;

			$('.it-exchange-variant-pricing-add-combo-select').each(function(){
				selectedVariantsforCombo.push($(this).val());
			});

			if ( selectedVariantsforCombo.length > 0 ) {
				selectedVariantsforCombo = selectedVariantsforCombo.join('&variants-array[]=');

				$.get( ajaxurl + '?action=it-exchange-variants-json-api&endpoint=get-atts-from-raw-combo&include-currency-data=true&variants-array[]=' + selectedVariantsforCombo, function(result) {

					result = $.parseJSON(result);

					// Grab teh div for variant pricing
					view.$existingCombos = $('#it-exchange-variant-pricing', view.$el);

					// Set template we're using
					var newPricingComboTemplate = wp.template('it-exchange-product-pricing-variant');

					// Set vars for new field data
					var data = {
						productVariants : view.productVariants.models,
						newCombo        : true,
						comboHash       : result.hash,
						title           : result.title,
						value           : result.value,
						allParents      : result.allParents,
						invalidCombo    : result.invalidCombo,
						symbol          : result.symbol,
						symbolPosition  : result.symbolPosition,
						thousandsSep    : result.thousandsSep,
						decimalsSep     : result.decimalsSep
					};

					if ( data.allParents ) {
						// If combos were all parants ('Any [variant name]'), print error message
						$('.it-exchange-variant-pricing-item-not-valid-combo').show().delay(5000).fadeOut();
					} else if ( $('.it-exchange-variant-pricing-item-' + data.comboHash).length ) {
						// If combo already exists, print error message
						$('.it-exchange-variant-pricing-item-already-exists').show().delay(5000).fadeOut();
					} else {
						// Prepend the template to the existing
						$('.it-exchange-variant-pricing-item-content', view.$existingCombos).slideUp().parent().removeClass('editing');
						view.$existingCombos.prepend(newPricingComboTemplate(data));
						$('.it-exchange-variant-pricing-label').removeClass('hidden');
					}
				});
			}
		},

		/**
		 * Reprint all variants (fires on resest of variants collections)
		*/
		addExistingCombos : function() {
			this.$existingCombos = $('#it-exchange-variant-pricing', this.$el);
			this.$existingCombos.html('');

			if ( this.existingPricingCombos.length ) {
				$('.it-exchange-variant-pricing-label').removeClass('hidden');
				this.existingPricingCombos.each( this.addOneCombo, this );
			}
		},

		/**
		 * Receive an instance of a ProductVariant model, pass it to the ProductVariant View
		 * and grab its rendered $el and append it to the list of product variants
		*/
		addOneCombo: function ( combo ) {
			// Set template we're using
			var pricingComboTemplate = wp.template('it-exchange-product-pricing-variant');

			// Set vars for new field data
			var data = {
				productVariants : this.productVariants.models,
				comboHash       : combo.get('hash'),
				title           : combo.get('title'),
				value           : combo.get('value'),
				cssID           : combo.get('cssID'),
				allParents      : combo.get('allParents'),
				invalidCombo    : combo.get('invalidCombo'),
				newCombo        : false,
				symbol          : combo.get('symbol'),
				symbolPosition  : combo.get('symbolPosition'),
				thousandsSep    : combo.get('thousandsSep'),
				decimalsSep     : combo.get('decimalsSep')

			};
			this.$existingCombos.append(pricingComboTemplate(data));

			/*
			THE CORRECT WAY TO DO THIS IS WITH ITS OWN VIEW. WE'LL HAVE TO COME BACK TO THAT
			var view = new itExchangeVariants.ProductPricingVariantComboView( { model: combo } );
			this.$combos.append( view.render().$el );
			*/
		},

		/**
		 * Manages the UPDATE Product Pricing Variants UI
		*/
		updateInvalidPricingCombo: function(event) {
			event.preventDefault();
			var $combo  = $(event.currentTarget).closest('.it-exchange-variant-pricing-item');
			var oldHash = $combo.attr('data-it-exchange-combo-hash');
			var oldHashRegExEscaped = oldHash.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
			var selectedVariantsforCombo = [];

			// Grab values of selects
			$('.it-exchange-variant-pricing-add-combo-select', $combo).each(function(){
				selectedVariantsforCombo.push($(this).val());
			});

			if ( selectedVariantsforCombo.length > 0 ) {
				selectedVariantsforCombo = selectedVariantsforCombo.join('&variants-array[]=');

				$.get( ajaxurl + '?action=it-exchange-variants-json-api&endpoint=get-atts-from-raw-combo&variants-array[]=' + selectedVariantsforCombo, function(result) {

					result = $.parseJSON(result);

					if ( result.hash ) {

						if ( result.allParents ) {
							// If combos were all parants ('Any [variant name]'), print error message
							$('.it-exchange-update-variant-pricing-item-not-valid-combo', $combo).show().delay(5000).fadeOut();
						} else {
							// Remove the invalid class
							$combo.removeClass('it-exchange-variant-pricing-item-invalid');

							// Update the title
							$combo.find('.it-exchange-variant-pricing-item-title-text').text(result.title);

							// Show the Pricing UI
							$('.pricing-ui', $combo).removeClass('hidden');

							// Hide the combo select UI
							$('.it-exchange-select-update-variant-pricing-combo', $combo).addClass('hidden');

							// Disable the lock field
							$('.it-exchange-variant-pricing-lock', $combo).prop('disabled', true);

							// Replace all instances of the old hash with the new hash
							$('#it-exchange-variant-pricing').each(function(){
								var html = $(this).html();
								$(this).html(html.replace(new RegExp(oldHashRegExEscaped, 'g'), result.hash));;
							});
						}
					}
				});
			}
		}
	});

	itExchangeVariants.ProductPricingVariantComboView = Backbone.View.extend({

		tagName : 'div',

		className : 'pricing-variant-row',

		template : wp.template( 'it-exchange-product-pricing-variants-combo' ),

		render : function () {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.$el
				.addClass('pricing-variant-row-' + this.model.get('hash'));

			return this;
		},

	});

}(jQuery));
