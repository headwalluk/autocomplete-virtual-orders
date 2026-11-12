<?php
/**
 * Autocomplete Virtual Orders: Uninstaller
 *
 * Runs when the plugin is deleted via Plugins → Delete, not on deactivation.
 * The plugin stores no options or order data; this removes the GitHub
 * updater's cached release lookups, including the ones 1.x left.
 *
 * @package Autocomplete_Virtual_Orders
 * @since   1.1.0
 */

namespace Autocomplete_Virtual_Orders;

// Bail out if not invoked by WordPress's uninstall handler.
defined( 'WP_UNINSTALL_PLUGIN' ) || die();

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/includes/class-github-updater.php';

Github_Updater::delete_cache( basename( __DIR__ ) );

foreach ( LEGACY_UPDATER_CACHE_KEYS as $acvo_legacy_cache_key ) {
	delete_transient( $acvo_legacy_cache_key );
}
