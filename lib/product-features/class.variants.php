<?php
/**
 * This will control email messages with any product types that register email message support.
 * By default, it registers a metabox on the product's add/edit screen and provides HTML / data for the frontend.
 *
 * @since 1.0.0
 * @package IT_Exchange
*/


class IT_Exchange_Product_Feature_Variants {

	/**
	 * Constructor. Registers hooks
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function IT_Exchange_Product_Feature_Variants() {
		if ( is_admin() ) {
			add_action( 'load-post-new.php', array( $this, 'init_feature_metaboxes' ) );
			add_action( 'load-post.php', array( $this, 'init_feature_metaboxes' ) );
			add_action( 'it_exchange_save_product', array( $this, 'save_feature_on_product_save' ), 9 );
		}
		add_action( 'it_exchange_enabled_addons_loaded', array( $this, 'register_feature_support' ) );
		add_action( 'it_exchange_enabled_addons_loaded', array( $this, 'add_feature_support_to_product_types' ) );
		add_action( 'it_exchange_update_product_feature_variants', array( $this, 'save_feature' ), 9, 3 );
		add_filter( 'it_exchange_get_product_feature_variants', array( $this, 'get_feature' ), 9, 3 );
		add_filter( 'it_exchange_product_has_feature_variants', array( $this, 'product_has_feature') , 9, 2 );
		add_filter( 'it_exchange_product_supports_feature_variants', array( $this, 'product_supports_feature') , 9, 2 );
	}

	/**
	 * Register the product feature and add it to enabled product-type addons
	 *
	 * @since 1.0.0
	*/
	function register_feature_support() {
		// Register the product feature
		$slug        = 'variants';
		$description = __( 'Allows store owners to add variant options to iThemes Exchange product types.', 'LION' );
		it_exchange_register_product_feature( $slug, $description );
	}

	/**
	 * Adds the feature support to active product types
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function add_feature_support_to_product_types() {
		// Add it to all enabled product-type addons
		$products = it_exchange_get_enabled_addons( array( 'category' => 'product-type' ) );
		foreach( $products as $key => $params ) {
			if ( in_array( $params['slug'], array('invoices-product-type', 'membership-product-type') ) )
				continue;
			it_exchange_add_feature_support_to_product_type( 'variants', $params['slug'] );
		}
	}

	/**
	 * Register's the metabox for any product type that supports the feature
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function init_feature_metaboxes() {

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

		if ( !empty( $_REQUEST['it-exchange-product-type'] ) )
			$product_type = $_REQUEST['it-exchange-product-type'];
		else
			$product_type = it_exchange_get_product_type( $post );

		if ( !empty( $post_type ) && 'it_exchange_prod' === $post_type ) {
			if ( !empty( $product_type ) &&  it_exchange_product_type_supports_feature( $product_type, 'variants' ) )
				add_action( 'it_exchange_product_metabox_callback_' . $product_type, array( $this, 'register_metabox' ) );
		}

	}

	/**
	 * Registers the feature metabox for a specific product type
	 *
	 * Hooked to it_exchange_product_metabox_callback_[product-type] where product type supports the feature
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function register_metabox() {
		add_meta_box( 'it-exchange-product-variants', __( 'Product Variants', 'LION' ), array( $this, 'print_metabox' ), 'it_exchange_prod', 'normal' );

		// Add Base Price Variants metabox if variants already exist
		if ( ! empty( $GLOBALS['post']->ID ) ) {
			$post_id = $GLOBALS['post']->ID;
		} else if ( isset( $_REQUEST['post'] ) ) {
			$post_id = (int) $_REQUEST['post'];
		} else if ( isset( $_REQUEST['post_ID'] ) )  {
			$post_id = (int) $_REQUEST['post_ID'];
		} else {
			$post_id = 0;
		}
		$variants = it_exchange_get_product_feature( $post_id, 'variants' );
		if ( ! empty( $variants['variants'] ) )
			add_meta_box( 'it-exchange-product-pricing-variants', __( 'Variant Pricing', 'LION' ), array( $this, 'print_variant_pricing_metabox' ), 'it_exchange_prod', 'normal' );
	}

	/**
	 * This echos the feature metabox.
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function print_metabox( $post ) {
		$product_feature_value  = it_exchange_get_product_feature( $post->ID, 'variants' );
		$variants_enabled       = ( ! empty( $product_feature_value['enabled'] ) && 'yes') ? 1 : 0;
		$existing_variants      = empty( $product_feature_value['variants'] ) ? 0 : 1;
		?>
		<script type="text/javascript">
			var itExchangeVariants = itExchangeVariants || {};
			itExchangeVariants.productFeatureSettings = {
				productId:       document.getElementById('post_ID').value,
				variantsEnabled: <?php echo esc_js( $variants_enabled ); ?>,
				hasVariants:     <?php echo esc_js( $existing_variants ); ?>
			};
		</script>
		<p><?php _e( 'Error loading variants. Please try again', 'LION' ); // This div should be replaced by backbone JS ?></p>
		<?php
	}

	/**
	 * The variant pricing metabox
	 *
	 * @since 1.0.0
	*/
	function print_variant_pricing_metabox( $post ) {
		?><div class="it-exchange-product-pricing-variants-inner"><p><?php _e( 'Loading Pricing Variants', 'LION' ); ?> <!-- This div will be destroyed by backbone --></div><?php
	}

	/**
	 * This saves the value
	 *
	 * @since 1.0.0
	 *
	 * @param object $post wp post object
	 * @return void
	*/
	function save_feature_on_product_save() {

		// Abort if we can't determine a product type
		if ( ! $product_type = it_exchange_get_product_type() )
			return;

		// Abort if we don't have a product ID
		$product_id = empty( $_POST['ID'] ) ? false : $_POST['ID'];
		if ( ! $product_id )
			return;

		// Abort if this product type doesn't support this feature
		if ( ! it_exchange_product_type_supports_feature( $product_type, 'variants' ) )
			return;

		// Abort if nothing changed
		if ( empty( $_POST['it-exchange-variants-updated'] ) )
			return;

        // Save options
		if ( isset( $_POST['it-exchange-product-variants'] ) ) {

			// Remove or hook to prevent endless loops.
			remove_action( 'it_exchange_save_product', array( $this, 'save_feature_on_product_save' ), 9 );

			// POST data
			$new_variant_data = $_POST['it-exchange-product-variants'];
			$new_variants     = empty( $_POST['it-exchange-product-variants']['variants'] ) ? array() : $_POST['it-exchange-product-variants']['variants'];

			// Grab existing variants postmeta
			$existing_variant_data = (array) it_exchange_get_product_feature( $product_id, 'variants' );

			// Were variants just disabled? Save that if they were.
			if ( empty( $new_variant_data['enabled'] ) || 'no' == $new_variant_data['enabled'] ) {
				$existing_variant_data['enabled'] = 'no';
				it_exchange_update_product_feature( $product_id, 'variants', $existing_variant_data );
			} else {
				// Enabled?
				$existing_variant_data['enabled'] = 'yes';

				// Save variant details for product
				$existing_variants = empty( $existing_variant_data['variants'] ) ? array() : (array) $existing_variant_data['variants'];

				// Loop through saved data and delete anything that is not in the new data (because it was deleted)
				foreach( $existing_variants as $array_key => $variant_id ) {
					if ( ! isset( $new_variants[$variant_id] ) || ! it_exchange_variants_addon_get_variant( $variant_id ) ) {
						// Delete post
						wp_delete_post( $variant_id, true );

						// Delete removed existing variants
						unset( $existing_variants[$array_key] );
					} else {
						// Update existing variants
						$new_variants[$variant_id]['post_title'] = empty( $new_variants[$variant_id]['title'] ) ? '' : $new_variants[$variant_id]['title'];
						$new_variants[$variant_id]['menu_order'] = empty( $new_variants[$variant_id]['order'] ) ? 0 : $new_variants[$variant_id]['order'];
						it_exchange_variants_addon_update_variant( $variant_id, $new_variants[$variant_id] );

						// Remove from new variants list so we don't add again
						unset( $new_variants[$variant_id] );
					}
				}

				// Init var for default cache
				$defaults_needing_updated = array();
				$new_ids_to_wp_ids        = array();

				// Loop through remaining new variants and add them
				foreach( $new_variants as $variant_id => $data ) {
					// We shouldn't have any existing variants in the array by now but lets check just to make sure
					if ( it_exchange_variants_addon_get_variant( $variant_id ) ) {
						// Update if it was found
						it_exchange_variants_addon_update_variant( $variant_id, $data );
						continue;
					}

					// Add new variants
					$args = array();
					if ( ! empty( $data['title'] ) )
						$args['post_title'] = $data['title'];
					if ( ! empty( $data['post_parent'] ) )
						$args['post_parent'] = $data['post_parent'];
					if ( ! empty( $data['post_parent'] ) && isset( $new_ids_to_wp_ids[$data['post_parent']] ) )
						$args['post_parent'] = $new_ids_to_wp_ids[$data['post_parent']];
					if ( ! empty( $data['default'] ) )
						$args['default'] = $data['default'];
					if ( ! empty( $data['order'] ) )
						$args['menu_order'] = $data['order'];
					if ( ! empty( $data['image'] ) )
						$args['image'] = $data['image'];
					if ( ! empty( $data['color'] ) )
						$args['color'] = $data['color'];
					if ( ! empty( $data['ui_type'] ) )
						$args['ui_type'] = $data['ui_type'];
					if ( ! empty( $data['preset_slug'] ) )
						$args['preset_slug'] = $data['preset_slug'];

					// Default may not be added yet so we need to do some checking and temp caching
					if ( ! empty( $data['default'] ) && ! it_exchange_variants_addon_get_variant( $data['default'] ) ) {
						$defaults_needing_updated[$variant_id] = $data['default'];
					} else if ( ! empty( $data['default'] ) ) {
						$args['default'] = $data['default'];
					}

					// Create Variant
					if ( $new_id = it_exchange_variants_addon_create_variant( $args ) ) {

						// Update the temp variant_id with the new_id in the defaults_needing_updated array if it is present
						if ( isset( $defaults_needing_updated[$variant_id] ) ) {
							$defaults_needing_updated[$new_id] = $defaults_needing_updated[$variant_id];
							unset( $defaults_needing_updated[$variant_id] );
						}

						// Check previously chached defaults. If this variant is a value, update the parent (key) with its new ID and unset from array
						if ( $parent_id = array_search( $variant_id, $defaults_needing_updated ) ) {
							it_exchange_variants_addon_update_variant( $parent_id, array( 'default' => $new_id ) );
							unset( $defaults_needing_updated[$parent_id] );
						}
						$existing_variants[] = $new_id;
						$new_ids_to_wp_ids[$variant_id] = $new_id;
					}
				}

				$existing_variant_data['variants'] = $existing_variants;

				// Update
				it_exchange_update_product_feature( $product_id, 'variants', $existing_variant_data );
			}

			// Add our action back
			add_action( 'it_exchange_save_product', array( $this, 'save_feature_on_product_save' ), 9 );
		} else {
			$existing_variant_data            = (array) it_exchange_get_product_feature( $product_id, 'variants' );
			$existing_variant_data['enabled'] = 'no';
			it_exchange_update_product_feature( $product_id, 'variants', $existing_variant_data );
		}

	}

	/**
	 * This updates the feature for a product
	 *
	 * @since 1.0.0
	 *
	 * @param integer $product_id the product id
	 * @param mixed $new_value the new value
	 * @return bolean
	*/
	function save_feature( $product_id, $new_value, $options=array() ) {

		// Save version number of variants
		$version_array = empty( $new_value['variants'] ) ? false : (array) $new_value['variants'];
		if ( $version_array ) {
			sort( $version_array );
			$version_hash = md5( serialize( $version_array ) );
			if ( empty( $new_value['variants_version'] ) || $new_value['variants_version'] != $version_hash )
				$new_value['variants_version'] = $version_hash;
		}

		// Only accept settings for max_number (default) or 'enabled' (checkbox)
		update_post_meta( $product_id, '_it-exchange-product-variants', $new_value );
		return true;
	}

	/**
	 * Return the product's features
	 *
	 * @since 1.0.0
	 * @param mixed $existing the values passed in by the WP Filter API. Ignored here.
	 * @param integer product_id the WordPress post ID
	 * @return string product feature
	*/
	function get_feature( $existing, $product_id, $options=array() ) {
		if ( it_exchange_product_supports_feature( $product_id, 'variants' ) )
			return get_post_meta( $product_id, '_it-exchange-product-variants', true );
        return false;
	}

	/**
	 * Does the product have this feature?
	 *
	 * @since 1.0.0
	 * @param mixed $result Not used by core
	 * @param integer $product_id
	 * @return boolean
	*/
	function product_has_feature( $result, $product_id ) {
		// Does this product type support this feature?
		if ( false === $this->product_supports_feature( false, $product_id ) )
			return false;
		return (boolean) $this->get_feature( false, $product_id );
	}

	/**
	 * Does the product support this feature?
	 *
	 * This is different than if it has the feature, a product can
	 * support a feature but might not have the feature set.
	 *
	 * @since 1.0.0
	 * @param mixed $result Not used by core
	 * @param integer $product_id
	 * @return boolean
	*/
	function product_supports_feature( $result, $product_id ) {
		// Does this product type support this feature?
		$product_type = it_exchange_get_product_type( $product_id );
		if ( it_exchange_product_type_supports_feature( $product_type, 'variants' ) ) {
			if ( $variants = get_post_meta( $product_id, '_it-exchange-product-variants', true ) )
				return ( empty( $variants['enabled'] ) || 'yes' != $variants['enabled'] ) ? false : true;
		}
		return false;
	}
}
$IT_Exchange_Product_Feature_Variants = new IT_Exchange_Product_Feature_Variants();
