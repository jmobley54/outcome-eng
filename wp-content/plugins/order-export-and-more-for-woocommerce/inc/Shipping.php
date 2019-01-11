<?php

class Shipping extends BaseEntity{
	
	
	public function __construct(){
		
		$this->id = "Shipping";
		$this->enabled = true;
		
		
		//load the fields into the array
		$this->fields = array();
		$this->fields = $this->load_fields();
		
		$this->filters = array();

	}
	
	
	/**
	 * populates the array the fields for this entity
	 */
	private function load_fields(){
		
		$fields = array();

		$fields['id'] = array(
			'name' => 'id',
			'placeholder' => __('Shipping Class ID', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['name'] = array(
			'name' => 'name',
			'placeholder' => __('Shipping Class Name', JEM_EXP_DOMAIN)	
				
		);
		

		$fields['desc'] = array(
				'name' => 'desc',
				'placeholder' => __('Shipping Class Description', JEM_EXP_DOMAIN)
		
		);
		
		$fields['slug'] = array(
				'name' => 'slug',
				'placeholder' => __('Shipping Class Slug', JEM_EXP_DOMAIN)
		
		);
		
		$fields['count'] = array(
				'name' => 'count',
				'placeholder' => __('# Times Used', JEM_EXP_DOMAIN)
		
		);

		//disble fields
		foreach($fields as $key=>$field){
			$fields[$key]['disabled'] = true;
		}
		return $fields;
	}

	
	/**
	 * Extract the filters from the post
	 * @see BaseEntity::extract_filters()
	 */
	public function extract_filters($post){
		
		
		
	}
	
	
	
	
	/**
	 * Creates the filters
	 * No prefix is used
	 * (non-PHPdoc)
	 * @see BaseEntity::generate_filters()
	 */
	public function generate_filters($prefix=""){
		
		
		
		$ret= '
			<div class="jemex-filter-section">
				<h3 class="jem-filter-header">' . __('No Filters', JEM_EXP_DOMAIN) . '</h3>
				<p class="instructions">' . __('There ar eno filters for Customers.', JEM_EXP_DOMAIN) . '</p>
			</div>
				
		';
		return $ret;
		
	}
	
	/**
	 * Returns true if we got data
	 * false if no data found
	 * (non-PHPdoc)
	 * @see BaseEntity::run_query()
	 */
	public function  run_query($file){

		return false;
		
	}
	
	/**
	 * Extracts the relevent product fields and adds them to the array
	 * this is where the hard work of getting the data out occurs
	 * Additionally do any formatting here....
	 */
	private function extract_fields( $item ){
	
		$data = array ();
		return $data;
	
	}
	
	
}

?>