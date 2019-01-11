
jQuery(document).ready(function($) {
	doFontCheck($);
	$(document).on('media_button_content_buttons_load', {jq: $}, fontEvent );

}); /* END OF JQUERY */

	//var fonts_loaded = [];

function fontEvent(e)
{
	doFontCheck(e.data.jq);
}

function doFontCheck($)
{
	$('.mb-text, .mb-text2').each(function () {
		var ff = $(this).css('fontFamily');
		var weight = $(this).css('fontWeight');

		if (typeof $(this).data('nofont') !== 'undefined')
		{
			return;
		}

		if (typeof ff === 'undefined') // can happen in page builders
			return;

		ff = ff.replace(/\'|\"|/g,''); // Chrome returns font family with ' in it.

		font_found = false;
		for (i= 0; i < fonts_loaded.length; i++) // prevent double loading
		{
			if (fonts_loaded[i] == ff)
			{
				font_found = true;
				return;
			}
		}

		if (! font_found)
		{
			fonts_loaded.push(ff);
	 		mbpro_loadFontFamilyStylesheet(ff);
		}
	});
}

function mbpro_loadFontFamilyStylesheet(font_family) {
	if (font_family.indexOf(",", font_family) >= 0)
		return; // a non-google font with comma, probably set by theme. This can happen if button doens't have font preference.

	var font_family_url = mbpro_getFontFamilyUrl(font_family);
	if (font_family_url != "") {

		jQuery("head").append("<link rel='stylesheet' type='text/css' href='" + font_family_url + "' />");
	}
}

function mbpro_getFontFamilyUrl(font_family) {
	var system_fonts = ['', 'Arial', 'Courier New', 'Georgia', 'Tahoma', 'Times New Roman', 'Trebuchet MS',
	'Verdana' ];

	if (system_fonts.indexOf(font_family) >= 0)
		return ""; // not further action needed

	var base_url = '//fonts.googleapis.com/css?family=';
	var family = font_family.replace(/\s/g, '+');
	return base_url + family;

}
