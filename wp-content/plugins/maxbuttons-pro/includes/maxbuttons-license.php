<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

$license = MB()->getClass('license');

// for license interface, always reload - refresh
$license->checkRemoteandReload();

$license_is_valid = $license->is_valid();
$license_is_activated = $license->is_activated();
$license_is_expired = $license->is_expired();


$admin = MB()->getClass('admin');
$page_title = __("License","maxbuttons-pro");

$admin->get_header(array("title" => $page_title) );

$button_label = __('Activate License', 'maxbuttons-pro');
$form_action = ($license_is_activated && ! $license_is_expired) ? 'deactivate_license' : 'activate_license';
?>

<div class='option-container'>
	<div class='title'>
		<?php _e('Your License','maxbuttons-pro') ?> :
		<?php if ($license_is_valid && ! $license_is_expired)
						_e( sprintf('%s Active %s', "<span class='active'>", "</span>"), 'maxbuttons-pro');
					else
						_e( sprintf('%s Not Active %s', "<span class='not-active'>", "</span>"), 'maxbuttons-pro');
		?>
  </div>
	<div class='inside'>
				<form id="license_form" class='license_form mb-ajax-form'>
				<div class='option-design option-status'>

						<div id="activation_progress" style="display: none;">
							<div class='content'>
							<img src="<?php echo MB()->get_plugin_url(true) ?>images/loading.gif" />
							<h4><?php _e('Your license key is being activated, please wait...', 'maxbuttons-pro') ?></h4>
							</div>
						</div>

						<div id="activation_success" class="alert alert-success" style="display: none;">
							<h4><?php _e('Your license key has been activated. Enjoy using MaxButtons Pro.', 'maxbuttons-pro') ?></h4>
						</div>

						<div id="ajax_error" class="alert alert-error" style="display: none;">
							<p><?php printf(__('There was an error activating your license key. Please try again or %scontact us%s so we can investigate the issue.', 'maxbuttons-pro'), '<a href="https://maxbuttons.com/contact/" target="_blank">', '</a>') ?></p>
							<h3>&nbsp;</h3>
						</div>

						<?php if($license_is_activated && ! $license_is_expired):
  							$button_label = __('Deactivate License', 'maxbuttons-pro');
							 ?>
							<h4><?php _e('Your license is active. Enjoy using MaxButtons Pro.', 'maxbuttons-pro') ?></h4>

							<p class='license-field'><span><?php _e('License key', 'maxbuttons-pro'); ?></span> <?php echo $license->get_key(); ?>
								<input type="hidden" name="license_key" value="<?php echo $license->get_key() ?>" />
							</p>

							<p class='license-field <?php if ($license_is_expired) echo 'expired' ?>'><span><?php _e('License expires', 'maxbuttons-pro') ?></span> <?php echo $license->get_expiration(); ?> </p>

						<p id='activate_license_field' class='license-field' ><span>&nbsp;</span>
							<input type="submit" id="deactivate_button" name="deactivate_button" class="button-primary mb-ajax-submit" value="<?php echo $button_label ?>"  data-action="<?php echo $form_action ?>" />

					</p>

						<?php else: ?>

				    <?php

	           ?>
						 <p><?php _e('Active your license to use the plugin', 'maxbuttons-pro'); ?> </p>

						<p class='license-field'><span><?php _e("Your License key", "maxbuttons-pro"); ?></span><input type="text" id="license_key" name="license_key" value="<?php echo $license->get_key(); ?>" class="license-key"  /></p>

						<?php if ($license_is_expired): ?>
						<p class='license-field <?php if ($license_is_expired) echo 'expired' ?>'><span><?php _e('License expires', 'maxbuttons-pro') ?></span> <?php echo $license->get_expiration(); ?>

						<?php $url = $license->get_renewal_url();

						?>
						<a class='button-primary renew-license' href='<?php echo $url ?>' target="_blank"><?php _e("Renew License", 'maxbuttons-pro'); ?></a>
						</p>

					<?php endif; ?>

						<p id='activate_license_field' class='license-field' ><span>&nbsp;</span>
							<input type="submit" id="activate_button" name="activate_button" class="button-primary mb-ajax-submit" value="<?php echo $button_label ?>"  data-action="<?php echo $form_action ?>" /> </p>


						<p class="license-note"><?php printf(__('Your license key can be found in your %sMaxButtons account%s.', 'maxbuttons-pro'), '<a href="https://www.maxbuttons.com/my-account/" target="_blank">', '</a>') ?></p>

					<?php endif; ?>
			</div> <!-- option -->
				</form>




			</div> <!-- inside -->
</div> <!-- option-container -->


</div> <!-- main -->
<div class="offers ad-wrap">
	<?php do_action("mb-display-ads"); ?>
</div>

<?php $admin->get_footer(); ?>
