(function(it_exchange_variants_add_edit_product) {

	it_exchange_variants_add_edit_product(window.jQuery, window, document);

	}(function($, window, document) {
		$(function() {

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

			// Init color pickers
			$('.it-exchange-variants-colorpicker').each(function(){
				itExchangeVariantsEnableColorPicker( $(this) );
			});

			$( '#it-exchange-variant-images' ).on( 'click', '.it-exchange-variant-image-item-title', function() {
				var parent = $( this ).parent();

				if ( parent.hasClass( 'editing' ) ) {
					parent.removeClass( 'editing' );

					parent.find( '.it-exchange-variant-image-item-content' ).stop().slideUp();
				} else {
					$( '.it-exchange-variant-image-item-content' ).stop().slideUp();
					$( '.it-exchange-variant-image-item' ).removeClass( 'editing' );

					parent.addClass( 'editing' ).find( '.it-exchange-variant-image-item-content' ).stop().slideDown();
				}
			});
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
