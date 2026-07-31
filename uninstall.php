<?php
/**
 * Runs when the plugin is deleted from the WordPress admin.
 *
 * Removes plugin data only if the user opted in (deactivate modal / settings),
 * with a developer filter override. Deactivation never deletes data — this is
 * the only place removal can happen.
 *
 * @package BluecadetEvents
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Load the plugin's own autoloader if present (standalone install); when
// installed as a Composer dependency the host project's autoloader already
// registers these classes.
$bc_events_autoload = __DIR__ . '/vendor/autoload.php';
if ( is_readable( $bc_events_autoload ) ) {
	require_once $bc_events_autoload;
}

if ( class_exists( \BluecadetEvents\Plugin\Uninstall::class ) ) {
	\BluecadetEvents\Plugin\Uninstall::run();
}
