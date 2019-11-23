<?php


// SECURITY CHECKS

// If not logged in, go login page
if ( !$User ) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


// If no project specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects?invalid'));
	die();
}


// Get the project ID
$project_ID = intval($_url[1]);

// If the specified project doesn't exist, go projects page
$project = Project::ID($project_ID);
if ( !$project ) {
	header('Location: '.site_url('projects?projectdoesntexist'));
	die();
}
$projectInfo = $project->getInfo();



// Data Type
$dataType = "page";

// Get the order
$order = get('order');

// Screen Filter
$screenFilter = get('screen');

// Category Filter
$catFilter = isset($_url[2]) ? $_url[2] : '';


// ALL THE PAGES
$pages = $User->getPages();
//die_to_print($pages);



// PAGES IN THIS PROJECT
$allMyPages = $User->getPages($project_ID, null, '');
//die_to_print($allMyPages);



// PROJECT SHARES QUERY:
// Exlude other types
$db->where('share_type', 'project');
$db->where('shared_object_ID', $project_ID);
$projectShares = $db->get('shares', null, "share_to, sharer_user_ID");
//die_to_print($projectShares);


// Check project access
$projectSharedMe = array_search(currentUserID(), array_column($projectShares, 'share_to')) !== false;

// If project doesn't belong to me and if no page belong to me
if (
	$projectInfo['user_ID'] != currentUserID() // If the project isn't belong to me
	&& !$projectSharedMe // And, if the project isn't shared to me
	&& count( $allMyPages ) === 0 // And, if there is no my page in it
	&& $catFilter != "mine"
	&& $User->getInfo('user_level_ID') != 1
) {

	// Redirect to "Projects" page
	header('Location: '.site_url('projects?noaccess'));
	die();

}



// Project Last Modified
$pagesOrderedModification = $allMyPages;
array_multisort(array_column($pagesOrderedModification, 'page_modified'), SORT_DESC, $pagesOrderedModification);
$project_modified = reset($pagesOrderedModification)['page_modified'];



// PINS IN THIS PROJECT
$allMyPins = $User->getPins(null, null, null, $project_ID);
//die_to_print($allMyPins);


// Count all the pin types
$totalLivePinCount = $totalStylePinCount = $totalPrivatePinCount = $totalCompletePinCount = 0;

if ( is_array($allMyPins) ) {

	$completePins = array_filter($allMyPins, function($pin) {

		return $pin['pin_complete'] == "1";

	});

	$inCompletePins = array_filter($allMyPins, function($pin) {

		return $pin['pin_complete'] == "0";

	});

	$totalCompletePinCount = count($completePins);

	$totalLivePinCount = count(array_filter($inCompletePins, function($pin) {

		return $pin['pin_type'] == "live" && $pin['pin_private'] == "0";

	}));

	$totalStylePinCount = count(array_filter($inCompletePins, function($pin) {

		return $pin['pin_type'] == "style" && $pin['pin_private'] == "0";

	}));

	$totalPrivatePinCount = count(array_filter($inCompletePins, function($pin) {

		return $pin['pin_private'] == "1" && $pin['user_ID'] == currentUserID();

	}));



	// Modification Check
	$totalContentChangeCount = count(array_filter($inCompletePins, function($pin) {

		return $pin['pin_type'] == "live" && $pin['pin_modification'] != "";

	}));

	$totalStyleChangeCount = count(array_filter($inCompletePins, function($pin) {

		return $pin['pin_type'] == "live" && $pin['pin_css'] != "";

	}));

	$totalCompleteContentChangeCount = count(array_filter($completePins, function($pin) {

		return $pin['pin_type'] == "live" && $pin['pin_modification'] != "";

	}));

	$totalCompleteStyleChangeCount = count(array_filter($completePins, function($pin) {

		return $pin['pin_type'] == "live" && $pin['pin_css'] != "";

	}));


}

$inCompletePinsCount = $totalLivePinCount + $totalStylePinCount + $totalPrivatePinCount;
$completePinsCount = $totalCompletePinCount;



// Detect the available screens
$available_screens = array();
//die_to_print( $User->getDevices(null, null, $project_ID) );
foreach ($User->getDevices(null, null, $project_ID) as $device) {

	$available_screens[$device['screen_cat_ID']] = array(
		"screen_cat_ID" => $device['screen_cat_ID'],
		"screen_cat_name" => $device['screen_cat_name'],
		"screen_cat_icon" => $device['screen_cat_icon']
	);

}



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