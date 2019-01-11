
var maxAdminLicense = function()
{

};

maxAdminLicense.prototype.init = function ()
{
  // license interface
//  $(document).on('submit', '#license_form', $.proxy(this.activateLicense, this) );
  $(document).on('maxajax_success_activate_license', $.proxy(this.ajaxSuccess, this));
  $(document).on('maxajax_success_deactivate_license', $.proxy(this.ajaxSuccess, this));

  $(document).on('maxajax_formpost_activate_license', $.proxy(this.ajaxpost, this));
  $(document).on('maxajax_formpost_deactivate_license', $.proxy(this.ajaxpost, this));

}

maxAdminLicense.prototype.ajaxpost = function(data, target)
{
    $('#ajax_error').hide();
    $('#ajax_error').find('h4').html('');

}

maxAdminLicense.prototype.ajaxError = function(e)
{
  $('#ajax_error').show();
}

maxAdminLicense.prototype.ajaxSuccess = function(e,result, status, object)
{
    if (typeof result !== 'object') // no clue
      var result = JSON.parse(result);

  if (result.status == 'error')
  {
      if (typeof result.additional_info !== 'undefined')
      {
        $('#ajax_error').find('h3').html(result.additional_info);
      }
      $('#ajax_error').show();

  }
  else if (result.status == 'success')
  {
      window.location.reload(true);
  }

}
