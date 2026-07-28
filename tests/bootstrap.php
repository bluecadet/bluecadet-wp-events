<?php
/**
 * PHPUnit bootstrap for the Bluecadet Events plugin.
 *
 * Resolves the WordPress test library, loads this plugin as a mu-plugin-ish
 * early require, boots WordPress, then creates the plugin's custom table
 * (register_activation_hook does not fire under the test runner).
 *
 * @package BluecadetEvents
 */

// Composer autoloader (gives us wp-phpunit + the plugin's dev autoload).
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Locate the WordPress PHPUnit test scaffolding. wp-env / wp-scripts set
// WP_TESTS_DIR; otherwise fall back to the wp-phpunit Composer package.
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = getenv( 'WP_PHPUNIT__DIR' );
}

if ( ! $_tests_dir && is_dir( dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit' ) ) {
	$_tests_dir = dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit';
}

if ( ! $_tests_dir || ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	fwrite(
		STDERR,
		"Could not find the WordPress test library.\n" .
		"Set WP_TESTS_DIR or install wp-phpunit/wp-phpunit (composer install).\n"
	);
	exit( 1 );
}

// Give access to tests_add_filter() before WordPress loads.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Load this plugin early so its post types, meta, and hooks register during
 * the normal WordPress boot (before the 'init' action fires).
 */
tests_add_filter(
	'muplugins_loaded',
	static function () {
		require dirname( __DIR__ ) . '/bluecadet-events.php';
	}
);

// Boot WordPress + the test framework.
require $_tests_dir . '/includes/bootstrap.php';

// WordPress is loaded now. Create the plugin's custom table once for the whole
// suite. This is DDL (implicit commit), so it must live here rather than inside
// a test — per-test row writes still roll back with the usual transaction.
\BluecadetEvents\Plugin\Activate::activate();
