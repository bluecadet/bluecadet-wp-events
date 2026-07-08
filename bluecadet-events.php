<?php

/**
 * @package BluecadetEvents
 *
 * @wordpress-plugin
 * Plugin Name:       Bluecadet Events
 * Description:       Events
 * Version:           1.0.0
 * Author:            Bluecadet
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

include_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );


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
