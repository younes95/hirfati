<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package azera-shop
 */

get_header();
azera_shop_wrapper_start(); ?>

	<main id="main" class="site-main" role="main">

	<?php
	while ( have_posts() ) :
		the_post();
?>

		<?php get_template_part( 'content', 'page' ); ?>

		<?php
			// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() || get_comments_number() ) :
			comments_template();
			endif;
		?>

	<?php endwhile; ?>

	</main><!-- #main -->
<?php
azera_shop_wrapper_end();
get_footer(); ?>
