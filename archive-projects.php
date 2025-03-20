<?php
get_header();
?>
<main id="primary" class="project-archive">
    <h1 class="page-title entry-title"><?php post_type_archive_title(); ?></h1>

    <?php if (have_posts()) : ?>
    <form method="GET" action="" class="project-filter-form">
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" placeholder="mm/dd/yy"
               value="<?php echo esc_attr($_GET['start_date'] ?? ''); ?>">

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" placeholder="mm/dd/yy"
               value="<?php echo esc_attr($_GET['end_date'] ?? ''); ?>">

        <input type="submit" value="Filter">
    </form>

    <div class="project-list">
        <?php
        $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
        $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';

        $args = array(
            'post_type' => 'projects',
            'meta_query' => array(
                'relation' => 'AND',
            ),
        );

        // Add Start Date filter if set
        if (!empty($start_date)) {
            $args['meta_query'][] = array(
                'key' => '_project_start_date',
                'value' => $start_date,
                'compare' => '>=',
                'type' => 'DATE',
            );
        }

        // Add End Date filter if set
        if (!empty($end_date)) {
            $args['meta_query'][] = array(
                'key' => '_project_end_date',
                'value' => $end_date,
                'compare' => '<=',
                'type' => 'DATE',
            );
        }

        $query = new WP_Query($args);
        while ($query->have_posts()) : $query->the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <p>Start
                    Date: <?php echo esc_html(date('m-d-Y', strtotime(get_post_meta(get_the_ID(), '_project_start_date', true)))); ?></p>
                <p>End
                    Date: <?php echo esc_html(date('m-d-Y', strtotime(get_post_meta(get_the_ID(), '_project_end_date', true)))); ?></p>
                <?php the_excerpt(); ?>
            </article>
        <?php endwhile;
        wp_reset_postdata();
        else :
            echo "<p>No projects found between chosen dates.</p>";
        endif;
        ?>
    </div>
</main>

<?php
get_footer();
?>
