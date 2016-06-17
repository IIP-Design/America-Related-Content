<?php

/**
	* Define the internationalization functionality
	*
	* Loads and defines the internationalization files for this plugin
	* so that it is ready for translation.
	*
	* @link       https://github.com/IIP-Design
	* @since      1.0.0
	*
	* @package    America_Related_Content
	* @subpackage America_Related_Content/includes
	*/




/**
	* Define the internationalization functionality.
	*
	* Loads and defines the internationalization files for this plugin
	* so that it is ready for translation.
	*
	* @since      1.0.0
	* @package    America_Related_Content
	* @subpackage America_Related_Content/includes
	* @author     Office of Design, U.S. Department of State <kleekampnf@america.gov>
	*/

class America_Related_Content_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */

	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'america-related-content', false, AMERICA_RELATED_CONTENT_DIR . '/languages/' );
	}
}
