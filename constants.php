<?php
/**
 * Plugin Constants
 *
 * All magic strings live here.
 *
 * @package Autocomplete_Virtual_Orders
 * @since 1.0.0
 */

namespace Autocomplete_Virtual_Orders;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

/**
 * WooCommerce order status we move qualifying orders to.
 *
 * Bare status slug, without the `wc-` prefix.
 */
const DEF_TARGET_STATUS = 'completed';

/**
 * The WooCommerce action that triggers the check.
 *
 * Fires whenever an order enters the Processing status, regardless of the
 * previous status.
 */
const HOOK_TRIGGER = 'woocommerce_order_status_processing';

/**
 * Plugin name, used untranslated as the order-note prefix.
 *
 * @since 1.1.0
 */
const PLUGIN_NAME = 'Autocomplete Virtual Orders';

/**
 * WooCommerce logger source (WooCommerce → Status → Logs).
 *
 * @since 1.1.0
 */
const LOG_SOURCE = 'autocomplete-virtual-orders';

/**
 * GitHub updater: the "owner/repo" that publishes tagged releases, and the
 * filter passed to Github_Updater. Other hooks are named literally at each call,
 * so phpcs can check their prefix.
 *
 * @since 1.0.0
 */
const UPDATER_GITHUB_REPO    = 'headwalluk/autocomplete-virtual-orders';
const FILTER_UPDATER_ENABLED = 'acvo_updater_enabled';

/**
 * Transients the 1.x updater cached its lookups in. uninstall.php still deletes
 * them; the current updater names its cache from the plugin slug.
 *
 * @since 1.2.0
 */
const LEGACY_UPDATER_CACHE_KEYS = array(
	'acvo_latest_release',
	'acvo_latest_release_failed',
);
