<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages product attributes
 *
 * Here all product attributes are defined and managed.
 *
 * @version		1.0.0
 * @package		ecommerce-product-catalog/includes
 * @author 		Norbert Dreszer
 */
add_action( 'product_details', 'show_sku', 8, 1 );

/**
 * Shows product SKU table
 *
 * @param object $post
 * @param array $single_names
 */
function show_sku( $product_id = false ) {
	if ( is_object( $product_id ) && isset( $product_id->ID ) ) {
		$product_id = $product_id->ID;
	}
	ic_show_template_file( 'product-page/product-sku.php', AL_BASE_TEMPLATES_PATH, $product_id );
}

/**
 * Returns sku table for product page
 *
 * @param int $product_id
 * @param array $single_names
 * @return string
 */
function get_product_sku_table( $product_id, $single_names ) {
	ob_start();
	show_sku( $product_id );
	return ob_get_clean();
}

/**
 * Returns SKU
 *
 * @param int $product_id
 * @return string
 */
function get_product_sku( $product_id ) {
	$sku = get_post_meta( $product_id, '_sku', true );
	return $sku;
}
