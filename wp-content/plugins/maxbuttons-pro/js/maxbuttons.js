
var maxAdminPro;

jQuery(document).ready(function($) {
	maxAdminPro = function ()
	{
 		this.pack_loader_process = 0;
 		this.uploader = null;

	}

if (typeof(maxAdmin) !== 'undefined')
	maxAdminPro.prototype = new maxAdmin();

maxAdminPro.prototype.init = function () {

	if (mbpro_options.colorPalette !== '')
		this.colorPalettes = mbpro_options.colorPalette;
 	maxAdmin.prototype.init.call(this);

	if ($('#license_not_active').length > 0)
	{
		$('.form-actions').html( $('#license_not_active').children().clone() );
	}

	// packs tabs and handling
	$('#packs_tab a').on('click', this.togglePacksTabs);
	$(document).on('get-free-packs', $.proxy(this.loadFreePacks, this) );
	$(document).on('click','.load_pack_preview', $.proxy(this.loadPackPreview, this) );

	// export form error handling
	$('#export-form').on('submit', $.proxy(this.checkExportForm, this));

 	// button importer from packs
 	$(document).on('change', '.pack-list input[type="checkbox"]', $.proxy(this.togglePackButton, this) );
 	$(document).on('click', '.pack-list-header button[name="import_button"]', $.proxy(this.importPackButtons, this) );
 	$(document).on('click', '.pack-list .shortcode-container a', function (e) {
 		e.preventDefault();
 		$(e.target).parents('label').trigger('click');  // fix for not being able to click on the button itself to select button
 	});
 	this.togglePackButton();

 	$('#add_data_attr').on('click', $.proxy(this.add_data_attr, this ) );


 }

maxAdminPro.prototype.update_color = function(event, ui, color)
{
	this.saveIndicator(true);
	var target = $(event.target);
	var id = $(target).attr('id');
	$('#' + id).val(color);

	if(id.indexOf('icon') !== -1)
			window.maxFoundry.maxIcons.updateIcon(target);
	else
		maxAdmin.prototype.update_color.call(this,event,ui, color);

}

maxAdminPro.prototype.togglePackButton = function ()
{
	if ( $('.pack-list').length == 0)
		return;  // el not defined

	var number = $('.pack-list input[type="checkbox"]:checked').length;

	$('.pack-list-header .count').text(number);

	if (number == 0)
		$('.pack-list-header button').prop('disabled', true);
	else
		$('.pack-list-header button').prop('disabled', false);

}

maxAdminPro.prototype.importPackButtons = function (e)
{

	var number_imported = 0;
	var ajax_actions = 0;

	var buttons = $('.pack-list input[type="checkbox"]:checked');
	ajax_actions = buttons.length;
	var self = this;

	buttons.each( function (index)
	{
		var id = $(this).val();
		var data = $('.pack-list div[data-button="' + id + '"]').text();

		data = {
			action: 'import_button',
			data: data,
		}

		$.post({
			url: maxajax.ajax_url,
			data: data,
			success: $.proxy( function(response) {
				number_imported++;

				this.importPackButtonDone(response, ajax_actions, number_imported);
			}, self),
		});
	});

}

maxAdminPro.prototype.importPackButtonDone = function (response, total, number_done )
{
	var data = JSON.parse(response);
	if (typeof data.button == 'undefined' || data.button <= 0)
	{
	}

	// all done.
	if (total == number_done)
	{

		$('input[name="import_modal"]').trigger('click'); // badly trigger import done modal

		$('.pack-list input[type="checkbox"]:checked').prop('checked', false);
		this.togglePackButton();

	}

}

maxAdminPro.prototype.updateAnchorText = function (target)
{

	var text = $('#text').val();
	var text2 = $('#text2').val();


 	var button_id = this.button_id;


	$(".mb-text").text(text);
	$(".mb-text2").text(text2);
	if (text2 == '')
		$('.mb-text2').hide();
	else
	{
		$('.mb-text2').show();
		$('.mb-text2').css('display','block');
	}

}

maxAdminPro.prototype.updateFont = function (target)
{
	window.maxFoundry.maxfonts.checkFonts();
}


maxAdminPro.prototype.togglePacksTabs = function(e) {
	e.preventDefault();
	var active = $('#packs_tab a.nav-tab-active');
	var clicked = $(e.target);

	$(active).removeClass('nav-tab-active');
	$(clicked).addClass('nav-tab-active');

	var active_screen = $(active).data('screen');
	var clicked_screen = $(clicked).data('screen');

	$('#' + active_screen).hide();
	$('#' + clicked_screen).show();

	if (clicked_screen == 'free_packs_screen')
	{
		$(document).trigger('get-free-packs');

	}
}

maxAdminPro.prototype.loadFreePacks = function () {

		if (this.pack_loader_process !== 0)
		{ return false; }

		this.pack_loader_process = 1; // loading
 		var nonce = $('#free-pack-nonce').text();
		var data = { 'remote_action': 'get_free_overview',
					 'action': 'pack_request',
					 'nonce': nonce,
				   };
		$.ajax({
			url: maxajax.ajax_url,
			type: 'GET',
			dataType: "html",
			data: data,
			success: $.proxy(this.updateFreePacksScreen, this)
		});
};

maxAdminPro.prototype.updateFreePacksScreen = function(results, status)
{
	this.pack_loader_process = 0;

 	$('.free_packs .pack_container').html(results);
 	$('.free_packs .loading').hide();
 	$(".free_preview").hide();
 	$(".free_packs").show();

	$(document).off("click", ".free_packs .use");
	$(document).on("click",".free_packs .use", $.proxy(this.getFreePackDownloadLink, this) );

}

maxAdminPro.prototype.loadPackPreview = function(e)
{
	e.preventDefault();
  	var pack_url = $(e.target).closest('[data-pack]').data("pack");
  	var pack_id = $(e.target).closest('[data-pack]').data('packname');
  	//if (typeof pack

	this.pack_loader_process = 2; // loading pack
  	var nonce = $('#free-pack-nonce').text();

		var data = { 'remote_action': 'get_free_pack_preview',
					 'action': 'pack_request',
					 'pack_url': pack_url,
					 'pack': pack_id,
					 'nonce': nonce,
				   };
		$.ajax({
			url: maxajax.ajax_url,
			type: 'GET',
			dataType: "html",
			data: data,
			success: $.proxy(this.showPackPreview, this, pack_id)
		});
}

maxAdminPro.prototype.showPackPreview = function (pack_id, results, status)
{
	this.pack_loader_process = 0;
	$(".free_preview .results").html(results);

	// for now hide the use this button button
	//$(".free_preview .results .use-pack-button").hide();

 	$(".free_packs").fadeOut();
 	$(".free_preview").fadeIn();

 	// load custom fonts
 	window.maxFoundry.maxfonts.checkFonts();

 	$(".free_preview .use").data("pack", pack_id);

	// this prevents binding too much events ( every load ) to this.
 	$(document).off("click", ".free_preview .use");
 	$(document).off("click",".free_preview .close");

 	$(document).on("click",".free_preview .close", function () {
	 	$(".free_packs").fadeIn();
	 	$(".free_preview").fadeOut();
 	});
 	$(document).on("click",".free_preview .use", $.proxy(this.getFreePackDownloadLink, this) );

}

maxAdminPro.prototype.getFreePackDownloadLink = function (e)
{
	if ($(e.target).hasClass('disabled'))
		return false;


	var pack_id = $(e.target).data("pack");
	this.pack_loader_process = 3; // download

  	var nonce = $('#free-pack-nonce').text();
	var data = { 'remote_action': 'get_free_download_link',
				 'action': 'pack_request',
				 'pack': pack_id,
				 'nonce': nonce,
			   };

	$.ajax({
			url: maxajax.ajax_url,
			type: 'GET',
			dataType: "html",
			data: data,
			success: $.proxy(this.packDownload, this, pack_id)
		});
}

maxAdminPro.prototype.packDownload = function (pack_id, results)
{
	try	{
		results = JSON.parse(results);
	} catch (e) { }

	if (typeof results.status !== 'undefined')
	{
		alert('An error occured: ' + results.error_message); return;
	}
	var port = '';
	if (window.location.port !== '' && typeof window.location.port !== 'undefined')
		var port = ':' + window.port;

	var url = window.location.protocol + '//'  +  window.location.host + port  + window.location.pathname;

  	window.location.href = url + '?page=maxbuttons-controller&action=pack&id=' + pack_id;

}

maxAdminPro.prototype.deletePack = function (modal)
{
	var target = modal.target;
	var href = $(modal.target).attr('href');
	$(modal.currentModal).find('.yes').attr('href', href);

}

maxAdminPro.prototype.checkExportForm = function(e)
{
	//e.preventDefault();
	if ( $('input[name="pack_name"]').val() == '')
	{
		 $('input[name="pack_name"]').css("border","1px solid #ff0000");
		 return false;
	}
	return true;
}

maxAdminPro.prototype.add_data_attr = function (e)
{

	var data_line = $('.option.anchor_data').first().clone();
	var attr_num  = $('.data_attr_plus').data('attrnum');

	$(data_line).find('.note').remove();

	$.each( $(data_line).find('input'), function (index, el)
	{

		$(el).attr('id', $(el).attr('id') + '_' + attr_num);
		$(el).attr('name', $(el).attr('name') + '_add[' + attr_num + ']');
		$(el).val('');


	});

	$('.option.add_data').before (data_line) ;

	attr_num = attr_num + 1;
	$('.data_attr_plus').data('attrnum', attr_num);
}


}); /* END OF JQUERY */
