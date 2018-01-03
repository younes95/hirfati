<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * The template to display product page
 *
 * Copy it to your theme implecode folder to edit the output: wp-content/themes/your-theme-folder-name/implecode/product-page.php
 *
 *
 * @version		1.1.2
 * @package		ecommerce-product-catalog/templates
 * @author 		Norbert Dreszer
 */
global $post;
$product		 = $post;
$product_id		 = $product->ID;
$this_product_id = ic_get_product_id();
if ( $this_product_id && $this_product_id !== $product_id ) {
	$product_id	 = $this_product_id;
	$product	 = get_post( $product_id );
	setup_postdata( $product );
}

$taxonomy		 = get_current_screen_tax();
$single_names	 = get_single_names();
$single_options	 = get_product_page_settings();
?>

<div id="product_details_container">
	<?php
	do_action( 'before_product_details', $product_id, $single_options );
	$details_class	 = product_gallery_enabled( $single_options[ 'enable_product_gallery' ], $single_options[ 'enable_product_gallery_only_when_exist' ], $product );
	?>
	<div id="product_details" class="product-details <?php echo $details_class; ?>">
		<?php
		do_action( 'product_details', $product, $single_names );
		?>
	</div>
	<?php
	do_action( 'product_details_container_end', $product, $single_names );
	?>
</div>
<?php
if ( current_user_can( 'edit_products' ) ) {
	do_action( 'ic_product_admin_actions', $product );
}
?>
<div class="after-product-details">
	<?php do_action( "after_product_details", $product, $single_names ); ?>
</div>
<?php do_action( "after_after_product_details", $product, $single_names ); ?>
<div class="after-product-description">
	<?php do_action( 'single_product_end', $product, $single_names, $taxonomy ); ?>
</div>

<?php
