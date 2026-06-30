<?php
/**
 * Private Plugin Functions
 *
 * Internal helpers used within the plugin namespace. Not part of the public API.
 *
 * @package AutocompleteVirtualOrders
 * @since 0.1.0
 */

namespace Autocomplete_Virtual_Orders;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

/**
 * Get the plugin instance.
 *
 * @since 0.1.0
 * @return Plugin The plugin instance.
 */
function get_plugin(): Plugin {
	global $acvo_plugin;
	return $acvo_plugin;
}
