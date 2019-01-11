;function wplnst_paywall_show($) {
	$('#wplnst-paywall').wplnst_lightboxed({
		centered : true,
		lightboxSpeed : 0,
		overlaySpeed  : 0,
		overlayCSS : {
			background: '#000',
			opacity: .7
		}
	});
}

;jQuery(document).ready(function($) {
	
	$('.wplnst-paywall-link').click(function() {
		$('.wplnst-row-actions').removeClass('visible');
		wplnst_paywall_show($);
		return false;
	});
	
	$('#wplnst-only-pro-remove').click(function() {
		$('#wplnst-paywall').trigger('close');
		return false;
	});
	
});