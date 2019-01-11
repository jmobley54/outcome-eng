<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');
$blockClass["advanced"] = "advancedBlock";
$blockOrder[80][] = "advanced";


class advancedBlock extends maxBlock
{
	protected $blockname = "advanced";
	protected $fields = array("important_css" => array("default" => "0"),
						"custom_rel" => array('default' => ''),
						"extra_classes" => array('default' => ''),
						"external_css" => array("default" => "0"),



						);

 	public function __construct()
 	{
 		parent::__construct();
 		add_filter('mb/button/rawcss', array($this, 'parse_css_advanced'), 1001, 2);  // run once
 		//add_filter('mb-css-blocks', array($this, 'preview_external_css'), 100 )
	}

	public function parse_css($css,  $mode = 'normal')
	{
		$css = parent::parse_css($css);
		$data = $this->data[$this->blockname];

		if (isset($data["important_css"]) && $data["important_css"] == 1)
		{
			$css["settings"]["important"] = 1;
		}

		return $css;
	}

	public function parse_css_advanced($css, $mode)
	{
		$data = $this->data[$this->blockname];

		if (isset($data["external_css"]) && $data["external_css"] == 1 && $mode == 'normal')
		{

			return array(
				"normal" => array(),
				"hover" => array());
		}

		return $css;
	}

 	public function parse_button($domObj, $mode = 'normal')
	{

		$data = $this->data[$this->blockname];

		$button_id = $this->data["id"];

		$anchor = $domObj->find("a",0);

 		if (isset($data["custom_rel"]) && trim($data["custom_rel"]) != '')
 		{
 			$rel = '';
 			$custom_rel = trim($data["custom_rel"]);
 			if (isset($anchor->rel) && $anchor->rel != '')
 			{
 				$anchor->rel .= ' ';
 			}

 			$anchor->rel .= $custom_rel;
 		}

 		if (isset($data["external_css"]) && $data["external_css"] == 1)
 		{
  			$anchor->class .= ' external-css ' ;
 		}

 		return $domObj;

 	}

	public function post_parse_button($domObj, $mode = 'normal')
	{
		$data = $this->data[$this->blockname];
		$button_id = $this->data["id"];
		$anchor = $domObj->find("a",0);

		if (isset($data["extra_classes"]) && trim($data["extra_classes"]) != '')
		{
			$extra = trim($data["extra_classes"]);
			$anchor->class .= ' ' . $extra;
		}

		return $domObj;
	}


	public function admin_fields()
	{
		$data = $this->data[$this->blockname];
		$admin = MB()->getClass('admin');

				$start_block = new maxField('block_start');
				$start_block->name = __('advanced', 'maxbuttons');
				$start_block->label = __('Advanced', 'maxbuttons');
				$admin->addField($start_block);

				$imp = new maxField('switch');
				$imp->note = __('Adding !important to the button styles can help avoid potential conflicts with your theme styles.', 'maxbuttons') ;
				$imp->id = 'important_css';
				$imp->name = $imp->id;
				$imp->value = 1;
				$imp->label = __('Use !Important', 'maxbuttons');
				$imp->checked = checked(maxBlocks::getValue('important_css'), 1, false);
				//$imp->value = maxBlocks::getValue('important_css');
				//$imp->output('start','end');

				$admin->addField($imp, 'start', 'end');

				$class = new maxField();
				$class->id = 'extra_classes';
				$class->name = $class->id;
				$class->label = __("Extra classes","maxbuttons");
				$class->value = maxBlocks::getValue($class->id);
				$class->note = __("Useful for custom code or other plugins who target classes", "maxbuttons");
				$class->help = "<p class='shortcode'>Shortcode attribute : extraclass </p> <p>Using attribute will add classes, not replace them </p> ";
				//$class->output('start','end');

				$admin->addField($class, 'start', 'end');

				$rel = new maxField();
				$rel->id = 'custom_rel';
				$rel->name = $rel->id;
				$rel->label = __("Custom Rel Tag","maxbuttons");
				$rel->value = maxBlocks::getValue($rel->id);
				$rel->note = __("Useful when button is targeting lightbox and/or popup plugins that use this method", "maxbuttons");
				//$rel->output('start','end');

				$admin->addField($rel, 'start', 'end');

			//	do_action('mb-after-advanced');

				$nocss = new maxField('switch');
				$nocss->note = __('By default, the CSS styles for the button are rendered within a &lt;style&gt; block in the HTML body. Enabling the "Use External CSS" option allows you to put the CSS code for the button into your theme stylesheet instead.', 'maxbuttons');
				$nocss->label = __('Use External CSS', 'maxbuttons');
				$nocss->id = 'external_css';
				$nocss->value = 1;
				$nocss->name = $nocss->id;
				$nocss->checked = checked(maxBlocks::getValue($nocss->id), 1, false);
				//$nocss->output('start','');
				$admin->addField($nocss, 'start');

				$nospace = new maxField('spacer');
				$nospace->content = __("Warning: This will remove all styling of the buttons!","maxbuttons");
			//	$nospace->output('','end');
				$admin->addField($nospace, '', 'end');

				$viewcss = new maxField('button');
				$viewcss->id = 'view_css_modal';
				$viewcss->name = $viewcss->id;
				$viewcss->label = '&nbsp;';
				$viewcss->button_label = __('View CSS', 'maxbuttons');
				$viewcss->inputclass = 'maxmodal';
				$viewcss->modal = 'view-css';

				$admin->addField($viewcss, 'start', 'end');
					?>


<?php

						$this->sidebar();
						$endblock = new maxField('block_end');
						$admin->addField($endblock);

						?>

						<div id="view-css" class="maxmodal-data" >
								<h3 class="title"><?php _e("External CSS","maxbuttons"); ?></h3>
							<div class="content">
								<p><?php _e('If the "Use External CSS" option is enabled for this button, copy and paste the CSS code below into your theme stylesheet.', 'maxbuttons') ?></p>

							<textarea id="maxbutton-css" readonly="readonly">
							<?php
								if (isset($this->data["id"]) && $this->data['id'] > 0)
								{
									$id = $this->data["id"];
									$b = MB()->getClass('button');

									$b->set($id);
									$b->parse_button();
									$b->parse_css("preview");

									echo $b->getparsedCSS();

								}
 								else
 								{ _e("Please save the button first","maxbuttons"); }

							 ?></textarea>
							 </div>
							 <div class='controls'>
							 	<p><a class='button-primary modal_close' href='javascript:void(0);'><?php _e("Close","maxbuttons"); ?></a>
							 	</p>
							 </div>
						</div>

						<?php
} // admin_fields

} // class

?>
