# How Autocomplete Virtual Orders works

## The gap it fills

WooCommerce completes an order automatically only when every item in it is both **virtual** and **downloadable**. Virtual products that are not downloadable — bookings, services, donations, event admissions, "pay an invoice" products — leave the order in **Processing** until someone completes it by hand.

This plugin completes those orders too.

## What happens when an order enters Processing

Each time an order moves into **Processing**, from any previous status, the plugin checks it:

1. **Does it have at least one line item?** An empty order is left alone — there is nothing to fulfil.
2. **Is every line item's product virtual?** This is the **Virtual** checkbox in the product data panel. A single physical item, or an item whose product has since been deleted, leaves the order in Processing.
3. If both are true, the order moves to **Completed** and gets this order note:

   > *Autocomplete Virtual Orders: order completed automatically (all items are virtual — nothing to ship).*

Completing the order sends WooCommerce's usual **Completed order** email to the customer, exactly as if you had completed it by hand.

## Examples

| Order contains | Result |
|----------------|--------|
| One virtual product | Completed |
| Several virtual products | Completed |
| A virtual product and a physical product | Stays in Processing |
| Only physical products | Stays in Processing |
| Virtual **and** downloadable products only | Completed (WooCommerce core would complete these too) |

## What it doesn't do

- It doesn't touch orders in any status other than Processing. Orders on hold, pending payment or failed are never changed.
- It doesn't change orders retrospectively. Orders already sitting in Processing when the plugin is activated stay there until they next enter Processing.
- It doesn't store settings or data of its own. Deleting the plugin leaves your orders as they are.

## Changing the behaviour

There is no settings page. A developer can change which orders are completed, what counts as virtual, and the status orders move to, using filters — see [hooks and filters](developers/hooks-and-filters.md).
