<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://facebook.com/AshiqurRahmanMollik/
 * @since      1.0.0
 *
 * @package    Quiz_Membership_Integrator
 * @subpackage Quiz_Membership_Integrator/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Quiz_Membership_Integrator
 * @subpackage Quiz_Membership_Integrator/includes
 * @author     Ashiqur Rahman <ashmollikbd@gmail.com>
 */
class Quiz_Membership_Integrator_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'quiz-membership-integrator',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
