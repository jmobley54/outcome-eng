		var mbEddButton = null;

jQuery(document).ready(function(jq) {
			$ = jq; // to counter noConflict bandits

		var active_window = null;
		var edd_mm = new window.maxFoundry.maxMedia;


		$(document).on('click', '.edd_media_button', activeButtonWindow);
		$(document).on('click', '.edd-maxbutton-field .remove-button', removeButton);

		function activeButtonWindow(e)
		{

			var target = $(e.target);
			var id = target.attr('name');
			active_window = id;

			edd_mm.init({callback: mbEddButton, useShortCodeOptions: false});
			edd_mm.openModal();
		}

		function removeButton(e)
		{
			var target = $(e.target);
			var id = target.data('remove');

			$('#edd-maxbutton-preview-' + id).html('');
			$('input[name="edd_settings[' + id + ']"]').val(0);

			$(target).addClass('hidden');
		}

		mbEddButton = function(id, target)
		{
			var button = $('.media-popup .maxbutton-' + id).parents('.shortcode-container').children().clone();
			$('#edd-maxbutton-preview-' + active_window).html(button);
			$('input[name="edd_settings[' + active_window + ']"]').val(id);
			$('.remove-button-' + active_window).removeClass('hidden');
			edd_mm.close();
			return false;
		}


});
