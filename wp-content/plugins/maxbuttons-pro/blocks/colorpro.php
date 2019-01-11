<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

$blockClass["color"] = "colorProBlock";

class colorProBlock extends colorBlock
{
	function __construct()
	{

		/*$this->fields["icon_color"] = array( "default" => '#ffffff',
								"css" => "color",
								"csspart" => "fa");

		$this->fields["icon_color_hover"] = array( "default" => '#ffffff',
								"css" => "color",
								"csspart" => "fa",
								"csspseudo" => "hover",
							); */
		// add colors to second line
		$this->fields["text_color"]["csspart"] .= ",mb-text2";
		$this->fields["text_color_hover"]["csspart"] .= ",mb-text2";
		$this->fields["text_shadow_color"]["csspart"] .= ",mb-text2";
		$this->fields["text_shadow_color_hover"]["csspart"] .= ",mb-text2";

		parent::__construct();

		// ** V 7. 6 - UNUSED **/ 
		//add_filter('color-color-labels', array($this, 'add_color_labels'));
		//add_filter('color-normal-colors', array($this,'add_normal_colors'));
		//add_filter('color-hover-colors', array($this,'add_hover_colors'));
	}

/*	function add_normal_colors($colors)
	{
		array_splice( $colors, 1,0, array("icon_color"));
		return $colors;
	}

	function add_hover_colors($colors)
	{
		array_splice( $colors, 1,0, array("icon_color_hover"));
		return $colors;
	} */

	function add_color_labels($labels)
	{
		array_splice( $labels, 1,0, array(__("Icon color","maxbuttons")) );
		return $labels;
	}

}
?>
