/*
* TipTip Java
* Author: Karol Heilman
* Version: 1.0
*/

( function( $ ){
	$( document ).ready( function(){
    
		//Enable TipTip on "help_tip" class. Value in "title".
		$(".help_tip").tipTip({
			defaultPosition: "left"
		});
	
	});
})( jQuery );