# Changelog

All notable changes to **Autocomplete Virtual Orders** are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-09-24

### Fixed

- **A typo in an `acvo_target_status` filter sent paid orders back to Pending payment.** WooCommerce silently replaces an unregistered status with `pending`, so a filter returning, say, `'compelted'` moved every qualifying order to Pending payment. The plugin now checks the value against the registered order statuses and falls back to `completed`, writing an error to the WooCommerce log (source `autocomplete-virtual-orders`). A non-string value falls back the same way, and a `wc-` prefix is now accepted. Introduced in 1.0.0.
- **Another plugin firing `woocommerce_order_status_processing` with a string order ID caused a fatal `TypeError`.** The callback now accepts any value and acts only on a numeric ID or a `WC_Order`. Introduced in 1.0.0.
- **The GitHub updater retried a failing lookup on every update check,** blocking the admin page for up to 10 seconds each time while GitHub was unreachable or rate-limiting. A failure is now cached for an hour.
- **`wp plugin list` and `wp plugin update` never saw a new release.** The updater loaded only in admin and cron requests, so WP-CLI reported no update. It now loads under WP-CLI too. Introduced in 1.0.0.
- **Translations never loaded on WordPress 6.0–6.6.** The plugin relied on WordPress 6.7's automatic loading of its `languages/` folder, so on older versions the order note was always in English. The minimum WordPress version is now 6.7 (see Changed). Introduced in 1.0.0.
- **The plugin name was translated, and badly.** German, French and Dutch rendered "Autocomplete" as text autocomplete (as in a search box), and the French order note said the order was *traitée* (processed) rather than *terminée*, WooCommerce's own label for Completed. All translations are regenerated: the plugin name, description and URIs now stay in English, and the order note carries fuller context for the translator.
- The updater could offer an unrelated release asset whose name merely started with the plugin slug. The versioned-zip fallback now requires `<slug>-<version>.zip`.

### Changed

- **Minimum PHP is now 8.2.** PHP 8.0 and 8.1 are end-of-life.
- **Minimum WordPress is now 6.7**, the first version that loads a plugin's bundled translations from its `Domain Path` header.
- Filter results for `acvo_should_autocomplete_order`, `acvo_order_is_all_virtual` and `acvo_updater_enabled` are read as booleans with `filter_var()`, so a callback returning `'no'`, `'off'` or `'false'` now counts as false.
- The GitHub updater loads on admin, cron and WP-CLI requests, not on front-end page loads, and compares releases against the plugin header version. It logs an error if the header and `ACVO_VERSION` disagree.
- The release workflow refuses to build unless the plugin header, `ACVO_VERSION` and the `readme.txt` stable tag all match the git tag.
- Documentation restructured by audience: `docs/installation.md`, `docs/how-it-works.md` and `docs/troubleshooting.md` for store owners; `docs/developers/hooks-and-filters.md` and `docs/developers/extending.md` for developers. `docs/hooks.md` is replaced by `docs/developers/hooks-and-filters.md`.
- Tested up to WooCommerce 11.1.

### Added

- `readme.txt`, `SECURITY.md` and `uninstall.php`, which removes the updater's cached release lookups when the plugin is deleted.

### Removed

- **The `ACVO_DIR` constant is renamed to `ACVO_PATH`.** Code outside the plugin that used `ACVO_DIR` must switch to `ACVO_PATH`.

## [1.0.0] - 2026-06-30

First public release.

### Added
- Auto-complete WooCommerce orders that contain only virtual items when they
  enter the **Processing** status.
- All-virtual detection over every order line item (`is_virtual()`); orders with
  any physical item, and empty orders, are left untouched.
- Order note recorded on each auto-completion.
- Filter `acvo_should_autocomplete_order` — master veto/override of the decision.
- Filter `acvo_order_is_all_virtual` — redefine what counts as "nothing to ship".
- Filter `acvo_target_status` — change the destination status (default `completed`).
- Action `acvo_order_autocompleted` — fires after an order is auto-completed.
- WooCommerce High-Performance Order Storage (HPOS) compatibility declaration.
- In-plugin GitHub release updater with the `acvo_updater_enabled` filter, plus a
  tag-triggered GitHub Actions release workflow.
- Internationalisation: `.pot` plus en_GB, de_DE, es_ES, fr_FR, it_IT and nl_NL
  translations.
- Documentation: `docs/installation.md` and `docs/hooks.md`.

[Unreleased]: https://github.com/headwalluk/autocomplete-virtual-orders/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/headwalluk/autocomplete-virtual-orders/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/headwalluk/autocomplete-virtual-orders/releases/tag/v1.0.0
