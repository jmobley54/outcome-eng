<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

class maxProUtils
{

  protected static $webfonts = array();

  public static function checkWebFonts($data)
	{

		$system_fonts = array('', 'Arial', 'Courier New', 'Georgia', 'Tahoma', 'Times New Roman', 'Trebuchet MS',
	'Verdana');
		$webfontspath = MB()->get_plugin_path(true) . '/assets/fonts/webfonts.json';
		$webfontspath = apply_filters('maxbuttons/webfonts', $webfontspath);

		$web_fonts = false;

		$fonts = array();
		if (isset($data['font']))
		{
			$fonts[$data['font']] = array('bold' => $data['font_weight'],
											  'style' => $data['font_style'],
									);
		}
		if (isset($data['font2']))
		{
			$fonts[$data['font2']] = array('bold' => $data['font_weight2'],
										  'style' => $data['font_style2'],
									);
		}

		foreach($fonts as $font => $options)
		{
			$is_bold = (isset($options['bold']) && $options['bold'] == 'bold' ) ? true: false;
			$is_italic = (isset($options['style']) && $options['style'] == 'italic' ) ? true: false;

			$full_font = $font;

			if(in_array($font, $system_fonts))
				continue;

			if (! $web_fonts) // preventing double loading
				$web_fonts = json_decode(file_get_contents($webfontspath), true);

			foreach($web_fonts['items'] as $index => $item)
			{
				if ($item['family'] == $font)
				{
					$weight = false;
					$variants = $item['variants'];

					if (in_array('regular', $variants))
					{
						$weight = 400;
					}
					elseif (in_array('300', $variants))
					{
						$weight = 300;
					}
					elseif(in_array('500', $variants))
					{
						$weight = 500;
					}
					break;
				}
			}
			$web_fonts = null;

			$font = preg_replace('/\s/', '+', $font);
			$url = '//fonts.googleapis.com/css?family=' . $font . ':' . $weight;

			static::$webfonts[$full_font] = $url;
		}

	}
	/** Function adds webfonts to compiled css
	*
	*
	*/
	public static function addWebFonts($css)
	{

		$fonts = '';
		foreach(static::$webfonts as $font => $url)
		{
			$fonts .= '@import url(' . $url . ');';
		}
		return $css . $fonts;
	}

}
