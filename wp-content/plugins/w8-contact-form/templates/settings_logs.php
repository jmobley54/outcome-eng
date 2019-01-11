	<div id="screen_preloader" style="position: absolute;width: 100%;height: 1000px;z-index: 9999;text-align: center;background: #fff;padding-top: 200px;"><h3>W8 Contact Form</h3><img src="<?php print(plugins_url( '/assets/img/screen_preloader.gif' , __FILE__ ));?>"><h5><?php _e( 'LOADING', W8CONTACT_FORM_TEXT_DOMAIN );?><br><br><?php _e( 'Please wait...', W8CONTACT_FORM_TEXT_DOMAIN );?></h5></div>
<div class="wrap w8contact_form" style="visibility:hidden">
	<br />
	<h3><?php _e( 'Logs', W8CONTACT_FORM_TEXT_DOMAIN );?><hr /></h3>
	<form method="post" action="options.php#contact_form_slider_logs"> 
		<?php @settings_fields('contact_form_slider_logs-group'); ?>
		<?php @do_settings_fields('contact_form_slider_logs-group'); ?>
		<?php do_settings_sections('contact_form_slider_logs'); ?>
		<?php @submit_button(); ?>
	</form>
	<div class="log-buttons">
		<input class="button" id="log-clear" type="button" value="<?php _e( 'CLEAR LOGS', W8CONTACT_FORM_TEXT_DOMAIN );?>">
		<input class="button" id="log-display" type="button" value="<?php _e( 'DISPLAY LOGS', W8CONTACT_FORM_TEXT_DOMAIN );?>">
	</div>
	<div id="cfs-log-entries"></div>
</div>
<div id="dialog-confirm" title="Delete Log Entries?">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php _e( 'The logs will be permanently deleted and cannot be recovered. Are you sure?', W8CONTACT_FORM_TEXT_DOMAIN );?></p>
</div>