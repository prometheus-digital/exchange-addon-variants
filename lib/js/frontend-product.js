/* JS for variants on product page */
jQuery(document).ready(function($) {
	/**
	 * Change selected color variants on click
	*/
	$('.it-exchange-product-variants').on('click', '.it-variant-color', function(event) {
		var variantValue = $(this).find('.it-variant-color-inner').attr('data-id');
		$(this)
			.siblings('.it-variant-color').removeClass('selected')
			.end()
			.addClass('selected')
			.closest('.it-variant-options')
				.find('.it-exchange-selected-variants-field')
					.val(variantValue)
					.trigger('change');
	});

	/**
	 * Change selected image variants on click
	*/
	$('.it-exchange-product-variants').on('click', '.it-variant-image', function(event) {
		var variantValue = $(this).find('img').attr('data-id');
		$(this)
			.siblings('.it-variant-image').removeClass('selected')
			.end()
			.addClass('selected')
			.closest('.it-variant-options')
				.find('.it-exchange-selected-variants-field')
					.val(variantValue)
					.trigger('change');
	});

	/**
	 * Change selected select values when updated
	*/
	$('.it-exchange-product-variants').on('change', '.it-variant-select', function(event) {
		var variantValue = $(this).val();
		$(this)
			.closest('.it-variant-options')
				.find('.it-exchange-selected-variants-field')
					.val(variantValue)
					.trigger('change');
	});

	/**
	 * Change selected raido values when updated
	*/
	$('.it-exchange-product-variants').on('change', '.it-variant-radios', function(event) {
		var variantValue = $( 'input[name='+$(this).find('input').attr('name')+']:checked' ).val()
		$(this)
			.closest('.it-variant-options')
				.find('.it-exchange-selected-variants-field')
					.val(variantValue)
					.trigger('change');
	});

	// Update the combos and product features when values change
	$('.it-exchange-selected-variants-field').on('change', function(event) {
		var selectedVariants = [];
		$('.it-exchange-selected-variants-field').each(function(){
			selectedVariants.push($(this).val());
		});

		// If we have selected variants (we always should), lets go get that combo's unique hash
		if ( selectedVariants.length > 0 ) {
			// Build the query string for the variants
			selectedVariants = selectedVariants.join('&variants-array[]=');

			// Make an ajax request for the combo attributes (which includes the hash)
			$.get( ajaxurl + '?action=it-exchange-variants-json-api&endpoint=get-updated-features-html-for-variants&include-alts=1&product-id=' + itExchangeProductID + '&variants-array[]=' + selectedVariants, function(result) {
				// Convert the result to json
				result = $.parseJSON(result);

				if ( typeof result.images != 'undefined' && typeof result.images.selector != 'undefined' && typeof result.images.html != 'undefined' ) {
					var newImages = [];
					$(result.images.selector).find('img').each(function(index,img) {
						newImages.push($(img).attr('src'));
					});
					var oldImages = [];
					$(result.images.html).find('img').each(function(index,img) {
						oldImages.push($(img).attr('src'));
					});
					if ( typeof result.images.transition != 'undefined' && ( $.trim(newImages) != $.trim(oldImages) || 'fade' == result.images.transition ) ) {
						if ( !itExchange.variantCombosUpdated ) {
							$(result.images.selector).html( result.images.html );
						} else {
							$(result.images.selector).fadeOut(400, function(){
								$(this).html( result.images.html );
								$(this).fadeIn(400);
								itExchange.featureImageZoom();
							});
						}
					} else {
						$(result.images.selector).html( result.images.html );
						itExchange.featureImageZoom();
					}
				}

				if ( typeof result.price.selector != 'undefined' && typeof result.price.html != 'undefined' ) {
					if ( typeof result.price.transition != 'undefined' && ( $.trim( result.price.html ) != $.trim($(result.price.selector).html()) || 'fade' == result.price.transition ) ) {
						if ( !itExchange.variantCombosUpdated ) {
							$(result.price.selector).html( result.price.html );
						} else {
							$(result.price.selector).fadeOut(400, function(){
								$(this).html( result.price.html );
								$(this).fadeIn(400);
							});
						}
					} else {
						$(result.price.selector).html( result.price.html );
					}
				}

				if ( typeof result.inventory.selector != 'undefined' && typeof result.inventory.html != 'undefined' ) {
					if ( typeof result.inventory.transition != 'undefined' && ( $.trim( result.inventory.html ) != $.trim($(result.inventory.selector).html()) || 'fade' == result.inventory.transition ) ) {
						if ( !itExchange.variantCombosUpdated ) {
							$(result.inventory.selector).html( result.inventory.html );
						} else {
							$(result.inventory.selector).fadeOut(400, function(){
								$(this).html( result.inventory.html );
								$(this).fadeIn(400);
							});
						}
					} else {
						$(result.inventory.selector).html( result.inventory.html );
					}
				}

				var $swPurchaseOptions = $('.it-exchange-sw-purchase-options');
				if ( $swPurchaseOptions.length ) {
					if ( $('.it-exchange-variant-combo', $swPurchaseOptions).length ) {
						$('.it-exchange-variant-combo', $swPurchaseOptions ).val(result.comboHash);
					} else {
						$swPurchaseOptions.each(function(){
							$(this).append('<input type="hidden" class="it-exchange-variant-combo" name="it-exchange-combo-hash" value="' + result.comboHash + '" />');
						});
					}
				}
				itExchange.variantCombosUpdated = true;
			});
		}
	});
	if ( !itExchange.variantCombosUpdated ) {
		$('.it-exchange-selected-variants-field:first').trigger('change');
	}
})
