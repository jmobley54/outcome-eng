jQuery(document).ready(function() {
	var cfsadmin = jQuery.noConflict();
	var selected_contact = "", rmdni = false, protocol;
	cfsadmin( function() {
	protocol = ('https:' == window.location.protocol ? 'https://' : 'http://');
		cfsadmin("body").on( "click", ".save_custom_fields",function() {
			var buttonspan = cfsadmin( this ).parent();
			var error = false;
			if ( rmdni == false ) {
				var customfsarray = new Array();
				var customfs = {};
				cfsadmin(".one-custom-field").each(function( index ) {
					if (cfsadmin(this).children(".cfid").val()!=''&&cfsadmin(this).children(".cfname").val())
					{
					customfs = {};
					if ( cfsadmin( this ).children( ".cfrequired" ).val() == '1' ) {
						var thisreq = 'true';
					}
					else {
						var thisreq = 'false';
					}
					customfs.id = cfsadmin( this ).children( ".cfid" ).val();
					customfs.priority = cfsadmin( this ).children( ".cfpriority" ).val();
					customfs.name = cfsadmin( this ).children( ".cfname" ).val();
					customfs.required = thisreq;
					customfs.type = cfsadmin( this ).children( ".cfid" ).attr("data-type");
					customfs.warning = cfsadmin( this ).children( ".cfwarning" ).val();
					customfs.minlength = cfsadmin( this ).children( ".cfminlength" ).val();
						customfsarray.push( customfs );
						cfsadmin( this ).css( "background", "" );
					}
					else {
						cfsadmin( this ).css( "background", "rgb(255, 185, 185)" );
					}
				});
				var options = [
					customfsarray
				];
				if ( error == false ) {
					rmdni = true;
					cfsadmin(buttonspan).html('<img width="20" style="margin-left:30px;" src="'+cfs_admin_datas.plugin_directory+'img/spreloader.gif">');
					var data = {
							action: 'ajax_cfs_admin',
							cfscmd: 'addcustomfields',
							options: JSON.stringify( options )
							};
					checker = setTimeout( function() {
						if ( cfsadmin( buttonspan ).html() != '<div class="acfield save_custom_fields button button-primary button-large">' + cfs_admin_datas.languages.save + '</div>' ) {
							cfsadmin(buttonspan).html( '<div class="acfield save_custom_fields button button-primary button-large">' + cfs_admin_datas.languages.save + '</div><div class="error-msg">' + cfs_admin_datas.languages.saveerror + '</span>' )
						}
					}, 15000 );
					cfsadmin.post( cfs_admin_datas.path, data, function( response ) {
						if ( response.indexOf( "success" ) >= 0 ) {
							clearTimeout( checker );
							cfsadmin( buttonspan ).html( '<span class="success_message">' + cfs_admin_datas.languages.successsave + '</span>' );
							setTimeout( function() { cfsadmin( buttonspan ).html( '<div class="acfield save_custom_fields button button-primary button-large">' + cfs_admin_datas.languages.save + '</div>' ) }, 2000 );
						}
					rmdni = false;
					});
				};
			}
		});

		cfsadmin( document ).on( "click", ".add_custom_fields", function () {
			cfsadmin(".custom_field_section .fields").append("<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='' placeholder=''><input type='text' data-type='text' class='cfid cfstooltip' data-title='ID of input field, eg.: FNAME' value='' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" placeholder='* ID'><input type='text' class='cfname cfstooltip' value='' data-title='Name of custom field, eg.: First Name' placeholder='* Placeholder Name'><input type='text' class='cfwarning cfstooltip' data-title='Warning text for the field if it is required, eg.: Firstname field is mandatory' value='' placeholder='Warning'><input type='text' class='cfminlength cfstooltip' data-title='Minimum character length for required field' value='' placeholder='0'><input type='checkbox' class='cfrequired cfstooltip' data-title='Check this if the field is mandatory' value='0'><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='"+cfs_admin_datas.plugin_directory+"img/delete.png'></div>");
			initialize_tooltips();
		});
		cfsadmin(document).on("click", ".add_custom_fields_radio", function () {
			cfsadmin(".custom_field_section .fields").append("<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='' placeholder=''><input type='text' data-type='radio' class='cfid cfstooltip' data-title='ID of radio field, eg.: GENDER' value='' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" placeholder='* ID'><input type='text' class='cfname cfstooltip longinput' value='' data-title='Name and value pair for custom field, eg.: Female:female,Male:male' placeholder='* Female:female,Male:male'><div class='minorspace'></div><input type='checkbox' class='cfrequired cfstooltip' data-title='Check this if the field is mandatory' value='0'><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='"+cfs_admin_datas.plugin_directory+"img/delete.png'></div>");
			initialize_tooltips();
		});
		cfsadmin(document).on("click", ".add_custom_fields_checkbox", function () {
			cfsadmin(".custom_field_section .fields").append("<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='' placeholder=''><input type='text' data-type='checkbox' class='cfid cfstooltip' data-title='ID of checkbox field, eg.: POLICY' value='' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" placeholder='* ID'><input type='text' class='cfname cfstooltip longinput' value='' data-title='Description for checkbox, eg.: Accept Terms and Conditions' placeholder='* Description for checkbox'><div class='minorspace'></div><input type='checkbox' class='cfrequired cfstooltip' data-title='Check this if the field is mandatory' value='0'><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='"+cfs_admin_datas.plugin_directory+"img/delete.png'></div>");
			initialize_tooltips();
		});
		cfsadmin(document).on("click", ".add_custom_fields_textarea", function () {
			cfsadmin(".custom_field_section .fields").append("<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='' placeholder=''><input type='text' data-type='textarea' class='cfid cfstooltip' data-title='ID of textarea field, eg.: Description' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" value='' placeholder='* ID'><input type='text' class='cfname cfstooltip' value='' data-title='Placeholder for custom field, eg.: Description' placeholder='* Placeholder Description'><input type='text' class='cfwarning cfstooltip' data-title='Warning text for the field if it is required, eg.: Description field is mandatory' value='' placeholder='Warning'><input type='text' class='cfminlength cfstooltip' data-title='Minimum character length for required field' value='' placeholder='0'><input type='checkbox' class='cfrequired cfstooltip' data-title='Check this if the field is mandatory' value='0'><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='"+cfs_admin_datas.plugin_directory+"img/delete.png'></div>");
			initialize_tooltips();
		});
		cfsadmin(document).on("click", ".add_custom_fields_select", function () {
			cfsadmin(".custom_field_section .fields").append("<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='' placeholder=''><input type='text' data-type='select' class='cfid cfstooltip' data-title='ID of radio field, eg.: FRUITS' value='' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" placeholder='* ID'><input type='text' class='cfname cfstooltip longinput' value='' data-title='Name and value pair for custom field, eg.: Select from the list,Apple:apple,Orange:orange,Lemon:lemon' placeholder='* Select from the list,Apple:applevalue,Orange:orangevalue,Lemon:lemonvalue' class='longinput'><div class='minorspace'></div><input type='checkbox' class='cfrequired cfstooltip' data-title='Check this if the field is mandatory' value='0'><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='"+cfs_admin_datas.plugin_directory+"img/delete.png'></div>");
			initialize_tooltips();
		});
		cfsadmin(document).on("click", ".add_custom_fields_hidden", function () {
			cfsadmin(".custom_field_section .fields").append("<div class='one-custom-field'><input type='text' maxlength='3' onkeyup=\"this.value = this.value.replace(/[^0-9]/g,\'\');\" class='cfpriority cfstooltip' data-title='Priority number in display order' value='' placeholder=''><input type='text' data-type='hidden' class='cfid cfstooltip' data-title='ID of hidden field, eg.: SIGNUP' value='' onkeyup=\"this.value = this.value.replace(/[^a-zA-Z0-9]/g,\'\');\" placeholder='* ID'><input type='text' class='cfname cfstooltip longinput' value='' data-title='Value of the field, eg.: blog name' placeholder='* blog name' class='longinput'><div class='emptycheckbox'></div><img class='remove_cfield cfstooltip' data-title='Remove Custom Field' src='"+cfs_admin_datas.plugin_directory+"img/delete.png'></div>");
			initialize_tooltips();
		});
		cfsadmin(document).on("click", ".remove_cfield", function () {
			cfsadmin(this).parent().remove();
		})		
	if (!cfsadmin("link[href='" + protocol + "netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.css']").length) cfsadmin('head').append('<link rel="stylesheet" href="' + protocol + 'netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.css" type="text/css" />');
		cfsadmin( ".w8contact_form .preview" ).each(function( index ) {
			create_color_picker( parseInt( index ) + 1 );
		})
		cfsadmin( ".sliderpanel" ).each(function( index ) {
		var value = cfsadmin(this).attr("data-value")+"%";
		var min = cfsadmin(this).attr("data-min");
		var max = cfsadmin(this).attr("data-max");
		var thisslider = cfsadmin(this).attr("data-input");
		cfsadmin( this ).slider({
		range: "min",
		value: value.replace("%","").replace("%",""),
		min: parseInt(min),
		max: parseInt(max),
		slide: function( event, ui ) {
			cfsadmin("."+thisslider).val(ui.value+"%");
		}
		});
	})
		cfsadmin( ".fontsliderpanel" ).each(function( index ) {
		var value = cfsadmin(this).attr("data-value")+"px";
		var min = cfsadmin(this).attr("data-min");
		var max = cfsadmin(this).attr("data-max");
		var thisslider = cfsadmin(this).attr("data-input");
		cfsadmin( this ).slider({
		range: "min",
		value: value.replace("px","").replace("px",""),
		min: parseInt(min),
		max: parseInt(max),
		slide: function( event, ui ) {
			cfsadmin("."+thisslider).val(ui.value+"px");
		}
		});
	})
	function initialize_tooltips()
	{
		cfsadmin(".cfstooltip").tooltip({
		items: "[data-title]",
		content: function () {
			return cfsadmin(this).data("title");
		},
			  hide: { effect: "explode", duration: 0 }
		});
	}
	initialize_tooltips();
	cfsadmin("#add-contact").click(function(){
	var counter = parseInt(cfsadmin(".one-contact").length)+1;
		cfsadmin("#saved-contacts").append('<div class="one-contact"><div class="one-contact-inner"><div class="one-contact-photo contact-element"><span class="contact-number">'+counter+'.</span><div class=\"imageelement\"><div id=\"uploaded_contact'+counter+'\"><input id=\"contact'+counter+'-upload\" class=\"button add-button\" type=\"button\" value=\"'+cfs_admin_datas.languages.addimage+'\" /></div></div><input type="checkbox" checked id="status-contact'+counter+'" class="contact-status"><label for="status-contact'+counter+'">Active</label></div><div class="one-contact-name contact-element"><input type="text" class="one-contact-name cfstooltip" data-title="'+cfs_admin_datas.languages.name+'" value="" placeholder="'+cfs_admin_datas.languages.entername+'"></div><div class="one-contact-title contact-element"><input type="text" class="one-contact-title cfstooltip" data-title="'+cfs_admin_datas.languages.title+'" value="" placeholder="'+cfs_admin_datas.languages.entertitle+'"></div><div class="one-contact-subject contact-element"><input type="text" class="one-contact-subject cfstooltip" data-title="'+cfs_admin_datas.languages.subject+'" value="" placeholder="'+cfs_admin_datas.languages.entersubject+'"></div><div class="one-contact-email contact-element"><input type="text" class="one-contact-email cfstooltip" data-title="'+cfs_admin_datas.languages.eaddress+'" value="'+cfs_admin_datas.adminemail+'" placeholder="'+cfs_admin_datas.languages.emailaddress+'"></div><div class="one-contact-comment contact-element"><textarea class="one-contact-message cfstooltip" data-title="'+cfs_admin_datas.languages.shortdesc+'" placeholder="'+cfs_admin_datas.languages.description+'"></textarea></div><div class="one-contact-social-elements"><div class="one-contact-social"><i class="fa fa-facebook"></i><input type="text" class="one-contact-facebook" value="" placeholder="'+cfs_admin_datas.languages.facebookurl+'"></div><div class="one-contact-social"><i class="fa fa-google-plus"></i><input type="text" class="one-contact-googleplus" value="" placeholder="'+cfs_admin_datas.languages.googleplusurl+'"></div><div class="one-contact-social"><i class="fa fa-twitter"></i><input type="text" class="one-contact-twitter" value="" placeholder="'+cfs_admin_datas.languages.twitterurl+'"></div><div class="one-contact-social"><i class="fa fa-pinterest"></i><input type="text" class="one-contact-pinterest" value="" placeholder="'+cfs_admin_datas.languages.pinteresturl+'"></div><div class="one-contact-social"><i class="fa fa-linkedin"></i><input type="text" class="one-contact-linkedin" value="" placeholder="'+cfs_admin_datas.languages.linkedinurl+'"></div><div class="one-contact-social"><i class="fa fa-skype"></i><input type="text" class="one-contact-skype" value="" placeholder="'+cfs_admin_datas.languages.skypeurl+'"></div><div class="one-contact-social"><i class="fa fa-tumblr"></i><input type="text" class="one-contact-tumblr" value="" placeholder="'+cfs_admin_datas.languages.tumblrurl+'"></div><div class="one-contact-social"><i class="fa fa-flickr"></i><input type="text" class="one-contact-flickr" value="" placeholder="'+cfs_admin_datas.languages.flickrurl+'"></div><div class="one-contact-social"><i class="fa fa-foursquare"></i><input type="text" class="one-contact-foursquare" value="" placeholder="'+cfs_admin_datas.languages.foursquareurl+'"></div><div class="one-contact-social"><i class="fa fa-youtube"></i><input type="text" class="one-contact-youtube" value="" placeholder="'+cfs_admin_datas.languages.youtubeurl+'"></div></div><div class="cfs-autoreply"><h4>'+cfs_admin_datas.languages.autoreply+'</h4><hr><span>'+cfs_admin_datas.languages.autoreplydesc+'</span><div class="cfs-ar-fields"><input type="text" class="cfs-ar-sendername" placeholder="'+cfs_admin_datas.languages.sendername+'" value=""><input type="text" class="cfs-ar-sendermail" placeholder="'+cfs_admin_datas.languages.senderemailaddress+'" value="">@'+window.location.hostname+'<textarea class="cfs-ar-autoreply" placeholder="'+cfs_admin_datas.languages.message+'"></textarea></div></div></div><div class="del-button"><input class="button delete-contact" type="button" value="'+cfs_admin_datas.languages.del+'"></div></div>');
		initialize_tooltips();
		cfsadmin(function(){cfsadmin("#contact"+counter+"-upload" ).pmu({"button":"#contact"+counter+"-upload","target":"#uploaded_contact"+counter,"container":"<div class=\"contact"+counter+"_container\"><img src=\"[content]\"><input type=\"hidden\" class=\"contact"+counter+"_image contact-photo\" name=\"contact"+counter+"\" value=\"objImageUrl\"><div><input class=\"remove_customimage_button button remove-button\" id=\"contact"+counter+"-remove\" type=\"button\" data-addid=\"contact"+counter+"\" value=\""+cfs_admin_datas.languages.remove+"\" /></div></div>","mode":"insert","indexcontainer":"","type":"single","callback":function(){}});
		cfsadmin(document).on("click","#contact"+counter+"-remove",function(){cfsadmin("#uploaded_contact"+counter).html("<div class=\"imageelement\"><div id=\"uploaded_contact"+counter+"\"><input id=\"contact"+counter+"-upload\" class=\"button add-button\" type=\"button\" value=\""+cfs_admin_datas.languages.addimage+"\" /></div></div>");return false;});})
	})
	
	function delete_contact(contact)
	{
	var thiselem = cfsadmin(contact).parent().parent();
			cfsadmin(thiselem).css({
			"-webkit-transform": "scale(0)",
			"-webkit-transition-duration": "600ms",
			"-webkit-transition-timing-function": "ease-out",
			"-moz-transform": "scale(0)",
			"-moz-transition-duration": "600ms",
			"-moz-transition-timing-function": "ease-out",
			"-ms-transform": "scale(0)",
			"-ms-transition-duration": "600ms",
			"-ms-transition-timing-function": "ease-out",
			"opacity":"0"
			});		
			setTimeout(function(){cfsadmin(thiselem).remove();},600);
			setTimeout(function(){
			cfsadmin(".contact-number").css({
			"-webkit-transform": "scale(0.5)",
			"-webkit-transition-duration": "300ms",
			"-webkit-transition-timing-function": "ease-out",
			"-moz-transform": "scale(0.5)",
			"-moz-transition-duration": "300ms",
			"-moz-transition-timing-function": "ease-out",
			"-ms-transform": "scale(0.5)",
			"-ms-transition-duration": "300ms",
			"-ms-transition-timing-function": "ease-out",
			"opacity":"0"
			});
				cfsadmin( ".contact-number" ).each(function( index ) {
					cfsadmin(contact).html(parseInt(index+1)+".");
				})
				},900)
			setTimeout(function(){
			cfsadmin(".contact-number").css({
			"-webkit-transform": "scale(1)",
			"-webkit-transition-duration": "300ms",
			"-webkit-transition-timing-function": "ease-out",
			"-moz-transform": "scale(1)",
			"-moz-transition-duration": "300ms",
			"-moz-transition-timing-function": "ease-out",
			"-ms-transform": "scale(1)",
			"-ms-transition-duration": "300ms",
			"-ms-transition-timing-function": "ease-out",
			"opacity":"1"
			});
			},1200);
			setTimeout(function(){save_contacts();},600);
	}
	
	cfsadmin(document).on("click",".delete-contact",function(){
	selected_contact = cfsadmin(this);
		cfsadmin( "#dialog-confirm2" ).dialog( "open" );
	})
	cfsadmin(document).on("click","#log-clear",function(){
		cfsadmin( "#dialog-confirm" ).dialog( "open" );
	})
	function clear_logs()
	{
		var data = {
			action: 'ajax_cfs',
			cfscmd: 'clearlogs'
		};
		cfsadmin("#cfs-log-entries").html('<img src="'+cfs_admin_datas.plugin_directory+'img/ajax-loader.gif">');
			cfsadmin.post(cfs_admin_datas.path, data, function(response) 
			{
				if (response=="fail") 
				{
				}
				else if(response=="empty")
				{
				}
				else
				{
					cfsadmin("#cfs-log-entries").html("<h4>"+cfs_admin_datas.languages.alllogs+"</h4>");
				}
			})	
	}

	cfsadmin( "#dialog-confirm2" ).dialog({
	  resizable: false,
	  height:220,
	  autoOpen: false,
	  modal: true,
	  buttons: [{
		text: ""+cfs_admin_datas.languages.deletecontact+"",
		click: function() {
		delete_contact(selected_contact);
		  cfsadmin( this ).dialog( "close" );
		}
		},
		{
		text: ""+cfs_admin_datas.languages.cancel+"",
		click: function() {
		  cfsadmin( this ).dialog( "close" );
		}
		}],
		close: function () {
		  cfsadmin( this ).dialog( "close" );
		}
	  });
	cfsadmin( "#dialog-confirm" ).dialog({
	  resizable: false,
	  height:220,
	  autoOpen: false,
	  modal: true,
	  buttons: [{
		text: ""+cfs_admin_datas.languages.clearlogs+"",
		click: function() {
		clear_logs();
		  cfsadmin( this ).dialog( "close" );
		}
		},
		{
		text: ""+cfs_admin_datas.languages.cancel+"",
		click: function() {
		  cfsadmin( this ).dialog( "close" );
		}
		}],
		close: function () {
		  cfsadmin( this ).dialog( "close" );
		}
	});

	cfsadmin(document).on("click",".one-log-row",function(){
		if (cfsadmin(this).children(".one-log-details").length>0)
		{
			if (cfsadmin(this).children(".one-log-details").css("height")=="0px") cfsadmin(this).children(".one-log-details").css("height",cfsadmin(this).children(".one-log-details")[0].scrollHeight+"px");
			else cfsadmin(this).children(".one-log-details").css("height","0px");
		}
	})
	cfsadmin("#log-display").click(function(){
				var data = {
			action: 'ajax_cfs',
			cfscmd: 'getlogs'
		};
		cfsadmin("#cfs-log-entries").html('<img src="'+cfs_admin_datas.plugin_directory+'img/ajax-loader.gif">');
			cfsadmin.post(cfs_admin_datas.path, data, function(response) 
			{
				if (response=="fail") 
				{
				}
				else if(response=="empty")
				{
					cfsadmin("#cfs-log-entries").html("<h4>"+cfs_admin_datas.languages.nolog+"</h4>");
				}
				else
				{
					cfsadmin("#cfs-log-entries").html(response);
				}
			})
	})
	
	 cfsadmin( "body" ).on( "click", ".cfrequired", function() {
		if ( cfsadmin( this ).val() == 0 || cfsadmin( this ).val() == "false" ) {
			cfsadmin( this ).val( "1" );
			cfsadmin( this ).attr( "checked", "checked" );
		}
		else {
			cfsadmin( this ).val( "0" );
			cfsadmin( this ).removeAttr( "checked", "" );
		}
	})
	
	function save_contacts()
	{
			var contactdatas = '[';
		cfsadmin( ".one-contact" ).each(function( index ) {
		var em = cfsadmin(this).children().children().find(".one-contact-email").val().escapeSpecialChars();
			contactdatas += '{';
			contactdatas += '"name":"'+cfsadmin(this).children().children().find(".one-contact-subject").val().replace('"', '\"')+'",';
			if (cfsadmin(this).children().children().find(".contact-status").attr('checked')) contactdatas += '"status":"1",';
			else contactdatas += '"status":"0",';
			contactdatas += '"email":"'+em+'",';
			contactdatas += '"emaildomain":"'+em+'",';
			contactdatas += '"title":"'+cfsadmin(this).children().children().find(".one-contact-name").val().escapeSpecialChars() + '",';
			contactdatas += '"subtitle":"'+cfsadmin(this).children().children().find(".one-contact-title").val().escapeSpecialChars() + '",';
			contactdatas += '"text":"'+cfsadmin(this).children().children().find(".one-contact-message").val().replace(/\r\n|\r|\n/g,"<br />").replace(/"/g,"'").escapeSpecialChars() + '",';
			if ( cfsadmin( this ).children().children().find( ".contact-photo" ).val() != undefined ) {
				contactdatas += '"photo":"' + cfsadmin( this ).children().children().find( ".contact-photo" ).val() + '",';
			}
			else {
				contactdatas += '"photo":"",';
			}
			contactdatas += '"facebook":"'+cfsadmin(this).children().children().find(".one-contact-facebook").val().escapeSpecialChars() + '",';
			contactdatas += '"googleplus":"'+cfsadmin(this).children().children().find(".one-contact-googleplus").val().escapeSpecialChars() + '",';
			contactdatas += '"twitter":"'+cfsadmin(this).children().children().find(".one-contact-twitter").val().escapeSpecialChars() + '",';
			contactdatas += '"pinterest":"'+cfsadmin(this).children().children().find(".one-contact-pinterest").val().escapeSpecialChars() + '",';
			contactdatas += '"linkedin":"'+cfsadmin(this).children().children().find(".one-contact-linkedin").val().escapeSpecialChars() + '",';
			contactdatas += '"skype":"'+cfsadmin(this).children().children().find(".one-contact-skype").val().escapeSpecialChars() + '",';
			contactdatas += '"tumblr":"'+cfsadmin(this).children().children().find(".one-contact-tumblr").val().escapeSpecialChars() + '",';
			contactdatas += '"flickr":"'+cfsadmin(this).children().children().find(".one-contact-flickr").val().escapeSpecialChars() + '",';
			contactdatas += '"foursquare":"'+cfsadmin(this).children().children().find(".one-contact-foursquare").val().escapeSpecialChars() + '",';
			contactdatas += '"youtube":"'+cfsadmin(this).children().children().find(".one-contact-youtube").val().escapeSpecialChars() + '",';
			contactdatas += '"arsendername":"'+cfsadmin(this).children().children().find(".cfs-ar-sendername").val().escapeSpecialChars() + '",';
			contactdatas += '"arsenderemail":"'+cfsadmin(this).children().children().find(".cfs-ar-sendermail").val().escapeSpecialChars() + '",';
			contactdatas += '"arsendermessage":"'+cfsadmin(this).children().children().find(".cfs-ar-autoreply").val().replace(/\r\n|\r|\n/g,"<br />").replace(/"/g,"'").escapeSpecialChars() + '"';
			contactdatas += '}';
			if ( parseInt( index + 1 ) < cfsadmin( ".one-contact" ).length ) {
				contactdatas += ',';
			}
		})
		contactdatas += ']';
		cfsadmin("#setting_contacts").val( contactdatas );
		cfsadmin("#contacts-form").submit();	
	}
	String.prototype.escapeSpecialChars = function() {
    return this.replace(/\\n/g, "\\n")
               .replace(/\\'/g, "\\'")
               .replace(/\\"/g, '\\"')
               .replace(/\\&/g, "\\&")
               .replace(/\\r/g, "\\r")
               .replace(/\\t/g, "\\t")
               .replace(/\\b/g, "\\b")
               .replace(/\\f/g, "\\f")
			   .replace(/\t+/g, "");
	};
	cfsadmin("#save-contact").click(function(){
		save_contacts();
	})
	
	function create_color_picker(num)
	{
	function rgbToHex(R,G,B) { return toHex(R)+toHex(G)+toHex(B); }
	function toHex(n) {
	 n = parseInt(n,10);
	 if (isNaN(n)) return "00";
	 n = Math.max(0,Math.min(n,255));
	 return "0123456789ABCDEF".charAt((n-n%16)/16)
		  + "0123456789ABCDEF".charAt(n%16);
	}

	function cutHex(h) { return (h.charAt(0)=="#") ? h.substring(1,7) : h}
	function hexToR(h) { return parseInt((cutHex(h)).substring(0,2),16) }
	function hexToG(h) { return parseInt((cutHex(h)).substring(2,4),16) }
	function hexToB(h) { return parseInt((cutHex(h)).substring(4,6),16) }

	function setBgColorById(id,sColor) {
	 var elem;
	 if (document.getElementById) {
	  if (elem=document.getElementById(id)) {
	   if (elem.style) elem.style.backgroundColor=sColor;
	  }
	 }
	}

		var bCanPreview = new Array();
		bCanPreview[num] = true; // can preview

		// create canvas and context objects
//		var canvas = document.getElementById('picker'+num);
		var canvas = cfsadmin('#picker'+num);
		var ctx = canvas[0].getContext('2d');

		// drawing active image
		var image = new Image();
		image.onload = function () {
			ctx.drawImage(image, 0, 0, image.width, image.height); // draw the image on the canvas
		}

		// select desired colorwheel
		var imageSrc = cfs_admin_datas.plugin_directory+"/img/colorpicker.png";

		image.src = imageSrc;

		cfsadmin('#picker'+num).mousemove(function(e) { // mouse move handler
			if (bCanPreview[num]) {
				// get coordinates of current position
				var canvasOffset = cfsadmin(canvas).offset();
				var canvasX = Math.floor(e.pageX - canvasOffset.left);
				var canvasY = Math.floor(e.pageY - canvasOffset.top);
				// get current pixel
				var imageData = ctx.getImageData(canvasX, canvasY, 1, 1);
				var pixel = imageData.data;

				// update preview color
				var pixelColor = "rgb("+pixel[0]+", "+pixel[1]+", "+pixel[2]+")";
				cfsadmin('.preview'+num).css('backgroundColor', pixelColor);
				cfsadmin(".radio-colorpicker" + num).val(pixelColor);
				// update controls
				cfsadmin('#rVal'+num).val(pixel[0]);
				cfsadmin('#gVal'+num).val(pixel[1]);
				cfsadmin('#bVal'+num).val(pixel[2]);
				cfsadmin('#rgbVal'+num).val(pixel[0]+','+pixel[1]+','+pixel[2]);

				var dColor = pixel[2] + 256 * pixel[1] + 65536 * pixel[0];
				cfsadmin('#hexVal'+num).val('#' + ('0000' + dColor.toString(16)).substr(-6));
			}
		});
		cfsadmin('#picker'+num).click(function(e) { // click event handler
			bCanPreview[num] = !bCanPreview[num];
	   });
		cfsadmin('.preview'+num).click(function(e) { // preview click
			cfsadmin('.colorpicker'+num).fadeToggle("slow", "linear");
			if (cfsadmin(this).css("background-color").indexOf("rgb") != -1)
			{
			var rgb_colors = cfsadmin(this).css("background-color").replace("rgb(","").replace(")","").split(",");
				cfsadmin('#rVal'+num).val(rgb_colors[0]);
				cfsadmin('#gVal'+num).val(rgb_colors[1]);
				cfsadmin('#bVal'+num).val(rgb_colors[2]);
				cfsadmin('#rgbVal'+num).val(rgb_colors[0]+', '+rgb_colors[1]+', '+rgb_colors[2]);
				var d2Color = rgb_colors[2] + 256 * rgb_colors[1] + 65536 * rgb_colors[0];
				cfsadmin('#hexVal'+num).val('#' + rgbToHex(rgb_colors[0],rgb_colors[1],rgb_colors[2]));
			}
			else cfsadmin('#hexVal'+num).val('');
			bCanPreview[num] = true;
		});
		cfsadmin('#hexVal'+num).keydown(function(event){
	  if(event.keyCode == 13)
		{
		event.preventDefault();
		if (cfsadmin(this).val()=="") {
			cfsadmin('#rVal'+num).val("");
			cfsadmin('#gVal'+num).val("");
			cfsadmin('#bVal'+num).val("");
			cfsadmin('#rgbVal'+num).val("");
			cfsadmin('.preview'+num).css('backgroundColor', "");
			cfsadmin(".radio-colorpicker" + num).val("transparent");
		}
		else
		{
			if (cfsadmin(this).val().indexOf("#") == -1) cfsadmin(this).val("#"+cfsadmin(this).val().substr(0,6));
			var hexpixelColor = cfsadmin(this).val();
			cfsadmin('#rVal'+num).val(hexToR(hexpixelColor));
			cfsadmin('#gVal'+num).val(hexToG(hexpixelColor));
			cfsadmin('#bVal'+num).val(hexToB(hexpixelColor));
			cfsadmin('#rgbVal'+num).val(hexToR(hexpixelColor)+', '+hexToG(hexpixelColor)+', '+hexToB(hexpixelColor));
			cfsadmin('.preview'+num).css('backgroundColor', hexpixelColor);
			cfsadmin(".radio-colorpicker" + num).val(hexpixelColor);
		}
		return false;
		}
		});
		cfsadmin(document).mouseup(function (e)
		{
			var container = cfsadmin(".colorpicker");

			if (!container.is(e.target)
				&& container.has(e.target).length === 0)
			{
				 cfsadmin('.colorpicker'+num).fadeOut("slow", "linear");
			}
		});
	cfsadmin(function() {
		cfsadmin( ".colorpicker" ).draggable();
	});
	cfsadmin( ".w8image-picker-select" ).imagepicker();
	}
	});
});
jQuery( window ).load( function() {
	jQuery( "#wpbody-content .wrap" ).css( "visibility", "visible" );
	jQuery( "#screen_preloader" ).fadeOut( "slow", function() {
		jQuery( this ).remove();
	});
})