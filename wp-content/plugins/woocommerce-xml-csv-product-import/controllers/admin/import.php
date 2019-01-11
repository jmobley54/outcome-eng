<?php 
/** 
 *
 * @author Maksym Tsypliakov <maksym.tsypliakov@gmail.com>
 */

class PMWI_Admin_Import extends PMWI_Controller_Admin 
{				
	public function index($post) {			
				
		$this->data['post'] =& $post;

		$this->render();

	}			
}
