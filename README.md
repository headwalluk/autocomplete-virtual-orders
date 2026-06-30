# Autocomplete Virtual Orders

![Version](https://img.shields.io/badge/version-1.0.0-brightgreen.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg?logo=wordpress&logoColor=white)
![WooCommerce](https://img.shields.io/badge/WooCommerce-9.0%2B-96588a.svg?logo=woocommerce&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4.svg?logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-GPLv2%2B-blue.svg)

A tiny WooCommerce plugin that automatically completes orders with **nothing to
ship**. When an order reaches **Processing**, if every item in it is a *virtual*
product, the order is moved straight to **Completed**. Any physical item, and the
order is left alone.

It fills one gap in WooCommerce core, which only auto-completes orders that are
virtual *and* downloadable — so bookings, services, donations, admissions and
other non-downloadable virtual products otherwise sit in Processing forever.

**Who it's for:** store owners selling services, bookings or donations who are
tired of completing orders by hand, and developers who want a small,
dependency-free, filter-driven building block rather than a bloated order-automation
suite.

## Features

- Auto-completes an order the moment it reaches Processing, if all items are virtual.
- Leaves any order with a physical item untouched.
- Zero configuration — works the moment it's activated.
- HPOS (High-Performance Order Storage) compatible.
- Fully customisable in code: three filters and an action, no settings page.
- Self-updating from GitHub releases.
- No telemetry, no phone-home, no upsells, no bloat.

## Documentation

| Guide | For |
| ----- | --- |
| [Installation & usage](docs/installation.md) | Store owners, web developers & designers — installing, activating and verifying it works. |
| [Hooks reference](docs/hooks.md) | Developers — the filters and action for customising behaviour, with examples. |

## Requirements

- WordPress 6.0+
- WooCommerce 9.0+
- PHP 8.0+

## Installation

1. Download `autocomplete-virtual-orders.zip` from the
   [latest release](https://github.com/headwalluk/autocomplete-virtual-orders/releases/latest).
2. In WordPress, go to **Plugins → Add New → Upload Plugin** and upload the zip.
3. Activate **Autocomplete Virtual Orders** in **Plugins**.

WooCommerce must be installed and active first. There's nothing to configure —
see the [Installation & usage guide](docs/installation.md) to verify it's working.

## License

GPLv2 or later — see [LICENSE](LICENSE).
