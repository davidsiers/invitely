<?php
/*
 * Plugin Name: Invitely
 * Plugin URI: https://tucsonbaptist.com
 * Version: 1.0.0
 * Description: A simple, useful and beautifully invitation system to help churches get visitors.
 * Author: David Siers
 * Author URI: https://siers.us
 * License: GPLv2 or later
 */

invitely_include('assets/includes/acf-initialize.php');
invitely_include('assets/includes/custom-post-type-templates.php');
invitely_include('assets/includes/shortcode.php');
invitely_include('assets/includes/assets.php');
invitely_include('assets/includes/register-custom-post-types.php');
invitely_include('assets/includes/rest-api.php');
invitely_include('assets/includes/push-notifications.php'); 

//Only to be done on plugin activation to rewrite permalinks
register_activation_hook( __FILE__, 'invitely_rewrite_flush' );
function invitely_rewrite_flush() {
    invitely_plugin_init();
    flush_rewrite_rules();
}

function invitely_get_path( $filename = '' ) {
	return plugin_dir_path( __FILE__ ) . ltrim($filename, '/');
}

function invitely_get_url( $filename = '' ) {
	return plugin_dir_url( __FILE__ ) . ltrim($filename, '/');
}

function invitely_include( $filename = '' ) {
	$file_path = invitely_get_path($filename);
	if( file_exists($file_path) ) {
		include_once($file_path);
	} 
}

?>