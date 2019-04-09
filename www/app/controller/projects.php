<?php


// SECURITY CHECKS

// If not logged in, go login page
if ( !userloggedIn() ) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}



// ADD NEW PROJECT OR PAGE
if (
	request('add_new') == "true"
	&& request('page-url') != ""
	&& request('project_ID') != ""
	&& ( request('project_ID') == "new" || request('project_ID') == "autodetect" || is_numeric(request('project_ID')) )
	// && post('add_new_nonce') == $_SESSION["add_new_nonce"] !!! Disable the nonce check for now!
) {

	$project_ID = request('project_ID');
	$page_url = request('page-url');
	if ( request('pinmode') == "browse" ) $page_url = rawurldecode($page_url);


	// URL check
	if (!filter_var($page_url, FILTER_VALIDATE_URL)) {
		header('Location: '.site_url("projects?invalidurl")); // If unsuccessful
		die();
	}



	// Add the project
	$new_project = false;
	if ($project_ID == "new" || $project_ID == "autodetect") {

		$new_project = true;

		$project_ID = Project::ID($project_ID)->addNew(
			request('project-name'),
			is_array(request('project_shares')) ? request('project_shares') : array(),
			request('category'),
			request('order'),
			request('page-url')
		);

	}

	// Check the result
	if(!$project_ID || !is_numeric($project_ID)) {
		header('Location: '.site_url('projects?addprojecterror')); // If unsuccessful
		die();
	}



	// Add the Page
	$page_ID = Page::ID()->addNew(
		$project_ID,
		request('page-url'),
		request('page-name'),
		is_array(request('page_shares')) ? request('page_shares') : array(),
		$new_project ? 0 : request('category'),
		$new_project ? 0 : request('order')
	);

	// Check the result
	if(!$page_ID) {
		header('Location: '.site_url("project/$project_ID?addpageerror")); // If unsuccessful
		die();
	}



	// Add a version
	$version_ID = Version::ID()->addNew(
		$page_ID
	);

	// Check the result
	if(!$version_ID) {
		header('Location: '.site_url("project/$project_ID?addversionerror")); // If unsuccessful
		die();
	}



	// Add the Devices
	$device_ID = Device::ID()->addNew(
		$version_ID,
		is_array(request('screens')) ? request('screens') : array(), // Screen IDs array
		request('page_width') != "" && is_numeric(request('page_width')) ? request('page_width') : null,
		request('page_height') != "" && is_numeric(request('page_height')) ? request('page_height') : null,
		true
	);

	// Check the result
	if(!$device_ID) {
		header('Location: '.site_url("project/$project_ID?adddeviceerror")); // If unsuccessful
		die();
	}


	// Update the project image
	if ($new_project) {

		$projectData = Project::ID($project_ID);
		$projectInfo = $projectData->getInfo();
		$project_image = $projectInfo['project_image_device_ID'];

		if ($project_image == null) $projectData->edit('project_image_device_ID', $device_ID);

	}


	// Revising URL
	$revise_url = site_url('revise/'.$device_ID);
	if ( request('pinmode') == "browse" ) $revise_url = $revise_url."?pinmode=browse&new=page";


	// If successful
	header('Location: '.$revise_url);
	die();

}



// ADD NEW SCREEN
if (
	is_numeric(get('new_screen'))
	&& is_numeric(get('version_ID'))
	// && get('nonce') == $_SESSION["new_screen_nonce"] !!! Disable the nonce check for now!
) {


	// Add the Devices
	$device_ID = Device::ID()->addNew(
		get('version_ID'),
		array(get('new_screen')),
		request('page_width') != "" && is_numeric(request('page_width')) ? request('page_width') : null,
		request('page_height') != "" && is_numeric(request('page_height')) ? request('page_height') : null
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



// ADD NEW VERSION
if (
	is_numeric(get('new_version'))
	// && get('nonce') == $_SESSION["new_screen_nonce"] !!! Disable the nonce check for now!
) {


	// Add a version
	$version_ID = Version::ID()->addNew(
		get('new_version')
	);

	// Check the result
	if(!$version_ID) {
		header('Location: '.site_url("projects?addversionerror")); // If unsuccessful
		die();
	}


	// Add the Devices
	$device_ID = Device::ID()->addNew(
		$version_ID,
		array(11), // Add custom for now !!!
		request('page_width') != "" && is_numeric(request('page_width')) ? request('page_width') : null,
		request('page_height') != "" && is_numeric(request('page_height')) ? request('page_height') : null
	);

	// Check the result
	if(!$device_ID) {
		header('Location: '.site_url("project?adddeviceerror")); // If unsuccessful
		die();
	}



	// If successful, redirect to "Revise" page
	header('Location: '.site_url('revise/'.$device_ID));
	die();

}



// Get the order
$order = get('order');

// Category Filter
$catFilter = isset($_url[1]) ? $_url[1] : '';



// PROJECTS DATA MODEL
$dataType = "project";
$allMyProjectsList = User::ID()->getMy("projects", $catFilter, $order);
//die_to_print($allMyProjectsList);
$theCategorizedData = categorize($allMyProjectsList, $dataType);
//die_to_print($theCategorizedData);



// MY PAGES AND COUNTS
$allMyPages = $mySharedPages;
$pageCount = array_column($allMyPages, 'project_ID', 'page_ID');
$pageCounts = array_count_values($pageCount);
//die_to_print($allMyPages);



// CATEGORY INFO
$categories = User::ID()->getCategories($dataType, $order);
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
	'vendor/jquery-sortable.js',
	'common.js',
	'block.js'
];

$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js'
];


$page_title = "Projects - Revisionary App";

if ($catFilter == "archived" || $catFilter == "deleted")
	$page_title = ucfirst($catFilter)." ".$page_title;

require view('modules/categorized_blocks');