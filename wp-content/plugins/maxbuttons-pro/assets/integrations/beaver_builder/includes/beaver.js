jQuery(document).ready(function(jq) {
	$ = jq; // to counter noConflict bandits

	var bb_mm = new window.maxFoundry.maxMedia;

  $(document).on('click','#maxbutton-picker', function ()
	{

		bb_mm.init({callback: beaverBuilder, useShortCodeOptions: false});
		bb_mm.openModal();
	});

	function beaverBuilder(id)
	{
		var button = jQuery('.media-popup .maxbutton-' + id).parents('.shortcode-container').children().clone();
		var button_id = id;

		//var button = jQuery('.media-buttons .maxbutton-' + id).parents('.shortcode-container').children().clone();
		jQuery("#beaver_maxbutton_preview").html(button);
		jQuery('#mb-beaver-field input[type="hidden"]').val(id);

		bb_mm.close();
		return false;

	}



}); /* END OF JQUERY */
