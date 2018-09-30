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
$pins = $db->get('pins pin');

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
