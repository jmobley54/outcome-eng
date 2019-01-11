<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

// harder than the normal one.

class maxProButton extends maxButton
{


	public function shortcode_overrides($data, $atts)
	{

		if (isset($atts["text2"]))
		{
				$data["text"]["text2"] = $atts["text2"];

		}
		if (isset($atts['google_label']))
		{
				$data["google"]["gtrack_label"] = $atts["google_label"];
		}

		if (isset($atts['google_category']))
		{
			$data["google"]["gtrack_cat"] = $atts["google_category"];
		}
		if (isset($atts['google_action']))
		{
			$data["google"]["gtrack_action"] = $atts["google_action"];
		}

		return $data;
	}

	public function update($data)
	{
		$return = parent::update($data);
		maxButtonsProAdmin::updateUsedFonts(); // fonts loader

		return $return;
	}

	/** Single button export
	*
	* Create the export format for one button
	*
	* @return $string Button Export Format
	*
	*/
	function export()
	{

	}

	/** Import single button
	*
	*  Imports a button from Button Export Format and creates a new entry into this installation.
	*
	*  @param string $import Import string
	*  @return ?
	*/
	function import($import)
	{



	}

}
