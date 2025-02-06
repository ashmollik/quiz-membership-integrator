<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://github.com/ashmollik/quiz-membership-integrator
 * @since             1.0.0
 * @package           Quiz_Membership_Integrator
 *
 * @wordpress-plugin
 * Plugin Name:       Quiz Membership Integrator
 * Plugin URI:        https://github.com/ashmollik/quiz-membership-integrator
 * Description:       Integrate quiz maker with Paid Membership Pro to upgrade membership level based on quiz scores.
 * Version:           1.0.0
 * Author:            Ashiqur Rahman
 * Author URI:        https://facebook.com/AshiqurRahmanMollik/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       quiz-membership-integrator
 * Domain Path:       /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include required files
require_once plugin_dir_path( __FILE__ ) . 'includes/qmi-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/qmi-admin-settings.php';

// Add menu page - Moved from admin settings file to main plugin file
if (!function_exists('qmi_add_admin_menu')) {
    function qmi_add_admin_menu() {
        $hook = add_menu_page(
            'Quiz Membership Integrator',
            'Quiz Membership',
            'manage_options',
            'quiz-membership-integrator',
            'qmi_render_admin_page',
            'dashicons-chart-area'
        );
        
        // Add a function to handle the admin page load
        add_action("load-$hook", 'qmi_admin_page_load');
    }
}
add_action('admin_menu', 'qmi_add_admin_menu');

/**
 * Handle admin page load and setup
 */
function qmi_admin_page_load() {
    // Create and verify nonce for page load
    $nonce = wp_create_nonce('qmi_admin_page_nonce');
    // Store nonce in a transient with user-specific key
    set_transient('qmi_admin_page_nonce_' . get_current_user_id(), $nonce, HOUR_IN_SECONDS);
}

/**
 * Verify if we're on the plugin admin page
 *
 * @return bool
 */
function qmi_is_plugin_admin_page() {
    // Check if we're on an admin page
    if (!is_admin()) {
        return false;
    }

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return false;
    }

    // Get the stored nonce
    $stored_nonce = get_transient('qmi_admin_page_nonce_' . get_current_user_id());
    
    // Verify we're on the correct page with proper nonce
    if (
        isset($_GET['page']) && 
        isset($_GET['_wpnonce']) && 
        $stored_nonce && 
        wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'qmi_admin_page_nonce')
    ) {
        $page = sanitize_text_field(wp_unslash($_GET['page']));
        return $page === 'quiz-membership-integrator';
    }

    return false;
}

// Enqueue admin scripts and styles
function qmi_enqueue_admin_scripts($hook) {
    // Early return if not our page
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    // Check if we're on our page without using $_GET directly
    $screen = get_current_screen();
    if (!$screen || 'toplevel_page_quiz-membership-integrator' !== $screen->id) {
        return;
    }

    wp_enqueue_script('qmi-admin-settings-js', plugin_dir_url(__FILE__) . 'admin/js/qmi-admin-settings.js', array('jquery'), '1.0.0', true);
    wp_enqueue_style('qmi-admin-css', plugin_dir_url(__FILE__) . 'admin/css/qmi-admin.css', array(), '1.0.0');
    wp_enqueue_style('qmi-font-awesome', plugin_dir_url(__FILE__) . 'admin/css/all.min.css', array(), '6.0.0');
    
    // Add nonce to JavaScript
    wp_localize_script('qmi-admin-settings-js', 'qmiAdmin', array(
        'nonce' => wp_create_nonce('qmi_admin_ajax_nonce'),
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('admin_enqueue_scripts', 'qmi_enqueue_admin_scripts');

// Add nonce field to admin pages where forms exist
function qmi_add_nonce_field() {
    $screen = get_current_screen();
    if ($screen && 'toplevel_page_quiz-membership-integrator' === $screen->id) {
        wp_nonce_field('qmi_admin_action', 'qmi_nonce');
    }
}
add_action('admin_footer', 'qmi_add_nonce_field');

// Form processing action
function qmi_process_form_submission() {
    // Check if user is authorized
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'quiz-membership-integrator'));
    }

    // Verify nonce
    check_admin_referer('qmi_admin_action', 'qmi_nonce');

    // Process form data here
    // Add your form processing logic
}
add_action('admin_post_qmi_form_action', 'qmi_process_form_submission');

// Remove Elementor scripts and notices from plugin page
function remove_elementor_scripts_from_my_plugin_page($hook) {
    $screen = get_current_screen();
    if ($screen && 'toplevel_page_quiz-membership-integrator' === $screen->id) {
        wp_deregister_script('elementor-admin');
        wp_deregister_style('elementor-admin');
    }
}
add_action('admin_enqueue_scripts', 'remove_elementor_scripts_from_my_plugin_page', 99);

// Remove admin notices on plugin page
function suppress_other_admin_notices() {
    $screen = get_current_screen();
    if ($screen && 'toplevel_page_quiz-membership-integrator' === $screen->id) {
        remove_all_actions('admin_notices');
    }
}
add_action('admin_notices', 'suppress_other_admin_notices', 1);

// Disable Elementor popups on plugin page
function remove_elementor_popups_from_my_plugin_page() {
    $screen = get_current_screen();
    if ($screen && 'toplevel_page_quiz-membership-integrator' === $screen->id) {
        remove_all_actions('admin_footer');
    }
}
add_action('admin_init', 'remove_elementor_popups_from_my_plugin_page');

// AJAX handler for form submissions
function qmi_handle_ajax_submission() {
    // Verify nonce
    check_ajax_referer('qmi_admin_ajax_nonce', 'nonce');

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }

    // Process AJAX request here
    // Add your AJAX processing logic

    wp_send_json_success('Action completed successfully');
}
add_action('wp_ajax_qmi_ajax_action', 'qmi_handle_ajax_submission');


// Enqueue for logo

// Enqueue admin styles and scripts
function qmi_enqueue_admin_styles($hook) {
    // Only enqueue on the plugin's admin page
    $screen = get_current_screen();
    if (!$screen || 'toplevel_page_quiz-membership-integrator' !== $screen->id) {
        return;
    }

    wp_enqueue_style('qmi-admin-style', plugin_dir_url(__FILE__) . 'admin/css/qmi-admin.css', array(), '1.0.0');

    // Register and enqueue the custom CSS that includes the logo as a background image
    $logo_url = plugin_dir_url(__FILE__) . 'admin/images/qmi-logo.gif';

    $custom_css = "
        .qmi-logo-container {
            background-image: url('" . esc_url($logo_url) . "');
            background-size: contain !important; /* Make sure the logo fits within the container */
            background-repeat: no-repeat !important; /* Prevent repeating */
            background-position: center !important; /* Center the logo */
            width: 150px !important; /* Adjust width */
            height: 150px !important; /* Adjust height */
            display: block !important; /* Display as block */
        }
    ";

    wp_add_inline_style('qmi-admin-style', $custom_css);
}
add_action('admin_enqueue_scripts', 'qmi_enqueue_admin_styles');
