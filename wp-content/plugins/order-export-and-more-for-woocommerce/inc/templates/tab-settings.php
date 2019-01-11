
<form method="post" action="<?php echo admin_url( "admin-post.php" ); ?>">
	<table class="form-table">
		<tbody>


			<tr id="general-settings">
				<td colspan="2" style="padding:0;">
					<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<?php _e( 'General Settings', 'jem-woocommerce-exporter' ); ?></h3>
					<p class="description"><?php __( 'Manage export options across Store Exporter from this screen.', 'jem-woocommerce-exporter' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="jemex_export_filename"><?php _e( 'Export filename', 'jem-woocommerce-exporter' ); ?></label></th>
				<td>
					<input type="text" name="jemex_export_filename" id="jemex_export_filename" value="<?php echo esc_attr( $this->settings['filename'] ); ?>" class="large-text code" />
					<p class="description"><?php _e( 'The filename of the exported data. ', 'jem-woocommerce-exporter' ); ?> </p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="jemex_encoding"><?php _e( 'Character Encoding', 'jem-woocommerce-exporter' ); ?></label></th>
				<td>
					<select id="jemex_encoding" name="jemex_encoding">
						<option value="UTF-8"<?php selected( $this->settings['encoding'], 'UTF-8' ); ?>><?php _e( 'UTF-8', 'jem-woocommerce-exporter' ); ?></option>
						<option value="UTF-16"<?php selected( $this->settings['encoding'], 'UTF-16' ); ?>><?php _e( 'UTF-16', 'jem-woocommerce-exporter' ); ?></option>
					</select>
								</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><label for="jemex_date_format"><?php _e( 'Date Format', 'jem-woocommerce-exporter' ); ?></label></th>
				<td>
					<ul>
						<li><input type="radio" name="jemex_date_format" value="F j, Y"<?php checked( $this->settings['date_format'], 'F j, Y' ); ?>> <span><?php echo date( 'F j, Y' ); ?></span></li>
						<li><input type="radio" name="jemex_date_format" value="Y/m/d"<?php checked( $this->settings['date_format'], 'Y/m/d' ); ?>> <span><?php echo 'YYYY/MM/DD'; ?></span></li>
						<li><input type="radio" name="jemex_date_format" value="m/d/Y"<?php checked( $this->settings['date_format'], 'm/d/Y' ); ?>> <span><?php echo 'MM/DD/YYYY'; ?></span></li>
						<li><input type="radio" name="jemex_date_format" value="d/m/Y"<?php checked( $this->settings['date_format'], 'd/m/Y' ); ?>> <span><?php echo 'DD/MM/YYYY'; ?></span></li>
					</ul>
					<p class="description"><?php _e( 'This will determine the format of all dates in the file.', 'jem-woocommerce-exporter' ); ?></p>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><label for="jemex_field_delimiter"><?php _e( 'Field Delimiter', 'jem-woocommerce-exporter' ); ?></label></th>
				<td>
					<input type="text" name="jemex_field_delimiter" id="jemex_field_delimiter" size="5" maxlength="5" value="<?php echo esc_attr( $this->settings['delimiter'] ); ?>" class="text code" />
					<p class="description"><?php _e( 'The character used to seperate fields in your file. Typically this is a comma ","', 'jem-woocommerce-exporter' ); ?> </p>
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


