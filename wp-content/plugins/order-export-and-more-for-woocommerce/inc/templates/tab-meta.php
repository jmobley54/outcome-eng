
<form method="post" action="<?php echo admin_url( "admin-post.php" ); ?>">
	<table class="form-table">
		<tbody>


			<tr valign="top">
				<td colspan="2" style="padding:0;">
					<h3>Access a powerful tool to view the Meta Data associated with your Orders and Products</h3>
					<p class="description"><a href="http://jem-products.com/woocommerce-export-orders-pro-plugin/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wordpress"><?php _e('Available in the PRO version', JEM_EXP_DOMAIN); ?></a></p>
				</td>
			</tr>


			</tbody>
	</table>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save changes', JEM_EXP_DOMAIN); ?>">
	</p>
		<input type="hidden" name="action" value="save_settings">
		<input type="hidden" name="_wp_http_referer" value="<?php echo urlencode( $_SERVER['REQUEST_URI'] ); ?>">
		 
	
</form>


