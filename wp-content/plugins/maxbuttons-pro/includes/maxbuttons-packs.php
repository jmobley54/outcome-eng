<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

if (! extension_loaded('simplexml') )
{
	$action = '';
	include(MB()->get_plugin_path() . '/includes/maxbuttons-no-simplexml.php');
	return;
}

// Start with an empty message
$message = '';
$maxPack = new maxProPack();

if ($_POST) {
	$message = $maxPack->import_local_pack($_FILES);
}
?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		<?php if ($_POST) { ?>
			jQuery("#maxbuttons .import .message").show();
		<?php } ?>


	});
</script>
<?php
$admin = MB()->getClass('admin');
$page_title = __("Packs","maxbuttons-pro");

$admin->get_header(array("title" => $page_title) );
?>
			<?php do_action('mb/packs/display_notices'); ?>

			<div class="import import-dragdrop" >
				<?php if ($message != '') { ?>
					<div class="mb-message"><?php echo $message ?></div>
				<?php } ?>
					<h3><?php _e('Import', 'maxbuttons-pro') ?></h3>
					<p><?php _e('Click below to select a file', 'maxbuttons-pro') ?></p>

					<form id="import-form" method="post" enctype="multipart/form-data">
						<input type="file" name="pack_zip" value="<?php _e("Select file","maxbuttons-pro"); ?>" />
						<input type="hidden" name="dummy" />
						<button type="submit" id="import-button" class="button-primary" href='javascript:void(0);'><?php _e('Import', 'maxbuttons-pro') ?></button>
					</form>
			</div> <!-- import -->


			<h2 class='tabs' id='packs_tab'>
			<a class='nav-tab nav-tab-active' data-screen='available_packs_screen' href='javascript:void(0);'><?php _e('Available Button Packs', 'maxbuttons-pro') ?></a>
			<!--
			<a class='nav-tab' data-screen='free_packs_screen' href='javascript:void(0);'><?php _e('Free Button Packs', 'maxbuttons-pro'); ?></a>
			-->
			</h2>
			<div class='spacer'></div>
			<div id="available_packs_screen">

			<?php

			$packs = $maxPack->get_local_packs();
			foreach($packs as $pack_name => $pack_files)
			{
				$maxPack->load_pack($pack_files);
				echo $maxPack->display_pack();


			}
			if (count($packs) == 0)
			{
				echo "<p>";
				_e("No Packs available. Import a button pack.","maxbuttons-pro");
				echo "</p>";
			}

		?>

				<div class="maxmodal-data" id="delete-pack" data-load='window.maxFoundry.maxadmin.deletePack'>
					<span class='title'><?php _e("Removing Button Pack","maxbuttons"); ?></span>
					<span class="content"><p><?php _e("You are about to permanently remove this button pack. Are you sure?", "maxbuttons"); ?></p></span>
						<div class='controls'>
							<a href="#" class="button-primary big yes"><?php _e("Yes","maxbuttons"); ?></a>
							&nbsp;&nbsp;
							<a class="modal_close button-primary no"><?php _e("No", "maxbuttons"); ?></a>

						</div>
				</div>

			</div> <!-- available packs -->

			<div id="free_packs_screen" style="display:none">
				<div class="free_packs">
					<span id="free-pack-nonce" style="display:none"><?php echo wp_create_nonce( "maxbuttons-free-pack" ); ?></span>
					<div class="pack_container"></div>
					<div class='loading'><?php _e("Loading free packs","maxbuttons-pro"); ?></div>
				</div>

				<div class="free_preview" style="display:none">
				  <a class="button-primary close" href='javascript:void(0);'><?php _e("Close","maxbuttons-pro"); ?></a>
				  <?php
				  	//global $maxbutton_pro;
					$licenseClass = $this->getClass('license');
					$license = $licenseClass->check_license();
				  	$able = ($license) ? "" : 'disabled';
				  ?>
				  <a class="button-primary use <?php echo $able ?> " href='javascript:void(0);'><?php _e("Download this pack", "maxbuttons-pro"); ?> </a>
		 			<div class="results"></div>

					<div class="clear"></div>
				</div>

			</div> <!-- free packs overview -->

		</div>

		<div class="offers ad-wrap">
			<?php do_action("mb-display-ads"); ?>
		</div>

<?php $admin->get_footer(); ?>
