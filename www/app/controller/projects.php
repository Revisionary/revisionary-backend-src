<?php
use Cocur\BackgroundProcess\BackgroundProcess;


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


	// Add the domain name as project name if not already entered
	$project_name = request('project-name');
	if ($project_name == "")
		$project_name = ucwords( str_replace('-', ' ', explode('.', parseUrl(request('page-url'))['domain'])[0]) );



	$project_ID = Project::ID()->addNew(
		$project_name,
		request('category'),
		request('order'),
		is_array(request('project_shares')) ? request('project_shares') : array()
	);



	// Add the first pages
	$firstPageAdded = false;
	if (
		request('page-url') != "" &&
		is_array(request('devices')) &&
		count(request('devices')) > 0
	) {


		// Add the pages
		$parent_page_ID = Page::ID()->addNew(
			request('page-url'),
			$project_ID,
			request('page-name'),
			0, // Category ID
			request('order'),
			is_array(request('devices')) ? request('devices') : array(), // Device IDs array
			is_array(request('page_shares')) ? request('page_shares') : array(),
			request('page-width') != "" ? request('page-width') : null,
			request('page-height') != "" ? request('page-height') : null
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


$page_title = "Projects - Revisionary App";

if ($catFilter == "archived" || $catFilter == "deleted")
$page_title = ucfirst($catFilter)." ".$page_title;

require view('modules/categorized_blocks');