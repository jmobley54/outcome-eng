	<div id="screen_preloader" style="position: absolute;width: 100%;height: 1000px;z-index: 9999;text-align: center;background: #fff;padding-top: 200px;"><h3>W8 Contact Form</h3><img src="<?php print(plugins_url( '/assets/img/screen_preloader.gif' , __FILE__ ));?>"><h5><?php _e( 'LOADING', W8CONTACT_FORM_TEXT_DOMAIN );?><br><br><?php _e( 'Please wait...', W8CONTACT_FORM_TEXT_DOMAIN );?></h5></div>
<div class="wrap w8contact_form" style="visibility:hidden">
	<br />
	<h3><?php _e( 'Update', W8CONTACT_FORM_TEXT_DOMAIN );?><hr /></h3>
	<?php 
		require_once(str_replace('templates','',sprintf("%s/modules/manual.update.php", dirname(__FILE__))));
		manual_plugin_updater::getInstance(
		'w8-contact-form/w8-contact-form.php',
		'w8-contact-form/w8-contact-form.php',
		array(),
		'contact_form_slider'
		);
	?>
</div>