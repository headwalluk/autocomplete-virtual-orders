# Autocomplete Virtual Orders - Project Tracker

**Version:** 0.1.0
**Last Updated:** 30 June 2026
**Current Phase:** Milestone 2 (Documentation & Packaging)
**Overall Progress:** 100% (v0.1.0 feature-complete)

---

## Overview

**Autocomplete Virtual Orders** is a tiny, single-purpose WordPress/WooCommerce
plugin. When an order enters the **Processing** status, the plugin checks whether
the order contains anything physical to ship. If every line item is virtual (so
there is nothing to pick, pack or post), the order is advanced to **Completed**.

The plugin fills a specific gap in WooCommerce core: core auto-completes orders
only when every item is virtual **and** downloadable. A virtual item that is not
downloadable (a booking, a service, a donation, a virtual ticket) leaves the
order stuck in Processing forever. This plugin completes those orders too.

Design philosophy: **does one thing and does it well.** No telemetry, no
phone-home, no upsells, no settings bloat. Zero configuration — it works on
activation. Every decision is overridable by developers through PHP filters.

- **Namespace:** `Autocomplete_Virtual_Orders`
- **Text Domain:** `autocomplete-virtual-orders`
- **Prefixes:** `acvo_` / `ACVO_`
- **PHP:** 8.0+ | **WordPress:** 6.0+ | **WooCommerce:** 9.0+
- **No build system** — plain PHP, no npm/Composer.

---

## Requirements (pinned 30 June 2026)

| # | Decision | Choice |
|---|----------|--------|
| 1 | What counts as "no physical items"? | Every line item passes `is_virtual()`. A single physical item leaves the order in Processing. |
| 2 | Configuration surface | Zero-config. Behaviour overridable via PHP filters only. No settings page, no stored options. |
| 3 | Trigger | `woocommerce_order_status_processing` — fires on **any** entry into Processing, regardless of previous status. |
| 4 | "Can be fulfilled" gating | None beyond all-virtual + paid (Processing implies payment). No stock/backorder/hold/opt-out checks. |
| 5 | Mixed orders | Left in Processing (physical item present). |
| 6 | Empty orders (no line items) | Left alone — nothing to fulfil, so not auto-completed. |

---

## Active TODO Items

- [ ] **Confirm `UPDATER_GITHUB_REPO`** in `constants.php` matches the real repo
      (currently `headwalluk/autocomplete-virtual-orders`). The updater and the
      release workflow both assume releases ship a `autocomplete-virtual-orders.zip`
      asset on tags matching `v*.*.*`.
- [ ] Initial git commit + first `v0.1.0` tag to exercise the release workflow.
- [ ] Decide whether to publish to WP.org (would need WP.org `readme.txt` + assets).

---

## Milestones

### Milestone 1: Core Plugin

**Status:** Complete ✅
**Priority:** High
**Started:** 30 June 2026
**Completed:** 30 June 2026

**Goal:** A working, phpcs-clean plugin that auto-completes all-virtual orders on
entry to Processing, with developer filters and HPOS compatibility.

#### Implementation Checklist

**Project Setup**
- [x] Pin requirements with Paul
- [x] `dev-notes/00-project-tracker.md`
- [x] `CLAUDE.md`
- [x] `phpcs.xml` (WordPress standard, prefixes `acvo` / `autocomplete_virtual_orders`)
- [x] `.gitignore`

**Core Files (review checkpoint — approved)**
- [x] `autocomplete-virtual-orders.php` — plugin header, constants, requires, HPOS decl, `run()`
- [x] `constants.php` — `DEF_TARGET_STATUS`, `HOOK_TRIGGER`
- [x] `functions-private.php` — `Autocomplete_Virtual_Orders\get_plugin()`
- [x] `includes/class-plugin.php` — orchestrator + `maybe_complete_virtual_order()` + `order_is_all_virtual()`

**Behaviour**
- [x] Hook `woocommerce_order_status_processing` (2 args)
- [x] All-virtual check over `$order->get_items()` using `is_virtual()`
- [x] Guard empty orders (return false)
- [x] `update_status( 'completed', ... )` with a clear order note
- [x] Skip if already in target status (no redundant transition)
- [x] Filters: `acvo_should_autocomplete_order`, `acvo_order_is_all_virtual`, `acvo_target_status`
- [x] Action: `acvo_order_autocompleted` fired after completion
- [x] HPOS (`custom_order_tables`) compatibility declaration

**Quality**
- [x] `phpcs` clean (5/5 files)
- [x] `php -l` clean (all files)
- [x] Manual test: all-virtual order → Completed (Paul, 30 June 2026)
- [ ] Manual test: mixed order → stays Processing
- [ ] Manual test: all-physical order → stays Processing
- [ ] Manual test: filter override forces/blocks completion
- [ ] Initial git commit

### Milestone 2: Documentation & Packaging

**Status:** Complete ✅
**Priority:** Medium
**Completed:** 30 June 2026

- [x] `README.md` (GitHub-facing, badges + summary + audience)
- [x] `LICENSE` (GPLv2)
- [x] `languages/autocomplete-virtual-orders.pot` (one runtime string + header metadata)
- [x] `CHANGELOG.md` (Keep a Changelog format, 0.1.0)
- [x] `docs/installation.md` (site owners / devs / designers)
- [x] `docs/hooks.md` (filters & actions reference)
- [ ] `readme.txt` (WP.org-style) — deferred unless we publish to WP.org

### Milestone 3: GitHub Distribution

**Status:** Complete ✅
**Priority:** Medium
**Completed:** 30 June 2026

- [x] `includes/class-github-updater.php` — refactored from Quick 2FA to `Autocomplete_Virtual_Orders`
- [x] Updater constants in `constants.php` (`UPDATER_GITHUB_REPO`, `UPDATER_CACHE_KEY`, `UPDATER_CACHE_TTL`)
- [x] `ACVO_BASENAME` defined; updater required + instantiated in `Plugin::run()`
- [x] `acvo_updater_enabled` filter wired
- [x] `.github/workflows/release.yml` — refactored slug + release notes, `main` branch
- [x] `.distignore` — excludes dev/CI files from the distributed zip
- [ ] Confirm repo slug + cut first `v0.1.0` release (see Active TODO)

---

## Technical Debt

_None yet._

---

## Notes for Development

- **HPOS:** never touch `get_post_meta()` for order data — use `WC_Order` methods only.
- **No loop risk:** the trigger is `..._status_processing`; we set status to
  `completed`, which fires `..._status_completed` — a different hook. No recursion.
- **`update_status()` saves immediately** in WooCommerce, so no explicit `save()` needed.
- **Idempotency:** the transition hook fires once per entry into Processing; setting
  the status to Completed moves the order out of Processing, so re-entry is the only
  way to re-trigger — which is the desired behaviour.
- **Filters are the public API.** Keep them stable and documented in `CLAUDE.md`.
