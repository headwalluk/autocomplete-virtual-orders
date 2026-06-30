<?php
/**
 * Main plugin orchestrator.
 *
 * @package AutocompleteVirtualOrders
 * @subpackage Includes
 * @since 0.1.0
 */

namespace Autocomplete_Virtual_Orders;

use WC_Order;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();

/**
 * Plugin class.
 *
 * Registers the single hook in run() and implements the auto-complete logic.
 *
 * @since 0.1.0
 */
class Plugin {

	/**
	 * GitHub release updater.
	 *
	 * @var Github_Updater|null
	 */
	private ?Github_Updater $github_updater = null;

	/**
	 * Register WordPress/WooCommerce hooks.
	 *
	 * @since 0.1.0
	 */
	public function run(): void {
		add_action( HOOK_TRIGGER, array( $this, 'maybe_complete_virtual_order' ), 10, 2 );

		$this->github_updater = new Github_Updater();
	}

	/**
	 * Complete an order if it contains only virtual items.
	 *
	 * Hooked to woocommerce_order_status_processing, so it fires whenever an
	 * order enters the Processing status. If the order has nothing physical to
	 * ship, it is advanced to the target status (Completed by default).
	 *
	 * @since 0.1.0
	 *
	 * @param int           $order_id WooCommerce order ID.
	 * @param WC_Order|null $order    The order object (passed by WooCommerce), or null.
	 */
	public function maybe_complete_virtual_order( int $order_id, $order = null ): void {
		if ( ! $order instanceof WC_Order ) {
			$order = wc_get_order( $order_id );
		}

		if ( $order instanceof WC_Order ) {
			$should_complete = $this->order_is_all_virtual( $order );

			/**
			 * Filter whether an order should be auto-completed.
			 *
			 * Return false to leave the order in Processing, or true to force
			 * completion of an order that would not otherwise qualify.
			 *
			 * @since 0.1.0
			 *
			 * @param bool     $should_complete Whether the order should be completed.
			 * @param WC_Order $order           The order being evaluated.
			 */
			$should_complete = (bool) apply_filters( 'acvo_should_autocomplete_order', $should_complete, $order );

			if ( $should_complete ) {
				/**
				 * Filter the status that qualifying orders are moved to.
				 *
				 * @since 0.1.0
				 *
				 * @param string   $status The target status slug (default 'completed').
				 * @param WC_Order $order  The order being evaluated.
				 */
				$target_status = (string) apply_filters( 'acvo_target_status', DEF_TARGET_STATUS, $order );

				// Skip if the order is already in the target status.
				if ( ! $order->has_status( $target_status ) ) {
					$order->update_status(
						$target_status,
						__( 'Autocomplete Virtual Orders: order completed automatically (all items are virtual — nothing to ship).', 'autocomplete-virtual-orders' )
					);

					/**
					 * Fires after an order has been auto-completed by this plugin.
					 *
					 * Use this to trigger custom side effects — bespoke emails,
					 * external fulfilment notifications, logging, etc.
					 *
					 * @since 0.1.0
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
	 * @since 0.1.0
	 *
	 * @param WC_Order $order The order to inspect.
	 * @return bool True if the order has at least one item and all items are virtual.
	 */
	public function order_is_all_virtual( WC_Order $order ): bool {
		$items  = $order->get_items();
		$result = ! empty( $items );

		foreach ( $items as $item ) {
			$product = $item->get_product();

			if ( ! $product || ! $product->is_virtual() ) {
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
		 * @since 0.1.0
		 *
		 * @param bool     $result Whether every item in the order is virtual.
		 * @param WC_Order $order  The order being inspected.
		 */
		return (bool) apply_filters( 'acvo_order_is_all_virtual', $result, $order );
	}
}
