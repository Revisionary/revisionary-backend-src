<?php


// SECURITY CHECKS

// If not logged in, go login page
if ( !userloggedIn() ) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}



// ADD NEW
if (
	request('add_new') == "true"
	&& request('page-url') != ""
	// && post('add_new_nonce') == $_SESSION["add_new_nonce"] !!! Disable the nonce check for now!
) {


	// Add the project
	$project_ID = Project::ID()->addNew(
		request('project-name'),
		is_array(request('project_shares')) ? request('project_shares') : array(),
		request('category'),
		request('order'),
		request('page-url')
	);

	// Check the result
	if(!$project_ID) {
		header('Location: '.site_url('projects?addprojecterror')); // If unsuccessful
		die();
	}



	// Add the Page
	$page_ID = Page::ID()->addNew(
		$project_ID,
		request('page-url'),
		request('page-name'),
		is_array(request('page_shares')) ? request('page_shares') : array()
	);

	// Check the result
	if(!$page_ID) {
		header('Location: '.site_url('projects?addpageerror')); // If unsuccessful
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
		header('Location: '.site_url('projects?adddeviceerror')); // If unsuccessful
		die();
	}



	// If successful
	if ($device_ID)
		header('Location: '.site_url('revise/'.$device_ID));
	elseif ($project_ID)
		header('Location: '.site_url('project/'.$project_ID.'#add-first-page'));
	else
		header('Location: '.site_url('projects?adderror'));

	die();

}



// Get the order
$order = get('order');

// Category Filter
$catFilter = isset($_url[1]) ? $_url[1] : '';



// PROJECTS DATA MODEL
$dataType = "project";
$allMyProjectsList = User::ID()->getMy("projects", $catFilter, $order);
$theCategorizedData = categorize($allMyProjectsList, $dataType);
//die_to_print($theCategorizedData);



// MY PAGES AND COUNTS
$allMyPages = $mySharedPages;
$pageCount = array_column($allMyPages, 'project_ID');
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
	'vendor/jquery.sortable.min.js',
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