# CLAUDE.md

Repository: public

Follows the **headwall-wp-plugin** skill. Load it before working on this plugin; this file holds
only what is specific to Autocomplete Virtual Orders.

## Project Overview

When an order enters **Processing**, the plugin checks whether it has anything physical to ship.
If every line item is virtual, the order moves to **Completed**. WooCommerce core only
auto-completes orders that are virtual **and** downloadable, so virtual-only bookings, services,
donations and tickets otherwise sit in Processing.

Does one thing: no settings page, no stored options, no telemetry. Every decision is a filter.

- **Namespace:** `Autocomplete_Virtual_Orders` · **Text domain:** `autocomplete-virtual-orders` · **Prefixes:** `acvo_` (hooks), `ACVO_` (constants), `autocomplete_virtual_orders_` (updater cache, named from the slug)
- **PHP / WordPress floor:** 8.2 / 6.7. 6.7 was set in 1.1.0, when translations relied on its
  automatic loading of `languages/`. `load_plugin_textdomain()` now does that, but WooCommerce
  9.x needs a recent WordPress anyway, so the floor stays
- **WooCommerce:** 9.0+, `Requires Plugins: woocommerce`
- **No build system, no settings, no assets, no WP-CLI commands, no stored options.** There is
  deliberately no version option or first-run check: there's nothing to migrate

## Behaviour

`Plugin::maybe_complete_virtual_order()` on `HOOK_TRIGGER` (`woocommerce_order_status_processing`),
which fires on any entry into Processing:

1. Resolve the order: a `WC_Order` passed by WooCommerce, else a numeric ID through
   `wc_get_order()`. Anything else is ignored
2. `order_is_all_virtual()`: true only if there is at least one line item and every item's
   product is a `WC_Product` passing `is_virtual()`; then `acvo_order_is_all_virtual`
3. `acvo_should_autocomplete_order` makes the final decision
4. `get_target_status()` reads `acvo_target_status` and falls back to `DEF_TARGET_STATUS` for a
   non-string or unregistered status. `WC_Order::set_status()` silently swaps an unknown status
   for `pending`, which would send a paid order back to Pending payment
5. If the order isn't already in the target status: `update_status()` with the order note, then
   `acvo_order_autocompleted`

No recursion: the order leaves Processing.

Errors go to the WooCommerce logger under `LOG_SOURCE` (**WooCommerce → Status → Logs**), through
`log_error()` in `functions-private.php`. WooCommerce is a hard dependency, so its log is the
durable record for order handling. The updater keeps its own `log_error()` / `log()`.

## Key Files

| File | Purpose |
|------|---------|
| `autocomplete-virtual-orders.php` | Header, `ACVO_*` constants, requires, updater, `acvo_plugin_run()` |
| `constants.php` | Target status, trigger hook, plugin name, log source, updater repo and filter, legacy cache keys |
| `functions-private.php` | `get_plugin()`, `log_error()` |
| `includes/class-plugin.php` | Hooks, the completion logic, WooCommerce compatibility, textdomain |
| `includes/class-github-updater.php` | Copied from Quick 2FA; change only `namespace` and `@package` |
| `uninstall.php` | Deletes the updater cache, and the 1.x updater's transients |

## Public Contracts

The skill's `public-contracts.md` has the rules. This plugin's:

| Contract | Names |
|----------|-------|
| Filters | `acvo_should_autocomplete_order`, `acvo_order_is_all_virtual`, `acvo_target_status`, `acvo_updater_enabled` |
| Actions | `acvo_order_autocompleted` |
| Global constants | `ACVO_VERSION`, `ACVO_FILE`, `ACVO_PATH`, `ACVO_URL`, `ACVO_BASENAME`. 1.1.0 renamed `ACVO_DIR` to `ACVO_PATH` |
| Global variable | `$acvo_plugin`, the `Plugin` instance. Undocumented, kept since `Plugin::instance()` replaced it |
| Order note text | The note added on completion. Sites may search order notes for it, so don't reword it. It contains an em-dash; leave it |

All the hooks are documented in `docs/developers/hooks-and-filters.md`; keep it in step with the
docblocks.

### High-Impact Code

These paths change the status of paid orders, which sends the customer's completion email:

- `Plugin::maybe_complete_virtual_order()`, `order_is_all_virtual()` and `get_target_status()`

## Testing

`dev-notes/testing/test-plugin-class.php` creates its own products and orders, suppresses mail,
captures this plugin's log entries and deletes everything afterwards:

```bash
wp eval-file wp-content/plugins/autocomplete-virtual-orders/dev-notes/testing/test-plugin-class.php
```

It must pass before a release and after any change to high-impact code.

## Release

Follow the skill's `release.md` (public). The updater's repository is `UPDATER_GITHUB_REPO` in
`constants.php`. The default branch is `main`.

## Reference Files

`docs/` (tracked, public):

- `docs/installation.md`, `docs/how-it-works.md`, `docs/troubleshooting.md`: store owners
- `docs/developers/hooks-and-filters.md`, `docs/developers/extending.md`: developers

`dev-notes/` (private, untracked):

- `dev-notes/00-project-tracker.md`: milestones, requirements, open questions
- `dev-notes/testing/`: the harness

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
