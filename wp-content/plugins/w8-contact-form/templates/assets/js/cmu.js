(function($){
 var methods = {
    init : function(opts) {
            var selector = jQuery( this ).selector; // Get the selector
            // Set default options
            var defaults = {
                'preview'	 	: '.preview-upload',
                'target'    	: '.uploaded',
                'button'  		: '.button-upload',
				'title'	  		: 'Upload an image',
				'container'		: '<div></div>',
				'indexcontainer': '.slide_container',
				'mode' 			: 'append',
				'type'			: 'multi',
				'callback'		: false
            };
            var opts  = $.extend({}, defaults, opts );
			// When the Button is clicked...
	var openNewImageDialog = function(title,onInsert,isMultiple, opts){
		
		if(isMultiple == undefined)
			isMultiple = false;
		// Media Library params
		var frame = wp.media({
			title : title,
			multiple : isMultiple,
			library : { type : 'image'},
			button : { text : 'Insert' },
			opts: opts
		});

		// Runs on select
		frame.on('select',function(){
			var objSettings = frame.state().get('selection').first().toJSON();
			var selection = frame.state().get('selection');
			var arrImages = [];
			var appender = "";
			var appended_images = jQuery(opts.indexcontainer).length;
			if(isMultiple == true){		//return image object when multiple
			    selection.map( function( attachment ) {
			    	var objImage = attachment.toJSON();
			    	var obj = {};
			    	obj.url = objImage.url;
			    	obj.id = objImage.id;
			    	arrImages.push(obj);
					appended_images++;
				if (opts.type=='multi') appender += opts.container.replace("[content]",objImage.url).replace("objImageUrl",objImage.url).replace('[index]',appended_images);		//including multiple images
				else if (opts.type=='single') appender = opts.container.replace("[content]",objImage.url).replace("objImageUrl",objImage.url).replace('[index]',appended_images);		//including single image in multiple mode
			    });
			}else{
				appended_images++;
				appender += opts.container.replace("[content]",objImage.url).replace("objImageUrl",objImage.url).replace('[index]',appended_images);		//including single image
				}
				if (opts.mode=='append') jQuery(opts.target).append(appender);
				else if (opts.mode=='insert') jQuery(opts.target).html(appender);
				jQuery(opts.callback).call;
		});

		// Open ML
		frame.open();
	}
	
    jQuery( document ).on( "click", opts.button , function() {
		openNewImageDialog('Upload or Select Images','',true, opts);
		return false;
    } );
   }
   }
jQuery.fn.pmu = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            return methods.init.apply( this, arguments );
        } else {
            jQuery.error( 'Method ' +  methodOrOptions + ' does not exist on jQuery.pmu' );
        }    
    };
})( jQuery );