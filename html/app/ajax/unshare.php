<?php

$data = array();
$status = "initiated";


// NONCE CHECK
if ( post("nonce") !== $_SESSION["js_nonce"] ) {

	$data['data']['status'] = "security-error";
	$data['data']['posted_nonce'] = post("nonce");
	$data['data']['session_nonce'] = $_SESSION["js_nonce"];


	// CREATE THE ERROR RESPONSE
	echo json_encode(array(
	  'data' => $data
	));

	return;
}


// Valid object ID?
if ( !is_numeric(post("object_ID")) ) {

	$data['data']['status'] = "invalid-obj-id";


	// CREATE THE ERROR RESPONSE
	echo json_encode(array(
	  'data' => $data
	));

	return;
}


// Valid user ID?
if ( !is_numeric(post("user_ID")) && !filter_var(post("user_ID"), FILTER_VALIDATE_EMAIL) ) {

	$data['data']['status'] = "invalid-user-id";
	$data['data']['user_ID'] = post("user_ID");


	// CREATE THE ERROR RESPONSE
	echo json_encode(array(
	  'data' => $data
	));

	return;
}







// Remove share from DB
$db->where('shared_object_ID', post("object_ID"));
$db->where('share_to', post("user_ID"));
$db->where('sharer_user_ID', currentUserID());
if( $db->delete('shares') ) {

	$data['data']['status'] = "unshared";


}



// CREATE THE RESPONSE
echo json_encode(array(
  'data' => $data
));
