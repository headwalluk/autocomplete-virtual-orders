<?php
/**
 * Plugin Name:          Autocomplete Virtual Orders
 * Plugin URI:           https://github.com/headwalluk/autocomplete-virtual-orders
 * Description:          Automatically completes WooCommerce orders that contain only virtual items (nothing to ship) when they reach Processing.
 * Version:              1.1.0
 * Requires at least:    6.7
 * Requires PHP:         8.2
 * Requires Plugins:     woocommerce
 * Author:               Paul Faulkner
 * Author URI:           https://headwall-hosting.com/
 * License:              GPLv2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          autocomplete-virtual-orders
 * Domain Path:          /languages
 * WC requires at least: 9.0
 * WC tested up to:      11.1
 *
 * @package Autocomplete_Virtual_Orders
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

define( 'ACVO_VERSION', '1.1.0' );
define( 'ACVO_FILE', __FILE__ );
define( 'ACVO_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACVO_URL', plugin_dir_url( __FILE__ ) );
define( 'ACVO_BASENAME', plugin_basename( __FILE__ ) );

require_once ACVO_PATH . 'constants.php';
require_once ACVO_PATH . 'functions-private.php';
require_once ACVO_PATH . 'includes/class-plugin.php';

// GitHub auto-updates (admin, cron and WP-CLI only — no need to load on front-end requests).
if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once ACVO_PATH . 'includes/class-github-updater.php';
	new Autocomplete_Virtual_Orders\Github_Updater();
}

/**
 * Declare compatibility with WooCommerce High-Performance Order Storage.
 *
 * @since 1.0.0
 */
function acvo_declare_hpos_compatibility(): void {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', ACVO_FILE, true );
	}
}
add_action( 'before_woocommerce_init', 'acvo_declare_hpos_compatibility' );

/**
 * Initialise the plugin.
 *
 * @since 1.0.0
 */
function acvo_plugin_run(): void {
	global $acvo_plugin;

	$acvo_plugin = new Autocomplete_Virtual_Orders\Plugin();
	$acvo_plugin->run();
}
acvo_plugin_run();
