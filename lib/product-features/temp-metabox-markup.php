<div class="it-exchange-existing-variants <?php echo (count($existing_variants) < 1 ) ? ' no-variants' : ''; ?>">
	<?php
	if ( $existing_variants = it_exchange_get_variants_for_product( $post->ID ) ) {
		foreach( $existing_variants as $variant ) {
			if ( ! $variant->is_variant_value )
				echo it_exchange_variants_addon_get_add_edit_variant_form_field( 'existing', $variant );
		}
	}
	/*
	<div class="it-exchange-existing-variant" data-variant-id="1" data-variant-open="false">
		<div class="variant-title">
			<span class="variant-title-move"></span>
			<span class="variant-title-text variant-text-placeholder">Leather Type</span>
			<input type="text" name="variant_title_text" value="Leather Type" class="variant-text-input hidden">
			<span class="variant-title-values-preview">
				Full Grain, Top Grain, No Grain
			</span>
			<span class="variant-title-delete it-exchange-remove-item">
				&times;
			</span>
		</div>
		<div class="variant-values">
			<div class="edit-variant">
				<span class="label">Values <?php it_exchange_admin_tooltip( 'tooltip goes here' ); ?></span>
				<ul class="variant-values-list">
					<li class="new-variant-value clearfix hidden" data-variant-value-id="" data-variant-value-parent="1">
						<div class="variant-value-reorder" data-variant-value-order=""></div>
						<div class="variant-value-info">
							<input type="radio" class="variant-radio-option" name="default-for-variant-1"/>
							<span class="variant-value-name variant-text-placeholder">New Value</span>
							<input type="text" name="variant-value-name[]" value="New Value" class="variant-text-input hidden">
							<a class="variant-value-image variant-value-has-image">
								<span class="variant-value-image-placeholder"></span>
							</a>
						</div>
						<div class="variant-value-delete">
							<a href class="it-exchange-remove-item">&times;</a>
						</div>
					</li>
					<li class="clearfix" data-variant-value-id="101" data-variant-value-parent="1">
						<div class="variant-value-reorder" data-variant-value-order="1"></div>
						<div class="variant-value-info">
							<input type="radio" class="variant-radio-option" name="default-for-variant-1" />
							<span class="variant-value-name variant-text-placeholder">Full Grain</span>
							<input type="text" name="variant-value-name[101]" value="Full Grain" class="variant-text-input hidden" />
							<a class="variant-value-image variant-value-has-image">
								<img src="http://f.cl.ly/items/0B2o3K073h3o1T0m2Z0u/Screen%20Shot%202014-01-08%20at%2010.55.09%20AM.png" alt=""/>
							</a>
						</div>
						<div class="variant-value-delete">
							<a href class="it-exchange-remove-item">&times;</a>
						</div>
					</li>
					<li class="clearfix" data-variant-value-id="102" data-variant-value-parent="1" >
						<div class="variant-value-reorder" data-variant-value-order="2"></div>
						<div class="variant-value-info">
							<input type="radio" class="variant-radio-option" name="default-for-variant-1" />
							<span class="variant-value-name variant-text-placeholder">Top Grain</span>
							<input type="text" name="variant-value-name[102]" value="Top Grain" class="variant-text-input hidden" />
							<a class="variant-value-image variant-value-has-image">
								<span class="variant-value-image-placeholder"></span>
							</a>
						</div>
						<div class="variant-value-delete">
							<a href class="it-exchange-remove-item">&times;</a>
						</div>
					</li>
					<li class="clearfix" data-variant-value-id="103" data-variant-value-parent="1" >
						<div class="variant-value-reorder" data-variant-value-order="3"></div>
						<div class="variant-value-info">
							<input type="radio" class="variant-radio-option" name="default-for-variant-1"/>
							<span class="variant-value-name variant-text-placeholder">No Grain</span>
							<input type="text" name="variant-value-name[103]" value="No Grain" class="variant-text-input hidden">
							<a href class="variant-value-image variant-value-has-image">
								<span class="variant-value-image-placeholder"></span>
							</a>
						</div>
						<div class="variant-value-delete">
							<a href class="it-exchange-remove-item">&times;</a>
						</div>
					</li>
				</ul>
				<div class="add-variant-value">
					<input type="button" class="button" value="Add Value" />
				</div>
			</div>
		</div>
	</div>

	<!-- Variant 2 -->
	<div class="it-exchange-existing-variant it-exchange-existing-variant-643" data-variant-id="643" data-variant-open="false">
		<div class="variant-title variant-title-643" data-variant-id="643">
			<span class="variant-title-move"></span>
			<span class="variant-title-text variant-text-placeholder">Size</span>
			<input type="text" name="variant_title_text" value="Size" class="variant-text-input hidden">
			<span class="variant-title-values-preview variant-title-values-preview-643" data-variant-id="643">
				Extra Small, Small, Medium, Large, X-Large
			</span>
			<span class="variant-title-delete variant-title-delete-643 it-exchange-remove-item" data-variant-id="643">
				&times;
			</span>
		</div>
	</div>
	*/
	?>
</div>

<div class="it-exchange-new-variant">

	<div class="it-exchange-new-variant-add-button">
		<a class="button button-primary"><?php _e( 'Add Variant', 'LION' ); ?></a>
	<div>

	<div class="it-exchange-new-variant-presets hidden clearfix">
		<div class="it-exchange-variant-presets-templates it-exchange-variant-presets-column">
			<div class="it-exchange-variant-column-inner">
				<?php
				$presets =  it_exchange_variants_addon_get_presets( array( 'core_only' => true ) );
				$ajax_nonce = wp_create_nonce( 'it-exchange-variants-addon-add-preset-template' );
				?>
				<script type="text/javascript">
				var itExchangeVariantsAddonAddPresetTemplateNonce = '<?php echo esc_js( $ajax_nonce ); ?>';
				</script>
				<?php
				foreach( (array) $presets as $key => $preset ) {
					if ( ! $preset->is_template )
						continue;

					$id        = $preset->get_property( 'ID' );
					$slug      = $preset->get_property( 'slug' );
					$title     = $preset->get_property( 'title' );
					$ui_type   = $preset->get_property( 'ui_type' );
					?>

					<div class="it-exchange-variants-preset it-exchange-variants-preset-template it-exchange-variants-preset-template-<?php esc_attr_e( $slug ); ?>" data-variant-presets-template-id="<?php esc_attr_e( $id ); ?>">
						<?php if ( $ui_type && is_file( dirname( dirname( __FILE__ ) ) . '/images/presets/' . $ui_type . '.png' ) ) : ?>
							<img src="<?php esc_attr_e( ITUtility::get_url_from_file( dirname( dirname( __FILE__ ) ) . '/images/presets/' . $ui_type . '.png' ) ); ?>" alt="" />
						<?php else : ?>
							<img src="" alt="<?php echo ITUtility::get_url_from_file( dirname( dirname( __FILE__ ) ) . '/images/presets/' . $ui_type . '.png' );?>" />
						<?php endif; ?>

						<a href="" class="it-exchange-variant-preset-template-title it-exchange-variant-preset-template-title-<?php esc_attr_e( $slug ); ?>"><?php echo esc_html( $title ); ?></a>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="it-exchange-variant-presets-saved it-exchange-variant-presets-column">
			<div class="it-exchange-variant-column-inner">
				<?php
				$presets =  it_exchange_variants_addon_get_presets();

				?><div class="label"><?php _e( 'My Presets', 'LION' ); ?></div><?php

				foreach( (array) $presets as $key => $preset ) {
					if ( $preset->is_template )
						continue;

					$id        = $preset->get_property( 'ID' );
					$slug      = $preset->get_property( 'slug' );
					$title     = $preset->get_property( 'title' );
					$ui_type   = $preset->get_property( 'ui_type' );
					?>

					<div class="it-exchange-variants-preset it-exchange-variants-preset-saved it-exchange-variants-preset-saved-<?php esc_attr_e( $slug ); ?>" data-variant-presets-saved-id="<?php esc_attr_e( $id ); ?>">
						<?php if ( $ui_type && is_file( dirname( dirname( __FILE__ ) ) . '/images/presets/' . $ui_type . '.png' ) ) : ?>
							<img src="<?php esc_attr_e( ITUtility::get_url_from_file( dirname( dirname( __FILE__ ) ) . '/images/presets/' . $ui_type . '.png' ) ); ?>" alt="" />
						<?php else : ?>
							<img src="" alt="<?php echo ITUtility::get_url_from_file( dirname( dirname( __FILE__ ) ) . '/images/presets/' . $ui_type . '.png' );?>" />
						<?php endif; ?>

						<a href="" class="it-exchange-variant-preset-saved-title it-exchange-variant-preset-saved-title-<?php esc_attr_e( $slug ); ?>"><?php echo esc_html( $title ); ?></a>
						<a href="" class="it-exchange-variant-preset-saved-delete">&times;</a>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
