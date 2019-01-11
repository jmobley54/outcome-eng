<?php

class Coupon extends BaseEntity{
	
	
	public function __construct(){
		
		$this->id = "Coupons";
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

		$fields['code'] = array(
			'name' => 'code',
			'placeholder' => __('Coupon Code', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['desc'] = array(
			'name' => 'desc',
			'placeholder' => __('Coupon Description', JEM_EXP_DOMAIN)	
				
		);
		

		$fields['discount_type'] = array(
				'name' => 'discount_type',
				'placeholder' => __('Discount Type', JEM_EXP_DOMAIN)
		
		);
		
		$fields['amt'] = array(
				'name' => 'amt',
				'placeholder' => __('Coupon Amount', JEM_EXP_DOMAIN)
		
		);
		
		$fields['free_shipping'] = array(
				'name' => 'free_shipping',
				'placeholder' => __('Allow Free Shipping', JEM_EXP_DOMAIN)
		
		);

		$fields['expiry_date'] = array(
				'name' => 'expiry_date',
				'placeholder' => __('Coupon Expiry Date', JEM_EXP_DOMAIN)
		
		);
		
		$fields['min_spend'] = array(
				'name' => 'min_spend',
				'placeholder' => __('Minimum Spend', JEM_EXP_DOMAIN)
		
		);
		
		$fields['max_spend'] = array(
				'name' => 'max_spend',
				'placeholder' => __('Maximum Spend', JEM_EXP_DOMAIN)
		
		);
		
		$fields['individual'] = array(
				'name' => 'individual',
				'placeholder' => __('Individual Use Only', JEM_EXP_DOMAIN)
		
		);
		
		$fields['exclude_sales'] = array(
				'name' => 'exclude_sales',
				'placeholder' => __('Exclude Sale Items', JEM_EXP_DOMAIN)
		
		);
		
		$fields['products'] = array(
				'name' => 'products',
				'placeholder' => __('Products', JEM_EXP_DOMAIN)
		
		);
		
		$fields['exclude_products'] = array(
				'name' => 'exclude_products',
				'placeholder' => __('Exclude Products', JEM_EXP_DOMAIN)
		
		);
		
		
		$fields['categories'] = array(
				'name' => 'categories',
				'placeholder' => __('Product categories', JEM_EXP_DOMAIN)
		
		);
		
		$fields['exclude_categories'] = array(
				'name' => 'exclude_categories',
				'placeholder' => __('Exclude Product categories', JEM_EXP_DOMAIN)
		
		);
		
		$fields['email_restrictions'] = array(
				'name' => 'email_restrictions',
				'placeholder' => __('Email Restrictions', JEM_EXP_DOMAIN)
		
		);
		
		$fields['coupon_limit'] = array(
				'name' => 'coupon_limit',
				'placeholder' => __('Usage Limit per Coupon', JEM_EXP_DOMAIN)
		
		);
		
		$fields['user_limit'] = array(
				'name' => 'user_limit',
				'placeholder' => __('Usage Limit per User', JEM_EXP_DOMAIN)
		
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
	private function extract_fields( $title, $desc, $item ){
	
		$data = array ();
	
	
		return $data;
	
	}
	
	
}

?>