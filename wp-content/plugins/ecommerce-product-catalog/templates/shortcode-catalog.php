<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements shortcode catalog functionality
 *
 * @version		1.1.3
 * @package		ecommerce-product-catalog/
 * @author 		Norbert Dreszer
 */
class ic_shortcode_catalog {

	private $multiple_settings, $status = 'catalog';

	function __construct() {
		add_shortcode( 'show_product_catalog', array( $this, 'catalog_shortcode' ) );

		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		if ( !is_ic_shortcode_integration() ) {
			return;
		}
		do_action( 'ic_shortcode_integration_init' );
		//add_action( 'ic_catalog_wp', array( $this, 'overwrite_query' ), 999 );
		add_action( 'ic_catalog_template_redirect', array( $this, 'overwrite_query' ), 999 );
		add_action( 'get_header', array( $this, 'catalog_query' ), 999 );

		add_action( 'wp_ajax_ic_assign_listing', array( $this, 'assign_listing' ) );

		add_filter( 'ic_catalog_body_class_start', array( $this, 'overwrite_query' ), 1 );
		add_filter( 'ic_catalog_single_body_class', array( $this, 'catalog_query' ), 99 );
		add_filter( 'ic_catalog_tax_body_class', array( $this, 'catalog_query' ), 98 );
//add_filter( 'ic_catalog_body_class', array( $this, 'catalog_query' ), 98 );

		add_filter( 'ic_catalog_pre_nav_menu', array( $this, 'remove_overwrite_filters' ), 99 );
		add_filter( 'ic_catalog_tax_nav_menu', array( $this, 'fake_tax_first_post' ), 99 );
		add_filter( 'ic_catalog_listing_nav_menu', array( $this, 'fake_listing_first_post' ), 99 );

		add_filter( 'post_class', array( $this, 'check_post_class' ), 99 );

		add_action( 'ic_catalog_wp_head_start', array( $this, 'catalog_query' ), -1 );
		add_action( 'ic_catalog_listing_wp_head', array( $this, 'overwrite_query' ), 999 );
		add_action( 'ic_catalog_search_wp_head', array( $this, 'overwrite_query' ), 999 );

		add_action( 'shortcode_catalog_init', array( $this, 'catalog_query' ) );
		add_action( 'shortcode_catalog_init', array( $this, 'remove_overwrite_filters' ) );
		add_action( 'product_listing_begin', array( $this, 'remove_overwrite_filters' ) );
	}

	function shortcode_init() {

		$this->multiple_settings = get_multiple_settings();
		remove_filter( 'the_content', array( $this, 'set_content' ), 1 );
		add_action( 'before_shortcode_catalog', array( $this, 'setup_postdata' ) );
		add_action( 'before_shortcode_catalog', array( $this, 'setup_loop' ) );
		add_action( 'after_shortcode_catalog', array( $this, 'end_query' ) );

		add_action( 'ic_shortcode_catalog_single_content', 'content_product_adder_single_content' );

		do_action( 'shortcode_catalog_init' );
		rewind_posts();
	}

	function catalog_shortcode() {
		$this->shortcode_init();
		ob_start();
		do_action( 'before_shortcode_catalog' );
		$this->shortcode_product_adder();
		do_action( 'after_shortcode_catalog' );
		$content = ob_get_clean();
		return $content;
	}

	function default_message() {
		if ( !current_user_can( 'manage_product_settings' ) ) {
			return;
		}
		$page_url	 = product_listing_url();
		$listing_id	 = get_product_listing_id();
		$page_id	 = get_the_ID();
		if ( $listing_id == $page_id || get_post_type() !== 'page' ) {
			return;
		}
		echo "<style>.ic_spinner{background: url(" . admin_url() . "/images/spinner.gif) no-repeat;display:none;width:20px;height:20px;margin-left:2px;vertical-align:middle;</style>";
		if ( !empty( $page_url ) ) {
			implecode_info( '<p>' . sprintf( __( 'Currently %sanother page%s is set to show main product listing. Would you like to show product listing here instead?', 'ecommerce-product-catalog' ), '<a href="' . $page_url . '">', '</a>' ) . '</p><button  type="button" class="button assign-listing-button">' . __( 'Yes', 'ecommerce-product-catalog' ) . '</button><div class="ic_spinner"></div><p style="font-size: 0.8em">* ' . sprintf( __( 'Please remove the %s shortcode to disable this info.', 'ecommerce-product-catalog' ), '[show_product_catalog]' ) . '</p>' );
			echo "<script>jQuery('.assign-listing-button').click(function() {" . $this->assing_listing_script( $page_id ) . "});</script>";
		} else {
			if ( !empty( $page_id ) ) {
				echo __( 'Please wait... we are preparing your product listing page.', 'ecommerce-product-catalog' ) . '<div class="ic_spinner"></div>';
				echo "<script>" . $this->assing_listing_script( $page_id ) . "</script>";
			}
		}
	}

	function assing_listing_script( $page_id ) {
		return "var data = {
        'action': 'ic_assign_listing',
		'page_id': '" . $page_id . "',
    };
	jQuery('.ic_spinner').css('display', 'inline-block');
	jQuery('.assign-listing-button').prop('disabled', true);
	jQuery.post( product_object.ajaxurl, data, function() {
		window.location.reload(false);
});";
	}

	function assign_listing() {
		if ( !empty( $_POST[ 'page_id' ] ) ) {
			$page_id = intval( $_POST[ 'page_id' ] );
			if ( !empty( $page_id ) && is_ic_shortcode_integration( $page_id ) ) {
				update_option( 'product_archive_page_id', $page_id );
				update_option( 'product_archive', $page_id );
				permalink_options_update();
			}
		}
		wp_die();
	}

	function shortcode_product_adder() {
		$query = $this->get_pre_shortcode_query();
		if ( empty( $query ) ) {
			return $this->default_message();
		}
		$class_exists = ic_get_global( 'ic_post_class_exists' );
		if ( !$class_exists ) {
			?>
			<div <?php post_class() ?>>
				<?php
			}
			$listing_status = ic_get_product_listing_status();

			if ( is_archive() || is_search() || is_home_archive() || is_ic_product_listing( $query ) || is_ic_taxonomy_page( $query ) || is_ic_product_search( $query ) ) {
				if ( !is_ic_product_listing( $query ) || (is_ic_product_listing( $query ) && ($listing_status === 'publish' || current_user_can( 'edit_private_products' ))) ) {
					$this->product_listing();
				}
			} else {
				$product_id			 = ic_get_product_id();
				$current_post_type	 = get_post_type( $product_id );
				$taxonomy			 = get_current_screen_tax();
				do_action( 'single_product_begin', $product_id, $current_post_type, $taxonomy );
				do_action( 'before_product_page' );
				echo '<div class="product-entry">';
				do_action( 'ic_shortcode_catalog_single_content' );
				echo '</div>';
				do_action( 'after_product_page' );
			}
			if ( !$class_exists ) {
				?>
			</div>
			<?php
		}
	}

	function product_listing() {
		$archive_template = get_product_listing_template();
		do_action( 'product_listing_begin', $this->multiple_settings );
		do_action( 'before_product_archive' );
		do_action( 'product_listing_entry_inside', $archive_template, $this->multiple_settings );
		do_action( 'product_listing_end', $archive_template, $this->multiple_settings );
		do_action( 'after_product_archive' );
	}

	function catalog_query( $return = null ) {
		if ( is_ic_shortcode_integration() && $this->status == 'page' ) {
			$this->status	 = 'catalog';
			global $wp_query, $post;
			$pre_query		 = $this->get_pre_shortcode_query();
			$pre_post		 = $this->get_pre_shortcode_post();
			if ( empty( $pre_query ) || empty( $pre_post ) ) {
				return;
			}
			$wp_query = $pre_query;
			if ( is_ic_product_page() ) {
				$post = $pre_post;
			}
			if ( empty( $wp_query->posts ) && is_ic_only_main_cats() ) {
				$listing_id				 = get_product_listing_id();
				$post					 = get_post( $listing_id );
				$wp_query->posts[ 0 ]	 = $post;
				$wp_query->post_count	 = 1;
				add_action( 'shortcode_catalog_init', array( $this, 'clear_posts' ) );
			}
		}
		return $return;
	}

	function fake_tax_first_post( $return = null ) {
		if ( !is_ic_shortcode_integration() ) {
			return $return;
		}
		global $wp_query;
		if ( !empty( $wp_query->queried_object->name ) || is_ic_taxonomy_page( $wp_query ) ) {
			add_filter( 'the_title', array( $this, 'fake_post_title' ), 10, 2 );
		}

		return $return;
	}

	function fake_listing_first_post( $return = null ) {
		if ( !is_ic_shortcode_integration() ) {
			return $return;
		}
		global $wp_query;
		$listing_id = get_product_listing_id();
		if ( (!empty( $wp_query->queried_object->ID ) && $wp_query->queried_object->ID == $listing_id) || is_ic_product_listing( $wp_query ) ) {
			add_filter( 'the_title', array( $this, 'fake_post_title' ), 10, 2 );
		}

		return $return;
	}

	function fake_post_title( $title, $id = null ) {
		if ( !empty( $id ) ) {
			global $wp_query;
			$post		 = get_post();
			$listing_id	 = get_product_listing_id();
			if ( is_ic_product_listing( $wp_query ) ) {
				remove_filter( 'the_title', array( $this, 'fake_post_title' ), 10, 2 );
				$title = get_product_listing_title();
			} else if ( ($post->ID == $id && $listing_id != $id) || is_ic_taxonomy_page( $wp_query ) ) {
//remove_filter( 'the_title', array( $this, 'fake_post_title' ), 10, 2 );
				if ( !empty( $wp_query->queried_object->name ) ) {
					$title = get_product_tax_title( $wp_query->queried_object->name );
				}
			}
		}
		return $title;
	}

	function remove_overwrite_filters() {
		remove_filter( 'the_title', array( $this, 'fake_post_title' ), 10, 2 );
	}

	function clear_posts() {
		if ( is_ic_only_main_cats() ) {
			global $wp_query;
			if ( $wp_query->post_count == 1 ) {
				$wp_query->post_count	 = 0;
				$wp_query->posts		 = '';
			}
		}
	}

	function end_query() {
		//add_filter( 'the_content', array( $this, 'clear' ) );
		$args = array( 'pagename' => $this->get_listing_slug() );
		query_posts( $args );
		if ( have_posts() ) {
			the_post();
		}
		global $wp_query;
		if ( !empty( $wp_query->post_count ) ) {
			$wp_query->post_count = 0;
		}
		if ( !empty( $wp_query->found_posts ) ) {
			$wp_query->found_posts = 0;
		}
		$wp_query->posts = '';

		ic_save_global( 'in_the_loop', 0 );
	}

	function overwrite_query() {
		if ( is_ic_shortcode_integration() && $this->status == 'catalog' ) {
			$this->status = 'page';
			$this->get_pre_shortcode_query();
			$this->get_pre_shortcode_post();
			add_filter( 'the_content', array( $this, 'set_content' ), 1 );
			$this->page_query();
		}
	}

	function get_pre_shortcode_query() {
		if ( is_ic_catalog_page() && is_ic_shortcode_integration() ) {
			$pre_post = ic_get_global( 'pre_shortcode_query' );
			if ( !$pre_post ) {
				//var_dump( $GLOBALS[ 'wp_query' ] );

				do_action( 'shortcode_catalog_query_first_save', $GLOBALS[ 'wp_query' ] );
				ic_save_global( 'pre_shortcode_query', $GLOBALS[ 'wp_query' ] );
				return $GLOBALS[ 'wp_query' ];
			}
			return $pre_post;
		}
		return false;
	}

	function get_pre_shortcode_post() {
		if ( is_ic_catalog_page() && is_ic_shortcode_integration() ) {
			$pre_post = ic_get_global( 'pre_shortcode_post' );
			if ( !$pre_post ) {
				//var_dump( $GLOBALS[ 'post' ] );
				ic_save_global( 'pre_shortcode_post', $GLOBALS[ 'post' ] );
				return $GLOBALS[ 'post' ];
			}
			return $pre_post;
		}
		return false;
	}

	function page_query( $return = null ) {
		if ( !is_admin() && is_ic_catalog_page() && is_ic_shortcode_integration() ) {
			global $wp_query, $post;
			$listing_slug = $this->get_listing_slug();
			if ( !empty( $wp_query->query[ 'pagename' ] ) && $wp_query->query[ 'pagename' ] === $listing_slug ) {
				return $return;
			}

			$new_query	 = ic_get_global( 'ic_shortcode_new_query' );
			$new_post	 = ic_get_global( 'ic_shortcode_new_post' );
			if ( $new_query && $new_post ) {
				$wp_query	 = $new_query;
				$post		 = $new_post;
				return $return;
			}
			$args = array( 'pagename' => $this->get_listing_slug() );
			query_posts( $args );

			$listing_id		 = get_product_listing_id();
			$listing_post	 = get_post( $listing_id );
			$post			 = $listing_post;
			$wp_query->post	 = $listing_post;
			$wp_query->posts = array( 0 => $listing_post );
			if ( empty( $wp_query->post_count ) ) {
				$wp_query->post_count = 1;
			}
			if ( empty( $wp_query->found_posts ) ) {
				$wp_query->found_posts = 1;
			}
			$pre_post = $this->get_pre_shortcode_query();
			if ( is_ic_product_search( $pre_post ) ) {
				$search_title						 = ic_get_search_page_title();
				$post->post_title					 = $search_title;
				$wp_query->post->post_title			 = $search_title;
				$wp_query->posts[ 0 ]->post_title	 = $search_title;
			} else if ( !empty( $pre_post->queried_object->labels->name ) ) {
				$post->post_title					 = $pre_post->queried_object->labels->name;
				$wp_query->post->post_title			 = $pre_post->queried_object->labels->name;
				$wp_query->posts[ 0 ]->post_title	 = $pre_post->queried_object->labels->name;
			} else if ( is_ic_product_listing( $pre_post ) ) {
				$wp_query->post->post_title			 = $listing_post->post_title;
				$wp_query->posts[ 0 ]->post_title	 = $listing_post->post_title;
			} else if ( !empty( $pre_post->queried_object->name ) ) {
				$tax_title							 = get_product_tax_title( $pre_post->queried_object->name );
				$post->post_title					 = $tax_title;
				$post->post_status					 = 'publish';
				$wp_query->post->post_title			 = $tax_title;
				$wp_query->posts[ 0 ]->post_title	 = $tax_title;
			} else if ( !empty( $pre_post->post->post_title ) ) {
				$post->post_title					 = $pre_post->post->post_title;
				$post->post_status					 = $pre_post->post->post_status;
				$wp_query->post->post_title			 = $pre_post->post->post_title;
				$wp_query->posts[ 0 ]->post_title	 = $pre_post->post->post_title;
			}
			ic_save_global( 'ic_shortcode_new_query', $wp_query );
			ic_save_global( 'ic_shortcode_new_post', $post );
		}
		return $return;
	}

	function the_post() {
		$this->page_query();
	}

	function set_content( $content ) {
		$listing_id	 = get_product_listing_id();
		$page		 = get_post( $listing_id );
		$readd		 = false;
		if ( has_filter( 'the_content', array( $this, 'set_content' ) ) ) {
			remove_filter( 'the_content', array( $this, 'set_content' ), 1 );
			$readd = true;
		}
		if ( !has_shortcode( $page->post_content, 'show_product_catalog' ) ) {
			//$page->post_content .= '[show_product_catalog]';
		}
		//$content = apply_filters( 'the_content', $page->post_content );
		$content = $page->post_content;
		//$content = do_shortcode( '[show_product_catalog]' );
		if ( $readd ) {
			add_filter( 'the_content', array( $this, 'set_content' ), 1 );
		}
		return $content;
	}

	function clear() {
		return '';
	}

	function get_listing_slug() {
		$listing_id	 = get_product_listing_id();
		$post		 = get_post( $listing_id );
		return $post->post_name;
	}

	function setup_postdata() {
		if ( is_ic_catalog_page() ) {
			global $post, $wp_query;
			if ( isset( $wp_query->queried_object->ID ) ) {
				$product_id	 = $wp_query->queried_object->ID;
				ic_save_global( 'product_id', $product_id );
				$post		 = get_post( $product_id );

				if ( empty( $post->post_content ) ) {
					$post->post_content = ' ';
				}
				setup_postdata( $post );
			}
		}
	}

	function setup_loop() {
		ic_save_global( 'in_the_loop', 1 );
	}

	function check_post_class( $class ) {
		ic_save_global( 'ic_post_class_exists', 1 );
		$class[] = 'page';
		$class[] = 'type-page';
		return $class;
	}

}

global $ic_shortcode_catalog;
$ic_shortcode_catalog = new ic_shortcode_catalog;
