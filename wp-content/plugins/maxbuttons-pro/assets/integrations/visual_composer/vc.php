<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

class MaxButtonsVC
{

	public static function init()
	{
		add_action( 'vc_before_init', array( maxUtils::namespaceit('MaxbuttonsVC'), 'checkvc' ) );
		add_action( 'vc_load_shortcode', array( maxUtils::namespaceit('MaxbuttonsVC'), 'load_shortcode_front') );
		if (class_exists('Vc_Manager'))
			add_shortcode('vc_maxbutton', array( maxUtils::namespaceit('MaxbuttonsVC'), 'shortcode') );
	}

	public static function checkvc()
	{
	   if ( ! defined( 'WPB_VC_VERSION' ) ) {
	 	return false;

	   }

	   if (! function_exists('vc_map'))
	   { 	return false;

	   }

	   if (! function_exists('vc_add_shortcode_param'))
	   {	return false;

		}

	 	$script_url = trailingslashit(MB()->get_plugin_url(true)) . 'assets/integrations/visual_composer/maxbuttonsvc.js';
	 	vc_add_shortcode_param('maxbutton_select' , array(maxUtils::namespaceit('MaxButtonsVC'),'selector'), $script_url ); // adding the chooser field
	 	self::map(); // adding the element
	}

	public static function load_shortcode_front($shortcodes)
	{
		return;
	}

	public static function shortcode($atts, $content = 0)
	{
		$url = '';
		if (isset($atts["url"]))
		{
			$link = vc_build_link($atts["url"]);
			$url = ' url="' . $link["url"] . '" ';
		}

		if (! isset($atts['id']))
			return;

		$text = isset($atts["text"])  ? ' text=' . $atts["text"] : '';

		if (vc_is_frontend_ajax())
		{
			$style = 'style="inline"';
		}
		else
			$style = '';

		return do_shortcode("[maxbutton id='" . $atts["id"] . "'" . $url . $text . " " . $style . " ]");
	}

	public static function map()
	{

      vc_map( array(
            "name" => __("MaxButton", 'maxbuttons-pro'),
            "description" => __("Add MaxButton", 'maxbuttons-pro'),
            "base" => "vc_maxbutton",
            "class" => "",
            "controls" => "full",
            "show_settings_on_create" => true,
           // "custom_markup" => static::shortcode(array('id' => ,
            "icon" => MB()->get_plugin_url() . '/images/mb-32.png', // or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
            "category" => __('Content', 'js_composer'),
            //'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
            //'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
            "params" => array(
                array(
                  "type" => "maxbutton_select",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Button", 'vc_extend'),
                  "param_name" => "id",
                  "value" => '',
                  "description" => __("", 'vc_extend')
              ),
              array(
                  "type" => "vc_link",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("URL [optional]", 'maxbuttons-pro'),
                  "param_name" => "url",
                  "value" => '',
                  "description" => __("Change URL for this button only", 'maxbuttons-pro')
              ),
              array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Text [optional]", 'maxbuttons-pro'),
                  "param_name" => "text",
                  "value" => '',
                  "description" => __("Use different text for this button only", 'vc_extend')
              ),
            )
        ) );


	}

	public static function selector($settings, $value)
	{
		$output = '<div class="maxbutton_select">
					<input name="' . esc_attr( $settings['param_name'] ) . '" type="hidden" value="' . esc_attr($value) . '"
					class="wpb_vc_param_value">
					<p class="button_preview"> ';

		 			$button= MB()->getClass('button');

 			if (intval($value) > 0)
 			{
 				$button->set($value);
 				$output .= $button->display(array('display' => false, 'echo' => false, 'load_css' => 'inline') );
 			}
		$nonce = wp_create_nonce('maxajax');

		$output .= '</p>
					<button class="button-primary vc_media_button" data-parent="body" data-nonce="' . $nonce . '" type="button">' . __("Select a button","maxbuttons-pro") . '</button>
					</div>';
		return $output;


	}
} // class


MaxButtonsVC::init();
