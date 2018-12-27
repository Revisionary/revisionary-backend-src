<?php


// SECURITY CHECKS

// If not logged in, go login page
if ( !userloggedIn() ) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


// If no project specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects'));
	die();
}


// Get the project ID
$project_ID = $_url[1];


// If the specified project doesn't exist, go projects page
$project = Project::ID($project_ID)->getInfo("project_ID, user_ID", true);
if ( !$project ) {
	header('Location: '.site_url('projects'));
	die();
}



// Get the order
$order = get('order');

// Screen Filter
$screenFilter = get('screen');

// Category Filter
$catFilter = isset($_url[2]) ? $_url[2] : '';



// PAGES DATA MODEL
$dataType = "page";
$allMyPagesList = User::ID()->getMy("pages", $catFilter, $order, $project_ID, null, true);
$theCategorizedData = categorize($allMyPagesList, $dataType);
//die_to_print($theCategorizedData);



// MY PAGES IN THIS PROJECT
$allMyPages = $thePreparedData;
//die_to_print($allMyPages);


// MY DEVICES IN THIS PROJECT
$allMyDevices = $devices;
//die_to_print($allMyDevices);



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
if ($allMyPages && $allMyDevices) {

	$db->join("devices d", "pin.device_ID = d.device_ID", "LEFT");

	$device_IDs = array_column($allMyDevices, "device_ID"); //print_r($page_IDs);
	$db->where('d.device_ID', $device_IDs, 'IN');
	$allMyPins = $db->get('pins pin', null, "pin.pin_type, pin.pin_private, pin.user_ID, d.device_ID");
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



// ADD NEW SCREEN
if (
	is_numeric(get('new_screen'))
	&& is_numeric(get('page_ID'))
	// && get('nonce') == $_SESSION["new_screen_nonce"] !!! Disable the nonce check for now!
) {


	// Add the Devices
	$device_ID = Device::ID()->addNew(
		get('page_ID'),
		array(get('new_screen')),
		request('page_width') != "" ? request('page_width') : null,
		request('page_height') != "" ? request('page_height') : null
	);

	// Check the result
	if(!$device_ID) {
		header('Location: '.site_url('projects?adddeviceerror')); // If unsuccessful
		die();
	}



	// If successful, redirect to "Revise" page
	header('Location: '.site_url('revise/'.$device_ID));
	die();

}



// ADD NEW PAGE
if (
	request('add_new') == "true"
	// && request('add_new_nonce') == $_SESSION["add_new_nonce"] !!! Disable the nonce check for now!
) {



	// Add the Page
	$page_ID = Page::ID()->addNew(
		$project_ID,
		request('page-url'),
		request('page-name'),
		is_array(request('page_shares')) ? request('page_shares') : array(),
		request('category'),
		request('order')
	);

	// Check the result
	if(!$page_ID) {
		header('Location: '.site_url("project/$project_ID?addpageerror")); // If unsuccessful
		die();
	}



	// Add the Devices
	$device_ID = Device::ID()->addNew(
		$page_ID,
		is_array(request('screens')) ? request('screens') : array(), // Screen IDs array
		request('page_width') != "" ? request('page_width') : null,
		request('page_height') != "" ? request('page_height') : null
	);

	// Check the result
	if(!$device_ID) {
		header('Location: '.site_url("project/$project_ID?adddeviceerror")); // If unsuccessful
		die();
	}



	// If successful, redirect to "Revise" page
	header('Location: '.site_url('revise/'.$device_ID));
	die();

}



// Project last modified
$db->where('project_ID', $project_ID);
$db->orderBy('page_modified', 'desc');
$project_modified = $db->getValue("pages", "page_modified");



// Detect the available screens
$available_screens = array();
foreach($theCategorizedData as $categories) {

	if ( isset($categories['theData']) ) {

		foreach($categories['theData'] as $page) {
			foreach ($page['devicesData'] as $device) {

				$available_screens[$device['screen_cat_ID']] = array(
					"screen_cat_ID" => $device['screen_cat_ID'],
					"screen_cat_name" => $device['screen_cat_name'],
					"screen_cat_icon" => $device['screen_cat_icon']
				);

			}
		}

	}

}



// PROJECT INFO
$projectInfo = Project::ID($project_ID)->getInfo(null, true);
//die_to_print($projectInfo);


// CATEGORY INFO
$categories = User::ID()->getCategories($dataType, $order, $project_ID);
//die_to_print($categories);


// SCREEN INFO
$screen_data = User::ID()->getScreenData();
//die_to_print($screen_data);



// Additional Scripts and Styles
$additionalCSS = [
	'vendor/jquery.mCustomScrollbar.css'
];

$additionalHeadJS = [
	'process.js',
	'vendor/jquery.mCustomScrollbar.concat.min.js',
	'vendor/jquery.sortable.min.js',
	'common.js',
	'block.js'
];

$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js'
];


// Generate new nonce for add new screens
$_SESSION["new_screen_nonce"] = uniqid(mt_rand(), true);


$page_title = $projectInfo['project_name']." Project - Revisionary App";

if ($catFilter == "archived" || $catFilter == "deleted")
	$page_title = ucfirst($catFilter)." Pages - Revisionary App";

require view('modules/categorized_blocks');