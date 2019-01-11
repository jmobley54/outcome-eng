
jQuery(document).ready(function ($) {

	$(document).on('click', '#edd_purchase_form #edd_purchase_submit button[type=submit]', function (e)
	{
		e.preventDefault();
		$('#edd_purchase_form #edd_purchase_submit input[type=submit]').trigger('click'); //trigger('click');
	});




});
