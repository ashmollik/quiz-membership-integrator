<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * This file is used to clean up any plugin-specific data or settings when the plugin is deleted.
 *
 * @package    Quiz_Membership_Integrator
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete options (example)
delete_option( 'quiz_membership_integrator_option' );
