<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Pin ID
if ( !is_numeric(request('pin_ID')) ) return;
$pin_ID = intval(request('pin_ID'));



// DO THE SECURITY CHECKS !!!
// a. Current user can access this pin?



// Get the comments
$comments = Pin::ID($pin_ID)->comments();

$status = $comments ? "Comments received" : "No Comments Found";


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID

);

echo json_encode(array(
  'data' => $data,
  'comments' => $comments
));
