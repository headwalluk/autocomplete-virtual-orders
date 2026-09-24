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
 * GitHub Updater configuration.
 *
 * UPDATER_GITHUB_REPO is the "owner/repo" that publishes tagged releases with a
 * `{slug}.zip` asset attached. Update this if the repository moves.
 */
const UPDATER_GITHUB_REPO = 'headwalluk/autocomplete-virtual-orders';
const UPDATER_CACHE_KEY   = 'acvo_latest_release';
const UPDATER_CACHE_TTL   = 12 * HOUR_IN_SECONDS;

/**
 * Back-off after a failed release lookup.
 *
 * Shorter than UPDATER_CACHE_TTL so a real release is not missed for long.
 *
 * @since 1.1.0
 */
const UPDATER_FAILURE_CACHE_KEY = 'acvo_latest_release_failed';
const UPDATER_FAILURE_CACHE_TTL = HOUR_IN_SECONDS;

/**
 * Seconds to wait on the GitHub API before giving up.
 *
 * @since 1.1.0
 */
const UPDATER_REQUEST_TIMEOUT = 10;
