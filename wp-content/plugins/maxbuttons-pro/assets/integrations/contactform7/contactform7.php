<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

use \WPCF7_TagGenerator as WPCF7_TagGenerator;
use \WPCF7_ContactForm as WPCF7_ContactForm;
use \WPCF7_FormTag as WPCF7_FormTag;

class MBCF7
{

	static $form_class = '';

	/** CF7 filters to add functions for MB **/
	public static function init()
	{
		add_filter('wpcf7_editor_panels', array( maxUtils::namespaceit('MBCF7'), 'panels') );
		add_filter('wpcf7_form_class_attr', array( maxUtils::namespaceit('MBCF7'), 'form_class') );
		add_action( 'wpcf7_init', array( maxUtils::namespaceit('MBCF7'), 'shortcode' ));

	}

	/** Add CF7 MB shortcode **/
	public static function shortcode()
	{
		if (function_exists('wpcf7_add_form_tag'))
			wpcf7_add_form_tag('cf7_maxbutton', array( maxUtils::namespaceit('MBCF7'), 'shortcode_handler') );
		else // backward compatible.
			wpcf7_add_shortcode('cf7_maxbutton', array( maxUtils::namespaceit('MBCF7'), 'shortcode_handler') );
	}

	public static function form_class($class)
	{
		$form = WPCF7_ContactForm::get_current();
		if (method_exists($form, 'scan_form_tags') )
		{
			$count = count($form->scan_form_tags(array('type' => 'cf7_maxbutton')));
		}
		else  // backward compatible.
		{
			$count = count($form->form_scan_shortcode(array('type' => 'cf7_maxbutton') ));
		}

		if ($count > 0 )
			$class .= ' mbsubmit';

		self::$form_class = $class;

		return $class;

	}

	//http://contactform7.com/2015/02/27/using-values-from-a-form-tag/#more-13351
	public static function shortcode_handler($tag)
	{

	$tag = new WPCF7_FormTag( $tag );

	$id = $tag->get_id_option();

	$form = WPCF7_ContactForm::get_current();

	$short = array('id' => $id,
				   'url' => '#submit',
				   'window' => 'same',
			);

		$script= "<script type='text/javascript'>
				jQuery(document).ready(function($) {
						$('.maxbutton-$id').on('click', function (e) {
							e.preventDefault();
							var target = e.target;
							$(target).parents('form.mbsubmit').submit();
					});
				});
				 </script>	";
		return $script . MB()->shortcode($short);
	}

	public static function panels($panels)
	{

		MB()->load_modal_script(); // load the button picker
		MB()->add_admin_styles('maxbuttons');

		MB()->load_media_script();

		$version = MAXBUTTONS_VERSION_NUM;
		wp_register_script('mbcf7_js',  MB()->get_plugin_url(true) . '/assets/integrations/contactform7/mbcf7.js', array('jquery', 'maxbuttons-modal'), $version, true);

		wp_localize_script('mbcf7_js','mbcf7',
			array('title' => __('Select a submit button for your form', 'maxbuttons-pro'),
				  'note' => __('Your selected button will function as a submit button', 'maxbuttons-pro'),
		));



		wp_enqueue_script('mbcf7_js');


		$tag_generator = WPCF7_TagGenerator::get_instance();
		$options = array('button_content' => '');
		$tag_generator->add('cf7_maxbutton', __("MaxButtons","maxbuttons-pro"), array(maxUtils::namespaceit('MBCF7'), 'button_window'), $options);
		return $panels;

	}

	public static function button_window($cform, $options)
	{
		?>
			<div class="insert-box">
				<input type="text" onfocus="this.select()" readonly="readonly" class="tag code" name="cf7_maxbutton">

				<div class="submitbox">
				<input type="button" id='mbcf7_insert_tag' value="Insert Tag" class="button button-primary insert-tag">
				</div>

				<br class="clear">

			</div>
		<?php

	}
}


MBCF7::init();
