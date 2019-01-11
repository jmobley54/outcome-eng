jQuery(document).ready(function(){ 

    
	jQuery('#colorSelector').ColorPicker({
			color: '#0000ff',
			onShow: function (colpkr) {
				jQuery(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				jQuery(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				jQuery('#colorSelector,#tmnf_post_bg').css('backgroundColor', '#' + hex);
				jQuery('#colorSelector').val('#' + hex); 
			}
		});
		
	jQuery('#colorSelector2').ColorPicker({
			color: '#0000ff',
			onShow: function (colpkr) {
				jQuery(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				jQuery(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				jQuery('#colorSelector2,#tmnf_post_text').css('backgroundColor', '#' + hex);
				jQuery('#colorSelector2').val('#' + hex); 
			}
		});
	

    }); 