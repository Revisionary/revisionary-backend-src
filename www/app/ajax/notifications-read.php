<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;



// If not logged in
if ( !userloggedIn() ) $status = "not-logged-in";



$allRead = Notification::ID()->readAll();
if ($allRead) $status = "success";



// CREATE THE RESPONSE
die(json_encode(array(
	'status' => $status,
	'nonce' => request('nonce')
	//'S_nonce' => $_SESSION['pin_nonce'],
)));
