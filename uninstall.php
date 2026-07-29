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

require_once __DIR__ . '/vendor/autoload.php';

\BluecadetEvents\Plugin\Uninstall::run();
