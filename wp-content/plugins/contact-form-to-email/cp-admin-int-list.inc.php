<?php

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

global $wpdb;
$message = "";
if (isset($_GET['a']) && $_GET['a'] == '1')
{
    $verify_nonce = wp_verify_nonce( $_GET['rsave'], 'cfte_update_actions_plist');
    if (!$verify_nonce)
    {
        echo 'Error: Form cannot be authenticated (nonce failed). Please contact our <a href="form2email.dwbooster.com/contact-us">support service</a> for verification and solution. Thank you.';
        return;
    }    
    
    define('CP_CFEMAIL_DEFAULT_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) );
    define('CP_CFEMAIL_DEFAULT_fp_destination_emails', CP_CFEMAIL_DEFAULT_fp_from_email);
    
    $wpdb->insert( $wpdb->prefix.$this->table_items, array( 
                                      'form_name' => stripcslashes($_GET["name"]),

                                      'form_structure' => $this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure),

                                      'fp_from_email' => $this->get_option('fp_from_email', CP_CFEMAIL_DEFAULT_fp_from_email),
                                      'fp_destination_emails' => $this->get_option('fp_destination_emails', CP_CFEMAIL_DEFAULT_fp_destination_emails),
                                      'fp_subject' => $this->get_option('fp_subject', CP_CFEMAIL_DEFAULT_fp_subject),
                                      'fp_inc_additional_info' => $this->get_option('fp_inc_additional_info', CP_CFEMAIL_DEFAULT_fp_inc_additional_info),
                                      'fp_return_page' => $this->get_option('fp_return_page', CP_CFEMAIL_DEFAULT_fp_return_page),
                                      'fp_message' => $this->get_option('fp_message', CP_CFEMAIL_DEFAULT_fp_message),
                                      'fp_emailformat' => $this->get_option('fp_emailformat', CP_CFEMAIL_DEFAULT_email_format),

                                      'cu_enable_copy_to_user' => $this->get_option('cu_enable_copy_to_user', CP_CFEMAIL_DEFAULT_cu_enable_copy_to_user),
                                      'cu_user_email_field' => $this->get_option('cu_user_email_field', CP_CFEMAIL_DEFAULT_cu_user_email_field),
                                      'cu_subject' => $this->get_option('cu_subject', CP_CFEMAIL_DEFAULT_cu_subject),
                                      'cu_message' => $this->get_option('cu_message', CP_CFEMAIL_DEFAULT_cu_message),
                                      'cu_emailformat' => $this->get_option('cu_emailformat', CP_CFEMAIL_DEFAULT_email_format),

                                      'vs_use_validation' => $this->get_option('vs_use_validation', CP_CFEMAIL_DEFAULT_vs_use_validation),
                                      'vs_text_is_required' => $this->get_option('vs_text_is_required', CP_CFEMAIL_DEFAULT_vs_text_is_required),
                                      'vs_text_is_email' => $this->get_option('vs_text_is_email', CP_CFEMAIL_DEFAULT_vs_text_is_email),
                                      'vs_text_datemmddyyyy' => $this->get_option('vs_text_datemmddyyyy', CP_CFEMAIL_DEFAULT_vs_text_datemmddyyyy),
                                      'vs_text_dateddmmyyyy' => $this->get_option('vs_text_dateddmmyyyy', CP_CFEMAIL_DEFAULT_vs_text_dateddmmyyyy),
                                      'vs_text_number' => $this->get_option('vs_text_number', CP_CFEMAIL_DEFAULT_vs_text_number),
                                      'vs_text_digits' => $this->get_option('vs_text_digits', CP_CFEMAIL_DEFAULT_vs_text_digits),
                                      'vs_text_max' => $this->get_option('vs_text_max', CP_CFEMAIL_DEFAULT_vs_text_max),
                                      'vs_text_min' => $this->get_option('vs_text_min', CP_CFEMAIL_DEFAULT_vs_text_min),                                       

                                      'cv_enable_captcha' => $this->get_option('cv_enable_captcha', CP_CFEMAIL_DEFAULT_cv_enable_captcha),
                                      'cv_width' => $this->get_option('cv_width', CP_CFEMAIL_DEFAULT_cv_width),
                                      'cv_height' => $this->get_option('cv_height', CP_CFEMAIL_DEFAULT_cv_height),
                                      'cv_chars' => $this->get_option('cv_chars', CP_CFEMAIL_DEFAULT_cv_chars),
                                      'cv_font' => $this->get_option('cv_font', CP_CFEMAIL_DEFAULT_cv_font),
                                      'cv_min_font_size' => $this->get_option('cv_min_font_size', CP_CFEMAIL_DEFAULT_cv_min_font_size),
                                      'cv_max_font_size' => $this->get_option('cv_max_font_size', CP_CFEMAIL_DEFAULT_cv_max_font_size),
                                      'cv_noise' => $this->get_option('cv_noise', CP_CFEMAIL_DEFAULT_cv_noise),
                                      'cv_noise_length' => $this->get_option('cv_noise_length', CP_CFEMAIL_DEFAULT_cv_noise_length),
                                      'cv_background' => $this->get_option('cv_background', CP_CFEMAIL_DEFAULT_cv_background),
                                      'cv_border' => $this->get_option('cv_border', CP_CFEMAIL_DEFAULT_cv_border),
                                      'cv_text_enter_valid_captcha' => $this->get_option('cv_text_enter_valid_captcha', CP_CFEMAIL_DEFAULT_cv_text_enter_valid_captcha)
                                     )
                      );   
    
    $message = "Item added";
} 
else if (isset($_GET['u']) && $_GET['u'] != '')
{    
    $verify_nonce = wp_verify_nonce( $_GET['rsave'], 'cfte_update_actions_plist');
    if (!$verify_nonce)
    {
        echo 'Error: Form cannot be authenticated (nonce failed). Please contact our <a href="form2email.dwbooster.com/contact-us">support service</a> for verification and solution. Thank you.';
        return;
    }       
    $wpdb->query( $wpdb->prepare( 'UPDATE `'.$wpdb->prefix.$this->table_items.'` SET form_name=%s WHERE id=%d', $_GET["name"], $_GET['u'] ) );
    $message = "Item updated";        
}
else if (isset($_GET['d']) && $_GET['d'] != '')
{    
    $verify_nonce = wp_verify_nonce( $_GET['rsave'], 'cfte_update_actions_plist');
    if (!$verify_nonce)
    {
        echo 'Error: Form cannot be authenticated (nonce failed). Please contact our <a href="form2email.dwbooster.com/contact-us">support service</a> for verification and solution. Thank you.';
        return;
    }   
    $wpdb->query( $wpdb->prepare( 'DELETE FROM `'.$wpdb->prefix.$this->table_items.'` WHERE id=%d', intval($_GET['d']) ) );
    $message = "Item deleted";
} else if (isset($_GET['c']) && $_GET['c'] != '')
{    
    $verify_nonce = wp_verify_nonce( $_GET['rsave'], 'cfte_update_actions_plist');
    if (!$verify_nonce)
    {
        echo 'Error: Form cannot be authenticated (nonce failed). Please contact our <a href="form2email.dwbooster.com/contact-us">support service</a> for verification and solution. Thank you.';
        return;
    }       
    $myrows = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->table_items." WHERE id=%d", $_GET['c'] ), ARRAY_A);    
    unset($myrows["id"]);
    $myrows["form_name"] = 'Cloned: '.$myrows["form_name"];
    $wpdb->insert( $wpdb->prefix.$this->table_items, $myrows);
    $message = "Item duplicated/cloned";
}
else if (isset($_GET['ac']) && $_GET['ac'] == 'st')
{   
    $verify_nonce = wp_verify_nonce( $_GET['rsave'], 'cfte_update_actions_plist');
    if (!$verify_nonce)
    {
        echo 'Error: Form cannot be authenticated (nonce failed). Please contact our <a href="form2email.dwbooster.com/contact-us">support service</a> for verification and solution. Thank you.';
        return;
    }       
    update_option( 'CP_CFTE_LOAD_SCRIPTS', ($_GET["scr"]=="1"?"0":"1") );   
    if ($_GET["chs"] != '')
    {
        $target_charset = esc_sql($_GET["chs"]);
        $tables = array( $wpdb->prefix.$this->table_messages, $wpdb->prefix.$this->table_items );                
        foreach ($tables as $tab)
        {  
            $myrows = $wpdb->get_results( "DESCRIBE {$tab}" );                                                                                 
            foreach ($myrows as $item)
	        {
	            $name = $item->Field;
		        $type = $item->Type;
		        if (preg_match("/^varchar\((\d+)\)$/i", $type, $mat) || !strcasecmp($type, "CHAR") || !strcasecmp($type, "TEXT") || !strcasecmp($type, "MEDIUMTEXT"))
		        {
	                $wpdb->query("ALTER TABLE {$tab} CHANGE {$name} {$name} {$type} COLLATE {$target_charset}");	            
	            }
	        }
        }
    }
    $message = "Troubleshoot settings updated";
} 
else if (isset($_POST["cp_cfte_rep_enable"]))
{
    update_option( 'cp_cfte_rep_enable', $_POST["cp_cfte_rep_enable"]);
    update_option( 'cp_cfte_rep_days', $_POST["cp_cfte_rep_days"]);
    update_option( 'cp_cfte_rep_hour', $_POST["cp_cfte_rep_hour"]);
    update_option( 'cp_cfte_rep_emails', $_POST["cp_cfte_rep_emails"]);
    update_option( 'cp_cfte_fp_from_email', $_POST["cp_cfte_fp_from_email"]);
    update_option( 'cp_cfte_rep_subject', $_POST["cp_cfte_rep_subject"]);
    update_option( 'cp_cfte_rep_emailformat', $_POST["cp_cfte_rep_emailformat"]);
    update_option( 'cp_cfte_rep_message', $_POST["cp_cfte_rep_message"]);
    $message = "Report settings updated";
}


if ($message) echo "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>".$message."</strong></p></div>";

$nonce = wp_create_nonce( 'cfte_update_actions_plist' );

?>
<div class="wrap">
<h1><?php echo $this->plugin_name; ?></h1>

<script type="text/javascript">
 function cp_addItem()
 {
    var calname = document.getElementById("cp_itemname").value;
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&rsave=<?php echo $nonce; ?>&a=1&r='+Math.random()+'&name='+encodeURIComponent(calname);       
 }
 
 function cp_updateItem(id)
 {
    var calname = document.getElementById("calname_"+id).value;    
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&rsave=<?php echo $nonce; ?>&u='+id+'&r='+Math.random()+'&name='+encodeURIComponent(calname);    
 }
 
 function cp_cloneItem(id)
 {
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&rsave=<?php echo $nonce; ?>&c='+id+'&r='+Math.random();  
 } 
 
 function cp_manageSettings(id)
 {
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&cal='+id+'&r='+Math.random();
 }
 
 function cp_publish(id)
 {
     document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&pwizard=1&cal='+id+'&r='+Math.random();
 } 
 
 function cp_viewMessages(id)
 {
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&cal='+id+'&list=1&r='+Math.random();
 } 
 
 function cp_viewReport(id)
 {
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&cal='+id+'&report=1&r='+Math.random();
 } 
 
 function cp_deleteItem(id)
 {
    if (confirm('Are you sure that you want to delete this item?'))
    {        
        document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&rsave=<?php echo $nonce; ?>&d='+id+'&r='+Math.random();
    }
 }
 
 function cp_updateConfig()
 {
    if (confirm('Are you sure that you want to update these settings?'))
    {        
        var scr = document.getElementById("ccscriptload").value;    
        var chs = document.getElementById("cccharsets").value;    
        document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&rsave=<?php echo $nonce; ?>&ac=st&scr='+scr+'&chs='+chs+'&r='+Math.random();
    }    
 }
 
</script>


<div id="normal-sortables" class="meta-box-sortables">


 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Form List / Items List</span></h3>
  <div class="inside">
  
  
  <table cellspacing="10" cellpadding="6" class="ahb-calendars-list"> 
   <tr>
    <th align="left">ID</th><th align="left">Form Name</th><th align="left">&nbsp; &nbsp; Options</th><th align="left">Shortcode for Pages and Posts</th>
   </tr> 
<?php  

  $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );                                                                     
  foreach ($myrows as $item)         
  {
?>
   <tr> 
    <td nowrap><?php echo $item->id; ?></td>
    <td nowrap><input type="text" name="calname_<?php echo $item->id; ?>" id="calname_<?php echo $item->id; ?>" value="<?php echo esc_attr($item->form_name); ?>" /></td>          
    
    <td>
                             <input style="margin-bottom:3px" class="button" type="button" name="calupdate_<?php echo $item->id; ?>" value="Rename" onclick="cp_updateItem(<?php echo $item->id; ?>);" /> 
                             <input style="margin-bottom:3px" class="button-primary button" type="button" name="calmanage_<?php echo $item->id; ?>" value="Settings" onclick="cp_manageSettings(<?php echo $item->id; ?>);" /> 
                             <input style="margin-bottom:3px" class="button-primary button" type="button" name="calpublish_<?php echo $item->id; ?>" value="<?php _e('Publish','cpappb'); ?>" onclick="cp_publish(<?php echo $item->id; ?>);" />                              
                             <input style="margin-bottom:3px" class="button" type="button" name="calmessages_<?php echo $item->id; ?>" value="Messages" onclick="cp_viewMessages(<?php echo $item->id; ?>);" />                              
                             <input style="margin-bottom:3px" class="button" type="button" name="calreport_<?php echo $item->id; ?>" value="Stats" onclick="cp_viewReport(<?php echo $item->id; ?>);" />                            
                             <input style="margin-bottom:3px" class="button" type="button" name="calclone_<?php echo $item->id; ?>" value="Clone" onclick="cp_cloneItem(<?php echo $item->id; ?>);" />                             
                             <input style="margin-bottom:3px" class="button" type="button" name="caldelete_<?php echo $item->id; ?>" value="Delete" onclick="cp_deleteItem(<?php echo $item->id; ?>);" />                             
    </td>
    <td><nobr>[<?php echo $this->shorttag; ?> id="<?php echo $item->id; ?>"]</nobr></td>          
   </tr>
<?php  
   } 
?>   
     
  </table> 
    
    <div class="clearer"></div>
   
  </div>    
 </div> 
 

<div class="ahb-section-container">
	<div class="ahb-section">
		<label>New Form</label>&nbsp;&nbsp;&nbsp;
		<input type="text" name="cp_itemname" id="cp_itemname" placeholder=" - Form Name - " class="ahb-new-calendar" />
		<input type="button" class="button-primary" value="Add New" onclick="cp_addItem();" />
	</div>
</div>


 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Automatic email reports for ALL forms: Send submissions in CSV format via email</span></h3>
  <div class="inside">
     <form name="updatereportsettings" action="" method="post">
     <table class="form-table">    
        <tr valign="top">
        <td scope="row" colspan="2">Enable Reports?
          <?php $option = get_option('cp_cfte_rep_enable', 'no'); ?>
          <select name="cp_cfte_rep_enable">
           <option value="no"<?php if ($option == 'no' || $option == '') echo ' selected'; ?>>No</option>
           <option value="yes"<?php if ($option == 'yes') echo ' selected'; ?>>Yes</option>
          </select>     
          &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
          Send report every: <input type="text" name="cp_cfte_rep_days" size="1" value="<?php echo esc_attr(get_option('cp_cfte_rep_days', '7')); ?>" /> days
          &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
          Send after this hour (server time):
          <select name="cp_cfte_rep_hour">
           <?php
             $hour = get_option('cp_cfte_rep_hour', '0');
             for ($k=0;$k<24;$k++)
                 echo '<option value="'.$k.'"'.($hour==$k?' selected':'').'>'.($k<10?'0':'').$k.'</option>';
           ?>
          </select>
        </td>
        <tr valign="top">
        <th scope="row">Send email from</th>
        <td><input type="text" name="cp_cfte_fp_from_email" size="70" value="<?php echo esc_attr(get_option('cp_cfte_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) )); ?>" /></td>
        </tr>       
        <tr valign="top">
        <th scope="row">Send to email(s)</th>
        <td><input type="text" name="cp_cfte_rep_emails" size="70" value="<?php echo esc_attr(get_option('cp_cfte_rep_emails', '')); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Email subject</th>
        <td><input type="text" name="cp_cfte_rep_subject" size="70" value="<?php echo esc_attr(get_option('cp_cfte_rep_subject', 'Submissions report...')); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Email format?</th>
        <td>
          <?php $option = get_option('cp_cfte_rep_emailformat', 'text'); ?>
          <select name="cp_cfte_rep_emailformat">
           <option value="text"<?php if ($option != 'html') echo ' selected'; ?>>Plain Text (default)</option>
           <option value="html"<?php if ($option == 'html') echo ' selected'; ?>>HTML (use html in the textarea below)</option>
          </select>
        </td>
        </tr>  
        <tr valign="top">
        <th scope="row">Email Text (CSV file will be attached)</th>
        <td><textarea type="text" name="cp_cfte_rep_message" rows="3" cols="80"><?php echo htmlspecialchars(get_option('cp_cfte_rep_message', 'Attached you will find the data from the form submissions.')); ?></textarea></td>
        </tr>        
        <tr valign="top">
        <th scope="row"></th>
        <td><input class="button-primary button" type="submit" name="cftesubbtn" value="Update Report Settings" /></td>
        </tr>        
     </table>       
     <p>Note: For setting up a report only for a specific form use the setting area available for that when editing each form settings.</p>
     </form>
  </div>    
 </div>


 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Troubleshoot Area</span></h3>
  <div class="inside"> 
    <p><strong>Important!</strong>: Use this area <strong>only</strong> if you are experiencing conflicts with third party plugins, with the theme scripts or with the character encoding.</p>
    <form name="updatesettings">
      Script load method:<br />
       <select id="ccscriptload" name="ccscriptload">
        <option value="0" <?php if (get_option('CP_CFTE_LOAD_SCRIPTS',"1") == "1") echo 'selected'; ?>>Classic (Recommended)</option>
        <option value="1" <?php if (get_option('CP_CFTE_LOAD_SCRIPTS',"1") != "1") echo 'selected'; ?>>Direct</option>
       </select><br />
       <em>* Change the script load method if the form doesn't appear in the public website.</em>
      
      <br /><br />
      Character encoding:<br />
       <select id="cccharsets" name="cccharsets">
        <option value="">Keep current charset (Recommended)</option>
        <option value="utf8_general_ci">UTF-8 (try this first)</option>
        <option value="latin1_swedish_ci">latin1_swedish_ci</option>
        <option value="hebrew_general_ci">hebrew_general_ci</option>
        <option value="gb2312_chinese_ci">gb2312_chinese_ci</option>        
       </select><br />
       <em>* Update the charset if you are getting problems displaying special/non-latin characters. After updated you need to edit the special characters again.</em>
       <br />
       <input class="button-primary button" type="button" onclick="cp_updateConfig();" name="gobtn" value="UPDATE" />
      <br /><br />      
    </form>

  </div>    
 </div>
 
<div id="addonsarea" >
<a name="addons-section"></a> 
<div id="metabox_basic_settings" class="postbox" >
	<h3 class='hndle' style="padding:5px;"><span>Add-ons Area</span></h3>
	<div class="inside"> 
    <style type="text/css">
    .cpfieldset { 
        border: 1px groove threedface;
        padding: 5px;
        width:400px;
        margin-right:10px;
    }
    .cpfieldset legend { font-weight: bold; color: #009900; } 
    </style>
    <fieldset class="cpfieldset" style="float:left;"><legend>Payment Gateways Integration</legend>
    <div><label for="addon-AuthNetSIM-20160910" style="font-weight:bold;"><input type="checkbox" disabled id="addon-AuthNetSIM-20160910" name="cfte_addons" value="addon-AuthNetSIM-20160910" >Authorize.net Server Integration Method</label> <div style="font-style:italic;padding-left:20px;">The add-on adds support for Authorize.net Server Integration Method payments</div></div>
    <div><label for="addon-idealmollie-20151212" style="font-weight:bold;"><input type="checkbox" disabled id="addon-idealmollie-20151212" name="cfte_addons" value="addon-idealmollie-20151212" >iDeal Mollie</label> <div style="font-style:italic;padding-left:20px;">The add-on adds support for iDeal via Mollie payments</div></div><div>
    <label for="addon-idealtargetpay-20151212" style="font-weight:bold;"><input type="checkbox" disabled id="addon-idealtargetpay-20151212" name="cfte_addons" value="addon-idealtargetpay-20151212" >iDeal TargetPay</label> <div style="font-style:italic;padding-left:20px;">The add-on adds support for iDeal via TargetPay payments</div></div><div>    
    <label for="addon-PayPalStandard-20170903" style="font-weight:bold;"><input type="checkbox" disabled id="addon-PayPalStandard-20170903" name="cfte_addons" value="addon-PayPalStandard-20170903" >PayPal Standard Payments Integration</label> <div style="font-style:italic;padding-left:20px;">The add-on adds support for PayPal Standard payments</div></div><div>
    <label for="addon-paypalpro-20151212" style="font-weight:bold;"><input type="checkbox" disabled id="addon-paypalpro-20151212" name="cfte_addons" value="addon-paypalpro-20151212" >PayPal Pro</label> <div style="font-style:italic;padding-left:20px;">The add-on adds support for PayPal Payment Pro payments to accept credit cars directly into the website</div></div><div>  
    <label for="addon-sabtpv-20151212" style="font-weight:bold;"><input type="checkbox" disabled id="addon-sabtpv-20151212" name="cfte_addons" value="addon-sabtpv-20151212" >RedSys TPV</label> <div style="font-style:italic;padding-left:20px;">The add-on adds support for RedSys TPV payments</div></div><div>
    <label for="addon-SagePay-20160706" style="font-weight:bold;"><input type="checkbox" disabled id="addon-SagePay-20160706" name="cfte_addons" value="addon-SagePay-20160706" >SagePay Payment Gateway</label> <div style="font-style:italic;padding-left:20px;">The add-on adds support for SagePay payments</div></div><div>
    <label for="addon-SagePayments-20160706" style="font-weight:bold;"><input type="checkbox" disabled id="addon-SagePayments-20160706" name="cfte_addons" value="addon-SagePayments-20160706" >SagePayments Payment Gateway</label> <div style="font-style:italic;padding-left:20px;">The add-on adds support for SagePayments payments</div></div><div>    
    <label for="addon-Skrill-20170903" style="font-weight:bold;"><input type="checkbox" disabled id="addon-Skrill-20170903" name="cfte_addons" value="addon-Skrill-20170903" >Skrill Payments Integration</label> <div style="font-style:italic;padding-left:20px;">The add-on adds support for Skrill payments</div></div><div>
    <label for="addon-stripe-20151212" style="font-weight:bold;"><input type="checkbox" disabled id="addon-stripe-20151212" name="cfte_addons" value="addon-stripe-20151212" >Stripe</label> <div style="font-style:italic;padding-left:20px;">The add-on adds support for Stripe payments</div></div>
    </fieldset>
    
    <fieldset class="cpfieldset"><legend>Improvements</legend>
    <div><label for="addon-cfficalattachment-20180730" style="font-weight:bold;"><input type="checkbox" disabled id="addon-cfficalattachment-20180730" name="cfte_addons" value="addon-cfficalattachment-20180730" >iCal Export Attached</label> <div style="font-style:italic;padding-left:20px;">The add-on allows to attach an iCal file with the date of a field</div></div><div>
    <label for="addon-uploads-20160330" style="font-weight:bold;"><input type="checkbox" disabled id="addon-uploads-20160330" name="cfte_addons" value="addon-uploads-20160330" >Uploads</label> <div style="font-style:italic;padding-left:20px;">The add-on allows to add the uploaded files to the Media Library, and the support for new mime types</div></div><div>    
    <label for="addon-signature-20171025" style="font-weight:bold;"><input type="checkbox" disabled id="addon-signature-20171025" name="cfte_addons" value="addon-signature-20171025">Signature Fields</label> <div style="font-style:italic;padding-left:20px;">The add-on allows to replace form fields with "Signature" fields</div></div><div>    
    <label for="addon-signature-20171025" style="font-weight:bold;"><input type="checkbox" disabled id="addon-paymentcalculations-20171025" name="cfte_addons" value="addon-paymentcalculations-20171025">Payment Calculations</label> <div style="font-style:italic;padding-left:20px;">The add-on allows to add the number values on dropdowns / checkboxes / radiobuttons to the total price for payments</div></div>  
    </fieldset>
    
    
    <fieldset class="cpfieldset"><legend>Integration with third party services</legend>
    <div><label for="addon-mailchimp-20170504" style="font-weight:bold;"><input type="checkbox" disabled id="addon-mailchimp-20170504" name="cfte_addons" value="addon-mailchimp-20170504" >MailChimp</label> <div style="font-style:italic;padding-left:20px;">The add-on creates MailChimp List members with the submitted information</div></div><div> 
    <label for="addon-recaptcha-20151106" style="font-weight:bold;"><input type="checkbox" disabled id="addon-recaptcha-20151106" name="cfte_addons" value="addon-recaptcha-20151106" >reCAPTCHA</label> <div style="font-style:italic;padding-left:20px;">The add-on allows to protect the forms with reCAPTCHA service of Google</div></div><div>
    <label for="addon-salesforce-20150311" style="font-weight:bold;"><input type="checkbox" disabled id="addon-salesforce-20150311" name="cfte_addons" value="addon-salesforce-20150311" >SalesForce</label> <div style="font-style:italic;padding-left:20px;">The add-on allows create SalesForce leads with the submitted information</div></div><div>
    <label for="addon-webhook-20150403" style="font-weight:bold;"><input type="checkbox" disabled id="addon-webhook-20150403" name="cfte_addons" value="addon-webhook-20150403" >WebHook</label> <div style="font-style:italic;padding-left:20px;">The add-on allows put the submitted information to a webhook URL, and integrate the forms with the Zapier service</div></div>
    </fieldset>
    
    <div style="clear:both"></div>
    
    <fieldset class="cpfieldset" style="float:left;"><legend>SMS Text Delivery</legend>
    <div><label for="addon-twilio-20170403" style="font-weight:bold;"><input type="checkbox" disabled id="addon-twilio-20170403" name="cfte_addons" value="addon-twilio-20170403" >Twilio</label> <div style="font-style:italic;padding-left:20px;">The add-on allows to send notification messages (SMS) via Twilio after submitting the form</div></div>
    <div><label for="addon-clickatell-20170403" style="font-weight:bold;"><input type="checkbox" disabled id="addon-clickatell-20170403" name="cfte_addons" value="addon-clickatell-20170403" >Clickatell</label> <div style="font-style:italic;padding-left:20px;">The add-on allows to send notification messages (SMS) via Clickatell after submitting the form</div></div> 
    </fieldset>    

    <fieldset class="cpfieldset"><legend>Integration with third party plugins</legend>
    <div><label for="addon-woocommerce-20150309" style="font-weight:bold;"><input type="checkbox" disabled id="addon-woocommerce-20150309" name="cfte_addons" value="addon-woocommerce-20150309" >WooCommerce</label> <div style="font-style:italic;padding-left:20px;">The add-on allows integrate the forms with WooCommerce products</div></div>	
    </fieldset>
    
    <div style="clear:both"></div>
    
    <div style="margin-top:20px;"><input class="button-primary button" type="button" style="cursor:pointer;color: #FFFFFF;font-weight:bold;" onclick="window.open('https://form2email.dwbooster.com/download?src=activateaddons');" name="activateAddon" value="Activate Addons" /></div>
    <div class="clear"></div>
    * Add-ons are available in <a href="https://form2email.dwbooster.com/download">upgraded versions</a>.
	</div>
</div>
</div>
 

 
   <script type="text/javascript">
   function cp_editArea(id)
   {       
          document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>&edit=1&cal=1&item='+id+'&r='+Math.random();
   }
  </script>
  
  
  <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Customization Area</span></h3>
  <div class="inside"> 
      <p>Use this area to add custom CSS styles or custom scripts. These styles and scripts will be keep safe even after updating the plugin.</p>
      <input class="button" type="button" onclick="cp_editArea('css');" name="gobtn3" value="Add Custom Styles" />
         
      <input class="button" style="margin-left:15px;" type="button" onclick="cp_editArea('js');" name="gobtn2" value="Add Custom JavaScript" />
      <div class="clear">
  </div>    
 </div> 
 
  
</div> 


[<a href="https://wordpress.org/support/plugin/contact-form-to-email#new-post" target="_blank">Support</a>] | [<a href="<?php echo $this->plugin_URL; ?>" target="_blank">Help</a>]
</form>
</div>