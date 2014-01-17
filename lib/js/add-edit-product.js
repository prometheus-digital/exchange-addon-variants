(function(it_exchange_variants_add_edit_product) {

	it_exchange_variants_add_edit_product(window.jQuery, window, document);

	}(function($, window, document) {
		$(function() {

			// Toggle display of Variants when Use Variants checbox is clicked
			$('#it-exchange-enable-product-variants').on('change', function(){
				$('.it-exchange-product-variants-inner').toggleClass('hide-if-js');
			});

			// AJAX response for Add Variant via Preset TEMPLATE
			$('a.it-exchange-variant-preset-template-title').on('click', function(event) {
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
					if ( response.status ) {
						$( '.variant-values' ).slideUp();
						order = $('.it-exchange-existing-variants').find('.it-exchange-existing-variant').last().find('.parent-variant-order-input').attr('value') || 0;
						order++;

						$('.it-exchange-existing-variants').append(response.html);
						$('.it-exchange-existing-variants').find('.it-exchange-existing-variant').last().find('.parent-variant-order-input').attr('value',order);

						$('.it-exchange-variants-colorpicker:visible:visible', '.it-exchange-existing-variants' ).each(function(){
							itExchangeVariantsEnableColorPicker( $(this) );
						});

						$( '.it-exchange-new-variant-add-button a' ).removeClass( 'toggle-open' );
						$( '.it-exchange-new-variant-presets' ).fadeOut( 100 );

						variant_values_sortable();
					}
				});
			});

			// AJAX response for Add Variant via SAVED preset
			$('a.it-exchange-variant-preset-saved-title').on('click', function(event) {
				event.preventDefault();
				var savedID = $(this).closest('.it-exchange-variants-preset-saved').data('variant-presets-saved-id');
				if ( ! savedID || ! itExchangeVariantsAddonAddPresetTemplateNonce )
					return;

				var data = {
					action:                'ite_add_edit_variants',
					itevAction:            'addVariantFromSaved',
					_itEVAddTemplateNonce: itExchangeVariantsAddonAddPresetTemplateNonce,
					itevSavedID:        savedID,
				};
				$.post(ajaxurl, data, function(response) {
					response = $.parseJSON(response);
					if ( response.status ) {
						$( '.variant-values' ).slideUp();
						order = $('.it-exchange-existing-variants').find('.it-exchange-existing-variant').last().find('.parent-variant-order-input').attr('value') || 0;
						order++;

						$('.it-exchange-existing-variants').append(response.html);
						$('.it-exchange-existing-variants').find('.it-exchange-existing-variant').last().find('.parent-variant-order-input').attr('value',order);

						$('.it-exchange-variants-colorpicker:visible:visible', '.it-exchange-existing-variants' ).each(function(){
							itExchangeVariantsEnableColorPicker( $(this) );
						});

						$( '.it-exchange-new-variant-add-button a' ).removeClass( 'toggle-open' );
						$( '.it-exchange-new-variant-presets' ).fadeOut( 100 );

						variant_values_sortable();
					}
				});
			});

			// Make the variant values list sortable
			variant_values_sortable = function() {
				$( '.variant-values-list' ).sortable({
					placeholder: 'sorting-placeholder clearfix',
					start: function( e, ui ) {
						$( '.sorting-placeholder' ).html( ui.item.context.innerHTML );
						$( this ).addClass( 'sorting' );
					},
					stop: function( e, ui ) {
						$( this ).removeClass( 'sorting' );
					}
				});
			}

			variant_values_sortable();

			// Make the existing variants sortable
			$( '.it-exchange-existing-variants' ).sortable({
				placeholder: 'it-exchange-existing-variant sorting-placeholder clearfix',
				start: function( e, ui ) {
					//console.log( ui );
					$( '.sorting-placeholder' ).html( ui.item.context.innerHTML );
					$( this ).addClass( 'sorting' );
				},
				stop: function( e, ui ) {
					$( this ).removeClass( 'sorting' );
				}
			});

			// Close all variant boxes on load
			$( '.variant-values' ).slideUp();

			// Toggle Variant Value display when clicked
			$( '.it-exchange-existing-variants' ).on( 'click', '.variant-title', function( event ) {
				var element = $( this );
				var parent = element.parent();
				var grandparent = parent.parent()

				if ( parent.data( 'variant-open' ) == true ) {
					parent.data( 'variant-open', 'false' ).attr( 'data-variant-open', false ).find( '.variant-values' ).stop().slideUp();
					element.find( '.variant-title-values-preview' ).css( 'visibility', 'visible' );

					$( '.variant-text-input' ).focusout();

					var text = [];

					parent.find( '.variant-values li' ).each( function() {
						if ( ! $( this ).hasClass( 'hidden' ) ) {
							text.push( $( this ).find( '.variant-value-name' ).text() );
						}
					});

					element.find( '.variant-title-values-preview' ).html( text.join( ', ' ) );

				} else {
					grandparent.find( '.it-exchange-existing-variant' ).data( 'variant-open', false ).attr( 'data-variant-open', false ).find( '.variant-values' ).stop().slideUp();
					parent.data( 'variant-open', true ).attr( 'data-variant-open', true ).find( '.variant-values' ).stop().slideDown();
					element.find( '.variant-title-values-preview' ).css( 'visibility', 'hidden' );
				}
			});

			// Make Variant Titles editable
			$('.it-exchange-existing-variants').on( 'click', '.variant-text-placeholder', function() {
				var element = $( this );
				var parent = element.parent();

				element.addClass( 'hidden' );

				if ( element.hasClass( 'variant-title-text' ) ) {
					parent.find( '.variant-title-values-preview' ).addClass( 'hidden' );
				}

				parent.find( '.variant-text-input' ).removeClass( 'hidden' ).focus().on( 'focusout', function() {
					if ( '' == $( this ).val() ) {
						$( this ).val( element.text() );
					}

					$( this ).addClass( 'hidden' );

					element.text( $( this ).val() ).removeClass( 'hidden' );

					if ( element.hasClass( 'variant-title-text' ) ) {
						parent.find( '.variant-title-values-preview' ).removeClass( 'hidden' );
					}
				});

				return false;
			})

			$( '.variant-title .variant-text-input' ).on( 'click', function() {
				return false;
			});

			// Add new variant value
			$( '.it-exchange-existing-variants' ).on( 'click', '.add-variant-value-button', function() {
				var target = $( this ).parent().parent().find( '.variant-values-list' );
				var clone = target.find( '.new-variant-value' ).clone();
				var last = target.find( 'li:last-child' );

				var options = {
					'id': last.data( 'variant-value-id' ) + 1,
					'order': last.find( '.variant-value-reorder' ).data( 'variant-value-order' ) + 1
				};

				clone.attr( 'data-variant-value-id', options.id ).removeClass( 'new-variant-value' ).removeClass( 'hidden' ).find( '.variant-value-reorder' ).attr( 'data-variant-value-order', options.order );
				clone.find( '.new-variant-name-field' ).attr( 'name', 'it-exchange-product-variants[variants][' + options.id + '][title]' ).end().find('.new-variant-parent-field').attr('name', 'it-exchange-product-variants[variants][' + options.id + '][post_parent]' ).end().find('.it-exchange-variants-colorpicker').attr('name','it-exchange-product-variants[variants][' + options.id + '][color]').end().find('.new-variant-order-field').attr('name', 'it-exchange-product-variants[variants][' + options.id + '][order]').attr('value', options.order).end().find('.new-variant-default-field').attr('value', options.id).end().find(':disabled').removeAttr('disabled');

				target.append( clone );

				variant_values_sortable();

				$('.it-exchange-variants-colorpicker:visible', clone).each(function(){
					itExchangeVariantsEnableColorPicker( $(this) );
				});
			});

			$( '.it-exchange-existing-variants' ).on( 'click', '.it-exchange-remove-item', function( event ) {
				event.preventDefault();
				$( this ).parent().parent().remove();
			});

			$( '.it-exchange-new-variant-add-button' ).on( 'click', 'a', function( event ) {
				event.preventDefault();

				if ( $( this ).hasClass( 'toggle-open') ) {
					$( this ).removeClass( 'toggle-open' );
					$( '.it-exchange-new-variant-presets' ).stop().fadeOut();
				} else {
					$( this ).addClass( 'toggle-open' );
					$( '.it-exchange-new-variant-presets' ).stop().fadeIn();
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

			// Variant Images
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

					image       = '<img src="' + source.sizes.thumbnail.url + '" alt="" />';

					$( '.variant-values-list' ).find( 'li[data-variant-value-id="' + variant_value_id + '"] .variant-value-image' ).html( image );
					$( '.variant-values-list' ).find( 'li[data-variant-value-id="' + variant_value_id + '"]').find('.it-exchange-variants-image').attr('value', source.sizes.full.url).attr('name','it-exchange-product-variants[variants][' + variant_value_id + '][image]');;
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

		// Init color pickers
		$('.it-exchange-variants-colorpicker').each(function(){
			itExchangeVariantsEnableColorPicker( $(this) );
		}); 
	})
);

function itExchangeVariantsEnableColorPicker(node) {

	jQuery(node).css( 'background-color', jQuery(node).val() ).css( 'color', jQuery(node).val() ).css( 'width', '28px' );
	jQuery(node).unbind( 'focus' );

	jQuery(node).ColorPicker({
		onChange: function( color, el ) {
			jQuery(el).val( color );
			jQuery(node).css( 'background-color', color ).css( 'color', color );
		},
		onBeforeShow: function () {
			color = ( '' !== this.value ) ? this.value : '#9EDCF0';
			jQuery(this).ColorPickerSetColor( color );
		}
	}).bind('keyup',
		function() {
			jQuery(this).ColorPickerSetColor( this.value );
		}
	);
}
