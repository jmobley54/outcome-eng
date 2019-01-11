jQuery(window).load(function() {
/*global jQuery:false */
"use strict";
	
  jQuery('.singleslider').flexslider({
		animation: "fade",
		animationDuration: 500,
		slideshowSpeed: 9000,
		pauseOnHover: true,
		controlNav: true,
		directionNav: true,
		manualControls: ".tmnf_slideshow_thumbnails li a",
		smoothHeight: true
    });
  
});