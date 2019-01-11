<?php

class Order extends BaseEntity{
	
	
	public function __construct(){
		
		$this->id = "Order";
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

		$fields['order_id'] = array(
			'name' => 'order_id',
			'placeholder' => __('Order ID', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['order_date'] = array(
			'name' => 'order_date',
			'placeholder' => __('Order Date', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['order_status'] = array(
			'name' => 'order_status',
			'placeholder' => __('Order Status', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['customer_name'] = array(
			'name' => 'customer_name',
			'placeholder' => __('Customer Name', JEM_EXP_DOMAIN)	
				
		);

		$fields['customer_email'] = array(
				'name' => 'customer_email',
				'placeholder' => __('Customer Email', JEM_EXP_DOMAIN)
		
		);
		
		$fields['order_total'] = array(
			'name' => 'order_total',
			'placeholder' => __('Order Total', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['order_shipping'] = array(
			'name' => 'order_shipping',
			'placeholder' => __('Order Shipping', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['order_shipping_tax'] = array(
			'name' => 'order_shipping_tax',
			'placeholder' => __('Order Shipping Tax', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['shipping_addr_line1'] = array(
			'name' => 'shipping_addr_line1',
			'placeholder' => __('Shipping Address Line 1', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['shipping_addr_line2'] = array(
			'name' => 'shipping_addr_line2',
			'placeholder' => __('Shipping Address Line 2', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['shipping_city'] = array(
			'name' => 'shipping_city',
			'placeholder' => __('Shipping City', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['shipping_state'] = array(
			'name' => 'shipping_state',
			'placeholder' => __('Shipping State', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['shipping_postcode'] = array(
			'name' => 'shipping_postcode',
			'placeholder' => __('Shipping Zip/Postcode', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['shipping_country'] = array(
			'name' => 'shipping_country',
			'placeholder' => __('Shipping Country', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['item_name'] = array(
				'name' => 'item_name',
				'placeholder' => __('Product Name', JEM_EXP_DOMAIN)
		
		);
		
		$fields['item_qty'] = array(
				'name' => 'item_qty',
				'placeholder' => __('Quantity of items purchased', JEM_EXP_DOMAIN)
		
		);
		
		$fields['item_subtotal'] = array(
				'name' => 'item_subtotal',
				'placeholder' => __('Item price EXCL. tax', JEM_EXP_DOMAIN)
		
		);
		
		$fields['item_tax'] = array(
				'name' => 'item_tax',
				'placeholder' => __('Item tax', JEM_EXP_DOMAIN)
		
		);
		
		$fields['item_total'] = array(
				'name' => 'item_total',
				'placeholder' => __('Item price INCL. tax', JEM_EXP_DOMAIN)
		
		);
		
		
		$fields['item_variation'] = array(
			'name' => 'item_variation',
			'placeholder' => __('Product Variations', JEM_EXP_DOMAIN)	
				
		);
		
		
		//**************************
		$fields['order_ccy'] = array(
				'disabled' => true,
				'name' => 'order_ccy',
				'placeholder' => __('Order Currency', JEM_EXP_DOMAIN)
		
		);
		
		$fields['order_discount'] = array(
				'disabled' => true,
				'name' => 'order_discount',
				'placeholder' => __('Order Discount', JEM_EXP_DOMAIN)
		
		);
		$fields['coupon_code'] = array(
				'disabled' => true,
				'name' => 'coupon_code',
				'placeholder' => __('Coupon Code', JEM_EXP_DOMAIN)
		
		);
		
		$fields['payment_gateway'] = array(
				'disabled' => true,
				'name' => 'payment_gateway',
				'placeholder' => __('Payment Gateway', JEM_EXP_DOMAIN)
		
		);
		
		$fields['shipping_method'] = array(
				'disabled' => true,
				'name' => 'shipping_method',
				'placeholder' => __('Shipping Method', JEM_EXP_DOMAIN)
		
		);
		
		$fields['shipping_weight'] = array(
				'disabled' => true,
				'name' => 'shipping_weight',
				'placeholder' => __('Shipping Weight', JEM_EXP_DOMAIN)
		
		);
		$fields['customer_note'] = array(
				'disabled' => true,
				'name' => 'customer_note',
				'placeholder' => __('Customer Message', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_addr_line1'] = array(
				'disabled' => true,
				'name' => 'billing_addr_line1',
				'placeholder' => __('Billing Address Line 1', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_addr_line2'] = array(
				'disabled' => true,
				'name' => 'billing_addr_line2',
				'placeholder' => __('Billing Address Line 2', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_city'] = array(
				'disabled' => true,
				'name' => 'billing_city',
				'placeholder' => __('Billing City', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_state'] = array(
				'disabled' => true,
				'name' => 'billing_state',
				'placeholder' => __('Billing State', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_postcode'] = array(
				'name' => 'billing_postcode',
				'disabled' => true,
				'placeholder' => __('Billing Zip/Postcode', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_country'] = array(
				'disabled' => true,
				'name' => 'billing_country',
				'placeholder' => __('Billing Country', JEM_EXP_DOMAIN)
		
		);
		
		$fields['billing_phone'] = array(
				'disabled' => true,
				'name' => 'billing_phone',
				'placeholder' => __('Billing Phone Number', JEM_EXP_DOMAIN)
		
		);
		return $fields;
	}
	

	/**
	 * Gnerates the hml output for the filters for the Order entity
	 * (non-PHPdoc)
	 * @see BaseEntity::generate_filters()
	 */
	public function generate_filters(){
	
		//lets create the array of order status
		$status = wc_get_order_statuses();
		
		$html = '<a href="#" id="order-filter-select-all-status">' . __('Select all', JEM_EXP_DOMAIN) .'</a>   |   <a href="#" id="order-filter-select-none-status">' . __('Select all', JEM_EXP_DOMAIN) .'</a>
		';
		
		foreach ($status as $key=>$val){
			$html .= '
				<div class="jem-order-status">
					<label><input type="checkbox" class="jem-checkbox" name="order-filter-order-status[]" value="' . $key . '">' . $val . '</label>		
				</div>
					
			';	
		}
		
		$ret = '
			<div>
				<h3 class="jem-filter-header">' . __('Date Filters', JEM_EXP_DOMAIN) . '</h3>
				<p class="instructions">' . __('Leave BLANK to export ALL orders.', JEM_EXP_DOMAIN) . '</p>
						</div>
			<div class="filter-dates">
				<label>
				' . __('From Date', JEM_EXP_DOMAIN) . '
				</label>
				<input id="order-filter-start-date"  name="order-filter-start-date" class="jemexp-datepicker">
				<label>
				' . __('To Date', JEM_EXP_DOMAIN) . '
				</label>
				<input id="order-filter-end-date"  name="order-filter-end-date" class="jemexp-datepicker">
			</div>
			<div class="jemex-filter-section">
				<h3 class="jem-filter-header">' . __('Order Status', JEM_EXP_DOMAIN) . '</h3>
				<p class="instructions">' . __('Select the order types you would like to export.', JEM_EXP_DOMAIN) . '</p>
			</div>
						<div> ' . $html . '
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

		//get all orders
		global $woocommerce;
		
		
		//do we have any date filters?
		$date_array = array();
		
		
		$dq = '';
		
		if( ($this->filters['start-date'] != '') || ($this->filters['end-date'] != '') ){
			
			if($this->filters['end-date'] != ''){
				$date_array['before'] =  $this->filters['end-date'] . " 23:59:59";
			}
				
				
					if($this->filters['start-date'] != ''){
				$date_array['after'] =  $this->filters['start-date'] . " 00:00:00";	
			}
			
			$dq = array(
					$date_array,
					'inclusive' => true
			);
			
		}
		
		
		//do we have any order status filters??
		
		//get the full list
		$order_statuses	=	array_keys( wc_get_order_statuses() );
		
		//lets build an array of filters selected
		
		
		//$args = array( 'post_type'=>'shop_order', 'posts_per_page'=>-1, 'post_status'=> apply_filters( 'wpg_order_statuses', $order_statuses  ), 'date_query'=>$dq );
		$args = array( 'post_type'=>'shop_order', 'posts_per_page'=>-1, 'post_status'=> $this->filters['order-status'], 'date_query'=>$dq );
		

		$orders = new WP_Query($args);
		
		//do we have any orders?
		if( $orders->have_posts() ){

			$data = array();
				
			//OK lets create the header row
			$data = $this->create_header_row();
			fputcsv( $file, $data );
				
			
			while( $orders->have_posts() ){
				
				//ok looping each order
				
				$orders->the_post();
				
				$order_details = new WC_Order( get_the_ID() );
				
				//Check the filters here - we need to query 
				
				$order_items = $order_details->get_items();
				
				//ok for orders we generate a line for EACH item
				
				//There are common fields that need to be gathered first and saved
				
				$common = $this->extract_common_fields($order_details, $order_items);
				
				//lets go thru each item
				$items = $order_details->get_items();
				
				foreach ( $items as $id => $item ) {

					//ok generate the line for this Item
					$data = $this->extract_fields($order_details, $order_items, $common, $id, $item); 

					//OK $data has all the fields, now output them to the csv file
					
					fputcsv( $file, $data );
				}
				
				
			}
		} else {
			//we don't have any orders!!!
			return false;
		}
	}
	
	/**
	 * Extracts the relevent order fields and adds them to the array
	 * this is where the hard work of getting the data out occurs
	 * Additionally do any formatting here.... 
	 * @param unknown $order_details
	 * @param unknown $order_items
	 */
	private function extract_fields( $order_details, $order_items, $common, $item_id,  $item ){
		
		$data = array ();
		
		// Go thru each field
		foreach ( $this->fieldsToExport as $name => $field ) {
			
			switch ($name) {
				
				case 'order_id' :
					array_push ( $data, $common ['order_id'] );
					break;
				
				case 'order_date' :
					
					//format the dat according to the user setting
					$date = new DateTime($common ['order_date']);
					$date = $date->format( $this->settings['date_format'] );
					array_push ( $data, $date );
					break;
				
					case 'order_status' :
					array_push ( $data, $common ['order_status'] );
					break;
				
				case 'customer_name' :
					array_push ( $data, $common ['customer_name'] );
					break;

				case 'customer_email' :
					array_push ( $data, $common ['customer_email'] );
					break;
						
				case 'order_total' :
					array_push ( $data, $common ['order_total'] );
					break;
					
				case 'order_shipping' :
					array_push ( $data, $common ['order_shipping'] );
					break;
					
				case 'order_shipping_tax' :
					array_push ( $data, $common ['order_shipping_tax'] );
					break;
						
						
				case 'shipping_addr_line1' :
					array_push ( $data, $common ['shipping_addr_line1'] );
					break;
				
				case 'shipping_addr_line2':
					array_push ( $data, $common ['shipping_addr_line2'] );
					break;
				
				case 'shipping_city' :
					array_push ( $data, $common ['shipping_city'] );
					break;
				
				case 'shipping_state' :
					array_push ( $data, $common ['shipping_state'] );
					break;
				
				case 'shipping_country' :
					array_push ( $data, $common ['shipping_country'] );
					break;
				
				case 'shipping_postcode' :
					array_push ( $data, $common ['shipping_postcode'] );
					break;
							
									
					// now handle the individual item fields
				case 'item_name' :
					array_push ( $data, $item['name']);
					break;
				
				case 'item_qty' :
					array_push ( $data, $item['qty'] );
					break;
				
				case 'item_subtotal' :
					array_push ( $data, $item['line_subtotal'] );
					break;
				
				case 'item_tax' :
					array_push ( $data, $item['line_tax'] );
					break;
				
				case 'item_total' :
					array_push ( $data, $item['line_total'] );
					break;
				
				case 'item_variation' :					
					
					if(version_compare( WC_VERSION, '3.1', '<' )){
                                            $product = $order_details->get_product_from_item($item);
                                            $meta = new WC_Order_Item_Meta( $item, $product );
                                            $meta_html = $meta->display( true, true , '_', ' | ' );
                                        }else{
                                            $meta_html = strip_tags( wc_display_item_meta( $item, array(
                                                'before' => "",
                                                'separator' => " | ",
                                                'after' => "",
                                                'echo' => false,
                                                'autop' => false,
                                                ) ) );
                                        }
					if( !empty( $meta_html ) ){												
						array_push( $data, $meta_html );
					} else {
						array_push ( $data,  "" );
					}
					break;
			}
		}
		
		return $data;
		
	}
	
	/**
	 * This gets the common fields for the order
	 * returns them in an array
	 */
	private function extract_common_fields($order_details, $order_items){
		
		$data = array();
		
		//Go thru each field
		foreach ($this->fieldsToExport as $name => $field){
				
			switch($name){
		
				case 'order_id' :
					//$data['order_id'] = $order_details->id;
					//$data['order_id'] =  is_callable( array( $order_details, 'get_id' ) ) ? $order_details->get_id() : $order_details->id;
					$data['order_id'] =  is_callable( array( $order_details, 'get_order_number' ) ) ? $order_details->get_order_number() : $order_details->id;
					break;
						
				case 'order_date' :
					//$data['order_date'] = $order_details->order_date;
					$data['order_date'] = is_callable( array( $order_details, 'get_date_created' ) ) ? $order_details->get_date_created() : $order_details->order_date;

					break;
						
					case 'order_status' :
					$data['order_status'] = $order_details->get_status();
					break;
						
				case 'customer_name' :
					$data['customer_name'] = $this->get_customer_name( is_callable( array( $order_details, 'get_id' ) ) ? $order_details->get_id() : $order_details->id );
					break;
		
				case 'customer_email' :
					//$data['customer_email'] = $order_details->billing_email ;
					$data['customer_email'] = is_callable( array( $order_details, 'get_billing_email' ) ) ? $order_details->get_billing_email() : $order_details->billing_email;

					break;
							
				case 'order_total' :
					$data['order_total'] = $order_details->get_total();
					break;

				case 'order_shipping' :
					//$data['order_shipping'] = $order_details->order_shipping;
					$data['order_shipping'] = is_callable( array( $order_details, 'get_shipping_total' ) ) ? $order_details->get_shipping_total() : $order_details->order_shipping;

					break;


				case 'order_shipping_tax' :
					//$data['order_shipping_tax'] = $order_details->order_shipping_tax;
					$data['order_shipping_tax'] = is_callable( array( $order_details, 'get_shipping_tax' ) ) ? $order_details->get_shipping_tax() : $order_details->order_shipping_tax;
					break;
						
						
				case 'shipping_addr_line1' :
					//$data['shipping_addr_line1'] = $order_details->shipping_address_1;
					$data['shipping_addr_line1'] = is_callable( array( $order_details, 'get_shipping_address_1' ) ) ? $order_details->get_shipping_address_1() : $order_details->shipping_address_1;

					break;
		
				case 'shipping_addr_line2' :
					//$data['shipping_addr_line2'] = $order_details->shipping_address_2;
					$data['shipping_addr_line2'] = is_callable( array( $order_details, 'get_shipping_address_2' ) ) ? $order_details->get_shipping_address_2() : $order_details->shipping_address_2;

					break;
				case 'shipping_city' :
					//$data ['shipping_city'] = $order_details->shipping_city;
					$data ['shipping_city'] = is_callable( array( $order_details, 'get_shipping_city' ) ) ? $order_details->get_shipping_city() : $order_details->shipping_city;;

					break;
				
				case 'shipping_state' :
					//$data ['shipping_state'] = $order_details->shipping_state;
					$data ['shipping_state'] = is_callable( array( $order_details, 'get_shipping_state' ) ) ? $order_details->get_shipping_state() : $order_details->shipping_state;

					break;
				
				case 'shipping_country' :
					//$data ['shipping_country'] = $order_details->shipping_country;
					$data ['shipping_country'] = is_callable( array( $order_details, 'get_shipping_country' ) ) ? $order_details->get_shipping_country() : $order_details->shipping_country;

					break;
				
				case 'shipping_postcode' :
					//$data ['shipping_postcode'] = $order_details->shipping_postcode;
					$data ['shipping_postcode'] = is_callable( array( $order_details, 'get_shipping_postcode' ) ) ? $order_details->get_shipping_postcode() : $order_details->shipping_postcode;

					break;
								
								
			}
		}
		
		return $data;
	}
	
	
	/**
	 * Lets get the Order filters
	 * (non-PHPdoc)
	 * @see BaseEntity::extract_filters()
	 */
	public function extract_filters($post){
		
		//Date range
		$this->filters['start-date'] = ( isset( $post['order-filter-start-date'] ) ? $post['order-filter-start-date'] : '' );
		$this->filters['end-date'] = ( isset( $post['order-filter-end-date'] ) ? $post['order-filter-end-date'] : '' );
		
		
		//Order types to get
		
		$this->filters['order-status'] = array();
		
		//if we don't have any order statuses selected return false
		if( !isset( $post['order-filter-order-status'] ) ){
			return __('There are no order statuses selected. Please select at least one order status', JEM_EXP_DOMAIN);
		}
		foreach($post['order-filter-order-status'] as $key=>$val){
			array_push($this->filters['order-status'], $val);
		}
	}
	
	/**
	 * Gets the cust name for an oder
	 * @param unknown $order_id
	 */
	private function get_customer_name( $order_id ){
		
		//no name?
		if( empty( $order_id ) ){
			return '';
		}
		
		$fname = get_post_meta( $order_id, '_billing_first_name', true );
		$lname  = get_post_meta( $order_id, '_billing_last_name', true );
		
		return trim( $fname . ' ' . $lname );
		
		
	}
	
	
	

}

?>