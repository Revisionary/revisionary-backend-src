<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;



// If not logged in
if ( !userloggedIn() ) {

	$status = "not-logged-in";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


$user_ID = getUserInfo()['userLevelID'] == 1 && is_numeric(get('user_ID')) ? intval(get('user_ID')) : currentUserID();
$userInfo = getUserInfo($user_ID);



// File check
if (
	!isset($_FILES['image']['name'])
	|| !isset($_FILES['image']['tmp_name'])
	|| !is_uploaded_file($_FILES['image']['tmp_name'])
) {

	$status = "no-file";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}



// Size check
if ($_FILES['image']['size'] > 3145728) {

	$status = "large-file";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// File info
$directory = cache."/users/user-$user_ID/";
$image = $_FILES['image']['name'];
$temporary = $_FILES['image']['tmp_name'];
$image_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
$image_name = generateRandomString().".".$image_extension;
$image_path = $directory.$image_name;


// Extension check
if ( !in_array($image_extension, array('jpeg', 'jpg', 'png')) ) {

	$status = "invalid-extension";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}



// Create user directory if not exist
if (!file_exists($directory)) {
	mkdir($directory);
}



// Move the file
if(move_uploaded_file($temporary, $image_path)) {


	// Delete the old one
	$old_image_name = $userInfo['userPic'];
	$old_image = $directory.$old_image_name;
	if ( $old_image_name != null && file_exists($old_image) ) unlink($old_image);


	// Update on DB
	User::ID($user_ID)->edit('user_picture', $image_name);


	$status = "success";

} else {

	$status = "not-moved";

}


die(json_encode(array(
	'status' => $status,
	'user_ID' => $user_ID,
	'new_url' => cache_url("users/user-$user_ID/$image_name"),
	'files' => $_FILES
)));