<?php get_header(); ?>

<main id="primary" class="single-project-container">
    <?php
    while (have_posts()) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <footer class="entry-footer">
                <p><strong>Project Name:</strong> <?php echo get_post_meta(get_the_ID(), '_project_name', true); ?></p>
                <p><strong>Project
                        Description:</strong> <?php echo get_post_meta(get_the_ID(), '_project_description', true); ?>
                </p>
                <p><strong>Start
                        Date:</strong> <?php echo date('m-d-Y', strtotime(get_post_meta(get_the_ID(), '_project_start_date', true))); ?>
                </p>
                <p><strong>End
                        Date:</strong> <?php echo date('m-d-Y', strtotime(get_post_meta(get_the_ID(), '_project_end_date', true))); ?>
                </p>
                <p><strong>Project URL:</strong> <a
                            href="<?php echo esc_url(get_post_meta(get_the_ID(), '_project_url', true)); ?>"
                            target="_blank"><?php echo esc_url(get_post_meta(get_the_ID(), '_project_url', true)); ?></a>
                </p>
            </footer>
        </article>
    <?php endwhile; ?>

    <div class="project-navigation">
        <?php
        $prev_post = get_previous_post();
        $next_post = get_next_post();
        ?>

        <?php if ($prev_post) : ?>
            <a class="prev-project" href="<?php echo get_permalink($prev_post->ID); ?>">
                ← Previous: <?php echo get_the_title($prev_post->ID); ?>
            </a>
        <?php endif; ?>

        <?php if ($next_post) : ?>
            <a class="next-project" href="<?php echo get_permalink($next_post->ID); ?>">
                Next: <?php echo get_the_title($next_post->ID); ?> →
            </a>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
