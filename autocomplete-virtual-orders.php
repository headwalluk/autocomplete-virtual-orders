<?php
/**
 * Plugin Name:          Autocomplete Virtual Orders
 * Plugin URI:           https://headwall-hosting.com/
 * Description:          Automatically completes WooCommerce orders that contain only virtual items (nothing to ship) when they reach Processing.
 * Version:              0.1.0
 * Requires at least:    6.0
 * Requires PHP:         8.0
 * Requires Plugins:     woocommerce
 * Author:               Paul Faulkner
 * Author URI:           https://headwall-hosting.com/
 * License:              GPLv2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          autocomplete-virtual-orders
 * Domain Path:          /languages
 * WC requires at least: 9.0
 * WC tested up to:      10.7
 *
 * @package AutocompleteVirtualOrders
 */

defined( 'ABSPATH' ) || die();

const ACVO_VERSION = '0.1.0';

define( 'ACVO_FILE', __FILE__ );
define( 'ACVO_BASENAME', plugin_basename( __FILE__ ) );
define( 'ACVO_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACVO_URL', plugin_dir_url( __FILE__ ) );

// Load constants, helpers and the plugin classes.
require_once ACVO_DIR . 'constants.php';
require_once ACVO_DIR . 'functions-private.php';
require_once ACVO_DIR . 'includes/class-github-updater.php';
require_once ACVO_DIR . 'includes/class-plugin.php';

/**
 * Declare that we are ready for WooCommerce HPOS (custom order tables).
 *
 * @see https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * Launch the plugin core.
 */
function acvo_plugin_run() {
	global $acvo_plugin;

	$acvo_plugin = new \Autocomplete_Virtual_Orders\Plugin();
	$acvo_plugin->run();
}
acvo_plugin_run();
