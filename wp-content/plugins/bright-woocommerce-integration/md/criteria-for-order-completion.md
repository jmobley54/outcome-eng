# Criteria for Order Completion

The Woocommerce Bridge for Wordpress will set an order status to completed in the following circumstances:

1.  every product in the order must have a connected Bright course id (or ids).
2.  If item above is **true**, the Woocommerce Bridge will set the order to completed **at the time the associated bright registrations or license keys are created.**
3.  If a completed order is manually set back to processing from completed in the WooCommerce order UI, Bright will *not* re-create the associated registration(s) or license id(s).


