<?php

$status = "initiated";


// NONCE CHECK
if ( request("nonce") !== $_SESSION["pin_nonce"] )
	return;


// Get the pin info
$pin_version_ID = intval(request('pin_version_ID'));
$pin_type = request('pin_type');
$pin_private = boolval(request('pin_private'));
$pin_x = intval(request('pin_x'));
$pin_y = intval(request('pin_y'));
$pin_element_index = request('pin_element_index');
$pin_midification_type = request('pin_modification_type');


// Are they numbers?
if ( !is_numeric($pin_version_ID) || is_numeric($pin_type) || !is_numeric($pin_y) )
	return;


// DO THE SECURITY CHECKS !!!
// a. Current user can add this pin?



// Add the pin
$pin_ID = Pin::ID()->addNew(
	$pin_version_ID,
	$pin_type,
	$pin_private,
	$pin_x,
	$pin_y,
	$pin_element_index,
	$pin_midification_type
);

if ($pin_ID) $status = "Added: $pin_ID";



// CREATE THE RESPONSE
$data = array(

	'status' => $status,
	'nonce' => request('nonce'),
	'S_nonce' => $_SESSION['pin_nonce'],
	'pin_x' => $pin_x,
	'pin_y' => $pin_y,
	'real_pin_ID' => $pin_ID

);

echo json_encode(array(
  'data' => $data
));
