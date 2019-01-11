<?php

class Product extends BaseEntity{
	
	
	public function __construct(){
		
		$this->id = "Product";
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
			'placeholder' => __('Product ID', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['sku'] = array(
			'name' => 'sku',
			'placeholder' => __('Product SKU', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['parent_id'] = array(
				'name' => 'parent_id',
				'placeholder' => __('Parent ID', JEM_EXP_DOMAIN)
		
		);
		
		$fields['parent_sku'] = array(
				'name' => 'parent_sku',
				'placeholder' => __('Parent SKU', JEM_EXP_DOMAIN)
		
		);
		
		
		$fields['name'] = array(
				'name' => 'name',
				'placeholder' => __('Product Name', JEM_EXP_DOMAIN)
		
		);
		
		$fields['product_type'] = array(
			'name' => 'product_type',
			'placeholder' => __('Product Type', JEM_EXP_DOMAIN)	
				
		);
		
		$fields['shipping_class'] = array(
				'name' => 'shipping_class',
				'placeholder' => __('Shipping Class', JEM_EXP_DOMAIN)
		
		);
		$fields['width'] = array(
				'name' => 'width',
				'placeholder' => __('Width', JEM_EXP_DOMAIN)
		
		);
		
		$fields['length'] = array(
				'name' => 'length',
				'placeholder' => __('Length', JEM_EXP_DOMAIN)
		
		);
		
		$fields['height'] = array(
				'name' => 'height',
				'placeholder' => __('Height', JEM_EXP_DOMAIN)
		
		);
		
		$fields['managing_stock'] = array(
				'name' => 'managing_stock',
				'placeholder' => __('Managing Stock', JEM_EXP_DOMAIN)
		
		);
		
		$fields['in_stock'] = array(
				'name' => 'in_stock',
				'placeholder' => __('In Stock', JEM_EXP_DOMAIN)
		
		);
		
		$fields['qty_in_stock'] = array(
				'name' => 'qty_in_stock',
				'placeholder' => __('Qty In Stock', JEM_EXP_DOMAIN)
		
		);
		
		$fields['downloadable'] = array(
				'name' => 'downloadable',
				'placeholder' => __('Downloadable', JEM_EXP_DOMAIN)
		
		);
		
		$fields['tax_status'] = array(
				'name' => 'tax_status',
				'placeholder' => __('Tax Status', JEM_EXP_DOMAIN)
		
		);
		
		$fields['tax_class'] = array(
				'name' => 'tax_class',
				'placeholder' => __('Tax Class', JEM_EXP_DOMAIN)
		
		);
		
		$fields['featured'] = array(
				'name' => 'featured',
				'placeholder' => __('Featured Product', JEM_EXP_DOMAIN)
		
		);

		$fields['price'] = array(
				'name' => 'price',
				'placeholder' => __('Price', JEM_EXP_DOMAIN)
		
		);
		
		$fields['sale_price'] = array(
				'name' => 'sale_price',
				'placeholder' => __('Sale Price', JEM_EXP_DOMAIN)
		
		);
		
		$fields['sale_from'] = array(
				'name' => 'sale_from',
				'placeholder' => __('Sale Start Date', JEM_EXP_DOMAIN)
		
		);
		
		$fields['sale_to'] = array(
				'name' => 'sale_to',
				'placeholder' => __('Sale End Date', JEM_EXP_DOMAIN)
		
		);
		
		
		return $fields;
	}

	/**
	 * Creates the filters
	 * (non-PHPdoc)
	 * @see BaseEntity::generate_filters()
	 */
	public function generate_filters(){
		
		return '<p>' .  __('Coming soon', JEM_EXP_DOMAIN) . '</p>';
	}
	
	/**
	 * Returns true if we got data
	 * false if no data found
	 * (non-PHPdoc)
	 * @see BaseEntity::run_query()
	 */
	public function  run_query($file){
	
		//get all Products
		global $woocommerce;
	
		//TODO - need to add filters here
	
		$limit_volume = -1;
		$offset = 0;
		$product_categories = false;
		$product_tags = false;
		$product_status = false;
		$product_type = false;
		$orderby = 'ID';
		$order = 'ASC';

		$post_type = array( 'product', 'product_variation' );
		$args = array(
				'post_type' => $post_type,
				'orderby' => $orderby,
				'order' => $order,
				'offset' => $offset,
				'posts_per_page' => $limit_volume,
				//'post_status' => woo_ce_post_statuses(),
				'fields' => 'ids',
				'suppress_filters' => false
		);	
	
	
	
	
		$ids = new WP_Query($args);
		
		//Array to hold products
		$products = array();
		
		//we now have an array of ids, so lets get the product for each id
		if($ids->posts){
			
			//sometimes there are products without parents, if so we ignore thjem
			foreach($ids->posts as $product_id){
				$pdct = get_post($product_id);
				
				if( $pdct->post_type == 'product_variation' ){
					//parent?
					if( $pdct->post_parent ) {
						if( get_post( $pdct->post_parent ) ) {
							//add it to our array!
							$products[] = $product_id;
							continue;
						} else {
							continue;
						}
					}
					
					
					//add it to the array 
					$products[] = $product_id;
					continue;
				} else {
					//simple product
					$products[] = $product_id;
				}
				
			}
			
		}
	
		//do we have any products?
		if( count($products) > 0 ){
	
			$data = array();
	
			//OK lets create the header row
			$data = $this->create_header_row();
			fputcsv( $file, $data );
	
				
			foreach($products as $product_id){
	
				//ok processing each product

					error_log($product_id);

				//$pdct = new WC_Product($product_id);
				$pdct = wc_get_product( $product_id);

	
				//Check the filters here - we need to query
	

				//ok create the row for this products
				$data = $this->extract_fields($pdct);
				//OK $data has all the fields, now output them to the csv file

				fputcsv( $file, $data );
				
	
			}
		} else {
			//we don't have any products!!!
			return false;
		}
	}
	
	/**
	 * Extracts the relevent product fields and adds them to the array
	 * this is where the hard work of getting the data out occurs
	 * Additionally do any formatting here....
	 */
	private function extract_fields( $p ){
	
		$data = array ();
	
		// Go thru each field
		foreach ( $this->fieldsToExport as $name => $field ) {
				
			switch ($name) {

				case 'id' :
					array_push ( $data,  is_callable( array( $p, 'get_id' ) ) ? $p->get_id() : $p->id  );
					break;
				
				case 'sku' :
					array_push( $data, $p->get_sku() );
					break;
				
				case 'parent_id' :
					$parent = is_callable( array( $p, 'get_parent_id' ) ) ? $p->get_parent_id() : $p->parent_id;
					if($parent == 0){
						$parent ='';
					}
					array_push ( $data, $parent );
					break;
	
				case 'parent_sku' :
					$parent = is_callable( array( $p, 'get_parent_id' ) ) ? $p->get_parent_id() : $p->parent_id;
					
					//if we have a parent, get the sku
					if(!empty($parent)){
						$temp = new WC_Product($parent);
							
						array_push ( $data, $temp->get_sku() );
						
					} else {
						//no parent so blank
						array_push ( $data, '' );
						
					}
					break;
	
				case 'name' :
					array_push ( $data, $p->get_title() );
					break;
	
				case 'product_type' :
					//Woo's product type is always null - bug somewhere
					//array_push ( $data, $p->product_type );
					$temp = wc_get_product( is_callable( array( $p, 'get_id' ) ) ? $p->get_id() : $p->id );
					array_push ( $data,  is_callable( array( $temp, 'get_type' ) ) ? $temp->get_type() : $temp->product_type  );
					break;
	
				case 'shipping_class' :
					array_push ( $data, $p->get_shipping_class() );
					break;
						
				case 'width' :
					array_push ( $data, is_callable( array( $p, 'get_width' ) ) ? $p->get_width() : $p->width );

					break;
						
				case 'length' :
					array_push ( $data, is_callable( array( $p, 'get_length' ) ) ? $p->get_length() : $p->length );
					break;
	
				case 'height' :
					array_push ( $data, is_callable( array( $p, 'get_height' ) ) ? $p->get_height() : $p->height );
					break;
	
				case 'managing_stock':
					array_push ( $data, $p->managing_stock() ? "YES" : "NO" );
					break;
	
				case 'in_stock':
					array_push ( $data, $p->is_in_stock() ? "YES" : "NO" );
					break;

				case 'qty_in_stock':
					if($p->managing_stock()){
						
						array_push ( $data, $p->stock );
					} else {
						
						array_push ( $data, '' );
					}
					break;
					
							
				case 'downloadable' :
					array_push ( $data, $p->is_downloadable() ?  'YES' : 'NO');
					break;
	
				case 'tax_status' :
					array_push ( $data, $p->get_tax_status() );
					break;
	
				case 'tax_class' :
					array_push ( $data, $p->get_tax_class() );
					break;
							
				case 'featured' :
					array_push ( $data, $p->is_featured() ?  'YES' : 'NO' );
					break;

				case 'price' :
					$price = $p->get_regular_price();
					
					//no price check if there is one in the basic function - woo is funky
					if($price==''){
						$price = $p->get_price();
					}
					array_push ( $data, $price );
					break;
						
						
				case 'sale_price' :
					array_push ( $data, $p->get_sale_price() );
					break;
	
				case 'sale_from' :
					$from =  ( $date = get_post_meta( is_callable( array( $p, 'get_id' ) ) ? $p->get_id() : $p->id, '_sale_price_dates_from', true ) ) ? date_i18n(  $this->settings['date_format'] , $date ) : '';
					array_push ( $data, $from );
					break;
	
				case 'sale_to' :
					$to =  ( $date = get_post_meta( is_callable( array( $p, 'get_id' ) ) ? $p->get_id() : $p->id, '_sale_price_dates_to', true ) ) ? date_i18n(  $this->settings['date_format'] , $date ) : '';
					array_push ( $data, $to );
					break;
	
			}
		}
	
		return $data;
	
	}
	
	
}

?>