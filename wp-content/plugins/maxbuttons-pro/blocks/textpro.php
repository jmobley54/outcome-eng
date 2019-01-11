<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');
$blockClass["text"] = "textBlockPro";

use \simple_html_dom as simple_html_dom;

class textBlockPro extends textBlock
{
	protected $blockname = "text";
	//protected $webfonts = array();

	protected $childfields = array(
						"text2" => array("default" => '',
										 "csspart" => 'mb-text2',
										),
					//	"text" => array("default" => ''),
						"font2" => array("default" => "Arial",
										 "css" => "font-family",
										 "csspart" => "mb-text2",
											  ),

						"font_size2" => array("default" => "15px",
											  "css" => "font-size",
											  "csspart" => "mb-text2"),

						"font_style2" => array("default" => "normal",
											  "css" => "font-style",
											  "csspart" => "mb-text2"),

						"font_weight2" => array("default" => "normal",
											  "css" => "font-weight",
											  "csspart" => "mb-text2"),

						"padding_top2" => array("default" => "15px",
											   "css" => "padding-top",
											   "csspart" => "mb-text2"),
						"padding_right2" => array("default" => "25px",
												"css" => "padding-right",
											   "csspart" => "mb-text2"),
						"padding_bottom2" => array("default" => "15px",
												"css" => "padding-bottom",
											   "csspart" => "mb-text2"),
						"padding_left2" => array("default" => "25px",
												"css" => "padding-left",
											   "csspart" => "mb-text2"),
						/*"text_align" => array("default" => "",
										 "css" => "text-align",
										 "csspart" => "mb-text",
										 ), */
						"text_align2" => array("default" => "",
										 "css" => "text-align",
										 "csspart" => "mb-text2",
										 ),
						'text_underline' => array('default' => '',
											'css' => 'text-decoration',
											'csspart' => 'mb-text'),
						'text_underline2' => array('default' => '',
											'css' => 'text-decoration',
											'csspart' => 'mb-text2',
										),
						);

	static private $actions_loaded = false;

	public function __construct()
	{
		$this->fields = array_merge($this->fields, $this->childfields);
 		$this->fields["text_shadow_offset_left"]["csspart"] .= ",mb-text2";
 		$this->fields["text_shadow_offset_top"]["csspart"] .= ",mb-text2";
 		$this->fields["text_shadow_width"]["csspart"] .= ",mb-text2";
		parent::__construct();

		// workaround to the changing of the classes - run once
		if (! static::$actions_loaded)
		{
			add_action('mb/editor/afterfield/text_color_hover', array($this, 'pro_text'));  // into basic
			add_action('mb/editor/afterfield/check_fstyle', array($this, 'pro_underline')); // basic, adds Underline

			add_action('mb/editor/afterfield/nofollow', array($this, 'fontManagerButton'));
			add_filter('mb/button/compiledcss', array(maxUtils::namespaceit('maxProUtils'), 'addWebFonts'));	// Now needed for style inline.

			add_action('mb/editor/afterfield/shortcode_text', array($this, 'pro_shortcode'));
			add_filter('mb/media/shortcode_data', array($this, 'shortcodeData'));
			static::$actions_loaded = true;
		}
	}

	public function map_fields($map)
	{
		$map = parent::map_fields($map);

		$map["text2"]["func"] = "updateAnchorText";
		$map["font"]["func"] = "updateFont";
		$map["font2"]["func"] = "updateFont";

		return $map;

	}


	public function parse_css($css, $mode = 'normal') {
	$data = $this->data[$this->blockname];

	$css = parent::parse_css($css, $mode);

	$css["mb-text2"]["normal"]["line-height"] = "1em";
	$css["mb-text2"]["normal"]["box-sizing"] = "border-box";  // default.

	$css["mb-text"]["normal"]["position"] = "relative";
	$css["mb-text2"]["normal"]["position"] = "relative";
	$css["mb-text2"]["normal"]["background-color"] = 'unset';

	if ($mode == 'editor' && isset($data["text2"]) && $data["text2"] == '')
		$css["mb-text2"]["normal"]["display"] = "none";
	else
		$css["mb-text2"]["normal"]["display"] = "block";

	return $css;

	}

	public function save_fields($data, $post)
	{
		$data = parent::save_fields($data, $post);

		if (count($post) == 0)
			return $data; // do not load the whole font thing if it's not a data change ..

		maxProUtils::checkWebFonts($data[$this->blockname] );

		// always save this, even if empty to unset previous fonts.
		$data[$this->blockname]['webfonts'] = $this->webfonts;

		return $data;

	}



	public function parse_button($domObj, $mode = 'normal')
	{
		$data = $this->data[$this->blockname];

		$anchor = $domObj->find("a",0);

		$textspan = '';

		$text = (isset($data["text"])) ? $data["text"] : "";
		$text2 = (isset($data["text2"])) ? $data["text2"] : "";

		// If there is no font set in MBP and there is a theme font, MBP will try to load that as Google Font which is wrong. This prevents that.
		$nofont = (! isset($data['font']) || $data['font'] == '') ? 'data-nofont' : '';
		$nofont2 = (! isset($data['font2']) || $data['font2'] == '') ? 'data-nofont' : '';

		$style2 = '';
		if ($mode == 'editor' && $text2 == '')
			$style2 = 'style="display:none;" ';

		if (isset($data["text"]) && $data["text"] != '' || $mode == 'editor')
			$textspan .= "<span class='mb-text' $nofont>" . do_shortcode($text) . "</span>";

		if ( (isset($data["text2"]) && $data["text2"] != '') || $mode == 'editor')
			$textspan .= '<span class="mb-text2"' . $style2 . ' ' . $nofont2 . '>'. do_shortcode($text2) . '</span>';

		if ($textspan != '')
			$anchor->innertext = $textspan .  $anchor->innertext;

		if (isset($data['webfonts']))
		{
			do_action('mb-footer',$this->data['id'], $data['webfonts'], 'font');
		}

		$newhtml = $domObj->save();
		$domObj =  new simple_html_dom();
		$domObj->load($newhtml);

		return $domObj;
	}

	// adding shortcode options for PRO version
	public function pro_shortcode()
	{
		$admin = MB()->getClass('admin');

		$field_text = new maxField();
		$field_text->label = __('Button Text 2','maxbuttons');
		$field_text->name = 'shortcode_text2';
		$field_text->id = 'shortcode_text2';
		$field_text->value = maxBlocks::getValue('text2') ;


		//$field_text->output('start','end');
		$admin->addField($field_text, 'start', 'end');
	}

	public function shortcodeData($shortcode_data)
	{
			$shortcode_data[] = array(
				'name' => 'shortcode_text2',
				'original' => maxBlocks::getValue('text2'),
				'shortcode' => 'text2',
		);

			return $shortcode_data;
	}

	public function pro_underline($field)
	{
		$admin = MB()->getClass('admin');

		$fline = new maxField('checkbox');
		$fline->icon = 'dashicons-editor-underline';
		$fline->title = __("Underline",'maxbuttons');
		$fline->id = 'check_fline';
		$fline->name = 'text_underline';
		$fline->value = 'underline';
		$fline->inputclass = 'check_button icon';
		$fline->checked = checked( maxBlocks::getValue('text_underline'), 'underline', false);

		$admin->addField($fline,'', '');
	}

	public function pro_text()
	{
		$admin = MB()->getClass('admin');

		$icon_url = MB()->get_plugin_url() . 'images/icons/' ;
		// TEXT
		$field_text = new maxField();
		$field_text->label = __('Text 2','maxbuttons');
		$field_text->name = 'text2';
		$field_text->id = 'text2';
		$field_text->value = maxBlocks::getValue('text2') ;
				$field_text->help = __('Shortcode attribute: text2', 'maxbuttons');

		//$field_text->output('start','end');
		$admin->addField($field_text, 'start', 'end');

		// FONTS
		$fonts = MB()->getClass('admin')->loadFonts();

		$field_font = new maxField('generic');
		$field_font->label = __('Font 2','maxbuttons');
		$field_font->name = 'font2';
		$field_font->id = $field_font->name;
		$field_font->value= maxBlocks::getValue('font2');
		$field_font->content = maxUtils::selectify($field_font->name, $fonts, $field_font->value);

//		$field_font->output('start');
		$admin->addField($field_font, 'start');

		// FONT SIZE
		$field_size = new maxField('number');
	//	$field_size->label = '';
		$field_size->name = 'font_size2';
		$field_size->id= $field_size->name;
		$field_size->inputclass = 'tiny';
		$field_size->min = 1;
		$field_size->value = maxUtils::strip_px(maxBlocks::getValue('font_size2'));
//		$field_size->content = maxUtils::selectify($field_size->name, $maxbuttons_font_sizes, $field_size->value, '', 'small');

//		$field_size->output();
		$admin->addField($field_size);
	?>

 	<?php
 		$fweight = new maxField('checkbox');
 		$fweight->icon = 'dashicons-editor-bold';
 		$fweight->title = __("Bold",'maxbuttons');
 		$fweight->id = 'check_fweight2';
 		$fweight->name = 'font_weight2';
 		$fweight->value = 'bold';
 		$fweight->inputclass = 'check_button icon';
 		$fweight->checked = checked( maxBlocks::getValue('font_weight2'), 'bold', false);

 		//$fweight->output();
		$admin->addField($fweight, 'group_start');

 		$fstyle = new maxField('checkbox');
 		$fstyle->icon = 'dashicons-editor-italic';
 		$fstyle->title = __("Italic",'maxbuttons');
 		$fstyle->id = 'check_fstyle2';
 		$fstyle->name = 'font_style2';
 		$fstyle->value = 'italic';
 		$fstyle->inputclass = 'check_button icon';
 		$fstyle->checked = checked( maxBlocks::getValue('font_style2'), 'italic', false);

 		//$fstyle->output();
		$admin->addField($fstyle);

		$fline = new maxField('checkbox');
 		$fline->icon = 'dashicons-editor-underline';
 		$fline->title = __("Underline",'maxbuttons');
 		$fline->id = 'check_fline2';
 		$fline->name = 'text_underline2';
 		$fline->value = 'underline';
 		$fline->inputclass = 'check_button icon';
 		$fline->checked = checked( maxBlocks::getValue('text_underline2'), 'underline', false);

		$admin->addField($fline,'', 'group_end');

 		$falign_left = new maxField('radio');
 		$falign_left->icon = 'dashicons-editor-alignleft';
 		$falign_left->title = __('Align left','maxbuttons');
 		$falign_left->id = 'radio_talign_left2';
 		$falign_left->name = 'text_align2';
 		$falign_left->value = 'left';
 		$falign_left->inputclass = 'check_button icon';
 		$falign_left->checked = checked ( maxblocks::getValue('text_align2'), 'left', false);

 		//$falign_left->output();
		$admin->addField($falign_left, 'group_start');

 		$falign_center = new maxField('radio');
 		$falign_center->icon = 'dashicons-editor-aligncenter';
 		$falign_center->title = __('Align center','maxbuttons');
 		$falign_center->id = 'radio_talign_center2';
 		$falign_center->name = 'text_align2';
 		$falign_center->value = 'center';
 		$falign_center->inputclass = 'check_button icon';
 		$falign_center->checked = checked( maxblocks::getValue('text_align2'), 'center', false);

 		//$falign_center->output();
		$admin->addField($falign_center);

 		$falign_right = new maxField('radio');
 		$falign_right->icon = 'dashicons-editor-alignright';
 		$falign_right->title = __('Align right','maxbuttons');
 		$falign_right->id = 'radio_talign_right2';
 		$falign_right->name = 'text_align2';
 		$falign_right->value = 'right';
 		$falign_right->inputclass = 'check_button icon';
 		$falign_right->checked = checked( maxblocks::getValue('text_align2'), 'right', false);

 		//$falign_right->output('','end');
		$admin->addField($falign_right, '',array('group_end','end') );

 		$ptop = new maxField('number');
 		$ptop->label = __('Padding 2', 'maxbuttons');
 		$ptop->id = 'padding_top2';
 		$ptop->name = $ptop->id;
 		$ptop->inputclass = 'tiny';
 		$ptop->before_input = '<img src="' . $icon_url . 'p_top.png" title="' . __("Padding Top","maxbuttons") . '" >';
 		$ptop->value = maxUtils::strip_px(maxBlocks::getValue('padding_top2'));

 		//$ptop->output('start');
		$admin->addField($ptop, 'start');

 		$pright = new maxField('number');
 		$pright->id = 'padding_right2';
 		$pright->name = $pright->id;
 		$pright->inputclass = 'tiny';
 		$pright->before_input = '<img src="' . $icon_url . 'p_right.png" class="icon padding" title="' . __("Padding Right","maxbuttons") . '" >';
 		$pright->value = maxUtils::strip_px(maxBlocks::getValue('padding_right2'));

 		//$pright->output();
		$admin->addField($pright);

 		$pbottom = new maxField('number');
 		$pbottom->id = 'padding_bottom2';
 		$pbottom->name = $pbottom->id;
 		$pbottom->inputclass = 'tiny';
 		$pbottom->before_input = '<img src="' . $icon_url . 'p_bottom.png" class="icon padding" title="' . __("Padding Bottom","maxbuttons") . '" >';
 		$pbottom->value = maxUtils::strip_px(maxBlocks::getValue('padding_bottom'));

 		//$pbottom->output();
		$admin->addField($pbottom);

 		$pleft = new maxField('number');
 		$pleft->id = 'padding_left2';
 		$pleft->name = $pleft->id;
 		$pleft->inputclass = 'tiny';
 		$pleft->before_input = '<img src="' . $icon_url . 'p_left.png" class="icon padding" title="' . __("Padding Left","maxbuttons") . '" >';
 		$pleft->value = maxUtils::strip_px(maxBlocks::getValue('padding_left2'));

 		//$pleft->output('','end');
		$admin->addField($pleft, '', 'end');
	}

	public function fontManagerButton()
	{
		$admin = MB()->getClass('admin');

		$button = new maxField('generic');
		$button->label = '';
		$button->id = 'manage-fonts';
		$button->name = 'button_fonts';
		$button->content = '<div id="manage-fonts" class="button manage-fonts maxmodal" data-modal="add-fonts" title="' . __("Add additional fonts","maxbuttons-pro") . '" ><i class="dashicons dashicons-editor-spellcheck"></i>' . __('Font Manager', 'maxbuttons-pro') . '</div>';
// 		$button->output('start','end');
		$admin->addField($button, 'start', 'end');
	}

	public function fontManagerModal()
	{
		?>
			<div class='maxmodal-data' id='add-fonts' data-load='window.maxFoundry.maxfonts.load' data-height='90%'>
				<span class='title'><?php _e("Font Manager","maxbuttons-pro"); ?>

				</span>

				<span class='content'>
					<p><?php printf(__("Select from <strong>%s</strong> fonts you would like to use and they will show up in the dropdown list. See %s Google Fonts %s for an overview", 'maxbuttons-pro'), '<span class="fontcount"></span>', '<a href="https://fonts.google.com/" target="_blank">', '</a>'); ?></p>
					<div class="loading_overlay"></div>
					<div class='loading'>
						<img src="<?php echo MB()->get_plugin_url(true) ?>images/loading.gif">
						<span><?php _e("Loading","maxbuttons-pro"); ?></span>
					</div>
					<div class="font_manager">
						<div class='font_search'> <span><?php _e("Search","maxbuttons-pro") ?></span>
								<input type='text' name='font_search' value='' />
						</div>

						<div class="font_wrap">
							<div class='font_left'>
							<ul class='items'>

							</ul>
							</div>
							<div class='font_right '>
								<ul class='items'>

								</ul>
							</div>
						</div>
						<div class='font_example'>
							<span class='placeholder'><?php _e("Click on a font to see an example","maxbuttons-pro"); ?></span>
							<span class='example_text'><span></span><?php _e("AaBbCcDdEeFfGgEeHh") ?></span>
						</div>
					</div>
				</span>
				<div class='controls'>
					<div class='controls_inline'>
						<button type='button' name='save_fonts' class='button-primary'><?php _e("Save changes", 'maxbuttons-pro'); ?></button> &nbsp;
						<button type='button' class='button-primary modal_close'><?php _e("Close","maxbuttons-pro"); ?></button>
					</div>
				</div>
			</div> <!-- maxmodal-data -->

		<?php
	}

	public function admin_fields()
	{

		parent::admin_fields();

		$this->fontManagerModal();
		return;



	 }  // admin fields
} // class

	?>
