 
var $j = jQuery.noConflict();

// website_ajax_url = '/wordpress/wp-admin/admin-ajax.php';

//Replace the wordpress with your website url as
//website_ajax_url = 'https://www.google.com/wp-admin/admin-ajax.php';

//jQuery(document).click('click', '.asd', function(){
//	alert('hi');
//});

jQuery(document).ready(function() {

	//Handles the sorting

	jQuery(".sortable_table").sortable({ 
		update: function (ev, tbody) {
			var current_table_name = jQuery('.checkbox-class:checked').val();
			var obj = {};
			var counter = 0;
			jQuery('.'+current_table_name+' > tbody  > tr').each(function() {
				if(jQuery(this).attr('data-key') != ""){
					var get_place_holder = jQuery(this).attr('data-key');}
//                   console.log(get_place_holder);
				if(get_place_holder != "undefined"){
					counter++;
					obj[get_place_holder] = counter;
				}

			});
			var form_data = {
				action : 'savefieldorder',
				pass_obj : obj,
				pass_current_table_name : current_table_name,
			};
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: form_data,
				success: function(data) {
				}
			});
		}
	});
	
	//initialises the date pickers
	jQuery('.jemexp-datepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});
	
	//Vertical tabs on export tab
    //jQuery( "#export-tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
    //jQuery( "#export-tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

    
	
	//Vertical Tabs
	jQuery('div.vert-tabs-container').each(function(){  
		jQuery(this).find('div.jemex-panel:not(:first)').hide();
	});
	
	jQuery('ul.jemex-vert-tabs a').click(function(){
		var outer_panel =  jQuery(this).closest('div.vert-tabs-container');
		jQuery('ul.jemex-vert-tabs li', outer_panel).removeClass('active');
		jQuery(this).parent().addClass('active');
		jQuery('div.jemex-panel', outer_panel).hide();
		jQuery( jQuery(this).attr('href') ).show();
		return false;
	});
	
	
	//Lets handle the clicks when a new export type is selected
	jQuery("input[type=radio][name=datatype]").on('change', function(){
		

		var type = jQuery(this).attr("value");

		//console.log('it changed' + jQuery(this).attr("value"));

		//the label contains the text if it is translated to another language
		var label= "label[for='" + type + "']";
		var transtext =  jQuery(this).next(label).text();
		//console.log('it changed' + jQuery(this).next(label).text());

		$entity = jQuery(this).attr("value") + '-div';
		
		//we need to load the field, labels & filter tabs with the appropriate data...
		//first hide all fields
		jQuery(".export-fields").css("display", "none");
		
		//and display our one
		jQuery('#' + $entity).css("display", "block");
		
		//do the same for the labels
		//first hide all labels
		jQuery(".export-labels").css("display", "none");
		
		//and display our one
		$entity = jQuery(this).attr("value") + '-labels-div';
		jQuery('#' + $entity).css("display", "block");
		
		//do the same for the filters
		//first hide all labels
		jQuery(".export-filters").css("display", "none");
		
		//and display our one
		$entity = jQuery(this).attr("value") + '-filters-div';
		jQuery('#' + $entity).css("display", "block");
		
		//do the same for the SCHEDULED
		//first hide all labels
		jQuery(".export-scheduled").css("display", "none");
		
		//and display our one
		$entity = jQuery(this).attr("value") + '-scheduled-div';
		jQuery('#' + $entity).css("display", "block");
		
		//and change the title!
		//jQuery('#entity-type-title').text(jQuery(this).attr("value"));
		jQuery('#entity-type-title').text(transtext);

		//update the hidden field with the name of the entity
		jQuery('#entity-being-edited').val(jQuery(this).attr("value"));
		
		//update the hidden field for the export
		jQuery('#entity-to-export').val(jQuery(this).attr("value"));
		
		//update the Submit Button
		jQuery('#submit-export').val('Export ' +  transtext );
		
		//update the Submit Button
		jQuery('#submit2-export').val('Export ' +  transtext );
		
		//do we need to disable it?
		if(jQuery(this).attr("value") != "Product" && jQuery(this).attr("value") != "Order"){
			jQuery('#submit-export').addClass('button');
			jQuery('#submit-export').addClass('button-disabled');
			jQuery('#submit-export').removeClass('button-primary');
			jQuery('#submit-export').removeClass('button-primary');
			jQuery('#submit-export').attr('type', 'button');

			jQuery('#submit2-export').addClass('button');
			jQuery('#submit2-export').addClass('button-disabled');
			jQuery('#submit2-export').removeClass('button-primary');
			jQuery('#submit2-export').removeClass('button-primary');
			jQuery('#submit2-export').attr('type', 'button');


			
		} else {
			jQuery('#submit-export').removeClass('button');
			jQuery('#submit-export').removeClass('button-disabled');
			jQuery('#submit-export').addClass('button-primary');
			jQuery('#submit-export').attr('type', 'submit');
			
			jQuery('#submit2-export').removeClass('button');
			jQuery('#submit2-export').removeClass('button-disabled');
			jQuery('#submit2-export').addClass('button-primary');
			jQuery('#submit2-export').attr('type', 'submit');
			
		}
	});
	

	tab = $j('#current-tab').html();

	//if we are not editing an existing entity default it
	entity = $j('#current-entity').html();
	
	//Lets default on first load to selecting the first datatype
	if(entity == ''){
		//if for some reason we have a blank entity then default to the first one
		jQuery('input[name=datatype]:first').attr("checked", true).trigger('change');
	} else {
		//other wise we have an entity  lets seacrh for it an activate it, the id is simply the entity value e.g. Order, Product etc
		jQuery('#' + entity).attr("checked", true).trigger('change');

		
	}

	//lets handle the export select all and select none
	
	//Select ALL
	$j("#export-select-all").click(function(){
		
		//first we find the labels that are visible - these are the active ones, then the input fields
		$j('.export-fields:visible input').prop('checked', true);
	});

	//Select NONE
	$j("#export-select-none").click(function(){
		
		//first we find the labels that are visible - these are the active ones, then the input fields
		$j('.export-fields:visible input').prop('checked', false);
	});

	
	//*****************
	//FILTER STUFF ORDERS
	//*****************
	
	//Handle select ALL & NONE on order filter
	
	//Select ALL
	$j("#order-filter-select-all-status").click(function(){
		
		$j('.jem-order-status input').prop('checked', true);
		return false;
	});

	$j("#order-filter-select-none-status").click(function(){ 
		
		$j('.jem-order-status input').prop('checked', false);
		return false;
	});

	//Lets set the correct tab
	tab = $j('#current-tab').html();
	subTab = $j('#current-sub-tab').html();
	console.log('we are here ' + tab + 'sub ' + subTab);
	selectTab(tab, subTab);

	//LETS SET THE DEFAULTS
	//$j('.export-fields input[type=checkbox]').prop('checked', true);
	$j('.jem-order-status input[type=checkbox]').prop('checked', true);
        
	// JS for sortable label for order and product export
	//jQuery('.sortable_table').sortable();
	$j('.sortable_table').sortable();



	//Simon 2.0.6 - save defaults functionality added
	//************************************
	// Handle clicks on SAVE AS DEFAULT
	//************************************
	$j('.jemx_save_defaults').click(function(){
		//change the destination to save default
		$j("[name='action']").attr('value', 'save_defaults');

	});
});




//Selects the right tabs & also the entity
function selectTab(tab, subTab){
	console.log('got a tab' + tab + 'with sub ' + subTab);
	
	
	//lets activate the apropriate tab
	//we fake a click and the rest takes place automagically
	switch(tab){
	
		//EXPORT TAB
		case "export" :
			switch(subTab){
			case "fields":
				$j("#vert-field-tab").trigger("click");
				break;
				
			case "labels":
				$j("#vert-label-tab").trigger("click");
				break;
				
			case "filters":
				$j("#vert-filter-tab").trigger("click");
				break;
				
			}
			break
			
		//SETTINGS TAB
		case "settings" :
			break;
	}
	
	
}


//jQuery(document).ready( function() {
//
//   jQuery(".call_to_ajax").click( function(event) {
//
//      event.preventDefault();
//
//      post_id = jQuery('#entity-to-export').val();
//
//      //if using the form the serialize the form data
//         //var serializedData = jQuery("#postform").serialize();
//         var serializedData = jQuery("#postform").serializeArray();
//         // var serializedData = jQuery('input[type=checkbox]').val();
//
//      jQuery.ajax({
//         type : "post",
//         dataType : "json",
//         url : ajaxurl,
//         statusCode: {
//              500: function() {
//                  alert(" 500 data still loading");
//                  console.log('500 ');
//              }
//          },
//
//         data : {action: "my_function", post_id : post_id, form_data : serializedData },
//
//         //if using the form then use this
//         //data : serializedData,
//
//         error: function(xhr, status, error) {
//            var err = eval("(" + xhr.responseText + ")");
//            alert(err.Message);
//        },
//         success: function(response) {
//          jQuery(".display_success_msg").css("display", "block");
//
//        },
//      });
//
//   });
//
//});
