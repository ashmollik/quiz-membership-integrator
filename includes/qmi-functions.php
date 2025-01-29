<?php
/**
 * Functions for Quiz Membership Integrator
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Upgrade user's membership level based on quiz score.
 */
function quiz_upgrade_membership_level_on_score($final_score) {
    // Check if Paid Memberships Pro is active
    if ( class_exists( 'PMPro_Membership_Level' ) ) {
        // Get current user ID
        $user_id = get_current_user_id();

        // Capability check: Ensure the user is logged in
        if ( $user_id && current_user_can( 'read' ) ) {
            // Sanitize final score
            $final_score = intval( $final_score );

            // Get current user's membership levels
            $current_levels = pmpro_getMembershipLevelsForUser( $user_id );

            // Get settings
            $options = get_option( 'qmi_settings' );
            $conditions = isset( $options['qmi_conditions'] ) ? $options['qmi_conditions'] : array();

            // Find the highest membership level user already has
            $current_highest_level = get_current_highest_membership_level( $current_levels );

            // Flag to track if user's membership level needs to be upgraded
            $upgrade_needed = false;

            foreach ( $conditions as $condition ) {
                $min_score = isset( $condition['min_score'] ) ? intval( $condition['min_score'] ) : 0;
                $max_score = isset( $condition['max_score'] ) ? intval( $condition['max_score'] ) : 0;
                $membership_level = isset( $condition['membership_level'] ) ? intval( $condition['membership_level'] ) : 0;
                $active = isset( $condition['active'] ) ? intval( $condition['active'] ) : 0;

                // Check if condition is active
                if ( $active ) {
                    // Check if final score falls within current condition range
                    if ( $final_score >= $min_score && $final_score <= $max_score ) {
                        // Ensure it's an upgrade (not a downgrade or no change)
                        if ( $current_highest_level < $membership_level ) {
                            // User needs to be upgraded
                            $upgrade_needed = true;
                        }
                        break; // Exit loop once the condition is met
                    }
                }
            }

            // If upgrade is needed, adjust user's membership levels
            if ( $upgrade_needed ) {
                // Upgrade user to the appropriate membership level
                pmpro_changeMembershipLevel( $membership_level, $user_id );
            }
        }
    }
}

/**
 * Get the highest membership level the user currently has.
 *
 * @param array $current_levels Array of current membership levels for the user.
 * @return int Highest membership level ID or 0 if none found.
 */
function get_current_highest_membership_level( $current_levels ) {
    $highest_level = 0;
    foreach ( $current_levels as $level ) {
        if ( $level->id > $highest_level ) {
            $highest_level = $level->id;
        }
    }
    return $highest_level;
}

/**
 * Hook into the point where the quiz results are processed and the final score is available.
 */
function quiz_membership_integration_hook($integrations_data, $integration_options) {
    // Check if final score is set and sanitize it
    if ( isset( $integration_options['ays_quiz_final_score'] ) ) {
        $final_score = intval( $integration_options['ays_quiz_final_score'] );
        quiz_upgrade_membership_level_on_score( $final_score );
    }
}

// Hook into the appropriate action
add_action('ays_qm_front_end_integrations', 'quiz_membership_integration_hook', 10, 2);
?>
