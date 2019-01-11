<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');
/* Helper class for uniform elements in admin pages */

add_action('mb-display-logo', array( maxUtils::namespaceit('maxAdminPro'),'logo'));
add_action('mb-display-tabs', array( maxUtils::namespaceit('maxAdminPro'),'tab_menu'));
add_action('mb-display-ads', array( maxUtils::namespaceit('maxAdminPro'), 'display_ads'));
//add_action('mb/header/display_notices', array('maxAdminPro','display_license'));

add_action('mb-display-meta', array( maxUtils::namespaceit('maxAdminPro'), 'display_search'));

add_action('maxbuttons_settings_end', array( maxUtils::namespaceit('maxAdminPro'), 'setting_custom_css'));
add_action('maxbuttons_settings_end', array( maxUtils::namespaceit('maxAdminPro'), 'setting_update_checker'));
add_action('maxbuttons_settings_end', array( maxUtils::namespaceit('maxAdminPro'), 'setting_usecssfile'));
add_action('maxbuttons_settings_end', array( maxUtils::namespaceit('maxAdminPro'), 'setting_colors'));

// Remove MB actions
remove_action('mb-display-logo', array( maxUtils::namespaceit('maxAdmin'),'logo'));
remove_action('mb-display-tabs', array( maxUtils::namespaceit('maxAdmin'),'tab_menu'));
remove_action('mb-display-ads', array( maxUtils::namespaceit('maxAdmin'), 'display_ads'));
remove_action('mb-display-reviewoffer', array( maxUtils::namespaceit('maxAdmin'), 'display_reviewoffer'));

class maxAdminPro extends maxAdmin
{
	static $tabs = null;

	public static function setting_custom_css()
	{

		$custom = get_option('maxbuttons_customcss');

	?>
		 <div class="option-design">
               	<label><?php _e("Custom CSS on button output","maxbuttons-pro"); ?></label>
               	<div class="input"><textarea class='customcss' name="maxbuttons_customcss" rows="5" cols="60"><?php echo esc_textarea($custom);  ?></textarea>
               	<br>
      <?php _e("Target buttons by .maxbutton for each button or .maxbutton-1 for a single (replace number with id)","maxbuttons-pro"); ?>
	       	</div>
	         <div class="clear"></div>

        </div>

	<?php
	}

	public static function setting_update_checker()
	{
		$update_check = get_option('maxbuttons_updatefailhide');
	?>
		<div class='option-design'>
			<p class='note'><?php _e("Only enable this when the plugin is updating but still reporting connection errors.",'maxbuttons-pro'); ?></p>
			<label><?php _e("Hide connection error warnings",'maxbuttons-pro'); ?></label>
			<div class='input'><input type='checkbox' name='maxbuttons_updatefailhide' value='1' <?php checked($update_check, 1); ?> >
			<br>

			</div>
			<div class='clear'></div>
		</div>
	<?php
	}

	public static function setting_usecssfile()
	{
		$use_cssfile = get_option('maxbuttons_usecssfile');
	?>
		<div class='option-design'>
			<p class='note'><?php _e("Enabling this option will enqueue a css file to the page footer. This will help with inline css SEO issues, but might not work well with caching ",'maxbuttons-pro'); ?></p>
			<label><?php _e("Use CSS file instead of inline output in footer",'maxbuttons-pro'); ?></label>
			<div class='input'><input type='checkbox' name='maxbuttons_usecssfile' value='1' <?php checked($use_cssfile, 1); ?> >
			<br>

			</div>
			<div class='clear'></div>
		</div>
		<?php
	}

	public static function setting_colors()
	{
		$color_array = get_option('maxbuttons_colors');

		if (! is_array($color_array) || count($color_array) == 0)
				$color_array = array('#000', '#fff', '#d33', '#d93', '#ee2', '#81d742', '#1e73be', '#8224e3');

		?>
		<div class='option-design'>
			<p class='note'><?php _e("These customizable colors will show in the bottom of the color picker.",'maxbuttons-pro'); ?></p>
			<div class='label'><?php _e("Palette Colors",'maxbuttons-pro'); ?></div>
		<?php
		foreach($color_array as $color):
		?>
		<div class="input mbcolor">
			<input type="text" name="maxbuttons_colors[]" class="color-field" value="<?php echo $color ?>">
		</div>


		<?php
		endforeach;
		?>

		</div> <!-- option design -->
		<?php
	}

	static function logo()
	{
	?>
			<?php _e('Brought to you by', 'maxbuttons-pro') ?>
			<a href="http://maxfoundry.com" target="_blank"><img src="<?php echo MB()->get_plugin_url() ?>images/max-foundry.png" alt="Max Foundry" /></a>
			<?php printf(__('makers of %sMaxGalleria%s and %sMaxInbound%s', 'maxbuttons-pro'), '<a href="https://maxgalleria.com/?ref=mbpro" target="_blank">', '</a>', '<a href="https://maxinbound.com/?ref=mbpro" target="_blank">', '</a>') ?>

	<?php
	}

	static function tab_items_init()
	{
			parent::tab_items_init();
			$tabs = maxAdmin::$tabs;
			unset($tabs["pro"]);

			$packs = array( "packs" => array("name" => __('Packs', 'maxbuttons-pro'),
							 "link" => "page=maxbuttons-packs",
							 "active" => "maxbuttons-packs",
							 ));
			$export = array( "export" => array("name" => __('Export', 'maxbuttons-pro'),
							 "link" => "page=maxbuttons-export",
							 "active" => "maxbuttons-export",
							 ));
			$license =   array("name" => __('License', 'maxbuttons-pro'),
							 "link" => "page=maxbuttons-license",
							 "active" => "maxbuttons-license",
							 );
 			array_splice($tabs, 1, 0, $packs);
			array_splice($tabs, 3, 0, $export);
			array_push($tabs, $license);

			self::$tabs = $tabs;


	}

	static function tab_menu()
	{
		 self::tab_items_init();
	?>
			<h2 class="tabs">
				<span class="spacer"></span>
		<?php foreach (self::$tabs as $tab => $tabdata) {
			if (isset($tabdata["userlevel"]) && ! current_user_can($tabdata["userlevel"]))
				continue;

			$link = admin_url() . "admin.php?" . $tabdata["link"];
			$name = $tabdata["name"];
			$active = '';
			if ($tabdata["active"] == $_GET["page"])
				$active = "nav-tab-active";

				echo "<a class='nav-tab $active' href='$link'>$name</a>";

		}
		echo "</h2>";
	}

	static function display_ads()
	{
		$version = self::getAdVersion();
		$plugin_url = MB()->get_plugin_url();
	 ?>

        <div class="ads image-ad">
        	<a href="http://www.maxbuttons.com/pricing/?utm_source=mbf-dash<?php echo $version ?>&utm_medium=mbp-plugin&utm_content=EBWG-sidebar-22&utm_campaign=inthecart<?php echo $version ?>" target="_blank"><img src="<?php echo $plugin_url ?>/images/ebwg_ad.png" /></a>

        </div>

        <div class="ads image-ad">
            <a href="https://wordpress.org/plugins/maxgalleria/?utm_source=mbp-dash<?php echo $version ?>&utm_medium=mbf_plugin&utm_content=MG_sidebar&utm_campaign=MG_promote" target="_blank">
            <img src="<?php echo $plugin_url ?>/images/mg_ad.png" /></a>
        </div>

        <?php
	}

	static function display_search()
	{
		$search = (isset($_GET["s"])) ? sanitize_text_field($_GET["s"]) : '';
		$page = (isset($_GET["page"])) ? sanitize_text_field($_GET["page"]) : '';
		$view = (isset($_GET["view"])) ? sanitize_text_field($_GET["view"]) : 'all';
		$paged = (isset($_GET["paged"])) ? sanitize_text_field($_GET["paged"]) : '';
		$order = (isset($_GET["order"])) ? sanitize_text_field($_GET["order"]) : 'DESC';
		$orderby = (isset($_GET["orderby"])) ? sanitize_text_field($_GET["orderby"]) : 'id';


		?>
		<form method="GET">
		<p class='search-box'>
			<input type="hidden" name="page" value="<?php echo $page ?>" />
			<input type="hidden" name="view" value="<?php echo $view ?>" />

			<input type="hidden" name="order" value="<?php echo $order ?>" />
			<input type="hidden" name="orderby" value="<?php echo $orderby ?>" />

			<input type="text" name="s" value="<?php echo $search ?>" />
			<input id="search-submit" class="button" type="submit" value="<?php _e("Search Buttons","maxbuttons-pro") ?>">
		</p>
			</form>

		<?php

	}

	public static function export_button($button)
	{
		$button_id = $button->getID();
		if ($button_id > 0)
		{

			?>
			<a id="button-export" class="maxmodal button" data-modal='export-button' href="javascript:void(0)">
				<?php _e("Export","maxbuttons"); ?>
			</a>

			<div class="maxmodal-data" id="export-button" data-load='window.maxFoundry.maxadmin.export_button'
				data-load-args='button_id:<?php echo $button_id ?>'>
				<span class='title'><?php _e("Export button","maxbuttons"); ?></span>
				<span class="content"><p><?php _e("Please wait for the export code", "maxbuttons"); ?></p></span>
					<div class='controls'>
						<input type="button" class="modal_close button-primary" value="<?php _e("Close", "maxbuttons"); ?>">
					</div>
			</div>


			<?php
		}

	}

} // class
