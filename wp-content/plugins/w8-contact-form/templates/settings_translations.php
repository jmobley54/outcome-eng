	<div id="screen_preloader" style="position: absolute;width: 100%;height: 1000px;z-index: 9999;text-align: center;background: #fff;padding-top: 200px;"><h3>W8 Contact Form</h3><img src="<?php print(plugins_url( '/assets/img/screen_preloader.gif' , __FILE__ ));?>"><h5><?php _e( 'LOADING', W8CONTACT_FORM_TEXT_DOMAIN );?><br><br><?php _e( 'Please wait...', W8CONTACT_FORM_TEXT_DOMAIN );?></h5></div>
<div class="wrap w8contact_form" style="visibility:hidden">
	<br />
	<h3><?php _e( 'Translations', W8CONTACT_FORM_TEXT_DOMAIN );?><hr /></h3>
	<form method="post" action="options.php#contact_form_slider_translations"> 
		<?php settings_fields('contact_form_slider_translations-group'); ?>
		<?php do_settings_fields('contact_form_slider_translations-group','contact_form_slider_translations-section'); ?>
		<?php do_settings_sections('contact_form_slider_translations'); ?>
		<?php submit_button(); ?>
	</form>
</div>