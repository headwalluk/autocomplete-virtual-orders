<?php
/**
 * Plugin Constants
 *
 * All magic strings live here.
 *
 * @package AutocompleteVirtualOrders
 * @since 0.1.0
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
 * GitHub Updater configuration.
 *
 * UPDATER_GITHUB_REPO is the "owner/repo" that publishes tagged releases with a
 * `{slug}.zip` asset attached. Update this if the repository moves.
 */
const UPDATER_GITHUB_REPO = 'headwalluk/autocomplete-virtual-orders';
const UPDATER_CACHE_KEY   = 'acvo_latest_release';
const UPDATER_CACHE_TTL   = 12 * HOUR_IN_SECONDS;
