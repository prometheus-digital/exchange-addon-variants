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

			// Init the productVariants collection property. (We'll fetch the variants in the render)
			this.productVariants = new itExchangeVariants.ProductVariants;

			// Init the core and saved presets collections properties. (We'll fetch them in the render)
			this.corePresets  = new itExchangeVariants.CorePresets;
			this.savedPresets = new itExchangeVariants.SavedPresets;

			// Register some listners to fire when productVariants collection is altered
			this.listenTo(this.productVariants, 'reset', this.addAllVariants);
			this.listenTo(this.productVariants, 'reset', this.makeVariantsSortable);
			this.listenTo(this.productVariants, 'add', this.addOneVariant);
			this.listenTo(this.productVariants, 'add remove', this.toggleEmptyVariants);

			// Register some listners to fire when preset collections are altered
			this.listenTo(this.corePresets, 'reset', this.addAllCorePresets);
			this.listenTo(this.savedPresets, 'reset', this.addAllSavedPresets);
		},

		/**
		 * Event Handlers
		*/
		events : {
			'change #it-exchange-enable-product-variants': 'toggleEnableVariants',
			'click .it-exchange-new-variant-add-button a': 'toggleAddVariantDiv',
			'mouseleave .it-exchange-new-variant-presets': 'fadeOutVariantPresetsPopup',
			'click .it-exchange-variants-preset-template': 'addNewVariantFromTemplate',
			'click .it-exchange-variants-preset-saved'   : 'addNewVariantFromSaved'
		},

		/**
		 * Render the subviews
		*/
		render : function(){
			// Empty container
			this.$el.empty();

			// Render
			this.$el.html( this.template );

			// Fetch and append Product Variants (events registered in init build the views on fetch)
			this.productVariants.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=product-variants&product-id=' + itExchangeVariants.productFeatureSettings.productId;
			this.productVariants.fetch({reset:true});

			// Fetch and append presets (events registered in init build the views on fetch)
			this.corePresets.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=core-presets';
			this.corePresets.fetch({reset:true});
			this.savedPresets.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=saved-presets';
			this.savedPresets.fetch({reset:true});

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

		// Shows/Hids the product variants div
		toggleEnableVariants : function() {
			$('.it-exchange-product-variants-inner').toggleClass('hide-if-js');
		},

		// Shows / Hides the Add New Variant dialog
		toggleAddVariantDiv : function(event) {
			if ( $(event.target).hasClass( 'toggle-open') ) {
				$(event.target).removeClass( 'toggle-open' );
				$( '.it-exchange-new-variant-presets' ).stop().fadeOut();
			} else {
				$(event.target).addClass( 'toggle-open' );
				$( '.it-exchange-new-variant-presets' ).stop().fadeIn();
			}
		},

		// Check for empty variants and perform actions
		toggleEmptyVariants: function() {
			var $existingVariants = this.$('.it-exchange-existing-variants', this.$el);

			// Hide / Show variants div depending on number of variants
			if ( ! this.productVariants.length ) {
				// Hide if no varints
				$existingVariants.addClass('no-variants');
			} else {
				// Show if we have variants
				$existingVariants.removeClass('no-variants');
			}
		},

		/**
		 * Reprint all variants (fires on resest of variants collections)
		*/
		addAllVariants : function() {
			this.$variants = $('.it-exchange-existing-variants');
			this.$variants.html('');

			if ( this.productVariants.length ){
				this.$variants.removeClass('no-variants');
				this.productVariants.each( this.addOneVariant, this );
			}
		},

		/**
		 * Receive an instance of a ProductVariant model, pass it to the ProductVariant View
		 * and grab its rendered $el and append it to the list of product variants
		*/
		addOneVariant: function ( variant ) {
			var view = new itExchangeVariants.ProductVariantView( { model: variant } );

			// Set variants if undefined
			if (typeof this.$variants == 'undefined') {
				this.$variants = $('.it-exchange-existing-variants');
			}

			// If we're creating a variant from a preset, we need a special URL
			if ( variant.get('newFromPreset') ) {
				variant.set('id', variant.cid);
				variant.values.url = ajaxurl + '?action=it-exchange-variants-json-api&endpoint=variant-values-from-preset&preset-id=' + variant.get('newFromPreset') + '&parent-id=' + variant.cid;
			}

			variant.values.fetch({reset:true});
			this.$variants.append( view.render().$el );

			// Reindex all variant order when adding a new variant
			$('.parent-variant-order-input', this.$variants).each(function(index){
				$(this).val(index);
			});
		},

		// Make the Product Variants list sortable
		makeVariantsSortable: function() {
			// Make the existing variants sortable
			$( '.it-exchange-existing-variants' ).sortable({
				placeholder: 'it-exchange-existing-variant sorting-placeholder clearfix',
				start: function( e, ui ) {
					$( '.sorting-placeholder' ).html( ui.item.context.innerHTML );
					$( this ).addClass( 'sorting' );
				},
				stop: function( e, ui ) {
					$( this ).removeClass( 'sorting' );
						$('.parent-variant-order-input', $(this)).each(function(index){
							$(this).val(index);
						});
				}
            });
		},

		/**
		 * Reprint all corePresets (fires on resest of corePresets collections)
		*/
		addAllCorePresets : function() {
			this.$corePresets = $( '.it-exchange-variant-presets-templates .it-exchange-variant-column-inner' );

			this.corePresets.each( this.addOneCorePreset, this );
		},

		/**
		 * Receive an instance of a CorePreset model, pass it to the CorePreset View
		 * and grab its rendered $el and append it to the list of core presets
		*/
		addOneCorePreset: function ( corePreset ) {
			var view = new itExchangeVariants.CorePresetView( { model: corePreset} );
			this.$corePresets.append( view.render().$('.it-exchange-variants-preset',this.$el)); // We're cheating by creating a disposable div in the view's render and then skipping it here
		},

		/**
		 * Reprint all savedPresets (fires on resest of savedPresets collections)
		*/
		addAllSavedPresets : function() {
			this.$savedPresets = $( '.it-exchange-variant-presets-saved .it-exchange-variant-column-inner' );
			this.savedPresets.each( this.addOneSavedPreset, this );
		},

		/**
		 * Receive an instance of a SavedPreset model, pass it to the SavedPreset View
		 * and grab its rendered $el and append it to the list of saved presets
		*/
		addOneSavedPreset: function ( savedPreset ) {
			var view = new itExchangeVariants.SavedPresetView( {model: savedPreset} );
			this.$savedPresets.append( view.render().$('.it-exchange-variants-preset',this.$el)); // We're cheating by creating a disposable div in the view's render and then skipping it here
		},

		/**
		 * Slowly fade out the variant preset popup
		*/
		fadeOutVariantPresetsPopup: function() {
			$('.it-exchange-new-variant-add-button a').removeClass( 'toggle-open' );
			$('.it-exchange-new-variant-presets').fadeOut();
		},

		/**
		 * Quickly close the variant preset popup
		*/
		closeVariantPresetsPopup: function() {
			$('.it-exchange-new-variant-add-button a').removeClass( 'toggle-open' );
			$('.it-exchange-new-variant-presets').hide();
		},

		/**
		 * Adds a new variant to the product variants form based on the template that was clicked
		*/
		addNewVariantFromTemplate: function(event) {
			event.preventDefault();
			this.closeVariantPresetsPopup();
			var id = $(event.currentTarget).data('variant-presets-template-id');
			this.addNewVariantToCollectionFromTemplate(id);
		},

		addNewVariantToCollectionFromTemplate: function(id){
			if ( this.corePresets.get(id)) {
				var presetVariant = this.corePresets.get(id).clone();
				var newVariant = {
					'title'        : presetVariant.get('title'),
					'presetSlug'   : presetVariant.get('slug'),
					'uiType'       : presetVariant.get('uiType'),
					'newFromPreset': presetVariant.get('id'),
					'openDiv'      : true
				}
				newVariant = this.productVariants.add(newVariant);
			}
		},

		/**
		 * Adds a new variant to the product variants form based on the saved preset that was clicked
		*/
		addNewVariantFromSaved: function(event) {
			event.preventDefault();
			this.closeVariantPresetsPopup();
			var id = $(event.currentTarget).data('variant-presets-saved-id');
			this.addNewVariantToCollectionFromSaved(id);
		},

		addNewVariantToCollectionFromSaved: function(id){
			if ( this.savedPresets.get(id)) {
				var savedVariant = this.savedPresets.get(id).clone();
				var newVariant = {
					'title'        : savedVariant.get('title'),
					'presetSlug'   : savedVariant.get('slug'),
					'uiType'       : savedVariant.get('uiType'),
					'newFromPreset': savedVariant.get('id'),
					'openDiv'      : true
				}
				newVariant = this.productVariants.add(newVariant);
			}
		}
	});

	itExchangeVariants.ProductVariantView = Backbone.View.extend({

		tagName : 'div',

		className : 'it-exchange-existing-variant',

		template : wp.template( 'it-exchange-admin-variant' ),

		initialize : function() {
			this.listenTo(this.model.values, 'reset', this.addAllVariantValues);
			this.listenTo(this.model.values, 'reset', this.makeVariantValuesSortable);
			this.listenTo(this.model.values, 'reset add remove', this.updateVariantValuesPreview);
			this.listenTo(this.model.values, 'reset add', this.initIThemesColorPicker);
		},

		render : function () {
			var openDiv = this.model.has('openDiv');
			this.$el.html( this.template( this.model.toJSON() ) );
			this.$el
				.data('variant-id', this.model.get('id'))
				.attr('data-variant-id', this.model.get('id'))
				.data('variant-open', openDiv)
				.attr('data-variant-open', openDiv);

			if ( openDiv ) {
				$('.variant-values').slideUp().addClass('hidden');
				$('.variant-values', this.$el).slideDown().removeClass('hidden');
			}
			return this;
		},

		events: {
			'click .variant-title': 'toggleVariantValues',
			'click .variant-text-placeholder': 'makeVariantTitlesEditable',
			'click .it-exchange-remove-variant':  'deleteVariant'
		},

		// Make Variant Values Sortable
		makeVariantValuesSortable: function() {
			$('.variant-values-list', this.$el ).sortable({
				placeholder: 'sorting-placeholder clearfix',
				start: function( e, ui ) {
					$( '.sorting-placeholder' ).html( ui.item.context.innerHTML );
					$( this ).addClass( 'sorting' );
				},
				stop: function( e, ui ) {
					$( this ).removeClass( 'sorting' );
					$('.variant-order-input', $(this)).each(function(index){
						$(this).val(index);
					});
				}
			});
		},

		// Make Variant Titles Editable
		makeVariantTitlesEditable: function(event) {
			// Prevent toggleVariantValues event from firing when clicking on title placeholder
			event.stopPropagation();

			var $target = $(event.currentTarget);
			var $parent = $target.parent();

			$target.addClass('hidden');

			if ( $target.hasClass( 'variant-title-text' ) ) {
				$parent.find( '.variant-title-values-preview' ).addClass( 'hidden' );
			}

			$parent.find('.variant-text-input')
				.removeClass( 'hidden' )
				.focus()
				.on( 'focusout', function() {
					if ( '' == $( this ).val() ) {
						$( this ).val( $target.text() );
					}

					$( this ).addClass( 'hidden' );

					$target.text( $( this ).val() ).removeClass( 'hidden' );

					if ( $target.hasClass('variant-title-text') ) {
						$parent.find('.variant-title-values-preview').removeClass( 'hidden' );
					}
				});
		},

		// Toggle display of variant values when clicked
		toggleVariantValues: function(event) {
			// Prevent toggle if click was on title field
			var srcElement = $(event.srcElement);
			if ( srcElement.hasClass('variant-text-input') )
				return;

			// Open Variant Values if currently closed
			if ( 'false' == this.$el.attr('data-variant-open') ) {
				this.$el
					.attr('data-variant-open','true')
					.find('.variant-title-values-preview')
						.css('visibility','hidden')
						.end()
					.find('.variant-values')
						.stop()
						.slideDown()
					.end()
					// Close all sibling if we're opening this one
					.siblings('.it-exchange-existing-variant')
						.attr('data-variant-open','false')
						.find('.variant-title-values-preview')
							.css('visibility','visible')
							.end()
						.find('.variant-values')
							.stop()
							.slideUp()
							.end();
				this.updateVariantValuesPreview();
			} else {
				// Close variant values if already open
				this.$el
					.attr('data-variant-open','false')
					.find('.variant-title-values-preview')
						.css('visibility','visible')
						.end()
					.find('.variant-values')
						.stop()
						.slideUp()
						.end();
			}

		},

		// Delete variant row when x is clicked
		deleteVariant: function(event) {
			event.stopPropagation();
			event.preventDefault();
			this.$el.fadeOut().remove();
			itExchangeVariants.adminMetaBoxView.productVariants.remove(this.model);
		},

		// Updates the preview span
		updateVariantValuesPreview: function() {
			this.model.set('valuesPreview', this.model.values.pluck('title').join(', ') );
			this.$('.variant-title-values-preview', this.$el).text(this.model.get('valuesPreview'));
		},

		// Adds a color picker to any color type values
		initIThemesColorPicker: function() {
			$('.it-exchange-variants-colorpicker', this.$el).each( function() {
				itExchangeVariantsEnableColorPicker( $(this) );
			});
		},

		// Reprint all variant valuess (fires on resest of variants collections)
		addAllVariantValues : function() {
			this.$variantValues = $('.variant-values-list', this.$el);
			this.$variantValues.html('');

			this.model.values.each( this.addOneVariantValue, this );
		},

		// Adds one variant to the list
		addOneVariantValue: function ( variantValue ) {
			var view = new itExchangeVariants.VariantValueView( { model: variantValue } );
			this.$variantValues.append( view.render().$el );
		}

	});

	itExchangeVariants.VariantValueView = Backbone.View.extend({

		tagName: 'li',
		className: 'clearfix',

		templateId: function() {
			var template = 'it-exchange-admin-variant-value';
			if ( 'image' == this.model.get('uiType') || 'color' == this.model.get('uiType') ) {
				template += '-' + this.model.get('uiType');
			}
			return template;
		},

		events: {
			'keyup .variant-text-input': 'updateVariantValuePreviewOnChange',
			'click .it-exchange-remove-variant-value':  'deleteVariantValue'
		},

		render : function () {
			this.template = wp.template( this.templateId() );
			this.id = 'variant-' + this.model.get('id');
			this.$el.html( this.template( this.model.toJSON() ) );
			return this;
		},

		updateVariantValuePreviewOnChange: function(event) {
			this.model.set('title', $(event.currentTarget).val() );
		},

		// Delete variant row when x is clicked
		deleteVariantValue: function(event) {
			event.stopPropagation();
			event.preventDefault();
			this.$el.fadeOut().remove();
			//itExchangeVariants.adminMetaBoxView.productVariants.values.remove(this.model);
			itExchangeVariants.adminMetaBoxView.productVariants.get(this.model.get('parentId')).values.remove(this.model);
		}

	})

	/**
	 * This view represents a core preset type
	 * The tagName/className props cause a new parent level div to be created
	 * In the metaBoxView that calls this view, we skip over it. This is to allow templating vars for the top level div
    */
	itExchangeVariants.CorePresetView = Backbone.View.extend({
		tagName: 'div',
		className: 'this-is-never-rendered',
		template: wp.template( 'it-exchange-admin-add-variant-core-preset' ),
		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			return this;
		}
	});

	/**
	 * This view represents a saved preset type
	 * The tagName/className props cause a new parent level div to be created
	 * In the metaBoxView that calls this view, we skip over it. This is to allow templating vars for the top level div
    */
	itExchangeVariants.SavedPresetView = Backbone.View.extend({
		tagName: 'div',
		className: 'this-is-never-rendered',
		template: wp.template( 'it-exchange-admin-add-variant-saved-preset' ),
		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			return this;
		}
	});
}(jQuery));
