/**
 * AJDE Backender scripts
 * @version  1.5.4
 */
jQuery(document).ready(function($){

	init();

	function init(){
		// focusing on correct settings tabs
		var hash = window.location.hash;
		//console.log(hash);

		if(hash=='' || hash=='undefined'){
		}else{
			var hashId = hash.split('#');

			$('.nfer').hide();
			$(hash).show();

			var obj = $('a[data-c_id='+hashId[1]+']');
			change_tab_position(obj);
		}
	}

	// colpase menu
		$('.ajde-collapse-menu').on('click', function(){
			if($(this).hasClass('close')){
				$(this).parent().removeClass('mini');
				$('.evo_diag').removeClass('mini');
				$(this).removeClass('close');
			}else{
				$(this).parent().addClass('mini');
				$('.evo_diag').addClass('mini');
				$(this).addClass('close');
			}
		});

	// switching between tabs
		$('#acus_left').find('a').click(function(){

			var nfer_id = $(this).data('c_id');
			$('.nfer').hide();
			$('#'+nfer_id).show();
			
			change_tab_position($(this));

			window.location.hash = nfer_id;

			if(nfer_id=='evcal_002'){
				$('#resetColor').show();
			}else{
				$('#resetColor').hide();
			}
			
			return false;
			
		});

		// position of the arrow
		function change_tab_position(obj){

			// class switch
			$('#acus_left').find('a').removeClass('focused');
			obj.addClass('focused');

			var menu_position = obj.position();
			//console.log(obj);
			$('#acus_arrow').css({'top':(menu_position.top+3)+'px'}).show();
		}

		// RESET colors
		$('#resetColor').on('click',function(){
			$('.colorselector ').each(function(){
				var item = $(this).siblings('input');
				item.attr({'value': item.attr('default') });
			});
			
		});

	// color circle guide popup
		$('#ajde_customization .hastitle').hover(function(){
			var poss = $(this).position();
			var title = $(this).attr('alt');
			//alert(poss.top)
			$('#ajde_color_guide').css({'top':(poss.top-33)+'px', 'left':(poss.left+11)}).html(title).show();
			//$('#ajde_color_guide').show();

		},function(){
			$('#ajde_color_guide').hide();
		});

	// COLOR PICKER
	// @version 2.0
	
	// font awesome icons
		var fa_icon_selection = '';
		$('.faicon').on('click','i', function(){
			var poss = $(this).position();
			$('.fa_icons_selection').css({'top':(poss.top-220)+'px', 'left':(poss.left-74)}).fadeIn('fast');

			fa_icon_selection = $(this);
		});

		//selection of new font icon
		$('.fa_icons_selection').on('click','li', function(){

			var icon = $(this).find('i').data('name');
			//console.log(icon)

			fa_icon_selection.attr({'class':'fa '+icon});
			fa_icon_selection.siblings('input').val(icon);

			$('.fa_icons_selection').fadeOut('fast');
		});
		// close with click outside popup box when pop is shown
		$(document).mouseup(function (e){
			var container=$('.fa_icons_selection');
			
				if (!container.is(e.target) // if the target of the click isn't the container...
				&& container.has(e.target).length === 0) // ... nor a descendant of the container
				{
					$('.fa_icons_selection').fadeOut('fast');
				}			
		});
	
	// multicolor title/name display
		$('.row_multicolor').on('mouseover','em',function(){
			var name = $(this).data('name');
			$(this).closest('.row_multicolor').find('.multicolor_alt').html(name);
		});
		$('.row_multicolor').on('mouseout','em',function(){
			$(this).closest('.row_multicolor').find('.multicolor_alt').html(' ');
		});	
	
	//legend
		$('.legend_icon').hover(function(){
			$(this).siblings('.legend').show();
		},function(){
			$(this).siblings('.legend').hide();
		});
		
	// image
		if($('.ajt_choose_image').length>0){
			var _custom_media = true,
			_orig_send_attachment = wp.media.editor.send.attachment;
		}
		
		$('.ajt_choose_image').click(function() {
			var send_attachment_bkp = wp.media.editor.send.attachment;
			var button = $(this),
				imagesection = button.parent();

			//var id = button.attr('id').replace('_button', '');
			_custom_media = true;
			wp.media.editor.send.attachment = function(props, attachment){
				if ( _custom_media ) {
					//console.log(attachment);

					imagesection.find('.ajt_image_id').val(attachment.id);					
					imagesection.find('.ajt_image_holder img').attr('src',attachment.url);
					imagesection.find('.ajt_image_holder').fadeIn();
					button.fadeOut();

					//$("#"+id).val(attachment.url);
				} else {
					return _orig_send_attachment.apply( this, [props, attachment] );
				};
			}

			wp.media.editor.open(button);
			return false;
		});
		$('.add_media').on('click', function(){
			_custom_media = false;
		});

		// removre image
		$('.ajde_remove_image').click(function() {  
			imagesection = $(this).closest('p');
			imagesection.find('.ajt_image_id').val('');					
			imagesection.find('.ajt_image_holder').fadeOut();
			imagesection.find('.ajt_choose_image').fadeIn();
	        return false;  
	    });
		
	// hidden section
		$('.ajdeSET_hidden_open').click(function(){
			$(this).next('.ajdeSET_hidden_body').slideToggle();
			if( $(this).hasClass('open')){
				$(this).removeClass('open')
			}else{
				$(this).addClass('open');
			}
		});	

	// sortable		
		$('.ajderearrange_box').sortable({		
			update: function(e, ul){
				var sortedID = $(this).sortable('toArray',{attribute:'val'});
				$(this).closest('.ajderearrange_box').siblings('.ajderearrange_order').val(sortedID);
			}
		});



		// hide sortables
			$('.ajderearrange_box').on('click','span',function(){
				$(this).toggleClass('hide');
				update_card_hides( $(this) );
			});

			function update_card_hides(obj){
				hidethese = '';
				$('.ajderearrange_box').find('span').each(function(index){
					if(!$(this).hasClass('hide'))
						hidethese += $(this).parent().attr('val')+',';
				});

				obj.closest('.ajderearrange_box').siblings('.ajderearrange_selected').val(hidethese);
			}
		
	// at first run a check on list items against saved list -
		var items='';
		$('#ajdeEVC_arrange_box').find('p').each(function(){
			if($(this).attr('val')!='' && $(this).attr('val')!='undefined'){
				items += $(this).attr('val')+',';
			}
		});
		$('.ajderearrange_order').val(items);	
});