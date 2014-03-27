<?php
if ( ! is_admin() )
	return;
$product_id             = empty( $GLOBALS['post']->ID ) ? 0 : $GLOBALS['post']->ID;
$variants_ui_enabled = ( 'yes' == it_exchange_get_product_feature( $product_id, 'inventory', array( 'setting' => 'variants-enabled' ) ) ) ? 'checked="checked"' : '';
?>

<!-- Inventory Variant Combos Container -->
<script type="text/template" id="tmpl-it-exchange-product-inventory-variants-container">
	<p class="intro-description">Use this to set the product's current inventory number.</p>
	<p>
		<input type="checkbox" id="it-exchange-enable-product-variant-inventory" class="it-exchange-checkbox-enable" name="it-exchange-enable-product-variant-inventory" value="yes" <?php esc_attr_e( $variants_ui_enabled ); ?>/>
		<label for="it-exchange-enable-product-variant-inventory">Enable Inventory Tracking for variants?</label>
	</p>

	<div class="it-exchange-product-inventory-variants-inner <?php echo ( '' == $variants_ui_enabled ) ? 'hide-if-js' : ''; ?>">
		<div class="it-exchange-product-inventory-variants-need-updated hide-if-js"></div>
		<div class="it-exchange-product-inventory-variants-table"></div>
	</div>
</script>

<!-- Header Template -->
<script type="text/template" id="tmpl-it-exchange-product-inventory-table-header">
	<# var width = ( 100 / ( _.size(data.variants) + 2)  ) #>
	<style type="text/css">
		.it-exchange-product-inventory-variants-table .inventory-variant-header-cell,
		.it-exchange-product-inventory-variants-table .inventory-variant-cell { max-width: {{ width }}% !important; }
	</style>
	<div class="inventory-variant-header-row">
		<# _.each(data.variants, function( variant ) { #>
			<div class="inventory-variant-header-cell">{{ variant }}</div>
		<# }); #>
		<div class="inventory-variant-header-cell inventory-variant-input-cell">Inventory</div>
	</div>
</script>

<script type="text/template" id="tmpl-it-exchange-product-inventory-table-footer">
</script>

<!-- Inventory Variant Combo Template -->
<script type="text/template" id="tmpl-it-exchange-product-inventory-variants-combo">
	<# _.each(data.variants, function( variant ) { #>
		<div class="inventory-variant-cell">{{ variant }}</div>
	<# }); #>
	<div class="inventory-variant-cell inventory-variant-input-cell">
		<input class="inventory-variant-input" name="it_exchange_inventory_variants[{{ data.hash }}]" type="number" value="{{ data.value }}">
	</div>
</script>
