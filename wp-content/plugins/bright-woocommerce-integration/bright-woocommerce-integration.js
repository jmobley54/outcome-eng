

// run this after templates are rendered
Bright.addHook('after_load',function() {
  setTimeout(function () {
    jQuery(".license-key-widget-wrapper input[type='text']").each(function () {
      if (Bright.parseURL(document.location).params.license_key) {
        jQuery(this).val(Bright.parseURL(document.location).params.license_key);
//        jQuery(".license-key-widget-wrapper input[type='Submit']").click();
      }
    });
  });
});
