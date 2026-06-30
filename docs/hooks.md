# Hooks Reference

**Autocomplete Virtual Orders** has no settings screen — its entire public API is
a small set of WordPress filters and one action. Drop these into your theme's
`functions.php` or a small site-specific plugin.

There are no options stored in the database; everything below is code-level
customisation.

---

## Filters

### `acvo_should_autocomplete_order`

The master decision. Return `false` to stop an order being completed, or `true`
to force completion of an order that wouldn't otherwise qualify.

```php
apply_filters( 'acvo_should_autocomplete_order', bool $should_complete, WC_Order $order );
```

| Parameter         | Type       | Description                                                        |
|-------------------|------------|--------------------------------------------------------------------|
| `$should_complete`| `bool`     | Default decision — `true` when every line item is virtual.         |
| `$order`          | `WC_Order` | The order being evaluated (already in Processing).                 |

**Example — never auto-complete orders over £500, even if all virtual:**

```php
add_filter( 'acvo_should_autocomplete_order', function ( $should_complete, $order ) {
	if ( $order->get_total() > 500 ) {
		return false;
	}
	return $should_complete;
}, 10, 2 );
```

**Example — also auto-complete free orders regardless of product type:**

```php
add_filter( 'acvo_should_autocomplete_order', function ( $should_complete, $order ) {
	if ( 0.0 === (float) $order->get_total() ) {
		return true;
	}
	return $should_complete;
}, 10, 2 );
```

---

### `acvo_order_is_all_virtual`

Redefine what "nothing to ship" means. This filters the result of the
all-virtual check *before* it reaches `acvo_should_autocomplete_order`.

```php
apply_filters( 'acvo_order_is_all_virtual', bool $is_all_virtual, WC_Order $order );
```

| Parameter        | Type       | Description                                                  |
|------------------|------------|--------------------------------------------------------------|
| `$is_all_virtual`| `bool`     | `true` if the order has ≥1 item and every item is virtual.   |
| `$order`         | `WC_Order` | The order being inspected.                                   |

**Example — treat a custom "deposit" product as non-shippable so it counts as
virtual even though it isn't flagged virtual in WooCommerce:**

```php
add_filter( 'acvo_order_is_all_virtual', function ( $is_all_virtual, $order ) {
	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( ! $product ) {
			return false;
		}
		// Allow virtual products OR our deposit product type.
		if ( ! $product->is_virtual() && 'deposit' !== $product->get_type() ) {
			return false;
		}
	}
	return true;
}, 10, 2 );
```

> **Tip:** use this filter to *change the definition* of shippable, and
> `acvo_should_autocomplete_order` to add *order-level conditions* (totals,
> customer, coupons, etc.).

---

### `acvo_target_status`

Change the status qualifying orders are moved to. Defaults to `completed`.
Use a bare status slug, **without** the `wc-` prefix.

```php
apply_filters( 'acvo_target_status', string $status, WC_Order $order );
```

| Parameter | Type       | Description                                  |
|-----------|------------|----------------------------------------------|
| `$status` | `string`   | Target status slug. Default `'completed'`.   |
| `$order`  | `WC_Order` | The order being evaluated.                   |

**Example — send virtual orders to a custom `fulfilled` status instead:**

```php
add_filter( 'acvo_target_status', function ( $status, $order ) {
	return 'fulfilled';
}, 10, 2 );
```

---

### `acvo_updater_enabled`

Toggle the in-plugin GitHub updater. Return `false` to disable update checks —
handy on staging/local environments or to pin a version.

```php
apply_filters( 'acvo_updater_enabled', bool $enabled );
```

**Example — disable updates on non-production sites:**

```php
add_filter( 'acvo_updater_enabled', function ( $enabled ) {
	return ( defined( 'WP_ENVIRONMENT_TYPE' ) && 'production' === WP_ENVIRONMENT_TYPE );
} );
```

---

## Actions

### `acvo_order_autocompleted`

Fires **after** an order has been auto-completed by this plugin. Use it for your
own side effects — bespoke emails, external fulfilment pings, audit logging.

```php
do_action( 'acvo_order_autocompleted', WC_Order $order, string $target_status );
```

| Parameter       | Type       | Description                                |
|-----------------|------------|--------------------------------------------|
| `$order`        | `WC_Order` | The order that was just completed.         |
| `$target_status`| `string`   | The status the order was moved to.         |

**Example — log every auto-completion:**

```php
add_action( 'acvo_order_autocompleted', function ( $order, $target_status ) {
	error_log( sprintf(
		'Order #%d auto-completed to %s by Autocomplete Virtual Orders.',
		$order->get_id(),
		$target_status
	) );
}, 10, 2 );
```

**Example — notify an external fulfilment system:**

```php
add_action( 'acvo_order_autocompleted', function ( $order ) {
	wp_remote_post( 'https://fulfilment.example.com/webhook', array(
		'body' => array( 'order_id' => $order->get_id() ),
	) );
} );
```

---

## How the pieces fit together

When an order enters **Processing**, the plugin runs this sequence:

1. **`acvo_order_is_all_virtual`** — decide whether the order has nothing to ship.
2. **`acvo_should_autocomplete_order`** — final yes/no (defaults to the result of step 1).
3. If yes and the order isn't already there, read **`acvo_target_status`** and
   move the order.
4. **`acvo_order_autocompleted`** — fires once the status has changed.
