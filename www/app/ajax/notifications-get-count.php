<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;



// If not logged in
if ( !userloggedIn() ) {

	$status = "not-logged-in";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}



$count = Notification::ID()->getCount();
$status = $count > 0 ? "new-notifications" : "no-new";



// CREATE THE RESPONSE
die(json_encode(array(
	'status' => $status,
	'newcount' => $count,
	'nonce' => request('nonce')
	//'S_nonce' => $_SESSION['pin_nonce'],
)));
