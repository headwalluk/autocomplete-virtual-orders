# Changelog

All notable changes to **Autocomplete Virtual Orders** are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-06-30

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
- Documentation: `docs/installation.md` and `docs/hooks.md`.

[Unreleased]: https://github.com/headwalluk/autocomplete-virtual-orders/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/headwalluk/autocomplete-virtual-orders/releases/tag/v0.1.0
