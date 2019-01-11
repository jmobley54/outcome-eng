/**********************************
* contact_form_slider.js
* title: Contact Form Slider
* description: Display a highly customizable Contact Form
* author: Pantherius
* website: http://pantherius.com
**********************************/ 


jQuery(document).ready(function() {
	if (cfs_params.bodyanim!=""&&cfs_params.bodyanim!="disabled") {
		if (cfs_params.bgtarget=="") jQuery('body').wrapInner('<div id="cfs_wrapper"><div id="cfs_wrapper_inside"></div></div>');
		else jQuery(''+cfs_params.bgtarget+'').wrapInner('<div id="cfs_wrapper"><div id="cfs_wrapper_inside"></div></div>');
		jQuery("#wpadminbar").appendTo("body");
		jQuery("#cfs_wrapper_inside>.modal-survey-container").appendTo("body");
		if ( cfs_params.excludeelements != "" ) {
			var ee = cfs_params.excludeelements.split( "," );
			jQuery.each( ee,function( index, value ) {
				jQuery( value ).appendTo( "body" );
			});
		}
	}
	if (jQuery('#bglock').length==0) jQuery("body").prepend('<div id="bglock" onclick="jQuery(\'body\').cfslider(\'remove\')"> </div>');
});

jQuery(window).load(function() {
if ( typeof cfs_params !== 'undefined')
{
	jQuery('body').cfslider();
}
});

(function( jQuery ){
    var methods = {
    init : function(options) {
	defaults = { 
			"customfields":[],
			"customcontact":[],
			"hide_icon":"false",						//hide all icons, you can combine it with the auto open option - true or false
			"auto_open":"false",						//auto open the Like Box Slider at the bottom of the page - true or false
			"captcha":"image",							//set the captcha style - image, math, hidden field or disabled
			"sendcopy":"false",							//enable sending copy to the sender email address - true or false
			"disableimage":"false",						//hide the contact image - true or false
			"direction":"left",							//position of the Contact Slider - left or right
			"closeable":"true",							//display close icon in the corner
			"transparency":"90",						//transparency for the locked screen/transparent background in percentages
			"icon_size":"medium",						//icon size for the Like Box - small, medium or large
			"lock_screen":"true",						//set the screen locked with a transparent background when the slider opens
			"vertical_distance":"50",					//vertical position of the Like Box icon related to the top in percentages
			"dofsu":"false",							//display once for the same user - true or false
			"shake":"0",								//shake animation time for Like Box in millisecs, eg: 5000 for 5sec
			"scheme":"light",							//light or dark
			"icon_url":"",								//absolute url of the icon for Like Box
			"skin":"default",
			"placeholder_name":"Enter your name",
			"placeholder_email":"Enter your email address",
			"placeholder_message":"Type your message...",
			"placeholder_captcha":"Enter the numbers",
			"placeholder_sendcopy":"Send a copy to my email address",
			"sendbutton_text":"SEND",
			"failed_text":"FAILED",
			"reverse_header":"false",
			"bordered_photo":"#d1d2d3",
			"photo_style":"false",
			"animationtype":"Bounce",
			"flat":"false",
			"bodyanim":"cfs_perspectiveright",
			"fontfamily":"",
			"pfontsize":"",
			"headerfontsize":"",
			"subheaderfontsize":"",
			"buttonfontsize":"",
			"fieldfontsize":"",
			"height":"full",
			"bgtarget":"",
			"pfontweight":"",
			"headerfontweight":"",
			"subheaderfontweight":"",
			"buttonfontweight":"",
			"fieldfontweight":"",
			"background":"",
			"button_background":"",
			"button_background_hover":""
	  };
	if ( typeof cfs_params !== 'undefined') options = cfs_params;
	var options = jQuery.extend({}, defaults, options);
if (options.dom=='true'&&jQuery('body').cfslider('detectmob')==true)
{
}
else
{
var lastScrollTop = 0, opened = false, block_autoopen = false, fcs_enabled_on_this_page = '', customdatas = {}, customfieldsarray = new Array(), fieldname, thisdata, protocol;
opened_slider = '';
boxtype = '';
parentbox = '';
protocol = ('https:' == window.location.protocol ? 'https://' : 'http://');
if (options.skin=='default') {space = 8;bspace = 6;}
else {space = 4;bspace = 2;}
if (!jQuery("link[href='" + protocol + "netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.css']").length) jQuery('head').append('<link rel="stylesheet" href="' + protocol + 'netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.css" type="text/css" />');

function getString(number_value)
{
	return number_value.toString();
}
	if ((jQuery("body").html()!=undefined))
	{
		var contact_form_slider_box = '';
		var contact_form_slider_closeable = '';
		var fbroot = '';
		var fbicon1 = '';
		var fbicon2 = '';
		var fbicon3 = '';
		var fbcs_scheme_name = 'light';
		if (options.scheme!=undefined) {
			if (options.scheme=='dark')
			{
				fbcs_scheme_name = options.scheme;
			}
			if (options.scheme=='light'||options.scheme=='')
			{
				fbcs_scheme_name = options.scheme;
			}
		}
		if (options.hide_icon=='true') fbicon2 = '<h2 style="display:none" class="fbicon_left icon_'+options.icon_size+'"></h2>';
		if (options.direction=='left'&&options.hide_icon=='false') 
		{
			if (options.icon_url==undefined||options.icon_url=='') fbicon2 = '<h2 style="left:-70px;top:'+options.vertical_distance+'%" class="fbicon_left icon_'+options.icon_size+'"></h2>';
			else {
			var imgSrc = options.icon_url;
			var _width, _height;
			jQuery("<img/>").attr("src", imgSrc + "?" + new Date().getTime()).load(function() {
				_width = this.width; 
				_height = this.height;
			 });
			fbicon2 = '<h2 style="left:-'+(_width+50)+'px;top:'+options.vertical_distance+'%" class="fbcs_left"><img src="'+options.icon_url+'" /></h2>';
			}
		}
		if (options.direction=='right'&&options.hide_icon=='false') 
		{
			if (options.icon_url==undefined||options.icon_url=='') fbicon2 = '<h2 style="right:-70px;top:'+options.vertical_distance+'%" class="fbicon_right icon_'+options.icon_size+'"></h2>';
			else {
			var imgSrc = options.icon_url;
			var _width, _height;
			jQuery("<img/>").attr("src", imgSrc + "?" + new Date().getTime()).load(function() {
				_width = this.width; 
				_height = this.height;
			 });
			fbicon2 = '<h2 style="right:-'+(_width+50)+'px;top:'+options.vertical_distance+'%" class="fbcs_right"><img src="'+options.icon_url+'" /></h2>';
			}
		}
		if (options.closeable=='true') contact_form_slider_closeable = '<a class="close_contact_slider"></a>';
		var socialicons = '';
		if (options.facebook!='') socialicons += '<a target="_blank" href="'+options.facebook+'"><i class="fa fa-facebook"></i></a>';
		if (options.googleplus!='') socialicons += '<a target="_blank" href="'+options.googleplus+'"><i class="fa fa-google-plus"></i></a>';
		if (options.twitter!='') socialicons += '<a target="_blank" href="'+options.twitter+'"><i class="fa fa-twitter"></i></a>';
		if (options.pinterest!='') socialicons += '<a target="_blank" href="'+options.pinterest+'"><i class="fa fa-pinterest"></i></a>';
		if (options.linkedin!='') socialicons += '<a target="_blank" href="'+options.linkedin+'"><i class="fa fa-linkedin"></i></a>';
		if (options.skype!='') socialicons += '<a target="_blank" href="'+options.skype+'"><i class="fa fa-skype"></i></a>';
		if (options.tumblr!='') socialicons += '<a target="_blank" href="'+options.tumblr+'"><i class="fa fa-tumblr"></i></a>';
		if (options.flickr!='') socialicons += '<a target="_blank" href="'+options.flickr+'"><i class="fa fa-flickr"></i></a>';
		if (options.foursquare!='') socialicons += '<a target="_blank" href="'+options.foursquare+'"><i class="fa fa-foursquare"></i></a>';
		if (options.youtube!='') socialicons += '<a target="_blank" href="'+options.youtube+'"><i class="fa fa-youtube"></i></a>';
		var defs = '';
		if (options.customcontact!='')
		{
		var cimgs = [];
		var subjects = '<select class="form-field" id="contact-form-slider-subject">';
			jQuery.each( options.customcontact, function( index, value ) {
				subjects += '<option value="' + value.name + '">' + value.name + '</option>';
				cimgs.push( value.photo );
			});
			subjects += '</select>';
			preload(cimgs);
		}
function display_contact(selected,intime,outtime)
{
jQuery(".cform-contact").css({
			"-webkit-transform": "scale(0.5)",
			"-webkit-transition-duration": ""+outtime+"ms",
			"-webkit-transition-timing-function": "ease-out",
			"-moz-transform": "scale(0.5)",
			"-moz-transition-duration": ""+outtime+"ms",
			"-moz-transition-timing-function": "ease-out",
			"-ms-transform": "scale(0.5)",
			"-ms-transition-duration": ""+outtime+"ms",
			"-ms-transition-timing-function": "ease-out",
			"opacity":"0"
			});
			setTimeout(function(){
			jQuery.each(options.customcontact,function( index, value ) {
				if (index==selected)
				{
					jQuery(".cform-title .cfheader").html(value.title);
					jQuery(".cform-msg").html(value.text);
					jQuery(".cform-subtitle").html(value.subtitle);
					socialicons = '';
					if (value.facebook!=''&&value.facebook!=undefined) socialicons += '<a target="_blank" href="'+value.facebook+'"><i class="fa fa-facebook"></i></a>';
					if (value.googleplus!=''&&value.googleplus!=undefined) socialicons += '<a target="_blank" href="'+value.googleplus+'"><i class="fa fa-google-plus"></i></a>';
					if (value.twitter!=''&&value.twitter!=undefined) socialicons += '<a target="_blank" href="'+value.twitter+'"><i class="fa fa-twitter"></i></a>';
					if (value.pinterest!=''&&value.pinterest!=undefined) socialicons += '<a target="_blank" href="'+value.pinterest+'"><i class="fa fa-pinterest"></i></a>';
					if (value.linkedin!=''&&value.linkedin!=undefined) socialicons += '<a target="_blank" href="'+value.linkedin+'"><i class="fa fa-linkedin"></i></a>';
					if (value.skype!=''&&value.skype!=undefined) socialicons += '<a target="_blank" href="skype:'+value.skype+'"><i class="fa fa-skype"></i></a>';
					if (value.tumblr!=''&&value.tumblr!=undefined) socialicons += '<a target="_blank" href="'+value.tumblr+'"><i class="fa fa-tumblr"></i></a>';
					if (value.flickr!=''&&value.flickr!=undefined) socialicons += '<a target="_blank" href="'+value.flickr+'"><i class="fa fa-flickr"></i></a>';
					if (value.foursquare!=''&&value.foursquare!=undefined) socialicons += '<a target="_blank" href="'+value.foursquare+'"><i class="fa fa-foursquare"></i></a>';
					if (value.youtube!=''&&value.youtube!=undefined) socialicons += '<a target="_blank" href="'+value.youtube+'"><i class="fa fa-youtube"></i></a>';
					if ( socialicons != "" ) {
						jQuery( ".cfslider-social-icons" ).html( socialicons );
					}
					else {
						jQuery( ".cfslider-social-icons" ).css( "display", "none" );
					}
					if ( value.photo != "" ) {
						jQuery( ".cform-photo" ).html( '<img src="' + value.photo + '">' );
						jQuery( ".cform-photo" ).css( "width", "40%" );
					}
					else {
						jQuery( ".cform-photo" ).html( '' );
						jQuery( ".cform-photo" ).css( "width", "0px" );						
					}
				}
				subjects += '<option value="' + value.name + '">' + value.name + '</option>';
				jQuery(".cform-contact").css({
			"-webkit-transform": "scale(1)",
			"-webkit-transition-duration": ""+intime+"ms",
			"-webkit-transition-timing-function": "ease-out",
			"-moz-transform": "scale(1)",
			"-moz-transition-duration": ""+intime+"ms",
			"-moz-transition-timing-function": "ease-out",
			"-ms-transform": "scale(1)",
			"-ms-transition-duration": ""+intime+"ms",
			"-ms-transition-timing-function": "ease-out",
			"opacity":"1"
			});
		})
		if (options.bordered_photo!="false") {jQuery(".cform-photo img").css("border","1px solid "+options.bordered_photo);}
		if (options.photo_style=="door") jQuery(".cform-photo img").css({"border-top-left-radius": "100%","border-top-right-radius": "100%"});
		if (options.photo_style=="badge") jQuery(".cform-photo img").css({"border-bottom-left-radius": "100%","border-bottom-right-radius": "100%"});
		if (options.photo_style=="leaf-right") jQuery(".cform-photo img").css({"border-bottom-left-radius": "100%","border-bottom-right-radius": "100%","border-top-left-radius": "100%"});
		if (options.photo_style=="leaf-left") jQuery(".cform-photo img").css({"border-bottom-left-radius": "100%","border-bottom-right-radius": "100%","border-top-right-radius": "100%"});
		if (options.photo_style=="bubble-right") jQuery(".cform-photo img").css({"border-top-left-radius": "100%","border-top-right-radius": "100%",
"border-bottom-left-radius": "100%"});
		if (options.photo_style=="bubble-left") jQuery(".cform-photo img").css({"border-top-left-radius": "100%","border-top-right-radius": "100%",
"border-bottom-right-radius": "100%"});
		if (options.photo_style=="rounded-left") jQuery(".cform-photo img").css({"border-top-left-radius": "100%","border-bottom-left-radius": "100%"});
		if (options.photo_style=="rounded-right") jQuery(".cform-photo img").css({"border-top-right-radius": "100%","border-bottom-right-radius": "100%"});
		if (options.photo_style=="rounded") jQuery(".cform-photo img").css({"border-radius": "100%"});
			}, outtime);
}
		function preload(arrayOfImages) {
			jQuery(arrayOfImages).each(function(){
				jQuery('<img/>')[0].src = this;
			});
		}
		function isValidEmailAddress(emailAddress) {
			var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
			return pattern.test(emailAddress);
		};
		
		function display_custom_field( field ) {
			if ( field.type == "text" ) {
				return ( '<div><input type="text" name="' + field.id + '" id="cfs-customfields-' + field.id + '" class="cfs-customfield form-field ' + field.id + '" placeholder="' + field.name + '"></div>' );
			}
			if ( field.type == "radio" ) {
				var returninput = "";
				jQuery.each( field.name.split( "," ), function( rindex, rvalue ) {
						var subelement = rvalue.split( ":" );
						if ( subelement[ 1 ] == undefined ) subelement[ 1 ] = "";
						returninput += '<input type="radio" id="cfs-customfields-' + field.id + rindex + '" value="' + subelement[ 1 ] + '" name="' + field.id + '" class="cfs-customfield form-field"><label for="cfs-customfields-' + field.id + rindex + '">' + subelement[ 0 ] + '</label>';
					});
				return ( '<div class="' + field.id + '">' + returninput + '</div>');
			}
			if ( field.type == "checkbox" ) {
				return ( '<div class="left_indent ' + field.id + '"><input type="checkbox" name="' + field.id + '" id="cfs-customfields-' + field.id + '" class="cfs-customfield form-field"><label for="cfs-customfields-' + field.id + '">' + field.name + '</label></div>' );
			}
			if ( field.type == "textarea" ) {
				return ( '<div><textarea placeholder="' + field.name + '" id="cfs-customfields-' + field.id + '" class="cfs-customfield form-field ' + field.id + '"></textarea></div>' );
			}
			if ( field.type == "select" ) {
				var returninput = "";
				jQuery.each( field.name.split( "," ), function( rindex, rvalue ) {
						var subelement = rvalue.split( ":" );
						if ( subelement[ 1 ] == undefined ) subelement[ 1 ] = "";
						returninput += '<option value="' + subelement[ 1 ] + '">' + subelement[ 0 ] + '</option>';
					});
				return ( '<div><select name="' + field.id + '" id="cfs-customfields-' + field.id + '" class="cfs-customfield form-field ' + field.id + '">' + returninput + '</select></div>' );
			}
			if ( field.type == "hidden" ) {
				return ( '<input type="hidden" name="' + field.id + '" id="cfs-customfields-' + field.id + '" class="cfs-customfield form-field ' + field.id + '" value="' + field.name + '">' );
			}
		}
		
		function beat_item(element)
		{
			jQuery(element).css({
						"-webkit-transform": "scale(0.9)",
						"-webkit-transition-duration": "300ms",
						"-webkit-transition-timing-function": "ease-out",
						"-moz-transform": "scale(0.9)",
						"-moz-transition-duration": "300ms",
						"-moz-transition-timing-function": "ease-out",
						"-ms-transform": "scale(0.9)",
						"-ms-transition-duration": "300ms",
						"-ms-transition-timing-function": "ease-out"
						});
						setTimeout(function(){
				jQuery(element).css({
			"-webkit-transform": "scale(1)",
			"-webkit-transition-duration": "200ms",
			"-webkit-transition-timing-function": "ease-out",
			"-moz-transform": "scale(1)",
			"-moz-transition-duration": "200ms",
			"-moz-transition-timing-function": "ease-out",
			"-ms-transform": "scale(1)",
			"-ms-transition-duration": "200ms",
			"-ms-transition-timing-function": "ease-out"
			});
				jQuery(element).css({
			"-webkit-transform": "",
			"-webkit-transition-duration": "",
			"-webkit-transition-timing-function": "",
			"-moz-transform": "",
			"-moz-transition-duration": "",
			"-moz-transition-timing-function": "",
			"-ms-transform": "",
			"-ms-transition-duration": "",
			"-ms-transition-timing-function": ""
			});
		},300);
		}
		jQuery(document).on("change","#contact-form-slider-subject",function(){
		var selected = jQuery(this)[0].selectedIndex;
		display_contact(selected,500,300);
			});
		var photoblock = '', captcha_class = "";
		if ( options.captcha == "hidden" || options.captcha == "disabled" ) {
			captcha_class = "nocaptcha";
		}
		if (options.disableimage!="true") photoblock = '<div class="cform-photo"></div>';
		if (options.reverse_header=="false") {headcontent = photoblock+'<div class="cform-title"><div class="cfheader"></div><p class="cform-subtitle"></p><div class="cfslider-social-icons"></div></div>';}
		else {headcontent = '<div class="cform-title"><div class="cfheader"></div><p class="cform-subtitle"></p><div class="cfslider-social-icons"></div></div>'+photoblock;}
		var sendcopy = '';
		if (options.sendcopy=="true") sendcopy = '<div class="cfslider-sendcopy-block"><label for="cfslider-form-sendcopy">'+options.placeholder_sendcopy+'</label><input type="checkbox" checked id="cfslider-form-sendcopy" value="1"></div>';
		var captcha_block = '';
		if (options.captcha=="image") captcha_block = '<img id="cfs-form-captcha-image" src="'+cfs_params.plugin_directory+'/captcha.php"><input type="text" id="cfs-form-captcha" value="" placeholder="'+options.placeholder_captcha+'">';
		if (options.captcha=="math") captcha_block = '<br><span class="cfs-math-captcha"><span class="math-number1">'+Math.floor((Math.random() * 10) + 1)+'</span> + <span class="math-number2">'+Math.floor((Math.random() * 10) + 1)+'</span> = </span><input type="text" id="cfs-form-captcha" value="">';
		if (options.captcha=="hidden") captcha_block = '<input type="hidden" id="cfs-form-captcha" value="">';
		contact_form_slider_box = '<div class="cfs_hdline contact-form-slider-box"><div class="cfs_hdline_inside cfs_hdline_'+options.skin+'_'+fbcs_scheme_name+' cfs_hdline_top"><div class="cfs_hdline_line">'+contact_form_slider_closeable+'</div><div class="cfsbox" class="'+options.direction+'_side_fbbox"><div class="cform-contact"><div class="cform-head cform-'+options.direction+' '+fbcs_scheme_name+'">'+headcontent+'</div><span class="cform-msg message_'+fbcs_scheme_name+'"></span></div><div class="cfslider-form">'
		
		if ( options.customfields != "" ) {
			jQuery.each( options.customfields, function( index ) {
				jQuery.each( this, function( index2 ) {
				  if ( this.priority == 1 ) {
					  contact_form_slider_box += display_custom_field( this );
				  }
				})
			});
		}
			contact_form_slider_box += subjects;
		if ( options.customfields != "" ) {
			jQuery.each( options.customfields, function( index ) {
				jQuery.each( this, function( index2 ) {
				  if ( this.priority == 2 ) {
					  contact_form_slider_box += display_custom_field( this );
				  }
				})
			});
		}
			contact_form_slider_box += '<input type="text" name="name" class="cfslider-form-name form-field" placeholder="'+options.placeholder_name+'">';
		if ( options.customfields != "" ) {
			jQuery.each( options.customfields, function( index ) {
				jQuery.each( this, function( index2 ) {
				  if ( this.priority == 3 ) {
					  contact_form_slider_box += display_custom_field( this );
				  }
				})
			});
		}
			contact_form_slider_box += '<input type="text" name="email" class="cfslider-form-email form-field" placeholder="'+options.placeholder_email+'">';
		if ( options.customfields != "" ) {
			jQuery.each( options.customfields, function( index ) {
				jQuery.each( this, function( index2 ) {
				  if ( this.priority == 4 ) {
					  contact_form_slider_box += display_custom_field( this );
				  }
				})
			});
		}
			contact_form_slider_box += '<textarea placeholder="'+options.placeholder_message+'" class="cfslider-form-message form-field"></textarea>';
		if ( options.customfields != "" ) {
			jQuery.each( options.customfields, function( index ) {
				jQuery.each( this, function( index2 ) {
				  if ( this.priority > 4 || jQuery.isNumeric( this.priority ) == false ) {
					  contact_form_slider_box += display_custom_field( this );
				  }
				})
			});
		}
		
		contact_form_slider_box += '<div class="bottom-form-section ' + captcha_class + '">'+captcha_block+'<a href="" class="submit-button">'+options.sendbutton_text+'</a>'+sendcopy+'</div></div></div></div>'+fbicon2+'</div>';
		if (jQuery(".contact-form-slider-box").length!=1) {
		if (options.flat=="false") jQuery('body').prepend( contact_form_slider_box );
		else jQuery("#cfs-container").html( contact_form_slider_box );
		if ( options.icon_url == undefined || options.icon_url == '' ) {
			console.log('debug');
			if ( options.icon_image != "" ) {
			console.log('debug2');
				if ( options.direction == 'left' && options.hide_icon == 'false' ) {
			console.log('debug3');
					jQuery( ".cfs_hdline h2" ).css( "backgroundImage", "url(" + options.plugin_directory + "/templates/assets/img/icon" + options.icon_image + "-left.png)" );
				}
				if ( options.direction == 'right' && options.hide_icon == 'false' ) {
					jQuery( ".cfs_hdline h2" ).css( "backgroundImage", "url(" + options.plugin_directory + "/templates/assets/img/icon" + options.icon_image + "-right.png)" );
				}
			}
		}
		if (jQuery('#contact-form-slider-subject').size()==1) {
			if (jQuery( "#contact-form-slider-subject option:selected" ).text()=="") {jQuery( "#contact-form-slider-subject" ).css("display","none");}
		}
		if (captcha_block!='') jQuery(".bottom-form-section").css("text-align","right");
		var _width, _height;
		if (jQuery('.cfs_hdline h2 img').length>0)
		{
		jQuery("<img/>").attr("src", jQuery('.cfs_hdline h2 img').attr("src") + "?" + new Date().getTime()).load(function() {
			_width = this.width; 
			_height = this.height;
			jQuery('.cfs_hdline h2').css("width",_width+"px");
		 });
		 }
			jQuery('.cfs_hdline h2').css("margin-top","-"+jQuery('.cfs_hdline h2').height()/2+"px");
		if (options.fontfamily!="")
		{
		if (!jQuery("link[href='" + protocol + "fonts.googleapis.com/css?family="+options.fontfamily+"']").length) jQuery('head').append('<link rel="stylesheet" href="' + protocol + 'fonts.googleapis.com/css?family='+options.fontfamily+':400,700" type="text/css" />');
			jQuery(".cfs_hdline, .cfs_hdline input, .cfs_hdline textarea, .cfs_hdline select, .cfs_hdline a").css("fontFamily",options.fontfamily);
		}
			if (options.pfontsize!="") jQuery(".cform-msg, .cfslider-form-sendcopy").css("fontSize",options.pfontsize);
			if (options.headerfontsize!="") jQuery(".cform-head .cfheader").css("fontSize",options.headerfontsize);
			if (options.subheaderfontsize!="") jQuery(".cform-head p").css("fontSize",options.subheaderfontsize);
			if (options.buttonfontsize!="") jQuery(".cfslider-form .submit-button").css("fontSize",options.buttonfontsize);
			if (options.fieldfontsize!="") jQuery(".cfslider-form .form-field, #cfs-form-captcha").css("fontSize",options.fieldfontsize);

			if (options.pfontweight!="" ) jQuery(".cform-msg, .cfslider-form-sendcopy").css("fontWeight",options.pfontweight);
			if (options.headerfontweight!="") jQuery(".cform-head .cfheader").css("fontWeight",options.headerfontweight);
			if (options.subheaderfontweight!="") jQuery(".cform-head p").css("fontWeight",options.subheaderfontweight);
			if (options.buttonfontweight!="") jQuery(".cfslider-form .submit-button").css("fontWeight",options.buttonfontweight);
			if (options.fieldfontweight!="") jQuery(".cfslider-form .form-field, #cfs-form-captcha").css("fontWeight",options.fieldfontweight);

			if (options.background!="" && options.background!="off") jQuery(".cfs_hdline_inside").css("backgroundColor",options.background);
			if (options.defaultcolor!="" && options.defaultcolor!="off") jQuery(".cform-msg, .cform-head .cfheader, .cform-head p").css("color",options.defaultcolor);
			if (options.buttoncolor!="" && options.buttoncolor!="off") jQuery(".cfslider-form .submit-button").css("color",options.buttoncolor);

			if (options.button_background!="" && options.button_background!="off") jQuery(".cfslider-form .submit-button").css("background",options.button_background);
			if (options.button_background_hover!="" && options.button_background_hover!="off") {
				jQuery(".cfslider-form .submit-button").on( "mouseenter", function() {
					jQuery(this).css("background",options.button_background)
				});
				jQuery(".cfslider-form .submit-button").on( "mouseleave", function() {
					jQuery(this).css("background",options.button_background_hover)
				});
			}
			}
		if ((options.captcha=="disabled")||(options.captcha=="hidden")) jQuery(".cfslider-form .submit-button").css({"marginLeft":"0px","width":"100%"});
		else jQuery(".cfslider-form .submit-button").css({"width":"70px"});
		if (options.disableimage=="true") {jQuery(".cform-title").css({"width":"80%","margin":"0 auto"});jQuery(".cform-head").css("margin","0px");}
		if (jQuery('body').cfslider('detectmob')==true) {jQuery(".cfs_hdline").css("width","80%");}
		if (jQuery('#bglock').length)
		{
			jQuery('#bglock').css("filter","alpha(opacity="+getString(options.transparency)+")");
			jQuery('#bglock').css("-khtml-opacity",""+getString(parseInt(options.transparency)/100)+"");
			jQuery('#bglock').css("-moz-opacity",""+getString(parseInt(options.transparency)/100)+"");
			jQuery('#bglock').css("opacity",""+getString(parseInt(options.transparency)/100)+"");
		}
		if (options.direction=='left') 
		{
			jQuery('.contact-form-slider-box').css("left",'-'+(parseInt(jQuery(".contact-form-slider-box").width())+space)+'px');
			 jQuery( ".contact-form-slider-box h2" ).animate({
				left: (jQuery(".contact-form-slider-box").width()+bspace)+'px'
				}, 1500, "linear", function() {
				// Animation complete.
					if (parseInt(options.shake)>0) {setInterval(function(){if (opened==false) {jQuery( ".contact-form-slider-box h2" ).effect( "shake", {direction: "up"} );}},parseInt(options.shake));}
					if (options.shake=='heartbeat') jQuery(".contact-form-slider-box h2").addClass("heartbeat");
				});
		}
		if (options.direction=='right') 
		{
			jQuery('.contact-form-slider-box').css("right",'-'+(jQuery(".contact-form-slider-box").width())+'px');
			 jQuery( ".contact-form-slider-box h2" ).animate({
				right: (jQuery(".contact-form-slider-box").width())+'px'
				}, 1500, "linear", function() {
				// Animation complete.
					if (parseInt(options.shake)>0) {setInterval(function(){if (opened==false) {jQuery( ".contact-form-slider-box h2" ).effect( "shake", {direction: "up"} );}},parseInt(options.shake));}
					if (options.shake=='heartbeat') jQuery(".contact-form-slider-box h2").addClass("heartbeat");
				});
		}
		display_contact(0,0,0);
		function check_custom_field( priority ) {
			var cfret = false;
		if ( options.customfields != "" ) {
			jQuery.each( options.customfields, function( index ) {
				jQuery.each( this, function( index2 ) {
					if ( this.priority == priority || ( priority == 0 && jQuery.isNumeric( this.priority ) == false ) ) {
						if ( this.required == 'true' && ( this.type == 'text' || this.type == 'textarea' ) ) {
							if ( jQuery( "." + this.id ).val() != "" && jQuery( "." + this.id ).val().length >= this.minlength ) {
								jQuery( "." + this.id ).css( "border", "1px solid #B4EEEC" );
							}
							else {
								jQuery( "." + this.id ).css( "border", "1px solid rgb(160, 10, 10)" );
								beat_item( ".cfslider-form ." + this.id );
								jQuery( "." + this.id ).focus();
								cfret = true;
							}
						}
						if ( this.required == 'true' && ( this.type == 'select' ) ) {
						if ( jQuery( "." + this.id + " option:selected" ).val() != "" ) {
								jQuery( "." + this.id ).css( "border", "1px solid #B4EEEC" );
							}
							else {
								jQuery( "." + this.id ).css( "border", "1px solid rgb(160, 10, 10)" );
								beat_item( ".cfslider-form ." + this.id );
								jQuery( "." + this.id ).focus();
								cfret = true;
							}
						}
						if ( this.required == 'true' && ( this.type == 'radio' ) ) {
							if ( jQuery( ".cfslider-form input[name=" + this.id + "]:checked" ).val() != undefined ) {
								jQuery( "." + this.id ).css( "border", "none" );
							}
							else {
								jQuery( "." + this.id ).css( "border", "none" );
								beat_item( ".cfslider-form ." + this.id );
								jQuery( "." + this.id ).css( "border", "none" );
								jQuery( "." + this.id ).focus();
								cfret = true;
							}
						}
						if ( this.required == 'true' && ( this.type == 'checkbox' ) ) {
							if ( jQuery( "." + this.id + ">input[type=checkbox]:checked" ).prop( 'checked' ) == true ) {
								jQuery( "." + this.id ).css( "border", "none" );
							}
							else {
								jQuery( "." + this.id ).css( "border", "none" );
								beat_item( ".cfslider-form ." + this.id );
								jQuery( "." + this.id ).css( "border", "none" );
								jQuery( "." + this.id ).focus();
								cfret = true;
							}
						}
					}
				})
			});
		}			
			return cfret;
		}
		
		jQuery(".submit-button").on("click", function(event){
        event.preventDefault();
		if ( check_custom_field( 1 ) == true ) {
				return true;
		}
		if ( check_custom_field( 2 ) == true ) {
				return true;
		}
		if ( jQuery( ".cfslider-form-name" ).val() != "" && jQuery( ".cfslider-form-name" ).val().length >= 2 ) {
			jQuery( ".cfslider-form-name" ).css( "border", "1px solid #B4EEEC" );
		}
		else {
			jQuery( ".cfslider-form-name" ).css( "border", "1px solid rgb(160, 10, 10)" );
			beat_item( ".cfslider-form-name" );
			jQuery( ".cfslider-form-name" ).focus();
			return true;
		}
		if ( check_custom_field( 3 ) == true ) {
				return true;
		}
		if ( jQuery( ".cfslider-form-email" ).val() != "" && jQuery( ".cfslider-form-email" ).val().length >3 && ( isValidEmailAddress( jQuery( ".cfslider-form-email" ).val() ) ) ) {
			jQuery( ".cfslider-form-email" ).css( "border", "1px solid #B4EEEC" );
		}
		else {
			jQuery( ".cfslider-form-email" ).css( "border", "1px solid rgb(160, 10, 10)" );
			beat_item( ".cfslider-form-email" );
			jQuery( ".cfslider-form-email" ).focus();
			return true;
		}
		if ( check_custom_field( 4 ) == true ) {
				return true;
		}
		if ( jQuery( ".cfslider-form-message" ).val() != "" && jQuery( ".cfslider-form-message" ).val().length > 5 ) {
			jQuery( ".cfslider-form-message" ).css( "border", "1px solid #B4EEEC" );
		}
		else {
			jQuery( ".cfslider-form-message" ).css( "border", "1px solid rgb(160, 10, 10)" );
			beat_item( ".cfslider-form-message" );
			jQuery( ".cfslider-form-message" ).focus();
			return true;
		}
		if ( check_custom_field( 0 ) == true ) {
				return true;
		}
		var captcha_f = '';
		if (options.captcha=="image") captcha_f = jQuery("#cfs-form-captcha").val();
		if (options.captcha=="hidden") {if (jQuery("#cfs-form-captcha").val().length>0) return true;}
		if (options.captcha=="math") {
			if (jQuery("#cfs-form-captcha").val()!=parseInt(jQuery(".math-number1").text())+parseInt(jQuery(".math-number2").text())) {jQuery("#cfs-form-captcha").css("border","1px solid rgb(160, 10, 10)");beat_item("#cfs-form-captcha");jQuery("#cfs-form-captcha").focus(); return true;}
			else {jQuery("#cfs-form-captcha").css("border","1px solid #B4EEEC");}
		}
		var sendc = '';
		if (options.sendcopy=="true"&&jQuery("#cfslider-form-sendcopy").is(':checked')) sendc = "true";
		if (jQuery("#contact-form-slider-subject").find(":selected").text()=="") {var subj = "Contact";}
		else {var subj = jQuery("#contact-form-slider-subject").find(":selected").text();}
				var data = {
					action: 'ajax_cfs',
					cfscmd: 'sendmail',
					remail: jQuery("#contact-form-slider-subject").find(":selected").val(),
					semail: jQuery(".cfslider-form-email").val(),
					name: jQuery(".cfslider-form-name").val(),
					subject: subj,
					message: jQuery(".cfslider-form-message").val(),
					captcha: captcha_f,
					cmode: options.captcha,
					sendc: sendc
				};

				if ( options.customfields != '' ) {
					customfieldsarray = [];
					thisdata = {};
					jQuery.each( options.customfields, function( index, value ) {
						jQuery.each( value, function( ind, val ) {
							fieldname = val.id;
							if ( val.type == undefined ) val.type = "text";
							if ( val.type == "radio" || val.type == "select" ) {
								val.minlength = 0;
							}
							if ( val.type == "select" ) {
								thisdata[ fieldname ] = jQuery( "." + val.id + " option:selected" ).val();
							}
							else if ( val.type == "radio" ) {
								thisdata[ fieldname ] = jQuery( "." + val.id + " input[type=radio]:checked:first" ).val();
							}
							else if ( val.type == "checkbox" ) {
								thisdata[ fieldname ] = jQuery( "." + val.id + ">input[type=checkbox]:checked" ).prop( 'checked' );
							}
							else {
								thisdata[ fieldname ] = jQuery( "." + val.id ).val();
							}
							if ( thisdata[ fieldname ] == "undefined" ) {
								thisdata[ fieldname ] = "empty";
							}
							customfieldsarray.push( val.id );
						});
					});
							customdatas = jQuery.extend( {}, customdatas, thisdata); 
				}
				customdatas[ 'customfieldsarray' ] = customfieldsarray;
				data = jQuery.extend( {}, data, customdatas );
				jQuery(".cfslider-form .submit-button").html('<img src="'+options.plugin_directory+'/templates/assets/img/ajax-loader.gif">');
				jQuery.post(options.path, data, function(response) 
				{
					if (response=="captcha")
					{
						jQuery("#cfs-form-captcha").css("border","1px solid rgb(160, 10, 10)");beat_item("#cfs-form-captcha");jQuery("#cfs-form-captcha").focus(); 
						var d = new Date();
						jQuery("#cfs-form-captcha-image").attr("src", cfs_params.plugin_directory+"/captcha.php?"+d.getTime());
						jQuery(".cfslider-form .submit-button").text(options.sendbutton_text);
						return true;
					}
					else if (response=="success") 
					{
		jQuery(".cfs_hdline_inside").append("<div class='cfs-response-message'>"+options.success_message+"</div>");
		if (options.headerfontsize!="") jQuery(".cfs-response-message").css("fontSize",options.pfontsize);
		if (options.flat=="true") jQuery(".cfs-response-message").css("padding-top",(jQuery("#cfs-container").height()/2)-11+"px");
						jQuery(".cfsbox").css({
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
		setTimeout(function(){
						jQuery(".cfs-response-message").css({
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
						},300);
						setTimeout(function(){
						jQuery(".cfs-response-message").css({
						"-webkit-transform": "scale(0.5)",
						"-webkit-transition-duration": "50ms",
						"-webkit-transition-timing-function": "ease-out",
						"-moz-transform": "scale(0.5)",
						"-moz-transition-duration": "50ms",
						"-moz-transition-timing-function": "ease-out",
						"-ms-transform": "scale(0.5)",
						"-ms-transition-duration": "50ms",
						"-ms-transition-timing-function": "ease-out",
						"opacity":"0"
						});
						},3000);
						setTimeout(function(){
						jQuery(".cfsbox").css({
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
						},3100);
						setTimeout(function(){
						jQuery('body').cfslider('remove');jQuery(".cfslider-form input, .cfslider-form textarea").val('');
						jQuery(".cfslider-form .submit-button").text(options.sendbutton_text);
						},2500);
					}
					else
					{
						jQuery(".cfslider-form .submit-button").text(options.failed_text);
						setTimeout(function(){jQuery(".cfslider-form .submit-button").text(options.sendbutton_text);},2500);
					}
				});

		
		
		});
		jQuery(".open_cslider").click(function(event) {
        event.preventDefault();
			jQuery('body').cfslider('open');
		})
		jQuery(".close_cslider").click(function(event) {
        event.preventDefault();
			jQuery('body').cfslider('close');
		})
		jQuery(".close_contact_slider").click(function(event) {
        event.preventDefault();
			jQuery('body').cfslider('close');
		})
		jQuery(".hide_cslider").click(function(event) {
        event.preventDefault();
			jQuery('body').cfslider('hide');
		})
		jQuery(".show_cslider").click(function(event) {
        event.preventDefault();
			jQuery('body').cfslider('show');
		})
		jQuery(".contact-form-slider-box h2").click(function() {
		if (parentbox==""||parentbox==undefined) {var parent_div = jQuery(this).parent();}
		else {var parent_div = jQuery(parentbox);}
		parentbox = '';
		if (jQuery(parent_div).find("h2").attr("class").indexOf("left")!=-1) var thisdirection = "left";
		else var thisdirection = "right";
			if (thisdirection=='left') 
			{
				block_autoopen = true;
				if (parseInt(jQuery(parent_div).css("left").replace("px",""))<-5) {divscroller(parent_div);return true;}
				if (parseInt(jQuery(parent_div).css("left").replace("px",""))>=-5) {jQuery('body').cfslider('remove',parent_div);return true;}
			}
			if (thisdirection=='right')
			{
				block_autoopen = true;
				if (parseInt(jQuery(parent_div).css("right").replace("px",""))<-5) {divscroller(parent_div);return true;}
				if (parseInt(jQuery(parent_div).css("right").replace("px",""))>=-5) {jQuery('body').cfslider('remove',parent_div);return true;}
			}
		});
	}

	if (options.flat!="true")
	{
	jQuery('.cfsbox').jScrollPane({
			showArrows: true,
			autoReinitialise:true,
			autoReinitialiseDelay:10,
			verticalGutter:-15
		});
		if (options.height=='full') {
			jQuery(".cfs_hdline").css({"height":"100%","top":"0%"});
		}
	}
	jQuery(window).resize(function() {
	jQuery('body').cfslider('resize');
	});

	jQuery(window).scroll(function() 
	{
		var st = jQuery(this).scrollTop();
		if(jQuery(window).scrollTop() + jQuery(window).height() > jQuery(document).height() - ((jQuery(document).height()/100)*10)&&st > lastScrollTop&&opened==false)
		{
		if (jQuery(".contact-form-slider-box").length==1)
		{
				if ((parseInt(jQuery(".contact-form-slider-box").css("left").replace("px",""))<-5&&options.direction=='left')||(parseInt(jQuery(".contact-form-slider-box").css("right").replace("px",""))<-5&&options.direction=='right'))
				{
					if (options.auto_open=='true'&&(jQuery('body').cfslider('getCookie','cfs_hdline')!='1'||options.dofsu=='false')&&(jQuery('.contact-form-slider-box')!=undefined))
					{
						if (options.dofsu=='true') visitor_rememberer();
						opened = true;
						if (block_autoopen==false) opened_slider = jQuery('.contact-form-slider-box');divscroller(opened_slider);
					}
				}
		}
		}
		lastScrollTop = st;
	});
	
	function visitor_rememberer()
	{
		var fbcscparams = [ 'cfs_hdline', '1', 999, 'days' ];
		jQuery('body').cfslider('setCookie',fbcscparams);
	}
	
	function divscroller(boxtype)
	{
	//if (options.bodyanim)
	if (options.bodyanim!="disabled"&&options.flat=="false") jQuery("#cfs_wrapper_inside").addClass(options.bodyanim);
	var d = new Date();
	jQuery("#cfs-form-captcha-image").attr("src", cfs_params.plugin_directory+"/captcha.php?"+d.getTime());
	opened_slider = boxtype;
		jQuery(".cfs_hdline").css("z-index","10");
		jQuery(opened_slider).css("z-index","9999999");
		jQuery('.fb_ltr').css("width",jQuery(".cfsbox").width()-20+'px');
		jQuery("."+jQuery(boxtype).attr("class").replace("cfs_hdline ","")+" h2").removeClass("heartbeat");
		if (options.lock_screen=='true') 
		{
			jQuery("#bglock").fadeIn(1000);
		}
		var screen_width = jQuery(window).width();
		if (options.direction=='left') 
		{
			jQuery(boxtype).animate({left: "-5px"}, 1000, "easeOut"+options.animationtype, function(){jQuery(".cfslider-form-name").focus();opened = true;});
		}
		if (options.direction=='right') 
		{
			jQuery(boxtype).animate({right: "-5px"}, 1000, "easeOut"+options.animationtype, function(){jQuery(".cfslider-form-name").focus();opened = true;});
		}
		opened = true;
	}
}
},
	setCookie : function(params)
	{
	var c_name = params[0];
	var value = params[1];
	var dduntil = params[2];
	var mode = params[3];
		if (mode=='days')
		{
			var exdate=new Date();
			exdate.setDate(exdate.getDate() + parseInt(dduntil));
			var c_value=escape(value) + ((dduntil==null) ? "" : "; expires="+exdate.toUTCString()) + "; path=/";
			document.cookie=c_name + "=" + c_value;		
		}
		if (mode=='minutes')
		{
			var now=new Date();
			var time = now.getTime();
			time += parseInt(dduntil);
			now.setTime(time);
			var c_value=escape(value) + ((dduntil==null) ? "" : "; expires="+now.toUTCString()) + "; path=/";
			document.cookie=c_name + "=" + c_value;
		}
	},
	getCookie : function(c_name) 
	{
		var c_value = document.cookie;
		var c_start = c_value.indexOf(" " + c_name + "=");
		if (c_start == -1)
		  {
		  c_start = c_value.indexOf(c_name + "=");
		  }
		if (c_start == -1)
		  {
		  c_value = null;
		  }
		else
		  {
		  c_start = c_value.indexOf("=", c_start) + 1;
		  var c_end = c_value.indexOf(";", c_start);
		  if (c_end == -1)
		  {
		c_end = c_value.length;
		}
		c_value = unescape(c_value.substring(c_start,c_end));
		}
		return c_value;
	},
    destroy : function() {
		jQuery(".cfs_hdline").remove();
		jQuery("#bglock").remove();		
		return 1;
	},
	open : function()
	{
	   if (parentbox=='') 
		{
			parentbox = '.contact-form-slider-box';
			jQuery( ".contact-form-slider-box h2" ).trigger( "click" );
		}
	},
	close : function()
	{
		jQuery('body').cfslider('remove');
	},
	hide : function()
	{
		jQuery('.cfs_hdline').hide();
	},
	show : function()
	{
		jQuery('.cfs_hdline').show();
	},
	detectmob : function()
	{
	   if(window.innerWidth <= 800 && window.innerHeight <= 600) {
		 return true;
	   } else {
		 return false;
	   }
	},
	remove : function(boxtype)
	{
	if ( typeof cfs_params !== 'undefined') options = cfs_params;
	var options = jQuery.extend({}, defaults, options);
	if (boxtype==undefined||boxtype=='') boxtype = opened_slider;
		if (jQuery('#bglock').length)
		{
			jQuery("#bglock").fadeOut(1000);
		}
		if (options.direction=='left') jQuery(boxtype).animate({left: "-"+(parseInt(jQuery(boxtype).width())+space)+"px"}, 1000, "easeOut"+options.animationtype,function(){if (jQuery("#twitter_timeline_cfs_hdline h2").length==1) jQuery("#twitter_timeline_cfs_hdline").css("z-index","11000");});
		if (options.direction=='right') jQuery(boxtype).animate({right: "-"+(jQuery(boxtype).width())+"px"}, 1000, "easeOut"+options.animationtype,function(){if (jQuery("#twitter_timeline_cfs_hdline h2").length==1) jQuery("#twitter_timeline_cfs_hdline").css("z-index","11000");});
		jQuery("#cfs_wrapper_inside").removeClass(options.bodyanim);
	},
	resize : function()
	{
	if ( typeof cfs_params !== 'undefined') options = cfs_params;
	var options = jQuery.extend({}, defaults, options);
		if (jQuery(".contact-form-slider-box").length==1)
	{
			if (jQuery('body').cfslider('detectmob')==true) {jQuery(".contact-form-slider-box").css("width","80%");}
			else jQuery('.contact-form-slider-box').css("width",'35%');
			if ( options.height != 'full' ) jQuery('.contact-form-slider-box').css("height",'80%');
			jQuery('.contact-form-slider-box .cfsbox').css("width",(jQuery(".contact-form-slider-box .cfs_hdline_inside").width())+'px');
			jQuery('.contact-form-slider-box .cfsbox').css("height",(jQuery(".contact-form-slider-box .cfs_hdline_inside").height()-55)+'px');
			jQuery('.contact-form-slider-box .fb_ltr').css("height",(jQuery(".contact-form-slider-box .cfsbox").height()/100*90)+'px');
			jQuery('.contact-form-slider-box .fb_ltr').css("width",jQuery(".contact-form-slider-box .cfsbox").width()-20+'px');
			if (parseInt(jQuery(".contact-form-slider-box").css("left").replace("px",""))<-5) jQuery('.contact-form-slider-box').css("left",'-'+(jQuery(".contact-form-slider-box").width()+space)+'px');
			if (parseInt(jQuery(".contact-form-slider-box").css("right").replace("px",""))<-5) jQuery('.contact-form-slider-box').css("right",'-'+(jQuery(".contact-form-slider-box").width())+'px');
		if (options.direction=='left') jQuery('.contact-form-slider-box h2').css("left",(parseInt(jQuery(".contact-form-slider-box").width())+bspace)+'px');
		if (options.direction=='right') jQuery('.contact-form-slider-box h2').css("left",'-'+jQuery(".contact-form-slider-box h2").width()+'px');
			jQuery(".contact-form-slider-box .fb-like-box").attr("data-width",(jQuery(".contact-form-slider-box").width()-30)+'px');
	}
	}
    };
jQuery.fn.cfslider = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            return methods.init.apply( this, arguments );
        } else {
            jQuery.error( 'Method ' +  methodOrOptions + ' does not exist on jQuery.cfslider' );
        }    
    };
})( jQuery );