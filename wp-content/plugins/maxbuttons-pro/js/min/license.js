var maxAdminLicense=function(){};maxAdminLicense.prototype.init=function(){$(document).on("maxajax_success_activate_license",$.proxy(this.ajaxSuccess,this)),$(document).on("maxajax_success_deactivate_license",$.proxy(this.ajaxSuccess,this)),$(document).on("maxajax_formpost_activate_license",$.proxy(this.ajaxpost,this)),$(document).on("maxajax_formpost_deactivate_license",$.proxy(this.ajaxpost,this))},maxAdminLicense.prototype.ajaxpost=function(a,o){$("#ajax_error").hide(),$("#ajax_error").find("h4").html("")},maxAdminLicense.prototype.ajaxError=function(a){$("#ajax_error").show()},maxAdminLicense.prototype.ajaxSuccess=function(a,o,t,e){if("object"!=typeof o)o=JSON.parse(o);"error"==o.status?(void 0!==o.additional_info&&$("#ajax_error").find("h3").html(o.additional_info),$("#ajax_error").show()):"success"==o.status&&window.location.reload(!0)};