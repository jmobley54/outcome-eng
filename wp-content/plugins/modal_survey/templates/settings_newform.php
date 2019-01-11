<div id="screen_preloader" style="position: absolute;width: 100%;height: 1000px;z-index: 9999;text-align: center;background: #fff;padding-top: 200px;"><h3>Modal Survey for WordPress</h3><img src="<?php print(plugins_url( '/assets/img/screen_preloader.gif' , __FILE__ ));?>"><h5><?php _e( 'LOADING', MODAL_SURVEY_TEXT_DOMAIN );?><br><br><?php _e( 'Please wait...', MODAL_SURVEY_TEXT_DOMAIN );?></h5></div>
<div class="wrap pantherius-jquery-ui wrap-padding" style="visibility:hidden">
<br />
<div class="title-border">
	<h3><?php _e( 'Create New Survey', MODAL_SURVEY_TEXT_DOMAIN );?></h3>
	<div class="help_link"><a target="_blank" href="http://modalsurvey.pantherius.com/documentation/#line2"><?php _e( 'Documentation', MODAL_SURVEY_TEXT_DOMAIN );?></a></div>
</div>
	<div id="modal_survey_settings">
		<input type="text" id="survey_name" value="" size="50" placeholder="<?php _e( 'Type the survey name here', MODAL_SURVEY_TEXT_DOMAIN );?>" /><span id="button-container"><a id="add_new_survey" class="button button-secondary button-small"><?php _e( 'New Survey', MODAL_SURVEY_TEXT_DOMAIN );?></a></span><span id="error_log"></span>
	</div>
</div>