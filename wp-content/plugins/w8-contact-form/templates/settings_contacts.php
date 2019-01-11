	<div id="screen_preloader" style="position: absolute;width: 100%;height: 1000px;z-index: 9999;text-align: center;background: #fff;padding-top: 200px;"><h3>W8 Contact Form</h3><img src="<?php print(plugins_url( '/assets/img/screen_preloader.gif' , __FILE__ ));?>"><h5><?php _e( 'LOADING', W8CONTACT_FORM_TEXT_DOMAIN );?><br><br><?php _e( 'Please wait...', W8CONTACT_FORM_TEXT_DOMAIN );?></h5></div>
<div class="wrap w8contact_form" style="visibility:hidden">
	<br />
	<h3><?php _e( 'Contacts', W8CONTACT_FORM_TEXT_DOMAIN );?><hr /></h3>
	<form method="post" id="contacts-form" action="options.php#contact_form_slider_contacts">
					<?php settings_fields( 'contact_form_slider_contacts-group' ); ?>
					<?php do_settings_fields( 'contact_form_slider_contacts-group', 'contact_form_slider_contacts-section' ); ?>
					<?php do_settings_sections( 'contact_form_slider_contacts' ); ?>
	</form>
	<?php 
	$beemail = get_bloginfo( 'admin_email' );
	$contacts = json_decode( stripslashes( get_option( 'setting_contacts' ) ) );
	if ( $contacts == NULL ) {
		$contacts = json_decode(stripslashes('[{"name":"General Questions","email":"' . $beemail . '","emaildomain":"' . $beemail . '","title":"'.get_bloginfo( 'title' ).'","subtitle":"'.get_bloginfo( 'description' ).'","text":"We appreciate your feedback, please leave a message. You can also contact with us on the social networks you can find above this message.","photo":"'.plugins_url('/assets/img/default-photo.png' , __FILE__).'","facebook":"#","googleplus":"#","twitter":"#","pinterest":"#","linkedin":"#","skype":"#","tumblr":"#","flickr":"#","foursquare":"#","youtube":"#"}]'));
	}
	if ( isset( $_REQUEST[ 'demo_contacts' ] ) && ! isset( $_REQUEST[ 'settings-updated'] ) ) {
	$demo_path = plugins_url( '/demo' , __FILE__ );
	$demo_folder_path = sprintf( "%s/demo", dirname( __FILE__ ) );
	$res = true;
	if( ! file_exists( $demo_folder_path ) ) {
		if ( ini_get( 'allow_url_fopen' ) ) {
			if( ! file_exists( $demo_folder_path ) ) {
				if ( ! mkdir( $demo_folder_path, 0777, true ) ) {
					_e( 'Failed to create folders...', W8CONTACT_FORM_TEXT_DOMAIN );
					$res = false;
				}
			}
			if ( $res ) {
				if( ! file_exists( $demo_folder_path . '/demo1.jpg' ) ) {
					file_put_contents( $demo_folder_path . '/demo1.jpg', file_get_contents( 'http://contactform.pantherius.com/demo/demo1.jpg' ) );
				}
				if( ! file_exists( $demo_folder_path . '/demo2.jpg' ) ) {
					file_put_contents( $demo_folder_path . '/demo2.jpg', file_get_contents( 'http://contactform.pantherius.com/demo/demo2.jpg' ) );
				}
				if( ! file_exists( $demo_folder_path . '/demo3.jpg' ) ) {
					file_put_contents( $demo_folder_path . '/demo3.jpg', file_get_contents( 'http://contactform.pantherius.com/demo/demo3.jpg' ) );
				}
				if( ! file_exists( $demo_folder_path . '/demo4.jpg' ) ) {
					file_put_contents( $demo_folder_path . '/demo4.jpg', file_get_contents( 'http://contactform.pantherius.com/demo/demo4.jpg' ) );
				}
				if( ! file_exists( $demo_folder_path . '/demo5.jpg' ) ) {
					file_put_contents( $demo_folder_path . '/demo5.jpg', file_get_contents( 'http://contactform.pantherius.com/demo/demo5.jpg' ) );
				}
			}
		}
		elseif( function_exists( 'curl_version' ) ) {
			if( ! file_exists( $demo_folder_path ) ) {
				if ( ! mkdir( $demo_folder_path, 0777, true ) ) {
					_e( 'Failed to create folders...', W8CONTACT_FORM_TEXT_DOMAIN );
					$res = false;
				}
			}
			if ( $res )	{
				function getimagewithcurl( $url, $target ) {
					if ( ! file_exists( $target ) ) {
						return true;
					}
					$ch = curl_init( $url );
					$fp = fopen( $target, 'wb' );
					curl_setopt( $ch, CURLOPT_FILE, $fp );
					curl_setopt( $ch, CURLOPT_HEADER, 0 );
					curl_exec( $ch );
					curl_close( $ch );
					fclose( $fp );
				}
				getimagewithcurl( 'http://contactform.pantherius.com/demo/demo1.jpg', $demo_folder_path . '/demo1.jpg' );
				getimagewithcurl( 'http://contactform.pantherius.com/demo/demo2.jpg', $demo_folder_path . '/demo2.jpg' );
				getimagewithcurl( 'http://contactform.pantherius.com/demo/demo3.jpg', $demo_folder_path . '/demo3.jpg' );
				getimagewithcurl( 'http://contactform.pantherius.com/demo/demo4.jpg', $demo_folder_path . '/demo4.jpg' );
				getimagewithcurl( 'http://contactform.pantherius.com/demo/demo5.jpg', $demo_folder_path . '/demo5.jpg' );
			}
		}
		else _e( 'Getting datas from external URL is not possible, becase allow_url_fopen and/or cURL is disabled on your server.', W8CONTACT_FORM_TEXT_DOMAIN );
		}
		$contacts = json_decode(stripslashes('[{"name":"General Questions","status":"1","email":"' . $beemail . '","emaildomain":"' . $beemail . '","title":"Betty Webb","subtitle":"Customer Support","text":"We appreciate your feedback, please leave a message. You can also contact with us on the social networks you can find above this message.","photo":"'.$demo_path.'/demo1.jpg","facebook":"https:\/\/facebook.com","googleplus":"","twitter":"https:\/\/twitter.com","pinterest":"https:\/\/pinterest.com","linkedin":"https:\/\/www.linkedin.com","skype":"bettywebb","tumblr":"","flickr":"","foursquare":"https:\/\/foursquare.com","youtube":"https:\/\/youtube.com"},{"name":"Partnership","status":"1","email":"' . $beemail . '","emaildomain":"' . $beemail . '","title":"David A. Smith","subtitle":"Business Development","text":"Our partners are present all over the world, but we always looking for new cooperation.<br \/>If you would like be our partner, feel free to use the form below.","photo":"'.$demo_path.'/demo2.jpg","facebook":"","googleplus":"https:\/\/plus.google.com","twitter":"https:\/\/twitter.com","pinterest":"https:\/\/pinterest.com","linkedin":"https:\/\/www.linkedin.com","skype":"","tumblr":"","flickr":"https:\/\/www.flickr.com","foursquare":"","youtube":""},{"name":"Sales & Retail","status":"1","email":"' . $beemail . '","emaildomain":"' . $beemail . '","title":"Gina C. Castillo","subtitle":"Sales Director","text":"Our products are available in most of the countries.<br \/>If you have any question related with Sales or Retail, please let me know.","photo":"'.$demo_path.'/demo3.jpg","facebook":"https:\/\/facebook.com","googleplus":"https:\/\/plus.google.com","twitter":"https:\/\/twitter.com","pinterest":"https:\/\/pinterest.com","linkedin":"","skype":"","tumblr":"","flickr":"https:\/\/www.flickr.com","foursquare":"","youtube":"https:\/\/youtube.com"},{"name":"Business Development","status":"1","email":"' . $beemail . '","emaildomain":"' . $beemail . '","title":"Jason Chang","subtitle":"Business Development Manager","text":"We are constantly analyze our company and improve it periodically.<br \/>Send your ideas or feedbacks and we will considering to realize.","photo":"'.$demo_path.'/demo4.jpg","facebook":"","googleplus":"https:\/\/plus.google.com","twitter":"https:\/\/twitter.com","pinterest":"","linkedin":"https:\/\/www.linkedin.com","skype":"jasonchang","tumblr":"https:\/\/www.tumblr.com","flickr":"https:\/\/www.flickr.com","foursquare":"","youtube":"https:\/\/youtube.com"},{"name":"Supply Department","status":"1","email":"' . $beemail . '","emaildomain":"' . $beemail . '","title":"Barbara Dyess","subtitle":"Supply Chain Manager","text":"If you are not sure the product you are looking for is on stock, feel free to send a query.<br \/>I will reply back usually between 4 and 8 hours.","photo":"'.$demo_path.'/demo5.jpg","facebook":"","googleplus":"https:\/\/plus.google.com","twitter":"https:\/\/twitter.com","pinterest":"https:\/\/pinterest.com","linkedin":"","skype":"barbaradyess","tumblr":"https:\/\/www.tumblr.com","flickr":"https:\/\/www.flickr.com","foursquare":"","youtube":""}]'));
	}

	?>
		<div id="contact-list">
			<div class="one-contact">
				<div class="one-contact-inner">
				<?php
				if (!isset($contacts[0]->arsendername)) $contacts[0]->arsendername='';
				if (!isset($contacts[0]->arsenderemail)) $contacts[0]->arsenderemail='';
				if (!isset($contacts[0]->arsendermessage)) $contacts[0]->arsendermessage='';
				if (!empty($contacts[0]->photo))
				{
					$upld = '<div class="imageelement"><div id="uploaded_contact1"><div class="contact1_container"><img src="'.$contacts[0]->photo.'"><input type="hidden" class="contact1-image contact-photo" value="'.$contacts[0]->photo.'"><div><input class="remove_customimage_button button remove-button" data-addid="contact1" id="contact1-remove" type="button" value="'.__( 'REMOVE', W8CONTACT_FORM_TEXT_DOMAIN ).'" /></div></div></div></div>';
				}
				else
				{
					$upld = '<div class="imageelement"><div id="uploaded_contact1"><input id="contact1-upload" class="button add-button" type="button" value="'.__( 'Add Image', W8CONTACT_FORM_TEXT_DOMAIN ).'" /></div></div>';				
				}
				?>
				<div class="one-contact-photo contact-element"><span class="contact-number">1.</span><?php print($upld);?><input type="checkbox" class="contact-status" disabled checked id="status-contact1"><label for="status-contact1">Active</label><script>jQuery(function(){jQuery("#contact1-upload" ).pmu({"button":"#contact1-upload","target":"#uploaded_contact1","container":"<div class=\"contact1_container\"><img src=\"[content]\"><input type=\"hidden\" class=\"contact1_image contact-photo\" name=\"contact1\" value=\"objImageUrl\"><div><input class=\"remove_customimage_button button remove-button\" id=\"contact1-remove\" type=\"button\" data-addid=\"contact1\" value=\"<?php _e( 'REMOVE', W8CONTACT_FORM_TEXT_DOMAIN );?>\" /></div></div>","mode":"insert","indexcontainer":"","type":"single","callback":function(){}});jQuery(document).on("click","#contact1-remove",function(){jQuery("#uploaded_contact1").html("<div class=\"imageelement\"><div id=\"uploaded_contact1\"><input id=\"contact1-upload\" class=\"button add-button\" type=\"button\" value=\"<?php _e( 'Add Image', W8CONTACT_FORM_TEXT_DOMAIN );?>\" /></div></div>");return false;});})</script></div>
				<div class="one-contact-name contact-element"><input type="text" class="one-contact-name cfstooltip" data-title="<?php _e( 'Name', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print($contacts[0]->title);?>" placeholder="<?php _e( 'Enter the name', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
				<div class="one-contact-title contact-element"><input type="text" class="one-contact-title cfstooltip" data-title="<?php _e( 'Title', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print($contacts[0]->subtitle);?>" placeholder="<?php _e( 'Enter the title', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
				<div class="one-contact-subject contact-element"><input type="text" class="one-contact-subject cfstooltip" data-title="<?php _e( 'Subject', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print($contacts[0]->name);?>" placeholder="<?php _e( 'Enter the subject', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
				<div class="one-contact-email contact-element"><input type="text" class="one-contact-email cfstooltip" data-title="<?php _e( 'Email Address', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print( $contacts[ 0 ]->email );?>" placeholder="<?php _e( 'Email address', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
				<div class="one-contact-comment contact-element"><textarea class="one-contact-message cfstooltip" data-title="<?php _e( 'Short Description', W8CONTACT_FORM_TEXT_DOMAIN );?>" placeholder="<?php _e( 'Description', W8CONTACT_FORM_TEXT_DOMAIN );?>"><?php print($contacts[0]->text);?></textarea></div>
					<div class="one-contact-social-elements">	
						<div class="one-contact-social"><i class="fa fa-facebook"></i><input type="text" class="one-contact-facebook" value="<?php print($contacts[0]->facebook);?>" placeholder="<?php _e( 'Facebook URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
						<div class="one-contact-social"><i class="fa fa-google-plus"></i><input type="text" class="one-contact-googleplus" value="<?php print($contacts[0]->googleplus);?>" placeholder="<?php _e( 'Google Plus URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
						<div class="one-contact-social"><i class="fa fa-twitter"></i><input type="text" class="one-contact-twitter" value="<?php print($contacts[0]->twitter);?>" placeholder="<?php _e( 'Twitter URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
						<div class="one-contact-social"><i class="fa fa-pinterest"></i><input type="text" class="one-contact-pinterest" value="<?php print($contacts[0]->pinterest);?>" placeholder="<?php _e( 'Pinterest URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
						<div class="one-contact-social"><i class="fa fa-linkedin"></i><input type="text" class="one-contact-linkedin" value="<?php print($contacts[0]->linkedin);?>" placeholder="<?php _e( 'LinkedIn URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
						<div class="one-contact-social"><i class="fa fa-skype"></i><input type="text" class="one-contact-skype" value="<?php print($contacts[0]->skype);?>" placeholder="<?php _e( 'Skype Username', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
						<div class="one-contact-social"><i class="fa fa-tumblr"></i><input type="text" class="one-contact-tumblr" value="<?php print($contacts[0]->tumblr);?>" placeholder="<?php _e( 'Tumblr URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
						<div class="one-contact-social"><i class="fa fa-flickr"></i><input type="text" class="one-contact-flickr" value="<?php print($contacts[0]->flickr);?>" placeholder="<?php _e( 'Flickr URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
						<div class="one-contact-social"><i class="fa fa-foursquare"></i><input type="text" class="one-contact-foursquare" value="<?php print($contacts[0]->foursquare);?>" placeholder="<?php _e( 'Foursquare URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
						<div class="one-contact-social"><i class="fa fa-youtube"></i><input type="text" class="one-contact-youtube" value="<?php print($contacts[0]->youtube);?>" placeholder="<?php _e( 'YouTube URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
					</div>
					<div class="cfs-autoreply">
						<h4><?php _e( 'Auto Reply', W8CONTACT_FORM_TEXT_DOMAIN );?></h4><hr>
						<span><?php _e( 'Leave the fields blank to disable autoreply or use the global autoreply option.', W8CONTACT_FORM_TEXT_DOMAIN );?></span>
						<div class="cfs-ar-fields">
							<input type="text" class="cfs-ar-sendername" placeholder="<?php _e( 'Sender Name', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print($contacts[0]->arsendername);?>">
							<input type="text" class="cfs-ar-sendermail" placeholder="<?php _e( 'Sender Email Address', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print($contacts[0]->arsenderemail);?>">@<?php print(str_replace("www.","",$_SERVER['HTTP_HOST']));?>
							<textarea class="cfs-ar-autoreply" placeholder="<?php _e( 'Message', W8CONTACT_FORM_TEXT_DOMAIN );?>"><?php print(str_replace("<br />","\n",($contacts[0]->arsendermessage)));?></textarea>
						</div>
					</div>
				</div>
			</div>
			<div id="saved-contacts">
			<?php
			foreach( $contacts as $key=>$cc ) {
			if ( $key > 0 ) {
				if (!isset($cc->arsendername)) $cc->arsendername='';
				if (!isset($cc->arsenderemail)) $cc->arsenderemail='';
				if (!isset($cc->arsendermessage)) $cc->arsendermessage='';
				if (empty($cc->email)&&empty($cc->emaildomain))
				{
					$adminemail = get_bloginfo( 'admin_email' );
					$ae = explode('@',$adminemail);
					$cc->email = $ae[0];
					$cc->emaildomain = $ae[1];
				}
				if (!isset($cc->youtube)) $cc->youtube = '';
				$cn = $key+1;
				if ($cc->status=="1") $status = "checked";
				else $status = "";
				?>
				<div class="one-contact">
					<div class="one-contact-inner">
					<?php
					if (empty($cc->photo))
					{
						$upl = '<div class="imageelement"><div id="uploaded_contact'.$cn.'"><input id="contact'.$cn.'-upload" class="button add-button" type="button" value="'.__( 'Add Image', W8CONTACT_FORM_TEXT_DOMAIN ).'" /></div></div>';
					}
					else
					{
						$upl = '<div class="imageelement"><div id="uploaded_contact'.$cn.'"><div class="contact'.$cn.'_container"><img src="'.$cc->photo.'"><input type="hidden" class="contact'.$cn.'-image contact-photo" value="'.$cc->photo.'"><div><input class="remove_customimage_button button remove-button" data-addid="contact'.$cn.'" id="contact'.$cn.'-remove" type="button" value="'.__( 'REMOVE', W8CONTACT_FORM_TEXT_DOMAIN ).'" /></div></div></div></div>';
					}
					?>
					<div class="one-contact-photo contact-element"><span class="contact-number"><?php print($cn);?>.</span><?php print($upl);?><input type="checkbox" <?php print($status);?> class="contact-status" id="status-contact<?php print($cn);?>"><label for="status-contact<?php print($cn);?>">Active</label><script>jQuery(function(){jQuery("#contact<?php print($cn);?>-upload" ).pmu({"button":"#contact<?php print($cn);?>-upload","target":"#uploaded_contact<?php print($cn);?>","container":"<div class=\"contact<?php print($cn);?>_container\"><img src=\"[content]\"><input type=\"hidden\" class=\"contact<?php print($cn);?>_image contact-photo\" name=\"contact<?php print($cn);?>\" value=\"objImageUrl\"><div><input class=\"remove_customimage_button button remove-button\" id=\"contact<?php print($cn);?>-remove\" type=\"button\" data-addid=\"contact<?php print($cn);?>\" value=\"<?php _e( 'REMOVE', W8CONTACT_FORM_TEXT_DOMAIN );?>\" /></div></div>","mode":"insert","indexcontainer":"","type":"single","callback":function(){}});jQuery(document).on("click","#contact<?php print($cn);?>-remove",function(){jQuery("#uploaded_contact<?php print($cn);?>").html("<div class=\"imageelement\"><div id=\"uploaded_contact<?php print($cn);?>\"><input id=\"contact<?php print($cn);?>-upload\" class=\"button add-button\" type=\"button\" value=\"<?php _e( 'Add Image', W8CONTACT_FORM_TEXT_DOMAIN );?>\" /></div></div>");return false;});})</script></div>
					<div class="one-contact-name contact-element"><input type="text" class="one-contact-name cfstooltip" data-title="<?php _e( 'Name', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print($cc->title);?>" placeholder="<?php _e( 'Enter the name', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
					<div class="one-contact-title contact-element"><input type="text" class="one-contact-title cfstooltip" data-title="<?php _e( 'Title', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print($cc->subtitle);?>" placeholder="<?php _e( 'Enter the title', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
					<div class="one-contact-subject contact-element"><input type="text" class="one-contact-subject cfstooltip" data-title="<?php _e( 'Subject', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print($cc->name);?>" placeholder="<?php _e( 'Enter the subject', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
					<div class="one-contact-email contact-element"><input type="text" class="one-contact-email cfstooltip" data-title="<?php _e( 'Email Address', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print( $cc->email );?>" placeholder="<?php _e( 'Email address', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
					<div class="one-contact-comment contact-element"><textarea class="one-contact-message cfstooltip" data-title="<?php _e( 'Short Description', W8CONTACT_FORM_TEXT_DOMAIN );?>" placeholder="<?php _e( 'Description', W8CONTACT_FORM_TEXT_DOMAIN );?>"><?php print(str_replace("<br />","\n",$cc->text));?></textarea></div>
						<div class="one-contact-social-elements">	
							<div class="one-contact-social"><i class="fa fa-facebook"></i><input type="text" class="one-contact-facebook" value="<?php print($cc->facebook);?>" placeholder="<?php _e( 'Facebook URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
							<div class="one-contact-social"><i class="fa fa-google-plus"></i><input type="text" class="one-contact-googleplus" value="<?php print($cc->googleplus);?>" placeholder="<?php _e( 'Google Plus URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
							<div class="one-contact-social"><i class="fa fa-twitter"></i><input type="text" class="one-contact-twitter" value="<?php print($cc->twitter);?>" placeholder="<?php _e( 'Twitter URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
							<div class="one-contact-social"><i class="fa fa-pinterest"></i><input type="text" class="one-contact-pinterest" value="<?php print($cc->pinterest);?>" placeholder="<?php _e( 'Pinterest URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
							<div class="one-contact-social"><i class="fa fa-linkedin"></i><input type="text" class="one-contact-linkedin" value="<?php print($cc->linkedin);?>" placeholder="<?php _e( 'LinkedIn URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
							<div class="one-contact-social"><i class="fa fa-skype"></i><input type="text" class="one-contact-skype" value="<?php print($cc->skype);?>" placeholder="<?php _e( 'Skype Username', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
							<div class="one-contact-social"><i class="fa fa-tumblr"></i><input type="text" class="one-contact-tumblr" value="<?php print($cc->tumblr);?>" placeholder="<?php _e( 'Tumblr URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
							<div class="one-contact-social"><i class="fa fa-flickr"></i><input type="text" class="one-contact-flickr" value="<?php print($cc->flickr);?>" placeholder="<?php _e( 'Flickr URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
							<div class="one-contact-social"><i class="fa fa-foursquare"></i><input type="text" class="one-contact-foursquare" value="<?php print($cc->foursquare);?>" placeholder="<?php _e( 'Foursquare URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
							<div class="one-contact-social"><i class="fa fa-youtube"></i><input type="text" class="one-contact-youtube" value="<?php print($cc->youtube);?>" placeholder="<?php _e( 'YouTube URL', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
						</div>
						<div class="cfs-autoreply">
							<h4><?php _e( 'Auto Reply', W8CONTACT_FORM_TEXT_DOMAIN );?></h4><hr>
							<span><?php _e( 'Leave the fields blank to disable auto-reply or use the global auto-reply option.', W8CONTACT_FORM_TEXT_DOMAIN );?></span>
							<div class="cfs-ar-fields">
								<input type="text" class="cfs-ar-sendername" placeholder="<?php _e( 'Sender Name', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print($cc->arsendername);?>">
								<input type="text" class="cfs-ar-sendermail" placeholder="<?php _e( 'Sender Email Address', W8CONTACT_FORM_TEXT_DOMAIN );?>" value="<?php print($cc->arsenderemail);?>">@<?php print(str_replace("www.","",$_SERVER['HTTP_HOST']));?>
								<textarea class="cfs-ar-autoreply" placeholder="<?php _e( 'Message', W8CONTACT_FORM_TEXT_DOMAIN );?>"><?php print(str_replace("<br />","\n",$cc->arsendermessage));?></textarea>
							</div>
						</div>
					</div>
					<div class="del-button"><input class="button delete-contact" type="button" value="<?php _e( 'DELETE', W8CONTACT_FORM_TEXT_DOMAIN );?>"></div>
				</div>			
			<?php
				}
			}
			?>
			</div>
		<div class="contact-buttons">
			<div id="demo_contacts_section">
			<form method="post" action="#contact_form_slider_contacts">
			<input type="hidden" name="demo_contacts" value="1">
			<input type="submit" class="button button-secondary button-small" value="<?php _e( 'Install Demo Contacts', W8CONTACT_FORM_TEXT_DOMAIN );?>">
			</form>
			<p><?php _e( 'Loading demo contacts doesn\'t save it automatically.', W8CONTACT_FORM_TEXT_DOMAIN );?></p>
			</div>
			<input class="button button-primary" id="save-contact" type="button" value="<?php _e( 'SAVE', W8CONTACT_FORM_TEXT_DOMAIN );?>">
			<input class="button" id="add-contact" type="button" value="<?php _e( 'ADD NEW CONTACT', W8CONTACT_FORM_TEXT_DOMAIN );?>">
		</div>
		</div>
</div>
<div id="dialog-confirm2" title="Delete Contact?">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php _e( 'This contact will be permanently deleted and cannot be recovered. Are you sure?', W8CONTACT_FORM_TEXT_DOMAIN );?></p>
</div>