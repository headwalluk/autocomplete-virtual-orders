# Autocomplete Virtual Orders

![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b?logo=wordpress&logoColor=white)
![WooCommerce](https://img.shields.io/badge/WooCommerce-9.0%2B-96588a?logo=woocommerce&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?logo=php&logoColor=white)
![HPOS](https://img.shields.io/badge/HPOS-compatible-2ea44f)
![License](https://img.shields.io/badge/license-GPLv2-blue)

Automatically completes WooCommerce orders that have **nothing to ship**.

When an order reaches **Processing**, if every item in it is a *virtual* product,
the order is moved straight to **Completed**. If anything physical is in the
order, it's left alone.

---

## What it does

WooCommerce core only auto-completes orders where every item is **virtual *and*
downloadable**. Virtual products that aren't downloadable — bookings, services,
donations, admissions, virtual gift cards, "pay an invoice" items — get stuck in
**Processing** forever and have to be completed by hand.

This plugin closes that gap, and nothing more:

- ✅ All items virtual → order auto-completes.
- ✅ Any physical item → order stays in Processing.
- ✅ HPOS compatible.
- ✅ Zero configuration — works the moment it's activated.
- ✅ Fully customisable in code via filters and an action.
- 🚫 No telemetry, no phone-home, no upsells, no settings bloat.

It does one thing and does it well.

## Who it's for

- **Store owners** selling services, bookings, donations, or any non-shippable
  product who don't want to complete orders manually.
- **Developers** who want a tiny, dependency-free, filter-driven building block
  instead of a heavyweight "order automation" suite.

## Install

Download `autocomplete-virtual-orders.zip` from the
[latest release](https://github.com/headwalluk/autocomplete-virtual-orders/releases/latest),
then in WordPress go to **Plugins → Add New → Upload Plugin**, upload it and
activate. WooCommerce must be active first.

Full instructions: **[docs/installation.md](docs/installation.md)**.

## Customise

No settings page — everything is done in code. Change when orders complete,
redefine what "shippable" means, send to a different status, or run your own code
afterwards:

```php
// Don't auto-complete orders over £500, even if all virtual.
add_filter( 'acvo_should_autocomplete_order', function ( $should, $order ) {
	return $order->get_total() > 500 ? false : $should;
}, 10, 2 );
```

Full filter & action reference: **[docs/hooks.md](docs/hooks.md)**.

## Requirements

WordPress 6.0+ · WooCommerce 9.0+ · PHP 8.0+

## License

[GPLv2 or later](LICENSE).
