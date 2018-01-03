<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WP Product template functions
 *
 * Here all plugin template functions are defined.
 *
 * @version		1.1.3
 * @package		ecommerce-product-catalog/
 * @author 		Norbert Dreszer
 */
function get_product_adder_path( $auto = false ) {
	if ( $auto ) {
		return get_stylesheet_directory() . '/auto-product-adder.php';
	} else {
		return get_stylesheet_directory() . '/product-adder.php';
	}
}

if ( !function_exists( 'get_custom_templates_folder' ) ) {

	function get_custom_templates_folder() {
		return get_stylesheet_directory() . '/implecode/';
	}

}

function get_custom_product_page_path() {
	$folder = get_custom_templates_folder();
	return $folder . 'product-page.php';
}

function get_custom_product_page_inside_path() {
	$folder = get_custom_templates_folder();
	return $folder . 'product-page-inside.php';
}

function get_custom_product_listing_path() {
	$folder = get_custom_templates_folder();
	return $folder . 'product-listing.php';
}

function get_page_php_path() {
	if ( file_exists( get_stylesheet_directory() . '/page.php' ) ) {
		$path = get_stylesheet_directory() . '/page.php';
	} else {
		$path = get_theme_root() . '/' . get_template() . '/page.php';
	}
	return $path;
}

function get_index_php_path() {
	if ( file_exists( get_stylesheet_directory() . '/index.php' ) ) {
		$path = get_stylesheet_directory() . '/index.php';
	} else {
		$path = get_theme_root() . '/' . get_template() . '/index.php';
	}
	return $path;
}
