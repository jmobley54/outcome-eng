<?php

// este es el bloque del banner para descuentos

global $codepeople_cftedk_banner_plugins;
if(empty($codepeople_cftedk_banner_plugins)) $codepeople_cftedk_banner_plugins = array();
if(!function_exists( 'codepeople_add_promotecftedk_banner' ))
{
	function codepeople_add_promotecftedk_banner($wp_admin_bar)
	{
		global $codepeople_cftedk_banner_plugins;

		if( empty($codepeople_cftedk_banner_plugins) || !is_admin() ) return;

        $screen = get_current_screen();
        if ( ($screen->post_type == 'page' || $screen->post_type == 'post') && $screen->base == 'post') return;
     
		// Take action over the banner
		if(isset($_POST['codepeople_cftedk_banner_nonce']) && wp_verify_nonce($_POST['codepeople_cftedk_banner_nonce'], __FILE__))
		{
			if(
				!empty($_POST['codepeople_cftedk_banner_plugin']) &&
				!empty($codepeople_cftedk_banner_plugins[$_POST['codepeople_cftedk_banner_plugin']])
			)
			{
				set_transient( 'codepeople_cftedk_banner_'.$_POST['codepeople_cftedk_banner_plugin'], -1, 0);
				if(
					!empty($_POST['codepeople_cftedk_banner_action']) &&
					$_POST['codepeople_cftedk_banner_action'] == 'set-review' &&
					!empty($codepeople_cftedk_banner_plugins[$_POST['codepeople_cftedk_banner_plugin']]['plugin_url'])
				)
				{
					print '<script>document.location.href="'.esc_js($codepeople_cftedk_banner_plugins[$_POST['codepeople_cftedk_banner_plugin']]['plugin_url']).'";</script>';
				}
			}
		}

		$minimum_days = 86400*1; // 1 days after review
		$now = time();

		foreach($codepeople_cftedk_banner_plugins as $plugin_slug => $plugin_data )
		{

        	$valuePromo = get_transient( 'codepeople_promote_banner_'.$plugin_slug );
			if( $valuePromo === false || $valuePromo > 0)   // display only after review answer
			{
				return;
			}

            $value = get_transient( 'codepeople_cftedk_banner_'.$plugin_slug );
			if( $value === false )
			{
				$value = $now;
				set_transient( 'codepeople_cftedk_banner_'.$plugin_slug, $value, 0 );
			}

			if($minimum_days <= abs($now-$value) && 0<$value)
			{
				?>
				<style>
					#codepeople-cftedkreview-banner{width:calc( 100% - 20px );width:-webkit-calc( 100% - 20px );width:-moz-calc( 100% - 20px );width:-o-calc( 100% - 20px );margin-top:5px;border:10px solid #008a15;background:#FFF;display:table;}
					#codepeople-cftedkreview-banner form{float:left; padding:0 5px;}
					#codepeople-cftedkreview-banner .codepeople-cftedkreview-banner-picture{width:120px;padding:10px 10px 10px 10px;float:left;text-align:center;}
					#codepeople-cftedkreview-banner .codepeople-cftedkreview-banner-content{float: left;padding:10px;width: calc( 100% - 160px );width: -webkit-calc( 100% - 160px );width: -moz-calc( 100% - 160px );width: -o-calc( 100% - 160px );}
					#codepeople-cftedkreview-banner  .codepeople-cftedkreview-banner-buttons{padding-top:20px;}
					#codepeople-cftedkreview-banner  .no-thank-button,
					#codepeople-cftedkreview-banner  .main-button{height: 28px;border-width:1px;border-style:solid;border-radius:5px;text-decoration: none;}
					#codepeople-cftedkreview-banner  .main-button{background: #00ba15;border-color: #009a15 #008a15 #008a15;-webkit-box-shadow: 0 1px 0 #006799;box-shadow: 0 1px 0 #006799;color: #fff;text-decoration: none;text-shadow: 0 -1px 1px #006799,1px 0 1px #006799,0 1px 1px #006799,-1px 0 1px #006799;}
					#codepeople-cftedkreview-banner  .no-thank-button {color: #555;border-color: #cccccc;background: #f7f7f7;-webkit-box-shadow: 0 1px 0 #cccccc;box-shadow: 0 1px 0 #cccccc;vertical-align: top;}
					#codepeople-cftedkreview-banner  .main-button:hover,#codepeople-cftedkreview-banner  .main-button:focus{background: #00be15;border-color: #008a15;color: #fff;}
					#codepeople-cftedkreview-banner  .no-thank-button:hover,
					#codepeople-cftedkreview-banner  .no-thank-button:focus{background: #fafafa;border-color: #999;color: #23282d;}
					@media screen AND (max-width:760px)
					{
						#codepeople-cftedkreview-banner{position:relative;top:50px;}
						#codepeople-cftedkreview-banner .codepeople-cftedkreview-banner-picture{display:none;}
						#codepeople-cftedkreview-banner .codepeople-cftedkreview-banner-content{width:calc( 100% - 20px );width:-webkit-calc( 100% - 20px );width:-moz-calc( 100% - 20px );width:-o-calc( 100% - 20px );}
					}
				</style>
				<div id="codepeople-cftedkreview-banner">
					<div class="codepeople-cftedkreview-banner-picture">
						<img alt="" src="<?php echo plugins_url('', __FILE__); ?>/images/form-builder-th.png" style="width:120px;border:1px dotted black;padding:1px;">
					</div>
					<div class="codepeople-cftedkreview-banner-content">
						<div class="codepeople-cftedkreview-banner-text">
							<p><strong>Great! You have been using the Contact Form to Email plugin for a while. Take a moment to 
                            improve your website by getting the <span style="color:#1582AB;font-weight:bold;"><a href="https://form2email.dwbooster.com/download">full featured version</a></span> of the plugin.</strong> 
                            </p><p>This way you can build more professional contact / payment and booking forms improving your users experience and giving a better overall impression about your website. You will get a rich featured Visual Form Builder, uploads processing, the ability to create payment/booking forms, premium support service and a <a href="https://form2email.dwbooster.com/download">lot more of features</a>.</p>
                            <p>Thank you for using the plugin!</p>
						</div>
						<div class="codepeople-cftedkreview-banner-buttons">
							<form method="post" target="_blank">
								<button class="main-button" onclick="jQuery(this).closest('[id=\'codepeople-cftedkreview-banner\']').hide();">Yes, improve the website with better contact forms!</button>
								<input type="hidden" name="codepeople_cftedk_banner_plugin" value="<?php echo esc_attr($plugin_slug); ?>" />
								<input type="hidden" name="codepeople_cftedk_banner_action" value="set-review" />
								<input type="hidden" name="codepeople_cftedk_banner_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
							</form>
							<form method="post">
								<button class="no-thank-button">No Thanks, don't want to see this message again.</button>
								<input type="hidden" name="codepeople_cftedk_banner_plugin" value="<?php echo esc_attr($plugin_slug); ?>" />
								<input type="hidden" name="codepeople_cftedk_banner_action" value="not-thanks" />
								<input type="hidden" name="codepeople_cftedk_banner_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
							</form>
							<div style="clear:both;display:block;"></div>
						</div>
						<div style="clear:both;"></div>
					</div>
					<div style="clear:both;"></div>
				</div>
				<?php
				return;
			}
		}
	}
	add_action( 'admin_bar_menu', 'codepeople_add_promotecftedk_banner' );
} // End codepeople_promote_banner block
?>