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


// If the specified project doesn't exist, go projects page
$project = Project::ID($project_ID)->getInfo("project_ID, user_ID", true);
if ( !$project ) {
	header('Location: '.site_url('projects'));
	die();
}



// PAGES DATA MODEL
$dataType = "page";
$allMyPagesList = UserAccess::ID()->getMy("pages", $catFilter, $order, $project_ID, null, true);
//echo "<pre>"; print_r($allMyPagesList); exit();

$theCategorizedData = categorize($allMyPagesList, $dataType, $deviceFilter);
//echo "<pre>"; print_r(array_column($theCategorizedData, 'theData')); exit();



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
	$project['user_ID'] != currentUserID() // If the project isn't belong to me
	&& array_search(currentUserID(), array_column($projectShares, 'share_to')) === false // And, if the project isn't shared to me
	&& count($allMyPagesList) == 0 // And, if there is no my page in it
	&& $catFilter != "mine"
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
	request('add_new') == "true"
	// && request('add_new_nonce') == $_SESSION["add_new_nonce"] !!! Disable the nonce check for now!
) {


	// Add the pages
	$parent_page_ID = Page::ID()->addNew(
		request('page-url'),
		request('project_ID'),
		request('page-name'),
		request('category'),
		request('order'),
		is_array(request('devices')) ? request('devices') : array(), // Device IDs array
		is_array(request('page_shares')) ? request('page_shares') : array(),
		request('page-width') != "" ? request('page-width') : null,
		request('page-height') != "" ? request('page-height') : null
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



// PROJECT INFO
$projectInfo = Project::ID($project_ID)->getInfo(null, true);
//echo "<pre>"; print_r($projectInfo); exit();


// CATEGORY INFO
$categories = UserAccess::ID()->getCategories($dataType, $order, $project_ID);
//print_r($categories); exit;


// DEVICE INFO
$device_data = UserAccess::ID()->getDeviceData();
//echo "<pre>"; print_r($device_data); exit();



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


// Generate new nonce for add new devices
$_SESSION["new_device_nonce"] = uniqid(mt_rand(), true);


$page_title = $projectInfo['project_name']." Project - Revisionary App";
require view('modules/categorized_blocks');