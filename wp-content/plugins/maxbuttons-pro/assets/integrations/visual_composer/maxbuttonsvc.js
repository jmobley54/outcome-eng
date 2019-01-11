jQuery(document).ready(function(jq) {
	$ = jq; // to counter noConflict bandits

doFontCheck($); // check for google web fonts
var vc_mm = new window.maxFoundry.maxMedia;

$(document).on('click', '.vc_media_button', function ()
{
	vc_mm.init({callback: visualcomposer_button, useShortCodeOptions: false});
	vc_mm.openModal();

});

function visualcomposer_button(id)
{
	var button = $('.media-popup .maxbutton-' + id).parents('.shortcode-container').children().clone();
	$('.vc_wrapper-param-type-maxbutton_select .button_preview').html(button);
	$('.vc_wrapper-param-type-maxbutton_select .maxbutton_select input').val(id);

	vc_mm.close();
}



}); /* END OF JQUERY */
