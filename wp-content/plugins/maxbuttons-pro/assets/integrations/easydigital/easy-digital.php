<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

use \simple_html_dom as simple_html_dom;
use MaxButtons\edd_maxbutton_callback as edd_maxbutton_callback;

class MaxButtonEDD
{
	public static function init()
	{
		add_filter('edd_purchase_download_form', array( maxUtils::namespaceit('MaxButtonEDD'), 'purchase_button'), 20);
		add_filter('edd_settings_styles', array( maxUtils::namespaceit('MaxButtonEDD'), 'style_settings'));

		add_action('admin_enqueue_scripts', array( maxUtils::namespaceit('MaxButtonEDD'), 'edd_load_scripts'));
		add_action('wp_enqueue_scripts', array( maxUtils::namespaceit('MaxButtonEDD'), 'edd_front_scripts'));

		// checkout button on purchase
		add_filter('edd_checkout_button_purchase', array( maxUtils::namespaceit('MaxButtonEDD'), 'checkout_button'));
	}

	public static function style_settings($settings)
	{
		require_once('edd_callback.php');

		$settings['main']['maxsettings'] =
						   array(
								'id' => 'maxsettings',
								'name' => '<h3>' . __("MaxButtons",'maxbuttons-pro') . '</h3>',
								'type' => 'header',
							);

		$settings['main']['purchase_button'] = array(
								'id' => 'maxedd_purchase',
								'name' => __("Purchase Button","maxbutton-pro"),
								'desc' => "This button will replace the add to cart / checkout buttons on product view. The text on the button will be replaced by EDD",
								'type' => 'maxbutton',

							);
		$settings['main']['checkout_button'] = array(
								'id' => 'maxedd_checkout',
								'name' => __("Checkout Button","maxbutton-pro"),
								'desc' => "This button will replace the EDD default on the checkout field. The text on the button will be replaced by EDD",
								'type' => 'maxbutton',
							);


 	return $settings;

	}

	public static function purchase_button($form)
	{
		$button_id = intval(edd_get_option('maxedd_purchase')); // from the style settings

		if (! $button_id || $button_id <= 0)
			return $form;

		$button = MB()->getClass('button');

		$button_args = array('echo' => false);

		$domObj = new simple_html_dom($form);

		// Find all anchors in the EDD form
		$anchors = $domObj->find('a,input');

		foreach($anchors as $anchor)
		{
			$button->set($button_id); // reset every time

 			$text = null;
 			if ($anchor->tag == 'input' && ($anchor->type != 'submit') ) // ignore hidden fields and other inputs
 				continue;

 			// try to find text via tag and innertext

			if ($anchor->tag == 'input')
				$text = $anchor->value;
 			elseif ( count($anchor->find('.edd-add-to-cart-label')) > 0)
 			{
 				$found = $anchor->find('.edd-add-to-cart-label');
 				if (isset($found[0]->innertext))
 				{
	 				$text = $found[0]->innertext;
	 			}

 			}
 			elseif (! is_null( $anchor->find('a',0)) )
 			{
 				$found = $anchor->find('a',0);
 				if (isset($found->innertext) && ! is_null($found->innertext))
	 				$text = $found->innertext;
 			}
 			elseif (isset( $anchor->innertext) && ! is_null($anchor->innertext))
 				$text = $anchor->innertext;

 			if (! is_null($text))
			{
				$button->setData('text', array('text' => $text));

			}

			$domButton = new simple_html_dom($button->display($button_args));

			$buttonAnchor = $domButton->find('a',0);
			$container = $domButton->find('.mb-container', 0);
			$center = $domButton->find('.mb-center', 0);

			// add container, check for center
			$prepend = '';
			$prepend_end = '';
			if (is_object($container))
			{
				 $is_centered = (is_object($center)) ? true : false;
	 		 	 $prepend = ' <span class="' . $center->class . '">';

				 if ($is_centered)
				 {
					 $prepend .= '<span class="' . $container->class . '">';
				 }
			 		$prepend_end = '</span></span>';
			}

			$style = $anchor->style;
			$class = $anchor->class;
			$class = str_replace(array('button', 'edd-submit'), '', $class);
 			$inner = $buttonAnchor->innertext;

			if ($anchor->tag == 'a')
			{

				$anchor->innertext = $inner ;
				$anchor->class = $class . ' ' . $buttonAnchor->class;
				$anchor->style = $style;
				if ($prepend != '')
				{
						$anchor->outertext = $prepend . $anchor->outertext . $prepend_end;
				}
			}
			elseif ($anchor->tag == 'input')
			{
				$style = $style . 'box-sizing: content-box; '; // original element

				$anchor->outertext = $prepend . '<button type="submit"
						class="' . $class . ' ' . $buttonAnchor->class . '" style="' . $style . '">' . $inner . '</button>' . $prepend_end;
			}
		}


		return $domObj->save();
	}

	public static function checkout_button($edd_button)
	{

		$button_id = intval(edd_get_option('maxedd_checkout')); // from the style settings

		if (! $button_id || $button_id <= 0)
			return $edd_button;

		$button = MB()->getClass('button');
		$button->set($button_id);

		// check if MB replacement is set, otherwise escape.

		// load DomObj.
		$domObj = new simple_html_dom($edd_button);
		$element = $domObj->find('input',0);

 		$element->style = "display:none";

		// Do Text
		$text = $element->value;
		$element_id = $element->id;
	//	$element->id = 'edd-purchase-button-hidden';

	 	if (! is_null($text))
		{
				$button->setData('text', array('text' => $text) );
		}

		$button_args = array('echo' => false, 'load_css' => 'inline');

		$the_button = $button->display($button_args);
		//$the_button = str_replace('.maxbutton-', '#edd_purchase_form .maxbutton-', $the_button);
		$domButton = new simple_html_dom($the_button);

		$newButton = $domButton->find('a', 0);

		$newButton->id = $element->id; //. '-max'; // removed due to Stripe process issues.
		$newButton->type = 'submit';
		$newButton->tag = 'button';
		$newButton->href= null;

		return $domButton->save() . $domObj->save();
	}

	public static function edd_load_scripts($hook)
	{
		// check if edd is there.
		if (! function_exists('edd_is_admin_page'))
			return;

		// edd script.php
		if ( ! apply_filters( 'edd_load_admin_scripts', edd_is_admin_page(), $hook ) ) {
			return;
		}
		$version = MAXBUTTONS_VERSION_NUM;
		//MB()->load_modal_script(); // load the button picker
		MB()->add_admin_styles('maxbuttons');
		MB()->load_media_script();


		wp_register_script('mb-edd-admin', MB()->get_plugin_url(true) . 'assets/integrations/easydigital/eddmaxadmin.js', array('jquery'),
			$version, true);

		$js_url = trailingslashit(MB()->get_plugin_url(true) . 'js');
		wp_enqueue_script('maxbuttons-font', $js_url . 'maxbuttons_fonts.js', array('jquery'),$version, true);


		wp_enqueue_script('mb-edd-admin');

		wp_enqueue_style('mb-edd-css', MB()->get_plugin_url(true) . 'assets/integrations/easydigital/eddmax.css');
	}

	public static function edd_front_scripts()
	{
		if (! function_exists('edd_is_ajax_disabled'))
			return;

		if ( edd_is_ajax_disabled() )
			return;

		$version = MAXBUTTONS_VERSION_NUM;
		$edd_version = defined('EDD_VERSION') ? EDD_VERSION : false;

		if (version_compare($edd_version, '2.9.3', '<')) // only for old version, 293 has it's own submit.
		{
			wp_register_script('mb-edd-front', MB()->get_plugin_url(true) . 'assets/integrations/easydigital/eddmax.js', array('jquery'), $version, true);
			wp_enqueue_script('mb-edd-front');
		}

	}

} // Class






MaxButtonEDD::init();
