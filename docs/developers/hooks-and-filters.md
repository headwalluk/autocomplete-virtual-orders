# Hooks and filters

This is the **public extension surface** of Autocomplete Virtual Orders. Anything not listed here should be considered internal — it may change without notice between releases. Functions and classes in the `Autocomplete_Virtual_Orders` namespace are private; the filters and action below are the supported integration points.

The plugin has no settings and stores no options, so these hooks are the only way to change its behaviour. Add them from your theme's `functions.php`, a custom mu-plugin, or a site-specific plugin. For worked examples, see [extending](extending.md).

## Compatibility

- A hook's name and arguments don't change within a major version. New arguments are only ever added at the end.
- A renamed hook keeps working under its old name until at least the next major version. The old name's result is passed on to the new one, and WordPress raises a deprecation notice when `WP_DEBUG` is on.
- Any change that could break an integration is listed in `CHANGELOG.md`.

## Order of evaluation

When an order enters **Processing**:

1. **`acvo_order_is_all_virtual`** — does the order have nothing to ship?
2. **`acvo_should_autocomplete_order`** — the final yes/no, defaulting to the result of step 1.
3. If yes, **`acvo_target_status`** picks the destination. If the order is already in that status, nothing happens.
4. The status changes, with an order note, and **`acvo_order_autocompleted`** fires.

## Filters

### `acvo_order_is_all_virtual`

Filter whether an order counts as having nothing to ship. Use it to change the *definition* of shippable; use `acvo_should_autocomplete_order` for order-level conditions such as totals, customers or coupons.

**Parameters:**
- `bool $is_all_virtual` — `true` when the order has at least one line item and every item's product is virtual
- `WC_Order $order` — The order being inspected

**Returns:** `bool`. The result is read with `filter_var( …, FILTER_VALIDATE_BOOLEAN )`, so `'no'`, `'off'` and `'false'` count as false.

```php
add_filter( 'acvo_order_is_all_virtual', function ( $is_all_virtual, $order ) {
    return $is_all_virtual && ! $order->get_meta( '_requires_posting' );
}, 10, 2 );
```

---

### `acvo_should_autocomplete_order`

The master decision. Return `false` to leave an order in Processing, or `true` to complete an order that would not otherwise qualify.

**Parameters:**
- `bool $should_complete` — Defaults to the result of `acvo_order_is_all_virtual`
- `WC_Order $order` — The order being evaluated, already in Processing

**Returns:** `bool`, read the same way as above.

```php
add_filter( 'acvo_should_autocomplete_order', function ( $should_complete, $order ) {
    return $should_complete && $order->get_total() <= 500;
}, 10, 2 );
```

---

### `acvo_target_status`

Filter the status qualifying orders are moved to. Defaults to `completed`.

**Parameters:**
- `string $status` — Target status slug. Default `'completed'`
- `WC_Order $order` — The order being evaluated

**Returns:** `string` — A **registered** order status slug. The `wc-` prefix is optional.

If the value is not a string, or is not a registered status, the plugin uses `completed` instead and writes an error to the WooCommerce log (**WooCommerce → Status → Logs**, source `autocomplete-virtual-orders`). Without this check, WooCommerce would silently move the order to **Pending payment**.

```php
add_filter( 'acvo_target_status', function ( $status, $order ) {
    return 'on-hold';
}, 10, 2 );
```

---

### `acvo_updater_enabled`

Filter whether the in-plugin GitHub updater checks for new releases.

**Parameters:**
- `bool $enabled` — Default `true`

**Returns:** `bool`, read the same way as above.

```php
add_filter( 'acvo_updater_enabled', '__return_false' );
```

## Actions

### `acvo_order_autocompleted`

Fires after this plugin has changed an order's status. It does not fire for orders that were left alone, or that were already in the target status.

**Parameters:**
- `WC_Order $order` — The order that was completed
- `string $target_status` — The status the order was moved to, without the `wc-` prefix

```php
add_action( 'acvo_order_autocompleted', function ( $order, $target_status ) {
    $order->add_order_note( 'Access details sent to the customer.' );
}, 10, 2 );
```
