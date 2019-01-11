<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

$maxPack = new maxProPack();

if ($_POST && isset($_POST["usebutton"]))
{
	$data = unserialize(stripslashes($_POST["buttondata"])); 
	$button = new maxButton(); 
 
 	if (isset($data['icon']['icon_id'])) 
 		$data['icon']['icon_id'] = 0; 
 
	$data["id"] = 0;
	$button->setupData($data);
	$data = $button->get();
	$data["name"] = $button->getName(); 
	$data["status"] = $button->getStatus();
 
	$button_id = $button->update($data);
	//wp_redirect(admin_url('admin.php?page=maxbuttons-controller&action=button&id=' . $button_id));
}	

$admin = MB()->getClass('admin'); 
$page_title = __("Packs","maxbuttons-pro"); 
 
$admin->get_header(array("title" => $page_title) );
	
		$pack = sanitize_text_field($_GET['id']);
		$parsed = $maxPack->parse_local_pack($pack);
		$maxPack->load_pack($parsed); 
	
$pack_array = $maxPack->get_pack_buttons();			
?>

<ul class='pack-crumbs'> 
	<li><a href="<?php echo admin_url('admin.php?page=maxbuttons-packs'); ?>"><?php _e("Packs","maxbuttons-pro"); ?></a></li>
	<li><?php echo $maxPack->getName() ?> </li>
</ul>

	<?php echo $maxPack->display_pack(array("packclass" => "pack-meta")); ?> 

	<?php if ( $pack_array['is_local'] ): ?> 
		<div class='pack-list-header'> 
			<h3><?php _e(sprintf('%s Buttons Selected', "<span class='count'>0</span>"), 'maxbuttons-pro'); ?></h3>
			<p><?php _e('Click on a button to select it for importing', 'maxbuttons-pro'); ?></p>
			
			<div class='import_button'>
					<button type='button' class='button button-primary button-big' name='import_button'>
						<?php _e('Import', 'maxbuttons-pro'); ?>
					</button>
			</div>
		</div>	
	<?php endif; ?>
		
	<div class='pack-list'>
			<?php
				$i = 0; 
				foreach($pack_array['buttons'] as $id => $button):  ?>
				<?php if ($i == 0 || ($i % 3) == 0)
					echo "<div class='row'>"; 
				?>
				<?php if ($pack_array['is_local']) : ?> 
					<input type='checkbox' name='button_import' value="<?php echo $id ?>" id='button-<?php echo $id ?>' />
				<?php endif; ?>
				<label class='pack-button' for='button-<?php echo $id ?>'> 
					
					<div class='content'><div class='shortcode-container'> 
						<?php echo $button['button']; ?> 
					</div>
					<div class='name'><?php echo $button['name']; ?>
									  <br /> 
									  <?php echo $button['desc']; ?>
					</div>					
					</div>
					<div style='display:none;' data-button='<?php echo $id ?>' ><?php echo $button['data']; ?></div>
				
				</label>
				<?php  if ( (($i+1) % 3) == 0) 
					echo "</div>"; 
				?>					
			<?php
			   $i++;
			 endforeach; // pack_array ?> 
<?php
			if (! ($i % 3) == 0)
				echo "</div>"; // div will not be closed with less then I % 3 ending
?>

			<input type="hidden" class='maxmodal' name="import_modal" value="blah" data-modal='import-done' >

		</div> <!-- heading --> 
	</div> <!-- packlist -->
			

	<!-- import done modal -->
	<div class="maxmodal-data" id="import-done">
		<span class='title'><?php _e("Buttons Imported","maxbuttons-pro"); ?></span>
		<span class="content"><p><?php _e("Your buttons have been imported", "maxbuttons-pro"); ?></p></span>
			<div class='controls'>
				<a class='modal_close button'><?php _e('Close','maxbuttons-pro'); ?></a>
				<a class='modal_close button' href="<?php echo admin_url('admin.php?page=maxbuttons-packs'); ?>">
					<?php _e('To packs', 'maxbuttons-pro'); ?></a>
				<a class='modal_close button-primary' href='<?php echo admin_url('admin.php?page=maxbuttons-controller') ?>'>
					<?php _e('To buttons','maxbuttons-pro'); ?></a>		
			</div>
	</div>
			
				<div class="maxmodal-data" id="delete-pack" data-load='window.maxFoundry.maxadmin.deletePack'>
					<span class='title'><?php _e("Removing Button Pack","maxbuttons"); ?></span>
					<span class="content"><p><?php _e("You are about to permanently remove this button pack. Are you sure?", "maxbuttons"); ?></p></span>
						<div class='controls'>
							<a href="#" class="button-primary big yes"><?php _e("Yes","maxbuttons"); ?></a>
							&nbsp;&nbsp;
							<a class="modal_close button-primary no"><?php _e("No", "maxbuttons"); ?></a>
				
						</div>
				</div>	
										
	<div class="ad-wrap">
		<?php do_action("mb-display-ads"); ?> 
	</div>
		
<?php $admin->get_footer(); ?> 
