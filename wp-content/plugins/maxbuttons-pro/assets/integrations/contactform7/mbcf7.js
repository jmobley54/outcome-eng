jQuery(document).ready(function(jq) {
	$ = jq;

	var cf7_mm = new window.maxFoundry.maxMedia;

	var mbtag = $('#tag-generator-list a.button[href*="maxbutton"]');
	$(mbtag).addClass('cf7_media_button');
	$(mbtag).removeClass('thickbox');
	$(mbtag).removeAttr('href');

	$(document).on('click','.cf7_media_button', function (e)
	{
		e.preventDefault();
		mbtrans.windowtitle = mbcf7.title;
		cf7_mm.init({callback: mbInsertCF7, useShortCodeOptions: false});

		cf7_mm.openModal();

		$(document).on('media_button_content_buttons_load', updateTitle)
	});

	function updateTitle()
	{
		cf7_mm.maxmodal.currentModal.find('.modal_content .hint').text(mbcf7.note);
  }

	function mbInsertCF7(id)
	{
			$('input[name="cf7_maxbutton"]').val('[cf7_maxbutton id:' + id + ']');
			$('#mbcf7_insert_tag').trigger('click');

			cf7_mm.close();

			return false;
	}

});
