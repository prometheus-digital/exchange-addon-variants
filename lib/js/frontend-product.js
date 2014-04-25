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
			$.get( ajaxurl + '?action=it-exchange-variants-json-api&endpoint=get-atts-from-raw-combo&include-alts=1&product-id=' + itExchangeProductID + '&variants-array[]=' + selectedVariants, function(result) {
				// Conver the result to json
				result = $.parseJSON(result);

				// Cache the HTML node in a jQuery var
				var $basePrice =  $('.it-exchange-base-price');

				// Set found matching combo to false by default. If this is the case, we use default base_price
				var itExchangeFoundMatchingCombo = false;

				// Do we have a direct match (Did the store owner setup a variant property for this combination?)
				if ( typeof itExchangeVariantPricing[result.hash] != 'undefined' ) {
					// If the value for this combo is the same as the current combo, don't do anything
					if ( $basePrice.text() == itExchangeVariantPricing[result.hash] ) {
						return;
					}
					// Update the price div with the value for the found combo
					$basePrice.fadeOut(400, function(){
						$(this).text(itExchangeVariantPricing[result.hash])
						.fadeIn();
					});
					// Flag that we found a match so we don't default to base_price
					itExchangeFoundMatchingCombo = true;
				} else if ( result.alts != false && $.isArray(result.alts) ) {
					// We make it here only if there was no direct match but the result returned some
					// alternate combo hashes for existing 'All' rules setup by the store owner

					// When we loop through jQuery each, it will break if we return false. Set to true by default
					var eachReturn = true;

					// Loop through the list of alternate hashes since a direct match wasn't found
					$.each(result.alts, function(index, hash) {
						// For each alt hash, see if we have a price variant for it
						if ( typeof itExchangeVariantPricing[hash] != 'undefined' ) {

							// Update the price div in the HTML if we found a match and its different than the current value
							if ( $basePrice.text() != itExchangeVariantPricing[hash] ) {
								$basePrice.fadeOut(400, function(){
									$(this).text(itExchangeVariantPricing[hash])
									.fadeIn();
								});
							}
							// Flag the return to false so that we break the loop
							eachReturn                   = false;

							// Flag that we found a match so we don't overwrite it later with the default base_price
							itExchangeFoundMatchingCombo = true;
						}
						// This determines if we break the each loop
						return eachReturn;
					});
				}

				// If we haven't found a match (direct match or alternate match) lets just set it to the default base_price
				if ( ! itExchangeFoundMatchingCombo && typeof itExchangeVariantPricing['base_price'] != 'undefined' ) {

					// No need to update the HTNL if the current price is the same as the default base price
					if ( $basePrice.text() == itExchangeVariantPricing['base_price'] ) {
						return;
					}
					// Update the div with the default base price
					$basePrice.fadeOut(400, function(){
						$(this).text(itExchangeVariantPricing['base_price'])
						.fadeIn();
					});
				}
				// Whew! we made it!
			});
		}
	});
})
