<?php
/**
 * Content for download single page.
 *
 * @package azera-shop
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'content-single-page' ); ?>>
	<header class="entry-header single-header">
		<?php the_title( '<h1 itemprop="headline" class="entry-title single-title">', '</h1>' ); ?>
		<?php echo apply_filters( 'azera_shop_header_underline', '<div class="colored-line-left"></div><div class="clearfix"></div>' ); ?>
	</header><!-- .entry-header -->

	<div itemprop="text" class="entry-content">

		<div class="edd-image-wrap">
			<?php
				// check if the post has a Post Thumbnail assigned to it.
			if ( has_post_thumbnail() ) {
				the_post_thumbnail();
			}
			?>
		</div>

		<?php the_content(); ?>
		<?php
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'azera-shop' ),
					'after'  => '</div>',
				)
			);
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php azera_shop_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
