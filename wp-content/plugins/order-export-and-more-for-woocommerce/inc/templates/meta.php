<form method="post" action="<?php echo admin_url( "admin-post.php" ); ?>">
<h3>Meta Viewer</h3>
<p class="instructions"><?php _e('You can view meta data that is associated with a Product or Order', JEM_EXP_DOMAIN); ?></p>
<p><a href="https://jem-products.com/knowledgebase/using-meta-data-viewer/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wordpress" target='_blank'><?php _e('See the documentation here ', JEM_EXP_DOMAIN); ?></a></p>
<p><?php _e('To export this data you will need the Pro version', JEM_EXP_DOMAIN); ?> <a href="https://jem-products.com/woocommerce-export-orders-pro-plugin/?utm_source=wordpress&utm_medium=plugin&utm_campaign=meta-viewer"  target='_blank'><?php _e('Click here for more details', JEM_EXP_DOMAIN); ?></a></p>
<div>
	<label for="meta_id"><?php _e('Product', JEM_EXP_DOMAIN); ?>/<?php _e('Order ID', JEM_EXP_DOMAIN); ?></label>
	<input type="text" size="25" name="meta_id">
	<input type="radio" name="meta_type" value="product" checked><?php _e('Product', JEM_EXP_DOMAIN); ?> &nbsp;&nbsp;
	<input type="radio" name="meta_type" value="order"><?php _e('Order', JEM_EXP_DOMAIN); ?>

</div>
    <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="View Meta">
    </p>
    <input type="hidden" name="action" value="update_meta">
    <input type="hidden" name="_wp_http_referer" value="<?php echo urlencode( $_SERVER['REQUEST_URI'] ); ?>">
<?php
    if($this->message != ""){
        echo $this->message;
    }

?>
    <TABLE class="jemxp-meta-table" style="font-family:monospace; text-align:left; width:100%;">
    <?php echo $html; ?>

    </TABLE>
    <TABLE class="jemxp-meta-table" style="">
        <?php echo $line_item_html; ?>

    </TABLE>
</form>




