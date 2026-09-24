<?php
/**
 * Private Plugin Functions
 *
 * Internal helpers used within the plugin namespace. Not part of the public API.
 *
 * @package Autocomplete_Virtual_Orders
 * @since 1.0.0
 */

namespace Autocomplete_Virtual_Orders;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

/**
 * Get the plugin instance.
 *
 * @since 1.0.0
 * @return Plugin The plugin instance.
 */
function get_plugin(): Plugin {
	global $acvo_plugin;
	return $acvo_plugin;
}

/**
 * Write an error to the WooCommerce log under LOG_SOURCE.
 *
 * @since 1.1.0
 *
 * @param string $message The message to log.
 */
function log_error( string $message ): void {
	wc_get_logger()->error( $message, array( 'source' => LOG_SOURCE ) );
}
