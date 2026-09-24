# Extending Autocomplete Virtual Orders

Practical recipes for common customisations. All examples go in your theme's `functions.php`, a custom mu-plugin, or a site-specific plugin.

For the full list of hooks and their arguments, see [hooks and filters](hooks-and-filters.md).

## Never auto-complete large orders

Leave orders over a threshold in Processing for a human to check, even when every item is virtual:

```php
add_filter( 'acvo_should_autocomplete_order', function ( $should_complete, $order ) {
    return $should_complete && $order->get_total() <= 500;
}, 10, 2 );
```

## Complete free orders regardless of product type

```php
add_filter( 'acvo_should_autocomplete_order', function ( $should_complete, $order ) {
    return $should_complete || 0.0 === (float) $order->get_total();
}, 10, 2 );
```

## Treat a custom product type as having nothing to ship

For example, a `deposit` product type that isn't flagged virtual in WooCommerce but never needs posting:

```php
add_filter( 'acvo_order_is_all_virtual', function ( $is_all_virtual, $order ) {
    $nothing_ships = count( $order->get_items() ) > 0;

    foreach ( $order->get_items() as $item ) {
        $product = $item->get_product();

        if ( ! $product instanceof WC_Product ) {
            $nothing_ships = false;
            break;
        }

        if ( ! $product->is_virtual() && 'deposit' !== $product->get_type() ) {
            $nothing_ships = false;
            break;
        }
    }

    return $nothing_ships;
}, 10, 2 );
```

## Treat one virtual product as shippable

The reverse: a product flagged virtual that still needs something posted, such as a gift voucher that is also sent as a printed card:

```php
add_filter( 'acvo_order_is_all_virtual', function ( $is_all_virtual, $order ) {
    $printed_card_product_id = 123;
    $result                  = $is_all_virtual;

    foreach ( $order->get_items() as $item ) {
        if ( $printed_card_product_id === $item->get_product_id() ) {
            $result = false;
            break;
        }
    }

    return $result;
}, 10, 2 );
```

## Send virtual orders to a custom status

Register the status first (with `register_post_status()` and the `wc_order_statuses` filter), then:

```php
add_filter( 'acvo_target_status', function ( $status, $order ) {
    return 'fulfilled';
}, 10, 2 );
```

If the status isn't registered when the order is processed, the plugin falls back to `completed` and logs an error rather than letting WooCommerce move the order to Pending payment.

## Notify an external system when an order is completed

```php
add_action( 'acvo_order_autocompleted', function ( $order, $target_status ) {
    wp_remote_post(
        'https://fulfilment.example.com/webhook',
        array(
            'blocking' => false,
            'body'     => array(
                'order_id' => $order->get_id(),
                'status'   => $target_status,
            ),
        )
    );
}, 10, 2 );
```

## Disable update checks on non-production sites

```php
add_filter( 'acvo_updater_enabled', function ( $enabled ) {
    return 'production' === wp_get_environment_type();
} );
```
