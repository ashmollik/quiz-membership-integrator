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
    
    // Enqueue Font Awesome stylesheet
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css' );
}

add_action( 'admin_enqueue_scripts', 'qmi_enqueue_admin_scripts' );



// 
// 
// 


// Remove Elementor scripts and notices from my plugin page
add_action('admin_enqueue_scripts', 'remove_elementor_scripts_from_my_plugin_page', 99);
function remove_elementor_scripts_from_my_plugin_page($hook) {
    // Target only your plugin's admin page
    if (isset($_GET['page']) && $_GET['page'] === 'quiz-membership-integrator') {
        // Deregister Elementor admin scripts or styles (adjust handles if needed)
        wp_deregister_script('elementor-admin');
        wp_deregister_style('elementor-admin');
    }
}

// Remove all admin notices on my plugin page
add_action('admin_notices', 'suppress_other_admin_notices', 1);
function suppress_other_admin_notices() {
    if (isset($_GET['page']) && $_GET['page'] === 'quiz-membership-integrator') {
        remove_all_actions('admin_notices');
    }
}

// Disable Elementor popups or footer hooks on my plugin page
add_action('admin_init', 'remove_elementor_popups_from_my_plugin_page');
function remove_elementor_popups_from_my_plugin_page() {
    if (isset($_GET['page']) && $_GET['page'] === 'quiz-membership-integrator') {
        // Remove Elementor popups or any other footer hooks
        remove_all_actions('admin_footer');
    }
}
