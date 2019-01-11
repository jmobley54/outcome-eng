<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');
$blockClass["icon"] = "iconBlock";
$blockOrder[60][] = "icon";

use \simple_html_dom as simple_html_dom;

class iconBlock extends maxBlock
{
	protected $blockname = "icon";
 	protected $fields = array("use_fa_icon" => array("default" => 0),
							  "fa_icon_value" => array("default" => '',
							  						 "css" => ''),

							  "fa_icon_size" => array("default" => '30px',
							  						 "css" => 'font-size',
							  						 "csspart" => "mb-icon,mb-icon-hover"),

							  "icon_id" => array('default' => '',
							  					 'css' => ''
							  					),
							  "icon_url" => 	array('default' => '',
							  						  'css' => ''),
								'icon_size' => array('default' => '',),
								'icon_color'  => array('default' => '#ffffff',
																			 'css' => 'color',
																			 'csspart' => 'mb-icon,mb-icon-hover',
																		 ),
								'icon_color_hover' => array ('default' => '#505ac7',
																				'css' => 'color',
																				'csspart' => 'mb-icon,mb-icon-hover',
																				'csspseudo' => 'hover',
									),
							  "icon_position"	=> array('default' => 'left',
							  						 'css' => 'text-align',
							  						 'csspart' => 'mb-icon,mb-icon-hover'),

							  'icon_padding_top' => array('default' => '13px',
							  						  'css' => 'padding-top',
							  						  'csspart' => 'mb-icon,mb-icon-hover'),

							  'icon_padding_right' => array('default' => '6px',
							  						  'css' => 'padding-right',
							  						  'csspart' => 'mb-icon,mb-icon-hover'),

							  'icon_padding_bottom' => array('default' => '0px',
							  						  'css' => 'padding-bottom',
							  						  'csspart' => 'mb-icon,mb-icon-hover'),

							  'icon_padding_left' => array('default' => '18px',
							  						  'css' => 'padding-left',
							  						  'csspart' => 'mb-icon,mb-icon-hover'),
								'background_position_horizontal' => array('default' => '50'),
								'background_position_vertical' => array('default' => '50'),

									"icon_hover_id" => array('default' => '',
								  					 'css' => ''
								  					),
								  "icon_hover_url" => 	array('default' => '',
								  						  'css' => ''),
									'icon_hover_size' => array('default' => '',
								),
									'fa_icon_hover_value' => array('default' => ''),
									'bind_to_text' => array('default' => 0),
								  /*"icon_hover_alt" => 	array('default' => '',
								  						  'css' => ''), */
	);

	protected $faicons_array = null;
	protected $fashims_array = null;
	protected $faicons_searcharray = null;

	// variables to make life easier
	protected $is_icon = false;
	protected $is_image = false;
	protected $has_hover = false;
	protected $is_textbound = false;
	protected $is_background = false;

	public function __construct()
	{
		parent::__construct();
	}


	public function set($dataArray)
	{
		parent::set($dataArray);
		// set env variables.

		// reset all
		$this->is_icon = false;
		$this->is_image = false;
		$this->has_hover = false;
		$this->is_textbound = false;
		$this->is_background = false;

		$data = isset($this->data[$this->blockname]) ?  $this->data[$this->blockname] : array();

		if (count($data) == 0)
		 	return;

		if (isset($data['use_fa_icon']) && $data['use_fa_icon'] == 1)
		{
			$this->is_icon = true;
			if (isset($data['fa_icon_hover_value']) && strlen(trim($data['fa_icon_hover_value'])) > 0)
				$this->has_hover = true;
		}
		else {
			$this->is_image = true;
			if (isset($data['icon_hover_id']) && $data['icon_hover_id'] > 0)
				$this->has_hover = true;
		}

		if (isset($data['bind_to_text']) && $data['bind_to_text'] == 1)
		{
			$this->is_textbound = true;
		}

		if (isset($data['icon_position']) && $data['icon_position'] == 'background')
		{
			$this->is_background = true;
			$this->is_textbound = false; // can't be both true
		}


	}

	public function parse_css($css, $mode = 'normal')
	{
		$css = parent::parse_css($css, $mode);

		$data = isset($this->data[$this->blockname]) ?  $this->data[$this->blockname] : array();

		 if (count($data) == 0)
		 		return $css; // no icons present here.

		//$text_bound = ( isset($data['bind_to_text']) && $data['bind_to_text'] == 1) ? true: false;

		$css = parent::parse_css($css);
		$css["mb-icon"]["normal"]["line-height"] = "0px";  // prevent rendering bigger div than icon
		$css["mb-icon"]["normal"]["display"] = "block";
		$css['mb-icon']['normal']['background-color'] = 'unset';
		$css['mb-icon-hover']['normal']['line-height'] = '0px';
		$css['mb-icon']['normal']['box-shadow'] = 'none'; // prevent theme annoyance

		$csspart = 'mb-icon';
		$csspseudo = 'normal';

		if (! $this->is_textbound)
		{
			$css = $this->parse_rule_textalign($css, 'mb-icon', 'normal');
			$css = $this->parse_rule_textalign($css, 'mb-icon-hover', 'normal');
			$css = $this->parse_rule_textalign($css, 'mb-icon-hover', 'hover');
		}

		//$position = $data["icon_position"];
		$use_fa_icon = $data["use_fa_icon"];

		if ($this->is_background)
		{
			unset($css[$csspart][$csspseudo]["text-align"]);
			$css[$csspart][$csspseudo]['position'] = 'absolute'; // pos absolute, 100% of size.
			$css[$csspart][$csspseudo]['top'] = '0';
			$css[$csspart][$csspseudo]['bottom'] = '0';
			$css[$csspart][$csspseudo]['left'] = '0';
			$css[$csspart][$csspseudo]['right'] = '0';

			$css[$csspart][$csspseudo]['box-sizing'] = 'border-box';

			$poshor = isset($data['background_position_horizontal']) ? $data['background_position_horizontal'] : null;
			$posver = isset($data['background_position_vertical']) ? $data['background_position_horizontal'] : null;

			if (! is_null($poshor))
			{
				$css['mb-icon']['normal']['background-position'] = " $poshor% $posver%";
			}
			elseif ( is_null($poshor) && is_null($posver)  ) // compat for previous versions
			{
				$ptop = $css['mb-icon']['normal']['padding-top'];
				$pleft = $css['mb-icon']['normal']['padding-left'];

				if ($ptop > 0 && $pleft > 0)
				{
					$css['mb-icon']['normal']['background-position'] = $pleft  . ' ' .  $ptop. '';
				}
			}
			$css['mb-icon']['normal']['background-repeat'] = 'no-repeat';
 		}

		if ($this->is_textbound)
		{
			$position = $data["icon_position"];
			$display_block = ($position == 'top' || $position == 'bottom') ? 'block' : 'inline-block';

			$css['mb-icon']['normal']['display'] = $display_block;
			$css['mb-icon']['normal']['vertical-align'] = 'middle';

			$css['mb-icon-hover']['normal']['vertical-align'] = 'middle';
			$css['mb-icon-hover']['normal']['display'] = $display_block;
		}

		if ($this->is_icon)
		{
			$css = $this->parseIconCSS($css);
		}
		else {
			$css = $this->parseImageCSS($css);
		}

		return $css;
	}

	protected function parseIconCSS($css)
	{
			$data = isset($this->data[$this->blockname]) ?  $this->data[$this->blockname] : array();

			$icon_size = isset($data['fa_icon_size']) ? $data['fa_icon_size'] : null;

			if ($this->is_background)
			{
				$icon_color = maxBlocks::getColorValue('icon_color');
				$svg = $this->getFASVG(array('icon' => $data['fa_icon_value'],
																			'size' => $icon_size,
																			'color' => $icon_color,
																		));
				$svg_hover = $this->getFASVG(array('icon' => $data['fa_icon_value'],
																			'size' => $icon_size,
																			'color' => maxBlocks::getColorValue('icon_color_hover'),
																		));

				 $css['mb-icon']['normal']['background-image'] = 'url(data:image/svg+xml;charset=utf-8,' . rawurlencode($svg) . ')';
				 $css['mb-icon']['normal']['background-repeat'] = 'no-repeat';
				 $css['mb-icon']['normal']['background-size'] = $icon_size . 'px ' . $icon_size . 'px ';

				 if ($this->has_hover)
				 {
					 $fa_icon_hover_value = $data['fa_icon_hover_value'];
					 $svg_hover = $this->getFASVG(array('icon' => $fa_icon_hover_value,
					 															'size' => $icon_size,
					 															'color' => maxBlocks::getColorValue('icon_color_hover'),
					 ));
				//	 $css['mb-icon-hover']['normal']['position'] = 'absolute';
				 }

				 $css['mb-icon']['hover']['background-image'] = 'url(data:image/svg+xml;charset=utf-8,' . rawurlencode($svg_hover) . ')';

			}
			elseif ($this->has_hover) // hover but not background
			{
				$css['mb-icon']['hover']['display'] = 'none'; //hide normal on hover
				$css['mb-icon-hover']['normal']['display'] = 'none'; // no display on normal

				$position = $data["icon_position"];
				$display_block = ($position == 'top' || $position == 'bottom') ? 'block' : 'inline-block';

				$css['mb-icon-hover']['hover']['display'] = $display_block; // display hover on hover

			}
			elseif (! $this->has_hover) // hover color is put on element that does not exist.
			{
				$css['mb-icon']['hover']['color'] = $css['mb-icon-hover']['hover']['color'];
			}

			if ($this->is_textbound)
			{
					$css['mb-icon']['normal']['line-height'] = $icon_size . 'px';
					$css['mb-icon-hover']['normal']['line-height'] = $icon_size . 'px';
			}

			return $css;
	}

	protected function parseImageCSS($css)
	{
			$data = isset($this->data[$this->blockname]) ?  $this->data[$this->blockname] : array();
			$icon_hover_id = isset($data['icon_hover_id']) ? $data['icon_hover_id'] : -1;
			$position = $data["icon_position"];

			if ($this->is_background)
			{
				$css['mb-icon']['normal']['background-image'] = 'url(' . $data["icon_url"] . ')';
				$css['mb-icon']['normal']['background-repeat'] = 'no-repeat';
			//	$css['mb-icon']['normal']['background-position'] = $data["icon_padding_left"] . 'px ' . $data["icon_padding_top"] . 'px' ;
				$css['mb-icon']['normal']['padding'] = '0';

				if ($this->has_hover)
				{
					$css['mb-icon']['hover']['background-image'] = 'url(' . $data['icon_hover_url'] . ')'; // display hover URL
				}
			}
			elseif ($this->has_hover) // hover, but not background
			{
				$css['mb-icon']['hover']['display'] = 'none'; //hide normal on hover
				$css['mb-icon-hover']['normal']['display'] = 'none'; // no display on normal
				$css['mb-icon-hover']['hover']['display'] = 'block'; // display hover on hover
			}

			if ($this->is_textbound)
			{
				$position = $data["icon_position"];
				$display_block = ($position == 'top' || $position == 'bottom') ? 'block' : 'inline-block';
				$css['mb-icon-hover']['hover']['display'] = $display_block;
			}

			$css['mb-img']['normal']['box-shadow'] = 'none';
			return $css;
	}

 	public function parse_button($domObj, $mode = 'normal')
 	{
 		$data = $this->data[$this->blockname];

 		$icon_id = isset($data['icon_id']) ? $data['icon_id'] : 0;
		$icon_url = isset($data["icon_url"]) ? $data['icon_url'] : '';
		//$use_fa_icon= isset($data["use_fa_icon"]) ? $data['use_fa_icon'] : 0 ;
		$position = isset($data["icon_position"]) ? $data['icon_position'] : '';
		//$is_background = ($position == 'background') ? true : false;

		$icon_hover_id = isset($data['icon_hover_id']) ? $data['icon_hover_id'] : 0;
		$icon_hover_url = isset($data['icon_hover_url']) ? $data['icon_hover_url'] : '';

		//$bind_to_text = isset($data['bind_to_text']) ? $data['bind_to_text'] : false;

		$icon_post = null;
		if ( $icon_id > 0)
		{
			$icon_post = get_post($icon_id);
		}

		if ($icon_id == 0 && $icon_url == '' && (! $this->is_icon ) )
				return $domObj; // no icon

		if ($this->is_textbound)
			$anchor = $domObj->find('.mb-text', 0);
		else
			$anchor = $domObj->find("a",0);

		$anchor_text = '';


		if ($this->is_icon && ! $this->is_background)
		{
				$has_normal = (isset($data['fa_icon_value']) && strlen(trim($data['fa_icon_value'])) > 0) ? true : false;
				$has_hover = (isset($data['fa_icon_hover_value']) && strlen(trim($data['fa_icon_hover_value'])) > 0) ? true : false;

			if (! $has_normal && ! $has_hover)
			 	return $domObj; // still no icon

			// icon size, null should trigger default in FASVG function
			$icon_size = isset($data['fa_icon_size']) ? $data['fa_icon_size'] : null;

			$svg = $this->getFASVG(array('icon' => $data['fa_icon_value'],
																	 'size' => $icon_size,
																	));
			if ($this->has_hover)
			{
			$svg_hover = $this->getFASVG(array('icon' => $data['fa_icon_hover_value'],
																		'size' => $icon_size,
																	));
			}
			else {
				$svg_hover = $svg;
			}
			if ($has_normal)
				$anchor_text = '<span class="mb-icon">' . $svg . '</span>';

				//$anchor_text = '<span class="mb-icon">' . $svg. '<i class=" ' . $data["fa_icon_value"] . '"></i></span>';

			if ($has_hover)
				$anchor_text .= '<span class="mb-icon-hover">' . $svg_hover . '</span>';

		}
		elseif($icon_id > 0 && ! is_null($icon_post) && ! $this->is_background)
		{
			$icon_title = $icon_post->post_title;
			$icon_alt = get_post_meta( $icon_id, '_wp_attachment_image_alt', true );
			$icon_size = isset($data['icon_size']) ? $data['icon_size'] : '';

			$data = wp_get_attachment_image_src($icon_id, $icon_size);
			//$src = $data[0];
			$width = $data[1];
			$height = $data[2];

			$anchor_text = '<span class="mb-icon  "><img class="mb-img" src="' . $icon_url . '"';

			if ($icon_alt != '')
			 $anchor_text .= ' alt="' . $icon_alt . '"';

			if ($icon_title != '')
			 $anchor_text .= ' title="' . $icon_title . '"';

			$anchor_text .= ' width="' . $width . '" height="' . $height . '" border="0" /></span>';

			if($this->has_hover)
			{
					//$hover_post = get_post($icon_hover_id);
					$icon_alt = get_post_meta( $icon_hover_id, '_wp_attachment_image_alt', true );
					$icon_hover_size = isset($data['icon_hover_size']) ? $data['icon_hover_size'] : '';

					$data = wp_get_attachment_image_src($icon_hover_id,$icon_hover_size);
					//$src = $data[0];
					$width = $data[1];
					$height = $data[2];

					$anchor_text .= '<span class="mb-icon-hover"><img class="mb-img" src="' . $icon_hover_url . '"';

					if ($icon_alt != '')
					 $anchor_text .= ' alt="' . $icon_alt . '"';

					if ($icon_title != '')
					 $anchor_text .= ' title="' . $icon_title . '"';

					$anchor_text .= ' width="' . $width . '" height="' . $height . '" border="0" /></span>';
			}
		}
		elseif (! $this->is_background) // Required for importing button packs
		{
			$data['icon_alt'] = isset($data['icon_alt']) ? $data['icon_alt'] : ''; // icon alt may not be set on older buttons
			$anchor_text = '<span class="mb-icon"><img class="mb-img" src="' . $data["icon_url"] . '" alt="' . $data["icon_alt"] . '" border="0" /></span>' ;
		}

		if ($this->is_background)
		{
			$anchor_text = '<span class="mb-icon"></span>';
		}

		if ($this->is_textbound) // in textbound, put right on the right side of element
		{
				if ( $position == 'bottom' || $position == 'right')
				{
					$anchor->innertext = $anchor->innertext . $anchor_text;
				}
				else {
					$anchor->innertext = $anchor_text . $anchor->innertext;
				}
		}
		else {
			if ($position == 'bottom')
				$anchor->innertext = $anchor->innertext . $anchor_text;
			else {
				$anchor->innertext = $anchor_text . $anchor->innertext;
			}
		}
		$newhtml = $domObj->save();

		$domObj =  new simple_html_dom();
		$domObj->load($newhtml);

 		return $domObj;
 	}



	/* Parse text align rule. Used for alignment of images and icons. In seperate function since it needs to process different pseudo's. Parses the POSITION*/
	public function parse_rule_textalign($css, $csspart, $csspseudo)
	{

		if (isset($css[$csspart][$csspseudo]["text-align"]) && $css[$csspart][$csspseudo]["text-align"] != '')
		{
			switch( $css[$csspart][$csspseudo]["text-align"])
			{
				case "left":
					$css[$csspart][$csspseudo]["float"] = 'left';
					unset($css[$csspart][$csspseudo]["text-align"]);
				break;
				case "right":
					$css[$csspart][$csspseudo]["float"] = 'right';
					unset($css[$csspart][$csspseudo]["text-align"]);

				break;
				case "top":
				case "bottom":
					$css[$csspart][$csspseudo]["text-align"] = 'center';
				break;
			}
		}

		return $css;
	}


	public function map_fields($map)
	{
		//$map["url"]["attr"] = "href";
		$map["icon_url"]["func"] = "window.maxFoundry.maxIcons.updateIcon";
		$map["fa_icon_size"]["func"] = "window.maxFoundry.maxIcons.updateIcon";
		$map["icon_position"]["func"] = "window.maxFoundry.maxIcons.updateIcon";
		$map["icon_padding_top"]["func"] = "window.maxFoundry.maxIcons.updateIcon";
		$map["icon_padding_bottom"]["func"] = "window.maxFoundry.maxIcons.updateIcon";
		$map["icon_padding_left"]["func"] = "window.maxFoundry.maxIcons.updateIcon";
		$map["icon_padding_right"]["func"] = "window.maxFoundry.maxIcons.updateIcon";

		$map["background_position_horizontal"]["func"] = "window.maxFoundry.maxIcons.updateIcon";
		$map["background_position_vertical"]["func"] = "window.maxFoundry.maxIcons.updateIcon";
		$map["bind_to_text"]["func"] = "window.maxFoundry.maxIcons.updateIcon";

		return $map;
	}

	public function getFaCategories()
	{
		$dir =  MB()->get_plugin_path() . "assets/libraries/font-awesome-5";

		$cat_json = file_get_contents($dir . '/categories.json');
		$cat_array = json_decode($cat_json, true);

		// remove some categories, because too little icons for a whole subject.
		if (isset($cat_array['gender']))
		{
			 $cat_array['users-people']['icons'] = array_merge($cat_array['users-people']['icons'], $cat_array['gender']['icons']);
			 unset($cat_array['gender']);
		}

		ksort($cat_array);
		return $cat_array;
	}

	public function getFAIcons()
	{
		if (! is_null($this->faicons_array) )
		{
			return $this->faicons_array;
		}


		$dir =  MB()->get_plugin_path() . "assets/libraries/font-awesome-5";

		$icon_list = array();

		$icon_json = file_get_contents($dir . '/icons_processed.json');
		$file_icon_array = json_decode($icon_json, true);

		$icon_array = array();
		$search_array = array();

		foreach($file_icon_array as $name => $data)
		{

				$styles = $data['styles']; // [brands, solid, regular]
				$nice_name = $data['label'];

				$style_tag = '';
				foreach($styles as $style)
				{
					 switch ($style)
					 {
						 case 'brands':
								$style_tag = 'fab';
								$category = 'brands';
						 break;
						 case 'solid':
								$style_tag = 'fas';
								$category = 'solid';
						 break;
						 case 'regular':
								$style_tag = 'far';
								$category = 'regular';
						 break;
					 }
					 $full_icon = $style_tag . ' fa-' . $name;
					 $icon_array[$name][$style]['icon']  = $full_icon;
					 $icon_array[$name][$style]['name'] = $name;
					 $icon_array[$name][$style]['category'] = $category;
					 $icon_array[$name][$style]['nice_name'] = $nice_name;
					 //$icon_array[$name][$style]['svg'] =  $this->getFASVG(array('icon' => $full_icon ) );
					 $search_array[$full_icon]['path'] = $data['svg'][$style]['path'];
					 $search_array[$full_icon]['viewbox'] = $data['svg'][$style]['viewBox'];
				}

		}

	//	$this->faicons_array = $icon_array;
		$this->faicons_searcharray = $search_array;
		return $icon_array;

	}


	public function getFASVG($args)
	{
			$defaults = array('icon'=> null,
												'title' => null,
												'size' => maxUtils::strip_px($this->fields['fa_icon_size']['default']),
												'color' => 'currentColor',
									);
			$args = wp_parse_args($args, $defaults);

			if (is_null($args['icon']) || strlen($args['icon']) <= 0)
			{
				return '';
			}

			if (is_null ($this->faicons_searcharray))
			{
				$this->getFAIcons();
			}

			$icons = $this->faicons_searcharray;

			$icon = $args['icon'];
			$size = $args['size'];
			$color = $args['color'];

			if (! isset($icons[$icon]))
			{
				$icon = $this->checkShims($icon);
			}

			$faicon_svg = $icons[$icon]['path'];
			$faicon_viewbox = implode(' ', $icons[$icon]['viewbox']);

			$svg = '<svg class="svg-mbp-fa" width="' . $size . '" height="' . $size . '" aria-hidden="true" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="' . $faicon_viewbox . '">
			<path fill="' . $color . '" d="' . $faicon_svg . '"></path></svg>';

			return $svg;
	}

	public function checkShims($icon)
	{

		if (is_null($this->fashims_array ) )
		{
 			$conversion_path = MB()->get_plugin_path() . '/assets/libraries/font-awesome-5/shims.json';
			$this->fashims_array = json_decode(file_get_contents($conversion_path), ARRAY_A);
		}
		$old_value = $icon;

		if (strpos($old_value, 'fa-') == 0)
		{
			$old_value = str_replace('fa-','', $old_value);
		}
		elseif (strpos($old_value, 'fa') == 0 )
		{
			$old_value = str_replace('fa', '', $old_value);
		}


		$old_value = trim($old_value);

		return maxInstall::searchNewFA($old_value, $this->fashims_array);
	}


	/** Show the image + upload + everything for a certain field id **/
	protected function imageUploader($args = array() )
	{
			$default_args = array(
					'id' => 'icon',
					'hide' => false,
					'show_hover_link' => true,
					'conditional' => false,
					'label' => __('Image', 'maxbuttons-pro'),
			);

			$args = wp_parse_args($args, $default_args);

			$admin = MB()->getClass('admin');

			$id = $args['id'];

			$icon_id =	maxBlocks::getValue($id . '_id');
			$icon_url = maxBlocks::getValue($id . '_url');
			$icon_alt = maxBlocks::getValue($id . '_alt');
			$icon_size = maxBlocks::getValue($id . '_size');

		//**Icon section.
			$iconid = new maxField('hidden');
			$iconid->id = $id . '_id';
			$iconid->name = $iconid->id;
			$iconid->value = $icon_id;

			$admin->addField($iconid, '', '');

			$iconurl = new maxField('hidden');
			$iconurl->id = $id . '_url';
			$iconurl->name = $iconurl->id;
			$iconurl->value = $icon_url;

			$admin->addField($iconurl, '','');

			$iconsize = new maxField('hidden');
			$iconsize->id = $id . '_size';
			$iconsize->name = $iconsize->id;
			$iconsize->value = $icon_size;

			$admin->addField($iconsize, '','');


			$iconpreview = new maxField('generic');
			$iconpreview->id = $id . '_option_preview';
			if ($args['hide'])
				$iconpreview->main_class = 'option hidden';
			$iconpreview->label =$args['label'];
			$iconpreview->name = $iconpreview->id;
			$iconpreview->start_conditional = $args['conditional'];
		//	$iconpreview->main_class  = 'option icon_preview';

			$content = '';
			$content .= '<span class="image_' . $id . '_preview the_icon_preview non-fa">';
			if (isset($icon_url) && $icon_url != '')
			{
				$content .= '<img src="' . $icon_url . '">';
			}
			$content .= '<span class="remove_icon dashicons dashicons-dismiss" data-key="' . $id . '"></span></span>';



			if ($icon_id > 0)
			{
				$data = get_post($icon_id);
				$icon_title = $data->post_title;
				$icon_alt = get_post_meta( $icon_id, '_wp_attachment_image_alt', true );

				$data = wp_get_attachment_image_src($icon_id, $icon_size);

				$filename = basename($data[0]);
				$width = $data[1];
				$height = $data[2];
			}
			else {
					$filename = $icon_alt = $icon_title = '';
			}
				$content .= "<span class='" . $id . "_data the_icon_data'>";
				$content .= "<label>" .  __("File Name","maxbuttons-pro") . "</label>
									<span class='filename'>" .  $filename . "</span>
									<label>" .  __("Alt","maxbuttons-pro") . "</label>
									<span class='alt'>" . $icon_alt . "</span>
									<label>" .  __("Title","maxbuttons-pro") . "</label>
									<span class='atttitle'>" .  $icon_title . "</span>
									</span>";


			$iconpreview->content = $content;
			$admin->addField($iconpreview, 'start', 'end');

			$image_button = new maxField('generic');
			$image_button->label = '&nbsp;';
			$image_button->id = $id . '_image_button';
			$image_button->name = $image_button->id;
			$image_button->start_conditional = $args['conditional'] ;

			if ($args['hide'])
				$image_button->main_class = 'option hidden';

			$image_button->content = '<input type="button" class="button" id="image_' . $id . '_button" name="image_' . $id . '_button"
			data-uploader_title="' . __('Select an Image', 'maxbuttons-pro') . '"
			data-uploader_button_text="' .  __('Use Image', 'maxbuttons-pro') . '"
			 value="' .  __('Select...', 'maxbuttons-pro') . '" />';

			 $end = $args['show_hover_link'] ? '' : 'end';
			 $admin->addField($image_button, 'start', $end);

			 if ($args['show_hover_link'])
		 	 {
			 	$hover_link = new maxField('generic');
				$hover_link->id = $id . '_hover_link';
				$hover_link->name = $hover_link->id;
			 	$hover_link->content = "<p class='add_hover_image'>
			 								 <a href='javascript:void(0);' id='add_hover_image'>" . __("Add a different Hover Image", 'maxbuttons-pro') . "</a>
			 							 </p>";

			 						 $admin->addField($hover_link, '', 'end');
		   }

	}

	public function admin_fields()
	{

		$data = $this->data[$this->blockname];
$admin = MB()->getClass('admin');

$color_copy_self = __("Replace color from other field", "maxbuttons");
$color_copy_move  = __("Copy Color to other field", "maxbuttons");


//$icon_list = $this->getFAIcons();
//$categories = $this->getFACategories();


?>
     <div class='maxmodal-data' id='view-icons' data-load='window.maxFoundry.maxIcons.loadIcons' >
        	<span class='title'> <?php _e("Icons and Images","maxbuttons-pro"); ?></span>
        	<span class='content'>
           		<div class='icon-search'>
           			<span class='label'> <?php _e('Keywords', 'maxbuttons-pro'); ?> </span>
								<input type='text' class='search-input' placeholder='<?php echo __("Search",'maxbuttons-pro'); ?>' />

								<div class='category-filter'>
									<ul class='categories'>
									<?php
									?>
									</ul>
									<ul class='styles'>
										<li data-category='regular'>Regular</li>
										<li data-category='solid'>Solid</li>
										<li data-category='brands'>Brands</li>
									</ul>
           				<span class='spinner'></span>
								</div>

           		</div>

            	<div class="font-awesome">
               			    <ul class="icon-list">
                        </ul>
                    </div>
             </span>
			<div class='controls'><p><button type='button' class='button-primary modal_close'><?php _e("Close","maxbuttons-pro"); ?></button>
			</p></div>
      </div>

		<div class="option-container">

				<div class="title"><?php _e('Icons and images', 'maxbuttons-pro') ?></div>
				<div class="inside">
<?php
		$use_fa = new maxField('switch');
		$use_fa->name = 'use_fa_icon';
		$use_fa->id = $use_fa->name;
		$use_fa->value = '1';
		$use_fa->checked = checked(maxBlocks::getValue('use_fa_icon'), 1, false);
		$use_fa->inputclass = 'fa_switch';
		$use_fa->label = __('Use Image', 'maxbuttons-pro');

//		$use_fa->output('start','');

		$admin->addField($use_fa, 'start', '');

		$ispacer = new maxfield('spacer');
		$ispacer->label = __('Use Font Awesome Icon','maxbuttons-pro');
	//	$ispacer->output('','end');

		$admin->addField($ispacer, '','end');

		$condition = array('target' => 'use_fa_icon', 'values' => 'checked');
		$faicon_active = htmlentities(json_encode($condition));

		$condition = array('target' => 'use_fa_icon', 'values' => 'unchecked');
		$image_active = htmlentities(json_encode($condition));

		$fa_icon_value = maxBlocks::getValue('fa_icon_value');
		$fa_icon_hover_value = maxBlocks::getValue('fa_icon_hover_value');

		$svg_icon = $this->getFASVG(array('icon' => $fa_icon_value));
		$mode = __('Normal', 'maxbuttons-pro');

		$show_hover = false;
		$svg_hover_icon = '';
		if ($fa_icon_hover_value !== '')
		{
				$show_hover = true;
				$svg_hover_icon = $this->getFASVG(array('icon' => $fa_icon_hover_value));
		}


/*** FONT AWESOME BUTTONS **/
		$fa_icon_preview = new maxField('generic');
		$fa_icon_preview->id = 'faicon_preview';
		$fa_icon_preview->label = '&nbsp;';
		$fa_icon_preview->name = $fa_icon_preview->id;
		$fa_icon_preview->content = "
                            <div class='fontawesome-only font-awesome-preview'>
															<span class='mode'>$mode</span>

															<span class='the-icon normal'>
								            		$svg_icon
															</span>
                            <div class='remove_fa_icon' data-mode='normal'>" . __('Remove','maxbuttons-pro') . "</div>
                            </div>";
    $fa_icon_preview->start_conditional = $faicon_active;

		$admin->addField($fa_icon_preview, 'start', '');

		// value fields
		$fa_icon = new maxField('hidden');
		$fa_icon->id = 'fa_icon_value';
		$fa_icon->name = $fa_icon->id;
		$fa_icon->value = $fa_icon_value;

		$admin->addField($fa_icon,'','');

		$mode = __('Hover', 'maxbuttons-pro');
		$show = (! $show_hover ) ? ' hidden ' : '';

		$content = "<div class='fontawesome-only font-awesome-preview $show'>
									<span class='mode'>$mode</span>
												<span class='the-icon hover'>
														$svg_hover_icon
														</span>
		             <div class='remove_fa_icon' data-mode='hover'>" . __('Remove','maxbuttons-pro') . "</div>
		             </div>";

		$icon_hover_preview = clone $fa_icon_preview;
		$icon_hover_preview->id = 'faicon_hover_preview';
		$icon_hover_preview->name = $icon_hover_preview->id;
		$icon_hover_preview->content = $content;
		$admin->addField($icon_hover_preview, '');

		$icon_hover = clone $fa_icon;
		$icon_hover->id = 'fa_icon_hover_value';
		$icon_hover->name = $icon_hover->id;
		$icon_hover->value = $fa_icon_hover_value;

		$admin->addField($icon_hover, '', 'end');

 // Select icon buttons
		$fa_button = new maxField('button');
		$fa_button->label = '&nbsp;';
		$fa_button->button_label = __("Select","maxbuttons-pro");
		$fa_button->name =  'select_icon';
		$fa_button->id = $fa_button->name;
		$fa_button->modal = 'view-icons';
		$fa_button->inputclass = 'maxmodal';
		$fa_button->start_conditional = $faicon_active;

		$admin->addField($fa_button,'start', '');

		$fa_hover_button = clone $fa_button;
		$fa_hover_button->id = 'select_hover_icon';
		$fa_hover_button->name = $fa_hover_button->id;
		if (! $show_hover)
		{
			$fa_hover_button->inputclass = 'option maxmodal hidden';
			$admin->addField($fa_hover_button);
		}
		else {
			$admin->addField($fa_hover_button,'','end');
		}

		if (! $show_hover)
		{
			$show_hover = new maxField('generic');
			$show_hover->content = "<div class='show_fa_hover'><a href='javascript:void(0);' id='add_hover_icon'>Add Different Hover Icon</a></div>";
			$show_hover->name = 'show_hover';
			$show_hover->id = $show_hover->name;

			$admin->addField($show_hover, '', 'end');
		}

		// Icon size
		$icon_size = new maxField('number');
		$icon_size->id = 'fa_icon_size';
		$icon_size->name = $icon_size->id;
		$icon_size->value = maxUtils::strip_px(maxBlocks::getValue('fa_icon_size'));
		$icon_size->label = __('Icon Size', 'maxbuttons-pro');
		$icon_size->min = 5;
		$icon_size->inputclass = 'tiny';
		$icon_size->start_conditional = $faicon_active;

		$admin->addField($icon_size, 'start','end');

		// Icon Color
		$color = new maxField('color');
		$color->id = 'icon_color';
		$color->name = $color->id;
		$color->value = maxBlocks::getColorValue('icon_color');
		$color->label = __('Icon Color','maxbuttons');
		$color->copycolor = true;
		$color->bindto ='icon_color_hover';
		$color->copypos = 'right';
		$color->start_conditional = $faicon_active;
    $color->left_title = $color_copy_self;
    $color->right_title = $color_copy_move;
		//$color->output('start');

		$admin->addField($color, 'start', '');

		// Icon Color Hover
		$hcolor = new maxField('color');
		$hcolor->id = 'icon_color_hover';
		$hcolor->name = $hcolor->id;
		$hcolor->value = maxBlocks::getColorValue('icon_color_hover');
		$hcolor->label = __('Hover','maxbuttons');
		$hcolor->copycolor = true;
		$hcolor->bindto ='icon_color';
		$hcolor->copypos = 'left';
    $hcolor->left_title = $color_copy_move;
    $hcolor->right_title = $color_copy_self;
		//$hcolor->output('', 'end');

		$admin->addField($hcolor, '','end');

		$hover_id = maxBlocks::getValue('icon_hover_id');
		if ($hover_id > 0)
		{
			$show_hover_link = false;
			$hide = false;
		}
		else {
			$show_hover_link = true;
			$hide = true;
		}

	// 	$show_hover_link = false; // offline for now.
		$this->imageUploader(
					array('show_hover_link' => $show_hover_link,
								'conditional' => $image_active,
		));
		$this->imageUploader(array(
								'id' => 'icon_hover',
								'show_hover_link' => false,
								'hide' => $hide,  // hide by default until there is a value for this field.
								'conditional' => $image_active,
								'label' => __('Image Hover', 'maxbuttons-pro'),
							));


 /** ICON HOVER **/

?>


					<?php

				$icon_positions = array(
					'left' => __('Left of text','maxbuttons-pro'),
					'right' => __('Right of text', 'maxbuttons-pro'),
					'top' => __('Top of text','maxbuttons-pro'),
					'bottom' => __('Bottom of text','maxbuttons-pro'),
					'background' => __('As background','maxbuttons-pro'),
			);

				$position = new maxField('option_select');
				$position->label = __('Position','maxbuttons-pro');
				$position->id = 'icon_position';
				$position->name = $position->id;
				$position->selected = maxBlocks::getValue($position->id);
				//$position->content = maxUtils::selectify($position->name, $icon_positions, $position->value);
				$position->options = $icon_positions;
				//$position->output('start','end');

				$admin->addField($position, 'start', 'end');

				/** Left, right top padding settings **/
				$condition = array('target' => 'icon_position', 'values' => array('left','right', 'top', 'bottom') );
						$padding_conditional = htmlentities(json_encode($condition));

				$icon_url = MB()->get_plugin_url() . 'images/icons/' ;

				$ptop = new maxField('number');
				$ptop->id = 'icon_padding_top';
				$ptop->name = $ptop->id;
				$ptop->min = 0;
			 	$ptop->inputclass = 'tiny';
				$ptop->value = maxUtils::strip_px(maxBlocks::getValue('icon_padding_top') );
				$ptop->label = __("Padding","maxbuttons-pro");
	 			$ptop->before_input = '<img src="' . $icon_url . 'p_top.png" title="' . __("Padding Top","maxbuttons") . '" >';
				$ptop->start_conditional = 	$padding_conditional;
				$admin->addField($ptop, 'start');

		 		$pright = new maxField('number');
		 		$pright->id = 'icon_padding_right';
		 		$pright->name = $pright->id;
				$pright->min = 0;
		 		$pright->inputclass = 'tiny';
		 		$pright->before_input = '<img src="' . $icon_url . 'p_right.png" class="icon padding" title="' . __("Padding Right","maxbuttons") . '" >';
		 		$pright->value = maxUtils::strip_px(maxBlocks::getValue('icon_padding_right'));
				$admin->addField($pright);

		 		$pbottom = new maxField('number');
		 		$pbottom->id = 'icon_padding_bottom';
		 		$pbottom->name = $pbottom->id;
				$pbottom->min = 0;
		 		$pbottom->inputclass = 'tiny';
		 		$pbottom->before_input = '<img src="' . $icon_url . 'p_bottom.png" class="icon padding" title="' . __("Padding Bottom","maxbuttons") . '" >';
		 		$pbottom->value = maxUtils::strip_px(maxBlocks::getValue('icon_padding_bottom'));
				$admin->addField($pbottom);

			 	$pleft = new maxField('number');
		 		$pleft->id = 'icon_padding_left';
		 		$pleft->name = $pleft->id;
				$pleft->min = 0;
		 		$pleft->inputclass = 'tiny';
		 		$pleft->before_input = '<img src="' . $icon_url . 'p_left.png" class="icon padding" title="' . __("Padding Left","maxbuttons") . '" >';
		 		$pleft->value = maxUtils::strip_px(maxBlocks::getValue('icon_padding_left'));
   		  $admin->addField($pleft, '', 'end');

				/** Background settings **/
				$condition = array('target' => 'icon_position', 'values' => array('background') );
				$background_conditional = htmlentities(json_encode($condition));

				$position_hor = new maxField('slider');
				$position_hor->name = 'background_position_horizontal';
				$position_hor->id = $position_hor->name;
				$position_hor->label = __('Horizontal Position', 'maxbuttons-pro');
				$position_hor->min = 0;
				$position_hor->max = 100;
				$position_hor->value = maxBlocks::getValue($position_hor->name);
				$position_hor->start_conditional = $background_conditional;
				$admin->addField($position_hor, 'start', 'end');

				$position_ver = new maxField('slider');
				$position_ver->name = 'background_position_vertical';
				$position_ver->id = $position_ver->name;
				$position_ver->label = __('Vertical Position', 'maxbuttons-pro');
				$position_ver->min = 0;
				$position_ver->max = 100;
				$position_ver->value = maxBlocks::getValue($position_ver->name);
				$position_ver->start_conditional = $background_conditional;
				$admin->addField($position_ver, 'start', 'end');

				$bind = new maxField('switch');
				$bind->name = 'bind_to_text';
				$bind->id = $bind->name;
				$bind->label = __('Bind to Text', 'maxbuttons-pro');
				$bind->value = 1;
				$bind->checked = checked(maxBlocks::getValue('bind_to_text'), 1, false);
				$bind->help = __('The icon / image will be bound to the text instead of button, taking it\'s positioning from the text position', 'maxbuttons-pro');
				$bind->start_conditional = $padding_conditional;
				$admin->addField($bind, 'start', 'end');

				$admin->display_fields();

				?>
				</div> <!-- inside -->
			</div>

<?php
	} // admin_fields
} // class

?>
