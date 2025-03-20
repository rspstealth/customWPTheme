<?php
get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

			endwhile;

			the_posts_navigation();

		else :

			echo 'No Posts Found.';

		endif;
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
