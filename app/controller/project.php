<?php

// SECURITY CHECKS

// If not logged in, go login page
if (!userloggedIn()) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


// If no project specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects'));
	die();
}


// If project doesn't exist
$db->where("project_ID", $_url[1]);
$project = $db->getOne("projects", "project_ID, user_ID");
if ( !$project ) {
	header('Location: '.site_url('projects'));
	die();
}



// PROJECT SHARES QUERY

// Exlude other types
$db->where('share_type', 'project');

// Is this project?
$db->where('shared_object_ID', $_url[1]);

// Project shares data
$projectShares = $db->get('shares', null, "share_to, sharer_user_ID");


// Get the project ID
$project_ID = $_url[1];


// Get the order
$order = isset($_GET['order']) ? $_GET['order'] : '';


// Category Filter
$catFilter = isset($_url[2]) ? $_url[2] : '';


// Device Filter
$deviceFilter = get('device');


// PAGES DATA MODEL
require model('project');
$theCategorizedData = the_data();
$dataType = "page";


//print_r(array_column($theCategorizedData, 'theData')); exit();
//print_r($theCategorizedData); exit();


// If project doesn't belong to me
if (
	$project['user_ID'] != currentUserID() &&
	array_search(currentUserID(), array_column($projectShares, 'share_to')) === false
) {


	// Collect all the pages data
	$allPages = array();
	foreach ($theCategorizedData as $category) {
		foreach ($category['theData'] as $page) {

			$allPages[] = $page;

		}
	}


	// Check if any my page
	$myPages = false;
	if (
		array_search(currentUserID(), array_column($allPages, 'user_ID')) !== false
	) $myPages = true;


	// Check if any my shared page
	$sharedPages = false;
	if (
		array_search(currentUserID(), array_column($allPages, 'share_to')) !== false
	) $sharedPages = true;


	// Check if here is archived or deleted pages
	$archivePages = false;
	if ($catFilter == "archived" || $catFilter == "deleted")
		$archivePages = true;


	// Otherwise !!!
	if (!$myPages && !$sharedPages && !$archivePages) {

		header('Location: '.site_url('projects'));
		die();

	}

}


// Project last modified
$db->where('project_ID', $project_ID);
$db->orderBy('page_modified', 'desc');
$project_modified = $db->getValue("pages", "page_modified");


// Only Page Data
$onlyPageData = array();
$allPageData = array_values(array_filter(array_column($theCategorizedData, 'theData')));
foreach($allPageData as $page) {
	foreach ($page as $page) {
		$onlyPageData[] = $page;
	}
}


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


// Additional JavaScripts
$additionalHeadJS = [
	'vendor/jquery.sortable.min.js',
	'block.js'
];

$page_title = Project::ID($_url[1])->projectName." Project - Revisionary App";
require view('dynamic/categorized_blocks');