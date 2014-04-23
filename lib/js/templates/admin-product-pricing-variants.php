<?php
if ( ! is_admin() )
	return;
$product_id = empty( $GLOBALS['post']->ID ) ? 0 : $GLOBALS['post']->ID;
?>

<!-- Pricing Variant Combos Container -->
<script type="text/template" id="tmpl-it-exchange-product-pricing-variants-container">
	<label for="product-variant-pricing-field"><?php _e( 'New Price Variant', 'LION' ); ?></label>

	<div class="add-new-product-pricing-variant-combination">
		<# if ( data.productVariants.length ) { #>

			<div class="it-exchange-select-new-variant-pricing-combo-div">
				<div class="it-exchange-variant-pricing-item-not-valid-combo it-exchange-variant-pricing-item-combo-error hidden"><?php _e( 'All combo selects cannot be "Any"', 'LION' ); ?></div>
				<div class="it-exchange-variant-pricing-item-already-exists it-exchange-variant-pricing-item-combo-error hidden"><?php _e( 'Combo already exists', 'LION' ); ?></div>
				<div class="it-exchange-variant-pricing-combo-selects">
					<# var variantVersion = false; #>
					<# _.each(data.productVariants, function( variant ) { #>
						<select class="it-exchange-variant-pricing-add-combo-select">';
							<option value="{{ variant.get('id') }}"><?php _e( 'Any', 'LION' ); ?> {{ variant.get('title') }}</option>
							<# _.each(variant.get('values'), function( value ) { #>
								<option value="{{ value.id }}">{{ value.title }}</option>
							<# }); #>
					</select>
					<# if ( !variantVersion && variant.get('version') ) { variantVersion = variant.get('version') } #>
					<# }); #>
				</div>

				<input type="button" id="it-exchange-variant-pricing-create-combo-button" value="<?php esc_attr_e( __( 'Add New Combo', 'LION' ) ); ?>" class="button button-primary">
			</div>
		<# } else { #>
			<p><?php _e( 'You must have one or more product variants before you can create a pricing variant.', 'LION' ); ?></p>
		<# } #>

	</div>
	<input id="it-exchange-product-pricing-variants-version" type="hidden" name="it-exchange-product-pricing-variants-version" value="{{ variantVersion }}" />
	<# if ( data.productVariants.length ) { #>
		<div class="it-exchange-variant-pricing-label hidden"><label for="it-exchange-variant-pricing"><?php _e( 'Existing Price Variants', 'LION' ); ?></label></div>
		<div id="it-exchange-variant-pricing"></div>
	<# } #>
</script>

<script type="text/template" id="tmpl-it-exchange-product-pricing-variant">
	<div class="it-exchange-variant-pricing-item it-exchange-variant-pricing-item-{{ data.comboHash }} <# if ( data.newCombo ) { #> editing<# } #><# if ( data.invalidCombo ) { #> it-exchange-variant-pricing-item-invalid<# } #>" data-it-exchange-combo-hash="{{ data.comboHash }}">
		<div class="it-exchange-variant-pricing-item-title">
			<span class="it-exchange-variant-pricing-item-title-text">{{ data.title }}</span>
			<span class="it-exchange-variant-pricing-item-price-preview">{{ data.value }}</span>
			<span class="it-exchange-variant-pricing-edit"></span>
		</div>
		<div class="it-exchange-variant-pricing-item-content <# if ( ! data.newCombo ) { #> hidden<# } #>">

			<# if ( data.invalidCombo ) { #>
				<input type="hidden" class="it-exchange-variant-pricing-lock" name="it-exchange-lock-product-pricing-variants" value="1" />
				<div class="it-exchange-select-update-variant-pricing-combo">
					<p><?php _e( 'This variant combination may no longer be valid due to a new or deleted variant. Please apply these pricing to the correct variant combination or delete it.', 'LION' ); ?></p>
					<div class="it-exchange-update-variant-pricing-item-not-valid-combo it-exchange-variant-pricing-item-combo-error hidden"><?php _e( 'All combo selects cannot be "Any"', 'LION' ); ?></div>
					<div class="it-exchange-update-variant-pricing-combo-selects">
						<# _.each(data.productVariants, function( variant ) { #>
							<select class="it-exchange-variant-pricing-update-combo-select">';
								<option value="{{ variant.get('id') }}"><?php _e( 'Any', 'LION' ); ?> {{ variant.get('title') }}</option>
								<# _.each(variant.get('values'), function( value ) { #>
									<option value="{{ value.id }}">{{ value.title }}</option>
								<# }); #>
						</select>
						<# }); #>
					</div>

					<input type="button" value="<?php esc_attr_e( __( 'Update Combo', 'LION' ) ); ?>" class="button button-primary it-exchange-update-variant-pricing-create-combo-button">
				</div>
			<# } #>

			<div class="pricing-ui<# if ( data.invalidCombo ) { #> hidden<# } #>">
				<label for="it-exchange-product-variant-pricing[{{ data.comboHash }}]">Variant Price</label>
				<input type="text" class="it-exchange-product-variant-price" name="it-exchange-product-variant-pricing[{{ data.comboHash }}]" value="{{ data.value }}" data-thousands-separator="{{ data.thousandsSep }}" data-decimal-separator="{{ data.decimalSep }}" data-symbol="{{ data.symbol }}" data-symbol-position="{{ data.symbolPosition }}" />
			</div>
			<div class="clear"><a class="delete-variant-price it-exchange-remove-item" href="">&times;</a></div>
		</div>
	</div>
</script>
