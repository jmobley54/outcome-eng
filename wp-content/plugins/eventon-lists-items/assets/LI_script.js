/**
 * Javascript: Lists and Items for eventon
 * @version  0.1
 */
jQuery(document).ready(function($){	

	// initial resize the event list items
		adjust_sizes();
	// responsive adjustments
		$( window ).resize(function() {
			adjust_sizes();
		});
		function adjust_sizes(){
			$('body').find('.EVOLI').each(function(){
				OBJ = $(this);
				var _WIDTH = OBJ.width();
				CONTAINER = OBJ.find('.EVOLI_container');
				CONTAINER.width(_WIDTH*2);
				OBJ.find('.EVOLI_list').width(_WIDTH);
				OBJ.find('.EVOLI_event_list').width(_WIDTH).show();
				if( CONTAINER.hasClass('moved')){
					CONTAINER.css({'margin-left':(_WIDTH*-1)});
				}
			});
		}

	// click on each list item
	$('.EVOLI_list').on('click','a',function(event){
		event.stopPropagation();
	});
	$('.EVOLI_list').on('click','li',function(){

		if($(this).hasClass('.noevents')) return false;

		OBJ = $(this);
		OBJP = OBJ.parent();
	
		OBJP.addClass('loading');
		EVOLI = OBJ.closest('.EVOLI');

		var ajaxdataa = { };
		ajaxdataa['action']='evoliajax_list';
		ajaxdataa['termid']= OBJ.data('id');
		ajaxdataa['tax']= OBJP.data('type');
		ajaxdataa['sepm']= OBJP.data('sepm');
		ajaxdataa['numm']= OBJP.data('numm');
		ajaxdataa['ux']= OBJP.data('ux_val');

		$.ajax({
			beforeSend: function(){	},
			type: 'POST',
			url:evoli_ajax_script.evoli_ajaxurl,
			data: ajaxdataa,
			dataType:'json',
			success:function(data){
				LIST = OBJ.closest('.EVOLI_container').find('.EVOLI_event_list_in');
				LIST.html(data.content);
				LIST.siblings('p.EVOLI_section').find('span').html(OBJ.data('section'));

				// move to top of the list
					POS = OBJP.offset();
					$("html, body").animate({ scrollTop: (POS.top -40) }, "slow");

			},complete:function(){

				OBJ.parent().removeClass('loading');
				_WIDTH = OBJ.parent().width();				
				OBJ.closest('.EVOLI_container').animate({'margin-left':'-'+_WIDTH}, 200).addClass('moved');
			}
		});
	});	
	
	// back to list
		$('body').on('click','.EVOLI_back_btn',function(){
			EVOLI = $(this).closest('.EVOLI');
			EVOLI.find('.EVOLI_container').removeClass('moved').animate({'margin-left':0},200,function(){
				EVOLI.find('.EVOLI_event_list_in').html('');
			});
		});
});