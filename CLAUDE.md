# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Autocomplete Virtual Orders** is a tiny, single-purpose WordPress/WooCommerce
plugin. When an order enters the **Processing** status, it checks whether the
order has anything physical to ship. If every line item is virtual, the order is
advanced to **Completed**.

It fills a gap in WooCommerce core: core only auto-completes orders that are
virtual **and** downloadable. Virtual-but-not-downloadable items (bookings,
services, donations, virtual tickets) otherwise sit in Processing forever.

Design philosophy: **does one thing and does it well.** No telemetry, no
phone-home, no upsells, no settings page. Zero configuration. Every decision is
overridable via PHP filters.

- **Namespace:** `Autocomplete_Virtual_Orders`
- **Text Domain:** `autocomplete-virtual-orders`
- **Prefixes:** `acvo` / `ACVO` / `autocomplete_virtual_orders`
- **PHP:** 8.0+ (do NOT use `declare(strict_types=1)` — breaks WordPress/WooCommerce interop)
- **WordPress:** 6.0+, **WooCommerce:** 9.0+
- **No build system** — no npm, no Composer, no bundler.

## Commands

```bash
phpcs                  # Check WordPress coding standards compliance
phpcbf                 # Auto-fix coding standards violations
phpcs includes/        # Check a specific directory
```

Always run `phpcs` before committing. Config is in `phpcs.xml` — WordPress
standards with prefixes `acvo` and `autocomplete_virtual_orders`.

### Pre-Commit Workflow

1. `phpcs` — check for violations
2. `phpcbf` — auto-fix
3. `phpcs` — verify clean
4. Stage and commit

### Commit Message Format

```
type: brief description

- Detail 1
- Detail 2
```

Types: `feat:` `fix:` `chore:` `refactor:` `docs:` `style:` `test:`

## Architecture

### Bootstrap Chain

`autocomplete-virtual-orders.php` defines constants (`ACVO_VERSION`, `ACVO_DIR`,
`ACVO_URL`), declares HPOS compatibility, requires `constants.php`,
`functions-private.php` and `includes/class-plugin.php`, then instantiates the
global `Plugin` and calls `Plugin::run()`, which registers the single hook.

### Core

| File | Role |
|---|---|
| `autocomplete-virtual-orders.php` | Bootstrap — constants, requires, HPOS declaration, `run()` |
| `constants.php` | `DEF_TARGET_STATUS`, `HOOK_TRIGGER` (all magic strings) |
| `functions-private.php` | `Autocomplete_Virtual_Orders\get_plugin()` internal helper |
| `includes/class-plugin.php` | Orchestrator. Registers the hook in `run()`; implements `maybe_complete_virtual_order()` and `order_is_all_virtual()` |

### Behaviour

1. Hook `woocommerce_order_status_processing` (fires on any entry into Processing).
2. `order_is_all_virtual( $order )` — true only if the order has at least one
   line item and every item's product passes `is_virtual()`.
3. If true (and not vetoed by a filter), `update_status( 'completed', $note )`.
4. Skip the transition if the order is already in the target status.

No recursion risk: the trigger is `..._status_processing`; we move the order to
`completed`, which fires `..._status_completed` — a different hook.

## Developer API (Filters)

This plugin has no settings. Its public API is three filters:

```php
// Master veto / override. $should defaults to order_is_all_virtual( $order ).
apply_filters( 'acvo_should_autocomplete_order', bool $should, WC_Order $order );

// Redefine what "all virtual" means (e.g. treat a custom product type as shippable).
apply_filters( 'acvo_order_is_all_virtual', bool $is_all_virtual, WC_Order $order );

// Change the destination status (default 'completed').
apply_filters( 'acvo_target_status', string $status, WC_Order $order );
```

## Key Conventions

- Register all hooks in `Plugin::run()`, implement in the class.
- Use constants from `constants.php` — never hardcode magic status strings.
- **No `declare(strict_types=1)`** — breaks WordPress/WooCommerce interop.
- **Single-Entry Single-Exit (SESE):** prefer one `return` at the end of a function.
- **Boolean coercion:** use `filter_var( $val, FILTER_VALIDATE_BOOLEAN )`.
- **HPOS:** never use `get_post_meta()` for order data — always `WC_Order` methods.
- **i18n:** all user-facing strings (e.g. the order note) wrapped in `__()` with
  the `autocomplete-virtual-orders` text domain.
- Short array syntax `[]` is allowed (configured in `phpcs.xml`).

## Reference Files

- `dev-notes/00-project-tracker.md` — milestones, requirements, roadmap.
