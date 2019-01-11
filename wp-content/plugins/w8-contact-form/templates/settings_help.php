	<div id="screen_preloader" style="position: absolute;width: 100%;height: 1000px;z-index: 9999;text-align: center;background: #fff;padding-top: 200px;"><h3>W8 Contact Form</h3><img src="<?php print(plugins_url( '/assets/img/screen_preloader.gif' , __FILE__ ));?>"><h5><?php _e( 'LOADING', W8CONTACT_FORM_TEXT_DOMAIN );?><br><br><?php _e( 'Please wait...', W8CONTACT_FORM_TEXT_DOMAIN );?></h5></div>
<div class="wrap w8contact_form" style="visibility:hidden">
	<br />
	<h3><?php _e( 'Help', W8CONTACT_FORM_TEXT_DOMAIN );?></h3>
	<hr />
	<p>
		<?php _e( 'To see the full documentation, please click on the following link:', W8CONTACT_FORM_TEXT_DOMAIN );?> <a target="_blank" href="http://contactform.pantherius.com/documentation"><?php _e( 'Documentation', W8CONTACT_FORM_TEXT_DOMAIN );?></a>
	</p>
	<hr />
	<p>    
	<?php print(file_get_contents("http://static.pantherius.com/plugin_directory.html")); ?>
	</p>
</div>