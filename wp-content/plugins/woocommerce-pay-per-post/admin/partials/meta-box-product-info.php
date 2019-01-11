<div id="ppp-product">
    <p><?php 
esc_attr_e( 'This is the id of the product that is required to have been purchased before a user can view the content of this page. You can select multiple products.', 'wc_pay_per_post' );
?><br>
    </p>
    <label for="woocommerce_ppp_product_id"><?php 
esc_attr_e( 'Select Product(s)', 'wc_pay_per_post' );
?><br>
		<?php 
echo  $drop_down ;
?>
    </label>
	<?php 

if ( wcppp_freemius()->is_not_paying() && !wcppp_freemius()->is_trial() ) {
    ?>
        <a href="<?php 
    echo  wcppp_freemius()->get_upgrade_url() ;
    ?>" title="upgrade today"><img src="<?php 
    echo  plugin_dir_url( __DIR__ ) . 'img/override-content.png' ;
    ?>" width="100%"></a>
	<?php 
}

?>
	<?php 
?>

</div>