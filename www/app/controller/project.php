<?php


// Get the project ID
$project_ID = $_url[1];

// Get the order
$order = isset($_GET['order']) ? $_GET['order'] : '';

// Category Filter
$catFilter = isset($_url[2]) ? $_url[2] : '';

// Device Filter
$deviceFilter = get('device');







// SECURITY CHECKS

// If not logged in, go login page
if (!userloggedIn()) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


// If no project specified or not numeric, go projects page
if ( !isset($project_ID) || !is_numeric($project_ID) ) {
	header('Location: '.site_url('projects'));
	die();
}


// If project doesn't exist
$db->where("project_ID", $project_ID);
$project = $db->getOne("projects", "project_ID, user_ID");
if ( !$project ) {
	header('Location: '.site_url('projects'));
	die();
}










// PAGES DATA MODEL
$dataType = "page";
require model('projects');
$theCategorizedData = the_data();
//echo "<pre>"; print_r(array_column($theCategorizedData, 'theData')); exit();
//echo "<pre>"; print_r($thePreparedData); exit();



// MY PAGES IN THIS PROJECT
$allMyPages = $thePreparedData;
//echo "<pre>"; print_r( $allMyPages ); die();



// PROJECT SHARES QUERY

// Exlude other types
$db->where('share_type', 'project');
$db->where('shared_object_ID', $project_ID);
$projectShares = $db->get('shares', null, "share_to, sharer_user_ID");
//echo "<pre>"; print_r($projectShares); echo "</pre>"; die();


// If project doesn't belong to me and if no page belong to me
if (
	$project['user_ID'] != currentUserID() && // If the project isn't belong to me
	array_search(currentUserID(), array_column($projectShares, 'share_to')) === false && // And, if the project isn't shared to me
	count($allMyPages) == 0 // And, if there is no my page in it
) {

	// Redirect to "Projects" page
	header('Location: '.site_url('projects'));
	die();

}



// COUNT ALL THE PINS
$totalLivePinCount = $totalStandardPinCount = $totalPrivatePinCount = 0;

// Bring all the pins
$allMyPins = array();
if ($allMyPages) {

	$db->join("versions ver", "pin.version_ID = ver.version_ID", "LEFT");

	$page_IDs = array_column($allMyPages, "page_ID"); //print_r($page_IDs);
	$db->where('ver.page_ID', $page_IDs, 'IN');
	$allMyPins = $db->get('pins pin', null, "pin.pin_type, pin.pin_private, pin.user_ID, ver.page_ID");
	//var_dump($allMyPins); die();


	if ($allMyPins) {

		$totalLivePinCount = count(array_filter($allMyPins, function($value) {

			return $value['pin_type'] == "live" && $value['pin_private'] == "0";

		}));
		$totalStandardPinCount = count(array_filter($allMyPins, function($value) {

			return $value['pin_type'] == "standard" && $value['pin_private'] == "0";

		}));
		$totalPrivatePinCount = count(array_filter($allMyPins, function($value) {

			return ($value['pin_type'] == "live" || $value['pin_type'] == "standard") && $value['pin_private'] == "1" && $value['user_ID'] == currentUserID();

		}));

	}


}



// ADD NEW DEVICE
if (
	is_numeric(get('new_device'))
	&& is_numeric(get('page_ID'))
	// && get('nonce') == $_SESSION["new_device_nonce"] !!! Disable the nonce check for now!
) {



	// Check if custom device sizes exist
	if ( is_numeric(get('page_width')) && is_numeric(get('page_height')) ) {

		// Add the device with dimensions
		$page_ID = Device::ID()->addNew(
			get('new_device'),
			get('page_ID'),
			null,
			null,
			null,
			get('page_width'),
			get('page_height')
		);

	} else {

		// Add the device
		$page_ID = Device::ID()->addNew(
			get('new_device'),
			get('page_ID')
		);

	}



	// Check the result
	if(!$page_ID) {
		header('Location: '.site_url('projects?adddeviceerror')); // If unsuccessful
		die();
	}


	// If successful, redirect to "Revise" page
	header('Location: '.site_url('revise/'.$page_ID));
	die();

}



// ADD NEW PAGE
if (
	post('add_new') == "true"
	// && post('add_new_nonce') == $_SESSION["add_new_nonce"] !!! Disable the nonce check for now!
) {


	// Add the pages
	$parent_page_ID = Page::ID()->addNew(
		post('page-url'),
		post('page-name'),
		post('project_ID'),
		post('category'),
		post('order'),
		is_array(post('devices')) ? post('devices') : array(), // Device IDs array
		is_array(post('page_shares')) ? post('page_shares') : array(),
		post('page-width') != "" ? post('page-width') : null,
		post('page-height') != "" ? post('page-height') : null
	);


	// Check the result
	if(!$parent_page_ID) {
		header('Location: '.site_url('project/'.$project_ID.'?addpageerror')); // If unsuccessful
		die();
	}


	// If successful, redirect to "Revise" page
	header('Location: '.site_url('revise/'.$parent_page_ID));
	die();

}



// Project last modified
$db->where('project_ID', $project_ID);
$db->orderBy('page_modified', 'desc');
$project_modified = $db->getValue("pages", "page_modified");


// Detect the available devices
$available_devices = array();
foreach($theCategorizedData as $categories) {

	foreach($categories['theData'] as $page) {

		$available_devices[$page['device_cat_ID']] = array(
			"device_cat_ID" => $page['device_cat_ID'],
			"device_cat_name" => $page['device_cat_name'],
			"device_cat_icon" => $page['device_cat_icon']
		);

		foreach ($page['subPageData'] as $subPage) {

			$available_devices[$subPage['device_cat_ID']] = array(
				"device_cat_ID" => $subPage['device_cat_ID'],
				"device_cat_name" => $subPage['device_cat_name'],
				"device_cat_icon" => $subPage['device_cat_icon']
			);

		}

	}

}



// DEVICE INFO

// Bring the device category info
$db->join("device_categories d_cat", "d.device_cat_ID = d_cat.device_cat_ID", "LEFT");

$db->where('d.device_user_ID', 1); // !!! ?

$db->orderBy('d_cat.device_cat_order', 'asc');
$db->orderBy(' d.device_order', 'asc');
$devices = $db->get('devices d');


// Prepare the devices data
$device_data = [];
foreach ($devices as $device) {

	if ( !isset($device_data[$device['device_cat_ID']]['devices']) ) {

		$device_data[$device['device_cat_ID']] = array(
			'device_cat_icon' => $device['device_cat_icon'],
			'device_cat_name' => $device['device_cat_name'],
			'devices' => array(),
		);

	}

	$device_data[$device['device_cat_ID']]['devices'][$device["device_ID"]] = $device;

}

//echo "<pre>"; print_r($device_data);
//echo "<pre>"; print_r($devices); exit();




// Additional Scripts and Styles
$additionalCSS = [
	'jquery.mCustomScrollbar.css'
];

$additionalHeadJS = [
	'process.js',
	'vendor/jquery.sortable.min.js',
	'common.js',
	'block.js'
];

$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js'
];


// Generate new nonce for add new modals
$_SESSION["add_new_nonce"] = uniqid(mt_rand(), true);


// Generate new nonce for add new devices
$_SESSION["new_device_nonce"] = uniqid(mt_rand(), true);


$page_title = Project::ID($_url[1])->projectName." Project - Revisionary App";
require view('modules/categorized_blocks');