<?php 
if(!class_exists('ihcStripe')){
	class ihcStripe{
		private $publishable_key = FALSE;
		private $secret_key = FALSE;
		private $level_data = array();
		private $currency = 'USD';
		
		public function __construct(){
			//set keys
			$this->publishable_key = get_option('ihc_stripe_publishable_key'); 
			$this->secret_key = get_option('ihc_stripe_secret_key');
			$this->level_data = get_option('ihc_levels');
			$this->currency = get_option('ihc_currency');
			
			//load stripe libs
			require_once IHC_PATH . 'classes/stripe/init.php';
			\Stripe\Stripe::setApiKey($this->secret_key);
		}
		
		public function payment_fields($level_id, $bind=TRUE){
			if (isset($this->level_data[$level_id])){
				$amount = $this->level_data[$level_id]['price']*100;
				if ($amount<50){
					$amount = 50;
				}
				$str = '
				<script src="https://checkout.stripe.com/checkout.js"></script>
				<script>
				var handler = StripeCheckout.configure({
					key: "' . $this->publishable_key . '",
					locale: "auto",
					token: function(response) {
						var input = jQuery("<input type=hidden name=stripeToken id=stripeToken />").val(response.id);
						var email = jQuery("<input type=hidden name=stripeEmail id=stripeEmail />").val(response.email);
						jQuery(".ihc-form-create-edit").append(input);
						jQuery(".ihc-form-create-edit").append(email);
						jQuery(".ihc-form-create-edit").submit();
					}
				});
				';

				if ($bind){
					$str .= '
								jQuery(document).ready(function(){			
										
									jQuery("#ihc_submit_bttn").bind("click", function(e){
										e.preventDefault();
										ihc_stripe_pay_on_register();
									});					
									
								});					
							';					
				}

				$str .= '
					function ihc_stripe_pay_on_register(){
						if ( jQuery("#stripeToken").val() ){
						
							} else {
								if (jQuery("[name=ihc_coupon]").val() && jQuery("[name=lid]").val()){
									//with coupon
									   	jQuery.ajax({
									        type : "post",
									        url : decodeURI(window.ihc_site_url)+"/wp-admin/admin-ajax.php",
									        data : {
									                   action: "ihc_check_coupon_code_via_ajax",
									                   code: jQuery("[name=ihc_coupon]").val(),
									                   lid: jQuery("[name=lid]").val()
									               },
									        success: function (data) {								        	
									   	 		if (data!=0){
									   	 			var obj = jQuery.parseJSON(data);
									   	 			if (obj.price==0){
									   	 				//jQuery(".ihc-form-create-edit").append("<input type=hidden name=ihcpay value=stripe />");
									   	 				jQuery(".ihc-form-create-edit").submit();
									   	 				return;
									   	 			} else if (obj.price<50){
									   	 				obj.price = 50;
									   	 			}
									   	 			handler.open({
														name: "' . $this->level_data[$level_id]['label'] . '",
														description: "' . $this->level_data[$level_id]['label'] . '",
														amount: obj.price,
														currency: "' . $this->currency . '" 
													});
									   	 		} else {
													handler.open({
														name: "' . $this->level_data[$level_id]['label'] . '",
														description: "' . $this->level_data[$level_id]['label'] . '",
														amount: ' . $amount . ',
														currency: "' . $this->currency . '" 
													});																									
									   	 		}
									        }
									   });	
								} else {
									//without coupon
									handler.open({
										name: "' . $this->level_data[$level_id]['label'] . '",
										description: "' . $this->level_data[$level_id]['label'] . '",
										amount: ' . $amount . ',
										currency: "' . $this->currency . '" 
									});										
								}				
							}					
						}				
					
				// Close Checkout on page navigation
				jQuery(window).on("popstate", function() {
					handler.close();
				});
				</script>
				';
				
				return $str;				
			}
		}
		
		public function charge($post_data){
			if (isset($this->level_data[$post_data['lid']])){
				
				$reccurrence = FALSE;
				if (isset($this->level_data[$post_data['lid']]['access_type']) && $this->level_data[$post_data['lid']]['access_type']=='regular_period'){
					$reccurrence = TRUE;
				}
								
				//DISCOUNT
				if (!empty($post_data['ihc_coupon'])){
					$coupon_data = ihc_check_coupon($post_data['ihc_coupon'], $post_data['lid']);
					if ($coupon_data && (!empty($coupon_data['reccuring']) || !$reccurrence)){
						//available only for single payment or discount on all reccuring payments
						$this->level_data[$post_data['lid']]['price'] = ihc_coupon_return_price_after_decrease($this->level_data[$post_data['lid']]['price'], $coupon_data);
					}
					
				}
				
				$amount = $this->level_data[$post_data['lid']]['price']*100;
				if ($amount<50){
					$amount = 50;// 0.50 cents minimum amount for stripe transactions
				}

				$customer_arr = array(
						'email' => $post_data['stripeEmail'],
						'card'  => $post_data['stripeToken'],
				);

				
				if ($reccurrence){
					$ihc_plan_code = 'ihc_plan_' . rand(1,10000);
					switch ($this->level_data[$post_data['lid']]['access_regular_time_type']){
						case 'D':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'day';
							break;
						case 'W':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'week';
							break;
						case 'M':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'month';
							break;
						case 'Y':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'year';
							break;
					}

					///trial
					$trial_period_days = 0;
					if (!empty($this->level_data[$post_data['lid']]['access_trial_type'])){
						if ($this->level_data[$post_data['lid']]['access_trial_type']==1 && isset($this->level_data[$post_data['lid']]['access_trial_time_value']) 
								&& $this->level_data[$post_data['lid']]['access_trial_time_value'] !=''){
							switch ($this->level_data[$post_data['lid']]['access_trial_time_type']){
								case 'D':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'];
									break;
								case 'W':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'] * 7; 
									break;
								case 'M':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'] * 31;
									break;
								case 'Y':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'] * 365;
									break;
							}
						} else if(isset($level_arr['access_trial_couple_cycles']) && $level_arr['access_trial_couple_cycles']!='') {
							switch ($this->level_data[$post_data['lid']]['access_regular_time_type']){
								case 'D':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $level_arr['access_trial_couple_cycles'];
									break;
								case 'W':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $level_arr['access_trial_couple_cycles'] * 7;
									break;
								case 'M':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $level_arr['access_trial_couple_cycles'] * 31;
									break;
								case 'Y':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $level_arr['access_trial_couple_cycles'] * 365;
									break;								
							}
						}
					}
					//end of trial
					
					$plan = array(
							"amount" => $amount,
							"interval_count" => $this->level_data[$post_data['lid']]['access_regular_time_value'],
							"interval" => $this->level_data[$post_data['lid']]['access_regular_time_type'],
							"trial_period_days" => $trial_period_days,
							"name" => "Reccuring for " . $post_data['lid'],
							"currency" => $this->currency,
							"id" => $ihc_plan_code,							
					);
					$return_data_plan = \Stripe\Plan::create($plan);
					$customer_arr['plan'] = $ihc_plan_code;
				}//end of reccurence
				
				$customer = \Stripe\Customer::create($customer_arr);
				
				$sub_id = '';
				if ($reccurrence){
					//delete the plan
					$plan = \Stripe\Plan::retrieve($ihc_plan_code);
					$plan->delete();					
					if ( isset($customer->subscriptions->data[0]->id)){
						$sub_id = $customer->subscriptions->data[0]->id;
					}	
				} else {
					$charge = \Stripe\Charge::create(array(
							'customer' => $customer->id,
							'amount'   => $amount,
							'currency' => $this->currency,
					));				
				}
				
				$amount = $amount/100;
				$response_return = array(
						'amount' => urlencode($amount),
						'currency' => $this->currency,
						'level' => $post_data['lid'],
						'item_name' => $this->level_data[$post_data['lid']]['name'],
						'customer' => $customer->id,
				);
				if ($sub_id){
					$response_return['subscription'] = $sub_id;
				}
					
				if ($reccurrence && isset($customer->id)){
					$response_return['message'] = "success";
					$response_return['trans_id'] = $customer->id;
				} else if (!empty($charge) && $charge->paid) {
					$response_return['message'] = "success";
					$response_return['trans_id'] = $charge->customer;
				} else {
					$response_return['message'] = "error";
				}				
				
				return $response_return;
			}
		}
		
		public function cancel_subscription($transaction_id){
			/*
			 * @param txn_id
			 * @return none
			 */	
			global $wpdb;
			$data = $wpdb->get_row("SELECT payment_data FROM " . $wpdb->prefix . "indeed_members_payments WHERE txn_id='" . $transaction_id . "';");
			$arr = json_decode($data->payment_data, TRUE);
			echo $arr['customer'] . ' ' . $arr['subscription'];
			if (!empty($arr['customer']) && !empty($arr['subscription'])){
				$customer = \Stripe\Customer::retrieve($arr['customer']);
				$subscription = $customer->subscriptions->retrieve($arr['subscription']);
				$value = $subscription->cancel();
				return $value;				
			} 			
		}

	}//end of class ihcStripe
	
}