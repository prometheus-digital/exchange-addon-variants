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
	
	if ( isset( $post_type ) && 'it_exchange_prod' === $post_type ) {
		wp_enqueue_script( 'it-exchange-variants-addon-colorpicker', ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/js/colorpicker/colorpicker.js' );
		wp_enqueue_script( 'it-exchange-variants-addon-add-edit-product', ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/js/add-edit-product.js', array( 'jquery', 'jquery-ui-sortable', 'it-exchange-dialog', 'it-exchange-variants-addon-colorpicker' ) );
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
