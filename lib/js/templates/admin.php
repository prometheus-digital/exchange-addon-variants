<?php
if ( ! is_admin() )
	return;
?>
<!-- Variant Container -->
<script type="text/template" id="tmpl-it-exchange-admin-variants-container">
	<p> 
		<input type="checkbox" id="it-exchange-enable-product-variants" value="yes" class="it-exchange-checkbox-enable" name="it-exchange-product-variants[enabled]" />
		<label for="it-exchange-enable-product-variants"><?php _e( 'Enable variants for this product', 'LION' ); ?></label><br />
	</p>

	<div class="it-exchange-product-variants-inner"> <!-- ADD hide-if-js CLASS IF Variants not enabled -->

		<div class="it-exchange-existing-variants"> <!-- ADD no-variants CLASS IF EMPTY -->
		</div>

		<div class="it-exchange-new-variant">

			<!-- Add New Variant Button -->
			<div class="it-exchange-new-variant-add-button">
				<a class="button button-primary"><?php _e( 'Add Variant', 'LION' ); ?></a>
			</div>

			<div class="it-exchange-new-variant-presets hidden clearfix">
				<!-- Core Presets -->
				<div class="it-exchange-variant-presets-templates it-exchange-variant-presets-column">
					<div class="it-exchange-variant-column-inner">
					</div>
				</div>

				<!-- Saved Presets -->
				<div class="it-exchange-variant-presets-saved it-exchange-variant-presets-column">
					<div class="it-exchange-variant-column-inner"></div>
				</div>
			</div>
		</div>

		<div class="variants-updated-notification">
			<p><?php _e( 'Changes made to product variants require you to save this product before other variant related options are updated.', 'LION' ); ?></p>
		</div>
	</div>
</script>

<!-- Variant Template -->
<script type="text/template" id="tmpl-it-exchange-admin-variant">
	<div class="it-exchange-existing-variant" data-variant-id="{{ data.id }}" data-variant-open="false">
		<div class="variant-title">
			<span class="variant-title-move">
				<input type="hidden" name="it-exchange-product-variants[variants][{{ data.id }}][order]" value="{{ data.order }}" class="parent-variant-order-input" />
			</span>
			<span class="variant-title-text variant-text-placeholder">{{ data.title }}</span>
			<input type="hidden" name="it-exchange-product-variants[variants][{{ data.id }}][id]" value="{{ data.id }}"> 
			<input type="text" name="it-exchange-product-variants[variants][{{ data.id }}][title]" value="{{ data.title }}" class="variant-text-input hidden">
			<input type="hidden" name="it-exchange-product-variants[variants][{{ data.id }}][ui_type]" value="{{ data.uiType }}"> 
			<input type="hidden" name="it-exchange-product-variants[variants][{{ data.id }}][preset_slug]" value="{{ data.presetSlug }}"> 
			<span class="variant-title-values-preview">{{ data.valuesPreview }}</span>
			<span class="variant-title-delete it-exchange-remove-item">&times;</span>
		</div>
		<div class="variant-values">
			<div class="edit-variant">
				<span class="label">
					Values
					<span class="it-exchange-tip" data-tip-content="tooltip goes here">i</span>
				</span>
				<ul class="variant-values-list ui-sortable">
				</ul>
			</div>
		</div>
	</div>
</script>

<!-- Variant Value Template for Images -->
<script type="text/template" id="tmpl-it-exchange-admin-variant-value-image">
	<!--<li class="clearfix" data-variant-value-id="{{ data.id }}" data-variant-value-parent="{{ data.parentId }}"> -->
		<div class="variant-value-reorder" data-variant-value-order="{{ data.order }}"> 
			<input type="hidden" name="it-exchange-product-variants[variants][{{ data.id }}][order]" value="{{ data.order }}" class="variant-order-input" />
		</div>
		<div class="variant-value-info">
			<input type="radio" class="variant-radio-option" name="it-exchange-product-variants[variants][{{ data.id }}][default]" value="{{ data.id }}" checked="{{ data.isDefault }}" />
			<span class="variant-value-name variant-text-placeholder">{{ data.title }}</span>
			<input type="text" name="it-exchange-product-variants[variants][{{ data.id }}][title]" value="{{ data.title }}" class="variant-text-input hidden" />
			<input type="hidden" name="it-exchange-product-variants[variants][{{ data.id }}][post_parent]" value="{{ data.parentId }}" class="variant-post-parent-hidden" />
			<a class="variant-value-image variant-value-has-image">
				<span class="variant-value-image-placeholder">{{ data.imageThumbnail }}</span>
			</a>
			<input type="hidden" value="{{ data.imageUrl }}" name="it-exchange-product-variants[variants][{{ data.id }}][image]" class="it-exchange-variants-image" />
		</div>
		<div class="variant-value-delete">
			<a href class="it-exchange-remove-item">&times;</a>
		</div>
	<!--</li>-->
</script>

<!-- Variant Value Template for Colors -->
<script type="text/template" id="tmpl-it-exchange-admin-variant-value-color">
	<!--<li class="clearfix" data-variant-value-id="{{ data.id }}" data-variant-value-parent="{{ data.parentId }}"> -->
		<div class="variant-value-reorder" data-variant-value-order="{{ data.order }}"> 
			<input type="hidden" name="it-exchange-product-variants[variants][{{ data.id }}][order]" value="{{ data.order }}" class="variant-order-input" />
		</div>
		<div class="variant-value-info">
			<input type="radio" class="variant-radio-option" name="it-exchange-product-variants[variants][{{ data.id }}][default]" value="{{ data.id }}" checked="{{ data.isDefault }}" />
			<span class="variant-value-name variant-text-placeholder">{{ data.title }}</span>
			<input type="text" name="it-exchange-product-variants[variants][{{ data.id }}][title]" value="{{ data.title }}" class="variant-text-input hidden" />
			<input type="hidden" name="it-exchange-product-variants[variants][{{ data.id }}][post_parent]" value="{{ data.parentId }}" class="variant-post-parent-hidden" />
			<div class="variant-value-hex">
				<input type="text" value="{{ data.color }}" name="it-exchange-product-variants[variants][{{ data.id }}][color]" class="it-exchange-variants-colorpicker" />
			</div>
		</div>
		<div class="variant-value-delete">
			<a href class="it-exchange-remove-item">&times;</a>
		</div>
	<!--</li>-->
</script>

<!-- Variant Value Template for Radios and Selects -->
<script type="text/template" id="tmpl-it-exchange-admin-variant-value">
	<!--<li class="clearfix" data-variant-value-id="{{ data.id }}" data-variant-value-parent="{{ data.parentId }}"> -->
		<div class="variant-value-reorder" data-variant-value-order="{{ data.order }}"> 
			<input type="hidden" name="it-exchange-product-variants[variants][{{ data.id }}][order]" value="{{ data.order }}" class="variant-order-input" />
		</div>
		<div class="variant-value-info">
			<input type="radio" class="variant-radio-option" name="it-exchange-product-variants[variants][{{ data.id }}][default]" value="{{ data.id }}" checked="{{ data.isDefault }}" />
			<span class="variant-value-name variant-text-placeholder">{{ data.title }}</span>
			<input type="text" name="it-exchange-product-variants[variants][{{ data.id }}][title]" value="{{ data.title }}" class="variant-text-input hidden" />
			<input type="hidden" name="it-exchange-product-variants[variants][{{ data.id }}][post_parent]" value="{{ data.parentId }}" class="variant-post-parent-hidden" />
		</div>
		<div class="variant-value-delete">
			<a href class="it-exchange-remove-item">&times;</a>
		</div>
	<!--</li>-->
</script>

<!-- Core Preset Variant Template for Add Variant -->
<script type="text/template" id="tmpl-it-exchange-admin-add-variant-core-preset">
	<div class="it-exchange-variants-preset it-exchange-variants-preset-template it-exchange-variants-preset-template-{{ data.slug }}" data-variant-presets-template-id="{{ data.id }}">
		<img src="{{ data.imageThumb }}" alt="{{ data.imageAlt }}" />
		<a href="" class="it-exchange-variant-preset-template-title it-exchange-variant-preset-template-title-{{ data.slug }}">
			{{ data.title }}
		</a>
	</div>
</script>

<!-- Saved Preset Variant Template for Add Variant -->
<script type="text/template" id="tmpl-it-exchange-admin-add-variant-saved-preset">
	<div class="label"><?php _e( 'My Presets', 'LION' ); ?></div>
	<div class="it-exchange-variants-preset it-exchange-variants-preset-saved it-exchange-variants-preset-saved-{{ data.slug }}" data-variant-presets-saved-id="{{ data.id }}">
		<img src="{{ data.imageThumb }}" alt="{{ data.imageAlt }}" />

		<a href="" class="it-exchange-variant-preset-saved-title it-exchange-variant-preset-saved-title-{{ data.slug }}">{{ data.title }}</a>
		<a href="" class="it-exchange-variant-preset-saved-delete">&times;</a>
	</div>
</script>
