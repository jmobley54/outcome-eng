<?php if ( ! defined( 'ABSPATH' ) ) { return; } /*#!-- Do not allow this file to be loaded unless in WP context*/
/**
 * This is the plugin's default page
 */
global $aebaseapi;
$purchase_codes = get_option(AEBaseApi::PURCHASE_CODES_OPTION_KEY, array());
$products = $aebaseapi->get_products();

?>
<style>
    div.ae-license:before {
		content: "\00e6";
		float: right;
		background: #8f1e20;
		width: 140px;
		height: 140px;
		font-size: 110px;
		color: white;
		text-align: center;
		font-weight: 300;
		line-height: 110px;
		-webkit-box-shadow: 0 1px 3px rgba(0,0,0,.2);
		box-shadow: 0 1px 3px rgba(0,0,0,.2);
		margin-top: 5px;
    }
    #ae-update-plugins-form table input{ width: 100%; }
    #ae-update-plugins-form .status{
    	text-align: center;
	    vertical-align: middle;
	    width: 30px;
    }
    #ae-update-plugins-form .status .dashicons-dismiss{
    	color: #a00;
    }
    #ae-update-plugins-form .status .dashicons-yes{
    	color: #73a724;
    }
    
</style>
<div class="wrap about-wrap ae-license">
	<h1><?php _e( 'Welcome', 'wc_point_of_sale' ); ?></h1>
	<p class="about-text"><?php _e( 'Thank you for purchasing from Actuality Extensions! To ensure you get the latest updates for bugs, features and tweaks, please enter the license details for your purchased in the table below.', 'wc_point_of_sale' ); ?></p>
	<hr>
	<div class="feature-section col two-col">
			<div class="col">
				<h4><?php _e( "Step 1 - Subscribe", "wc_point_of_sale" ); ?></h4>
				<p><?php _e( 'Join and subscribe to our newsletter to get the latest updates on new features and notices. Only special product announcements and updates will be sent, no spam.', 'wc_point_of_sale' ); ?></p>
				<a href="http://actualityextensions.us7.list-manage.com/subscribe?u=d360506c406997bb1eb300ec9&id=3a2056f6b4" class="button button-large button-primary" target="_blank"><?php esc_html_e( 'Subscribe', 'wc_point_of_sale' ); ?></a>
			</div>
			<div class="col last-feature">
				<h4><?php _e( "Step 2 - CodeCanyon", "wc_point_of_sale" ); ?></h4>
				<p><?php _e( 'Locate your purchase code through logging into ', 'wc_point_of_sale' ); ?><a href="https://codecanyon.net/sign_in" target="_blank"><?php _e( 'CodeCanyon.net', 'wc_point_of_sale' ); ?></a><?php _e( ', then go to Downloads > Plugin Name > Download > License certificate & purchase code. ', 'wc_point_of_sale' ); ?></p>
				<a href="https://codecanyon.net/sign_in/" class="button button-large button-primary" target="_blank"><?php esc_html_e( 'Retrieve Purchase Code', 'wc_point_of_sale' ); ?></a>
			</div>
	</div>
	<h4><?php _e( 'Step 3 - Installed Products', 'wc_point_of_sale' ); ?></h4>
</div>
<div class="wrap" style="margin: 25px 40px 0 20px;">
</div>
<div class="wrap about-wrap">
	<form id="ae-update-plugins-form" method="post" style="margin: 2em 0;">
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php _e( 'Product', 'wc_point_of_sale' ); ?></th>
					<th><?php _e( 'Purchase Code', 'wc_point_of_sale' ); ?></th>
					<th class="status"></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			foreach ($products as $file ) {
				$plugin_slug = basename($file, '.php');
				$pluginData = get_plugin_data($file);
				$purchase_code = isset($purchase_codes[$plugin_slug]) ? $purchase_codes[$plugin_slug] : '';
				if( $pluginData ){
					?>
					<tr>
						<th scope="row"><strong><?php echo $pluginData['Name']; ?></strong></th>
						<td><input type="text" placeholder="<?php _e( 'Place your purchase code here', 'wc_point_of_sale' ); ?>" class="regular-text" name="envato-update-plugins_purchase_code[<?php echo $plugin_slug;?>]"
						           value="<?php echo $purchase_code;?>" /></td>
			           	<td class="status">
			           		<?php
                            $code_validation = ae_updater_validate_code( $plugin_slug, $purchase_code );
			           		if( !isset($code_validation->error) ){
			           			?>
			           			<span class="dashicons dashicons-yes"></span>
			           			<?php
			           		}else{
			           			?>
			           			<span class="dashicons dashicons-dismiss" title="<?php echo $code_validation->error ?>"></span>
			           			<?php
			           		}
			           		?>
			           	</td>
					</tr>
					<?php					
				}
			}
			?>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit"class="button button-large button-primary" id="envato-update-plugins_submit"
			       value="<?php _e( 'Save Settings', 'envato-update-plugins');?>" />
		</p>
		<?php wp_nonce_field( 'wc-crm-settings' );?>
	</form>
	<hr>
	<h2><?php _e( 'Support', 'wc_point_of_sale' ); ?></h2>
	<div class="feature-section col two-col">
			<div class="col">
				<h4><?php _e( "Submit A Ticket", "wc_point_of_sale" ); ?></h4>
				<p><?php _e( "We offer our support through our advanced ticket system. Use our contact form to filter through your query so your ticket can be allocated to the right department. ", "wc_point_of_sale" ); ?></p>
				<a href="http://actualityextensions.com/contact/" class="button button-large button-primary" target="_blank"><?php esc_html_e( 'Submit a ticket', 'wc_point_of_sale' ); ?></a>
			</div>
			<div class="col last-feature">
				<h4><?php _e( "Documentation", "wc_point_of_sale" ); ?></h4>
				<p><?php _e( "This is the place to go to reference different aspects of the plugin. Our online documentation is a useful resource for learning the ins and outs of using our plugins.", "wc_point_of_sale" ); ?></p>
				<a href="http://actualityextensions.com/documentation/" class="button button-large button-primary" target="_blank"><?php esc_html_e( 'Documentation', 'Avada' ); ?></a>
			</div>
	</div>
	
</div>
