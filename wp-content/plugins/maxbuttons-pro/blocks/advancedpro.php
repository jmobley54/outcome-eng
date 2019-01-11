<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');
$blockClass["advanced"] = "advancedProBlock";


class advancedProBlock extends advancedBlock
{
	static $hooks_loaded = false;

	public function __construct()
	{
		parent::__construct();
		$this->fields['anchor_data'] = array('default' => '');
		$this->fields['anchor_data_value'] = array('default' => '');
		$this->fields['extra_id']  = array('default' => '');
		$this->fields['custom_css'] = array('default' => '');
		$this->fields['custom_css_hover'] = array('default' => '');

		if (! self::$hooks_loaded)
		{
			//add_action('mb-after-advanced', array($this, 'advanced_fields'));
      add_action('mb/editor/afterfield/custom_rel', array($this, 'advanced_fields'));  // into basic

      self::$hooks_loaded = true;
		}

		add_filter('mb/button/compiledcss', array($this, 'check_customcss'), 10, 1);

	}

	public function save_fields($data, $post)
	{
		$data = parent::save_fields($data, $post);

		$extra_data = array();

		if ( isset($post['anchor_data_add']) )
		{

			$anchor_keys = $post['anchor_data_add'];
			$anchor_values = $post['anchor_data_value_add'];

			foreach($anchor_keys as $index => $value)
			{
				$value = sanitize_text_field($value);
				if (strlen($value) > 0)
				{
					$extra_data[$value] = isset($anchor_values[$index]) ? sanitize_text_field($anchor_values[$index]) : '';

				}
			}

			$data[$this->blockname]['extra_data'] = $extra_data;
		}

		if (isset($post['custom_css']) && strlen($post['custom_css']) > 0)
		{
			// sanitize textarea doesn't filter line break, which is required here.
			$data[$this->blockname]['custom_css'] = sanitize_textarea_field($post['custom_css']);
		}

		if (isset($post['custom_css_hover']) && strlen($post['custom_css_hover']) > 0)
		{
			// sanitize textarea doesn't filter line break, which is required here.
			$data[$this->blockname]['custom_css_hover'] = sanitize_textarea_field($post['custom_css_hover']);
		}

	return $data;

	}

	/** Check if button has custom CSS. This is a Filter for CompileCSS in the button class **/
	public function check_customcss($css)
	{
		$data = $this->data[$this->blockname];

		$id = $this->data['id'];
		$name = $this->data['basic']['name'];

		$custom_css = isset($data['custom_css']) ? $data['custom_css'] : '';
		$custom_css_hover = isset($data['custom_css_hover']) ? $data['custom_css_hover'] : '';

		if (strlen($custom_css) <= 0 && strlen($custom_css_hover) <= 0) // none
		{
			return $css;
		}

		if (strlen($custom_css) > 0)
		{
			$args = array(
					'id' => $id,
					'name' => $name,
					'custom_css' => $custom_css,
					'is_hover' => false);

			$css = $this->build_customcss($css, $args);
		}

		if (strlen($custom_css_hover) > 0)
		{
			$args = array(
					'id' => $id,
					'name' => $name,
					'custom_css' => $custom_css_hover,
					'is_hover' => true);

			$css = $this->build_customcss($css, $args);
		}

		// Hack . Remove filter on this instance ( run once ), otherwise it will fire for every button after.
		$bool = remove_filter('mb/button/compiledcss', array($this, 'check_customcss'), 10);
		return $css;
	}

	public function build_customcss($css, $args)
	{

		$id = $args['id'];
		$name = $args['name'];
		$is_hover = $args['is_hover'];
		$custom_css = $args['custom_css'];

		$parser = new maxCSSParser();

		// imitate main selector here. Workaround. Created by CSSParser Usually. Bleh
		//.maxbutton-93.maxbutton.maxbutton-yhenk2

		$main_selector = '.maxbutton-' . $id . '.maxbutton';
		if ( $is_hover)
		{
			$main_selector .= ':hover';
		}
		if (strlen($name) > 0)
		{
			$name = maxUtils::translit($name); // name science from button class.
			$name = sanitize_title($name);
			$name = str_replace('%', '', $name);
			$main_selector .= '.maxbutton-' . $name;
		}

		$custom_compiled = $parser->compile($main_selector . '{' . $custom_css . '}');

		if (! $parser->get_compile_errors())
		{
			$css .=  $custom_compiled;
		}

		return $css;

	}

	public function advanced_fields()
	{
    $admin = MB()->getClass('admin');

		$extraid = new maxField();
		$extraid->id = 'extra_id';
		$extraid->name = $extraid->id;
		$extraid->value = maxBlocks::getValue($extraid->id);
		$extraid->label = __('Button ID', 'maxbuttons-pro');
		$extraid->help = __('Sets the ID. Take care to not assign the same ID to multiple buttons. This can cause issues', 'maxbuttons-pro');
		//$extraid->inputclass = 'medium';

		$admin->addField($extraid, 'start', 'end');

		$data = new maxField();
		$data->id = 'anchor_data';
		$data->name = $data->id;
		$data->value = maxBlocks::getValue($data->id);
		$data->label = __('Data Attribute','maxbuttons-pro');
		$data->note = __('Used for targeting plugins and other data sources that make use of the data- attribute.', 'maxbuttons-pro');
		$data->before_input = 'data-';
		$data->placeholder = "example";
		$data->inputclass = 'medium';
		//$data->output('start', '');

    $admin->addField($data, 'start', '');

		$dval = new maxField();
		$dval->id = 'anchor_data_value';
		$dval->name = $dval->id;
		$dval->value= maxBlocks::getValue($dval->id);
		$dval->label = __('Value', 'maxbuttons-pro');
		$dval->placeholder = '';
		$dval->inputclass = 'medium';
		//$dval->output(false, 'end');

    $admin->addField($dval, '', 'end');

		$extradata_num = 0;

		$extra_data = maxBlocks::getValue('extra_data');

		if ($extra_data)
		{
			foreach($extra_data as $key => $value)
			{
				$data = new maxField();
				$data->id = 'anchor_data_add[' . $extradata_num . ']';
				$data->name = $data->id;
				$data->value = $key;
				$data->label = __('Data Attribute','maxbuttons-pro');
				$data->note = '';
				$data->before_input = 'data-';
				$data->inputclass = 'medium';
		//		$data->output('start', '');

        $admin->addField($data, 'start');

				$dval = new maxField();
				$dval->id = 'anchor_data_value_add[' . $extradata_num . ']';
				$dval->name = $dval->id;
				$dval->value= $value;
				$dval->label = __('Value', 'maxbuttons-pro');
				$dval->placeholder = '';
				$dval->inputclass = 'medium';
			//	$dval->output(false, 'end');

        $admin->addField($dval, '', 'end');

				$extradata_num++;
			}

		}


		$plus_data = new maxField('generic');
		$plus_data->id = 'add_data';
		$plus_data->name = $plus_data->id;
		$plus_data->label = '&nbsp;';
		$plus_data->content = " <div id='add_data_attr' class='data_attr_plus' data-attrnum='$extradata_num'> </div>";

	//	$plus_data->output('start','end');
    $admin->addField($plus_data, 'start', 'end');

		$customcss = new maxField('textarea');
		$customcss->id = 'custom_css';
		$customcss->name = $customcss->id;
		$customcss->label = __('Custom CSS', 'maxbuttons-pro');
		$customcss->note = __('Custom CSS only for this button', 'maxbuttons-pro');
		$customcss->help = __('Custom CSS will be wrapped around MaxButton class. Use elements to override i.e. .mb-text { color: #ff0000; } for text','maxbuttons-pro');
		$customcss->value = maxBlocks::getValue($customcss->id);

		if (strlen($customcss->value) > 0)
		{
			$parser = new maxCSSParser();
			$custom_compiled = $parser->compile($customcss->value);
			$error = $parser->get_compile_errors();

			if ($error)
				$customcss->error = $error->getMessage();
		}

		$admin->addField($customcss, 'start', 'end');


		$customcss_hover = clone $customcss;
		$customcss_hover->id = 'custom_css_hover';
		$customcss_hover->name = $customcss_hover->id;
		$customcss_hover->label = __('Custom CSS Hover', 'maxbuttons-pro');
		$customcss_hover->note = __('Custom CSS only for this button on hover', 'maxbuttons-pro');
		$customcss_hover->value = maxBlocks::getValue($customcss_hover->id);

		if (strlen($customcss_hover->value) > 0)
		{
			$parser = new maxCSSParser();
			$custom_compiled = $parser->compile($customcss_hover->value);
			$error = $parser->get_compile_errors();

			if ($error)
				$customcss_hover->error = $error->getMessage();
		}
		else {
			$customcss_hover->error = null;  // in case the cloned one has an error.
		}

		$admin->addField($customcss_hover, 'start', 'end');


    $admin->display_fields();
	}

	public function parse_button($domObj, $mode= 'normal')
	{
		$domObj = parent::parse_button($domObj, $mode);

		$data = $this->data[$this->blockname];
		$button_id = $this->data["id"];
		$anchor = $domObj->find("a",0);

 		$anchor_data = isset($data['anchor_data']) ? trim($data['anchor_data']) : '';
 		$anchor_data_value = isset($data['anchor_data_value']) ? $data['anchor_data_value'] : '';
 		if (strlen($anchor_data) > 0)
	 		$anchor->{'data-' .$anchor_data} = $anchor_data_value;

		if (isset($data['extra_id']) && strlen(trim($data['extra_id'])) > 0)
		{
			$anchor->id = $data['extra_id'];
		}

 		if (isset($data['extra_data']))
 		{
 			foreach($data['extra_data'] as $key => $value)
 			{
 				$anchor->{'data-' . $key} = $value;
 			}

		}



		return $domObj;
	}

}
