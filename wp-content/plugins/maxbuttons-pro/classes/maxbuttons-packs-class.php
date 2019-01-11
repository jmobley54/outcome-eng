<?php
namespace MaxButtons;
//  Class for handling everything pack related.
defined('ABSPATH') or die('No direct access permitted');

use \RecursiveIteratorIterator as RecursiveIteratorIterator;
use \RecursiveDirectoryIterator as RecursiveDirectoryIterator;

class maxProPack extends maxPack
{
	function __construct()
	{
		if (! is_dir(MAXBUTTONS_PRO_PACKS_DIR))
				maxInstallPro::create_folders();

		$this->default_img_url = MAXBUTTONS_PRO_PLUGIN_URL . '/images/default-pack.png';

	}

	/* Standard WP init of the filesystem */
	private function init_filesystem()
	{
		// This filter forces WP to use the 'direct' method when working
		// with the filesystem, instead of possible alternatives like FTP.
		// See this post http://wpquestions.com/question/show/id/2685.
		add_filter('filesystem_method', array($this,'initfs_filter'));


		// Now instantiate WP_Filesystem and then remove the filter since
		// we only want to enforce it for the import functionality.
		WP_Filesystem();
		remove_filter('filesystem_method', array($this,'initfs_filter'));
	}

	/* Require direct acccess */
	public function initfs_filter()
	{
			return 'direct';

	}

	public function checkZipModule()
	{
		if( class_exists('ZipArchive'))
		{
			return true;
		}
		MB()->add_notice('error', __('PHP Zip Module is not loaded, but is required for export functionality!','maxbuttons-pro'));
		return false;
	}

	/* Import function for uploaded ZIP files */
	function import_local_pack($files)
	{
		$file_name = $files['pack_zip']['name'];

		if (!empty($file_name)) {
				$this->init_filesystem();

				// Get the type of uploaded file
				$arr_file_type = wp_check_filetype(basename($file_name));
				$uploaded_file_type = $arr_file_type['type'];

				// Check allowed type
				$allowed_file_types = array('application/zip');
				if (in_array($uploaded_file_type, $allowed_file_types)) {
					// Set the overrides and do the upload
					$overrides = array('test_form' => false);
					$uploaded_file = wp_handle_upload($files['pack_zip'], $overrides);

					if (isset($uploaded_file['file'])) {
						// Unzip the file to the packs folder
						$unzip_result = unzip_file($uploaded_file['file'], MAXBUTTONS_PRO_PACKS_DIR);


						if ($unzip_result === true) {
							// Success
							$message = sprintf(__('The %s%s%s button pack file was imported successfully.', 'maxbuttons-pro'), '<strong>', $file_name, '</strong>');
						}
						else {
							// Failure

							$message = sprintf(__('The %s%s%s button pack file could not be unzipped.', 'maxbuttons-pro'), '<strong>', $file_name, '</strong>');
						}
					}
					else {
						// Something went wrong, file wasn't saved
						$message = sprintf(__('The %s%s%s button pack file could not be saved to the filesystem.', 'maxbuttons-pro'), '<strong>', $file_name, '</strong>');
					}
				}
				else {
					// Wrong file type
					$message = sprintf(__('Only %sZIP%s files are supported for importing button packs.', 'maxbuttons-pro'), '<strong>', '</strong>');
				}
			}
			else {
				// No file given
				$message = __('No file was selected.', 'maxbuttons-pro');
			}
		return $message;
	}

	/* Find the library of packs on this computer */
	function get_local_packs()
	{
		if (! is_dir(MAXBUTTONS_PRO_PACKS_DIR))
			return array(); // directory failed. No action since  error will be invoked on constructor already

		$known_packs = scandir(MAXBUTTONS_PRO_PACKS_DIR);
		$packs = array();
		foreach ($known_packs as $pack) {

			$parsed = $this->parse_local_pack($pack);
			if($parsed)
				$packs[$pack] =  $parsed;
		}
		return $packs;
	}



	/* Open, check and parse a pack on this computer. This function loads the metadata of a pack */
	public function parse_local_pack($pack)
	{

		if ($pack == '.' || $pack == '..' || ! is_dir(MAXBUTTONS_PRO_PACKS_DIR . '/' . $pack) )
		{	return false;  } // not a pack

		$pack_dir = MAXBUTTONS_PRO_PACKS_DIR . "/" . $pack . "/";
		$this->setPackPath(MAXBUTTONS_PRO_PACKS_URL . '/' . $pack . "/");

		$pack_file = $pack_dir . $pack . '.xml';
		$pack_file = $pack_dir . $pack . '.xml';
		$pack_img_file = $pack_dir . '/pack.png';
		$pack_img_url = MAXBUTTONS_PRO_PACKS_URL . '/' . $pack . '/pack.png';

		if (! file_exists($pack_file))
		{ return false; } // no pack definitions

		if (! file_exists($pack_img_file)) {
			$pack_img_url = $this->default_img_url;
		}
		return array("file" => $pack_file,
							  "dir" => $pack,
							  "img" => $pack_img_url,
							  "is_local" => true
							);
	}




	/* Opens a remote pack via URL wrapper function in xml load string */
	function parseload_remote_pack($xmlstring, $remote_path)
	{
		$this->setPackPath($remote_path);


		$xml = simplexml_load_string($xmlstring, null,  LIBXML_NOCDATA);

		$this->pack_xml = $xml;

		$pack = $xml->pack[0];
		$packset = current($pack->attributes());

		$packset["image"] = $this->default_img_url;
		$packset["is_local"] = false;

		$this->set_pack($packset);

	}

	/* Display pack data like name and creator */
	public function display_pack($args = array())
	{
		$defaults = array("packclass" => "pack");
		$args = wp_parse_args($args, $defaults);

		$license = (isset($args["license"])) ? $args["license"] : false;

		if ($this->is_local)
		{
			$packadminlink = '<a href="' . admin_url() . 'admin.php?page=maxbuttons-packs&action=pack&id=' . $this->pack_dir . '">';
		}
		else
		{
			$packadminlink = "<a class='load_pack_preview' href='javascript:void(0);' data-pack='" . $this->pack_url . "'
							   data-packname='" . $this->pack_dir . "'>";
		}
		$output = "<div class='" . $args["packclass"] . "'>";
		$output .= $packadminlink;
		$output .= "<img class='image' src='" . $this->img . "'>";
		$output .= "</a>";

		$output .= "<h3>" . $packadminlink . $this->name . "</a></h3>";
		if ($this->author != '') {
				$author = sprintf(__('%sBy: ', 'maxbuttons-pro'), '<h4>');

				if ($this->authorurl != '') {
					$author .= '<a  href="' . $this->authorurl . '" target="_blank">' . $this->author . '</a>';
				}
				else {
					$author .= $this->author;
				}

				$author .= '</h4>';
				$output .= $author;
		}
		$output .= "<p>" . $this->description . "</p>";

		if ($this->is_local)
		{
		$output .= '<p><a class="maxmodal button" data-modal="delete-pack" href="' . admin_url() . 'admin.php?page=maxbuttons-controller&action=pack-delete&id=' . $this->pack_dir . '&noheader=true">' . __('Delete', 'maxbuttons-pro') . '</a></p>';
		}
		if (! $this->is_local)
		{
			$output .= "<p><a href='javascript:void(0);' class='button-primary load_pack_preview' data-pack='" . $this->pack_url . "'
							   data-packname='" . $this->pack_dir . "'>" . __("Show","maxbuttons-pro") . "</a>&nbsp;";
			if ($license)
			{
				$output .= "<a href='javascript:void(0);' class='button-primary use' data-pack='" . $this->pack_dir . "'>" .  __("Download this pack", "maxbuttons-pro") . "</a></p>";
			}
			else
			{
				$output .= "<a href='javascript:void(0);' class='button-primary disabled'>" .  __("Download this pack", "maxbuttons-pro") . "</a>";
			}
		}
		$output .= "</div>";

		return $output;
	}

	/* Get the buttons in the pack */
	public function get_pack_buttons()
	{
		$counter = 1;

		$pack_array = array(
					'is_local' => $this->is_local,
					'buttons' => array(),
		);

 		$xml = $this->pack_xml;

		foreach ($xml->maxbutton as $xmlbutton) {

				// Check for buttons attributes ( aka old version )
				$button_array = maybe_unserialize($this->parse_pack_button($xmlbutton));
				$temp_id = $button_array["id"];

				$button = MB()->getClass("button");
				$button->clear(); // reset any previous left data.

				$button->setupData($button_array); // load the data

				// prevent importing of icon_ids.
				$button->setData('icon', array('icon_id' => 0) );
				$button_array = $button->get();

				// not being got by button->get()
				$button_array['name'] = $button->getName();
				$button_array['status'] = $button->getStatus();

				$pack_array['buttons'][$temp_id] = array(
							'name' => $button->getName(),
							'desc' => $button->getDescription(),
							'button' => $button->display(array("echo" => false, "mode" => "preview", "preview_part" => "full", "load_css" => "inline")   ),
							'data' => json_encode($button_array),
				);

			}


		return $pack_array;
	}

	static function ajax_import_button()
	{
		$data = json_decode( stripslashes($_POST["data"]), ARRAY_A );
		$button = MB()->getClass('button');

	 	if (isset($data['icon']['icon_id']))
	 		$data['icon']['icon_id'] = 0;

		$data["id"] = 0;

		$button->setupData($data);
		$data = $button->get();
		$data["name"] = $button->getName();
		$data["status"] = $button->getStatus();

		$button_id = $button->update($data);

		echo json_encode( array('button' => $button_id) );
		exit();

	}

	/** Ajax action related to retrieval packs from remote locations */
	static function ajax_actions()
	{
		$action = $_GET["remote_action"];
		$params = $_GET;
		$params["action"] = $action;
		unset($params["remote_action"]);
		$params["wc-api"] = "packs-api";
		$error = false;

	    check_ajax_referer( 'maxbuttons-free-pack', 'nonce' );

 		$url = MB()->get_api_url();

		$params["license_key"] = get_option(MAXBUTTONS_PRO_LICENSE_KEY);

	//	$url = self::$packs_api ; // maybe to api url of main class?

		$url = add_query_arg($params, $url);

		if ($action == 'get_free_pack_preview')
		{
			$url = $_GET["pack_url"];
		}

		$response = wp_remote_get($url);

		if (is_wp_error($response) || $response['response']['code'] != 200) {
			$result = 'error';
			exit(__("Error occured when contacting Pack server","maxbuttons-pro"));
		}
		else {
			$result = wp_remote_retrieve_body($response);

		}
		$maxpack = new maxProPack();

		switch ($action)
		{
			case "get_free_overview":
				$packs = json_decode($result, true);

				$output = '';
				if (! is_array($packs) || count($packs) == 0)
					exit();

				$licenseClass = $this->getClass('license');
				$license = $licenseClass->check_license();

				if (! $license )
				{
					$output .= "<div class='mb-message error'>" . __("Without valid license it's not possible to install free packs.", "maxbuttons-pro") .
								"</div>";
				}

				foreach($packs as $name => $pack)
				{
					$pack["is_local"] = false;
					//$pack["name"] = $name;
					$pack["pack_dir" ] = $pack["query_name"];

					$maxpack->set_pack($pack);
					$args = array("license" => $license);
					$output .= $maxpack->display_pack($args);
				}
				echo $output;
			break;
			case "get_free_pack_preview":
					//$result = json_decode($result, true);

					if (isset($result["errors"]))  // oh oh
					{
						$result_error = array_shift($result["errors"]);
						echo "<div class='error'>" . $result_error["message"] . "</div>";
					}

					if (isset($result))
					{
					//	$pack_xml = maybe_unserialize($result);

						$pack_name = $_GET["pack"];
						$remote_path =  str_replace($pack_name . '.xml', '', $url);

						$pack_xml = $result;
						//$pack_xml = json_decode($result, true);
 						$maxpack->parseload_remote_pack($pack_xml, $remote_path);

						echo $maxpack->output_pack_buttons();
					}
			break;
			case "get_free_download_link":
				//global $wp_filesystem;

			$result = json_decode($result, ARRAY_A);

			if (isset($result["errors"]))  // oh oh
			{
				$error = true;
				$result_error = array_shift($result["errors"]);
				$error_message = $result_error["message"];
			}

				$pack = $params["pack"];
	 		if (! $error) {
				if (isset($result["package"]))
				{
					$zip = wp_remote_get($result["package"]);
				}
				else
				{
					$error = true;
					$error_message = __("Server did not return a valid package link","maxbuttons-pro");
				}


		 		if (is_wp_error($zip))
		 		{
		 			$error = true;
		 			$error_message = __("Failed to get package from server","maxbuttons-pro");
		 		}
				elseif ($zip["response"]["code"] == 404)
				{
					$error = true;
					$error_message = "404";
				}
				else
				{
					$file = $zip["body"];
					 $upload_dir = wp_upload_dir();
					$path = $upload_dir["path"];
					$filename = $path . "/" . $pack . ".zip";
					$handle = fopen($filename,"w+");
					fwrite($handle, $file);
					fclose($handle);

					if (! file_exists($filename)) {
						$error = true;
						$error_message = __("Download Failed","maxbuttons-pro");

					}
					else
					{

						$maxpack->init_filesystem();
						$unzip_result = unzip_file($filename, MAXBUTTONS_PRO_PACKS_DIR);

						unlink($filename); // don't take up space in the upload dir.

						if (is_wp_error($unzip_result))
						{
							$error = true;
							$error_message = __("WordPress could not install your pack","maxbuttons-pro") . " - " . $unzip_result->get_error_message();
						}
					}
				}
			} // ! error
			break;
		}
		if ($error)
		{
			echo json_encode(array("status" => "error", "error_message" => $error_message));
		}
		die();
	}

	/* Create an pack from button for export */
	public function export_pack($args)
	{
		$defaults = array(
			"pack_name" => "",
			"pack_description" => "",
			"pack_author" => "",
			"pack_author_url" => "",
			"buttons" => array(),
			"output_type" => "zip",  // possible - zip for zipfile, ziplocation for file URL, xml for xmlfile only.

		);

		$args = wp_parse_args($args, $defaults);
		extract($args);

 		$full_pack_name = $pack_name;

		// Get the pack name by replacing spaces with dashes and stripping out any
		// special chars by only accepting letters, numbers, underscores, and dashes
		$pack_name = str_replace(' ', '-', $pack_name);
		$pack_name = preg_replace('/[^a-zA-Z0-9_-]/s', '', $pack_name);
		$pack_name = strtolower($pack_name);


		// The root node
		$xml = '<maxbuttons>';

		// Pack element and attributes
		$xml .= '<pack ';
		$xml .= 'name="' . stripslashes($full_pack_name) . '" ';
		$xml .= 'description="' . $pack_description . '" ';
		$xml .= 'author="' . $pack_author . '" ';
		$xml .= 'author_url="' . $pack_author_url . '" ';
		$xml .= '/>';

		// Build elements and attributes for each button

		// create the output folder
		$pack_folder = $this->create_export_folder($pack_name);

		foreach ($buttons as $index => $button_id) {
			$button = MB()->getClass("button");
			$button->set($button_id);
			$data = $button->get();

			$xml .= '<maxbutton> ';
			$xml .= '<name>' . htmlspecialchars($button->getName()) . '</name>';
			$xml .= '<status>' . htmlspecialchars($button->getStatus()) . '</status>';

			$blocks = $button->getBlocks();

			foreach($blocks as $block)
			{
				$block_name = $block->get_name();

				// don't export meta blocks
				if (isset($data[$block_name]) && $block_name != 'meta')
				{
					if ($block_name  == 'icon' && $data[$block_name]['icon_url'] != '')
					{
						$data[$block_name]["icon_url"] = $this->handle_icon_file($data[$block_name]["icon_url"], $pack_folder);
						$data[$block_name]["icon_id"] = 0; // don't export post ID of other installation.
					}
					$xml .= "<$block_name><![CDATA[" .  json_encode($data[$block_name]) . "]]></$block_name>";
				}
			}
 			$xml = apply_filters('maxbutton_export_button', $xml, $button_id);

			$xml .= "</maxbutton>";

		}

		// Close the root node
		$xml .= '</maxbuttons>';


 		$xml = apply_filters('maxbutton_export_xml', $xml, $pack_name, $buttons);

		$this->write_xml_export($xml, $pack_name, $pack_folder);

		if (! $this->checkZipModule() && $output_type !== 'xml')
			return false; // no zip module, no zip file.

		if ($output_type == 'ziplocation')
		{
			$zip_export = $this->create_export_zip($pack_name, $pack_folder, false);
			return $zip_export;
		}
		if ($output_type == 'zip')
		{

			$zip_export = $this->create_export_zip($pack_name, $pack_folder);
			if ($zip_export === false) // error
			{
				MB()->add_notice('error', "Failed to create ZIP file");
				return false;
			}
			// Now delete the folder
			$this->delete_export_folder($pack_folder);

			// Output the ZIP
			$this->output_zip($zip_export, $pack_name);
		}


		if($output_type == 'xml')
			return $xml;

	}

	protected function create_export_folder($pack_name)
	{
			// Create the pack folder in the exports folder
		$pack_folder = MAXBUTTONS_PRO_EXPORTS_DIR . '/' . $pack_name . '/';

		if (! is_dir($pack_folder))
			mkdir($pack_folder, 0777, true);

		if (! is_dir($pack_folder))
		{  echo("Could not read: $pack_folder - Check your file permissions");
			return;
		}
		return $pack_folder;
	}

	protected function write_xml_export($xml, $pack_name, $pack_folder)
	{
			// Write xml to file
		$file_name = $pack_name . '.xml';
		$file_path = $pack_folder . $file_name;
		$file_handle = fopen($file_path, 'w');
		fwrite($file_handle, $xml);
		fclose($file_handle);

	}

	protected function create_export_zip($pack_name, $pack_folder, $return_local = true)
	{
		if (! $this->checkZipModule())
			return false;

		// Start creating the zip file
		$zip = new \ZipArchive();
	//	$zip_overwrite = true;
		$zip_archive = MAXBUTTONS_PRO_EXPORTS_DIR . '/' . $pack_name . '.zip';
		$zip_archive_url = MAXBUTTONS_PRO_EXPORTS_URL . '/' . $pack_name . '.zip';

		$zip_result = $zip->open($zip_archive, \ZIPARCHIVE::CREATE);

		if($zip_result === true)
		{
			// This is the root folder inside the zip archive
			$zip_root_folder = $pack_name . '/';
			$zip->addEmptyDir($zip_root_folder);

			// Get files recursively
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pack_folder));

			// Add each file to the zip archive in the root folder
			foreach ($files as $file) {
				$file = str_replace('\\', '/', realpath($file));
				if (is_file($file) === true) {
					if ($file != '.' && $file != '..') {
						$zip->addFile($file, $zip_root_folder . basename($file));
					}
				}
			}

			$zip->close();
		}
		else
		{ return false; }


		if ($return_local)
			return $zip_archive;
		else
			return $zip_archive_url;
	}

	protected function output_zip($zip_archive, $pack_name)
	{

		// And finally set the zip file for download - In case there is a buffer
		if (ob_get_length() > 0 ) {
			$result = ob_end_clean(); // delete any previous output;
		}


		header('Content-type: application/zip');
		header('Content-disposition: attachment; filename="' . $pack_name . '.zip"');
		//readfile($zip_archive_url);
		readfile($zip_archive);
		exit();
	}

	function delete_export_folder($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != '.' && $object != '..') {
					if (filetype($dir . '/' . $object) == 'dir') {
						// Recursive call to delete the folder
						mbpro_delete_folder($dir . '/' . $object);
					}
					else {
						// Delete the file
						unlink($dir . '/' . $object);
					}
				}
			}

			reset($objects);
			rmdir($dir);
		}
    }

	function handle_icon_file($icon_url, $pack_folder)
	{
		if ($icon_url != '') {

		// Download temp file and remove its extension
		$temp_file = download_url($icon_url);

		// If the result of download_url() is an object, then we know it's the WP_Error object most likely
		// caused by an HTTP 404 error when trying to download the icon. If that happens, we just ignore it.
		if (!is_object($temp_file)) {
			$temp_file_no_ext = substr($temp_file, 0, -3);

			// Rename temp file with extension from icon
			$icon_ext = substr($icon_url, -3, 3);
			$temp_file_new = $temp_file_no_ext . $icon_ext;
			rename($temp_file, $temp_file_new);

			// Copy the file into the pack folder and delete the original
			copy($temp_file_new, $pack_folder . basename($temp_file_new));
			unlink($temp_file_new);

			// Put the basename of the file into the XML
			return basename($temp_file_new);
			}
		}
	}

} // class


?>
