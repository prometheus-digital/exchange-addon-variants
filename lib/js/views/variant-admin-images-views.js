var itExchangeVariants = itExchangeVariants || {};

(function($){

	'use strict';

	/**
	 * Core admin variants view
	*/
	itExchangeVariants.ProductImagesVariantsMetaBoxView = Backbone.View.extend({

		// Metabox container
		el : function() {
			return $('#it-exchange-product-image-variants');
		},

		template: wp.template( 'it-exchange-product-images-variants-container' ),

		initialize : function() {

			var post_id = $('#post_ID').val();

			// Init the all product's current variants
			this.productVariants = new itExchangeVariants.ProductVariants;
			this.productVariants.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=product-variant-hierarchy&product-id=' + post_id;
			this.productVariants.fetch({reset:true});

			// Init the productVariants collection property. (We'll fetch the variants in the render)
			this.existingImageCombos     = new itExchangeVariants.ProductImagesExistingVariantCombos;
			this.existingImageCombos.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=existing-images-combos&product-id=' + post_id;

			// Register some listners to fire when productVariants collection is altered
			this.listenTo(this.productVariants, 'reset', this.render);
			this.listenTo(this.existingImageCombos, 'reset', this.addExistingCombos);
		},

		/**
		 * Event Handlers
		*/
		events : {
			'click #it-exchange-variant-images-create-combo-button' : 'addNewImageComboDiv',
			'click .it-exchange-variant-image-item-title' : 'toggleVariantImageDiv',
			'click .it-exchange-update-variant-images-create-combo-button' : 'updateInvalidImageCombo',
			//'change #it-exchange-enable-product-variant-images': 'toggleEnableVariantImages',
		},

		/**
		 * Render the subviews
		*/
		render : function(){
			// Render
			this.$el.html('');
			this.$el.append( this.template( { productVariants:this.productVariants.models} ) );
			this.existingImageCombos.fetch({reset:true});
		},

		/**
		 * Toggle Variant Image Divs when title is clicked
		*/
		toggleVariantImageDiv: function(event) {
			var parent = $(event.currentTarget).parent();

			if ( parent.hasClass( 'editing' ) ) { 
				parent.removeClass( 'editing' );

				parent.find( '.it-exchange-variant-image-item-content' ).stop().slideUp();
			} else {
				$( '.it-exchange-variant-image-item-content' ).stop().slideUp();
				$( '.it-exchange-variant-image-item' ).removeClass( 'editing' );

				parent.addClass( 'editing' ).find( '.it-exchange-variant-image-item-content' ).stop().slideDown();
			} 
		},
		/**
		 * Add new combination div
		*/
		addNewImageComboDiv : function(event) {
			// Prevent link click from refreshing page
			event.preventDefault();

			// Get hash for current selected combos
			var selectedVariantsforCombo = [];
			var that = this;

			$('.it-exchange-variant-images-add-combo-select').each(function(){
				selectedVariantsforCombo.push($(this).val());
			});

			if ( selectedVariantsforCombo.length > 0 ) {
				selectedVariantsforCombo = selectedVariantsforCombo.join('&variants-array[]=');

				$.get( ajaxurl + '?action=it-exchange-variants-json-api&endpoint=get-atts-from-raw-combo&variants-array[]=' + selectedVariantsforCombo, function(result) {

					result = $.parseJSON(result);

					// Grab teh div for variant images
					that.$existingCombos = $('#it-exchange-variant-images', that.$el);

					// Set template we're using
					var newImageComboTemplate = wp.template('it-exchange-product-images-variant');

					// Set vars for new field data
					var data = {
						productVariants : that.productVariants.models,
						comboHash       : result.hash,
						title           : result.title,
						imageThumbURL   : '',
						featuredImage   : false,
						variantImages   : false,
						allParents      : result.allParents,
						invalidCombo    : result.invalidCombo
					};

					if ( data.allParents ) {
						// If combos were all parants ('Any [variant name]'), print error message
						$('.it-exchange-variant-image-item-not-valid-combo').show().delay(5000).fadeOut();
					} else if ( $('.it-exchange-variant-image-item-' + data.comboHash).length ) {
						// If combo already exists, print error message
						$('.it-exchange-variant-image-item-already-exists').show().delay(5000).fadeOut();
					} else {
						// Prepend the template to the existing
						$('.it-exchange-variant-image-item-content', that.$existingCombos).slideUp().parent().removeClass('editing');
						that.$existingCombos.prepend(newImageComboTemplate(data));

						// Make new items draggable/droppable
						$('.it-exchange-variant-feature-image-' + data.comboHash).droppable( it_exchange_feature_droppable );
						$('.it-exchange-variant-feature-image-' + data.comboHash).droppable('option', 'accept', '.it-exchange-gallery-images-' + data.comboHash + ' li');
						$('.it-exchange-gallery-images-' + data.comboHash).sortable( it_exchange_gallery_sortable );
					}
				});
			}
		},

		/**
		 * Reprint all variants (fires on resest of variants collections)
		*/
		addExistingCombos : function() {
			this.$existingCombos = $('#it-exchange-variant-images', this.$el);
			this.$existingCombos.html('');

			if ( this.existingImageCombos.length ) {
				this.existingImageCombos.each( this.addOneCombo, this );
			}
		},

		/**
		 * Receive an instance of a ProductVariant model, pass it to the ProductVariant View
		 * and grab its rendered $el and append it to the list of product variants
		*/
		addOneCombo: function ( combo ) {
			// Set template we're using
			var imageComboTemplate = wp.template('it-exchange-product-images-variant');

			// Set vars for new field data
			var data = {
				productVariants : this.productVariants.models,
				comboHash       : combo.get('hash'),
				title           : combo.get('title'),
				featuredImage   : combo.get('featuredImage'),
				variantImages   : combo.get('productImages'),
				cssID           : combo.get('cssID'),
				allParents      : combo.get('allParents'),
				invalidCombo    : combo.get('invalidCombo')

			};
			this.$existingCombos.append(imageComboTemplate(data));

			// Make existing gallery items draggable/droppable
			$('.it-exchange-variant-feature-image-' + data.comboHash).droppable( it_exchange_feature_droppable );
			$('.it-exchange-variant-feature-image-' + data.comboHash).droppable('option', 'accept', '.it-exchange-gallery-images-' + data.comboHash + ' li');
			$('.it-exchange-gallery-images-' + data.comboHash).sortable( it_exchange_gallery_sortable );

			/*
			THE CORRECT WAY TO DO THIS IS WITH ITS OWN VIEW. WE'LL HAVE TO COME BACK TO THAT
			var view = new itExchangeVariants.ProductImagesVariantComboView( { model: combo } );
			this.$combos.append( view.render().$el );
			*/
		},

		/**
		 * Manages the UPDATE Product Image Variants UI
		*/
		updateInvalidImageCombo: function(event) {
			event.preventDefault();
			var $combo  = $(event.currentTarget).closest('.it-exchange-variant-image-item');
			var oldHash = $combo.attr('data-it-exchange-combo-hash');
			var oldHashRegExEscaped = oldHash.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
			var selectedVariantsforCombo = [];

			// Grab values of selects
			$('.it-exchange-variant-images-add-combo-select', $combo).each(function(){
				selectedVariantsforCombo.push($(this).val());
			});

			if ( selectedVariantsforCombo.length > 0 ) {
				selectedVariantsforCombo = selectedVariantsforCombo.join('&variants-array[]=');

				$.get( ajaxurl + '?action=it-exchange-variants-json-api&endpoint=get-atts-from-raw-combo&variants-array[]=' + selectedVariantsforCombo, function(result) {

					result = $.parseJSON(result);

					if ( result.hash ) {

						if ( result.allParents ) {
							// If combos were all parants ('Any [variant name]'), print error message
							$('.it-exchange-update-variant-image-item-not-valid-combo', $combo).show().delay(5000).fadeOut();
						} else {
							// Remove the invalid class
							$combo.removeClass('it-exchange-variant-image-item-invalid');

							// Show the images UI
							$('.images-ui', $combo).removeClass('hidden');

							// Hide the combo select UI
							$('.it-exchange-select-update-variant-images-combo', $combo).addClass('hidden');

							// Disable the lock field
							$('.it-exchange-variant-images-lock', $combo).prop('disabled', true);

							// Replace all instances of the old hash with the new hash
							$('#it-exchange-variant-images').each(function(){
								var html = $(this).html();
								$(this).html(html.replace(new RegExp(oldHashRegExEscaped, 'g'), result.hash));;
							});

							// DOM node changes with hacky search and replace so reattach drag/drop events
							$('.it-exchange-variant-feature-image-' + result.hash ).droppable( it_exchange_feature_droppable );
							$('.it-exchange-variant-feature-image-' + result.hash ).droppable('option', 'accept', '.it-exchange-gallery-images-' + result.hash + ' li');
							$('.it-exchange-gallery-images-' + result.hash ).sortable( it_exchange_gallery_sortable );
						}
					}
				});
			}
		}
	});

	itExchangeVariants.ProductImagesVariantComboView = Backbone.View.extend({

		tagName : 'div',

		className : 'images-variant-row',

		template : wp.template( 'it-exchange-product-images-variants-combo' ),

		render : function () {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.$el
				.addClass('images-variant-row-' + this.model.get('hash'));

			return this;
		},

	});

}(jQuery));
