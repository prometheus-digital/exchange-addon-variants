<div class="it-exchange-existing-variants <?php echo (count($existing_variants) < 1 ) ? ' no-variants' : ''; ?>">
	<?php
	if ( $existing_variants = it_exchange_get_variants_for_product( $post->ID ) ) {
		foreach( $existing_variants as $variant ) {
			if ( ! $variant->is_variant_value )
				echo it_exchange_variants_addon_get_add_edit_variant_form_field( 'existing', $variant );
		}
	}
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
