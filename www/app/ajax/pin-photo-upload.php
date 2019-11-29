<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;



// If not logged in
if ( !userLoggedIn() ) {

	$status = "not-logged-in";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}
$user_ID = currentUserID();


// Invalid pin_ID
if ( !is_numeric(get('pin_ID')) ) {

	$status = "invalid";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}
$pin_ID = intval(get('pin_ID'));


// Check pin
$pinData = Pin::ID($pin_ID);
if ( !$pinData ) {

	$status = "no-pin";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}
$pinInfo = $pinData->getInfo();


// Check pin modification type
if ( $pinInfo['pin_modification_type'] != "image" ) {

	$status = "invalid-pin-type";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


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
if ($_FILES['image']['size'] > 15000000) {

	$status = "large-file";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// File info
$image = $_FILES['image']['name'];
$temp_file_location = $_FILES['image']['tmp_name'];	
$image_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
$image_name = generateRandomString().".".$image_extension;


// Extension check
if ( !in_array($image_extension, array('jpeg', 'jpg', 'png', 'gif')) ) {

	$status = "invalid-extension";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// Resize
resize_image($temp_file_location, 1920, 1920);


// Select file to upload
$file = new File($temp_file_location);


// Rename if exists
while ( $file->fileExists("pin-images/pin-$pin_ID/$image_name") )
	$image_name = generateRandomString().".".$image_extension;


// Upload
$pin_image_url = $file->upload("pin-images/pin-$pin_ID/$image_name", "s3");
if ( !$pin_image_url ) {

	$status = "not-uploaded";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}

// New local URL
$new_url = is_string($pin_image_url) && strpos($pin_image_url, '://') !== false ? $pin_image_url : cache_url("pin-images/pin-$pin_ID/$image_name");


// Modify the pin
$pin_modified = $pinData->modify($new_url);
if ( !$pin_modified ) {

	$status = "not-modified";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// Delete the old Image
$old_image = $pinInfo['pin_modification'];
if ( strpos($old_image, '://') !== false ) { // On S3

	$old_image_path = substr(parse_url($old_image, PHP_URL_PATH), 1);

	// Delete old ımage
	$file = new File($old_image_path, "s3");
	$file->delete();

}




$status = "success";


// Site log
$log->info("User #$user_ID Uploaded a Pin #$pin_ID image: '$new_url'");


// INVALIDATE THE CACHES
$cache->deleteKeysByTag('pins');




die(json_encode(array(
	'status' => $status,
	'pin_ID' => $pin_ID,
	'user_ID' => $user_ID,
	'new_url' => $new_url,
	'files' => $_FILES
)));