<?php
if ( ! is_admin() )
	return;
$product_id             = empty( $GLOBALS['post']->ID ) ? 0 : $GLOBALS['post']->ID;
$variants_ui_enabled = ( 'yes' == it_exchange_get_product_feature( $product_id, 'inventory', array( 'setting' => 'variants-enabled' ) ) ) ? 'checked="checked"' : '';
?>

<!-- Inventory Variant Combos Container -->
<script type="text/template" id="tmpl-it-exchange-product-inventory-variants-container">
	<p>
		<input type="checkbox" id="it-exchange-enable-product-variant-inventory" class="it-exchange-checkbox-enable" name="it-exchange-enable-product-variant-inventory" value="yes" <?php esc_attr_e( $variants_ui_enabled ); ?>/>
		<label for="it-exchange-enable-product-variant-inventory">Enable Inventory Tracking for variants?</label>
	</p>

	<div class="it-exchange-product-inventory-variants-inner <?php echo ( '' == $variants_ui_enabled ) ? 'hide-if-js' : ''; ?>">
		<div class="it-exchange-product-inventory-variants-missing-table"></div>
		<div class="it-exchange-product-inventory-variants-table"></div>
	</div>
</script>

<!-- Header Template -->
<script type="text/template" id="tmpl-it-exchange-product-inventory-table-header">
	<# var width = ( 100 / ( _.size(data.variants) + 2)  ) #>
	<style type="text/css">
		.it-exchange-product-inventory-variants-table .inventory-variant-header-cell,
		.it-exchange-product-inventory-variants-table .inventory-variant-cell { max-width: {{ width }}% ; }
	</style>
	<input id="it-exchange-inventory-variants-version" type="hidden" name="it-exchange-inventory-variants-version" disabled value="{{ data.version }}" />
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
		<input class="inventory-variant-input inventory-variant-input-{{ data.hash }}" name="it_exchange_inventory_variants[{{ data.hash }}]" type="number" value="{{ data.value }}">
	</div>
</script>

<!-- Inventory Variant Missing Combo Template -->
<script type="text/template" id="tmpl-it-exchange-product-inventory-variants-missing-combo">
	<div class="missing-inventory-notification-dialog">
		<p><?php _e( sprintf( '%s{{ data.title }}%s is no longer valid combination because your product variants have changed. What should we do with its old inventory value?', '<strong>', '</strong>' ), 'LION' ); ?></p>
		<a href="#" class="notification-transfer button button-primary"><?php _e( 'Transfer Inventory to New Combo(s)', 'LION' ); ?></a>
		<a href="#" class="notification-discard button"><?php _e( 'Discard Inventory', 'LION' ); ?></a>
		<input class="inventory-variant-missing-value" type="text" value="{{ data.value }}" disabled />
		<input type="hidden" name="it-exchange-lock-inventory-variants" value="1" />
	</div>

	<div class="existing-inventory-variant-transfer-dialog hidden">
		<p><?php printf( __( 'Use the checkboxes below to transfer inventory %s({{ data.value }})%s from the old vairant combination %s{{ data.title }}%s to one or more new combinations.', 'LION' ), '<strong>', '</strong>', '<strong>', '</strong>' ); ?></p>
		<# _.each( data.existingVariants, function( variant ) { #>
			<label class="existing-inventory-variant-checkbox-{{ variant.get('hash') }}-label" for="existing-inventory-variant-checkbox-{{ variant.get('hash') }}-{{ data.hash }}">
			<input class="existing-inventory-variant-checkbox existing-inventory-variant-checkbox-{{variant.get('hash')}}" id="existing-inventory-variant-checkbox-{{ variant.get('hash') }}-{{ data.hash }}" data-hash="{{ variant.get('hash') }}" type="checkbox" value="{{ data.value }}" /> {{ variant.get('title') }}<br />
			</label>
		<# }); #>
		<a href="#" class="transfer-save button button-primary"><?php _e( 'Apply', 'LION' ); ?></a>
		<a href="#" class="transfer-cancel button"><?php _e( 'Cancel', 'LION' ); ?></a>
	</div>
</script>

<script type="text/template" id="tmpl-it-exchange-product-inventory-variants-disgard-all-missing-combos">
	<a id="it-exchange-product-inventory-variants-disgard-all-missing-combos" href="#"><?php _e( 'Discard all invalid inventories', 'LION' ); ?></a>
</script>
