<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;



// If not logged in
if ( !userloggedIn() ) $status = "not-logged-in";



$count = Notification::ID()->getCount();
if ($count) $status = "success";




// CREATE THE RESPONSE
$data = array(

	'status' => $status,
	'nonce' => request('nonce')
	//'S_nonce' => $_SESSION['pin_nonce'],

);

die(json_encode(array(
  'data' => $data,
  'newcount' => $count
)));
