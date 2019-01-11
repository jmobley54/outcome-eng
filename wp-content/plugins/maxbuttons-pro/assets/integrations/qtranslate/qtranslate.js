jQuery(document).ready(function ($) {

	var updatetranslation = function () {
			// maintain previous state of form updated to prevent annoying messages when leaving page without updating.
			var fm = window.maxFoundry.maxadmin.form_updated;
			$('.mb_tab input[type="text"]').trigger('change');
			window.maxFoundry.maxadmin.form_updated = fm;

	}

	var qtx = qTranslateConfig.qtx;
	qtx.addLanguageSwitchListener(updatetranslation);

	// init
	updatetranslation();
});
