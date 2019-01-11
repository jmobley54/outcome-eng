<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab='.$tab.'&subtab=paypal';?>"><?php _e('PayPal', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab='.$tab.'&subtab=authorize';?>"><?php _e('Authorize', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab='.$tab.'&subtab=stripe';?>"><?php _e('Stripe', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab='.$tab.'&subtab=twocheckout';?>"><?php _e('2Checkout', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab='.$tab.'&subtab=bank_transfer';?>"><?php _e('Bank transfer', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab=general&subtab=pay_settings';?>"><?php _e('Payments Settings', 'ihc');?></a>
</div>
<?php 
echo ihc_inside_dashboard_error_license();

if (empty($_GET['subtab'])){
	//listing payment methods
	$pages = ihc_get_all_pages();//getting pages
	echo ihc_check_default_pages_set();//set default pages message
	echo ihc_check_payment_gateways();
	?>
	<div class="iump-page-title">Ultimate Membership Pro - 
		<span class="second-text">
			<?php _e('Payments Services', 'ihc');?>
		</span>
	</div>
	<div class="iump-payment-list-wrapper">
		<div class="iump-payment-box-wrap">
		<?php $pay_stat = ihc_check_payment_status('paypal'); ?>
		  <a href="<?php echo $url.'&tab='.$tab.'&subtab=paypal';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">PayPal</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		 </a>	
		</div>
		<div class="iump-payment-box-wrap">
		  <?php $pay_stat = ihc_check_payment_status('authorize'); ?>
		  <a href="<?php echo $url.'&tab='.$tab.'&subtab=authorize';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Authorize.net</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		 </a>	
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('stripe'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=stripe';?>"> 	
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Stripe</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>	
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('twocheckout'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=twocheckout';?>"> 	
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">2Checkout</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>	
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('bank_transfer'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=bank_transfer';?>"> 	
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Bank Transfer</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>	
		</div>					
	</div>
	<?php 
} else {
	switch ($_GET['subtab']){
		case 'paypal':
			ihc_save_update_metas('payment_paypal');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_paypal');//getting metas
			$pages = ihc_get_all_pages();//getting pages
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			?>
			<div class="iump-page-title">Ultimate Membership Pro - 
				<span class="second-text">
					<?php _e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form action="" method="post">
					<div class="ihc-stuffbox">
						<h3><?php _e('PayPal Activation:', 'ihc');?></h3>
						<div class="inside">		
							<div class="iump-form-line">
								<h4><?php _e('Once all the Settings are properly done, the Payment Option can be activated to be available for further use.', 'ihc');?> </h4>
								<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($meta_arr['ihc_paypal_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_paypal_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_paypal_status'];?>" name="ihc_paypal_status" id="ihc_paypal_status" /> 				
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>			
						</div>	
					</div>
					<div class="ihc-stuffbox">
					
						<h3><?php _e('PayPal Settings:', 'ihc');?></h3>
						
						<div class="inside">
							<div class="iump-form-line">
								<label class="iump-labels"><?php _e('E-mail Address:', 'ihc');?></label> <input type="text" value="<?php echo $meta_arr['ihc_paypal_email'];?>" name="ihc_paypal_email" style="width: 300px;" />
							</div>
			
							<div class="iump-form-line iump-no-border">
								<label class="iump-labels"><?php _e('Enable Sandbox', 'ihc');?></label> <input type="checkbox" onClick="check_and_h(this, '#enable_sandbox');" <?php if($meta_arr['ihc_paypal_sandbox']) echo 'checked';?> />
								<input type="hidden" name="ihc_paypal_sandbox" value="<?php echo $meta_arr['ihc_paypal_sandbox'];?>" id="enable_sandbox" />
							</div>
							<div class="iump-form-line iump-special-line">
								<label class="iump-labels-special"><?php _e('Redirect Page after Payment:', 'ihc');?></label>
								<select name="ihc_paypal_return_page">
									<option value="-1" <?php if($meta_arr['ihc_paypal_return_page']==-1)echo 'selected';?> >...</option>
									<?php 
										if($pages){
											foreach($pages as $k=>$v){
												?>
													<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_paypal_return_page']==$k) echo 'selected';?> ><?php echo $v;?></option>
												<?php 
											}						
										}
									?>
								</select>
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>				
						</div>
					</div>
					
					<div class="ihc-stuffbox">
						<h3><?php _e('Multi-Payment Selection:', 'ihc');?></h3>
						<div class="inside">
							<div class="iump-form-line iump-no-border">
								<label class="iump-labels"><?php _e('Label:', 'ihc');?></label>
								<input type="text" name="ihc_paypal_label" value="<?php echo $meta_arr['ihc_paypal_label'];?>" />
							</div>
							
							<div class="iump-form-line iump-no-border">
								<label class="iump-labels"><?php _e('Order:', 'ihc');?></label>
								<input type="number" min="1" name="ihc_paypal_select_order" value="<?php echo $meta_arr['ihc_paypal_select_order'];?>" />
							</div>						
																																
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>						
						</div>
					</div>						
					
			</form>
			<?php 		
		break;
		
		case 'stripe':
			ihc_save_update_metas('payment_stripe');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_stripe');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			?>
			<div class="iump-page-title">Ultimate Membership Pro - 
				<span class="second-text">
					<?php _e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form action="" method="post">
			<div class="ihc-stuffbox">
						<h3><?php _e('Stripe Activation:', 'ihc');?></h3>
						<div class="inside">		
							<div class="iump-form-line">
								<h4><?php _e('Once all the Settings are properly done, the Payment Option can be activated to be available for further use.', 'ihc');?> </h4>
								<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($meta_arr['ihc_stripe_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_stripe_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_stripe_status'];?>" name="ihc_stripe_status" id="ihc_stripe_status" /> 				
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>			
						</div>	
					</div>
				<div class="ihc-stuffbox">				
					<h3><?php _e('Stripe Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Secret Key:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_stripe_secret_key'];?>" name="ihc_stripe_secret_key" style="width: 300px;" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Publishable Key:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_stripe_publishable_key'];?>" name="ihc_stripe_publishable_key" style="width: 300px;" />
						</div>	
						<div class="iump-form-line">
							<?php 
								_e("Be sure You set your Web Hook URL to: ");
								echo IHC_URL . 'stripe_webhook.php';
							?>
						</div> 									
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>				
					</div>
				</div>
				
				<div class="ihc-stuffbox">
					<h3><?php _e('Multi-Payment Selection:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Label:', 'ihc');?></label>
							<input type="text" name="ihc_stripe_label" value="<?php echo $meta_arr['ihc_stripe_label'];?>" />
						</div>
						
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Order:', 'ihc');?></label>
							<input type="number" min="1" name="ihc_stripe_select_order" value="<?php echo $meta_arr['ihc_stripe_select_order'];?>" />
						</div>						
																															
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>						
					</div>
				</div>	
								
			</form>		
			<?php 
		break;
		
		case 'authorize':
			ihc_save_update_metas('payment_authorize');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_authorize');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			?>
			<div class="iump-page-title">Ultimate Membership Pro - 
				<span class="second-text">
					<?php _e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form action="" method="post">
			<div class="ihc-stuffbox">
						<h3><?php _e('Authorize.net Activation:', 'ihc');?></h3>
						<div class="inside">		
							<div class="iump-form-line">
								<h4><?php _e('Once all the Settings are properly done, the Payment Option can be activated to be available for further use.', 'ihc');?> </h4>
								<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($meta_arr['ihc_authorize_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_authorize_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_authorize_status'];?>" name="ihc_authorize_status" id="ihc_authorize_status" /> 				
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>			
						</div>	
					</div>
				<div class="ihc-stuffbox">				
					<h3><?php _e('Authorize.net Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Login ID:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_authorize_login_id'];?>" name="ihc_authorize_login_id" style="width: 300px;" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Transaction Key:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_authorize_transaction_key'];?>" name="ihc_authorize_transaction_key" style="width: 300px;" />
						</div>	
						<div class="iump-form-line iump-no-border">
								<label class="iump-labels"><?php _e('Enable Sandbox', 'ihc');?></label> <input type="checkbox" onClick="check_and_h(this, '#enable_authorize_sandbox');" <?php if($meta_arr['ihc_authorize_sandbox']) echo 'checked';?> />
								<input type="hidden" name="ihc_authorize_sandbox" value="<?php echo $meta_arr['ihc_authorize_sandbox'];?>" id="enable_authorize_sandbox" />
						</div>
						<div class="iump-form-line">
							<?php 
								_e("Be sure You set your Silent Post URL to: ");
								echo IHC_URL . 'authorize_response.php';
							?>
						</div> 	
			
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>				
					</div>
				</div>
				
				<div class="ihc-stuffbox">
					<h3><?php _e('Multi-Payment Selection:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Label:', 'ihc');?></label>
							<input type="text" name="ihc_authorize_label" value="<?php echo $meta_arr['ihc_authorize_label'];?>" />
						</div>
						
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Order:', 'ihc');?></label>
							<input type="number" min="1" name="ihc_authorize_select_order" value="<?php echo $meta_arr['ihc_authorize_select_order'];?>" />
						</div>						
																															
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>						
					</div>
				</div>					
				
			</form>		
			<?php 
		break;	
		
		case 'twocheckout':
			ihc_save_update_metas('payment_twocheckout');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_twocheckout');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			?>
			<div class="iump-page-title">Ultimate Membership Pro - 
				<span class="second-text">
					<?php _e('2Checkout Services', 'ihc');?>
				</span>
			</div>
			<form action="" method="post">
				<div class="ihc-stuffbox">
					<h3><?php _e('2Checkout Activation:', 'ihc');?></h3>
					<div class="inside">		
						<div class="iump-form-line">
							<h4><?php _e('Once all the Settings are properly done, the Payment Option can be activated to be available for further use.', 'ihc');?> </h4>
							<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($meta_arr['ihc_twocheckout_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_twocheckout_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_twocheckout_status'];?>" name="ihc_twocheckout_status" id="ihc_twocheckout_status" /> 				
						</div>
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>			
					</div>	
				</div>
				<div class="ihc-stuffbox">
					<h3><?php _e('2Checkout Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('API Username:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_api_user'];?>" name="ihc_twocheckout_api_user" style="width: 300px;" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('API Password:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_api_pass'];?>" name="ihc_twocheckout_api_pass" style="width: 300px;" />
						</div>						
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('API Private Key:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_private_key'];?>" name="ihc_twocheckout_private_key" style="width: 300px;" />
						</div>	
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Account Number:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_account_number'];?>" name="ihc_twocheckout_account_number" style="width: 300px;" />
						</div>	
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Secret Word:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_secret_word'];?>" name="ihc_twocheckout_secret_word" style="width: 300px;" />
						</div>	
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Enable Sandbox', 'ihc');?></label> <input type="checkbox" onClick="check_and_h(this, '#ihc_twocheckout_sandbox');" <?php if($meta_arr['ihc_twocheckout_sandbox']) echo 'checked';?> />
							<input type="hidden" name="ihc_twocheckout_sandbox" value="<?php echo $meta_arr['ihc_twocheckout_sandbox'];?>" id="ihc_twocheckout_sandbox" />
						</div>		
						<div class="iump-form-line">
							<?php 
								_e("Be sure You set Your 'Web Hook URL'(ISN) and Your 'Approved URL' to: ");
								echo admin_url("admin-ajax.php") . "?action=ihc_twocheckout_ins";
							?>
						</div> 					
																									
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>	
					</div>			
				</div>
				
				<div class="ihc-stuffbox">
					<h3><?php _e('Multi-Payment Selection:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Label:', 'ihc');?></label>
							<input type="text" name="ihc_twocheckout_label" value="<?php echo $meta_arr['ihc_twocheckout_label'];?>" />
						</div>
						
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Order:', 'ihc');?></label>
							<input type="number" min="1" name="ihc_twocheckout_select_order" value="<?php echo $meta_arr['ihc_twocheckout_select_order'];?>" />
						</div>						
																															
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>						
					</div>
				</div>				
				
			</form>			
						
			<?php 
			break;
			
		case 'bank_transfer':
			ihc_save_update_metas('payment_bank_transfer');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_bank_transfer');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			?>
				<div class="iump-page-title">Ultimate Membership Pro - 
					<span class="second-text">
						<?php _e('Bank Transfer Services', 'ihc');?>
					</span>
				</div>		
			<form action="" method="post">
				<div class="ihc-stuffbox">
					<h3><?php _e('Bank Transfer Activation:', 'ihc');?></h3>
					<div class="inside">		
						<div class="iump-form-line">
							<h4><?php _e('Once all the Settings are properly done, the Payment Option can be activated to be available for further use.', 'ihc');?> </h4>
							<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($meta_arr['ihc_bank_transfer_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_bank_transfer_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_bank_transfer_status'];?>" name="ihc_bank_transfer_status" id="ihc_bank_transfer_status" /> 				
						</div>
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>			
					</div>	
				</div>
				<div class="ihc-stuffbox">
					<h3><?php _e('Bank Transfer Message:', 'ihc');?></h3>
					<div class="inside">
							<div style="padding-left: 5px; width: 70%;display:inline-block;">
								<?php wp_editor( $meta_arr['ihc_bank_transfer_message'], 'ihc_bank_transfer_message', array('textarea_name'=>'ihc_bank_transfer_message', 'quicktags'=>TRUE) );?>
							</div>
							<div style="width: 25%; display: inline-block; vertical-align: top;margin-left: 10px; color: #333;">
								<div>{siteurl}</div>
								<div>{username}</div>
								<div>{first_name}</div>
								<div>{last_name}</div>
								<div>{user_id}</div>
								<div>{level_id}</div>
								<div>{level_name}</div>
								<div>{amount}</div>
								<div>{currency}</div>
							</div>																							
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>	
					</div>			
				</div>
				
				<div class="ihc-stuffbox">
					<h3><?php _e('Multi-Payment Selection:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Label:', 'ihc');?></label>
							<input type="text" name="ihc_bank_transfer_label" value="<?php echo $meta_arr['ihc_bank_transfer_label'];?>" />
						</div>
						
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Order:', 'ihc');?></label>
							<input type="number" min="1" name="ihc_bank_transfer_select_order" value="<?php echo $meta_arr['ihc_bank_transfer_select_order'];?>" />
						</div>						
																															
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>						
					</div>
				</div>
				
			</form>					
						
			<?php 		
			break;
	}

}//end of switch
