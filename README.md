# Autocomplete Virtual Orders

[![Version](https://img.shields.io/github/v/release/headwalluk/autocomplete-virtual-orders?label=version&color=blue)](https://github.com/headwalluk/autocomplete-virtual-orders/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://www.php.net/)
[![WordPress](https://img.shields.io/badge/WordPress-6.7+-21759B.svg)](https://wordpress.org/)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-9.0+-96588A.svg)](https://woocommerce.com/)
[![License](https://img.shields.io/badge/license-GPL--2.0+-green.svg)](LICENSE)
[![Coding Standards](https://img.shields.io/badge/WordPress-Coding%20Standards-blue.svg)](https://github.com/WordPress/WordPress-Coding-Standards)

Automatically completes WooCommerce orders that contain only virtual items (nothing to ship) when they reach Processing.

WooCommerce core only auto-completes orders that are virtual *and* downloadable, so bookings, services, donations, admissions and other non-downloadable virtual products otherwise sit in Processing until someone completes them by hand.

**Who it's for:** store owners selling services, bookings or donations who are tired of completing orders by hand, and developers who want a small, dependency-free, filter-driven building block rather than an order-automation suite.

## What it does

- Completes an order the moment it reaches Processing, if every item is virtual
- Leaves any order with a physical item untouched
- Records an order note on every order it completes
- Zero configuration — works the moment it's activated
- HPOS (High-Performance Order Storage) compatible
- Customisable in code: four filters and an action, no settings page
- Self-updating from GitHub releases
- No telemetry, no phone-home, no upsells

## Install

1. Download `autocomplete-virtual-orders.zip` from the [latest release](https://github.com/headwalluk/autocomplete-virtual-orders/releases/latest)
2. WordPress admin → Plugins → Add New → Upload Plugin → choose the zip → Install Now → Activate

After install, the plugin receives future updates automatically via the bundled GitHub updater.

### Requirements

- WordPress 6.7 or later
- WooCommerce 9.0 or later
- PHP 8.2 or later

## Documentation

Full user and developer documentation lives in [`docs/`](docs/):

- [Installation](docs/installation.md)
- [How it works](docs/how-it-works.md)
- [Troubleshooting](docs/troubleshooting.md)
- [Hooks and filters](docs/developers/hooks-and-filters.md) *(for developers)*
- [Extending Autocomplete Virtual Orders](docs/developers/extending.md) *(for developers)*

## Security disclosures

See [SECURITY.md](SECURITY.md) for the responsible-disclosure process.

## License

GPL v2 or later. See [LICENSE](LICENSE).
