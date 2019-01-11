<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');

// Check here for existence of this module. Otherwise return. 
if (! class_exists('Fusion_Core_PageBuilder')) 
	return; 


add_action('wp_ajax_fusion_pallete_elements', function () { 
	$builder = \Fusion_Core_PageBuilder::get_instance();
 
	if(isset($_POST['category']) && $_POST['category'] == 'Palette') { //if pallete required
		try {
			require_once('class-maxbutton-block.php'); 
			
			header("Content-Type: application/json");
			
			$palette = new Palette();
			$elements = json_decode($palette->to_JSON()) ;

			$maxBlock = new \TF_MaxButtonBlock();
			//array_push($this->elements, $alert_box->element_to_array());
					
			array_unshift($elements[1]->elements ,$maxBlock->element_to_array() ) ;
			$elements = json_encode($elements);
			
			echo $elements;
		} catch(Exception $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
		exit();
	} 
	
}, 9); 
		
/*
class MaxPalette extends Palette
{

	public function load_categories()
	{
		parent::load_categories();
		print_R($this->categories);
		$this->categories =array();
			return array();
	
	}

}


class MaxBuilderElements extends BuilderElements 
{





}



*/





?>
