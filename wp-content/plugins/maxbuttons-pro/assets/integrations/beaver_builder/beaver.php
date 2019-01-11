<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

class MaxButtonsBeaver
{

	public static function init()
	{
		 add_action('init', array( maxUtils::namespaceit('MaxButtonsBeaver'),'add_module'));

	}

	public static function add_module()
	{
		if ( class_exists( '\FLBuilder' ) ) {
			require_once('module-maxbutton.php');

			add_filter('fl_builder_control_maxbutton-field', array( maxUtils::namespaceit('MaxButtonsBeaver'), 'button_field'), 10, 4);
			$settings = static::get_settings();
			\FLBuilder::register_module( maxUtils::namespaceit('moduleMaxbutton'), $settings );

		}

	}

	public static function get_settings()
	{
		$settings = array(
			'tab' => array(
				'title' => __('MaxButtons Pro','maxbuttons-pro'),
				'sections' => array(
					'section' => array (
						'title' => __('Choose a MaxButton', 'maxbuttons-pro'),
						'fields' => static::get_fields(),
					),
				),
			),

		);

		return $settings;
	}

	protected static function get_fields()
	{
		$fields = array(
					'button_id' => array(
					'type' => 'maxbutton-field',
					'label' => __('Button','maxbuttons-pro'),
				),
				'text' => array(
					'type'          => 'text',
					'label'         => __( 'Button Text', 'maxbuttons-pro' ),
					'default'       => '',
				//	'maxlength'     => '',
					'size'          => '40',
					//'placeholder'   => __( 'Placeholder text', 'fl-builder' ),
					//'class'         => 'my-css-class',
				//	'description'   => __( 'Change text for this button [optional]', 'maxbuttons-pro' ),
					'help'          => __( 'You can override the default button text - this is optional', 'maxbuttons-pro' )
				),

				'url' => array(
					'type'          => 'text',
					'label'         => __( 'URL', 'fl-builder' ),
					'default'       => '',
				//	'maxlength'     => '',
					'size'          => '40',
					//'placeholder'   => __( 'Placeholder text', 'fl-builder' ),
					//'class'         => 'my-css-class',
				//	'description'   => __( 'Custom URL for this button [optional]', 'maxbuttons-pro' ),
					'help'          => __( 'Here you can change the link set in the button editor - this is optional', 'maxbuttons-pro' )
				),
			);
		return $fields;
	}

	public static function button_field($name, $value, $field, $settings) {
    	echo '<div id="mb-beaver-field">
    		   <input type="hidden" name="' . $name . '" value="' . $value . '">';


    	echo '<div id="beaver_maxbutton_preview">';
    	if (intval($value) > 0)
    	{
    		$button = MB()->getClass('button');
    		$args = array('id' => $value, 'style' => 'inline');

			echo $button->shortcode($args);
    	}
    	echo '</div>';

    	echo  '<div class="mbbeaver_button"><button type="button" id="maxbutton-picker" class="fl-builder-button fl-builder-button-large fl-builder-button-primary maxbutton_media_button"
    			 name="' . $name . '">' . __('Select a button') . '</button></div>';
  		echo ' </div>'; // closing statement

	}

}


MaxButtonsBeaver::init();
