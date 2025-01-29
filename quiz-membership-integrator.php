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
require_once plugin_dir_path( __FILE__ ) . 'admin/qmi-admin-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/qmi-functions.php';

// Enqueue admin scripts and styles
function qmi_enqueue_admin_scripts() {
    wp_enqueue_script( 'qmi-admin-settings-js', plugin_dir_url( __FILE__ ) . 'admin/js/qmi-admin-settings.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_style( 'qmi-admin-css', plugin_dir_url( __FILE__ ) . 'admin/css/qmi-admin.css', array(), '1.0.0' );
    
    // Enqueue local Font Awesome stylesheet
    wp_enqueue_style( 'qmi-font-awesome', plugin_dir_url( __FILE__ ) . 'admin/css/all.min.css', array(), '6.0.0' );
}
add_action( 'admin_enqueue_scripts', 'qmi_enqueue_admin_scripts' );

// Validate nonce before processing any form submissions
function qmi_validate_nonce() {
    if ( isset( $_POST['qmi_nonce'] ) ) {
        $nonce = sanitize_text_field( wp_unslash( $_POST['qmi_nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'qmi_admin_action' ) ) {
            wp_die( esc_html__( 'Security check failed.', 'quiz-membership-integrator' ) );
        }
    }
}
add_action( 'admin_init', 'qmi_validate_nonce' );

// Add nonce field to admin pages where forms exist
function qmi_add_nonce_field() {
    echo '<input type="hidden" name="qmi_nonce" value="' . esc_attr( wp_create_nonce( 'qmi_admin_action' ) ) . '" />';
}
add_action( 'admin_footer', 'qmi_add_nonce_field' );

// Form processing action
function qmi_process_form_submission() {

    if ( isset( $_POST['qmi_nonce'] ) ) {
        $nonce = sanitize_text_field( wp_unslash( $_POST['qmi_nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'qmi_admin_action' ) ) {
            wp_die( esc_html__( 'Security check failed.', 'quiz-membership-integrator' ) );
        }

    }
}
add_action( 'admin_post_qmi_form_action', 'qmi_process_form_submission' );

// Remove Elementor scripts and notices from my plugin page
function remove_elementor_scripts_from_my_plugin_page($hook) {
    if ( isset( $_GET['page'] ) ) {
        $page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
        if ( $page === 'quiz-membership-integrator' ) {
            wp_deregister_script('elementor-admin');
            wp_deregister_style('elementor-admin');
        }
    }
}
add_action('admin_enqueue_scripts', 'remove_elementor_scripts_from_my_plugin_page', 99);

// Remove all admin notices on my plugin page
function suppress_other_admin_notices() {
    if ( isset( $_GET['page'] ) ) {
        $page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
        if ( $page === 'quiz-membership-integrator' ) {
            remove_all_actions('admin_notices');
        }
    }
}
add_action('admin_notices', 'suppress_other_admin_notices', 1);

// Disable Elementor popups or footer hooks on my plugin page
function remove_elementor_popups_from_my_plugin_page() {
    if ( isset( $_GET['page'] ) ) {
        $page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
        if ( $page === 'quiz-membership-integrator' ) {
            remove_all_actions('admin_footer');
        }
    }
}
add_action('admin_init', 'remove_elementor_popups_from_my_plugin_page');
