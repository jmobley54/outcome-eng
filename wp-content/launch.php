<?php

/* Note that this script expects to live in the wp-content directory of
   Wordpress installation. */

$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

if (!is_user_logged_in()) {
    header( 'Location: /algorithm-launch?redirect_to=' . $_SERVER['REQUEST_URI']) ;
    exit;
}
?>

