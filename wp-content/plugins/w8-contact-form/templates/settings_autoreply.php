	<div id="screen_preloader" style="position: absolute;width: 100%;height: 1000px;z-index: 9999;text-align: center;background: #fff;padding-top: 200px;"><h3>W8 Contact Form</h3><img src="<?php print(plugins_url( '/assets/img/screen_preloader.gif' , __FILE__ ));?>"><h5><?php _e( 'LOADING', W8CONTACT_FORM_TEXT_DOMAIN );?><br><br><?php _e( 'Please wait...', W8CONTACT_FORM_TEXT_DOMAIN );?></h5></div>
<div class="wrap w8contact_form" style="visibility:hidden">
	<br />
	<h3><?php _e( 'Auto-Reply', W8CONTACT_FORM_TEXT_DOMAIN );?></h3>
	<div class="help_link"><a target="_blank" href="http://contactform.pantherius.com/documentation"><?php _e( 'Documentation', W8CONTACT_FORM_TEXT_DOMAIN );?></a></div>
	<hr /><br>
	<form method="post" action="options.php#contact_form_slider_autoreply"> 
		<?php @settings_fields('contact_form_slider_autoreply-group'); ?>
		<?php @do_settings_fields('contact_form_slider_autoreply-group'); ?>
		<?php do_settings_sections('contact_form_slider_autoreply'); ?>
		<?php @submit_button(); ?>
	</form>
</div>