<?php 
function pmwi_pmxi_options_tab( $isWizard, $post )
{			
    if ( $post['custom_type'] == 'product' && class_exists('WooCommerce') ):

        wp_enqueue_script('pmwi-admin-options-script', PMWI_ROOT_URL . '/static/js/admin-options.js', array('jquery'), PMWI_FREE_VERSION);
        wp_enqueue_style('pmwi-admin-options-style', PMWI_ROOT_URL . '/static/css/admin-options.css', array(), PMWI_FREE_VERSION);

    endif;
}
