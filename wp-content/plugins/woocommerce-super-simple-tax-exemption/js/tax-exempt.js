jQuery(document).ready(function($) {
	$('#tax_exempt_id_field').hide();

$('#tax_exempt_checkbox').on('click', function (event) {
	$('#tax_exempt_id_field').slideDown('slow');
});
});