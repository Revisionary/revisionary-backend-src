<?php


// SECURITY CHECKS

// If not logged in, go login page
if ( !userloggedIn() ) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}

// Current user level ID
$currentUserLevel_ID = getUserInfo()['userLevelID'];


// If no project specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects?invalid'));
	die();
}


// Get the project ID
$project_ID = $_url[1];


// If the specified project doesn't exist, go projects page
$project = Project::ID($project_ID);
if ( !$project ) {
	header('Location: '.site_url('projects?projectdoesntexist'));
	die();
}


// PROJECT INFO
$projectInfo = $project->getInfo();
//die_to_print($projectInfo);




// Get the order
$order = get('order');

// Screen Filter
$screenFilter = get('screen');

// Category Filter
$catFilter = isset($_url[2]) ? $_url[2] : '';



// PAGES DATA MODEL
$dataType = "page";
$allMyPagesList = User::ID()->getMy("pages", $catFilter, $order, $project_ID, null, true);
//die_to_print($allMyPagesList);

$theCategorizedData = categorize($allMyPagesList, $dataType);
//die_to_print($theCategorizedData);



// MY PAGES IN THIS PROJECT
$allMyPages = $thePreparedData;
//die_to_print($allMyPages);


// MY DEVICES IN THIS PROJECT
$allMyVersions = $versions; // Comes globally from 'categorize.php'
//die_to_print($allMyVersions);


// MY DEVICES IN THIS PROJECT
$allMyDevices = $devices; // Comes globally from 'categorize.php'
//die_to_print($allMyDevices);



// PROJECT SHARES QUERY

// Exlude other types
$db->where('share_type', 'project');
$db->where('shared_object_ID', $project_ID);
$projectShares = $db->get('shares', null, "share_to, sharer_user_ID");
//echo "<pre>"; print_r($projectShares); echo "</pre>"; die();

$projectSharedMe = array_search(currentUserID(), array_column($projectShares, 'share_to')) !== false;

// If project doesn't belong to me and if no page belong to me
if (
	$projectInfo['user_ID'] != currentUserID() // If the project isn't belong to me
	&& !$projectSharedMe // And, if the project isn't shared to me
	&& count($allMyPagesList) == 0 // And, if there is no my page in it
	&& $catFilter != "mine"
	&& $currentUserLevel_ID != 1
) {

	// Redirect to "Projects" page
	header('Location: '.site_url('projects?noaccess'));
	die();

}



// COUNT ALL THE PINS !!!
$totalLivePinCount = $totalStandardPinCount = $totalPrivatePinCount = $totalCompletePinCount = 0;

$allMyPins = array();
if ($allMyPages && $allMyDevices) {

	$db->join("devices d", "pin.device_ID = d.device_ID", "LEFT");

	$device_IDs = array_column($allMyDevices, "device_ID"); //print_r($page_IDs);
	$db->where('d.device_ID', $device_IDs, 'IN');
	$allMyPins = $db->get('pins pin', null, "pin.pin_type, pin.pin_private, pin.pin_complete, pin.user_ID, d.device_ID");
	//die_to_print($allMyPins);


	if ($allMyPins) {

		$totalLivePinCount = count(array_filter($allMyPins, function($value) {

			return $value['pin_type'] == "live" && $value['pin_private'] == "0" && $value['pin_complete'] == "0";

		}));
		$totalStandardPinCount = count(array_filter($allMyPins, function($value) {

			return $value['pin_type'] == "standard" && $value['pin_private'] == "0" && $value['pin_complete'] == "0";

		}));
		$totalPrivatePinCount = count(array_filter($allMyPins, function($value) {

			return ($value['pin_type'] == "live" || $value['pin_type'] == "standard") && $value['pin_private'] == "1" && $value['user_ID'] == currentUserID() && $value['pin_complete'] == "0";

		}));
		$totalCompletePinCount = count(array_filter($allMyPins, function($value) {

			return $value['pin_complete'] == "1";

		}));

	}


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

			if ( isset($page['versionsData']) ) {

				foreach ($page['versionsData'] as $version) {

					if ( isset($version['devicesData']) ) {


						foreach ($version['devicesData'] as $device) {

							$available_screens[$device['screen_cat_ID']] = array(
								"screen_cat_ID" => $device['screen_cat_ID'],
								"screen_cat_name" => $device['screen_cat_name'],
								"screen_cat_icon" => $device['screen_cat_icon']
							);

						}


					}

				}

			}


		}

	}

}


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
	'vendor/jquery-sortable.js',
	'common.js',
	'block.js'
];

$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js'
];


// Generate new nonce for add new screens
//$_SESSION["new_screen_nonce"] = uniqid(mt_rand(), true);


$page_title = $projectInfo['project_name']." Project - Revisionary App";

if ($catFilter == "archived" || $catFilter == "deleted")
	$page_title = ucfirst($catFilter)." Pages - Revisionary App";

require view('modules/categorized_blocks');