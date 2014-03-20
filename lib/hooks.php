<?php
/**
 * Enqueues Variant scripts to WordPress Dashboard
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix WordPress passed variable
 * @return void
*/
function it_exchange_variants_addon_admin_wp_enqueue_scripts( $hook_suffix ) { 
	global $post;
	
	if ( isset( $_REQUEST['post_type'] ) ) { 
		$post_type = $_REQUEST['post_type'];
	} else {
		if ( isset( $_REQUEST['post'] ) ) 
			$post_id = (int) $_REQUEST['post'];
		elseif ( isset( $_REQUEST['post_ID'] ) ) 
			$post_id = (int) $_REQUEST['post_ID'];
		else
			$post_id = 0;

		if ( $post_id )
			$post = get_post( $post_id );

		if ( isset( $post ) && !empty( $post ) ) 
			$post_type = $post->post_type;
	}   
	
	if ( isset( $post_type ) && 'it_exchange_prod' === $post_type && ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) ) {
		wp_enqueue_script( 'it-exchange-variants-addon-colorpicker', ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/js/colorpicker/colorpicker.js' );
		wp_enqueue_script( 'it-exchange-variants-addon-add-edit-product', ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/js/add-edit-product.js', array( 'jquery', 'it-exchange-dialog', 'it-exchange-variants-addon-colorpicker' ) );

		// Backbone scripts
		$url_base = ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/js/';
		$deps     = array( 'jquery', 'wp-backbone', 'underscore', 'jquery-ui-sortable', 'it-exchange-dialog' );
		wp_enqueue_script( 'it-exchange-variants-addon-variant-models',  $url_base . 'models/variant-models.js', $deps );
		wp_enqueue_script( 'it-exchange-variants-addon-variant-collections',  $url_base . 'collections/variant-collections.js', $deps );
		wp_enqueue_script( 'it-exchange-variants-addon-variant-admin-views',  $url_base . 'views/variant-admin-views.js', $deps );
		wp_enqueue_script( 'it-exchange-variants-addon-variant-admin-core',  $url_base . 'admin-variants.js', $deps );
		add_action( 'admin_footer', 'it_exchange_variants_addon_load_backbone_admin_templates' );
	}
}
add_action( 'admin_enqueue_scripts', 'it_exchange_variants_addon_admin_wp_enqueue_scripts' );

/**
 * Enqueues Variant styles to WordPress Dashboard
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix WordPress passed variable
 * @return void
*/
function it_exchange_variants_addon_admin_wp_enqueue_styles( $hook_suffix ) { 
	global $post;

	if ( isset( $_REQUEST['post_type'] ) ) { 
		$post_type = $_REQUEST['post_type'];
	} else {
		if ( isset( $_REQUEST['post'] ) ) 
			$post_id = (int) $_REQUEST['post'];
		elseif ( isset( $_REQUEST['post_ID'] ) ) 
			$post_id = (int) $_REQUEST['post_ID'];
		else
			$post_id = 0;

		if ( $post_id )
			$post = get_post( $post_id );

		if ( isset( $post ) && !empty( $post ) ) 
			$post_type = $post->post_type;
	}   
	
	if ( isset( $post_type ) && 'it_exchange_prod' === $post_type ) {
		wp_enqueue_style( 'it-exchange-variants-addon-colorpicker', ITUtility::get_url_from_file( dirname( __FILE__) ) . '/js/colorpicker/colorpicker.css' );
		wp_enqueue_style( 'it-exchange-variants-addon-add-edit-product', ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/css/add-edit-product.css' );
	}
}
add_action( 'admin_print_styles', 'it_exchange_variants_addon_admin_wp_enqueue_styles' );

/**
 * Checks to see if the presets exist
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_variants_addon_setup_preset_variants() {
	it_exchange_variants_addon_create_inital_presets();
}
add_action( 'admin_init', 'it_exchange_variants_addon_setup_preset_variants' );

/**
 * Prints the hash id for a combo of variants via ajax
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_variants_addon_ajax_get_selected_variants_id_hash() {
	if ( empty( $_POST['it_exchange_selected_variants'] ) )
		return false;

	$variants_to_hash = array();
	foreach( (array) $_POST['it_exchange_selected_variants'] as $id ) {
		if ( $variant = it_exchange_variants_addon_get_variant( $id ) )
			$variants_to_hash[empty( $variant->post_parent ) ? $id : $variant->post_parent] = $id;
	}
	die( empty( $variants_to_hash ) ? false : it_exchange_variants_addon_get_selected_variants_id_hash( $variants_to_hash ) );
}
add_action( 'wp_ajax_it_exchange_variants_get_selected_id_hash', 'it_exchange_variants_addon_ajax_get_selected_variants_id_hash' );
add_action( 'wp_ajax_nopriv_it_exchange_variants_get_selected_id_hash', 'it_exchange_variants_addon_ajax_get_selected_variants_id_hash' );

function it_exchange_variants_addon_load_backbone_admin_templates() {
	include( dirname( __FILE__ ) . '/js/templates/admin.php' );
}

function it_exchange_variants_json_api() {

	$endpoint   = empty( $_REQUEST['endpoint'] ) ? false : $_REQUEST['endpoint'];
	$product_id = empty( $_REQUEST['product-id'] ) ? false : $_REQUEST['product-id'];
	$variant_id = empty( $_REQUEST['product-variant'] ) ? false : $_REQUEST['product-variant'];
	$preset_id  = empty( $_REQUEST['preset-id'] ) ? false : $_REQUEST['preset-id'];
	$parent_id  = empty( $_REQUEST['parent-id'] ) ? false : $_REQUEST['parent-id'];
	$ui_type    = empty( $_REQUEST['ui-type'] ) ? false : $_REQUEST['ui-type'];

	if ( empty( $endpoint ) )
		return false;

	if ( 'product-variants' == $endpoint ) {
		if ( ! empty( $product_id ) ) {
			$variants  = (array) it_exchange_get_variants_for_product( $product_id );
			$response = array();
			foreach( $variants as $variant ) {
				if ( empty( $variant->ID ) )
					continue;
				$response_variant = new stdClass();
				$response_variant->id            = $variant->ID;
				$response_variant->title         = $variant->post_title;
				$response_variant->order         = $variant->menu_order;
				$response_variant->uiType        = $variant->ui_type;
				$response_variant->presetSlug    = $variant->preset_slug;
				$response_variant->valuesPreview = '';

				$response[] = $response_variant;
			}
			die( json_encode( $response ) );
		}
	} else if ( 'variant-values' == $endpoint ) {
		if ( ! empty( $variant_id ) ) {
			$parent   = it_exchange_variants_addon_get_variant( $variant_id );
			$variants = (array) it_exchange_get_values_for_variant( $variant_id );
			$response = array();
			foreach( $variants as $variant ) {
				if ( empty( $variant->ID ) )
					continue;
				$response_variant = new stdClass();
				$response_variant->id            = $variant->ID;
				$response_variant->parentId     = $variant->post_parent;
				$response_variant->title         = $variant->post_title;
				$response_variant->order         = $variant->menu_order;
				$response_variant->uiType        = empty( $parent->ui_type ) ? false : $parent->ui_type;
				$response_variant->color         = empty( $variant->color ) ? false : $variant->color;
				$response_variant->imageUrl      = empty( $variant->image ) ? false : $variant->image;
				$response_variant->isDefault     = empty( $variant->default ) ? '' : 'checked';
				$response_variant->presetSlug    = empty( $parent->preset_slug ) ? false : $parent->preset_slug;

				$response[] = $response_variant;
			}
			die( json_encode( $response ) );
		}
	} else if ( 'variant-values-from-preset' == $endpoint ) {
		if ( ! empty( $preset_id ) ) {
			$preset = it_exchange_variants_addon_get_preset( $preset_id );
			$values = empty( $preset->values ) ? array() : $preset->values;

			$response = array();
			foreach( $values as $value ) {
				$response_value = new stdClass();
				$response_value->id            = uniqid(rand());
				$response_value->parentId      = $parent_id;
				$response_value->title         = $value['title'];
				$response_value->order         = empty( $value['order'] ) ? 0 : $value['order'];
				$response_value->color         = empty( $value['color'] ) ? false : $value['color'];
				$response_value->imageUrl      = empty( $value['image'] ) ? false : $value['image'];
				$response_value->uiType        = empty( $preset->ui_type ) ? false : $preset->ui_type;
				$response_value->isDefault     = empty( $preset->default ) ? '' : 'checked';
				$response_value->presetSlug    = empty( $preset->slug ) ? false : $preset->slug;

				$response[] = $response_value;
			}
			die( json_encode( $response ) );
		}
	} else if ( 'variant-value-from-ui-type' == $endpoint ) {
		if ( ! empty( $parent_id ) && ! empty( $ui_type ) ) {
			if ( $presets = it_exchange_variants_addon_get_presets( array( 'core_only' => true ) ) ) {

				foreach( $presets as $preset ) {
					if ( ! $preset->is_template || empty( $preset->ui_type ) || $ui_type != $preset->ui_type || empty( $preset->values[0] ) )
						continue;

					$value = $preset->values[0];

					$response = new stdClass();
					$response->id            = uniqid(rand());
					$response->parentId      = (int) $parent_id;
					$response->title         = $preset->title;
					$response->order         = empty( $value['order'] ) ? 0 : $value['order'];
					$response->color         = empty( $value['color'] ) ? false : $value['color'];
					$response->imageUrl      = empty( $value['image'] ) ? false : $value['image'];
					$response->uiType        = empty( $preset->ui_type ) ? false : $preset->ui_type;
					$response->isDefault     = '';
					$response->presetSlug    = empty( $preset->slug ) ? false : $preset->slug;

					// We only want one so die here
					die( json_encode( $response ) );
				}
			}
		}
	} else if ( 'core-presets' == $endpoint ) {
		if ( $presets = it_exchange_variants_addon_get_presets( array( 'core_only' => true ) ) ) {
			$reponse = array();
			foreach( $presets as $preset ) {
				if ( ! $preset->is_template )
					continue;
				$core_preset             = new stdClass();
				$core_preset->id         = $preset->ID;
				$core_preset->slug       = $preset->slug;
				$core_preset->title      = $preset->title;
				$core_preset->values     = $preset->values;
				$core_preset->order      = empty( $preset->menu_order ) ? 0 : $preset->menu_order;
				$core_preset->uiType     = empty( $preset->ui_type ) ? '' : $preset->ui_type;
				$core_preset->imageAlt   = $preset->title;
				$core_preset->imageThumb = ( ! empty( $preset->ui_type ) && is_file( dirname( __FILE__ ) . '/images/presets/' . $preset->ui_type . '.png' ) ) 
					? ITUtility::get_url_from_file( dirname( __FILE__ ) . '/images/presets/' . $preset->ui_type . '.png' ) 
					: ''; 

				$response[] = $core_preset;
			}
			die( json_encode( $response ) );
		}
	} else if ( 'saved-presets' == $endpoint ) {
		if ( $presets = it_exchange_variants_addon_get_presets() ) {
			$reponse = array();
			foreach( $presets as $preset ) {
				if ( $preset->is_template )
					continue;
				$core_preset             = new stdClass();
				$core_preset->id         = $preset->ID;
				$core_preset->slug       = $preset->slug;
				$core_preset->title      = $preset->title;
				$core_preset->order      = empty( $preset->menu_order ) ? 0 : $preset->menu_order;
				$core_preset->uiType     = empty( $preset->ui_type ) ? '' : $preset->ui_type;
				$core_preset->values     = $preset->values;
				$core_preset->imageAlt   = $preset->title;
				$core_preset->imageThumb = ( ! empty( $preset->ui_type ) && is_file( dirname( __FILE__ ) . '/images/presets/' . $preset->ui_type . '.png' ) ) 
					? ITUtility::get_url_from_file( dirname( __FILE__ ) . '/images/presets/' . $preset->ui_type . '.png' ) 
					: ''; 

				$response[] = $core_preset;
			}
			die( json_encode( $response ) );
		}
	}
	return false;
}
add_action( 'wp_ajax_it-exchange-variants-json-api', 'it_exchange_variants_json_api' );
