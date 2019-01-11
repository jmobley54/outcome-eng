	var maxFonts;

jQuery(document).ready(function($) {

	maxFonts = function ()
	{	}

	maxFonts.prototype = {
		webFonts: false,
		userFonts: false,
		usedFonts: false,
		systemFonts : ['', 'Arial', 'Courier New', 'Georgia', 'Tahoma', 'Times New Roman', 'Trebuchet MS',
	'Verdana' ],
		delay: null,
		googleBaseURL: '//fonts.googleapis.com/css?family=',
		displayOnLoad: true,
		fontsLoaded: [],
	};

	maxFonts.prototype.load = function()
	{
		if (! this.userFonts)
			this.loadUserFonts();


		if (this.webFonts === false)
			this.parseWebFonts();
		else
			this.showFonts('web');

	}

	maxFonts.prototype.close = function ()
	{
		window.maxFoundry.maxmodal.close();
	}

	maxFonts.prototype.checkFonts = function ()
	{
		var self = this;

		$('.mb-text, .mb-text2').each(function () {
			var ff = $(this).css('fontFamily');
			var weight = $(this).css('fontWeight');
			var family = self.getWebFamily(ff, weight);
			if (family !== false)
				$("head").append("<link rel='stylesheet' type='text/css' href='" + family + "' />");

		});
	}

	// function to parse a google font load string
	maxFonts.prototype.getWebFamily = function(font, weight)
	{
		if (this.systemFonts.indexOf(font) >= 0)
			return false;

		var fonts = JSON.parse(mb_font_options.combined_fonts);

		var self = this;
		var url = false;

		$.each(fonts, function(index, el)
		{
			if (el == font)
			{
				var family = font;
				family = family.replace("'",'');
				family = family.replace(/\s/g, '+');
				if (family.indexOf(",", family) >= 0)
				{
					return false; // a non-google font with comma, probably set by theme. This can happen if button doens't have font preference.
				}
				if (self.fontsLoaded.indexOf(family) >= 0)
				{

					return false;  // prevent doubles
				}
				self.fontsLoaded.push(family);

				url = self.googleBaseURL + family;

				return false;
			}
		});

		return url;
	}

	maxFonts.prototype.delay = function ()
	{
		var timers = {};
		return function (callback, ms, label) {
			label = label || 'defaultTimer';
			clearTimeout(timers[label] || 0);
 			timers[label] = setTimeout(callback, ms);
			};

	}();

	maxFonts.prototype.loadDone = function()
	{
		$('.max-modal.add-fonts').find('.loading, .loading_overlay').hide();

		$('.max-modal.add-fonts .fontcount').text( this.webFonts.length );
		$('.max-modal.add-fonts .font_search').off('keyup');
		$('.max-modal.add-fonts .font_search').on('keyup', $.proxy(function () { // search keyword with n ms delay
			 this.delay( $.proxy(function() { this.searchkw() },this), 300, 'search');
    	}, this) );
    	$('.max-modal.add-fonts .font_manager .items input').off('click');
    	$('.max-modal.add-fonts .font_manager .items input').on('click', $.proxy(this.renderExample, this));

    	$('.max-modal.add-fonts button[name="save_fonts"]').off('click');
    	$('.max-modal.add-fonts button[name="save_fonts"]').on('click', $.proxy(this.saveFonts, this));

	}

	maxFonts.prototype.searchkw = function ()
	{

		var word = $('.max-modal .font_search input').val();
		$('.max-modal .font_search input').prop('disabled',true);
		word = word.toLowerCase();

		$('.max-modal .font_manager .items li label input').each(function (index, el)
		{
			if (word == '' || $(this).val().toLowerCase().indexOf(word) >= 0 )
			{
				$(this).parents('li').show();
			}
			else
				$(this).parents('li').hide();


		});
		$('.max-modal .font_search input').prop('disabled',false);

	}

	maxFonts.prototype.renderExample = function (e)
	{
		var target = e.target;

		if ( target.nodeName !== 'LI')
			target = $(e.target).parents('li');

		var font = $(target).find('input').val();

	//	$('.max-modal .font_manager .items li').css('border','');
	//	$(target).css('border','1px solid #000');
	 $('.font_example .example_text span').html(font);

		family = this.getWebFamily(font);
		if(family)
			$("head").append("<link rel='stylesheet' type='text/css' href='" + family + "' />");

		$('.font_example .placeholder').hide();
		$('.font_example .example_text').show().css('fontFamily', font);

	}

	maxFonts.prototype.saveFonts = function()
	{
		var fontsToSave = $('.max-modal .font_manager .items li input:checked');

		var fonts= {};
		$(fontsToSave).each(function ()
		{

			val = $(this).val();
			fonts[val] = val;

		});
		var data = {
						 'action': 'font_manager',
					   'font_action' : 'save',
					   'fonts' : fonts,
					  };

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: data,
			success: $.proxy(this.saveDone, this),
		});

	}

	// parse back new fonts to interface - close window
	maxFonts.prototype.saveDone = function (res)
	{
		var result = JSON.parse(res);

		var newfonts = result.fonts;
		var used_fonts = result.usedfonts;

		var editors = $('#maxbuttons select[name="font"], #maxbuttons select[name="font2"]');
		var selected = $(editors).find(':selected');

		$(editors).children('option').remove();

		$.each(newfonts, function (index, el) {
			$(editors).append('<option value="' + index + '">' + el + '</option>');

		});

		// restore previous selected options
		$(editors[0]).find('option[value="' + $(selected[0]).val() + '"]').attr('selected', 'selected');
		$(editors[1]).find('option[value="' + $(selected[1]).val() + '"]').attr('selected', 'selected');

		// set the font editor window before close
		mb_font_options.used_fonts = used_fonts;
		this.loadUserFonts();
		this.showFonts('web');

		this.close();
	}

	maxFonts.prototype.parseWebFonts = function ()
	{
		var self = this;
		var webfonts = [];

	  $.getJSON(mb_font_options.webfonts, $.proxy(function (fonts) {
			 $(fonts.items).each( function ()
			 {

				 var variants = this.variants;

				 var thisfont = {};

				 thisfont.family = this.family;
				 thisfont.variants = this.variants;

				 webfonts.push(thisfont);
			 });

			 this.webFonts = webfonts;

			 if (this.displayOnLoad)
	 			this.showFonts('web');
		 }, this) );
		//var fonts = JSON.parse(mb_font_options.webfonts);
	}

	/** User fonts - Fonts picked by users via font manager */
	maxFonts.prototype.loadUserFonts = function()
	{
		var userfonts = [];
		var fonts = mb_font_options.user_fonts;

		if (fonts === '')  // nothing.
		{
			this.userFonts = [] ;
			return;
		}
		$.each(fonts, function(index, value)
		{
			if (value !== '') 	// omit the empty one
				userfonts.push(value);
		});
		this.userFonts = userfonts;
	}

  /** Used fonts - fonts used by any button */
	maxFonts.prototype.loadUsedFonts = function()
	{
		var usedfonts = [];
		var fonts = mb_font_options.used_fonts;

		if (fonts === '')  // nothing.
		{
			this.usedFonts = [] ;
			return;
		}
		$.each(fonts, function(index, value)
		{
			if (value !== '') 	// omit the empty one
				usedfonts.push(value);
		});
		this.usedFonts = usedfonts;
	}


	maxFonts.prototype.showFonts = function(type)
	{
		userfonts = this.userFonts;

		if (type === 'web')
		{
			fonts = this.webFonts;
			var left_element = '.max-modal .font_manager .font_left';
			var right_element = '.max-modal .font_manager .font_right';

			$('.max-modal .font_manager').find('input[type="checkbox"]').attr('checked', false);

			$(left_element).find('.items li').remove(); //reset
			$(right_element).find('.items li').remove(); //reset
		}
		else { return false; }

		var num_items = fonts.length;

		var num_left = Math.ceil(num_items / 2);
		var cur_el = left_element;
		var value;
		var selected = false;

		for(i = 0; i < fonts.length; i++)
		{
			if (i >= num_left)
			{
				cur_el = right_element;
			}
			value = fonts[i].family;

			if($.inArray(value, userfonts) >= 0)
				{ selected = 'checked';}
			else
				{ selected = ''; }

			var id = value.toLowerCase().replace(' ', '');

			$(cur_el + ' .items').append('<li><input type="checkbox" id="' + id + '" name="userfonts[]" value="' + value + '"' + selected + '><label for="' + id + '"><span>' + value + '</span></label></li>');

		}

		this.loadDone();

	}

}); /* END OF JQUERY */
