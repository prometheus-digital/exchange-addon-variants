(function(it_exchange_variants_add_edit_product) {

	it_exchange_variants_add_edit_product(window.jQuery, window, document);

	}(function($, window, document) {
		$(function() {

			// NOTE This needs to be removed before launch.
			$( '#it-exchange-advanced-tab-nav #ui-id-9').click();

			// Toggle display of Variants when Use Variants checbox is clicked
			$('#it-exchange-enable-product-variants').on('change', function(){
				$('.it-exchange-product-variants-inner').toggleClass('hide-if-js');
			});

			// AJAX response for Add Variant
			$('a.it-exchange-variant-preset-template-title', '.it-exchange-variant-presets-templates').on('click', function(event) {
				event.preventDefault();
				var templateID = $(this).closest('.it-exchange-variants-preset-template').data('variant-presets-template-id');
				if ( ! templateID || ! itExchangeVariantsAddonAddPresetTemplateNonce )
					return;

				var data = {
					action:                'ite_add_edit_variants',
					itevAction:            'addVariantFromTemplate',
					_itEVAddTemplateNonce: itExchangeVariantsAddonAddPresetTemplateNonce,
					itevTemplateID:        templateID,
				};
				$.post(ajaxurl, data, function(response) {
					response = $.parseJSON(response);
					if ( response.status )
						$('.it-exchange-existing-variants').append(response.html);
					console.log(response);
				});
			});

			$( '.variant-values-list' ).sortable({
				placeholder: 'sorting-placeholder clearfix',
				start: function( e, ui ) {
					console.log( ui );
					$( '.sorting-placeholder' ).html( ui.item.context.innerHTML );
					$( this ).addClass( 'sorting' );
				},
				stop: function( e, ui ) {
					$( this ).removeClass( 'sorting' );
				}
			});

			$( '.it-exchange-existing-variants' ).sortable({
				placeholder: 'it-exchange-existing-variant sorting-placeholder clearfix',
				start: function( e, ui ) {
					console.log( ui );
					$( '.sorting-placeholder' ).html( ui.item.context.innerHTML );
					$( this ).addClass( 'sorting' );
				},
				stop: function( e, ui ) {
					$( this ).removeClass( 'sorting' );
				}
			});

			$( '.variant-values' ).slideUp();

			$( '.variant-title' ).on( 'click', function( event ) {
				var element = $( this );
				var parent = element.parent();
				var grandparent = parent.parent()

				if ( parent.data( 'variant-open' ) == true ) {
					parent.data( 'variant-open', 'false' ).attr( 'data-variant-open', false ).find( '.variant-values' ).stop().slideUp();
					element.find( '.variant-title-values-preview' ).css( 'visibility', 'visible' );
				} else {
					grandparent.find( '.it-exchange-existing-variant' ).data( 'variant-open', false ).attr( 'data-variant-open', false ).find( '.variant-values' ).stop().slideUp();
					parent.data( 'variant-open', true ).attr( 'data-variant-open', true ).find( '.variant-values' ).stop().slideDown();
					element.find( '.variant-title-values-preview' ).css( 'visibility', 'hidden' );
				}
			});

			$( document ).on( 'click', '.variant-text-placeholder', function() {
				var element = $( this );
				var parent = element.parent();

				element.addClass( 'hidden' );

				if ( element.hasClass( 'variant-title-text' ) ) {
					parent.find( '.variant-title-values-preview' ).addClass( 'hidden' );
					console.log( parent );
				}

				parent.find( '.variant-text-input' ).removeClass( 'hidden' ).focus().on( 'focusout', function() {
					$( this ).addClass( 'hidden' );

					parent.find( '.variant-text-placeholder' ).text( $( this ).val() ).removeClass( 'hidden' );

					if ( element.hasClass( 'variant-title-text' ) ) {
						parent.find( '.variant-title-values-preview' ).removeClass( 'hidden' );
					}
				});

				return false;
			})

			$( '.variant-title .variant-text-input' ).on( 'click', function() {
				return false;
			});

			$( '.add-variant-value' ).on( 'click', 'input', function() {
				var target = $( this ).parent().parent().find( '.variant-values-list' );
				var clone = target.find( '.new-variant-value' ).clone();
				var last = target.find( 'li:last-child' );

				var options = {
					'id': last.data( 'variant-value-id' ) + 1,
					'order': last.find( '.variant-value-reorder' ).data( 'variant-value-order' ) + 1
				};

				clone.attr( 'data-variant-value-id', options.id ).removeClass( 'new-variant-value' ).removeClass( 'hidden' ).find( '.variant-value-reorder' ).attr( 'data-variant-value-order', options.order );

				target.append( clone );
			});

			$( '.it-exchange-existing-variants' ).on( 'click', '.it-exchange-remove-item', function( event ) {
				event.preventDefault();
				$( this ).parent().parent().remove();
			});

			$( '.it-exchange-new-variant-add-button' ).on( 'click', 'a', function( event ) {
				event.preventDefault();

				if ( $( this ).hasClass( 'toggle-open') ) {
					$( this ).removeClass( 'toggle-open' );
					$( '.it-exchange-new-variant-presets' ).fadeOut();
				} else {
					$( this ).addClass( 'toggle-open' );
					$( '.it-exchange-new-variant-presets' ).fadeIn();
				}
			});

			$( '.it-exchange-new-variant-presets' ).on( 'mouseleave', function() {
				$( '.it-exchange-new-variant-add-button a' ).removeClass( 'toggle-open' );
				$( this ).fadeOut();
			});
			
			$( '.it-exchange-variant-preset-saved-delete' ).on( 'click', function( event ) {
				event.preventDefault();
				
				$( this ).parent().remove();
			});
			
			add_images_frame = {

				frame: function() {
					if ( this._frame )
						return this._frame;

					this._frame = wp.media({
						title: addEditProductL10n.mediaManagerTitle,
						library: {
							type: 'image'
						},
						multiple: false
					});

					this._frame.on( 'open', this.open ).state('library').on( 'select', this.select );

					return this._frame;
				},

				select: function() {
					source = this.get( 'selection' ).single().toJSON();

					image = '<img src="' + source.sizes.thumbnail.url + '" alt="" />';

					$( '.variant-values-list' ).find( 'li[data-variant-value-id="' + variant_value_id + '"] .variant-value-image' ).html( image );
				},

				init: function() {
					$( '#wpbody' ).on( 'click', '.variant-value-image', function( event ) {
						event.preventDefault();

						variant_value_id = $( this ).parent().parent().data( 'variant-value-id' );

						add_images_frame.frame().open();
					});
				}
			};
			
			add_images_frame.init();
		});
	})
);
