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

			// Init the productVariants collection property. (We'll fetch the variants in the render)
			this.missingCombos = new itExchangeVariants.ProductImagesVariantMissingCombos;
			this.missingCombos.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=missing-images-combos&product-id=' + post_id;

			// Register some listners to fire when productVariants collection is altered
			this.listenTo(this.productVariants, 'reset', this.render);
			this.listenTo(this.existingImageCombos, 'reset', this.addExistingCombos);
		//	this.listenTo(this.missingCombos, 'reset', this.addAllMissingCombos);
		},

		/**
		 * Event Handlers
		*/
		events : {
			'click #it-exchange-variant-images-create-combo-button' : 'addNewImageComboDiv',
			'click .it-exchange-variant-image-item-title' : 'toggleVariantImageDiv',
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
						variantImages   : false
					};

					// Prepend the template to the existing
					$('.it-exchange-variant-image-item-content', that.$existingCombos).slideUp().parent().removeClass('editing');
					that.$existingCombos.prepend(newImageComboTemplate(data));

					// Make new item droppable
					$('.it-exchange-variant-feature-image-' + data.comboHash).droppable( it_exchange_feature_droppable );
					$('.it-exchange-variant-feature-image-' + data.comboHash).droppable('option', 'accept', '.it-exchange-gallery-images-' + data.comboHash + ' li');
					$('.it-exchange-gallery-images-' + data.comboHash).sortable( it_exchange_gallery_sortable );
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
				this.headerTemplate = wp.template('it-exchange-product-images-table-header');
				this.footerTemplate = wp.template('it-exchange-product-images-table-footer');
				var parents = this.existingImageCombos.first().get('variants');
				parents = _.keys(parents);
				this.$combos.append( this.headerTemplate( { variants: parents, version: this.existingImageCombos.first().get('version') } ) );

				this.existingImageCombos.each( this.addOneCombo, this );

				this.$combos.append( this.footerTemplate() );
				this.missingCombos.fetch({reset:true});
			}
		},

		/**
		 * Receive an instance of a ProductVariant model, pass it to the ProductVariant View
		 * and grab its rendered $el and append it to the list of product variants
		*/
		addOneCombo: function ( combo ) {
			var view = new itExchangeVariants.ProductImagesVariantComboView( { model: combo } );
			this.$combos.append( view.render().$el );
		},

		/**
		 * Reprint all variants (fires on resest of variants collections)
		*/
		addAllMissingCombos : function() {


			if ( ! this.missingCombos.length ) {
				$('#it-exchange-images-variants-version').removeProp('disabled');
			} else {
				this.$missingCombos = $('.it-exchange-product-images-variants-missing-table');
				this.$missingCombos.html('');

				var parents = this.missingCombos.first().get('variants');
				parents = _.keys(parents);

				this.missingCombos.each( this.addOneMissingCombo, this );
			}
		},

		/**
		 * Receive an instance of a ProductVariant model, pass it to the ProductVariant View
		 * and grab its rendered $el and append it to the list of product variants
		*/
		addOneMissingCombo: function ( combo ) {
			combo.set( 'existingVariants', this.existingImageCombos.models );
			var view = new itExchangeVariants.ProductImagesVariantMissingComboView( { model: combo } );
			this.$missingCombos.append( view.render().$el );
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

	itExchangeVariants.ProductImagesVariantMissingComboView = Backbone.View.extend({

		tagName : 'div',

		className : 'images-variant-missing-row',

		template : wp.template( 'it-exchange-product-images-variants-missing-combo' ),

		/**
		 * Event Handlers
		*/
		events : {
			'click .notification-transfer': 'toggleNotificationTransferDialogs',
			'click .notification-discard': 'removeMissingComboDiv',
			'click .transfer-cancel': 'toggleNotificationTransferDialogs',
			'click .transfer-save': 'applyMissingimagesToExistingImages',
		},

		render : function () {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.$el
				.addClass('images-variant-missing-row-' + this.model.get('hash'));

			return this;
		},

		toggleNotificationTransferDialogs: function(event) {
			event.preventDefault();
			this.$('.missing-images-notification-dialog').toggleClass('hidden');
			this.$('.existing-images-variant-transfer-dialog').toggleClass('hidden');
		},

		applyMissingImagesToExistingImages: function(event) {
			event.preventDefault();
			var that = this;
			$('.existing-images-variant-checkbox:checked', this.$el).each(function() {
				var hash  = $(this).attr('data-hash');
				var value = this.value;

				// Remove all other instances of these checboxes
				$('.existing-images-variant-checkbox-' + hash + '-label').remove();

				// Apply value to any new combos they checked
				$('.images-variant-input-' + hash).val(value);
			});

			// Remove this missing div
			that.removeMissingComboDiv(event);

			// Check count of remaining missing combos. If it is zero, remove the disable property from the version and unlock the updates
			if ( ! $('.images-variant-missing-row').length ) {
				$('#it-exchange-images-variants-version').removeProp('disabled');
			}
		},

		removeMissingComboDiv: function(event) {
			event.preventDefault();
			this.$el.slideUp().remove();
		}
	});
}(jQuery));
