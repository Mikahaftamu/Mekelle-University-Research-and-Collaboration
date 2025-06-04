<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Register a new template location.
 *
 * @return array
 */
function bb_afa_register_template_location($templates) {
	$templates[BB_AFA_TEMPLATE_DIR . 'activity/' . BB_AFA_PAGE_TEMPLATE_FILENAME . '.php'] = __('Custom Template With Activity Feed', 'activity-feed-anywhere-for-buddyboss');
    return $templates;
}
add_filter('theme_page_templates', 'bb_afa_register_template_location');

/**
 * Load template from plugin folder when to use
 *
 * @return string
 */
function bb_afa_load_page_template($template) {
    if (is_page_template(BB_AFA_TEMPLATE_DIR . 'activity/' . BB_AFA_PAGE_TEMPLATE_FILENAME . '.php')) {
        // Check if template exists in theme
        $theme_template = get_stylesheet_directory() . '/' . BB_AFA_PAGE_TEMPLATE_FILENAME . '.php';
        if (file_exists($theme_template)) {
            return $theme_template;
        }
        // Use template from plugin
        $plugin_template = BB_AFA_PLUGIN_PATH . 'templates/activity/' . BB_AFA_PAGE_TEMPLATE_FILENAME . '.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter('template_include', 'bb_afa_load_page_template');