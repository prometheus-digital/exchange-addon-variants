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

		if ( selectedVariants.length > 0 ) {
			selectedVariants = selectedVariants.join('&variants-array[]=');
			$.get( ajaxurl + '?action=it-exchange-variants-json-api&endpoint=get-atts-from-raw-combo&variants-array[]=' + selectedVariants, function(result) {
				result = $.parseJSON(result);
				var $basePrice =  $('.it-exchange-base-price');
				if ( typeof itExchangeVariantPricing[result.hash] != 'undefined' && $basePrice.text() != itExchangeVariantPricing[result.hash] ) {
					$basePrice.fadeOut(400, function(){
						$(this).text(itExchangeVariantPricing[result.hash])
						.fadeIn();
					});
				} else if ( typeof itExchangeVariantPricing['base_price'] != 'undefined' && $basePrice.text() != itExchangeVariantPricing['base_price'] ) {
					$basePrice.fadeOut(400, function(){
						$(this).text(itExchangeVariantPricing['base_price'])
						.fadeIn();
					});
				}
				console.log(result);
			});
		}
	});
})
