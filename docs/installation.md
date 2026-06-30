# Installation & Usage

Everything a site owner, web developer or designer needs to get **Autocomplete
Virtual Orders** running. There is nothing to configure — once activated, it
works.

---

## What it does

When a WooCommerce order moves into the **Processing** status, the plugin looks
at the order's contents:

- If **every** item in the order is a *virtual* product (nothing physical to pick,
  pack or post), the order is moved straight to **Completed**.
- If the order contains **any** physical item, it is left in Processing,
  untouched.

That's the whole job.

### Why you might need it

WooCommerce core already auto-completes an order when *every* item is both
**virtual** *and* **downloadable**. The gap is virtual products that are **not**
downloadable — bookings, services, donations, event admissions, virtual gift
cards, "pay an invoice" products, and so on. Without this plugin, those orders
sit in **Processing** indefinitely and have to be completed by hand.

### Who it's for

- **Store owners** selling services, bookings, donations or any non-shippable
  product who are tired of manually completing orders.
- **Developers** who want a small, dependency-free, filter-driven building block
  rather than a bloated "order automation" suite.

---

## Requirements

| Requirement | Minimum |
|-------------|---------|
| WordPress   | 6.0     |
| WooCommerce | 9.0     |
| PHP         | 8.0     |

WooCommerce **High-Performance Order Storage (HPOS)** is fully supported.

---

## Installing

### Option A — Upload the zip (recommended)

1. Download `autocomplete-virtual-orders.zip` from the
   [latest GitHub release](https://github.com/headwalluk/autocomplete-virtual-orders/releases/latest).
2. In your WordPress admin, go to **Plugins → Add New → Upload Plugin**.
3. Choose the zip file and click **Install Now**.
4. Click **Activate**.

### Option B — Manual install via FTP/SFTP

1. Unzip `autocomplete-virtual-orders.zip`.
2. Upload the `autocomplete-virtual-orders` folder into
   `wp-content/plugins/` on your server.
3. In WordPress admin, go to **Plugins** and click **Activate** next to
   *Autocomplete Virtual Orders*.

WooCommerce must be installed and active first — the plugin lists it as a
required dependency, so WordPress will not let you activate it otherwise.

---

## Activating & verifying

There is no settings page. To confirm it is working:

1. Create a test product and tick **Virtual** in the product data panel.
2. Place a test order for that product and take it through to payment (or mark it
   **Processing** manually from the order screen).
3. The order should immediately advance to **Completed**, and you'll see a note
   on the order:
   > *Autocomplete Virtual Orders: order completed automatically (all items are
   > virtual — nothing to ship).*

If you add a **physical** (non-virtual) product to the same order, it will stay
in **Processing** instead — exactly as intended.

---

## Updating

The plugin includes a lightweight GitHub-based updater. When a new release is
published, your site sees it on the **Plugins** screen and in **Dashboard →
Updates**, just like a wordpress.org plugin — no extra service or account needed.

Updates can be paused with a filter if you need to pin a version (see
[hooks.md](hooks.md) → `acvo_updater_enabled`).

---

## Uninstalling

Deactivate and delete the plugin from the **Plugins** screen. The plugin stores
no options or database tables, so nothing is left behind. Orders already
completed stay completed.

---

## Customising the behaviour

Need to change *when* an order auto-completes, redefine what counts as
"shippable", send to a different status, or run your own code afterwards? All of
that is done in code via filters and actions — see **[hooks.md](hooks.md)**.
