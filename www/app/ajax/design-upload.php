<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;






// // CREATE THE RESPONSE
// die(json_encode(array(
// 	'status' => print_r($_REQUEST, true),
// 	'nonce' => request('nonce')
// 	//'S_nonce' => $_SESSION['pin_nonce'],
// )));



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


// File check
if (
	!isset($_FILES['design-upload']['name'])
	|| !isset($_FILES['design-upload']['tmp_name'])
	|| !is_uploaded_file($_FILES['design-upload']['tmp_name'])
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
if ($_FILES['design-upload']['size'] > 15000000) {

	$status = "large-file";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// File info
$image = $_FILES['design-upload']['name'];
$temp_file_location = $_FILES['design-upload']['tmp_name'];	
$image_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));


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



// PROJECT WORKS
$project_ID = is_numeric(request('project_ID')) ? intval(request('project_ID')) : request('project_ID');
$projectData = Project::ID($project_ID);

if ($project_ID != "new" && is_numeric($project_ID) && !$projectData) {


	$status = "wrong-project";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'project_ID' => $project_ID,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));


}



// PAGE WORKS
$page_name = request('page-name');
$page_ID = Page::ID('new')->addNew($project_ID, 'image', $page_name);
if (!$page_ID) {
	
	
	$status = "page-not-created";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'project_ID' => $project_ID,
		'page_ID' => $page_ID,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));


}



// PHASE WORKS
$phase_ID = Phase::ID('new')->addNew($page_ID, true);
if (!$phase_ID) {
	
	
	$status = "phase-not-created";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'project_ID' => $project_ID,
		'page_ID' => $page_ID,
		'phase_ID' => $phase_ID,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));


}



// DEVICE WORKS
list($width, $height) = getimagesize($temp_file_location);
$device_ID = Device::ID('new')->addNew(
	$phase_ID, 
	request('screens'),
	$width,
	$height
);
if (!$device_ID) {


	$status = "device-not-created";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'project_ID' => $project_ID,
		'page_ID' => $page_ID,
		'phase_ID' => $phase_ID,
		'device_ID' => $device_ID,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));


}




// Image Info
$image_name = "device-$device_ID.$image_extension";
$deviceData = Device::ID($device_ID);
$image_location = $deviceData->getImage();



// Select file to move
$file = new File($temp_file_location);


// Upload
$result = $file->upload($image_location, "local");
if ( !$result ) {

	$status = "not-uploaded";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'project_ID' => $project_ID,
		'page_ID' => $page_ID,
		'phase_ID' => $phase_ID,
		'device_ID' => $device_ID,
		'image_name' => $image_name,
		'image_location' => $image_location
	)));

}






// Site log
//$log->info("User #$user_ID Changed Avatar: '$image_name'");



$status = "success";

// CREATE THE RESPONSE
die(json_encode(array(
	'status' => $status,
	//'status' => print_r($_REQUEST, true),
	'project_ID' => $project_ID,
	'page_ID' => $page_ID,
	'phase_ID' => $phase_ID,
	'device_ID' => $device_ID,
	'image_name' => $image_name,
	'image_location' => $image_location,
	'files' => $_FILES
)));