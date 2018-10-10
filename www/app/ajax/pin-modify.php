<?php

$status = "initiated";


// NONCE CHECK
if ( request("nonce") !== $_SESSION["pin_nonce"] )
	return;


// Get the pin info
$pin_ID = request('pin_ID');
$modification = request('modification');
$modification_type = request('modification_type') == "html" ? "html" : "image"; // !!!


// Null detection
if ($modification_type == "image" && $modification == "null")
	$modification = null;


// Are they numbers?
if ( !is_numeric($pin_ID) )
	return;


// DO THE SECURITY CHECKS !!!
// a. Current user can edit this pin?



// Modify the pin
$pin_modified = Pin::ID($pin_ID)->modify($modification, $modification_type);

if ($pin_modified) $status = "Pin Modified: $pin_ID";


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID

);

echo json_encode(array(
  'data' => $data
));
