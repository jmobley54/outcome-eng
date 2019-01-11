
function mbIcons()
{
  this.current_faicon = null;
  this.modal = null;
  this.is_icon = false;
  this.is_image = false;
	this.has_hover = false;
	this.is_textbound = false;
	this.is_background = false;
}

mbIcons.prototype.init = function ()
{

    // set button
    $('#select_icon, #select_hover_icon').on('click', $.proxy(function (e){
  			this.current_faicon = $(e.target).attr('id');
  	}, this)) ;

  	// When user selects icon
  	$(document).on('click','.font-awesome ul li', $.proxy(this.selectFAIcon, this));

  	// Icon search and filters
  	$(document).on('keyup','.search-input',  _.debounce( $.proxy(function(e)
  	{
  		this.searchIcons();
  	}, this), 300));
  	$(document).on('click', '.icon-search .category-filter li', $.proxy(this.filterIcon, this));
    //$(document).on('click', '.icon-search .active-filters li', $.proxy(function (e) { $(e.target).remove(); this.searchIcons(); }, this) );

  	/* Selected icon is being removed */
  	$(document).on('click', '.font-awesome-preview .remove_fa_icon', $.proxy(this.removeIcon, this));

    // for adding hovers
    $('#add_hover_image').on('click', function (e) {
  			$('.option.icon_hover_option_preview, .option.icon_hover_image_button').removeClass('hidden');
  			$(this).remove();
  	});

  	$('#add_hover_icon').on('click', function (e)
    {
  		$('.faicon_hover_preview .font-awesome-preview, #select_hover_icon').removeClass('hidden');
  		$(this).parent().remove();
    });


    this.attachMediaUploader('icon');
  	this.attachMediaUploader('icon_hover');
}


mbIcons.prototype.loadIcons = function (modal)
{
  this.modal = modal;

  var maxajax = window.maxFoundry.maxAjax;
  var data = maxajax.ajaxInit();
  data['plugin_action'] = 'load_icons';

  maxajax.ajaxPost(data, $.proxy(this.writeIcons, this) );
}

mbIcons.prototype.writeIcons = function (result)
{
  var modal = this.modal;
  var currentModal = modal.currentModal;
  var result = JSON.parse(result);

  if (result.categories)
  {
    $.each(result.categories, function (index, category) {

        var icons  = category.icons;
        var label = category.label;
        var num_icons = icons.length;

        $(currentModal).find('.categories').append('<li data-category="' + index + '" data-icons="' + icons + '">' + label +  '(' + num_icons + ')</li>');

    });
  }

  if (result.icons)
  {
    $.each(result.icons, function (name, styles) {

      $.each(styles, function(index, item)
      {
        $(currentModal).find('.icon-list').append('<li class="' + item.name + ' ' + item.category + '" title="' + item.nice_name + '" data-mbicon="' + item.icon +   '"> ' + item.svg + '</li>');
      }); // item loop
   });
  }

  modal.checkResize();
}


/** Default states of icons*/
mbIcons.prototype.font_awesome_icon = function()
	{
		var checked = $('input[id="use_fa_icon"]').is(':checked');
		if (checked)
		{
			$(".non-fa").hide();
			$(".fontawesome-only").show();
			$('.font-awesome').addClass('checked');
		}
		else
		{
			$(".fontawesome-only").hide();
			$(".non-fa").show();
			$('.font-awesome').removeClass('checked');

		}
		this.updateIcon();
	}

/** Event when user select an icon in the popup **/
mbIcons.prototype.selectFAIcon = function(e)
{
		e.preventDefault();
 		var target = $(e.target);

		if (typeof target.data('mbicon') === 'undefined')
 			target = $(target).parents('li');

    var icon  = {};
    icon.value = $(target).data('mbicon');
    icon.svg = $(target).find('svg')[0].outerHTML;

    window.maxFoundry.maxadmin.saveIndicator(true);

		if (this.current_faicon == 'select_icon')
		{
			mode = 'normal';
			$('#fa_icon_value').val(icon.value);
      $('.font-awesome-preview .the-icon.normal').html(icon.svg);
		}
		else {
			mode = 'hover';
			$('#fa_icon_hover_value').val(icon.value);
      $('.font-awesome-preview .the-icon.hover').html(icon.svg);

		}

 		 window.maxFoundry.maxmodal.close(); // handy
 		 this.updateIcon(mode, icon);
}

// reset and put and position icon
mbIcons.prototype.updateIcon = function (target)
	{
		target = target || '';
		this.is_icon = $('input[id="use_fa_icon"]').is(':checked');
    var textbound = $('input[id="bind_to_text"]').is(':checked');
    var position = $('#icon_position').val();

    if (! this.is_icon)
      this.is_image = false;

      //this.has_hover = false;
  	this.is_background = (position == 'background' ? true : false);
    this.is_textbound = (! this.is_background && textbound ? true : false );

		var ptop = parseInt($('#icon_padding_top').val());
		var pright = parseInt($('#icon_padding_right').val());
		var pbottom = parseInt($('#icon_padding_bottom').val());
		var pleft = parseInt($('#icon_padding_left').val());

		if (isNaN(ptop) || ptop < 0) ptop = 0;
		if (isNaN(pright) || pright < 0) pright = 0;
		if (isNaN(pbottom) || pbottom < 0) pbottom = 0;
		if (isNaN(pleft) || pleft < 0) pleft = 0;

		var padding = ptop + 'px ' + pright + 'px ' + pbottom + 'px ' + pleft + 'px';
		var icon_url = $('#icon_url').val();
		var icon_hover_url = $('#icon_hover_url').val();

		$('.output .result').find('.mb-icon').remove();

		var anchorhtml = $('.output .result a.normal').html();
		var anchorhtml_hover = $('.output .result a.hover').html();

		if (! this.is_icon && (icon_url == '' && icon_hover_url == '') )
			return;

    $('.output .result a .mb-icon, .output .result a .mb-icon-hover').remove();
    var $path = $('.output .result a.normal');
    var $path_hover = $('.output .result a.hover');

    if ( this.is_textbound)
    {
      var $path = $('.output .result a.normal .mb-text');
      var $path_hover = $('.output .result a.hover .mb-text');
    }

		if (position == 'bottom')
		{
      $path.append("<span class='mb-icon'  > </span>" );
      $path_hover.append("<span class='mb-icon'  > </span>" );
		}
		else
		{
      if (this.is_textbound && position == 'right' )
      {
        $path.append("<span class='mb-icon'  > </span>" );
        $path_hover.append("<span class='mb-icon'  > </span>" );
      }
      else {
        $path.prepend("<span class='mb-icon' ></span>");
  			$path_hover.prepend("<span class='mb-icon' ></span>");
      }

		}

    var $mb_icon = $('.output .result a .mb-icon');

		$mb_icon.css({'padding': padding,
                  'display': 'block',
                  'lineHeight': '0px',
                });
	 	// with update important statements are removed:
	  //$mb_icon.css();
		//$mb_icon.css('lineHeight', '0px');

	 	switch(position)
	 	{
	 		case "left":
	 			$mb_icon.css('float', 'left');
	 		break;
	 		case "right":
	 			$mb_icon.css('float', 'right');
	 		break;
	 		case "top":
	 		case "bottom":
		 		$mb_icon.css('textAlign', 'center');
	 		break;
      case 'background':

        var poshor = $('input[name="background_position_horizontal"]').val();
        var posver = $('input[name="background_position_vertical"]').val();

        $mb_icon.css({
              'backgroundPosition': poshor + '% ' + posver + '%',
              'background-repeat': 'no-repeat',
              'position': 'absolute',
              'box-sizing': 'border-box',
              'backgroundRepeat': 'no-repeat',
              'padding': '0',
              'left' : '0',
              'right': '0',
              'top' : '0',
              'bottom': '0'
        });
        //$mb_icon.css('background-repeat', 'no-repeat');

        //$mb_icon.css('position', 'absolute');
        //$mb_icon.css('box-sizing', 'border-box');
        //$mb_icon.css('backgroundImage','url(' + url + ')');
        //$mb_icon.css('backgroundRepeat', 'no-repeat');
        //$mb_icon.css('backgroundPosition', pleft + 'px ' + ptop + 'px');
        //$mb_icon.css('padding', '0');

        //$mb_icon.css('left', '0');
      //  $mb_icon.css('right', '0');
      //  $mb_icon.css('top', '0');
      //  $mb_icon.css('bottom', '0');
      //  $mb_icon.children().remove();
      break;
	 	}

    if (this.is_textbound)
    {
      $mb_icon.css('float', 'none');
      if ( position == 'left' || position == 'right')
        $mb_icon.css('display', 'inline-block');

      if ( position == 'top' || position == 'bottom')
        $mb_icon.css('display', 'block');

      $mb_icon.css('verticalAlign', 'middle');
    }

		if (this.is_icon)
		{
			this.updateFontAwesomeIcon();
		}
		else
		{
			this.updateImageIcon('icon');
		}


	}

  mbIcons.prototype.updateFontAwesomeIcon = function(mode, item)
  	{

      var icon_value = $('#fa_icon_value').val();
      var icon_hover_value = $('#fa_icon_hover_value').val();
    //  var bind_to_text = $('input[id="use_fa_icon"]').is(':checked');


      var svg_icon = $('.font-awesome-preview .the-icon.normal').html();
      var svg_icon_hover = $('.font-awesome-preview .the-icon.hover').html();

      var $output_icon = $('.output .result a.normal .mb-icon');
      var $output_hover_icon = $('.output .result a.hover .mb-icon');
      var $output_combined = $('.output .result a .mb-icon');

      if (icon_hover_value.length == 0)
      {
        svg_icon_hover = svg_icon;
      }

  		var position = $('#icon_position').val();

  		if (icon_value == '')
  		{
  			$('.output .result a .mb-icon').css('padding', ''); // remove the padding since it's false.
  			return; // no icon
  		}
  		// update both preview and interface

  		var icon_size = $('#fa_icon_size').val();
  		var icon_color = $('#icon_color').val();
  		var icon_color_hover = $('#icon_color_hover').val();

      // to both
      var $svg_icon = $(svg_icon);
      var $svg_icon_hover = $(svg_icon_hover);
      $svg_icon.find('path').attr('fill', icon_color);
      $svg_icon_hover.find('path').attr('fill', icon_color_hover);

  		if (position == 'background')
  		{
        //data:image/svg+xml;utf8,
        $output_combined.css('backgroundSize', icon_size  + 'px ' + icon_size + 'px');

        $output_icon.css('backgroundImage','url(data:image/svg+xml;charset=utf-8,'  + escape($svg_icon.prop('outerHTML'))  + ')');
        $output_hover_icon.css('backgroundImage','url(data:image/svg+xml;charset=utf-8,'  + escape($svg_icon_hover.prop('outerHTML')) + ')');

  		}
      else {

        $output_icon.append($svg_icon);
        $output_hover_icon.append($svg_icon_hover);

        $output_combined.find('svg').width(icon_size);
        $output_combined.find('svg').height(icon_size);

        if (this.is_textbound)
        {
          $output_combined.css('fontSize', icon_size + 'px');
          $output_combined.css('lineHeight', icon_size + 'px');
        }

      }

		  $('.font-awesome-preview .remove_fa_icon').show();
  }


mbIcons.prototype.updateImageIcon = function(key)
{
    var hover_key = key + '_hover';

    var image = {};
    var image_hover = {};

    image.url = $('#' + key + '_url').val();
    image.alt = $('.' + key + '_data .alt').text();
    image.title = $('.' + key + '_data .atttitle').text();

    image_hover.url = $('#' + hover_key + '_url').val();
    image.alt = $('.' + hover_key + '_data .alt').text();
    image.title = $('.' + hover_key + '_data .atttitle').text();

    // if hover not defined, fallback fully on image.
    if (typeof image_hover.url == undefined || image_hover.url == '')
    {
      image_hover = image;
    }

    var $output_icon = $('.output .result a.normal .mb-icon');
    var $output_hover_icon = $('.output .result a.hover .mb-icon');
    var $output_combined = $('.output .result a .mb-icon');


		var position = $('#icon_position').val();

		var pleft = parseInt($('#icon_padding_left').val());
		var ptop = parseInt($('#icon_padding_top').val());

		if (position == 'background')
		{
      $output_icon.css('backgroundImage','url(' + image.url + ')');
      $output_hover_icon.css('backgroundImage','url(' + image_hover.url + ')');
		}
		else
		{
				$output_icon.html('<img src="' + image.url + '" alt="' + image.alt + '" title="' + image.title + '" >');
        $output_hover_icon.html('<img src="' + image_hover.url + '" alt="' + image_hover.alt + '" title="' + image_hover.title + '" >');
				//$('.output .result a.' + mode).find('.mb-icon-hover').remove();
		}
}

/** Search for a Font Awesome Icon in the modal **/
mbIcons.prototype.searchIcons = function()
{
	$('.icon-search .spinner').css('visibility', 'visible');

	var search_string = $('.max-modal .search-input').val();

  var filters = []; //$('.active-filters li').data('icons');
  var styles = [];
  $('.category-filter li.selected').each(function () {

      if (typeof $(this).data('icons') === 'undefined'  )
      {
        styles.push( $(this).data('category') );
      }
      else
      {
        icons = $(this).data('icons');
        icons = icons.split(',');
        $.merge(filters, icons );
      }
  });

	$('.icon-list li').hide();

  if (search_string.length > 0)
	   $('.icon-list li[class*="' + search_string + '"]').show();

  if (filters.length > 0)
  {
      filters = filters.map(function(el) {  // add prefix
        return '.' + el;
      });

      var flist = filters.join(',');
      $('.icon-list').find(flist).show();

  }

  if (search_string == '' && filters.length == 0)
	{
		$('.icon-list li').show();
	}

  // negative filter last
  if (styles.length > 0)
  {
      styles = styles.map(function(el) {  // add prefix
        return '.' + el;
      });

      var slist = styles.join(',');

      $('.icon-list li').not(slist).hide();
  }

	$('.icon-search .spinner').css('visibility', 'hidden');

}

mbIcons.prototype.filterIcon = function (e)
{
  var target = $(e.target);
  var icons = $(target).data('icons');
  //var clone = $(target).clone();
  //var cat = $(target).data('category');

  //if ( $('.active-filters li').find('[data-category="' + cat + '"]').length > 0)
  //  return;

  if($(target).hasClass('selected'))
  {
    $(target).removeClass('selected');
  }
  else {
    $(target).addClass('selected');
  }

  this.searchIcons();

}


mbIcons.prototype.attachMediaUploader = function (key) {

 $('#image_' + key + '_button').on('click', { key: key }, $.proxy(function(event) {
	 var self = this;
		event.preventDefault();

		// Create the media frame.
	  this.uploader = wp.media.frames.mb_media_frame = wp.media({
	 	 title: $( this ).data( 'uploader_title' ),
	 	 button: {
	 	 text: $( this ).data( 'uploader_button_text' ),
	 	 },
	 	 id: 'maxbuttons-wp-image-picker',
	 	 frame: 'post', // this setting makes a lot of others not work.
	 	 state: 'insert',
	 	 library: {
	 		 type: 'image'
	 	 },
	 	 multiple: false // Set to true to allow multiple files to be selected
	  });


		this.uploader.on ( 'insert', function ()
		{
			 		var attachment = self.uploader.state().get('selection').first().toJSON();
					var sizes = attachment.sizes;

					var selected_size = $('.media-frame').find('select.size').val();

					var url = attachment.url;
					if (sizes[selected_size] && sizes[selected_size].url)
					{
							url = sizes[selected_size].url;
					}

			 		$('#' + key + '_url' ).val(url); // set URL field to image value
			 		$('.image_' + key + '_preview').find('img').remove();

			 		$('.image_' + key + '_preview').prepend('<img src="' + url + '">');

			 		$('#' + key + '_id').val(attachment.id);
					$('#' + key + '_size').val(selected_size);

			 		$('#' + key).trigger('keyup');
					$('.' + key + '_preview .remove_icon').show();

					$('.' + key + '_data .alt').text(attachment.alt);
					$('.' + key + '_data .atttitle').text(attachment.title);
					$('.' + key + '_data .filename').text(attachment.filename);

					window.maxFoundry.maxadmin.saveIndicator(true);
					self.updateIcon();

		});

// uploader ready seems to be some undocced feature.
		this.uploader.on('uploader:ready', function() {
				var selection = self.uploader.state().get('selection');
			  id = $('#' + key + '_id').val();
			  var attachment = wp.media.attachment(id);
			  selection.add( attachment ? [ attachment ] : [] );
		});

		// Finally, open the modal
		this.uploader.open();

	}, this)  );

	// Image remove

	$(document).on('click','.image_' + key + '_preview .remove_icon', $.proxy(this.removeImage, this) );

	if (! parseInt($('#' + key +  '_id').val()) > 0 )
	{
		$('.' + key + '_preview .remove_icon').hide();
	}
}

mbIcons.prototype.removeImage = function(e)
{
	var key  = $(e.target).data('key');
	var parent = $(e.target).parents('.input');

	$('#' + key + '_id').val('');
	$('#' + key + '_url').val('');

	$(parent).find('img').remove();
	$(parent).find('.remove_icon').hide();
	$(parent).find('.icon_data span').text('');

	window.maxFoundry.maxadmin.saveIndicator(true);
	this.updateIcon();
}


mbIcons.prototype.removeIcon = function (e)
{
	var target = $(e.target);
	var mode = $(target).data('mode');

	if (mode == 'hover')
	{
		var val = $('#fa_icon_hover_value').val();
		$('#fa_icon_hover_value').val('');
	}
	else {
		var val = $('#fa_icon_value').val();
		$('#fa_icon_value').val('');
	}


	$(target).parents('.font-awesome-preview').find('.the-icon').html(''); //.removeClass(val);
 	$(target).hide();

	this.updateIcon(mode);
}
