<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// If not logged in, go login page
if ( !userloggedIn() ) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}




// ADD NEW
if (
	post('add_new') == "true"
	&& post('page-url') != ""
	// && post('add_new_nonce') == $_SESSION["add_new_nonce"] !!! Disable the nonce check for now!
) {


	// Add the domain name as project name if not already entered
	$project_name = post('project-name');
	if ($project_name == "")
		$project_name = ucwords( str_replace('-', ' ', explode('.', parseUrl(post('page-url'))['domain'])[0]) );



	$project_ID = Project::ID()->addNew(
		$project_name,
		post('category'),
		post('order'),
		is_array(post('project_shares')) ? post('project_shares') : array()
	);



	// Add the first pages
	$firstPageAdded = false;
	if (
		post('page-url') != "" &&
		is_array(post('devices')) &&
		count(post('devices')) > 0
	) {


		// Add the pages
		$parent_page_ID = Page::ID()->addNew(
			post('page-url'),
			$project_ID,
			post('page-name'),
			0, // Category ID
			post('order'),
			is_array(post('devices')) ? post('devices') : array(), // Device IDs array
			is_array(post('page_shares')) ? post('page_shares') : array(),
			post('page-width') != "" ? post('page-width') : null,
			post('page-height') != "" ? post('page-height') : null
		);

		if ($parent_page_ID) $firstPageAdded = true;

	}



	// Check the result
	if(!$project_ID) {
		header('Location: '.site_url('projects?addpageerror')); // If unsuccessful
		die();
	}



	// If successful
	if ($firstPageAdded)
		header('Location: '.site_url('revise/'.$parent_page_ID));
	else
		header('Location: '.site_url('project/'.$project_ID.'#add-first-page'));

	die();

}


// Get the order
$order = isset($_GET['order']) ? $_GET['order'] : '';


// Category Filter
$catFilter = isset($_url[1]) ? $_url[1] : '';



// PROJECTS DATA MODEL
$dataType = "project";
$allMyProjectsList = UserAccess::ID()->getMy("projects", $catFilter, $order);
$theCategorizedData = categorize($allMyProjectsList, $dataType);
//echo "<pre>"; print_r(array_column($theCategorizedData, 'theData')); exit();



// MY PAGES AND COUNTS
$allMyPages = $mySharedPages;
$pageCount = array_column($allMyPages, 'project_ID');
$pageCounts = array_count_values($pageCount);
//echo "<pre>"; print_r( $pageCount ); die();


// CATEGORY INFO
$categories = UserAccess::ID()->getCategories($dataType, $order);
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


// Generate new nonce for add new modals
$_SESSION["add_new_nonce"] = uniqid(mt_rand(), true);


$page_title = "Projects - Revisionary App";

if ($catFilter == "archived" || $catFilter == "deleted")
$page_title = ucfirst($catFilter)." ".$page_title;

require view('modules/categorized_blocks');