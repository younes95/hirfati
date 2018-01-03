<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package azera-shop
 */

get_header();
azera_shop_wrapper_start( 'col-md-8', false );

echo '<main';
if ( have_posts() ) {
	echo ' itemscope itemtype="http://schema.org/Blog"';}
echo ' id="main" class="site-main" role="main">';

if ( have_posts() ) : ?>

	<?php ;/* Start the Loop */ ?>
	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<?php
		get_template_part( 'content' );
		?>

	<?php endwhile; ?>

	<?php the_posts_navigation(); ?>

<?php else : ?>

	<?php get_template_part( 'content', 'none' ); ?>

<?php endif; ?>

</main><!-- #main -->

<?php
azera_shop_wrapper_end( true );
get_footer(); ?>
