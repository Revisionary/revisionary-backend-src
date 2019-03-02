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


$offset = request("offset");


// Offset numerical?
if ( !is_numeric($offset) || $offset < 0 ) {

	$status = "wrong-offset";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}



$notifications = Notification::ID()->getHTML($offset);
if ($notifications) $status = "success";



// CREATE THE RESPONSE
die(json_encode(array(
	'status' => $status,
	'notifications' => $notifications,
	'nonce' => request('nonce')
	//'S_nonce' => $_SESSION['pin_nonce'],
)));
