<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
$message = trim(request('message'));
$newPin = request('newPin') == "yes" ? "yes" : "no";


// Pin ID validation
if ( !is_numeric(request('pin_ID')) || empty($message) ) return;
$pin_ID = intval(request('pin_ID'));



// DO THE SECURITY CHECKS !!!
// a. Current user can access this pin?



// Get the comments
$comment_sent = Pin::ID($pin_ID)->addComment($message, $newPin);
if ($comment_sent) $status = "Comment sent: $message";


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID,
	'message' => $message

);

echo json_encode(array(
  'data' => $data
));
