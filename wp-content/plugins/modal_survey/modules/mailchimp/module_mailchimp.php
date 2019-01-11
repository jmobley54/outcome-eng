<?php
	if( ! class_exists( 'MailChimp' ) ) {
		require_once( sprintf( "%s/MailChimp.php", dirname( __FILE__ ) ) );
	}
	$MailChimp = new MailChimp( $sopts[ 75 ] );
	$mlid = $mailchimp_listid;
	$mc_data = array(
		'email_address' => $email,
		'status' => 'subscribed'
	);
	$result = $MailChimp->post( 'lists/' . $mlid . '/members', $mc_data );
	if ( $result[ 'status' ] == 400 ) {
		$subscriber_hash = $MailChimp->subscriberHash( $email );
		$result = $MailChimp->patch( 'lists/' . $mlid . '/members/' . $subscriber_hash, $mc_data );					
	}
	if ( $MailChimp->success() ) {
		$result = true;
	}
	else {
		$result = false;
		$error_msg = $MailChimp->getLastError();
		var_dump($error_msg);
	}	
?>