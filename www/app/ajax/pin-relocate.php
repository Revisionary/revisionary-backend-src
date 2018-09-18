<?php

$status = "initiated";


// NONCE CHECK
if ( request("nonce") !== $_SESSION["pin_nonce"] )
	return;


// Get the pin info
$pin_ID = request('pin_ID');
$pin_x = request('pin_x');
$pin_y = request('pin_y');


// Are they numbers?
if ( !is_numeric($pin_ID) || !is_numeric($pin_x) || !is_numeric($pin_y) )
	return;


// DO THE SECURITY CHECKS!
// a. Current user can edit this pin?



// Update the pin
$pin_updated = Pin::ID($pin_ID)->reLocate($pin_x, $pin_y);

if ($pin_updated) $status = "Pin relocated: $pin_ID";


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	'S_nonce' => $_SESSION['pin_nonce'],
	'pin_x' => $pin_x,
	'pin_y' => $pin_y,
	'pin_ID' => $pin_ID

);

echo json_encode(array(
  'data' => $data
));
