<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
$pin_ID = request('pin_ID');
$comment_ID = trim(request('comment_ID'));


// Are they numbers?
if ( !is_numeric($pin_ID) || !is_numeric($comment_ID) )
	return;


// DO THE SECURITY CHECKS !!!
// a. Current user can access this pin?



// Get the comments
$comment_deleted = Pin::ID($pin_ID)->deleteComment($comment_ID);
if ($comment_deleted) $status = "Comment #$comment_ID DELETED";


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID,
	'comment_ID' => $comment_ID

);

echo json_encode(array(
  'data' => $data
));
