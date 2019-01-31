<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
$pin_ID = request('pin_ID');
$complete = request('complete') == "complete" ? true : false;
$imageDataURL = request('imgDataURL') != "" ? request('imgDataURL') : "";


// Are they numbers?
if ( !is_numeric($pin_ID) )
	return;


// DO THE SECURITY CHECKS !!!
// a. Current user can edit this pin?



$pinData = Pin::ID($pin_ID);



// Complete/Incomplete the pin
$pin_completed = $complete ? $pinData->complete($imageDataURL) : $pinData->inComplete($imageDataURL);

if ($pin_completed) {

	$status = "Pin ".($complete ? "completed" : "incompleted").": $pin_ID";

}


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID,
	'imageDataURL' => $imageDataURL

);

echo json_encode(array(
  'data' => $data
));
