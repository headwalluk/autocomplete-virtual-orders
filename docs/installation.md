# Installation

Everything you need to get **Autocomplete Virtual Orders** running. There is nothing to configure — once it's activated, it works. For what it does to your orders, see [how it works](how-it-works.md).

## Requirements

| Requirement | Minimum |
|-------------|---------|
| WordPress   | 6.7     |
| WooCommerce | 9.0     |
| PHP         | 8.2     |

WooCommerce **High-Performance Order Storage (HPOS)** is fully supported.

## Installing

### Option A — Upload the zip (recommended)

1. Download `autocomplete-virtual-orders.zip` from the [latest GitHub release](https://github.com/headwalluk/autocomplete-virtual-orders/releases/latest).
2. In your WordPress admin, go to **Plugins → Add New → Upload Plugin**.
3. Choose the zip file and click **Install Now**.
4. Click **Activate**.

### Option B — Manual install via SFTP

1. Unzip `autocomplete-virtual-orders.zip`.
2. Upload the `autocomplete-virtual-orders` folder into `wp-content/plugins/` on your server.
3. In WordPress admin, go to **Plugins** and click **Activate** next to *Autocomplete Virtual Orders*.

WooCommerce must be installed and active first. The plugin lists it as a required plugin, so WordPress won't let you activate it otherwise.

## Checking it works

There is no settings page. To confirm it is working:

1. Create a test product and tick **Virtual** in the product data panel.
2. Place a test order for that product and take it through to payment, or set it to **Processing** from the order screen.
3. The order should move straight on to **Completed**, with this note on the order:

   > *Autocomplete Virtual Orders: order completed automatically (all items are virtual — nothing to ship).*

An order that also contains a physical (non-virtual) product stays in **Processing**. If an order doesn't behave as expected, see [troubleshooting](troubleshooting.md).

## Updating

The plugin includes a lightweight GitHub-based updater. When a new release is published, your site sees it on the **Plugins** screen and in **Dashboard → Updates**, just like a wordpress.org plugin — no extra service or account needed.

A developer can pause updates if you need to pin a version; see [hooks and filters](developers/hooks-and-filters.md#acvo_updater_enabled).

## Uninstalling

Deactivate and delete the plugin from the **Plugins** screen. The plugin stores no settings; deleting it removes its cached update check. Orders it has already completed stay completed.
