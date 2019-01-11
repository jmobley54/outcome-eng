<?php
/**
 * This template renders just a cart
 */
?>
<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>
    <?php print do_shortcode( '[woocommerce_cart]'); ?>
<?php endif;