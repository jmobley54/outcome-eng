<?php
namespace MaxButtons;

		$button_id = intval($settings->button_id); 
		if ($button_id > 0) : 
			$button = MB()->getClass('button'); 
			
			$url = sanitize_text_field($settings->url); 
			$text = sanitize_text_field($settings->text); 
			
			$args = array('id' => $button_id, 'style' => 'inline'); 
			if ($url != '') $args['url'] = $url; 
			if ($text != '') $args['text'] = $text; 
			
			echo $button->shortcode($args); 			
			
		else : 
			if ( \FLBuilderModel::is_builder_active())
				echo "<p class='frontend-warning'>" . __("Click here to select MaxButtons","maxbutton-pro"). "</p>"; 
		endif; 
 
