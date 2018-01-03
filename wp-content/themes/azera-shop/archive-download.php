<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package azera-shop
 */
get_header();

azera_shop_wrapper_start();

echo '<main';
if ( have_posts() ) {
	echo ' itemscope itemtype="http://schema.org/Blog"';
}
echo ' id="main" class="site-main" role="main">';

if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php ;/* Start the Loop */ ?>
			<?php
			while ( have_posts() ) :
				the_post();
?>
				<?php

					/**
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'content', 'archive-download' );
				?>

			<?php endwhile; ?>

			<?php echo apply_filters( 'azera_shop_post_navigation_filter', get_the_posts_navigation() ); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

	</main><!-- #main -->

<?php
azera_shop_wrapper_end();
get_footer(); ?>
