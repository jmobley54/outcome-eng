/*! @preserve
 * Plugin Name:       Woo Align Buttons
 * Plugin URI:        https://wordpress.org/plugins/woo-align-buttons
 * Description:       A lightweight plugin to align WooCommerce "Add to cart" buttons.
 * Version:           3.5.3
 * Author:            320up
 * Author URI:        https://320up.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
(function($) {
  "use strict";
  if ($("ul.products").length) {
    var wooAlignButtons = function() {
      $("ul.products").each(function() {
        var gridRows = [];
        var tempRow = [];
        var wooGridElements = $(this).children("li");
        wooGridElements.each(function(index) {
          if ($(this).css("clear") !== "none" && index !== 0) {
            gridRows.push(tempRow);
            tempRow = [];
          }
          tempRow.push(this);
          if (wooGridElements.length === index + 1) {
            gridRows.push(tempRow);
          }
        });
        $.each(gridRows, function() {
          var tallestWooInfo = 0;
          $.each(this, function() {
            $(this).find("#woo-height").css({
              "min-height": "",
              "padding-bottom": ""
            });
            var wooInfoHeight = $(this).find("#woo-height").height();
            var wooSpacing = 10;
            var totalHeight = wooInfoHeight + wooSpacing;
            if (totalHeight > tallestWooInfo) {
              tallestWooInfo = totalHeight;
            }
          });
          $.each(this, function() {
            $(this).find("#woo-height").css("min-height", tallestWooInfo);
          });
        });
      });
    };
    wooAlignButtons();
    $(window).on("load", function() {
      wooAlignButtons();
      $(window).on("hold-your-horses", function() {
        wooAlignButtons();
      });
    });
  }
  var resizeTimer;
  $(window).resize(function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      $(window).trigger("hold-your-horses");
    }, 250);
  });
  // Scroll function for ajax load more issues
  // Uncomment next 3 lines to use
  //$(window).on("scroll", function() {
    //wooAlignButtons();
  //});
  // End scroll function
})(jQuery);
