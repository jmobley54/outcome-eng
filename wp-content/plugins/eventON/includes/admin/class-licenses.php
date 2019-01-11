<?php
/**
 * Eventon License class
 * @version 2.5.1
 */
class evo_license{

	public $code;
	public $error_msg;

	public function __construct(){
		$this->products = get_option('_evo_products');
	}

	// check purchase code correct format
		// @version 2.4
			public function purchase_key_format($key, $type='eventon'){				
				if(!strpos($key, '-'))	return false;

				if($type== 'eventon' || $type=='main'){
					$str = explode('-', $key);

					$status = true;
					$status = $this->is_valid_format($key);

					$status = (strlen($str[1])==4 && strlen($str[2])==4 && strlen($str[3])==4 )? $status: false;

					$w1 = str_split($str[1]);
					//if($w1[0] == $w1[1] && $w1[1] == $w1[2] && $w1[2] == $w1[3]) $status = false;

					$w3 = str_split($str[3]);
					//if($w3[0] == $w3[1] && $w3[1] == $w3[2] && $w3[2] == $w3[3]) $status = false;
					
					return $status;
				}else{
					$str = explode('-', $key);
					return (strlen($str[1])==4 && strlen($str[2])==4 && strlen($str[3])==4 && strpos($str[0], 'EV')!== false)? true: false;
				}				
			}

			// Check for licekse key format is valid
				public function is_valid_format($key){
					$pattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
					return (bool) preg_match( $pattern, $key );
				}

	// Actions
		// activate
			function activate($slug){
				if(!empty($this->products[$slug])){					
					return $this->update_field($slug, 'status', 'active');
				}else{return false;}
			}
		// deactivate
			function deactivate($slug){
				$product_data = $this->products;
				if(!empty($product_data[$slug])){

					$new_data = $product_data;
					$new_data[$slug]['status']='inactive';

					update_option('_evo_products',$new_data);

					// update the instance values
					$this->products = get_option('_evo_products');
					return true;
				}else{
					$this->code = '07';
					return false;
				}
			}
		// Update
			public function update_field($slug, $field, $value){
				$product_data = $this->products;

				if(!empty($product_data[$slug])){
					$new_data = $product_data;
					$new_data[$slug][$field]=$value;
					update_option('_evo_products',$new_data);
					
					// refresh the evo products values
					$this->products = get_option('_evo_products');
					return true;
				}else{return false;}
			}
		// save envato data
			function save_envato_data(){

				if(empty($_POST['type'])) return false;

				if($_POST['type']!= 'main') return false;

				foreach(array(
					'envato_username','envato_api_key'
				) as $field){
					if(!empty($_POST[$field])){						
						$this->update_field('eventon',$field , $_POST[$field]);
					}
				}

				if(!empty($_POST['purchase_key']))
					$this->update_field('eventon','key' , $_POST['purchase_key']);
			}


	// Returns		
		public function get_product($slug){
			if(empty($this->products[$slug])) return false;
			return $this->products[$slug];
		}

		// check if the license key was validated remotely
		public function remotely_validated($slug){
			$product = $this->get_product($slug);

			return (!empty($product['remote_validity']) && $product['status']=='active')? true: false;
		}
		public function get_license($slug){
			if(!empty($this->products[$slug]) && !empty($this->products[$slug]['key'])){
				return $this->products[$slug]['key'];
			}else{return false;}
		}
		public function get_partial_license($slug){	
			global $eventon;

			$key=$this->get_license($slug);
			if(!empty($key )){
				if($slug=='eventon'){
					$valid_key = $eventon->license->purchase_key_format($key);
					if($valid_key){
						$parts = explode('-', $key);
						return 'xxxxxxxx-xxxx-xxxx-xxxx-'.$parts[4];
					}else{
						$this->deactivate($slug);
						return 'n/a';
					}
				}else{
					// for addons
					return 'xxxxxxxx-xxxx-xxxx-xxxx-';
				}
			}else{return '--';}
		}

		// Eventon products kriyaathmaka kiya danum deema
		public function kriyathmakada($slug){
			//print_r($this->products);
			if(!empty($this->products[$slug])){
				return (!empty($this->products[$slug]['status']) && $this->products[$slug]['status']=='active' &&
					!empty($this->products[$slug]['key'])
				)? true:false;
			}else{return false;}
		}

		// for AJAX
		// return api url 
			public function get_api_url($args){
				$url = '';
				if($args['slug']=='eventon'){

					// get the eventon product saved info
					$product = $this->get_product('eventon');					

					$api_key = !empty($product['envato_api_key'])? $product['envato_api_key']: 'vzfrb2suklzlq3r339k5t0r3ktemw7zi';
					$api_username = !empty($product['envato_username'])? $product['envato_username']:  'ashanjay';
					$api_key = 'vzfrb2suklzlq3r339k5t0r3ktemw7zi';
					$api_username = 'ashanjay';
					$url = 'http://marketplace.envato.com/api/edge/'.$api_username.'/'.$api_key.'/verify-purchase:'.$args['key'].'.json';				
				}else{
					$instance = !empty($args['instance'])?$args['instance']:1;
					
					$url='http//www.myeventon.com/woocommerce/?wc-api=software-api&request=activation&email='.$args['email'].'&licence_key='.$args['key'].'&product_id='.$args['product_id'].'&instance='.$instance;
				}
				return $url;
			}

		// remote validation for eventon
			function eventon_remote_validation($url, $key, $slug){

				$output = array();
				$output['error_code'] = 00;
				$output['status'] = 'bad';

				if(empty($slug)) return false;

				$response = wp_remote_post( $url);
				
				if ( is_wp_error( $response ) ){
					$output['error_code'] = 20; return $output;
				}

				if ( $response['response']['code'] !== 200 ) {
					$output['error_code'] = 21; return $output;
				}

				$json = json_decode( $response['body'], true );
				
				if($slug == 'eventon'){
					
					if ( ! $json || ! isset( $json['verify-purchase'] ) ) {
						$output['error_code'] = 03; return $output;
					}

					if( empty($json['verify-purchase'])){
						$output['error_code'] = 02; return $output;
					}

					if( !empty($json['verify-purchase']['item_id']) && $json['verify-purchase']['item_id'] !='1211017'){
						$output['error_code'] = 22; return $output;
					}

					// update other values
					if(!empty($json['verify-purchase']['buyer'])) 
						$this->update_field('eventon', 'buyer', $json['verify-purchase']['buyer']);

					$this->update_field('eventon', 'remote_validity','valid' );					
					$this->eventon_kriyathmaka_karanna();

				}else{ 
				// for addons
					if ( ! $json || ! isset( $json['activated'] ) || empty($json['activated']) ) {
						$output['error_code'] = 30; return $output;
					}

					if ( !empty( $json['error'] ) ){
						$output['error_code'] = 02; return $output;
					}

					$this->update_field($slug, 'remote_validity','valid' );
					$this->update_field($slug, 'key',$key );

				}

				$output['status'] = 'good';

				return $output;
			}


		// eventon witharak - kriyathmaka karanna
			public function eventon_kriyathmaka_karanna(){
				$this->update_field('eventon', 'status', 'active');
			}
			public function evo_kriyathmaka_karanna($slug){
				$this->update_field($slug, 'status', 'active');
			}
			public function evo_kriyathmaka_karanna_locally($slug){
				$this->update_field($slug, 'status', 'active');
				$this->update_field($slug, 'remote_validity', 'local');
			}


	// error code decipher
		public function error_code($code=''){

			$code = empty($code)? (!empty($this->code)? $this->code:'20'): $code;
			
			$array = array(
				"00"=>'',
				'01'=>"No data returned from envato API",
				"02"=>'Your license is not a valid one!, please check and try again.',
				"03"=>'envato verification API is busy at moment, please try later.',
				"04"=>'This license is already registered with a different site.',
				"05"=>'Your EventON version is older than 2.2.17.',
				"06"=>'Eventon license key not passed correct!',
				"07"=>'Could not deactivate eventON license from remote server',
				'08'=>'http request failed, connection time out. Please contact your web provider!',
				'09'=>'wp_remote_post() method did not work to verify licenses, trying a backup method now..',

				'10'=>'License key is not valid, please try again.',
				'11'=>'Could not verify. Server might be busy, please try again LATER!',
				'12'=>'Activated successfully and synced w/ eventon server!',
				'13'=>'Remote validation did not work, but we have activated the software within your site!',

				'20'=>'Please try again later!',
				'21'=>'Server did not respond with OK',
				'22'=>'Purchase key is for a wrong software.',

				'30'=>'EventON API did not provide output, try again later.',

				'101'=>'Invalid license key!',
				'102'=>'Addon has been deactivated!',
				'103'=>'You have exceeded maxium number of activations!',
				'104'=>'Invalid instance ID!',
				'105'=>'Invalid security key!',
				'100'=>'Invalid request!',
			);
			return $array[$code];
		}
}