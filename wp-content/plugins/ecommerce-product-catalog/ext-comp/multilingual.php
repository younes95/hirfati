<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Integrates multilingual plugins with eCommerce Product Catalog
 *
 * @created: May 28, 2015
 * @package: ecommerce-product-catalog/ext-comp
 */
class ic_catalog_multilingual {

	function __construct() {
		add_action( 'pll_pre_init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'init' ), 1 );
	}

	function init() {
		if ( !is_admin() || is_ic_ajax() ) {
			//add_filter( 'product_listing_id', array( $this, 'replace_product_listing_id' ) );
			add_filter( 'product_listing_url', array( $this, 'replace_product_listing_url' ) );
			add_filter( 'ic_permalink_id', array( __CLASS__, 'replace_page_id' ) );
			//add_filter( 'pll_post_type_link', array( $this, 'pll_post_type_link' ) );
			//add_filter( 'pll_get_taxonomies', array( $this, 'catalog_taxonomies' ) );
			//add_filter( 'pll_get_post_types', array( $this, 'catalog_post_types' ) );
			add_action( 'ic_ajax_self_submit', array( $this, 'ajax_apply_lang' ) );
			add_action( 'admin_init', array( $this, 'ajax_apply_lang' ) );
			add_filter( 'ic_product_ajax_query_vars', array( $this, 'ajax_query_vars' ) );
			//add_action( 'pll_language_defined', array( $this, 'test' ) );
			//$this->test( 'eee' );
//add_filter( 'wpml_get_translated_slug', 'ic_multilingual_listing_slug', 10, 3 );
			//add_action( 'wp', array( $this, 'overwrite_archive_query' ) );
		}
	}

	function test( $lang ) {
		error_log( $lang );
		error_log( print_r( $_REQUEST, 1 ) );
	}

	/**
	 * Replaces product listing IDs for different language
	 * @param int $listing_id
	 * @return int
	 */
	function replace_product_listing_id( $listing_id ) {
		if ( !empty( $listing_id ) ) {
			if ( function_exists( 'pll_get_post' ) ) {
				$alternative_listing_id = pll_get_post( $listing_id );
			}
			if ( function_exists( 'icl_object_id' ) ) {
				$alternative_listing_id = icl_object_id( $listing_id, 'page', true );
			}
			if ( !empty( $alternative_listing_id ) ) {
				$listing_id = $alternative_listing_id;
			}
		}
		return $listing_id;
	}

	static function replace_page_id( $id, $lang = null ) {
		if ( !empty( $id ) ) {
			if ( function_exists( 'pll_get_post' ) ) {
				$alternative_id = pll_get_post( $id );
			}
			if ( function_exists( 'icl_object_id' ) ) {
				$post_type = get_post_type( $id );
				if ( !empty( $post_type ) ) {
					$alternative_id = icl_object_id( $id, $post_type, true, $lang );
				}
			}
			if ( !empty( $alternative_id ) ) {
				$id = $alternative_id;
			}
		}
		return $id;
	}

	function replace_product_listing_url( $url ) {
		$post_type = ic_get_post_type();
		if ( !empty( $post_type ) && ic_string_contains( $post_type, 'al_product' ) ) {
			$new_url = get_post_type_archive_link( $post_type );
		} else {
			$new_url = get_post_type_archive_link( 'al_product' );
		}
		if ( !empty( $new_url ) ) {
			$url = $new_url;
		}
		return $url;
	}

	function pll_post_type_link( $link ) {
		var_dump( $link );
		return $link;
	}

	/**
	 * Adds taxonomy translation support to polylang
	 *
	 * @param array $taxonomies
	 * @return array
	 */
	function catalog_taxonomies( $taxonomies ) {
		$taxonomies[] = 'al_product-cat';
		return $taxonomies;
	}

	/**
	 * Adds post type translation support to polylang
	 *
	 * @param array $post_types
	 * @return array
	 */
	function catalog_post_types( $post_types ) {
		$post_types[] = 'al_product';
		return $post_types;
	}

	function ajax_apply_lang( $query_vars = null ) {
		if ( !is_ic_ajax() ) {
			return;
		}
		if ( isset( $query_vars[ 'ic_lang' ] ) ) {
			$lang = $query_vars[ 'ic_lang' ];
		} else if ( isset( $_POST[ 'query_vars' ] ) && is_ic_ajax() ) {
			$ajax_query_vars = json_decode( stripslashes( $_POST[ 'query_vars' ] ), true );
			if ( !empty( $ajax_query_vars[ 'ic_lang' ] ) ) {
				$lang = $ajax_query_vars[ 'ic_lang' ];
			}
		}
		if ( !empty( $lang ) ) {
			$_POST[ 'lang' ]	 = $lang;
			$_REQUEST[ 'lang' ]	 = $lang;
			do_action( 'wpml_switch_language', $lang );
		}
	}

	function ajax_query_vars( $query_vars ) {
		$my_current_lang = apply_filters( 'wpml_current_language', NULL );
		if ( $my_current_lang ) {
			$query_vars[ 'ic_lang' ] = $my_current_lang;
		}
		return $query_vars;
	}

	function ic_multilingual_listing_slug( $slug, $post_type, $lang ) {
		if ( ic_string_contains( $post_type, 'al_product' ) && !empty( $lang ) ) {
			$listing_id	 = get_product_listing_id();
			$new_page_id = icl_object_id( $listing_id, 'page', true, $lang );
			$post		 = get_post( $new_page_id );
			$slug		 = $post->post_name;
		}
		return $slug;
	}

	/*
	  function overwrite_archive_query() {
	  $listing_id			 = get_product_listing_id();
	  $translated_listing	 = $this->replace_product_listing_id( $listing_id );
	  global $wp_query;
	  if ( is_page( $translated_listing ) ) {

	  global $wp_query;
	  $wp_query = new WP_QUERY( array( 'post_type' => 'al_product' ) );
	  var_dump( $wp_query );
	  }
	  }
	 *
	 */
}

$ic_catalog_multilingual = new ic_catalog_multilingual;
