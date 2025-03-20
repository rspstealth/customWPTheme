<?php
// This function enqueues the Normalize.css for use. The first parameter is a name for the stylesheet, the second is the URL. Here we
// use an online version of the css file.
function add_normalize_CSS()
{
    wp_enqueue_style('normalize-styles', "https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css");
}

add_action('wp_enqueue_scripts', 'add_normalize_CSS');

// Register a new sidebar 'sidebar'
function add_widget_support()
{
    register_sidebar(array(
        'name' => 'Sidebar',
        'id' => 'sidebar',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
}

add_action('widgets_init', 'add_widget_support');

// Create Custom Post Type 'Projects'
function custom_post_type_projects()
{
    $labels = array(
        'name' => 'Projects',
        'singular_name' => 'Project',
        'menu_name' => 'Projects',
        'name_admin_bar' => 'Project',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Project',
        'new_item' => 'New Project',
        'edit_item' => 'Edit Project',
        'view_item' => 'View Project',
        'all_items' => 'All Projects',
        'search_items' => 'Search Projects',
        'not_found' => 'No projects found',
        'not_found_in_trash' => 'No projects found in trash'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-portfolio',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite' => array('slug' => 'projects'),
        'capability_type' => 'post',
        'show_in_rest' => true // Enables Gutenberg editor
    );

    register_post_type('projects', $args);
}

add_action('init', 'custom_post_type_projects');

function add_project_meta_boxes()
{
    add_meta_box(
        'project_details',
        'Project Details',
        'render_project_meta_box',
        'projects',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'add_project_meta_boxes');

//to display project meta box on backend
function render_project_meta_box($post)
{
    wp_nonce_field(basename(__FILE__), 'project_nonce');

    $project_name = get_post_meta($post->ID, '_project_name', true);
    $project_description = get_post_meta($post->ID, '_project_description', true);
    $project_start_date = get_post_meta($post->ID, '_project_start_date', true);
    $project_end_date = get_post_meta($post->ID, '_project_end_date', true);
    $project_url = get_post_meta($post->ID, '_project_url', true);
    ?>

    <p><label>Project Name:</label><br>
        <input type="text" name="project_name" value="<?php echo esc_attr($project_name); ?>" style="width:100%;">
    </p>

    <p><label>Project Description:</label><br>
        <textarea name="project_description"
                  style="width:100%;"><?php echo esc_textarea($project_description); ?></textarea>
    </p>

    <p><label>Project Start Date:</label><br>
        <input type="date" name="project_start_date"
               value="<?php echo esc_attr(date('Y-m-d', strtotime($project_start_date))); ?>">
    </p>

    <p><label>Project End Date:</label><br>
        <input type="date" name="project_end_date"
               value="<?php echo esc_attr(date('Y-m-d', strtotime($project_end_date))); ?>">
    </p>

    <p><label>Project URL:</label><br>
        <input type="url" name="project_url" value="<?php echo esc_url($project_url); ?>" style="width:100%;">
    </p>

    <?php
}

// to save project meta data
function save_project_meta_data($post_id)
{
    if (!isset($_POST['project_nonce']) || !wp_verify_nonce($_POST['project_nonce'], basename(__FILE__))) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['project_name', 'project_description', 'project_start_date', 'project_end_date', 'project_url'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, "_$field", sanitize_text_field($_POST[$field]));
        }
    }
}

add_action('save_post', 'save_project_meta_data');

//register custom wp nav menu
function custom_theme_menus()
{
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'custom_theme'),
    ));
}

add_action('after_setup_theme', 'custom_theme_menus');

//API end point to get JSON formatted list of projects
//end point to use: /wp-json/custom/v1/projects/
function custom_projects_api_endpoint()
{
    register_rest_route('custom/v1', '/projects/', [
        'methods' => 'GET',
        'callback' => 'get_projects_data',
        'permission_callback' => '__return_true'
    ]);
}

add_action('rest_api_init', 'custom_projects_api_endpoint');

//api callback function to collect projects data
function get_projects_data()
{
    $args = [
        'post_type' => 'projects',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ];

    $projects = get_posts($args);
    $data = [];

    if (!empty($projects)) {
        foreach ($projects as $project) {
            $data[] = [
                'title' => get_the_title($project->ID),
                'url' => get_permalink($project->ID),
                'start_date' => get_post_meta($project->ID, '_project_start_date', true),
                'end_date' => get_post_meta($project->ID, '_project_end_date', true)
            ];
        }
    }

    return rest_ensure_response($data);
}

/* Unused Media */
if (!defined('ABSPATH')) exit;

//ADD "Unused Media" PAGE TO ADMIN MENU
function unused_media_admin_menu()
{
    add_media_page(
        'Unused Media',
        'Unused Media',
        'manage_options',
        'unused-media',
        'unused_media_page'
    );
}

add_action('admin_menu', 'unused_media_admin_menu');

//get unused media (if any)
function get_unused_media()
{
    global $wpdb;

    // Get all images attachments
    $all_media = $wpdb->get_results("
        SELECT ID, guid 
        FROM {$wpdb->posts} 
        WHERE post_type = 'attachment' 
        AND post_mime_type LIKE 'image%'
    ");

    $unused_media = [];

    foreach ($all_media as $media) {
        $media_id = $media->ID;
        $media_url = $media->guid;

        //Not attached to any post/page
        $post_parent = $wpdb->get_var($wpdb->prepare("
            SELECT post_parent FROM {$wpdb->posts} WHERE ID = %d
        ", $media_id));

        // Not used in any published posts content
        $used_in_content = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) 
            FROM {$wpdb->posts} 
            WHERE post_status = 'publish' 
            AND post_content LIKE %s
        ", '%' . $media_url . '%'));

        // Not used in any published custom fields => ACF OR postmeta
        $used_in_meta = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) 
            FROM {$wpdb->postmeta} 
            WHERE meta_value LIKE %s
        ", '%' . $media_url . '%'));

        // Not used as a featured image (_thumbnail_id)
        $used_as_thumbnail = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) 
            FROM {$wpdb->postmeta} pm
            JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE p.post_status = 'publish'
            AND pm.meta_key = '_thumbnail_id' 
            AND pm.meta_value = %d
        ", $media_id));

        // If NOT used anywhere in published posts/pages, add them to unused media array
        if ($post_parent == 0 && $used_in_content == 0 && $used_in_meta == 0 && $used_as_thumbnail == 0) {
            $unused_media[] = [
                'id' => $media_id,
                'title' => get_the_title($media_id),
                'url' => $media_url,
            ];
        }
    }

    return $unused_media;
}


// Unused media page function, to display unused media and delete process
function unused_media_page()
{
    //if not ADMIN / manage page
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    //get unused media list array
    $unused_media = get_unused_media();
    ?>
    <div class="wrap">
        <h1>Unused Media Files</h1>
        <p>These media files are not used in any post or custom field.</p>
        <table class="widefat fixed">
            <thead>
            <tr>
                <th>Preview</th>
                <th>Title</th>
                <th>URL</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($unused_media)) : ?>
                <?php foreach ($unused_media as $media) : ?>
                    <tr id="media-<?php echo esc_attr($media['id']); ?>">
                        <td><img src="<?php echo esc_url($media['url']); ?>" width="50" height="50"></td>
                        <td><?php echo esc_html($media['title']); ?></td>
                        <td><?php echo esc_url($media['url']); ?></td>
                        <td>
                            <button class="delete-media-btn" data-id="<?php echo esc_attr($media['id']); ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4">No unused media found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-media-btn').forEach(
                button => {
                    button.addEventListener('click', function () {
                        let mediaId = this.getAttribute('data-id');
                        if (confirm('Confirm media deletion?')) {
                            fetch(ajaxurl, {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: `action=delete_unused_media&media_id=${mediaId}&_wpnonce=<?php echo wp_create_nonce('delete_media_nonce'); ?>`
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        document.getElementById('media-' + mediaId).remove();
                                        alert('Media deleted successfully.');
                                    } else {
                                        alert('Failed to delete media.');
                                    }
                                });
                        }
                    });
                });
        });
    </script>
    <?php
}

//delete unused media ajax function
function delete_unused_media()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permission denied']);
    }

    check_ajax_referer('delete_media_nonce');

    $media_id = isset($_POST['media_id']) ? intval($_POST['media_id']) : 0;
    if (!$media_id) {
        wp_send_json_error(['message' => 'Invalid media ID']);
    }

    // Delete the media file
    $deleted = wp_delete_attachment($media_id, true);
    if ($deleted) {
        wp_send_json_success(['message' => 'Media deleted successfully']);
    } else {
        wp_send_json_error(['message' => 'Failed to delete media']);
    }
}

add_action('wp_ajax_delete_unused_media', 'delete_unused_media');

//logo support
function custom_theme_setup()
{
    add_theme_support('custom-logo', array(
        'height' => 100,
        'width' => 120,
        'flex-height' => true,
        'flex-width' => true,
    ));
}

add_action('after_setup_theme', 'custom_theme_setup');

//custom javascript action
function custom_theme_scripts()
{
    wp_enqueue_script('menu-script', get_template_directory_uri() . '/js/script.js', array(), false, true);
}

add_action('wp_enqueue_scripts', 'custom_theme_scripts');