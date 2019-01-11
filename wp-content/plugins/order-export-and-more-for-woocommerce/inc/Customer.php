<?php

class Customer extends BaseEntity{
	
	
	public function __construct(){
		
		$this->id = "Customer";
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
			'placeholder' => __('User ID', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['username'] = array(
			'name' => 'username',
			'placeholder' => __('User Name', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['billing_first_name'] = array(
				'name' => 'billing_first_name',
				'placeholder' => __('Billing First Name', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_last_name'] = array(
				'name' => 'billing_last_name',
				'placeholder' => __('Billing Last Name', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_company'] = array(
				'name' => 'billing_company',
				'placeholder' => __('Billing Company', JEM_EXP_DOMAIN)
		
		);
		
		
		
		$fields['billing_addr1'] = array(
				'name' => 'billing_addr1',
				'placeholder' => __('Billing Address 1', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_addr2'] = array(
			'name' => 'billing_addr2',
			'placeholder' => __('Billing Address 2', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['billing_city'] = array(
			'name' => 'billing_city',
			'placeholder' => __('Billing City', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['billing_state'] = array(
			'name' => 'billing_state',
			'placeholder' => __('Billing State', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['billing_zip'] = array(
			'name' => 'billing_zip',
			'placeholder' => __('Billing Zipcode/Postcode', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['billing_country'] = array(
			'name' => 'billing_country',
			'placeholder' => __('Billing Country', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['billing_phone'] = array(
				'name' => 'billing_phone',
				'placeholder' => __('Billing Phone Number', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_email'] = array(
				'name' => 'billing_email',
				'placeholder' => __('Billing Email Address', JEM_EXP_DOMAIN)
		
		);
		
		
		$fields['shipping_first_name'] = array(
				'name' => 'shipping_first_name',
				'placeholder' => __('Shipping First Name', JEM_EXP_DOMAIN)
		
		);
		
		$fields['shipping_last_name'] = array(
				'name' => 'shipping_last_name',
				'placeholder' => __('Shipping Last Name', JEM_EXP_DOMAIN)
		
		);
		
		$fields['shipping_company'] = array(
				'name' => 'shipping_company',
				'placeholder' => __('Shipping Company', JEM_EXP_DOMAIN)
		
		);
		
		
		$fields['shipping_addr1'] = array(
				'name' => 'shipping_addr1',
				'placeholder' => __('Shipping Address 1', JEM_EXP_DOMAIN)
		
		);
		
		$fields['shipping_addr2'] = array(
				'name' => 'shipping_addr2',
				'placeholder' => __('Shipping Address 2', JEM_EXP_DOMAIN)
		
		);
		
		$fields['shipping_city'] = array(
				'name' => 'shipping_city',
				'placeholder' => __('Shipping City', JEM_EXP_DOMAIN)
		
		);
		
		$fields['shipping_state'] = array(
				'name' => 'shipping_state',
				'placeholder' => __('Shipping State', JEM_EXP_DOMAIN)
		
		);
		
		$fields['shipping_zip'] = array(
				'name' => 'shipping_zip',
				'placeholder' => __('Shipping Zipcode/Postcode', JEM_EXP_DOMAIN)
		
		);
		
		$fields['shipping_country'] = array(
				'name' => 'shipping_country',
				'placeholder' => __('Shipping Country', JEM_EXP_DOMAIN)
		
		);
		
		
		
		
		$fields['num_orders_placed'] = array(
			'name' => 'num_orders_placed',
			'placeholder' => __('# Orders Placed', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['total_spent'] = array(
				'name' => 'total_spent',
				'placeholder' => __('Total Spent', JEM_EXP_DOMAIN)
		
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
		
		
		//filters on this object contains any filters we need to set....
		
		$ret= '
			<div class="jemex-filter-section">
				<h3 class="jem-filter-header">' . __('No Filters', JEM_EXP_DOMAIN) . '</h3>
				<p class="instructions">' . __('There are no filters for Customers.', JEM_EXP_DOMAIN) . '</p>
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
	private function extract_fields( $user ){
	
		$data = array ();
	
		return $data;
	
	}
	
	
}

?>