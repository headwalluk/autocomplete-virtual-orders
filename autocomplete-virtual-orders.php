<?php
/**
 * Plugin Name:          Autocomplete Virtual Orders
 * Plugin URI:           https://headwall-hosting.com/plugins/autocomplete-virtual-orders/
 * Description:          Automatically completes WooCommerce orders that contain only virtual items (nothing to ship) when they reach Processing.
 * Version:              1.2.0
 * Requires at least:    6.7
 * Requires PHP:         8.2
 * Requires Plugins:     woocommerce
 * Author:               Paul Faulkner
 * Author URI:           https://headwall-hosting.com/a-web-guys-blog/
 * License:              GPLv2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          autocomplete-virtual-orders
 * Domain Path:          /languages
 * WC requires at least: 9.0
 * WC tested up to:      11.3
 *
 * @package Autocomplete_Virtual_Orders
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

define( 'ACVO_VERSION', '1.2.0' );
define( 'ACVO_FILE', __FILE__ );
define( 'ACVO_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACVO_URL', plugin_dir_url( __FILE__ ) );
define( 'ACVO_BASENAME', plugin_basename( __FILE__ ) );

require_once ACVO_PATH . 'constants.php';
require_once ACVO_PATH . 'functions-private.php';
require_once ACVO_PATH . 'includes/class-plugin.php';

// GitHub auto-updates: admin, cron and WP-CLI only.
if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once ACVO_PATH . 'includes/class-github-updater.php';
	new Autocomplete_Virtual_Orders\Github_Updater( ACVO_FILE, Autocomplete_Virtual_Orders\UPDATER_GITHUB_REPO, ACVO_VERSION, Autocomplete_Virtual_Orders\FILTER_UPDATER_ENABLED );
}

/**
 * Initialise the plugin.
 *
 * @since 1.0.0
 */
function acvo_plugin_run(): void {
	global $acvo_plugin;

	// The global predates Plugin::instance() and is kept for code that reads it.
	$acvo_plugin = Autocomplete_Virtual_Orders\Plugin::instance();
	$acvo_plugin->run();
}
acvo_plugin_run();
