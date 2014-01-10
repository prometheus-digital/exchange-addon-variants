(function(it_exchange_variants_add_edit_product) {

	it_exchange_variants_add_edit_product(window.jQuery, window, document);

	}(function($, window, document) {
		$(function() {
			
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
				var target = $( this ).parent().parent();
				var clone = target.find( 'li:last-child' ).clone();

				var options = {
					'id': clone.data( 'variant-value-id' ) + 1,
					'order': clone.find( '.variant-value-reorder' ).data( 'variant-value-order' ) + 1
				};

				var atts = {
					
				};

				clone.attr( 'data-variant-value-id', options.id );
				clone.find( '.variant-value-reorder' ).attr( 'data-variant-value-order', options.order );
				clone.find( '.variant-text-input' ).attr( 'name', 'variant-value-name[' + options.id + ']' ).val( 'New Option' );
				clone.find( '.variant-value-name' ).text( 'New Option' );

				target.find( '.variant-values-list' ).append( clone );
			});

			$( '.it-exchange-existing-variants' ).on( 'click', '.it-exchange-remove-item', function( event ) {
				event.preventDefault();
				$( this ).parent().parent().remove();
			});
		});
	})
);