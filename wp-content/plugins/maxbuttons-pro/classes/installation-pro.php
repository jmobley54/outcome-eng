<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

class maxInstallPro extends maxInstall
{
 	static function convertOldFields($row)
 	{

 		$fields = parent::convertOldFields($row);

 		return $fields;
 	}

 	static function create_database_table()
 	{
		parent::create_database_table();
 	}

 	static function activation_hook($network_wide)
 	{
 		parent::activation_hook($network_wide);
 	}

 	static function deactivation_hook($network_wide)
 	{
 		parent::deactivation_hook($network_wide);
 	}

	static function activate_plugin($gocreate = true)
	{

		parent::activate_plugin(false);
		self::create_folders();

		$old_packs_folder = MAXBUTTONS_PRO_PLUGIN_DIR . '/packs';
		$new_packs_folder = MAXBUTTONS_PRO_PACKS_DIR;
		self::copy_existing_packs_to_uploads($old_packs_folder, $new_packs_folder);

    delete_site_transient('update_plugins'); // nuke the update cache to prevent hanging update messages 
    delete_transient('mbpro_update_cache');

		maxButtonsProAdmin::updateUsedFonts();
	}

	static function clear()
	{
		parent::clear();
		delete_option('maxbuttons_pro_license_lastcheck');
		delete_option('maxbuttons_pro_user_level');
	}


	static function deactivate_plugin()
	{
		maxInstall::deactivate_plugin();
	}

	static function create_folders()
	{
		$uploads = wp_upload_dir();

		$maxbuttons_pro_dir = $uploads['basedir'] . '/maxbuttons-pro';
		$packs_dir = $maxbuttons_pro_dir . '/packs';
		$exports_dir = $maxbuttons_pro_dir . '/exports';

		// Check to create the maxbuttons-pro folder in the uploads directory
		if (!file_exists($maxbuttons_pro_dir) and !is_dir($maxbuttons_pro_dir)) {
			$result = mkdir($maxbuttons_pro_dir);
			if (! $result)
			{
				trigger_error("Please check your permissions of the wp-uploads directory", E_USER_WARNING);
				MB()->add_notice("error", __("Permission denied while creating directory, please check your wp-uploads directory permissions","maxbuttons-pro"));
				return;
			}
		}

		static::write_empty_file($maxbuttons_pro_dir);

		// Check to create the packs folder in the maxbuttons-pro uploads folder
		if (!file_exists($packs_dir) and !is_dir($packs_dir)) {
			mkdir($packs_dir);
		}

		// Check to create the exports folder in the maxbuttons-pro uploads folder
		if (!file_exists($exports_dir) and !is_dir($exports_dir)) {
			mkdir($exports_dir);
		}

		static::write_empty_file($maxbuttons_pro_dir);
		static::write_empty_file($packs_dir);
		static::write_empty_file($exports_dir);


	}

	static function write_empty_file($directory)
	{
		$index_file = trailingslashit($directory) . 'index.php';
		if (! file_exists($index_file) )
		{
			$f = fopen($index_file,'w');
			fwrite($f, '');
			fclose($f);

		}

	}

	static function copy_existing_packs_to_uploads($old_packs_folder, $new_packs_folder) {
	// Only continue if the old packs folder exists
		if (file_exists($old_packs_folder) and is_dir($old_packs_folder)) {
			$dir = opendir($old_packs_folder);
			@mkdir($new_packs_folder);

			while (($file = readdir($dir)) !== false) {
				if (($file != '.') && ($file != '..')) {
					if (is_dir($old_packs_folder . '/' . $file)) {
						// Recursive call
						mbpro_copy_existing_packs_to_uploads($old_packs_folder . '/' . $file, $new_packs_folder . '/' . $file);
					}
					else {
						copy($old_packs_folder . '/' . $file, $new_packs_folder . '/' . $file);
					}
				}
			}

			closedir($dir);
		}
	}


	static function button_pro_blockpath($paths)
	{

		$paths[] = plugin_dir_path( MAXBUTTONS_PRO_ROOT_FILE ) . "blocks/";

		return $paths;
	}

	static function collection_pro_path($paths)
	{
		$paths[] =  plugin_dir_path( MAXBUTTONS_PRO_ROOT_FILE ) . "collections/";
		return $paths;
	}


} // Class
