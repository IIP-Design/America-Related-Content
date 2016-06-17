<?php

/**********************************************************************************************************
Plugin Name:    America Related Content
Description:    A simple plugin that loads content from within the same taxonomy as a post or page
Version:        1.0.0
Author:         Office of Design, U.S. Department of State
License:        MIT
Text Domain:    america
Domain Path:    /languages/
************************************************************************************************************/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// Constants
define( 'AMERICA_RELATED_CONTENT_DIR', plugin_dir_path( dirname( __FILE__ ) ) . 'america-related-content/' );



function activate_america_related_content() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-america-related-content-activator.php';
	America_Related_Content_Activator::activate();
}


function deactivate_america_related_content() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-america-related-content-deactivator.php';
	America_Related_Content_Deactivator::deactivate();
}


register_activation_hook( __FILE__, 'activate_america_related_content' );
register_deactivation_hook( __FILE__, 'deactivate_america_related_content' );


require plugin_dir_path( __FILE__ ) . 'includes/class-america-related-content.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_america_related_content() {

	$plugin = new America_Related_Content();
	$plugin->run();

}
run_america_related_content();
