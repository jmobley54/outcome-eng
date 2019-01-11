<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

class MBXTranslate
{

	public static function init()
	{
		if(defined( 'QTRANSLATE_FILE'))
		{
			add_filter('mb/button/data_before_display', array(maxUtils::namespaceit('MBXTranslate'), 'translate_backend'), 10, 3);
			add_filter('mb-footer', array(maxUtils::namespaceit('MBXTranslate'), 'translate_footerfront'), 10, 3);
		}

	}


	public static function translate_backend($data, $mode, $args)
	{
		if ($mode == 'editor')
			return $data;

		$preview = isset($args['preview']) ? $args['preview'] : false;


		if ($preview)
		{
			if (isset($data['basic']['url']))
			{
				$data['basic']['url'] = apply_filters('translate_text', $data['basic']['url'] );
				$data['url'] = $data['basic']['url'];
			}

			if (isset($data['text']['text']))
				$data['text']['text'] = apply_filters('translate_text', $data['text']['text'] );

			if (isset($data['text']['text2']))
				$data['text']['text2'] = apply_filters('translate_text', $data['text']['text2'] );

				if (isset($data['basic']['link_title']))
					$data['basic']['link_title'] = apply_filters('translate_text', $data['basic']['link_title'] );

		}

		//translate_text
		return $data;
	}

	/** Loading items in the 'footer' of MB doens't get picked up by Qtranslate. This is for instance the static Collection buttons */
	public static function translate_footerfront($item, $output, $type = 'css')
	{
		if ($type == 'collection_output')
		{
			$output = \qtranxf_useCurrentLanguageIfNotFoundShowAvailable($output);
			MB()->do_footer($item, $output, $type);
		}
	}

/* Due to I18N Json
	public static function enqueue_script()
	{
			exit('EQ SCIPT');
			$version = MAXBUTTONS_VERSION_NUM;

			wp_enqueue_script('mbpro-qtranslate', MB()->get_plugin_url(true) . 'assets/integrations/qtranslate/qtranslate.js', array('jquery'),
				$version, true  );

	} */

}

MBXTranslate::init();
