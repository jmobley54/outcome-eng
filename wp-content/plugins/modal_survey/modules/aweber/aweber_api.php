<?php
require_once( sprintf( "%s/aweber_api/aweber_api.php", dirname( __FILE__ ) ) );
$aweber = new AWeberAPI( $sopts[ 30 ], $sopts[ 31 ] );

try {
    $account = $aweber->getAccount( $sopts[ 32 ], $sopts[ 33 ] );
    $listURL = "/accounts/" . $account->data[ 'id' ] . "/lists/" . $sopts[ 34 ];
    $list = $account->loadFromUrl( $listURL );

    # create a subscriber
    $params = array(
        'email' => $email,
        'ip_address' => $_SERVER[ 'REMOTE_ADDR' ],
        'misc_notes' => 'Added by Modal Survey',
        'name' => $name
    );
	if ( ! empty( $mv ) ) {
		$params[ 'custom_fields' ] = $mv;
	}
    $subscribers = $list->subscribers;
    $new_subscriber = $subscribers->create( $params );
	$result = true;

} catch(AWeberAPIException $exc) {
	if ( $exc->message == "email: Subscriber already subscribed and has not confirmed." ) {
		$result = true;
	}
	else {
		$error = "AWeberAPIException: $exc->message";
	}
}
?>