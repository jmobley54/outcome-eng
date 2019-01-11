	<div id="screen_preloader" style="position: absolute;width: 100%;height: 1000px;z-index: 9999;text-align: center;background: #fff;padding-top: 200px;"><h3>W8 Contact Form</h3><img src="<?php print(plugins_url( '/assets/img/screen_preloader.gif' , __FILE__ ));?>"><h5><?php _e( 'LOADING', W8CONTACT_FORM_TEXT_DOMAIN );?><br><br><?php _e( 'Please wait...', W8CONTACT_FORM_TEXT_DOMAIN );?></h5></div>
<div class="wrap w8contact_form" style="visibility:hidden">
	<br />
	<h3><?php _e( 'Custom Fields', W8CONTACT_FORM_TEXT_DOMAIN );?><hr /></h3>
	<p>
	<?php _e( 'Fields with marked * are mandatory to save the custom field. The row marked with red background if it has missing parameters during the save process.', W8CONTACT_FORM_TEXT_DOMAIN );
	$custom_fields = json_decode( stripslashes( get_option('cfs-custom-fields') ) );
	if ( is_array( $custom_fields ) ) {
		foreach( $custom_fields as $cf ) {
			foreach( $cf as $cufi ) {
				if ( $cufi->type == "text" ) {
					print( "<div class='one-custom-field'><input type='text' maxlength='3' class='cfpriority cfstooltip' onkeyup='this.value = this.value.replace(/[^0-9]/g,\"\");' data-title='Priority number in display order' value='" . $cufi->priority . "' placeholder=''><input type='text' data-type='text' class='cfid cfstooltip' data-title='ID of input field, eg.: FNAME' value='" . $cufi->id . "' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" placeholder='* ID'><input type='text' class='cfname cfstooltip' value='" . $cufi->name . "' data-title='Name of custom field, eg.: First Name' placeholder='* Name'><input type='text' class='cfwarning cfstooltip' data-title='Warning text for the field if it is required, eg.: Firstname field is mandatory' value='" . $cufi->warning . "' placeholder='Warning'><input type='text' class='cfminlength cfstooltip' data-title='Minimum character length for required field' value='" . $cufi->minlength . "' placeholder='0'><input type='checkbox' " . ( $cufi->required == 'true' ? 'checked' : '' ) . " class='cfrequired cfstooltip' data-title='Check this if the field is mandatory' value='0'><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='" . plugins_url( '/assets/img/delete.png', __FILE__) . "'></div>" );
				}
				if ( $cufi->type == "radio" ) {
					print( "<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='" . $cufi->priority . "' placeholder=''><input type='text' data-type='radio' class='cfid cfstooltip' data-title='ID of radio field, eg.: GENDER' value='" . $cufi->id . "' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" placeholder='* ID'><input type='text' class='cfname cfstooltip longinput' value='" . $cufi->name . "' data-title='Name and value pair for custom field, eg.: Female:female,Male:male' placeholder='* Female:female,Male:male'><div class='minorspace'></div><input type='checkbox' class='cfrequired cfstooltip' " . ( $cufi->required == 'true' ? 'checked' : '' ) . " data-title='Check this if the field is mandatory' value='0'><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='" . plugins_url( '/assets/img/delete.png', __FILE__) . "'></div>" );
				}
				if ( $cufi->type == "checkbox" ) {
					print( "<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='" . $cufi->priority . "' placeholder=''><input type='text' data-type='checkbox' class='cfid cfstooltip' data-title='ID of checkbox field, eg.: POLICY' value='" . $cufi->id . "' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" placeholder='* ID'><input type='text' class='cfname cfstooltip longinput' value='" . $cufi->name . "' data-title='Description for checkbox, eg.: Accept Terms and Conditions' placeholder='* Accept Terms and Conditions'><div class='minorspace'></div><input type='checkbox' class='cfrequired cfstooltip' " . ( $cufi->required == 'true' ? 'checked' : '' ) . " data-title='Check this if the field is mandatory' value='0'><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='" . plugins_url( '/assets/img/delete.png', __FILE__) . "'></div>" );
				}
				if ( $cufi->type == "textarea" ) {
					print( "<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='" . $cufi->priority . "' placeholder=''><input type='text' data-type='textarea' class='cfid cfstooltip' data-title='ID of textarea field, eg.: Description' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" value='" . $cufi->id . "' placeholder='* ID'><input type='text' class='cfname cfstooltip' value='" . $cufi->name . "' data-title='Placeholder for custom field, eg.: Description' placeholder='* Description'><input type='text' class='cfwarning cfstooltip' data-title='Warning text for the field if it is required, eg.: Description field is mandatory' value='" . $cufi->warning . "' placeholder='Warning'><input type='text' class='cfminlength cfstooltip' data-title='Minimum character length for required field' value='" . $cufi->minlength . "' placeholder='0'><input type='checkbox' class='cfrequired cfstooltip' " . ( $cufi->required == 'true' ? 'checked' : '' ) . " data-title='Check this if the field is mandatory' value='0'><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='" . plugins_url( '/assets/img/delete.png', __FILE__) . "'></div>" );					
				}
				if ( $cufi->type == "select" ) {
					print( "<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='" . $cufi->priority . "' placeholder=''><input type='text' data-type='select' class='cfid cfstooltip' data-title='ID of radio field, eg.: FRUITS' value='" . $cufi->id . "' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" placeholder='* ID'><input type='text' class='cfname cfstooltip longinput' value='" . $cufi->name . "' data-title='Name and value pair for custom field, eg.: Select from the list,Apple:apple,Orange:orange,Lemon:lemon' placeholder='* Select from the list,Apple:applevalue,Orange:orangevalue,Lemon:lemonvalue' class='longinput'><div class='minorspace'></div><input type='checkbox' class='cfrequired cfstooltip' " . ( $cufi->required == 'true' ? 'checked' : '' ) . " data-title='Check this if the field is mandatory' value='0'><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='" . plugins_url( '/assets/img/delete.png', __FILE__) . "'></div>" );
				}
				if ( $cufi->type == "hidden" ) {
					print( "<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='" . $cufi->priority . "' placeholder=''><input type='text' data-type='hidden' class='cfid cfstooltip' data-title='ID of hidden field, eg.: SIGNUP' value='" . $cufi->id . "' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" placeholder='* ID'><input type='text' class='cfname cfstooltip longinput' value='" . $cufi->name . "' data-title='Value of the field, eg.: blog name' placeholder='* blog name' class='longinput'><div class='emptycheckbox'></div><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='" . plugins_url( '/assets/img/delete.png', __FILE__) . "'></div>" );
				}
			}
		}
	}
	?>
	</p>
		<div class="custom_field_section">
			<div class="fields"></div>
			<div class="fields_save_area">
				<div class="cfs-cf-save-container"><div class="acfield save_custom_fields button button-primary button-large"><?php _e( 'SAVE', W8CONTACT_FORM_TEXT_DOMAIN );?></div></div>
				<div class="fields_add_area">
					<div class="acfield add_custom_fields button button-secondary button-large"><?php _e( 'ADD TEXT FIELD', W8CONTACT_FORM_TEXT_DOMAIN );?></div>
					<div class="acfield add_custom_fields_textarea button button-secondary button-large"><?php _e( 'ADD TEXTAREA', W8CONTACT_FORM_TEXT_DOMAIN );?></div>
					<div class="acfield add_custom_fields_radio button button-secondary button-large"><?php _e( 'ADD RADIO BUTTONS', W8CONTACT_FORM_TEXT_DOMAIN );?></div>
					<div class="acfield add_custom_fields_checkbox button button-secondary button-large"><?php _e( 'ADD CHECKBOX', W8CONTACT_FORM_TEXT_DOMAIN );?></div>
					<div class="acfield add_custom_fields_select button button-secondary button-large"><?php _e( 'ADD SELECT BOX', W8CONTACT_FORM_TEXT_DOMAIN );?></div>
					<div class="acfield add_custom_fields_hidden button button-secondary button-large"><?php _e( 'ADD HIDDEN FIELD', W8CONTACT_FORM_TEXT_DOMAIN );?></div>
				</div>
			</div>
		</div>
</div>