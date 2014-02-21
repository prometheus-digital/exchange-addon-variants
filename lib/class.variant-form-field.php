<?php
/**
 * This generates a series of variant form field for the variant metabox.
 * @since 1.0.0
 * @package IT_Exchange_Variants_Addon
*/
class IT_Exchange_Variants_Addon_Form_Field {
	
	var $init_type              = false;
	var $variant_id             = false;
	
	var $object                 = false;
	var $object_values          = array();
	var $id                     = false;
	var $variant_title          = false;
	var $variant_values_preview = '';
	var $div                    = '';

	function IT_Exchange_Variants_Addon_Form_Field( $type, $variant=false ) {
		if ( ! in_array( $type, array( 'template', 'saved', 'existing' ) ) )
			return;

		if ( is_object( $variant ) && 'IT_Exchange_Variants_Addon_Variant' == get_class( $variant ) ) {
			$this->variant_id = $variant->ID;
			$this->object     = $variant;
		} else {
			$this->variant_id = (int) $variant;
		}

		$this->init_type    = $type;
		$this->variant_slug = false;

		$init_func = 'init_' . $type;
		$this->$init_func();
		$this->start_div();
		$this->add_variant_title();
		$this->add_variant_values();
		$this->close_div();
	}

	function init_existing() {
		// Load the variant data
		if ( empty( $this->object->ID ) || $this->variant_id != $this->object->ID )
			$this->object = it_exchange_variants_addon_get_variant( $this->variant_id );


		$this->variant_slug  = $this->object->post_name;
		$this->object->slug  = $this->object->post_name;
		$this->object->title = $this->object->post_title;

		$this->object_values = $this->object->get_property( 'values' );
		
		$this->id            = $this->object->ID;
		$this->variant_title = $this->object->post_title;
	}

	function init_template() {

		// Load the template data
		$this->object        = it_exchange_variants_addon_get_preset( $this->variant_id );
		$this->variant_slug  = $this->object->get_property( 'slug' );
		$this->object_values = $this->object->get_property( 'values' );
		
		// Set a temp ID for this new div
		$this->id            = uniqid();
		$this->variant_title = sprintf( __( 'New %s Variant Title', 'LION' ), $this->object->get_property( 'title' ) );
	}

	function init_saved() {

		// Load the template data
		$this->object        = it_exchange_variants_addon_get_preset( $this->variant_id );
		$this->variant_slug  = $this->object->get_property( 'slug' );
		$this->object_values = $this->object->get_property( 'values' );

		// Set a temp ID for this new div
		$this->id            = uniqid();
		$this->variant_title = sprintf( __( 'New %s Variant Title', 'LION' ), $this->object->get_property( 'title' ) );
	}

	function start_div() {
		$data_open = ( 'existing' == $this->init_type ) ? 'false' : 'true';
		$this->div .= '<div class="it-exchange-existing-variant" data-variant-id="' . esc_attr( $this->variant_id ) . '" data-variant-open="' . esc_attr( $data_open ) . '">';
	}

	function add_variant_title() {
		if ( ! is_object( $this->object ) )
			return;

		// Setup preview text
		$preview_fields = array();
		if ( 'existing' == $this->init_type && ! $this->object->is_variant_value && ! empty( $this->object_values ) ) {
			foreach( $this->object_values as $object_value ) {
				$preview_fields[] = $object_value->title;
			}
		}
		$this->variant_values_preview = implode( $preview_fields, ', ' );
		$title = '
		<div class="variant-title">
			<span class="variant-title-move">
				<input type="hidden" name="it-exchange-product-variants[variants][' . esc_attr( $this->id ) . '][order]" value="' . esc_attr( $this->object->menu_order ) . '" class="parent-variant-order-input" />
			</span>
			<span class="variant-title-text variant-text-placeholder">' . $this->variant_title . '</span>
			<input type="hidden" name="it-exchange-product-variants[variants][' . esc_attr( $this->id ) . '][id]" value="' . esc_attr( $this->id ) . '">
			<input type="text" name="it-exchange-product-variants[variants][' . esc_attr( $this->id ) . '][title]" value="' . esc_attr( $this->variant_title ) . '" class="variant-text-input hidden">
			<input type="hidden" name="it-exchange-product-variants[variants][' . esc_attr( $this->id ) . '][ui_type]" value="' . esc_attr( $this->object->ui_type ) . '">
			<input type="hidden" name="it-exchange-product-variants[variants][' . esc_attr( $this->id ) . '][preset_slug]" value="' . esc_attr( $this->variant_slug ) . '">
			<span class="variant-title-values-preview">
				' . $this->variant_values_preview . '
			</span>
			<span class="variant-title-delete it-exchange-remove-item">
				&times;
			</span>
		</div>
		';

		$this->div .= $title;
	}

	function add_variant_values() {
		if ( ! is_object( $this->object) || ! empty( $this->object->post_parent ) )
			return;

		$values = '
		<div class="variant-values">
			<div class="edit-variant">
				<span class="label">' . __( 'Values', 'LION' ) . ' ' . it_exchange_admin_tooltip( 'tooltip goes here', false ) .'</span>
				<ul class="variant-values-list">
					<li class="new-variant-value clearfix hidden" data-variant-value-id="" data-variant-value-parent="' . esc_attr( $this->id ) . '">
						<div class="variant-value-reorder" data-variant-value-order="">
							<input disabled="disabled" type="hidden" name="" value="" class="new-variant-order-field variant-order-input" />
						</div>
						<div class="variant-value-info">
							<input disabled="disabled" type="radio" class="new-variant-default-field variant-radio-option" name="it-exchange-product-variants[variants][' . esc_attr( $this->id ) . '][default]" value=""/>
							<span class="variant-value-name variant-text-placeholder">' . __( 'New Value', 'LION' ) . '</span>
							<input disabled="diabled" type="text" name="" value="' . __( 'New Value', 'LION' ) . '" class="new-variant-name-field variant-text-input hidden">
							<input disabled="disabled" type="hidden" name="" value="' . esc_attr( $this->id ) . '" class="new-variant-parent-field variant-post-parent-hidden" />
							' . $this->get_variant_value_visual( true ) . '
						</div>
						<div class="variant-value-delete">
							<a href class="it-exchange-remove-item">&times;</a>
						</div>
					</li>';

				$int = 0;

				foreach( $this->object_values as $value ) {
					$int++;
					$value               = $this->get_child_variant( $value );
					$this->current_value = $value;
					$value_id            = $value->get_property( 'ID' );
					$value_title         = $value->get_property( 'title' );
					$checked             = checked( $this->object->default, $value_id, false );

					$values .= '
					<li class="clearfix" data-variant-value-id="' . esc_attr( $value_id ) . '" data-variant-value-parent="' . esc_attr( $this->id ) . '">
						<div class="variant-value-reorder" data-variant-value-order="' . esc_attr( $int ) . '">
							<input type="hidden" name="it-exchange-product-variants[variants][' . esc_attr( $value_id ) . '][order]" value="' . esc_attr( $int ) . '" class="variant-order-input" />
						</div>
						<div class="variant-value-info">
							<input type="radio" class="variant-radio-option" name="it-exchange-product-variants[variants][' . esc_attr( $this->id ) . '][default]" value="' . esc_attr( $value_id ) . '" ' . $checked . '/>
							<span class="variant-value-name variant-text-placeholder">' . $value_title . '</span>
							<input type="text" name="it-exchange-product-variants[variants][' . esc_attr( $value_id ) . '][title]" value="' . esc_attr( $value_title ) . '" class="variant-text-input hidden" />
							<input type="hidden" name="it-exchange-product-variants[variants][' . esc_attr( $value_id ) . '][post_parent]" value="' . esc_attr( $this->id ) . '" class="variant-post-parent-hidden" />
							' . $this->get_variant_value_visual() . '
						</div>
						<div class="variant-value-delete">
							<a href class="it-exchange-remove-item">&times;</a>
						</div>
					</li>
					';
				}

				$values .= '
				</ul>
				<div class="add-variant-value">
					<input type="button" class="button add-variant-value-button" value="Add Value" />
				</div>
			</div>
		</div>
		';
		$this->div .= $values;
	}

	function get_child_variant( $value ) {
		switch( $this->init_type ) {
			case 'template' :
				return is_array( $value ) ? it_exchange_variants_addon_get_saved_preset_value( $value ) : it_exchange_variants_addon_get_preset( $value );	
				break;
			case 'saved' :
				return it_exchange_variants_addon_get_saved_preset_value( $value );
				break;
			case 'existing' :
				//ITUtility::print_r($value);
				//die( __FILE__ . ' | ' . __LINE__ );
				return $value;
		}
	}

	function get_variant_value_visual( $is_new_template=false ) {
		$html = '';
		$disabled = empty( $is_new_template ) ? '' : 'disabled="disabled" ';
		switch( $this->init_type ) {
			case 'template' :
				$slug = $this->object->get_property( 'slug' );
				if ( 'template-image' == $slug ) {
					$variant_id = empty( $is_new_template ) ? $this->current_value->ID : '';
					$html = '
					<a class="variant-value-image variant-value-has-image">
						<span class="variant-value-image-placeholder"></span>
					</a>
					<input ' . $disabled . 'type="hidden" value="" name="it-exchange-product-variants[variants][' . esc_attr( $variant_id ) . '][image]" class="it-exchange-variants-image" />
					';
				} elseif ( 'template-hex' == $slug ) {
					$variant_id = empty( $is_new_template ) ? $this->current_value->ID : '';
					$html = '
					<div class="variant-value-hex">
						<input ' . $disabled . 'type="text" value="#F1FFDE" name="it-exchange-product-variants[variants][' . esc_attr( $variant_id ) . '][color]" class="it-exchange-variants-colorpicker" />
					</div>
					';
				}
				break;
			case 'saved' :
				if ( ! empty( $this->object->post_parent ) ) {
					$parent = it_exchange_variant_addon_get_preset( $this->object->post_parent );
					$ui_type = $parent->get_property->ui_type;
				} else {
					$ui_type = $this->object->get_property( 'ui_type' );
				}
				if ( 'image' == $ui_type ) {
					$html = '
					<a class="variant-value-image variant-value-has-image">
						<span class="variant-value-image-placeholder"></span>
					</a>
					<input type="hidden" value="" name="it-exchange-product-variants[variants][' . esc_attr( $variant_id ) . '][image]" class="it-exchange-variants-image" />
					';
				} elseif ( 'color' == $ui_type ) {
					$default    = empty( $this->current_value->color ) ? $this->object->get_property( 'default' ) : $this->current_value->get_property( 'color' );
					$variant_id = empty( $this->current_value->ID ) ? $this->id : $this->current_value->ID;

					if ( ! empty( $is_new_template ) ) {
						$default    = '#ffffff';
						$variant_id = '';
					}
					$html = '
					<div class="variant-value-hex">
						<input ' . $disabled . 'type="text" value="' . esc_attr( $default ) . '" name="it-exchange-product-variants[variants][' . esc_attr( $variant_id ) . '][color]" class="it-exchange-variants-colorpicker" />
					</div>
					';
				}
				break;
			case 'existing' :
				if ( ! empty( $this->object->post_parent ) ) {
					$parent = it_exchange_variant_addon_get_preset( $this->object->post_parent );
					$ui_type = $parent->get_property->ui_type;
				} else {
					$ui_type = $this->object->get_property( 'ui_type' );
				}
				$variant_id = empty( $this->current_value->ID ) ? $this->id : $this->current_value->ID;
				$image_src  = empty( $this->current_value->image ) ? '' : $this->current_value->image;
				$image_element = empty( $image_src ) ? '' : '<img src="' . esc_attr( $image_src ) . '" alt="">';

				if ( 'image' == $ui_type ) {
					$html = '
					<a class="variant-value-image variant-value-has-image">
						<span class="variant-value-image-placeholder">
						' . $image_element . '		
						</span>
					</a>
					<input type="hidden" value="' . esc_attr( $image_src ) . '" name="it-exchange-product-variants[variants][' . esc_attr( $variant_id ) . '][image]" class="it-exchange-variants-image" />
					';
				} elseif ( 'color' == $ui_type ) {
					$default    = empty( $this->current_value->color ) ? $this->object->get_property( 'default' ) : $this->current_value->get_property( 'color' );
					$variant_id = empty( $this->current_value->ID ) ? $this->id : $this->current_value->ID;

					if ( ! empty( $is_new_template ) ) {
						$default    = '#ffffff';
						$variant_id = '';
					}
					$html = '
					<div class="variant-value-hex">
						<input ' . $disabled . 'type="text" value="' . esc_attr( $default ) . '" name="it-exchange-product-variants[variants][' . esc_attr( $variant_id ) . '][color]" class="it-exchange-variants-colorpicker" />
					</div>
					';
				}
				break;
		}
		return $html;
	}

	function close_div() {
		$this->div .= '</div>';
	}
}
