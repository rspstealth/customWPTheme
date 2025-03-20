<?php get_header(); ?>

    <main id="primary" class="single-article-container">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header>
                    <h1 class="page-title entry-title"><?php the_title(); ?></h1>
                </header>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
                <div class="entry-meta">
                    <span class="post-date"><?php echo get_the_date('F j, Y'); ?></span>
                </div>
            </article>

        <?php endwhile; else : ?>
            <p>Sorry, no post was found!</p>
        <?php endif; ?>
    </main>

<?php get_footer(); ?>