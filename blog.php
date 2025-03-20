<?php
/*
Template Name: Blog
Template Post Type: page
*/

get_header(); ?>

    <main id="primary" class="site-main">

    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Latest Blog Posts', 'custom-theme'); ?></h1>
    </header>


    <div class="content-wrapper">
        <div class="blog-posts">
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

            $args = array(
                'post_type' => 'post',
                'paged' => $paged
            );

            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large'); ?>
                            <?php endif; ?>
                        </a>

                        <header class="entry-header">
                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                        </header>

                        <div class="entry-meta">
                            <span class="post-date"><?php echo get_the_date('F j, Y'); ?></span>
                        </div>

                        <div class="entry-excerpt">
                            <?php the_excerpt(); ?>
                        </div>

                        <footer class="entry-footer">
                            <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                        </footer>
                    </article>
                <?php
                endwhile;
            else :
                echo '<p>No posts found.</p>';
            endif;
            ?>
        </div> <!-- .blog-posts -->
        <!-- Proper Pagination -->
        <div class="pagination">
            <?php
            echo paginate_links(array(
                'total' => $query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
                'prev_text' => __('« Previous'),
                'next_text' => __('Next »'),
            ));
            ?>
        </div>
        <?php
        wp_reset_postdata();
        ?>
    </div><!-- .content-wrapper -->
    </div><!-- #page -->
<?php
get_footer();
