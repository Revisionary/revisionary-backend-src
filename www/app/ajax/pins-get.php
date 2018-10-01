<?php

$status = "initiated";


// NONCE CHECK
if ( request("nonce") !== $_SESSION["pin_nonce"] )
	return;


// Get the pin info
$version_ID = request('version_ID');


// Are they numbers?
if ( !is_numeric($version_ID) )
	return;



// DO THE SECURITY CHECKS !!!
// a. Current user can access this pin?



// Get pins from this version
$db->where('version_ID', $version_ID);

// Hide private pins to other people
$db->where ("(user_ID = ".currentUserID()." or (user_ID != ".currentUserID()." and pin_private = 0))");

// Get the pin data
$pins = $db->get('pins pin', null, 'pin.pin_ID, pin.pin_complete, pin.pin_created, pin.pin_element_index, pin.pin_modification, pin.pin_modification_type, pin.pin_private, pin.pin_type, pin.pin_x, pin.pin_y, pin.user_ID, pin.version_ID');

$status = $pins ? "Pins received" : "No Pins Found";


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	'S_nonce' => $_SESSION['pin_nonce'],
	'version_ID' => $version_ID

);

echo json_encode(array(
  'data' => $data,
  'pins' => $pins
));
