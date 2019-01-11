<?php
/*
Plugin Name: MaxButtons Pro
Plugin URI: http://maxbuttons.com
Description: The ultimate WordPress button generator. If you have the free version, you should deactivate it when running the Pro version.
Version: 7.8
Author: Max Foundry
Author URI: http://maxfoundry.com
Text Domain: maxbuttons-pro
Domain Path: /languages

Copyright 2018 Max Foundry, LLC (http://maxfoundry.com)
*/
namespace MaxButtons;

if (! function_exists('MaxButtons\maxbuttons_php52_nono'))
{
	function maxbuttons_php52_nono()
	{
		$message = sprintf( __("From version 3 MaxButtons requires at least PHP 5.3 . You are running version: %s ","maxbuttons"), PHP_VERSION);
		echo"<div class='error'> <h4>$message</h4></div>";
		return;
	}
}
if ( version_compare(PHP_VERSION, '5.3', '<' ) ) {

	add_action( 'admin_notices', 'MaxButtons\maxbuttons_php52_nono' );
	return;
}

if (! function_exists('MaxButtons\maxbutton_double_load'))
{
	function maxbutton_double_load()
	{
		$message =  __("Already found an instance of MaxButtons running. Please check if you are trying to activate two MaxButtons plugins and deactivate one. ","maxbuttons" );
		echo "<div class='error'><h4>$message</h4></div>";
		return;
	}
}

if (function_exists("MaxButtons\MB"))
{
	add_action('admin_notices', 'MaxButtons\maxbutton_double_load');
	return;
}

if (! defined('MAXBUTTONS_ROOT_FILE'))
	define("MAXBUTTONS_ROOT_FILE", dirname(__FILE__) . "/MaxButtons/maxbuttons.php"); // File to core package.
define("MAXBUTTONS_PRO_ROOT_FILE",__FILE__);

if (! defined('MAXBUTTONS_VERSION_NUM'))
	define('MAXBUTTONS_VERSION_NUM', '7.8');

define('MAXBUTTONSPRO_RELEASE',"15 Dec 2018");

// init MaxButtons
require_once("MaxButtons/classes/maxbuttons-class.php");
require_once("MaxButtons/classes/buttons.php");
require_once('MaxButtons/classes/button.php');
require_once("MaxButtons/classes/installation.php");
require_once("MaxButtons/classes/max-utils.php");

require_once("MaxButtons/classes/maxCSSParser.php");
require_once("MaxButtons/classes/admin-class.php");

require_once("MaxButtons/classes/block.php");

require_once('MaxButtons/classes/field.php');
require_once('MaxButtons/classes/blocks.php');

require_once("MaxButtons/classes/integrations.php");
require_once("MaxButtons/includes/maxbuttons-admin-helper.php");

// external libraries
if ( version_compare(PHP_VERSION, '5.4', '<' ) )
{
		require_once("MaxButtons/assets/libraries/scssphp_legacy/scss.inc.php");
}
else
{
	require_once("MaxButtons/assets/libraries/scssphp/scss.inc.php");
}
require_once("MaxButtons/assets/libraries/simple-template/simple_template.php");

if (! class_exists('simple_html_dom_node'))
	require_once("MaxButtons/assets/libraries/simplehtmldom/simple_html_dom.php");

// Do the pro
require_once("classes/maxbuttons-pro-class.php");
require_once("classes/button-pro-class.php");
require_once("classes/installation-pro.php");
require_once('classes/license.php');
require_once('classes/max-pro-utils.php');
require_once("classes/maxbuttonspro-admin-helper.php");
require_once("classes/pack-class.php");
require_once("classes/maxbuttons-packs-class.php");
require_once("classes/admin_pro_class.php");
require_once("classes/integrations.php");
require_once('classes/maxUpdate.php');


if (! function_exists("MaxButtons\MB"))	{
	function MB()
	{
		return maxButtonsPro::getInstance();
	}
}
// runtime.
$m = new maxButtonsPro();

// install pro blocks
add_filter('mb-block-paths', array(maxUtils::namespaceit("maxInstallPro"), "button_pro_blockpath"));
add_filter('mbcollection_paths', array(maxUtils::namespaceit('maxInstallPro'), "collection_pro_path"));

register_activation_hook(__FILE__, array(maxUtils::namespaceit("maxInstallPro"),'activation_hook') );
register_deactivation_hook(__FILE__,array(maxUtils::namespaceit("maxInstallPro"), 'deactivation_hook') );
