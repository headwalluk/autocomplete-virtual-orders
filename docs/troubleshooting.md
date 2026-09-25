# Troubleshooting

## An all-virtual order stayed in Processing

Work through these in order:

1. **Is the plugin active?** Check **Plugins**. WooCommerce must be active too.
2. **Did the order *enter* Processing after the plugin was activated?** The plugin acts on the move into Processing, not on orders already there. Change the status to **On hold** and back to **Processing** to run the check again.
3. **Is every product ticked Virtual?** Open each product in the order and check the **Virtual** box in the product data panel. For a variable product, it is the variation's **Virtual** box that counts.
4. **Does every line item still have a product?** If a product was deleted after the order was placed, the plugin can't tell whether it was virtual, so it leaves the order alone.
5. **Is custom code overriding the decision?** Search your theme and plugins for `acvo_should_autocomplete_order` and `acvo_order_is_all_virtual`.

## An order was moved to a status I didn't expect

The destination can be changed with the `acvo_target_status` filter. If a filter returns a status that isn't registered with WooCommerce, the plugin uses **Completed** instead and logs an error. Check **WooCommerce → Status → Logs** for entries from the `autocomplete-virtual-orders` source.

## An order with a physical item was completed

Something is overriding the default decision. Search your theme and plugins for `acvo_should_autocomplete_order` and `acvo_order_is_all_virtual`.

## Updates aren't appearing

- Updates are checked at most every 12 hours. After a failed check, the plugin waits an hour before asking GitHub again.
- To check from the command line, run `wp plugin list --name=autocomplete-virtual-orders --fields=name,version,update,update_version`. `wp plugin update autocomplete-virtual-orders` installs a pending update.
- Check for `acvo_updater_enabled` in your code — it may have been used to pause updates.
- Check the PHP error log for lines starting `Autocomplete_Virtual_Orders Github_Updater [error]:`. Failed update checks are always logged there, whether or not `WP_DEBUG` is on.
