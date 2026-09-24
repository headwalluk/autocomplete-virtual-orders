<?php
/**
 * Autocomplete Virtual Orders — Uninstaller
 *
 * Runs when the plugin is deleted via Plugins → Delete, not on deactivation.
 * The plugin stores no options or order data; this removes the GitHub
 * updater's cached release lookups.
 *
 * @package Autocomplete_Virtual_Orders
 * @since   1.1.0
 */

// Bail out if not invoked by WordPress's uninstall handler.
defined( 'WP_UNINSTALL_PLUGIN' ) || die();

require_once __DIR__ . '/constants.php';

delete_transient( Autocomplete_Virtual_Orders\UPDATER_CACHE_KEY );
delete_transient( Autocomplete_Virtual_Orders\UPDATER_FAILURE_CACHE_KEY );
