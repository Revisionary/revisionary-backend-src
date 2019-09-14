<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
$phase_ID = request('phase_ID');
$device_ID = request('device_ID');
$user = User::ID();
$pins = array();



// Are they numbers?
if ( !is_numeric($device_ID) )
	return;



// If not logged in
if ( !$user ) $status = "not-logged-in";


// If device is not exist
else if ( !$user->canAccess($phase_ID, "phase") ) $status = "no-access";


// Get the pins
else {

	$pins = $user->getPins($phase_ID, $device_ID);
	$status = $pins ? "Pins received" : "No Pins Found";

}



// CREATE THE RESPONSE
$data = array(

	'status' => $status,
	//'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'phase_ID' => $phase_ID,
	'device_ID' => $device_ID

);

die(json_encode(array(
  'data' => $data,
  'pins' => $pins
)));
