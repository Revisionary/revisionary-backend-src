<?php


// SECURITY CHECKS

// If not logged in, go login page
if ( !$User ) {
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

	$project_ID = $project_ID_initial = request('project_ID');
	$page_url = request('page-url');
	if ( request('pinmode') == "browse" ) $page_url = rawurldecode($page_url);


	// URL check
	if (!filter_var($page_url, FILTER_VALIDATE_URL)) {
		header('Location: '.site_url("projects?invalidurl")); // If unsuccessful
		die();
	}



	// Standardize the URL before saving
	$page_url = urlStandardize($page_url);



	// Check for redirects
	$page_url = get_redirect_final_target($page_url);



	// Add the project
	$new_project = false;
	if ($project_ID == "new" || $project_ID == "autodetect") {

		$new_project = true;

		$project_ID = Project::ID($project_ID)->addNew(
			$page_url,
			request('project-name'),
			is_array(request('project_shares')) ? request('project_shares') : array(),
			request('category'),
			request('order')
		);

	}

	// Check the result
	if(!$project_ID || !is_numeric($project_ID)) {
		header('Location: '.site_url('projects?addprojecterror')); // If unsuccessful
		die();
	}



	if ($project_ID_initial == 'autodetect') {


		// Bring the project info
		$db->join("projects pr", "pr.project_ID = p.project_ID", "LEFT");

		$db->where('p.user_ID', currentUserID());
		$db->where('p.page_deleted', 0);
		$db->where('p.page_archived', 0);
		$db->where('pr.project_deleted', 0);
		$db->where('pr.project_archived', 0);
		$db->where('p.page_url', $page_url);
		$pages_match = $db->get('pages p', null, 'p.page_url, p.page_ID, p.project_ID');
		$possible_page_IDs = array_unique(array_column($pages_match, 'page_ID'));


		// Make it project id if the result has 1 record
		if ( count($possible_page_IDs) == 1 )
			$page_ID = reset($possible_page_IDs);
		else
			$page_ID = "new";


	}


	if ($project_ID_initial != 'autodetect' || $page_ID == "new") {


		// Add the Page
		$page_ID = Page::ID()->addNew(
			$project_ID,
			$page_url,
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


	}



	// Add a phase
	$phase_ID = Phase::ID()->addNew(
		$page_ID
	);

	// Check the result
	if(!$phase_ID) {
		header('Location: '.site_url("project/$project_ID?addphaseerror")); // If unsuccessful
		die();
	}



	// Add the Devices
	$device_ID = Device::ID()->addNew(
		intval($phase_ID),
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
	$revise_url = site_url('revise/'.$device_ID.'?new');
	if ( request('pinmode') == "browse" ) $revise_url = $revise_url."=page&pinmode=browse";


	// If successful
	header('Location: '.$revise_url);
	die();

}



// ADD NEW SCREEN
if (
	is_numeric(get('new_screen'))
	&& is_numeric(get('phase_ID'))
	// && get('nonce') == $_SESSION["new_screen_nonce"] !!! Disable the nonce check for now!
) {


	// Add the Devices
	$device_ID = Device::ID()->addNew(
		get('phase_ID'),
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
	is_numeric(get('new_phase'))
	// && get('nonce') == $_SESSION["new_screen_nonce"] !!! Disable the nonce check for now!
) {


	// Add a phase
	$phase_ID = Phase::ID()->addNew(
		get('new_phase')
	);

	// Check the result
	if(!$phase_ID) {
		header('Location: '.site_url("projects?addphaseerror")); // If unsuccessful
		die();
	}


	// Add the Devices
	$device_ID = Device::ID()->addNew(
		intval($phase_ID),
		array(11), // Add custom for now !!!
		request('page_width') != "" && is_numeric(request('page_width')) ? request('page_width') : null,
		request('page_height') != "" && is_numeric(request('page_height')) ? request('page_height') : null,
		false,
		true // From phase
	);

	// Check the result
	if(!$device_ID) {
		header('Location: '.site_url("project?adddeviceerror")); // If unsuccessful
		die();
	}



	// If successful, redirect to "Revise" page
	header('Location: '.site_url('revise/'.$device_ID.'?new'));
	die();

}



// Data Type
$dataType = "project";

// Get the order !!!
$order = get('order');

// Category Filter
$catFilter = isset($_url[1]) ? $_url[1] : '';



// PAGES DATA AND COUNTS
$pages = $User->getPages();
//die_to_print($pages);
$pageCounts = array_column($pages, 'project_ID', 'page_ID');
//die_to_print($pageCounts);
$pageCounts = array_count_values($pageCounts);
//die_to_print($pageCounts);



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