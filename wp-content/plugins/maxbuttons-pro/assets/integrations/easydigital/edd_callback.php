<?php
// This is in a seperate file due to EDD not liking namespaced callbacks

function edd_maxbutton_callback($args)
{
		$id = edd_sanitize_key( $args['id'] );
		$edd_option = edd_get_option( $args['id'] )  ;
		?>

		<?php
    	echo '<div class="edd-maxbutton-field">
    		   <input type="hidden" name="edd_settings[' . $id . ']" value="' . $edd_option . '">';

    	echo '<div class="edd-maxbutton-preview" id="edd-maxbutton-preview-' . $id . '">';
    	if (intval($edd_option) > 0)
    	{
    		$button = MaxButtons\MB()->getClass('button');
    		$button_args = array('id' => $edd_option, 'style' => 'inline');

			echo $button->shortcode($button_args);
    	}
    	echo '</div>';

		$hidden = ($edd_option > 0) ? '' : ' hidden ';
		$nonce = wp_create_nonce('maxajax');

			echo  '<div class="edd-maxbutton"><button type="button" class="button edd_media_button"
    			data-callback="mbEddButton" data-nonce="' . $nonce . '" data-parent="#wpbody" name="' . $id . '">' . __('Select a button') . '</button>';

    	echo '<button class="button remove-button remove-button-' . $id  . ' ' . $hidden . '" type="button" data-remove="' . $id . '" >' . __('Remove','maxbuttons-pro') . '</button></div>';

    	echo '<label class="description" >' . wp_kses_post($args['desc']) . '</label>';
    	echo ' </div>'; // closing statement
}
?>
