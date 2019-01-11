<?php 
namespace MaxButtons; 
defined('ABSPATH') or die('No direct access permitted');

// 3rd Party integrations for MaxButtonsPRO 
class maxIntegrationsPRO
{
	static function init() 
	{
		add_action('plugins_loaded', array( maxUtils::namespaceit('maxIntegrationsPRO'), 'load_integrations'), 999); 

		self::doDirectInit();
	}
	
	
	static function load_integrations()
	{
		$path = MB()->get_plugin_path(true) . "assets/integrations/"; 
		 		
		//require_once( MB()->get_plugin_path(true) . "assets/integrations/fusion_builder/fusion_builder.php"); 
		require_once($path . 'qtranslate/qtranslate.php'); 
	}

	static function doDirectInit() 
	{
		$path = MB()->get_plugin_path(true) . "assets/integrations/"; 
		require_once($path . 'contactform7/contactform7.php'); 
		require_once($path . 'visual_composer/vc.php'); 
		require_once($path . 'beaver_builder/beaver.php'); 
		require_once($path . 'easydigital/easy-digital.php'); 

		//require_once($path . 'wpmenu/wpmenu.php'); 
	}


} // class

