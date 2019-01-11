<?php
namespace MaxButtons;

$button = MB()->getClass('button');

$mbadmin = MaxButtonsAdmin::getInstance();
$packs = MB()->getClass("pack");
$packs->checkZipModule();

if (isset($_GET["action"]) && $_GET["action"] == "download")
{
	if(! isset($_POST["pack_name"]) || $_POST["pack_name"] == '')
	{
	 wp_redirect(admin_url() ."admin.php?page=maxbuttons-export&action=list&error=noname");

	}
	else
	{
		$buttons = is_array($_POST["maxbutton"]) ? array_filter($_POST['maxbutton']) : array();

		$args = array(
			"pack_name" => sanitize_text_field($_POST['pack_name']),
			"pack_description" => sanitize_text_field($_POST['pack_description']),
			"pack_author" => sanitize_text_field($_POST['pack_author']),
			"pack_author_url" => sanitize_text_field($_POST['pack_author_url']),
			"buttons" => $buttons,
			);

		$result = $packs->export_pack($args);

		if ($result === false)
		{
			MB()->add_notice('error', __('Export failed','maxbuttons-pro'));
			do_action('mb/packs/display_notices');
			echo " <a href='/wp-admin/admin.php?page=maxbuttons-export&action=list'>Go back</a>";
			exit();
		}
		else
			exit();
	}
	//exit();

}

// check permissions on structure;
//is_writable
if (! is_writeable(MAXBUTTONS_PRO_EXPORTS_DIR))
{
			$_GET["error"] = "exportnowrite";
}


if (isset($_GET["error"]) && $_GET["error"] != '')
{
	$error = sanitize_text_field($_GET["error"]);
	switch($error)
	{
		case "noname":
			$error = __("Pack name is required for export","maxbuttons-pro");
		break;
		case "exportnowrite":
			$error = __("Export directory not writable! Check your permissions","maxbuttons-pro");
		break;
		default:
			$error = __("Unkown export error occured", "maxbuttons-pro");
		break;
	}
}

 $args = array("limit" => -1);

$published_buttons = $mbadmin->getButtons($args);
$pagination = $mbadmin->getButtonPages();
?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#export-buttons").click(function() {
			jQuery("#export-form").submit();
			return false;
		});

		jQuery("#select-all").click(function() {
			 jQuery("input[type='checkbox']").attr("checked", jQuery("#select-all").is(":checked"));
		});
	});
</script>

<?php
$admin = MB()->getClass('admin');
$page_title = __("Export","maxbuttons-pro");

$admin->get_header(array("title" => $page_title) );
?>

		<?php if (isset($error))
		{
			echo "<div class='mb-message error'>$error</div>";
		}
		?>
		<?php
			do_action('mb/packs/display_notices');
		?>
			<div class="form-actions">
				<a id="export-buttons" href='javascript:void(0);' class="button-primary"><?php _e('Export Buttons', 'maxbuttons-pro') ?></a>
			</div>

			<p><?php _e('Exporting your buttons creates a button pack zip file. Fill out the short form below, then select which buttons to export.', 'maxbuttons-pro') ?></p>

			<div class="spacer"></div>


	<form id="export-form" class="export_table" method="post" action="<?php echo admin_url('admin.php?page=maxbuttons-export&action=download&noheader=true'); ?>">
				<div class="option">
					<div class="label"><?php _e('Pack Name', 'maxbuttons-pro') ?></div>
					<div class="input">
						<input type="text" id="pack_name" name="pack_name" />
					</div>
				</div>

				<div class="option">
					<div class="label"><?php _e('Pack<br />Description', 'maxbuttons-pro') ?></div>
					<div class="input">
						<textarea id="pack_description" name="pack_description"></textarea>
					</div>
				</div>

				<div class="option">
					<div class="label"><?php _e('Pack Author', 'maxbuttons-pro') ?></div>
					<div class="input">
						<input type="text" id="pack_author" name="pack_author" />
					</div>
				</div>

				<div class="option">
					<div class="label"><?php _e('Pack Author URL', 'maxbuttons-pro') ?></div>
					<div class="input">
						<input type="text" id="pack_author_url" name="pack_author_url" />
					</div>
				</div>

				<div class="button-list">
					<div class='export-list'>
						<div class='heading'>
							<span class="content"><?php _e('Button', 'maxbuttons-pro') ?></span>
							<span class="name"><?php _e('Name and Description', 'maxbuttons-pro') ?></span>
							<span class="action"><?php _e('Shortcode', 'maxbuttons-pro') ?></span>
						</div>
						<?php
						$i = 0;
						foreach ($published_buttons as $b) {
								$id = $b["id"];
								$button->set($id);

						 ?>
							<input type="checkbox" id="maxbutton_<?php echo $id ?>" name="maxbutton[]" value="<?php echo $id ?>" />

							<label class="export-button" for="maxbutton_<?php echo $id ?>">

									<div class="content">
										<?php //echo do_shortcode('[maxbutton id="' . $id . '" ]')
										$button->display( array("mode" => 'preview') );
										?>
									</div>
									<div class="name">
										<?php echo $button->getName() ?>
										<br />
										<p><?php echo $button->getDescription() ?></p>
									</div>
									<div class="action">
										[maxbutton id="<?php echo $id ?>"] <br />
										 [maxbutton name="<?php echo $button->getName(); ?>"]
										<span class='selected dashicons dashicons-yes'></span>
									</div>

							</label>

						<?php

						} ?>
					</div>
				</div>
			</form>
		</div>

		<div class="offers ad-wrap">
			<?php do_action('mb-display-ads'); ?>
		</div>
<?php $admin->get_footer(); ?>
