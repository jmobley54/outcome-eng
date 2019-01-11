# Understanding the Checkout Process

The bright woocommerce integration bridge contains a method called

    managerOrder($wc_order)

that manages all facets of the WooCommerce checkout, as it pertains to Bright.

This document covers 

 * how and when this routine is called, 
 * what it does, 
 * how to check what it did,
 * and how to call it manually or otherwise force it to "run again".

# How It Is Called

By default, Bright's manageOrder routine will run when the order enters a processing state, which is when the payment has been successfully processed.

# What It Does

For [simple orders][1], Bright will insure there's a valid launchable registration for the course linked to the product or products in the order.

For [license key orders][2], Bright will create a license key based on the linked course(s) and or Bright Metadata for the course.

# How to Check What It Did

## Simple Orders

## License Key Orders

When 


# Re-processing a License Order

The first step is to remove the order custom field called 

    "bright_license"

which will contain the current license ID of the order.   After that, it will be possible
to re-process the order using the "Manager Order Via Bright" action of the Order Actions.

If you attempt to reprocess the order but the existing license key exists, an order note note will be
added with the text [for example]:

    Re-using previously created license [license id].  Delete the order custom field bright_license will force Bright to create a new license when running Manage Order via Bright.

If you delete the bright_license from an order, this does not deactivate the license in Bright.   To do that, mark the invitation [also know as a license] inactive via 
a Bright Administration Console.


# Errors

Errors in processing my result in an order note being added.  Please check the order notes when troubleshooting your Bright-related orders.


 [1]: http://help.aura-software.com/linking-a-product/
