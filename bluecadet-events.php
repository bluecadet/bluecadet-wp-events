<?php

/**
 * @package BluecadetEvents
 *
 * @wordpress-plugin
 * Plugin Name:       Bluecadet Events
 * Description:       Events
 * Version:           1.0.2
 * Author:            Bluecadet
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Load the plugin's own Composer autoloader when it exists (standalone install).
// When this plugin is installed AS a Composer dependency, its deps live in the
// host project's vendor/ and that autoloader already registers these classes,
// so there is no vendor/autoload.php here — including it unconditionally would
// emit warnings ("unexpected output during activation").
$bc_events_autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
if ( is_readable( $bc_events_autoload ) ) {
	require_once $bc_events_autoload;
}


/**
 * The code that runs during plugin activation.
 */
register_activation_hook( __FILE__, function() {
  BluecadetEvents\Plugin\Activate::activate();
} );


/**
 * Fire in the hole
 *
 */
(new BluecadetEvents\Plugin\Init)->init();


/**
 * Global functions
 *
 */
include_once plugin_dir_path( __FILE__ ) . 'global_functions/helpers.php';
