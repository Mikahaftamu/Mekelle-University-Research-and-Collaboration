<?php
/**
 * Enhanced Custom Login System for BuddyX Theme
 * 1. Creates beautiful custom login page
 * 2. Allows guest browsing (optional login)
 * 3. Keeps wp-admin login as default
 * 4. Handles logout redirection
 * 5. Hides login page from menus
 */

// ============================
add_action('wp_enqueue_scripts', 'buddyx_child_enqueue_styles');
function buddyx_child_enqueue_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style('buddyx-parent-style', get_template_directory_uri() . '/style.css');
    
    // Enqueue child theme stylesheet
    wp_enqueue_style('buddyx-child-style', 
        get_stylesheet_directory_uri() . '/style.css',
        array('buddyx-parent-style'), // Make sure child styles load after parent
        wp_get_theme()->get('Version') // Use theme version for cache busting
    );
    
    // Enqueue custom login styles (only on login page)
    if (is_page('login')) {
        wp_enqueue_style('buddyx-login-style', 
            get_stylesheet_directory_uri() . '/login-styles.css', // Create this file
            array('buddyx-child-style'),
            filemtime(get_stylesheet_directory() . '/login-styles.css')
        );
    }
}
// ============================

function custom_login_form_shortcode() {
    if (is_user_logged_in()) {
        wp_redirect(home_url());
        exit;
    }

    ob_start(); ?>
    
    <div class="buddyx-login-page-wrapper">
        <div class="buddyx-login-left-section">
            <div class="buddyx-login-left-content">
                <h1>Welcome Back!</h1>
                <p>Join our community of like-minded individuals and take your experience to the next level.</p>
                <div class="buddyx-brand-logo">
                    <!-- You can add your logo here if needed -->
                </div>
            </div>
        </div>
        
        <div class="buddyx-login-right-section">
            <div class="buddyx-login-container">
                <?php if (isset($_GET['login']) && $_GET['login'] === 'failed') : ?>
                    <div class="buddyx-alert buddyx-alert-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        <span>Invalid credentials. Please try again.</span>
                    </div>
                <?php endif; ?>

                <form method="post" class="buddyx-login-form">
                    <div class="buddyx-login-header">
                        <h2>Sign In</h2>
                        <p>Access your account to continue</p>
                    </div>

                    <div class="buddyx-form-group">
                        <label for="username">Username or Email</label>
                        <div class="buddyx-input-group">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                            </svg>
                            <input type="text" name="custom_username" placeholder="Enter your username or email" required>
                        </div>
                    </div>

                    <div class="buddyx-form-group">
                        <label for="password">Password</label>
                        <div class="buddyx-input-group">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                            </svg>
                            <input type="password" name="custom_password" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <div class="buddyx-form-options">
                        <label class="buddyx-remember-me">
                            <input type="checkbox" name="rememberme"> Remember me
                        </label>
                        <a href="<?php echo wp_lostpassword_url(); ?>" class="buddyx-forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" name="custom_login_submit" class="buddyx-login-button">
                        Sign In
                    </button>

                    <div class="buddyx-login-footer">
                        <p>Don't have an account? <a href="<?php echo wp_registration_url(); ?>">Sign up</a></p>
                        <p>Or <a href="<?php echo home_url(); ?>" class="buddyx-continue-guest">continue as guest</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php return ob_get_clean();
}
add_shortcode('custom_login_form', 'custom_login_form_shortcode');

// ============================
// Handle Login Submission
// ============================
function handle_custom_login_form() {
    if (isset($_POST['custom_login_submit'])) {
        $creds = array(
            'user_login'    => sanitize_user($_POST['custom_username']),
            'user_password' => $_POST['custom_password'],
            'remember'      => isset($_POST['rememberme'])
        );

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            wp_redirect(home_url('/login?login=failed'));
            exit;
        } else {
            wp_redirect(home_url());
            exit;
        }
    }
}
add_action('init', 'handle_custom_login_form');

// ============================
// Modify Login URLs
// ============================
function custom_login_url($login_url, $redirect) {
    if (!is_admin() && !strpos($login_url, 'wp-admin')) {
        $login_url = home_url('/login');
        if (!empty($redirect)) {
            $login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
        }
    }
    return $login_url;
}
add_filter('login_url', 'custom_login_url', 10, 2);

// ============================
// Handle Logout
// ============================
function custom_logout_redirect() {
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        check_admin_referer('log-out');
        wp_logout();
        wp_redirect(home_url('/login?logged_out=true'));
        exit;
    }
}
add_action('init', 'custom_logout_redirect');

// ============================
// Modify Logout URL
// ============================
function custom_logout_url($logout_url, $redirect) {
    $redirect = $redirect ?: home_url('/login');
    return wp_nonce_url(
        add_query_arg('action', 'logout', home_url('/')),
        'log-out',
        '_wpnonce'
    ) . '&redirect_to=' . urlencode($redirect);
}
add_filter('logout_url', 'custom_logout_url', 10, 2);

// ============================
// Hide Login Page from Menus
// ============================
function hide_login_page_from_menus($items, $args) {
    if (is_admin()) {
        return $items;
    }
    
    foreach ($items as $key => $item) {
        if ($item->object == 'page' && strtolower($item->title) == 'login') {
            unset($items[$key]);
        }
    }
    return $items;
}
add_filter('wp_get_nav_menu_items', 'hide_login_page_from_menus', 10, 2);




function enable_custom_avatar_cover_uploads() {
    add_filter('bp_core_enable_avatar_uploads', '__return_true');
    add_filter('bp_core_enable_cover_image_uploads', '__return_true');
}
add_action('bp_init', 'enable_custom_avatar_cover_uploads');






add_filter('bp_core_fetch_avatar_no_grav', '__return_true');




function redirect_to_avatar_upload_after_login($redirect_to, $request, $user) {
    if (isset($user->ID)) {
        return bp_core_get_user_domain($user->ID) . 'profile/change-avatar/';
    }
    return $redirect_to;
}
add_filter('login_redirect', 'redirect_to_avatar_upload_after_login', 10, 3);




// hiding the search and messege icons 
function remove_buddyx_header_icons() {
    remove_action( 'buddyx_header_icons', 'buddyx_header_search', 10 );
    remove_action( 'buddyx_header_icons', 'buddyx_header_messages', 20 );
}
add_action( 'after_setup_theme', 'remove_buddyx_header_icons' );
//*end of the hiding off search and message icon




//here for the activity functionality




// Remove original BP Attachments function and replace with fixed version
function buddyx_child_fix_bp_attachments() {
    // Remove original filters
    remove_filter('bp_before_activity_post_update_parse_args', 'bp_attachments_activity_attach_media');
    remove_filter('bp_before_groups_post_update_parse_args', 'bp_attachments_activity_attach_media');
    
    // Add our fixed version
    add_filter('bp_before_activity_post_update_parse_args', 'buddyx_fixed_bp_attachments_activity_attach_media', 10, 1);
    add_filter('bp_before_groups_post_update_parse_args', 'buddyx_fixed_bp_attachments_activity_attach_media', 10, 1);
}
add_action('bp_init', 'buddyx_child_fix_bp_attachments', 20);

/**
 * Fixed version of bp_attachments_activity_attach_media
 */
function buddyx_fixed_bp_attachments_activity_attach_media($args = array()) {
    // Debug logging - shows what we're receiving
    error_log('BP Attachments Args Received: ' . print_r($args, true));
    
    // Ensure $args is always an array
    if (!is_array($args)) {
        if (is_string($args)) {
            $args = array('content' => $args);
        } else {
            $args = array();
        }
    }

    // Only proceed if in Nouveau and we have a media URL
    if ('nouveau' === bp_get_theme_compat_id() && isset($_POST['_bp_attachments_medium_url'])) {
        $medium_url = esc_url_raw(wp_unslash($_POST['_bp_attachments_medium_url']));

        if (!$medium_url) {
            return $args;
        }

        $medium_pathinfo = bp_attachments_get_medium_path($medium_url, true);

        // Validate the medium exists
        if (!isset($medium_pathinfo['id']) || !isset($medium_pathinfo['path'])) {
            return $args;
        }

        $medium = bp_attachments_get_medium($medium_pathinfo['id'], $medium_pathinfo['path']);
        
        if (!is_object($medium) || !isset($medium->media_type)) {
            return $args;
        }

        // Create appropriate block based on media type
        $medium_block = '';
        switch ($medium->media_type) {
            case 'image':
            case 'audio':
            case 'video':
                $medium_block = bp_attachments_get_serialized_block(array(
                    'blockName' => sprintf('bp/%s-attachment', $medium->media_type),
                    'attrs' => array(
                        'align' => 'center',
                        'url' => $medium_url,
                        'src' => $medium->links['src'] ?? '',
                    ),
                ));
                break;
            default:
                $medium_block = bp_attachments_get_serialized_block(array(
                    'blockName' => 'bp/file-attachment',
                    'attrs' => array(
                        'url' => $medium_url,
                        'name' => $medium->name ?? '',
                        'mediaType' => $medium->media_type,
                    ),
                ));
                break;
        }

        // Handle existing content
        $content = '';
        if (!empty($args['content'])) {
            $content = bp_attachments_get_serialized_block(array(
                'innerContent' => array('<p>' . $args['content'] . '</p>'),
            ));
        }

        // Combine content with media block if we have one
        if ($medium_block) {
            $text_block = $content;
            $content .= "\n" . $medium_block;
            
            $args['content'] = apply_filters('bp_attachments_activity_attach_media', $content, $text_block, $medium_block, $args);
        }
    }

    return $args;
}

// Fix translation loading timing for all plugins
function buddyx_fix_translation_timing() {
    // Load translations properly at init
    load_plugin_textdomain('bp-attachments', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    load_plugin_textdomain('bbpress', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    load_plugin_textdomain('fakerpress', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('init', 'buddyx_fix_translation_timing', 1);

// Fix bbPress dynamic property deprecation
function buddyx_fix_bbpress_dynamic_properties() {
    if (class_exists('BBP_Forums_Component') && !property_exists('BBP_Forums_Component', 'members')) {
        class BuddyX_BBP_Forums_Component extends BBP_Forums_Component {
            public $members;
            public $activity;
        }
        
        // Replace the original component
        remove_action('bp_init', 'bbp_setup_forums_component', 6);
        add_action('bp_init', function() {
            buddypress()->forums = new BuddyX_BBP_Forums_Component();
        }, 6);
    }
}
add_action('plugins_loaded', 'buddyx_fix_bbpress_dynamic_properties');







/**
 * Render media blocks in activity content
 */
function buddyx_render_activity_media_blocks($content) {
    if (has_blocks($content)) {
        // Parse and render blocks
        $blocks = parse_blocks($content);
        $rendered_content = '';
        
        foreach ($blocks as $block) {
            $rendered_content .= render_block($block);
        }
        
        return $rendered_content;
    }
    return $content;
}
add_filter('bp_get_activity_content_body', 'buddyx_render_activity_media_blocks', 9);

//for handling delete projectsfrom the front end
function handle_delete_project() {
    try {
        // Verify nonce
        if (!check_ajax_referer('add_project_nonce', 'nonce', false)) {
            wp_send_json_error('Security check failed.');
            return;
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error('You must be logged in to delete a project.');
            return;
        }

        // Get and validate project ID
        if (!isset($_POST['project_id']) || !is_numeric($_POST['project_id'])) {
            wp_send_json_error('Invalid project ID.');
            return;
        }

        $project_id = intval($_POST['project_id']);
        
        // Get project
        $project = get_post($project_id);
        if (!$project) {
            wp_send_json_error('Project not found.');
            return;
        }

        // Verify project type
        if ($project->post_type !== 'project') {
            wp_send_json_error('Invalid project type.');
            return;
        }

        // Check permissions
        if ($project->post_author != get_current_user_id() && !current_user_can('delete_others_posts')) {
            wp_send_json_error('You do not have permission to delete this project.');
            return;
        }

        // Delete associated document if exists
        $doc_id = get_post_meta($project_id, 'project_document', true);
        if ($doc_id) {
            wp_delete_attachment($doc_id, true);
        }

        // Delete the project
        $deleted = wp_delete_post($project_id, true);
        
        if ($deleted) {
            wp_send_json_success('Project deleted successfully.');
        } else {
            wp_send_json_error('Failed to delete project.');
        }

    } catch (Exception $e) {
        wp_send_json_error('An error occurred: ' . $e->getMessage());
    }
}
add_action('wp_ajax_delete_project', 'handle_delete_project');









//publications







//archives

function register_researcher_publications_cpt() {
    $labels = array(
        'name' => 'Publications',
        'singular_name' => 'Publication',
        'menu_name' => 'Publications',
        'name_admin_bar' => 'Publication',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Publication',
        'new_item' => 'New Publication',
        'edit_item' => 'Edit Publication',
        'view_item' => 'View Publication',
        'all_items' => 'All Publications',
        'search_items' => 'Search Publications',
        'not_found' => 'No publications found.',
        'not_found_in_trash' => 'No publications found in Trash.'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'publications'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-book-alt',
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields'),
        'show_in_rest' => true,
        'show_in_buddypress' => true,
        'taxonomies' => array('publication_type', 'publication_year')
    );

    register_post_type('publication', $args);

    // Register Publication Type Taxonomy
    register_taxonomy('publication_type', 'publication', array(
        'label' => 'Publication Types',
        'rewrite' => array('slug' => 'publication-type'),
        'hierarchical' => true,
        'show_in_rest' => true
    ));

    // Register Publication Year Taxonomy
    register_taxonomy('publication_year', 'publication', array(
        'label' => 'Publication Years',
        'rewrite' => array('slug' => 'publication-year'),
        'hierarchical' => false,
        'show_in_rest' => true
    ));
}
add_action('init', 'register_researcher_publications_cpt');
//archive of publication


//publication tab in the profile
function bp_custom_publication_tab() {
    bp_core_new_nav_item(array(
        'name' => 'Publications',
        'slug' => 'publications',
        'screen_function' => 'bp_custom_publication_screen',
        'position' => 30,
        'default_subnav_slug' => 'publications',
        'show_for_displayed_user' => true
    ));
}

function bp_custom_publication_screen() {
    // Add action to display our template when BP loads the content
    add_action('bp_template_content', 'bp_load_publications_template');
    
    // Load BuddyPress's default template (handles the container)
    bp_core_load_template('members/single/plugins');
}

function bp_load_publications_template() {
    // Locate the template file in your child theme
    $template = locate_template('buddypress/members/single/publications.php');
    
    if (!empty($template)) {
        // Extract the user ID to make it available in the template
        $user_id = bp_displayed_user_id();
        
        // Include the template file
        include($template);
    } else {
        echo '<p>Publications template not found.</p>';
    }
}
add_action('bp_setup_nav', 'bp_custom_publication_tab');

// Keep your existing publications count function
function add_publications_count_to_profile() {
    $user_id = bp_displayed_user_id();
    $pub_count = count_user_posts($user_id, 'publication');
    $proj_count = count_user_posts($user_id, 'project');
    
    echo '<div class="profile-counts">';
    
    if ($pub_count > 0) {
        echo '<div class="profile-count-item">';
        echo '<strong>' . $pub_count . '</strong> ' . _n('Publication', 'Publications', $pub_count);
        echo '</div>';
    }
    
    if ($proj_count > 0) {
        echo '<div class="profile-count-item">';
        echo '<strong>' . $proj_count . '</strong> ' . _n('Project', 'Projects', $proj_count);
        echo '</div>';
    }
    
    echo '</div>';
}
add_action('bp_profile_header_meta', 'add_publications_count_to_profile');


///add filter 
// AJAX Publications Filter
add_action('wp_ajax_filter_publications', 'filter_publications_callback');
add_action('wp_ajax_nopriv_filter_publications', 'filter_publications_callback');

function filter_publications_callback() {
    check_ajax_referer('publications_nonce', 'nonce');
    
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    $year = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $author = isset($_POST['author']) ? intval($_POST['author']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    
    $args = array(
        'post_type' => 'publication',
        'posts_per_page' => 12,
        'paged' => $paged,
        'orderby' => 'meta_value',
        'meta_key' => 'publication_date',
        'order' => 'DESC'
    );
    
    if (!empty($type)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'publication_type',
                'field' => 'slug',
                'terms' => $type
            )
        );
    }
    
    if (!empty($year)) {
        $args['tax_query'] = isset($args['tax_query']) ? $args['tax_query'] : array();
        $args['tax_query'][] = array(
            'taxonomy' => 'publication_year',
            'field' => 'slug',
            'terms' => $year
        );
    }
    
    if (!empty($author)) {
        $args['author'] = $author;
    }
    
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    $publications = new WP_Query($args);
    
    if ($publications->have_posts()) {
        echo '<div class="buddyx-row">';
        while ($publications->have_posts()) {
            $publications->the_post();
            echo '<div class="buddyx-col-md-6 buddyx-col-lg-4">';
            get_template_part('template-parts/publication', 'card');
            echo '</div>';
        }
        echo '</div>';
        
        echo '<div class="publications-pagination">';
        echo paginate_links(array(
            'total' => $publications->max_num_pages,
            'current' => $paged,
            'prev_text' => __('« Previous'),
            'next_text' => __('Next »'),
        ));
        echo '</div>';
    } else {
        echo '<p>No publications found matching your criteria.</p>';
    }
    
    wp_reset_postdata();
    wp_die();
}

// Enqueue AJAX script
add_action('wp_enqueue_scripts', 'publications_ajax_scripts');
function publications_ajax_scripts() {
    if (is_post_type_archive('publication') || is_tax('publication_type') || is_tax('publication_year')) {
        wp_enqueue_script('publications-ajax', get_stylesheet_directory_uri() . '/js/publications-ajax.js', array('jquery'), '1.0', true);
        wp_localize_script('publications-ajax', 'publications_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('publications_nonce')
        ));
    }
}








//////projects

//Add this to your functions.php (or similar to your "Publication" CPT):
function register_researcher_projects_cpt() {
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
        'not_found' => 'No projects found.',
        'not_found_in_trash' => 'No projects found in Trash.'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest' => true,
        'show_in_buddypress' => true,
    );

    register_post_type('project', $args);
}
add_action('init', 'register_researcher_projects_cpt');

//Add a "Projects" Tab to BuddyPress Profile

function bp_custom_projects_tab() {
    bp_core_new_nav_item(array(
        'name' => 'Projects',
        'slug' => 'projects',
        'screen_function' => 'bp_custom_projects_screen',
        'position' => 31,
        'default_subnav_slug' => 'projects',
        'show_for_displayed_user' => true
    ));
}
add_action('bp_setup_nav', 'bp_custom_projects_tab');

function bp_custom_projects_screen() {
    add_action('bp_template_content', 'bp_load_projects_template');
    bp_core_load_template('members/single/plugins');
}

function bp_load_projects_template() {
    $template = locate_template('buddypress/members/single/projects.php');
    if (!empty($template)) {
        $user_id = bp_displayed_user_id();
        include($template);
    } else {
        echo '<p>Projects template not found.</p>';
    }
}
//Show "+" Icon with Tooltip Below Profile Header

function add_projects_plus_icon_to_profile() {
    if (bp_is_my_profile() && bp_is_current_component('projects')) {
        $add_url = home_url('/add-project'); // Change to your add project page
        echo '<div class="profile-add-project" style="margin: 20px 0; text-align:center;">
            <a href="' . esc_url($add_url) . '" title="Add new project" style="font-size: 2em; text-decoration: none;">
                <span class="dashicons dashicons-plus"></span>
            </a>
        </div>';
    }
}
add_action('bp_template_content', 'add_projects_plus_icon_to_profile', 5);

//Add Activity When a Project is Created or Updated
function bp_project_activity_on_save($post_id, $post, $update) {
    // Only for 'project' post type and published posts
    if ($post->post_type !== 'project' || $post->post_status !== 'publish') {
        return;
    }

    // Get user ID
    $user_id = $post->post_author;

    // Set action text
    $action = $update
        ? sprintf('%s updated a project: <a href="%s">%s</a>', bp_core_get_user_displayname($user_id), get_permalink($post_id), esc_html($post->post_title))
        : sprintf('%s created a new project: <a href="%s">%s</a>', bp_core_get_user_displayname($user_id), get_permalink($post_id), esc_html($post->post_title));

    // Record activity only if BuddyPress is active
    if (function_exists('bp_activity_add')) {
        bp_activity_add(array(
            'user_id'   => $user_id,
            'action'    => $action,
            'component' => 'projects', // You can use 'activity' or register your own component
            'type'      => $update ? 'updated_project' : 'new_project',
            'item_id'   => $post_id,
            'secondary_item_id' => false,
            'recorded_time' => current_time('mysql'),
            'hide_sitewide' => false,
        ));
    }
}
add_action('save_post_project', 'bp_project_activity_on_save', 10, 3);
//Add Activity When a Project is Deleted
function bp_project_activity_on_delete($post_id) {
    $post = get_post($post_id);
    if ($post && $post->post_type === 'project') {
        // Optionally, you can remove the activity or record a "deleted" activity
        // Remove activity:
        if (function_exists('bp_activity_delete')) {
            bp_activity_delete(array(
                'item_id' => $post_id,
                'component' => 'projects',
            ));
        }
    }
}
add_action('before_delete_post', 'bp_project_activity_on_delete');

//enqueue the modal for the project
function enqueue_project_modal_assets() {
    wp_enqueue_style('project-modal-style', get_stylesheet_directory_uri() . '/css/project-modal.css');
    wp_enqueue_script('project-modal-script', get_stylesheet_directory_uri() . '/js/project-modal.js', array('jquery'), null, true);
    wp_localize_script('project-modal-script', 'projectModalVars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('add_project_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_project_modal_assets');
//modal for the project opnings
function add_project_modal_html() {
    if (bp_is_my_profile() && bp_is_current_component('projects')) { ?>
        <div id="project-modal" class="project-modal">
            <div class="project-modal-content">
                <span class="project-modal-close">&times;</span>
                <h2>Add New Project</h2>
                <form id="add-project-form" enctype="multipart/form-data">
                    <label>Project Title*</label>
                    <input type="text" name="project_title" required>
                    
                    <label>Description*</label>
                    <textarea name="project_description" required></textarea>
                    
                    <label>Start Date</label>
                    <input type="date" name="project_start">
                    
                    <label>End Date</label>
                    <input type="date" name="project_end">
                    
                    <label>Funding Amount</label>
                    <input type="number" name="project_funding" min="0" step="any">
                    
                    <label>Project Document (PDF)</label>
                    <input type="file" name="project_document" accept="application/pdf">
                    
                    <label>Collaborators</label>
                    <input type="text" name="project_collaborators" placeholder="Names, comma separated">
                    
                    <input type="hidden" name="project_status" value="pending">
                    <button type="submit">Submit Project</button>
                </form>
                <div id="project-form-message"></div>
            </div>
        </div>
    <?php }
}
add_action('bp_template_content', 'add_project_modal_html', 6);
//handle the project modal ajax in php for Proects
function handle_add_new_project() {
    check_ajax_referer('add_project_nonce', 'nonce');
    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in to create a project.');
        return;
    }

    $user_id = get_current_user_id();
    $title = sanitize_text_field($_POST['project_title']);
    $desc = sanitize_textarea_field($_POST['project_description']);
    $start = sanitize_text_field($_POST['project_start']);
    $end = sanitize_text_field($_POST['project_end']);
    $funding = floatval($_POST['project_funding']);
    $collab = sanitize_text_field($_POST['project_collaborators']);
    $status = 'pending';

    if (empty($title) || empty($desc)) {
        wp_send_json_error('Title and description are required.');
        return;
    }

    $post_id = wp_insert_post(array(
        'post_type' => 'project',
        'post_title' => $title,
        'post_content' => $desc,
        'post_status' => 'publish',
        'post_author' => $user_id,
        'meta_input' => array(
            'project_start' => $start,
            'project_end' => $end,
            'project_funding' => $funding,
            'project_collaborators' => $collab,
            'project_status' => $status,
        )
    ));

    if (is_wp_error($post_id)) {
        wp_send_json_error('Error creating project: ' . $post_id->get_error_message());
        return;
    }

    // Handle file upload
    if (!empty($_FILES['project_document']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $uploaded = media_handle_upload('project_document', $post_id);
        if (is_wp_error($uploaded)) {
            wp_send_json_error('File upload failed: ' . $uploaded->get_error_message());
            return;
        }
        update_post_meta($post_id, 'project_document', $uploaded);
    }

    wp_send_json_success('Project created successfully!');
}
add_action('wp_ajax_add_new_project', 'handle_add_new_project');

//Send Email Notification to Admin on New Project Submission

function notify_admin_on_new_project($post_id, $post, $update) {
    // Only for 'project' post type, only when created (not updated)
    if ($post->post_type !== 'project' || $update) return;

    // Get admin email(s)
    $admin_email = get_option('admin_email');
    $project_author = get_userdata($post->post_author);

    // Email subject and message
    $subject = 'New Project Submitted for Approval';
    $message = "A new project has been submitted by {$project_author->display_name}.\n\n";
    $message .= "Title: {$post->post_title}\n";
    $message .= "View/Edit: " . admin_url("post.php?post={$post_id}&action=edit") . "\n\n";
    $message .= "Please review and approve or reject this project in the admin dashboard.";

    // Send email
    wp_mail($admin_email, $subject, $message);
}
add_action('wp_insert_post', 'notify_admin_on_new_project', 10, 3);
//Add a Custom Meta Box for Project 

// Add meta box
function project_status_meta_box() {
    add_meta_box(
        'project_status_box',
        'Project Approval Status',
        'project_status_box_content',
        'project',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'project_status_meta_box');

function project_status_box_content($post) {
    $status = get_post_meta($post->ID, 'project_status', true) ?: 'pending';
    ?>
    <label for="project_status_field">Status:</label>
    <select name="project_status_field" id="project_status_field">
        <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
        <option value="approved" <?php selected($status, 'approved'); ?>>Approved</option>
        <option value="rejected" <?php selected($status, 'rejected'); ?>>Rejected</option>
    </select>
    <?php
    wp_nonce_field('save_project_status', 'project_status_nonce');
}

// Save status
function save_project_status_meta($post_id) {
    if (!isset($_POST['project_status_nonce']) || !wp_verify_nonce($_POST['project_status_nonce'], 'save_project_status')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if ('project' !== $_POST['post_type']) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['project_status_field'])) {
        update_post_meta($post_id, 'project_status', sanitize_text_field($_POST['project_status_field']));
    }
}
add_action('save_post', 'save_project_status_meta');
//Show Status in Project List Table
//To make it even easier, add a "Status" column to the Projects list in the admin:
// Add column
function add_project_status_column($columns) {
    $columns['project_status'] = 'Status';
    return $columns;
}
add_filter('manage_project_posts_columns', 'add_project_status_column');

function show_project_status_column($column, $post_id) {
    if ($column == 'project_status') {
        $status = get_post_meta($post_id, 'project_status', true) ?: 'pending';
        echo ucfirst($status);
    }
}
add_action('manage_project_posts_custom_column', 'show_project_status_column', 10, 2);

function handle_add_project_collaborators() {
    check_ajax_referer('add_project_nonce', 'nonce');
    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in to add collaborators.');
        return;
    }

    $project_id = intval($_POST['project_id']);
    $project = get_post($project_id);

    if (!$project || $project->post_type !== 'project' || 
        ($project->post_author != get_current_user_id() && !current_user_can('edit_others_posts'))) {
        wp_send_json_error('You do not have permission to edit this project.');
        return;
    }

    $user_collabs = isset($_POST['project_collaborators_users']) ? array_map('intval', $_POST['project_collaborators_users']) : array();
    $external_collabs = sanitize_text_field($_POST['project_collaborators_external']);
    $working_area = esc_url_raw($_POST['project_working_area']);

    update_post_meta($project_id, 'project_collaborators_users', $user_collabs);
    update_post_meta($project_id, 'project_collaborators_external', $external_collabs);
    update_post_meta($project_id, 'project_working_area', $working_area);

    wp_send_json_success('Collaborators updated successfully!');
}
add_action('wp_ajax_add_project_collaborators', 'handle_add_project_collaborators');

// Get Project Data for Editing
function get_project_data() {
    check_ajax_referer('add_project_nonce', 'nonce');
    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in to edit a project.');
        return;
    }

    $project_id = intval($_POST['project_id']);
    $project = get_post($project_id);

    if (!$project || $project->post_type !== 'project' || 
        ($project->post_author != get_current_user_id() && !current_user_can('edit_others_posts'))) {
        wp_send_json_error('You do not have permission to edit this project.');
        return;
    }

    $data = array(
        'ID' => $project->ID,
        'title' => $project->post_title,
        'description' => $project->post_content,
        'start_date' => get_post_meta($project_id, 'project_start', true),
        'end_date' => get_post_meta($project_id, 'project_end', true),
        'funding' => get_post_meta($project_id, 'project_funding', true),
        'collaborators' => get_post_meta($project_id, 'project_collaborators', true),
        'document_url' => wp_get_attachment_url(get_post_meta($project_id, 'project_document', true))
    );

    wp_send_json_success($data);
}
add_action('wp_ajax_get_project_data', 'get_project_data');

// Update Project
function update_project() {
    // Debug log
    error_log('Update project request received');
    
    check_ajax_referer('add_project_nonce', 'nonce');
    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in to update a project.');
        return;
    }

    $project_id = intval($_POST['project_id']);
    error_log('Attempting to update project ID: ' . $project_id);
    
    $project = get_post($project_id);

    if (!$project || $project->post_type !== 'project' || 
        ($project->post_author != get_current_user_id() && !current_user_can('edit_others_posts'))) {
        wp_send_json_error('You do not have permission to edit this project.');
        return;
    }

    $title = sanitize_text_field($_POST['project_title']);
    $desc = sanitize_textarea_field($_POST['project_description']);
    $start = sanitize_text_field($_POST['project_start']);
    $end = sanitize_text_field($_POST['project_end']);
    $funding = floatval($_POST['project_funding']);
    $collab = sanitize_text_field($_POST['project_collaborators']);

    if (empty($title) || empty($desc)) {
        wp_send_json_error('Title and description are required.');
        return;
    }

    $updated = wp_update_post(array(
        'ID' => $project_id,
        'post_title' => $title,
        'post_content' => $desc
    ));

    if (is_wp_error($updated)) {
        error_log('Error updating project: ' . $updated->get_error_message());
        wp_send_json_error('Error updating project: ' . $updated->get_error_message());
        return;
    }

    update_post_meta($project_id, 'project_start', $start);
    update_post_meta($project_id, 'project_end', $end);
    update_post_meta($project_id, 'project_funding', $funding);
    update_post_meta($project_id, 'project_collaborators', $collab);

    // Handle file upload if new file is provided
    if (!empty($_FILES['project_document']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Delete old document if exists
        $old_doc_id = get_post_meta($project_id, 'project_document', true);
        if ($old_doc_id) {
            wp_delete_attachment($old_doc_id, true);
        }

        $uploaded = media_handle_upload('project_document', $project_id);
        if (is_wp_error($uploaded)) {
            error_log('File upload failed: ' . $uploaded->get_error_message());
            wp_send_json_error('File upload failed: ' . $uploaded->get_error_message());
            return;
        }
        update_post_meta($project_id, 'project_document', $uploaded);
    }

    error_log('Project updated successfully');
    wp_send_json_success('Project updated successfully!');
}
add_action('wp_ajax_update_project', 'update_project');

// Keep only the main menu dropdown filter:
add_filter('wp_nav_menu_items', function($items, $args) {
    // Change 'primary' to your menu location if needed
    if ($args->theme_location === 'primary') {
        // Find the Activity menu item
        $activity_url = home_url('/activity/');
        $publications_url = get_post_type_archive_link('publication');
        $projects_url = get_post_type_archive_link('project');

        // Build submenu HTML
        $submenu = '
            <ul class="sub-menu">
                <li><a href="' . esc_url($publications_url) . '">Publications</a></li>
                <li><a href="' . esc_url($projects_url) . '">Projects</a></li>
            </ul>
        ';

        // Insert submenu after Activity
        $items = preg_replace(
            '#(<li[^>]*>\s*<a[^>]*href="' . preg_quote($activity_url, '#') . '"[^>]*>.*?</a>)#',
            '$1' . $submenu,
            $items
        );
    }
    return $items;
}, 10, 2);

// Add Logout button under BuddyPress Settings with confirmation
function buddyx_logout_link_at_profile_bottom() {
    if (bp_is_my_profile()) {
        $logout_url = wp_logout_url(home_url('/login'));
        echo '<div class="bp-profile-logout-link" style="text-align:center; margin: 40px 0 0 0;">
            <a href="' . esc_url($logout_url) . '" id="bp-logout-link" style="color: #e74c3c; font-weight: bold; text-decoration: none; font-size: 1.1em;">Log Out</a>
        </div>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var link = document.getElementById("bp-logout-link");
            if(link) {
                link.addEventListener("click", function(e) {
                    if(!confirm("Are you sure you want to log out?")) {
                        e.preventDefault();
                    }
                });
            }
        });
        </script>
        <style>
            .bp-profile-logout-link a:hover { text-decoration: underline; color: #c0392b; }
        </style>';
    }
}
// Remove previous logout link hooks
remove_action('bp_after_member_settings_nav', 'buddyx_logout_link_beneath_settings_nav');
remove_action('bp_after_member_options_nav', 'buddyx_logout_link_below_settings_nav');
// Add the logout link at the very bottom of the BuddyPress profile
add_action('bp_after_member_body', 'buddyx_logout_link_at_profile_bottom');







//mickealy

// ORCID Integration Functions
function add_orcid_field_to_profile() {
    if (function_exists('bp_is_active') && bp_is_active('xprofile')) {
        // Check if the field already exists
        $field_id = xprofile_get_field_id_from_name('ORCID ID');
        
        if (!$field_id) {
            // Create the ORCID field
            $field_id = xprofile_insert_field(
                array(
                    'field_group_id' => 1, // Default group
                    'name' => 'ORCID ID',
                    'type' => 'textbox',
                    'description' => 'Enter your ORCID ID (e.g., 0000-0000-0000-0000)',
                    'is_required' => false,
                    'can_delete' => false,
                    'order_by' => 'custom',
                    'is_default_option' => false,
                    'field_order' => 1,
                )
            );
        }
    }
}
add_action('bp_init', 'add_orcid_field_to_profile');

// Enhanced ORCID publication display
function orcid_publications_content() {
    $user_id = bp_displayed_user_id();
    $orcid_id = xprofile_get_field_data('ORCID ID', $user_id);
    if (empty($orcid_id)) {
        echo '<div class="bp-feedback info">';
        echo '<span class="bp-icon" aria-hidden="true"></span>';
        echo '<p>' . __('No ORCID ID found for this user.', 'buddyx') . '</p>';
        echo '<p>' . __('Please add your ORCID ID in your profile under Academic Information section.', 'buddyx') . '</p>';
        echo '<p><a href="' . bp_core_get_user_domain($user_id) . 'profile/edit/group/1/" class="button">Add ORCID ID</a></p>';
        echo '<p><a href="https://orcid.org/register" target="_blank">Get an ORCID ID</a></p>';
        echo '</div>';
        return;
    }
    $orcid_id = preg_replace('/[^0-9X]/', '', $orcid_id);
    $formatted_orcid = substr($orcid_id, 0, 4) . '-' . substr($orcid_id, 4, 4) . '-' . substr($orcid_id, 8, 4) . '-' . substr($orcid_id, 12, 4);
    $publications = fetch_orcid_publications($orcid_id);
    if (is_wp_error($publications)) {
        echo '<div class="bp-feedback error">';
        echo '<span class="bp-icon" aria-hidden="true"></span>';
        echo '<p>' . esc_html($publications->get_error_message()) . '</p>';
        echo '<p><a href="https://orcid.org/' . esc_attr($formatted_orcid) . '" target="_blank">View your ORCID profile</a></p>';
        echo '</div>';
        return;
    }
    if (empty($publications)) {
        echo '<div class="bp-feedback info">';
        echo '<span class="bp-icon" aria-hidden="true"></span>';
        echo '<p>' . __('No publications found for this ORCID ID. Please make sure your ORCID profile is public and contains works.', 'buddyx') . '</p>';
        echo '<p><a href="https://orcid.org/' . esc_attr($formatted_orcid) . '" target="_blank">View your ORCID profile</a></p>';
        echo '</div>';
        return;
    }
    echo '<div class="orcid-publications-list">';
    foreach ($publications as $i => $publication) {
        $work_id = $publication['work_id'] ?? $i; // fallback if not available
        echo '<div class="orcid-publication-card">';
        echo '<div class="orcid-publication-header">';
        echo '<input type="checkbox" />';
        echo '<strong class="orcid-publication-title">' . esc_html($publication['title']) . '</strong>';
        echo '<div class="orcid-publication-visibility"><span class="visibility-icon">👁️</span> Everyone</div>';
        echo '<button class="orcid-publication-edit" title="Edit">✏️</button>';
        echo '</div>';
        echo '<div class="orcid-publication-meta">';
        if (!empty($publication['author'])) {
            echo '<span class="orcid-publication-author">' . esc_html($publication['author']) . '</span>';
        }
        if (!empty($publication['date'])) {
            echo '<span class="orcid-publication-date">' . esc_html($publication['date']) . '</span>';
        } elseif (!empty($publication['year'])) {
            echo '<span class="orcid-publication-date">' . esc_html($publication['year']) . '</span>';
        }
        if (!empty($publication['type'])) {
            echo '<span class="orcid-publication-type">' . esc_html($publication['type']) . '</span>';
        }
        if (!empty($publication['role'])) {
            echo '<span class="orcid-publication-role">' . esc_html($publication['role']) . '</span>';
        }
        echo '</div>';
        if (!empty($publication['contributors'])) {
            echo '<div class="orcid-publication-contributors">CONTRIBUTORS: ' . esc_html($publication['contributors']) . '</div>';
        }
        echo '<div class="orcid-publication-source"><span class="source-label">Source:</span> <span class="source-avatar">👤</span> ' . esc_html($publication['source'] ?? bp_core_get_user_displayname($user_id)) . '</div>';
        echo '<div class="orcid-publication-actions">';
        echo '<a href="#" class="show-more-detail" data-orcid="' . esc_attr($formatted_orcid) . '" data-workid="' . esc_attr($work_id) . '" data-index="' . esc_attr($i) . '">Show more detail</a>';
        echo '<button class="orcid-publication-delete" title="Delete">🗑️</button>';
        echo '</div>';
        // Hidden details for modal (JSON encoded)
        echo '<script type="application/json" id="orcid-pub-' . esc_attr($i) . '">' . json_encode($publication) . '</script>';
        echo '</div>';
    }
    echo '</div>';
    // Modal HTML
    echo '<div id="orcid-detail-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#fff;padding:24px 32px;border-radius:10px;max-width:500px;width:90%;position:relative;">
            <button id="orcid-modal-close" style="position:absolute;top:8px;right:12px;font-size:1.5em;background:none;border:none;cursor:pointer;">&times;</button>
            <div id="orcid-modal-content"></div>
        </div>
    </div>';
    ?>
    <style>
    .orcid-publication-card { border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); padding: 16px; }
    .orcid-publication-header { display: flex; align-items: center; justify-content: space-between; background: #f3f4f6; padding: 8px 12px; border-radius: 6px 6px 0 0; font-weight: bold; }
    .orcid-publication-title { flex: 1; margin-left: 8px; }
    .orcid-publication-visibility { margin-left: 16px; color: #16a34a; font-size: 0.95em; display: flex; align-items: center; }
    .orcid-publication-edit, .orcid-publication-delete { background: none; border: none; cursor: pointer; margin-left: 8px; font-size: 1.1em; }
    .orcid-publication-meta, .orcid-publication-contributors, .orcid-publication-source, .orcid-publication-actions { margin-top: 8px; font-size: 0.98em; }
    .orcid-publication-meta span { margin-right: 12px; color: #555; }
    .source-label { font-weight: bold; }
    .source-avatar { margin-right: 4px; }
    .show-more-detail { color: #2563eb; text-decoration: underline; cursor: pointer; }
    #orcid-detail-modal { display: none; align-items: center; justify-content: center; }
    #orcid-detail-modal.active { display: flex !important; }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.show-more-detail').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var idx = this.getAttribute('data-index');
                var orcid = this.getAttribute('data-orcid');
                var workid = this.getAttribute('data-workid');
                var pubData = document.getElementById('orcid-pub-' + idx);
                if (!pubData) return;
                var pub = JSON.parse(pubData.textContent);
                var html = '';
                html += '<h2>' + (pub.title ? pub.title : '') + '</h2>';
                if (pub.subtitle) html += '<h3 style="font-weight:normal;color:#555;">' + pub.subtitle + '</h3>';
                html += '<p><strong>Type:</strong> ' + (pub.type ? pub.type : '-') + '</p>';
                html += '<p><strong>Date:</strong> ' + (pub.date ? pub.date : '-') + '</p>';
                html += '<p><strong>Journal:</strong> ' + (pub.journal ? pub.journal : '-') + '</p>';
                html += '<p><strong>Author:</strong> ' + (pub.author ? pub.author : '-') + '</p>';
                html += '<p><strong>Contributors:</strong> ' + (pub.contributors ? pub.contributors : '-') + '</p>';
                html += '<p><strong>Role:</strong> ' + (pub.role ? pub.role : '-') + '</p>';
                html += '<p><strong>DOI:</strong> ' + (pub.doi ? '<a href="https://doi.org/' + pub.doi + '" target="_blank">' + pub.doi + '</a>' : '-') + '</p>';
                html += '<p><strong>Source:</strong> ' + (pub.source ? pub.source : '-') + '</p>';
                if (pub.short_description) html += '<p><strong>Description:</strong> ' + pub.short_description + '</p>';
                if (pub.citation) html += '<p><strong>Citation:</strong> <span style="font-size:0.95em;">' + pub.citation + '</span></p>';
                if (pub.language) html += '<p><strong>Language:</strong> ' + pub.language + '</p>';
                if (pub.country) html += '<p><strong>Country:</strong> ' + pub.country + '</p>';
                if (pub.url) html += '<p><strong>URL:</strong> <a href="' + pub.url + '" target="_blank">' + pub.url + '</a></p>';
                if (pub.funding) html += '<p><strong>Funding:</strong> ' + JSON.stringify(pub.funding) + '</p>';
                if (pub.external_ids && pub.external_ids.length > 0) {
                    html += '<p><strong>External IDs:</strong><ul>';
                    pub.external_ids.forEach(function(eid) {
                        html += '<li>' + eid['external-id-type'] + ': ' + eid['external-id-value'] + '</li>';
                    });
                    html += '</ul></p>';
                }
                html += '<p><a href="https://orcid.org/' + orcid + (workid ? '/work/' + workid : '') + '" target="_blank" style="color:#2563eb;">View on ORCID</a></p>';
                // Placeholder for edit button (future OAuth2 integration)
                html += '<button disabled style="margin-top:10px;opacity:0.5;">Edit (ORCID sync coming soon)</button>';
                document.getElementById('orcid-modal-content').innerHTML = html;
                document.getElementById('orcid-detail-modal').classList.add('active');
            });
        });
        document.getElementById('orcid-modal-close').addEventListener('click', function() {
            document.getElementById('orcid-detail-modal').classList.remove('active');
        });
        document.getElementById('orcid-detail-modal').addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('active');
        });
    });
    </script>
    <?php
}

// Enhance fetch_orcid_publications to fetch full details for each work using the /work/{put-code} endpoint, and merge all available attributes into the publication array. Update the modal display to show all available fields. Add basic caching to avoid repeated API calls for the same user within 10 minutes.

function fetch_orcid_publications($orcid_id) {
    if (empty($orcid_id)) {
        return false;
    }
    $orcid_id = preg_replace('/[^0-9X]/', '', $orcid_id);
    if (!preg_match('/^\d{4}\d{4}\d{4}\d{4}$/', $orcid_id)) {
        return new WP_Error('invalid_orcid', 'Invalid ORCID ID format. Please use the format: 0000-0000-0000-0000');
    }
    $formatted_orcid = substr($orcid_id, 0, 4) . '-' . substr($orcid_id, 4, 4) . '-' . substr($orcid_id, 8, 4) . '-' . substr($orcid_id, 12, 4);
    $api_url = "https://pub.orcid.org/v3.0/{$formatted_orcid}/works";
    $args = array(
        'headers' => array(
            'Accept' => 'application/json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version')
        ),
        'timeout' => 45,
        'sslverify' => true,
        'httpversion' => '1.1',
        'blocking' => true,
        'redirection' => 5
    );
    // Simple transient cache for 10 minutes
    $cache_key = 'orcid_full_works_' . md5($orcid_id);
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }
    $response = wp_remote_get($api_url, $args);
    if (is_wp_error($response)) {
        return new WP_Error('api_error', 'Error connecting to ORCID API: ' . $response->get_error_message());
    }
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    if ($response_code !== 200) {
        return new WP_Error('api_error', 'ORCID API returned error code: ' . $response_code);
    }
    $data = json_decode($response_body, true);
    if (json_last_error() !== JSON_ERROR_NONE || empty($data) || !isset($data['group'])) {
        return new WP_Error('no_works', 'No works found in ORCID profile.');
    }
    $publications = array();
    foreach ($data['group'] as $group) {
        if (isset($group['work-summary'][0])) {
            $work_summary = $group['work-summary'][0];
            $put_code = $work_summary['put-code'];
            $work_url = "https://pub.orcid.org/v3.0/{$formatted_orcid}/work/{$put_code}";
            $work_response = wp_remote_get($work_url, $args);
            if (!is_wp_error($work_response) && wp_remote_retrieve_response_code($work_response) == 200) {
                $work_details = json_decode(wp_remote_retrieve_body($work_response), true);
                // Merge all available fields
                $publication = array(
                    'work_id' => $put_code,
                    'title' => $work_details['title']['title']['value'] ?? '',
                    'subtitle' => $work_details['title']['subtitle']['value'] ?? '',
                    'type' => $work_details['type'] ?? '',
                    'doi' => '',
                    'year' => $work_details['publication-date']['year']['value'] ?? '',
                    'month' => $work_details['publication-date']['month']['value'] ?? '',
                    'day' => $work_details['publication-date']['day']['value'] ?? '',
                    'date' => '',
                    'journal' => $work_details['journal-title']['value'] ?? '',
                    'url' => $work_details['url']['value'] ?? '',
                    'short_description' => $work_details['short-description'] ?? '',
                    'contributors' => '',
                    'author' => '',
                    'role' => '',
                    'source' => $work_details['source']['source-name']['value'] ?? '',
                    'external_ids' => $work_details['external-ids']['external-id'] ?? array(),
                    'language' => $work_details['language-code'] ?? '',
                    'country' => $work_details['country']['value'] ?? '',
                    'citation' => $work_details['citation']['citation-value'] ?? '',
                    'citation_type' => $work_details['citation']['citation-type'] ?? '',
                    'funding' => $work_details['funding'] ?? '',
                    'work_details_raw' => $work_details,
                );
                // DOI and other external IDs
                if (!empty($publication['external_ids'])) {
                    foreach ($publication['external_ids'] as $eid) {
                        if (($eid['external-id-type'] ?? '') === 'doi') {
                            $publication['doi'] = $eid['external-id-value'] ?? '';
                        }
                    }
                }
                // Contributors
                if (isset($work_details['contributors']['contributor'])) {
                    $names = array();
                    foreach ($work_details['contributors']['contributor'] as $contrib) {
                        if (!empty($contrib['credit-name']['value'])) {
                            $names[] = $contrib['credit-name']['value'];
                        }
                    }
                    $publication['contributors'] = implode(', ', $names);
                    if (isset($work_details['contributors']['contributor'][0]['credit-name']['value'])) {
                        $publication['author'] = $work_details['contributors']['contributor'][0]['credit-name']['value'];
                    }
                    if (isset($work_details['contributors']['contributor'][0]['contributor-attributes']['contributor-role'])) {
                        $publication['role'] = $work_details['contributors']['contributor'][0]['contributor-attributes']['contributor-role'];
                    }
                }
                // Date
                if (!empty($publication['year'])) {
                    $publication['date'] = $publication['year'];
                    if (!empty($publication['month'])) {
                        $publication['date'] .= '-' . str_pad($publication['month'], 2, '0', STR_PAD_LEFT);
                    }
                    if (!empty($publication['day'])) {
                        $publication['date'] .= '-' . str_pad($publication['day'], 2, '0', STR_PAD_LEFT);
                    }
                }
                $publications[] = $publication;
            }
        }
    }
    set_transient($cache_key, $publications, 10 * MINUTE_IN_SECONDS);
    return $publications;
}

// Add ORCID publications tab to profile
function add_orcid_publications_tab() {
    if (function_exists('bp_is_active') && bp_is_active('xprofile')) {
        bp_core_new_nav_item(array(
            'name' => __('ORCID Async', 'buddyx'),
            'slug' => 'orcid-publications',
            'screen_function' => 'orcid_publications_screen',
            'position' => 40,
            'default_subnav_slug' => 'orcid-publications',
            'show_for_displayed_user' => true
        ));
    }
}
add_action('bp_setup_nav', 'add_orcid_publications_tab');

// Screen function for ORCID publications
function orcid_publications_screen() {
    add_action('bp_template_content', 'orcid_publications_content');
    bp_core_load_template('members/single/plugins');
}

// Function to verify ORCID ID exists
function verify_orcid_id_exists($orcid_id) {
    // Clean the ORCID ID
    $orcid_id = preg_replace('/[^0-9X]/', '', $orcid_id);
    
    // Basic format validation
    if (!preg_match('/^\d{4}\d{4}\d{4}\d{4}$/', $orcid_id)) {
        return new WP_Error('invalid_format', 'Invalid ORCID ID format. Please use the format: 0000-0000-0000-0000');
    }
    
    // Check if the profile exists
    $api_url = "https://pub.orcid.org/v3.0/{$orcid_id}";
    $response = wp_remote_get($api_url, array(
        'headers' => array('Accept' => 'application/json'),
        'timeout' => 30
    ));
    
    if (is_wp_error($response)) {
        return new WP_Error('api_error', 'Error checking ORCID ID: ' . $response->get_error_message());
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code === 404) {
        return new WP_Error('not_found', 'This ORCID ID does not exist. Please verify your ID at https://orcid.org/' . $orcid_id);
    }
    
    return true;
}

// Function to ensure ORCID field in profile
function ensure_orcid_field_in_profile() {
    if (function_exists('bp_is_active') && bp_is_active('xprofile')) {
        // First, check if the field exists
        $field_id = xprofile_get_field_id_from_name('ORCID ID');
        
        if (!$field_id) {
            // Get or create the "Academic Information" group
            $group_id = xprofile_get_field_group_id_from_name('Academic Information');
            if (!$group_id) {
                $group_id = xprofile_insert_field_group(array(
                    'name' => 'Academic Information',
                    'description' => 'Academic and research information',
                    'can_delete' => false
                ));
            }
            
            // Create the ORCID field in the Academic Information group
            $field_id = xprofile_insert_field(
                array(
                    'field_group_id' => $group_id,
                    'name' => 'ORCID ID',
                    'type' => 'textbox',
                    'description' => 'Enter your ORCID ID (e.g., 0000-0000-0000-0000). This will automatically display your publications on your profile. <a href="https://orcid.org/register" target="_blank">Get an ORCID ID</a>',
                    'is_required' => false,
                    'can_delete' => false,
                    'order_by' => 'custom',
                    'is_default_option' => false,
                    'field_order' => 1,
                )
            );
            
            if ($field_id) {
                // Add validation for ORCID ID format
                add_filter('bp_xprofile_validate_field', function($valid, $value, $field) use ($field_id) {
                    if ($field->id == $field_id && !empty($value)) {
                        // Clean the ORCID ID
                        $clean_id = preg_replace('/[^0-9X]/', '', $value);
                        
                        // Check format
                        if (!preg_match('/^\d{4}\d{4}\d{4}\d{4}$/', $clean_id)) {
                            return new WP_Error('invalid_orcid', 'Please enter a valid ORCID ID in the format: 0000-0000-0000-0000');
                        }
                        
                        // Verify the ORCID ID exists
                        $verification = verify_orcid_id_exists($clean_id);
                        if (is_wp_error($verification)) {
                            return $verification;
                        }
                    }
                    return $valid;
                }, 10, 3);
                
                bp_core_add_message('ORCID ID field has been created in the Academic Information section.', 'success');
            }
        } else {
            // Field exists, make sure it's in the right group
            $field = new BP_XProfile_Field($field_id);
            $group = new BP_XProfile_Group($field->group_id);
            
            // Update the field description if needed
            if ($field->description !== 'Enter your ORCID ID (e.g., 0000-0000-0000-0000). This will automatically display your publications on your profile. <a href="https://orcid.org/register" target="_blank">Get an ORCID ID</a>') {
                // Use BP_XProfile_Field class to update the field
                $field->description = 'Enter your ORCID ID (e.g., 0000-0000-0000-0000). This will automatically display your publications on your profile. <a href="https://orcid.org/register" target="_blank">Get an ORCID ID</a>';
                $field->save();
            }
            
            bp_core_add_message(sprintf(
                'ORCID ID field is located in the "%s" section of your profile.',
                $group->name
            ), 'info');
        }
    }
}
add_action('bp_init', 'ensure_orcid_field_in_profile');

// Add AJAX handler for ORCID verification
function verify_orcid_id_ajax() {
    check_ajax_referer('orcid_verify_nonce', 'nonce');
    
    $orcid_id = isset($_POST['orcid_id']) ? sanitize_text_field($_POST['orcid_id']) : '';
    
    if (empty($orcid_id)) {
        wp_send_json_error('Please enter an ORCID ID');
    }
    
    $verification = verify_orcid_id_exists($orcid_id);
    
    if (is_wp_error($verification)) {
        wp_send_json_error($verification->get_error_message());
    }
    
    wp_send_json_success('ORCID ID is valid');
}
add_action('wp_ajax_verify_orcid_id', 'verify_orcid_id_ajax');

// Add JavaScript for ORCID verification
function add_orcid_verification_script() {
    if (bp_is_user_profile_edit()) {
        wp_enqueue_script('orcid-verification', get_stylesheet_directory_uri() . '/js/orcid-verification.js', array('jquery'), '1.0', true);
        wp_localize_script('orcid-verification', 'orcid_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('orcid_verify_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'add_orcid_verification_script');

// Remove "Forums" tab from BuddyPress profile (main nav and subnav)
function fully_remove_forums_tab_bp() {
    bp_core_remove_nav_item('forums');
    // Remove possible subnavs under forums
    if (function_exists('bp_core_remove_subnav_item')) {
        bp_core_remove_subnav_item('forums', 'topics');
        bp_core_remove_subnav_item('forums', 'replies');
        bp_core_remove_subnav_item('forums', 'favorites');
        bp_core_remove_subnav_item('forums', 'subscriptions');
    }
}
add_action('bp_setup_nav', 'fully_remove_forums_tab_bp', 99);