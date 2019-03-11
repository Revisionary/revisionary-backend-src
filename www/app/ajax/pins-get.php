<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
$device_ID = request('device_ID');
$currentUserID = currentUserID();
$pins = array();


// Are they numbers?
if ( !is_numeric($device_ID) )
	return;


// DO THE SECURITY CHECKS !!!
// a. Current user can access this pin?



// If not logged in
if ( !userloggedIn() ) $status = "not-logged-in";


// If device is not exist
else if ( !User::ID()->canAccess($device_ID, "device") ) {

	$status = "no-access";

}


// Get the pins
else {

	// Get pins from this device
	$db->where('device_ID', $device_ID);

	// Hide private pins to other people
	$db->where ("(user_ID = ".$currentUserID." or (user_ID != ".$currentUserID." and pin_private = 0))");

	// Get the pin data
	$pins = $db->get('pins pin', null, '
		pin.pin_ID,
		pin.pin_complete,
		pin.pin_private,
		pin.pin_type,
		pin.pin_element_index,
		pin.pin_modification_type,
		pin.pin_modification,
		pin.pin_modification_original,
		pin.pin_css,
		pin.pin_x,
		pin.pin_y,
		pin.user_ID
	');

	$status = $pins ? "Pins received" : "No Pins Found";

}



// CREATE THE RESPONSE
$data = array(

	'status' => $status,
	//'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'device_ID' => $device_ID

);

die(json_encode(array(
  'data' => $data,
  'pins' => $pins
)));
