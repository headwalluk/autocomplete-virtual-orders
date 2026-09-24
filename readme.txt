=== Autocomplete Virtual Orders ===
Contributors: headwalluk
Tags: woocommerce, orders, virtual, autocomplete, order status
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 8.2
Requires Plugins: woocommerce
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically completes WooCommerce orders that contain only virtual items (nothing to ship) when they reach Processing.

== Description ==

WooCommerce only auto-completes an order when every item is both virtual **and** downloadable. Bookings, services, donations, event admissions and other virtual products that are not downloadable leave the order sitting in Processing until someone completes it by hand.

Autocomplete Virtual Orders closes that gap. When an order enters Processing, it checks every line item. If all of them are virtual, the order moves straight to Completed and an order note records why. An order with any physical item is left alone.

There is no settings page and nothing to configure. Every decision can be changed in code through filters.

Distributed via GitHub releases with in-plugin auto-updates from [headwalluk/autocomplete-virtual-orders](https://github.com/headwalluk/autocomplete-virtual-orders). The plugin is **not** listed on wordpress.org — install via the GitHub release zip.

**Full documentation lives in the [GitHub repository](https://github.com/headwalluk/autocomplete-virtual-orders)** — see the [`docs/`](https://github.com/headwalluk/autocomplete-virtual-orders/tree/main/docs) directory for installation, how it works, troubleshooting, and the developer hook reference.

== Installation ==

1. Download `autocomplete-virtual-orders.zip` from the [latest GitHub release](https://github.com/headwalluk/autocomplete-virtual-orders/releases/latest)
2. WordPress admin → Plugins → Add New → Upload Plugin → choose the zip → Install Now → Activate

WooCommerce must be installed and active first. The plugin will receive future updates automatically via its bundled GitHub updater.

== Frequently Asked Questions ==

= Is there a settings page? =

No. The plugin works as soon as it is activated. Developers can change which orders complete, what counts as virtual, and the destination status with filters — see [`docs/developers/hooks-and-filters.md`](https://github.com/headwalluk/autocomplete-virtual-orders/blob/main/docs/developers/hooks-and-filters.md) on GitHub.

= What happens to an order with both virtual and physical items? =

It stays in Processing, exactly as it would without the plugin.

= Does it support High-Performance Order Storage (HPOS)? =

Yes.

= Where's the full documentation? =

On GitHub: [headwalluk/autocomplete-virtual-orders](https://github.com/headwalluk/autocomplete-virtual-orders). The [`docs/`](https://github.com/headwalluk/autocomplete-virtual-orders/tree/main/docs) directory covers installation, how it works, troubleshooting, and the developer hook reference.

= How do I report a security issue? =

See [`SECURITY.md`](https://github.com/headwalluk/autocomplete-virtual-orders/blob/main/SECURITY.md) in the repository for the responsible-disclosure process.

== Changelog ==

= 1.0.0 =
Initial public release. See [CHANGELOG.md](https://github.com/headwalluk/autocomplete-virtual-orders/blob/main/CHANGELOG.md) on GitHub.

== Upgrade Notice ==

= 1.0.0 =
Initial public release.

== Privacy Policy ==

Autocomplete Virtual Orders stores no personal data. It adds an order note when it completes an order.

No data is sent to external services. The in-plugin GitHub updater polls `api.github.com/repos/headwalluk/autocomplete-virtual-orders/releases/latest` on a 12-hour cache to check for updates; disable it via the `acvo_updater_enabled` filter if needed.
