<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://properprogramming.com
 * @since      1.0.0
 * @author     Micheal Parisi (Proper Programming, LLC)
 * @copyright  2020
 *
 * @package    c_t_c_Change_Case_Data
 * @subpackage c_t_c_Change_Case_Data/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    c_t_c_Change_Case_Data
 * @subpackage c_t_c_Change_Case_Data/includes
 * @author     Michael Parisi <mgparisicpu@gmail.comm>
 */
class c_t_c_Change_Case_Data_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'change-titles-case',
			FALSE,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);

	}


}
