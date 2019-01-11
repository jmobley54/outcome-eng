<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');
$blockClass["google"] = "googleBlock";
$blockOrder[110][] = "google";


class googleBlock extends maxBlock
{
	protected $blockname = "google";
	protected $fields = array(
							  "gtrack_enable" => array("default" => "0"),
							  "gtrack_cat" => array("default" => 'MaxButtons'),
							  "gtrack_action" => array("default" => "click"),
							  "gtrack_label" => array("default" => ""),
							  "gtrack_value" => array("default" => 0),
							  "gtrack_interaction" => array("default" => '1'),
							 );


	function __construct()
	{
		parent::__construct();

	}

	/*public function parse_css($css,  $mode = 'normal')
	{
		$data = $this->data[$this->blockname];


		return $css;

	} */

	public function save_fields($data, $post)
	{
		$data = parent::save_fields($data, $post);


		$block = (isset($data[$this->blockname])) ? $data[$this->blockname] : array();

		if (! isset($post["gtrack_interaction"]) && isset($post['gtrack_realsave']) ) // do not set default if somebody unchecks it
			$block["gtrack_interaction"] = 0;

		$data[$this->blockname] = $block;

 		return $data;
	}

	public function parse_button($domObj, $mode = 'normal')
	{

		$data = $this->data[$this->blockname];

		// not needed anymore see issue #45
		//
		//$anchor->id = "maxbutton-" . $this->data["document_id"];

		// it's off
 		if(! isset($data["gtrack_enable"]) || intval($data["gtrack_enable"]) != 1)
 		{
 			return $domObj;
 		}

 		$anchor = $domObj->find("a",0);

  		$gtrack_cat = isset($data["gtrack_cat"]) ? $data["gtrack_cat"] : '';
 		$gtrack_action = isset($data["gtrack_action"]) ? $data["gtrack_action"] : '';
 		$gtrack_label = isset($data["gtrack_label"]) ? $data["gtrack_label"] : '';
 		$gtrack_value = isset($data["gtrack_value"]) ? intval($data["gtrack_value"]) : 0;
 		$gtrack_interaction = isset($data["gtrack_interaction"]) ? $data["gtrack_interaction"] : 1;

 		if ($gtrack_interaction == 1)
 			$gtrack_interaction = 'false'; // false because in GA it's 'nonInteraction' that's set.
 		else
 			$gtrack_interaction = 'true';

 		$tag = "data-mbga";
 		//event,id, cat, action, label, value, noninteraction
 		$json_array = array("cat" => $gtrack_cat,
 							"action" => $gtrack_action,
 							"label" => $gtrack_label,
 							"value" => $gtrack_value,
 							"noninteraction" => $gtrack_interaction,
 						);
 		$anchor->$tag = htmlentities(json_encode($json_array), ENT_QUOTES, 'UTF-8');

		return $domObj;

	}

	public function parse_js($js, $mode = 'normal')
	{
		$data = $this->data[$this->blockname];
 		$id = $this->data["id"];

 		$document_id = (isset($this->data["document_id"])) ? $this->data["document_id"] : 0;

 		// This will be removed in upcoming version. Handling now done by setting data- in parse button.
 		return $js;

		/*
 		if(! isset($data["gtrack_enable"]) || intval($data["gtrack_enable"]) != 1)
 		{
 			return $js;
 		}

 		$gtrack_cat = isset($data["gtrack_cat"]) ? $data["gtrack_cat"] : '';
 		$gtrack_action = isset($data["gtrack_action"]) ? $data["gtrack_action"] : '';
 		$gtrack_label = isset($data["gtrack_label"]) ? $data["gtrack_label"] : '';
 		$gtrack_value = isset($data["gtrack_value"]) ? intval($data["gtrack_value"]) : 0;
 		$gtrack_interaction = isset($data["gtrack_interaction"]) ? $data["gtrack_interaction"] : 1;

 		if ($gtrack_interaction == 1)
 			$gtrack_interaction = 'false'; // false because in GA it's 'nonInteraction' that's set.
 		else
 			$gtrack_interaction = 'true';

 		if ($gtrack_cat == '' || $gtrack_action == '' )
 			return $js; // required

 		$id = "maxbutton-" . $document_id;
		$function = " var button = document.getElementById('$id');
 			mbAddListener(button, 'click', function (event)  { ";

 		//$function .= "event.preventDefault();";
 		$function .= "mbTrackEvent(event,'$id', '$gtrack_cat', '$gtrack_action', '$gtrack_label', $gtrack_value, $gtrack_interaction);  ";

 		$function .= " });";
 		$js[] = $function;

 		return $js;
		*/
	}

	public function map_fields($map)
	{

		return $map;
	}

	public function admin_fields()
	{
		//parent::admin_fields();

		$data = $this->data[$this->blockname];
		foreach($this->fields as $field => $options)
		{
 	 	    $default = (isset($options["default"])) ? $options["default"] : '';
			${$field} = (isset($data[$field])) ? $data[$field] : $default;

		}

?>

	<div class="option-container" data-options="google" >
		<div class="title"><?php _e('Google Event Tracking for Button Clicks', 'maxbuttons-pro') ?>
		<span class='manual-box'><a class='manual-toggle' href='javascript:void(0);' data-target="google"> <?php _e("Getting Started","maxbuttons-pro"); ?> </a></span>
		</div>

	<div class="inside">

	<?php

		$enable = new maxField('switch');
		$enable->id = 'gtrack_enable';
		$enable->name = $enable->id;
		$enable->value = '1';
		$enable->checked = checked(maxBlocks::getValue('gtrack_enable'), 1,false);
		$enable->label = __("Enable Event Tracking", "maxbuttons-pro");
		$enable->output('start','end');

		$cat = new maxField('text');
		$cat->id = 'gtrack_cat';
		$cat->name = $cat->id;
		$cat->value = maxBlocks::getValue('gtrack_cat');
		$cat->label = __('Category','maxbuttons-pro');
		$cat->help = __("Category is a group name for a set of buttons you want to track.  For example you might track your Buy Now buttons as a group. <p class='shortcode'> Shortcode attribute: google_category</p>","maxbuttons-pro");
		$cat->output('start','end');

		$action = new maxField('text');
		$action->id = 'gtrack_action';
		$action->name = $action->id;
		$action->label = __("Action", "maxbuttons-pro");
		$action->help = __("The type of interaction (i.e. click, play, buy) <p class='shortcode'> Shortcode attribute: google_action</p>","maxbuttons-pro");
		$action->value = maxBlocks::getValue('gtrack_action');

		$action->output('start','end');

		$label = new maxField('text');
		$label->id = 'gtrack_label';
		$label->name = $label->id;
		$label->label = __('Label','maxbuttons-pro');
		$label->value = maxBlocks::getValue('gtrack_label');
		$label->help = __("Useful for categorizing events (i.e. 'front-page' ) <p class='shortcode'> Shortcode attribute: google_label</p>","maxbuttons-pro");
		$label->output('start','end');

		$val = new maxField('number');
		$val->id = 'gtrack_value';
		$val->name = $val->id;
		$val->value = maxBlocks::getValue('gtrack_value');
		$val->label = __('Value','maxbuttons-pro');
		$val->min = 0;
		$val->inputclass = 'tiny';
		$val->help = __("Value lets you assign the value (weight, importance) to you of a user clicking on this button", "maxbuttons-pro");
		$val->output('start','end');

		$int = new maxField('switch');
		$int->id = 'gtrack_interaction';
		$int->name = $int->id;
		$int->value = '1';
		$int->checked = checked(maxBlocks::getValue('gtrack_interaction'), '1', false);
		$int->help = __("When checked  Google Analytics will see this event as a visitor interaction. This lowers the bounce rate.  We recommend checking this setting.", "maxbuttons-pro");
		$int->label = __('Interaction','maxbuttons-pro');
		$int->output('start','end');

		// because of default on of interaction - look for real saves, or default inits.
		$hid = new maxField('hidden');
		$hid->id = 'gtrack_realsave';
		$hid->name = $hid->id;
		$hid->value = 'true';
		$hid->output('','');


		?>

		<div class="note-line">
			<p>* <?php _e("Both event category and action are required fields","maxbuttons-pro"); ?></p>
		</div>

	</div> <!-- inside -->



	</div> <!-- option container -->
	<div class="manual-entry" data-manual="google">
		<h3><?php _e("Adding Google Event Tracking to your button", "maxbuttons-pro"); ?>
			<span class="dashicons dashicons-no window close manual-toggle" data-target="google"></span>
		</h3>
		<p><?php _e("Google Event tracking allows you to receive an event notification in Google Analytics everytime a visitor
			clicks your button.", "maxbuttons-pro"); ?> </p>

		<p><a href="http://maxbuttons.com/google-analytics-event-tracking-wordpress-buttons/" target="_blank"><?php _e("Setting up Event Tracking", "maxbuttons-pro"); ?> </a></p>

		<h4><?php _e("Setup","maxbuttons-pro"); ?></h4>
		<ol class='manual_list'>
			<li><?php printf(__("Setup Google Analytics on your WordPress site. You can either manually insert the correct Analytics code or
				use a popular plugin like %sGoogle Analytics%s, %sGoogle Analytics Dashboard for WP%s or one of the many others","maxbuttons-pro"), '<a href="https://wordpress.org/plugins/google-analytics-for-wordpress/" target="_blank">','</a>','<a href="https://wordpress.org/plugins/google-analytics-for-wordpress/" target="_blank">','</a>'); ?>
			</li>
			<li><?php printf(__("Click the checkbox %s'Enable Event Tracking'%s to activate the tracking for the button","maxbuttons-pro"),
				"<strong>","</strong>"); ?></li>
			<li><?php _e("The fields Event category and Event Action are required for Google Analytics and you should fill them out. This controls how your events will be ordered in Google Analytics.","maxbuttons-pro") ?></li>
			<li><?php _e("Save the button and test if the event are being inserted into your GA account", "maxbuttons-pro") ?></li>
		    </ol>


	</div>
<?php }  // admin_display

 } // class

 ?>
