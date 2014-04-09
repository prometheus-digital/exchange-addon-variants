<?php
if ( ! is_admin() )
	return;
$product_id             = empty( $GLOBALS['post']->ID ) ? 0 : $GLOBALS['post']->ID;
?>

<!-- Images Variant Combos Container -->
<script type="text/template" id="tmpl-it-exchange-product-images-variants-container">
	<label for="product-variant-images-field"><?php _e( 'Variant Images', 'LION' ); ?></label>

	<div class="add-new-product-variant-combination">
		<div class="label"><?php _e( 'Add Product Images for Variant Combination:', 'LION' ); ?></div>
		<input type="button" id="it-exchange-variant-images-add-combo-button" value="<?php esc_attr_e( __( 'Add New', 'LION' ) ); ?>" class="button button-primary">

		<div class="it-exchange-select-new-variant-images-combo-div hidden">
			<div class="it-exchange-variant-image-combo-selects">
				<# _.each(data.productVariants, function( variant ) { #>
					<select class="it-exchange-variant-images-add-combo-select">';
						<option value="{{ variant.get('id') }}"><?php _e( 'All', 'LION' ); ?> {{ variant.get('title') }}</option>
						<# _.each(variant.get('values'), function( value ) { #>
							<option value="{{ value.id }}">{{ value.title }}</option>
						<# }); #>
				</select>
				<# }); #>
			</div>

			<input type="button" id="it-exchange-variant-images-create-combo-button" value="<?php esc_attr_e( __( 'Create Combo', 'LION' ) ); ?>" class="button button-primary">
			<a href="#" id="it-exchange-cancel-variant-images-create-combo"><?php _e( 'Cancel', 'LION' ); ?></a>
		</div>

	</div>
	<div class="it-exchange-product-images-variants-missing-div"></div>
	<div id="it-exchange-variant-images"></div>
</script>

<script type="text/template" id="tmpl-it-exchange-product-images-variant">
	<div class="it-exchange-variant-image-item it-exchange-variant-image-item-{{ data.comboHash }} <# if ( ! data.featuredImage ) { #> editing<# } #>">
		<div class="it-exchange-variant-image-item-title">
			<p>
				<span class="it-exchange-variant-image-item-title-img"><img src="{{ data.imageThumbURL }}" alt="" /></span>
				<span class="it-exchange-variant-image-item-title-text">{{ data.title }}</span>
				<span class="it-exchange-variant-image-edit"></span>
			</p>
		</div>
		<div class="it-exchange-variant-image-item-content <# if ( data.featuredImage ) { #> hidden<# } #>">


			<div class="it-exchange-variant-feature-image-{{ data.comboHash }} ui-droppable it-exchange-feature-images-div" data-combo-hash="{{ data.comboHash }}">
				<ul class="feature-image">
					<# if ( data.featuredImage ) { #>
					<li id="{{ data.id }}" data-image-id="{{ data.imageID }}">
						<a class="image-edit is-featured" href="" data-image-id="{{ data.imageID }}">
							<img alt="Featured Image" data-thumb="{{ data.imageThumbURL }}" data-large="{{ data.imageLargeURL }}" src="{{ data.imageURL }}">
								<span class="overlay"></span>
						</a>
						<span class="remove-item">&times;</span>
						<input type="hidden" value="{{ data.imageID }}" name="it-exchange-product-variant-images[{{ data.comboHash }}][0]">
					</li>
					<# } #>
				</ul>
				<div class="replace-feature-image"><span><?php _e( 'Replace featured image', 'LION' ); ?></span></div>
			</div>
			<ul id="it-exchange-variant-gallery-images" class="it-exchange-gallery-images it-exchange-gallery-images-{{ data.comboHash }}">
				<# if ( data.variantImages ) { #>
				$image_int = 0;
				foreach( $images as $image_key => $image_id ) {
					$image_int++; // Should start with 1. Featured is 0.
					$thumb = wp_get_attachment_thumb_url( $image_id );
					$large = wp_get_attachment_url( $image_id );
					$src   = $thumb;
					?>
					<li id="{{ image.uniqueID }}" data-image-id="{{ image.id }}">
						<a href class="image-edit" data-image-id="{{ image.id }}">
							<img src="{{ image.URL }}" data-large="{{ image.largeURL }}" data-thumb="{{ image.thumbURL }}" alt="" />
							<span class="overlay"></span>
						</a>
						<span class="remove-item">&times;</span>
						<input type="hidden" name="it-exchange-product-variant-images[{{ data.comboHash }}][{{ image.int }}]" value="{{ image.id }}" />
					</li>
				<# } #>
				<li class="it-exchange-add-new-image it-exchange-add-new-variant-image disable-sorting<# if ( ! data.featureImage ) { #> empty<# } #>">
					<a href data-variant-id="{{ data.comboHash }}">
						<span><?php _e( 'Add Images', 'LION' ); ?></span>
					</a>
				</li>
			</ul>
			<div class="clear"><a class="delete-variant-gallery" href=""><?php _e( 'Delete variant gallery', 'LION' ); ?></a></div>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-it-exchange-product-images-variants-add-new-combo">
	<div class="it-exchange-variant-image-item it-exchange-variant-image-item-c9aeb4c351a7fd52eade5fce95223137" data-combo-hash="c9aeb4c351a7fd52eade5fce95223137">
		<div class="it-exchange-variant-image-item-title">
			<p>
				<span class="it-exchange-variant-image-item-title-img"><img class="hidden" src="" alt=""></span>
				<span class="it-exchange-variant-image-item-title-text">Create new Variant Image Gallery</span>
				<span class="it-exchange-variant-image-edit"></span>
			</p>
		</div>
		<div class="it-exchange-variant-image-item-content">


		<div class="ui-droppable it-exchange-feature-images-div it-exchange-variant-feature-image-c9aeb4c351a7fd52eade5fce95223137">
			<ul class="feature-image"></ul>
			<div class="replace-feature-image"><span>Replace featured image</span></div>
		</div>

		<ul id="it-exchange-variant-gallery-images" class="it-exchange-gallery-images ui-sortable">
			<li class="it-exchange-add-new-image it-exchange-add-new-variant-image disable-sorting empty"><a href="" data-variant-id="c9aeb4c351a7fd52eade5fce95223137"><span>Add Images</span></a></li>
		</ul>
		<div class="clear"><a class="delete-variant-gallery" href="">Delete variant gallery</a></div>
	</div>
</script>
