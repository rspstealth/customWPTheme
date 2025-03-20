<?php
/*
Template Name: Home
Template Post Type: page
*/

get_header(); ?>

	<main id="primary" class="site-main">
        <section class="content-area content-full-width">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <article class="article-full">
                    <header>
                        <h1 class="page-title entry-title"><?php the_title(); ?></h1>
                    </header>
                    <?php the_content(); ?>
                </article>
            <?php endwhile; else : ?>
                <article>
                    <p>Sorry, no post was found!</p>
                </article>
            <?php endif; ?>
        </section>

	</main><!-- #main -->

<?php
get_footer();
