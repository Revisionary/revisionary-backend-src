<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// If not logged in, go login page
if ( !userloggedIn() ) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}




// ADD NEW
if ( post('add_new') == "true" && post('add_new_nonce') == $_SESSION["add_new_nonce"] ) {


	$project_ID = Project::ID()->addNew(
		post('project-name'),
		post('category'),
		post('order'),
		is_array(post('project_shares')) ? post('project_shares') : array()
	);



	// Add the first pages
	$firstPageAdded = false;
	if (
		post('page-url') != "" &&
		post('page-name') != "" &&
		is_array(post('devices')) &&
		count(post('devices')) > 0
	) {


		// Add the pages
		$parent_page_ID = Page::ID()->addNew(
			post('page-url'),
			post('page-name'),
			$project_ID,
			0, // Category ID
			post('order'),
			is_array(post('devices')) ? post('devices') : array(), // Device IDs array
			is_array(post('page_shares')) ? post('page_shares') : array()
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
require model('projects');
$theCategorizedData = the_data();
$dataType = 'project';


//print_r($projectsData); exit();


// Additional Scripts and Styles
$additionalCSS = [
	'jquery.mCustomScrollbar.css'
];

$additionalHeadJS = [
	'process.js',
	'vendor/jquery.sortable.min.js',
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

require view('dynamic/categorized_blocks');