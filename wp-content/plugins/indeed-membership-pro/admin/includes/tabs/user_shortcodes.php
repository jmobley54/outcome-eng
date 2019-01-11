	<div class="ihc-stuffbox">
		<h3>
			<label style="text-transform: uppercase; font-size:16px;">
				<?php _e('Main ShortCodes', 'ihc');?>
			</label>
		</h3>
		<div class="inside">
			<div class="ihc-popup-content help-shortcodes" style="text-align: center;">
        	<div style="margin: 0 auto; display: inline-block;">
	            <div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-user-plus-ihc"></i><?php _e('Register Form', 'ihc');?><span>[ihc-register]</span></div>
	            <div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-sign-in-ihc"></i><?php _e('Login Form', 'ihc');?><span>[ihc-login-form]</span></div>
	            <div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-sign-out-ihc"></i><?php _e('Logout Button', 'ihc');?><span>[ihc-logout-link]</span></div>
	            <div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-unlock-ihc"></i><?php _e('Password Recovery', 'ihc');?><span>[ihc-pass-reset]</span></div>
	            <div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-user-ihc"></i><?php _e('Account Page', 'ihc');?><span>[ihc-user-page]</span></div>
	            <div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-user-plus-ihc"></i><?php _e('Subscription Plan', 'ihc');?><span>[ihc-select-level]</span></div>	
	            <div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-user-ihc"></i><?php _e('Visitor Inside User Page', 'ihc');?><span style="line-height: 23px;">[ihc-visitor-inside-user-page]</span></div>            
				<div class="ihc-clear"></div>
        	</div>
    	</div>
			<div class="clear"></div>
		</div>
	</div>
<div class="ihc-stuffbox">
	<h3>
		<label style="text-transform: uppercase; font-size:16px;"><?php _e('User ShortCodes', 'ihc');?></label>
	</h3>
	<div class="inside">
		<div class="ihc-popup-content help-shortcodes" style="">
			<table class="wp-list-table widefat fixed tags ihc-manage-user-expire">
			<thead>
				<tr>
					<th>Field</th>
					<th>Shortcode</th>
				</tr>
			</thead>
			<tbody>
	       	<?php 
	       	$data = ihc_get_user_reg_fields();
	       	$constants = array('username'=>'', 'user_email'=>'', 'first_name'=>'', 'last_name'=>'', 'account_page'=>'',
	       			'login_page'=>'', 'current_level'=>'', 'current_level_expire_date'=>'', 'level_list'=>'',
	       			'blogname'=>'', 'blogurl'=>'', 'verify_email_address_link'=>'', 'level_name'=>'' );
	       	foreach ($constants as $k=>$v){
	       		?>
				<tr>
					<td><?php echo $k;?></td>
					<td>[ihc-user field="<?php echo $k;?>"]</td>
				</tr>
	       		<?php 
	       	}
	       	$custom_fields = ihc_get_custom_constant_fields();
	       	foreach ($custom_fields as $k=>$v){
	       		$k = str_replace('{', '', $k);
	       		$k = str_replace('}', '', $k);
	       		?>
	       			<tr>
	       				<td><?php echo $v;?></td>
	       				<td>[ihc-user field="<?php echo $k;?>"]</td>
	       			</tr>
	       		<?php 
	       	}	       	
	       	//ihc_get_custom_constant_fields();
	       	?>
	       	</tbody></table>
    	</div>
		<div class="ihc-clear"></div>
	</div>
</div>
<div class="ihc-stuffbox">
	<h3>
		<label style="text-transform: uppercase; font-size:16px;"><?php _e('Levels', 'ihc');?></label>
	</h3>
	<div class="inside">
		<?php 
		$levels = get_option('ihc_levels');
		$levels = ihc_reorder_arr($levels);
		if ($levels && count($levels)){
			?>
			<table class="wp-list-table widefat fixed tags ihc-manage-user-expire">
			<thead>
				<tr>
					<th>Name</th>
					<th>Link</th>
				</tr>
			</thead>
			<tbody>
	       	<?php 
				foreach ($levels as $k=>$v){
					?>
						<tr>
							<td><?php echo $v['name'];?></td>
							<td>
								[ihc-level-link id=<?php echo $k;?>]<span style="font-size: 10px; font-style: italic;"><?php _e('Your Content Here', 'ihc');?></span>[/ihc-level-link]
							</td>
						</tr>
					<?php 					
				}
	       	?>
	       	</tbody></table>
			<?php 
		}
		?>
	
	</div>
</div>
<?php 

