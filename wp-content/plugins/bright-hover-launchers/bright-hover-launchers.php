<?php
    /*
    Plugin Name: Bright Hovering Caption Launch Templates
    Plugin URI: http://www.aura-softare.com/
    Description: bright-hover-launchers
    Author: Aura Software
    Version: 1.1
    Author URI: http://www.aura-software.com/

    Source code created by Aura Software, LLC is licensed under a
    Attribution-NoDerivs 3.0 Unported United States License
    http://creativecommons.org/licenses/by-nd/3.0/
    */

$plugin_root = dirname (__FILE__);

class BrightHoverLauncherTemplates {

  public static function loadScripts() {
    /* wp_register_style('bright_hover_custom', */
  	/* 				plugins_url('bright-hover-launchers/bright-hover-launchers.css')); */

    wp_register_style('bright_hover_default',
  					plugins_url('bright-hover-launchers/CaptionHoverEffects/css/default.css'));

    wp_enqueue_style('bright_hover_default');


    wp_register_style('bright_hover_component',
  					plugins_url('bright-hover-launchers/CaptionHoverEffects/css/component.css'));
    wp_enqueue_style('bright_hover_component');

    wp_enqueue_script('bright_hover_modernizr',
  					plugins_url('bright-hover-launchers/CaptionHoverEffects/js/modernizr.custom.js'), array('bright'));

    wp_enqueue_script('bright_hover_toucheffects',
                      plugins_url('bright-hover-launchers/CaptionHoverEffects/js/toucheffects.js'), array('bright'));


    wp_enqueue_script('bright_hover_custom',
  					plugins_url('bright-hover-launchers/bright-hover-launchers.js'));
  
    /* if you use the bright testing qunit framework, you can define QUnit tests to test your templates */
    if (bright_testing()) {
      wp_enqueue_script('bright_hover_custom',
                        plugins_url('qunit-tests.js', __FILE__),
                        array('bright'));
    }
  }

  public static function produceTemplateCode(array $args = array()) {
    $style = Bright\extractFromArray($args,'style');
    $srcRoot="/wp-content/plugins/bright-hover-launchers/CaptionHoverEffects/";

    $code =<<<EOF
		<div class="container">
			<ul class="grid cs-style-{$style}">
				<li>
					<figure>
						<img src="{{#fetch_image this}}{{/fetch_image}}" alt="img01">
						<figcaption>
							<h3>{{title}}</h3>
                            <div>
                               <div>{{{launchbutton}}}</div>
{{#if registration}}
<div>
Complete: {{registration.complete}}<br/>
Passed: {{registration.success}}<br/>
Score: {{registration.score}}<br/>
</div>
{{else}}
<span>No results yet!  Click '{{#bGetDefaultValue attributes.launch_button_text 'Launch course'}}{{/bGetDefaultValue}}' to get started</span>
{{/if}}
</div>
						</figcaption>
					</figure>
				</li>
			</ul>
		</div><!-- /container -->
EOF;
   return $code;
  }
    
}

add_action('wp_enqueue_scripts', 'BrightHoverLauncherTemplates::loadScripts');

if (!class_exists('Bright\\Wordpress')) 
  require_once(plugin_dir_path( __FILE__ ) . '../bright/bright.php');

$bright = \Bright\Wordpress::getInstance(); 


for ($i = 1; $i <= 7; $i++) 
  $bright->addTemplate("hovering-caption-over-image-{$i}",BrightHoverLauncherTemplates::produceTemplateCode(array('style' => $i)));




