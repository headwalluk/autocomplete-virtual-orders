# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Autocomplete Virtual Orders** is a tiny, single-purpose WordPress/WooCommerce plugin. When an
order enters **Processing**, it checks whether the order has anything physical to ship. If every
line item is virtual, the order is advanced to **Completed**.

It fills a gap in WooCommerce core, which only auto-completes orders that are virtual **and**
downloadable. Virtual-but-not-downloadable items (bookings, services, donations, virtual tickets)
otherwise sit in Processing forever.

Design philosophy: **does one thing and does it well.** No telemetry, no phone-home, no upsells,
no settings page, no stored options. Every decision is overridable through filters.

- **Namespace:** `Autocomplete_Virtual_Orders` for all classes and private functions
- **Text Domain:** `autocomplete-virtual-orders`
- **Prefixes:** `acvo` / `ACVO` / `autocomplete_virtual_orders` (configured in `phpcs.xml`)
- **PHP:** 8.2+ (do NOT use `declare(strict_types=1)` — breaks WordPress/WooCommerce interop)
- **WordPress:** 6.0+, **WooCommerce:** 9.0+ (`Requires Plugins: woocommerce`)
- **No build system** — no npm, no Composer, no bundler

The structure and conventions follow the maintainer's reference plugin, `quick-2fa` (a sibling
under `wp-content/plugins/`). Where this file is silent, that plugin's `CLAUDE.md` is the standard.
This file and the code comments must match what the code actually does.

This plugin is published publicly on GitHub. Tracked files must contain no client names, client
URLs, fleet measurements or client data of any kind — in code, comments, docs, fixtures or commit
messages.

`dev-notes/` is **private and untracked** (`.gitignore`), backed up with the dev site, and blocked
from the web by `dev-notes/.htaccess`. It is the right home for client-identifying material. Never
copy content from `dev-notes/` into a tracked file without scrubbing it, and never reference a
`dev-notes/` path from a file that ships in the release zip.

## Commands

```bash
phpcs                            # Check WordPress Coding Standards (configured in phpcs.xml)
phpcbf                           # Auto-fix coding standards violations
phpcs includes/class-plugin.php  # Check a specific file
```

```bash
wp-translate . --check-instructions   # Is the block at the end of this file still current?
wp-translate . --sync-instructions    # Update it; review the diff afterwards
wp-translate . --dry-run              # Preview; no DeepL calls, no writes
```

Run `--check-instructions` after a `wp-translate` upgrade; if it reports drift, run
`--sync-instructions` and review the diff. Never hand-edit inside the
`wp-translate:begin`/`end` markers — the block is hash-validated and edits break it.

## Testing

There is no unit-test framework and none is wanted. Behaviour is exercised against the live dev
site through WP-CLI. The harness creates its own products and orders, suppresses mail, and deletes
everything afterwards:

```bash
wp eval-file wp-content/plugins/autocomplete-virtual-orders/dev-notes/testing/test-plugin-class.php
```

Two rules: reset the state you touch afterwards, and test the **defensive** path as well as the
happy one — calling the Processing callback with a string ID or junk arguments is what proves it
tolerates a sloppy third-party caller.

## Architecture

### Bootstrap

`autocomplete-virtual-orders.php` defines `ACVO_VERSION`, `ACVO_FILE`, `ACVO_PATH`, `ACVO_URL`
and `ACVO_BASENAME`, requires `constants.php`, `functions-private.php` and
`includes/class-plugin.php`, and declares HPOS compatibility. `acvo_plugin_run()` then stores the
`Plugin` instance in the global `$acvo_plugin` (read back with `get_plugin()`) and calls `run()`.
The GitHub updater is loaded only on admin, cron and WP-CLI requests.

### Key Files

| File | Purpose |
|------|---------|
| `autocomplete-virtual-orders.php` | Plugin header, constants, requires, HPOS declaration, updater loading, `acvo_plugin_run()` |
| `constants.php` | All magic strings and numbers: target status, trigger hook, plugin name, log source, updater config |
| `functions-private.php` | `get_plugin()` and `log_error()`. Private to the plugin — sites use the hooks in `docs/developers/hooks-and-filters.md` |
| `includes/class-plugin.php` | Registers the hook in `run()`; implements `maybe_complete_virtual_order()`, `order_is_all_virtual()` and `get_target_status()` |
| `includes/class-github-updater.php` | In-plugin updater: checks GitHub Releases and feeds the WordPress update transient. Ported from quick-2fa; keep the two in step |
| `uninstall.php` | Deletes the updater's transients |

### Behaviour

1. `HOOK_TRIGGER` (`woocommerce_order_status_processing`) fires on any entry into Processing
2. `order_is_all_virtual()` — true only if the order has at least one line item and every item's
   product is a `WC_Product` that passes `is_virtual()`; then filtered
3. `acvo_should_autocomplete_order` makes the final decision
4. `get_target_status()` reads `acvo_target_status` and falls back to `DEF_TARGET_STATUS` if the
   result is not a registered status — `WC_Order::set_status()` silently swaps an unknown status
   for `pending`, which would send a paid order back to Pending payment
5. If the order is not already in the target status, `update_status()` with an order note, then
   `acvo_order_autocompleted` fires

No recursion: the trigger is `..._status_processing`, and the order moves to a different status.

### Logging

- `log_error()` in `functions-private.php` writes to the WooCommerce logger under `LOG_SOURCE`
  (**WooCommerce → Status → Logs**). WooCommerce is a hard dependency, so its logger is the
  durable record — use it for failures in order handling
- `Github_Updater::log_error()` logs unconditionally to the PHP error log; `log()` only under
  `WP_DEBUG`. This matches quick-2fa so the updater can be ported between plugins unchanged

Never hide an error behind a debug flag, and never leave a `catch` that records nothing.

## Public Contracts

Code outside the plugin depends on these. Treat everything in this table as a contract:

| Contract | Examples | Breaks when |
|----------|----------|-------------|
| Filters and actions | `acvo_should_autocomplete_order`, `acvo_order_is_all_virtual`, `acvo_target_status`, `acvo_updater_enabled`, `acvo_order_autocompleted` | renamed or removed, or an argument is removed or reordered |
| Global constants | `ACVO_VERSION`, `ACVO_PATH`, `ACVO_URL`, `ACVO_FILE`, `ACVO_BASENAME` | renamed or removed |
| Order note text | the note added on completion | reworded — sites may search order notes for it |

- **Add, don't change.** New filter arguments go at the end. New behaviour gets a new filter, not a
  new meaning for an existing one
- **Deprecate, don't rename.** Fire the old name through `apply_filters_deprecated()` and pass its
  result into the new filter. Remove the old name no earlier than the next major version
- Record any change to a contract in `CHANGELOG.md`, and if you can't tell whether anything outside
  the plugin depends on it, stop and ask

The public reference is `docs/developers/hooks-and-filters.md`; keep it in step with the docblocks.

## Code Conventions

### PHP Style

- **Single-Entry Single-Exit (SESE):** one `return` at the end of a function. Top-of-function
  guard clauses are acceptable; `return` mid-function or inside a loop is not — set the result and
  `break`
- **An `if` with one or more `elseif` branches ends in a plain `else`**, never an `elseif`. A
  branch that does nothing is still written out, with a short comment. `phpcs.xml` excludes the
  `if`/`elseif`/`else` codes of `Generic.CodeAnalysis.EmptyStatement` so these pass
- **No assignment inside a condition** — assign on the line before
- **Constants for all magic strings/numbers** in `constants.php`
- **Type hints and return types** on all functions and class properties
- **Callbacks on hooks the plugin doesn't own take `mixed`.** Check each value before use
  (`instanceof`, `is_numeric()`). `Plugin::maybe_complete_virtual_order()` is the pattern
- **Check what a filter returns.** Read booleans with
  `(bool) filter_var( $value, FILTER_VALIDATE_BOOLEAN )`, and validate anything else, falling back
  to the default with a logged error — `Plugin::get_target_status()` is the pattern
- **Guard object lookups with `instanceof`, not truthiness** — `wc_get_order()`,
  `$item->get_product()` and friends return `false` on failure, and `wc_get_order()` can return a
  refund, which is not a `WC_Order`
- **HPOS:** never use `get_post_meta()` or `$wpdb` for order data — always `WC_Order` methods
- **i18n:** user-facing strings (the order note) wrapped in `__()` with the
  `autocomplete-virtual-orders` text domain; see the wp-translate block below
- Short array syntax is allowed, but the existing code uses `array()`; match the surrounding file

### Comments

- One-line docblock summary per function, saying what it does. `@param`, `@return` and `@since`
  lines don't count
- Inline comments only where the mechanism isn't obvious: a load-order trap, an API behaving
  unexpectedly, a guard whose absence would be silently wrong
- Don't restate what the names already say
- Reasoning and history go in `docs/` or `CHANGELOG.md`, not in comments

### Commit Messages

```
type: brief description

- Detail 1
- Detail 2
```

Types: `feat:` `fix:` `chore:` `refactor:` `docs:` `style:` `test:`

### Pre-Commit Workflow

1. `phpcs` — check violations
2. `phpcbf` — auto-fix
3. `phpcs` — verify clean: no errors **and no warnings**
4. Stage and commit

Every `phpcs:ignore` and `phpcs:disable` names the exact sniff and ends with `-- reason`.

## Release Workflow

1. Update the version in `autocomplete-virtual-orders.php` — **both** the `Version:` header and the
   `ACVO_VERSION` constant
2. Update `CHANGELOG.md`: move the `[Unreleased]` entries under the new version. If the version
   differs from the one in new `@since` tags, update those too
3. Update `readme.txt`: `Stable tag`, and a `== Changelog ==` / `== Upgrade Notice ==` entry
4. Run `phpcs` and the test harness
5. Tag the release in git (`vX.Y.Z`) and push the tag

The version lives in **three** places that must agree with the git tag: the header `Version:`,
`ACVO_VERSION`, and the `readme.txt` stable tag. `.github/workflows/release.yml` refuses to build
on a mismatch. The version must always correspond to a real GitHub Release tag, or the updater
will offer a release that doesn't exist.

## Reference Files

`docs/` is the maintained, public documentation. **One audience per document** — do not mix
store-owner and developer material in the same file:

- `docs/installation.md`, `docs/how-it-works.md`, `docs/troubleshooting.md` — store owners
- `docs/developers/hooks-and-filters.md` — the public extension surface
- `docs/developers/extending.md` — practical developer recipes
- `CHANGELOG.md` — per-version release notes

Supporting material (private, untracked):

- `dev-notes/00-project-tracker.md` — milestones, requirements, open questions
- `dev-notes/testing/` — WP-CLI behaviour harnesses

<!-- wp-translate:begin v=1.2.0 hash=d002c0d20c378973956462c0f9c3d6054f258c9fae52add470531221654343f3 -->
## Translating this plugin (wp-translate conventions)

This plugin's `.po`/`.mo` files are generated from source by
[wp-translate](https://github.com/headwalluk/wp-translate-tool), which
machine-translates strings with DeepL. Machine translation is only as good as
the strings you give it — follow these conventions when adding or editing
user-facing text.

### 1. Disambiguate short or ambiguous strings with `_x()`

DeepL handles full sentences well but guesses badly on short, context-free
labels. Give it context with `_x()` (or `esc_html_x()`, `_ex()`):

```php
// Ambiguous out of context — DeepL may read "Sent" as "late", "Folder" as "leaflet"
__( 'Sent', 'autocomplete-virtual-orders' );

// Disambiguated — the context is passed to the translator and to DeepL
_x( 'Sent', 'email delivery status', 'autocomplete-virtual-orders' );
_x( 'Folder', 'IMAP mailbox', 'autocomplete-virtual-orders' );
_x( 'Open', 'verb; button label', 'autocomplete-virtual-orders' );
```

The context (2nd argument) is never shown to users. Use it whenever a string is a
single word, a short label, or has more than one plausible meaning.

### 2. Use placeholders, never concatenation

Build dynamic text with `printf`/`sprintf` so the whole sentence translates as a
unit, and add a `translators:` comment to explain each placeholder:

```php
/* translators: %s is the user's display name */
printf( esc_html__( 'Welcome back, %s', 'autocomplete-virtual-orders' ), $name );
```

Never split a sentence across multiple translation calls — word order differs
between languages.

### 3. Use `_n()` for anything that can be counted

Never build a count-dependent sentence by hand, and never settle for a single
form that reads correctly only for one number. Languages differ in how many
plural forms they have — English and German have two, French treats 0 as
singular, Polish and Russian have three, Japanese has one, Arabic has six — and
`_n()` is the only way to express that.

```php
// Wrong — "1 reviews", and untranslatable into languages with other forms
printf( esc_html__( '%d reviews', 'autocomplete-virtual-orders' ), $count );

// Right — wp-translate fills every form the target locale needs
printf(
    esc_html( _n( '%d review', '%d reviews', $count, 'autocomplete-virtual-orders' ) ),
    $count
);
```

Keep the placeholder in **both** forms, even when the singular reads fine
without it (`'%d review'`, not `'One review'`) — some locales use the singular
slot for other numbers too.

For a short or ambiguous countable noun, use `_nx()` — the plural equivalent of
`_x()` — so the context reaches DeepL:

```php
// "Review" alone is ambiguous: critique? opinion? inspection?
_nx( '%d review', '%d reviews', $count, 'customer feedback on a company', 'autocomplete-virtual-orders' );
```

**Locales needing more than two forms will have their extra slots left empty for
a human translator.** DeepL supplies a singular and a plural; nobody can invent
Polish's third form from those, and wp-translate deliberately leaves it blank
rather than filling it with a plausible guess. Expect to see empty
`msgstr[2]` entries in `pl_PL` — that is correct behaviour, not a failure.

### 4. Acronyms and technical tokens

wp-translate keeps common acronyms (`TLS`, `API`, `SMTP`, `URL`, `ID`, `UTC`, …)
verbatim automatically. If you introduce an unusual acronym or product name that
must not be translated, keep it as its own standalone string so it is recognised,
or ask the maintainer to add it to the tool's acronym list.

### 5. Don't translate dates — let WordPress localise them

Never add month or day-of-week names (full or abbreviated) as translatable
strings. DeepL frequently mistranslates short forms like `Mon`, `Tue`, `Jan`,
`Feb` even with context hints. WordPress already ships locale-aware names — use
`$wp_locale`:

```php
global $wp_locale;
$wp_locale->get_month( $month_number );        // "January" (1-based)
$wp_locale->get_month_abbrev( $month_name );   // "Jan"
$wp_locale->get_weekday( $weekday_number );     // "Monday" (0 = Sunday)
$wp_locale->get_weekday_abbrev( $weekday_name ); // "Mon"
```

For formatted dates, prefer `wp_date()` / `date_i18n()`, which localise month and
day names automatically.

### 6. English source dialect

Write source strings in standard English. wp-translate handles English targets
locally (no DeepL): `en`/`en_US` use the source as-is, and `en_GB`/`en_AU`/… get
American spellings converted to British automatically (`color` → `colour`).

### Running wp-translate

After changing strings, regenerate translations:

```bash
wp-translate /path/to/this-plugin              # auto-detect locales from languages/
wp-translate /path/to/this-plugin en_GB,fr_FR  # explicit locales
wp-translate /path/to/this-plugin --dry-run    # preview; no API calls, no writes
```

Requires WP-CLI (`wp`) and a DeepL API key at `~/.config/deepl.env`. The tool
regenerates the `.pot` from source, translates new/changed strings for each
locale, and compiles the `.mo` files.
<!-- wp-translate:end -->
