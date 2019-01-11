<?php 
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');
$blockClass["responsive"] = "responsiveBlockPro"; 
//$blockOrder[30][] = 'responsive'; 
 
class responsiveBlockPro extends responsiveBlock 
{

	public function __construct()
	{
		parent::__construct();
		$this->multi_fields["mq_font_size"]["csspart"] .= ",mb-text2"; 
		$this->multi_fields["mq_font_size_unit"]["csspart"] .= ",mb-text2"; 
	}

	public function parse_css($css, $mode = 'normal')
	{
 		$css = parent::parse_css($css, $mode); 
 
 		if (isset($data["auto_responsive"]) && $data["auto_responsive"] == 1)
		{
		
		 	if ( isset($this->data["text"]["font_size"]) )
 			{
	 			$css["mb-text2"]["responsive"]["phone"][0]["font-size"] = floor(intval($this->data["text"]["font_size"]) * 0.8) . 'px';  				
 			}
 			
		
		}
 		return $css;
 	}	
}

