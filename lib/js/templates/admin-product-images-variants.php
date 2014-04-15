<?php
if ( ! is_admin() )
	return;
$product_id = empty( $GLOBALS['post']->ID ) ? 0 : $GLOBALS['post']->ID;
?>

<!-- Images Variant Combos Container -->
<script type="text/template" id="tmpl-it-exchange-product-images-variants-container">
	<label for="product-variant-images-field"><?php _e( 'Variant Images', 'LION' ); ?></label>

	<div class="add-new-product-images-variant-combination">
		<# if ( data.productVariants.length ) { #>
			<div class="label"><?php _e( 'Add Product Images for Variant Combination:', 'LION' ); ?></div>

			<div class="it-exchange-select-new-variant-images-combo-div">
				<div class="it-exchange-variant-image-item-not-valid-combo it-exchange-variant-image-item-combo-error hidden"><?php _e( 'All combo selects cannot be "Any"', 'LION' ); ?></div>
				<div class="it-exchange-variant-image-item-already-exists it-exchange-variant-image-item-combo-error hidden"><?php _e( 'Combo already exists', 'LION' ); ?></div>
				<div class="it-exchange-variant-image-combo-selects">
					<# var variantVersion = false; #>
					<# _.each(data.productVariants, function( variant ) { #>
						<select class="it-exchange-variant-images-add-combo-select">';
							<option value="{{ variant.get('id') }}"><?php _e( 'Any', 'LION' ); ?> {{ variant.get('title') }}</option>
							<# _.each(variant.get('values'), function( value ) { #>
								<option value="{{ value.id }}">{{ value.title }}</option>
							<# }); #>
					</select>
					<# if ( !variantVersion && variant.get('version') ) { variantVersion = variant.get('version') } #>
					<# }); #>
				</div>

				<input type="button" id="it-exchange-variant-images-create-combo-button" value="<?php esc_attr_e( __( 'Create New Image Set', 'LION' ) ); ?>" class="button button-primary">
			</div>
		<# } else { #>
			<p><?php _e( 'You must have one or more product variants before you can create an image gallery for product variant combinations', 'LION' ); ?></p>
		<# } #>

	</div>
	<input id="it-exchange-product-images-variants-version" type="hidden" name="it-exchange-product-images-variants-version" value="{{ variantVersion }}" />
	<# if ( data.productVariants.length ) { #>
		<div id="it-exchange-variant-images"></div>
	<# } #>
</script>

<script type="text/template" id="tmpl-it-exchange-product-images-variant">
	<div class="it-exchange-variant-image-item it-exchange-variant-image-item-{{ data.comboHash }} <# if ( ! data.featuredImage ) { #> editing<# } #><# if ( data.invalidCombo ) { #> it-exchange-variant-image-item-invalid<# } #>" data-it-exchange-combo-hash="{{ data.comboHash }}">
		<div class="it-exchange-variant-image-item-title">
			<p>
				<span class="it-exchange-variant-image-item-title-img"><img src="<# if ( data.featuredImage.thumbURL ) { #>{{ data.featuredImage.thumbURL }}<# } #>" alt="" /></span>
				<span class="it-exchange-variant-image-item-title-text">{{ data.title }}</span>
				<span class="it-exchange-variant-image-edit"></span>
			</p>
		</div>
		<div class="it-exchange-variant-image-item-content <# if ( data.featuredImage ) { #> hidden<# } #>">

			<# if ( data.invalidCombo ) { #>
				<input type="hidden" class="it-exchange-variant-images-lock" name="it-exchange-lock-product-images-variants" value="1" />
				<div class="it-exchange-select-update-variant-images-combo">
					<p><?php _e( 'This variant combination may no longer be valid due to a new or deleted variant. Please apply these images to the correct variant combination or delete it.', 'LION' ); ?></p>
					<div class="it-exchange-update-variant-image-item-not-valid-combo it-exchange-variant-image-item-combo-error hidden"><?php _e( 'All combo selects cannot be "Any"', 'LION' ); ?></div>
					<div class="it-exchange-update-variant-image-combo-selects">
						<# _.each(data.productVariants, function( variant ) { #>
							<select class="it-exchange-variant-images-add-combo-select">';
								<option value="{{ variant.get('id') }}"><?php _e( 'Any', 'LION' ); ?> {{ variant.get('title') }}</option>
								<# _.each(variant.get('values'), function( value ) { #>
									<option value="{{ value.id }}">{{ value.title }}</option>
								<# }); #>
						</select>
						<# }); #>
					</div>

					<input type="button" value="<?php esc_attr_e( __( 'Update Combo', 'LION' ) ); ?>" class="button button-primary it-exchange-update-variant-images-create-combo-button">
				</div>
			<# } #>

			<div class="images-ui<# if ( data.invalidCombo ) { #> hidden<# } #>">
				<div class="it-exchange-variant-feature-image-{{ data.comboHash }} ui-droppable it-exchange-feature-images-div" data-combo-hash="{{ data.comboHash }}">
					<ul class="feature-image">
						<# if ( data.featuredImage ) { #>
						<li id="{{ data.featuredImage.cssID }}" data-image-id="{{ data.featuredImage.imageID }}">
							<a class="image-edit is-featured" href="" data-image-id="{{ data.featuredImage.imageID }}">
								<img alt="Featured Image" data-thumb="{{ data.featuredImage.thumbURL }}" data-large="{{ data.featuredImage.largeURL }}" src="{{ data.featuredImage.largeURL }}">
									<span class="overlay"></span>
							</a>
							<span class="remove-item">&times;</span>
							<input type="hidden" value="{{ data.featuredImage.imageID }}" name="it-exchange-product-variant-images[{{ data.comboHash }}][]">
						</li>
						<# } #>
					</ul>
					<div class="replace-feature-image"><span><?php _e( 'Replace featured image', 'LION' ); ?></span></div>
				</div>
				<ul id="it-exchange-variant-gallery-images" class="it-exchange-gallery-images it-exchange-gallery-images-{{ data.comboHash }}">
					<# if ( data.variantImages ) { #>
						<# _.each(data.variantImages, function( image ) { #>
							<li id="{{ image.cssID}}" data-image-id="{{ image.imageID }}">
								<a href class="image-edit" data-image-id="{{ image.imageID }}">
									<img src="{{ image.thumbURL }}" data-large="{{ image.largeURL }}" data-thumb="{{ image.thumbURL }}" alt="" />
									<span class="overlay"></span>
								</a>
								<span class="remove-item">&times;</span>
								<input type="hidden" name="it-exchange-product-variant-images[{{ data.comboHash }}][]" value="{{ image.imageID }}" />
							</li>
						<# }) #>
					<# } #>
					<li class="it-exchange-add-new-image it-exchange-add-new-variant-image disable-sorting<# if ( ! data.featuredImage ) { #> empty<# } #>">
						<a href data-variant-id="{{ data.comboHash }}">
							<span><?php _e( 'Add Images', 'LION' ); ?></span>
						</a>
					</li>
				</ul>
			</div>
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
