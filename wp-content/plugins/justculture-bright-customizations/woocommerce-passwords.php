<?php

// https://wordpress.org/support/topic/woocommerce-password-strength-meter-too-high/page/2/

function reduce_woocommerce_min_strength_requirement( $strength ) {
    return 1;
}

add_filter( 'woocommerce_min_password_strength', 'reduce_woocommerce_min_strength_requirement' );