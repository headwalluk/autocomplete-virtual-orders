<?php
/**
 * Main plugin orchestrator.
 *
 * @package Autocomplete_Virtual_Orders
 * @since 1.0.0
 */

namespace Autocomplete_Virtual_Orders;

use WC_Order;
use WC_Product;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

/**
 * Plugin class.
 *
 * Registers the single hook in run() and implements the auto-complete logic.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Register WordPress/WooCommerce hooks.
	 *
	 * @since 1.0.0
	 */
	public function run(): void {
		add_action( HOOK_TRIGGER, array( $this, 'maybe_complete_virtual_order' ), 10, 2 );
	}

	/**
	 * Complete an order if it contains only virtual items.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $order_id WooCommerce order ID.
	 * @param mixed $order    The order object passed by WooCommerce, or null.
	 */
	public function maybe_complete_virtual_order( mixed $order_id, mixed $order = null ): void {
		if ( $order instanceof WC_Order ) {
			// WooCommerce passed the order; no lookup needed.
		} elseif ( is_numeric( $order_id ) ) {
			$order = wc_get_order( (int) $order_id );
		} else {
			$order = null;
		}

		if ( $order instanceof WC_Order ) {
			$should_complete = $this->order_is_all_virtual( $order );

			/**
			 * Filter whether an order should be auto-completed.
			 *
			 * Return false to leave the order in Processing, or true to force
			 * completion of an order that would not otherwise qualify.
			 *
			 * @since 1.0.0
			 *
			 * @param bool     $should_complete Whether the order should be completed.
			 * @param WC_Order $order           The order being evaluated.
			 */
			$should_complete = (bool) filter_var( apply_filters( 'acvo_should_autocomplete_order', $should_complete, $order ), FILTER_VALIDATE_BOOLEAN );

			if ( $should_complete ) {
				$target_status = $this->get_target_status( $order );

				if ( ! $order->has_status( $target_status ) ) {
					$order->update_status(
						$target_status,
						sprintf(
							/* translators: Order note added when WooCommerce moves an order to its Completed status. %s is the plugin name, not translated. */
							__( '%s: order completed automatically (all items are virtual — nothing to ship).', 'autocomplete-virtual-orders' ),
							PLUGIN_NAME
						)
					);

					/**
					 * Fires after an order has been auto-completed by this plugin.
					 *
					 * Use this to trigger custom side effects — bespoke emails,
					 * external fulfilment notifications, logging, etc.
					 *
					 * @since 1.0.0
					 *
					 * @param WC_Order $order         The order that was completed.
					 * @param string   $target_status The status the order was moved to.
					 */
					do_action( 'acvo_order_autocompleted', $order, $target_status );
				}
			}
		}
	}

	/**
	 * Determine whether every item in an order is virtual.
	 *
	 * An order with no line items returns false: there is nothing to fulfil, so
	 * it should not be auto-completed.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order The order to inspect.
	 * @return bool True if the order has at least one item and all items are virtual.
	 */
	public function order_is_all_virtual( WC_Order $order ): bool {
		$items  = $order->get_items();
		$result = ! empty( $items );

		foreach ( $items as $item ) {
			$product = $item->get_product();

			if ( ! $product instanceof WC_Product || ! $product->is_virtual() ) {
				$result = false;
				break;
			}
		}

		/**
		 * Filter whether an order counts as entirely virtual.
		 *
		 * Lets sites redefine "shippable" — for example, treating a custom
		 * product type as physical even when WooCommerce reports it as virtual.
		 *
		 * @since 1.0.0
		 *
		 * @param bool     $result Whether every item in the order is virtual.
		 * @param WC_Order $order  The order being inspected.
		 */
		return (bool) filter_var( apply_filters( 'acvo_order_is_all_virtual', $result, $order ), FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Resolve the filtered target status, falling back to DEF_TARGET_STATUS if it is not registered.
	 *
	 * @since 1.1.0
	 *
	 * @param WC_Order $order The order being completed.
	 * @return string Bare status slug, without the `wc-` prefix.
	 */
	private function get_target_status( WC_Order $order ): string {
		/**
		 * Filter the status that qualifying orders are moved to.
		 *
		 * @since 1.0.0
		 *
		 * @param string   $status The target status slug (default 'completed').
		 * @param WC_Order $order  The order being evaluated.
		 */
		$filtered_status = apply_filters( 'acvo_target_status', DEF_TARGET_STATUS, $order );
		$target_status   = DEF_TARGET_STATUS;

		if ( ! is_string( $filtered_status ) ) {
			log_error( sprintf( 'acvo_target_status returned %s for order #%d; using "%s".', get_debug_type( $filtered_status ), $order->get_id(), DEF_TARGET_STATUS ) );
		} else {
			$bare_status = str_starts_with( $filtered_status, 'wc-' ) ? substr( $filtered_status, 3 ) : $filtered_status;

			// WC_Order::set_status() silently swaps an unregistered status for "pending".
			if ( array_key_exists( 'wc-' . $bare_status, wc_get_order_statuses() ) ) {
				$target_status = $bare_status;
			} else {
				log_error( sprintf( 'acvo_target_status returned unregistered status "%s" for order #%d; using "%s".', $filtered_status, $order->get_id(), DEF_TARGET_STATUS ) );
			}
		}

		return $target_status;
	}
}
