<?php
use Cocur\BackgroundProcess\BackgroundProcess;

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

//var_dump($projectShares); die();



// Check if any my pages in the project

// Bring the shared ones
$db->join("shares s", "p.page_ID = s.shared_object_ID", "LEFT");
$db->joinWhere("shares s", "s.share_type", "page");
$db->joinWhere("shares s", "s.share_to", currentUserID());

// Ony my pages or shared to me
$db->where('(user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');

// Exclude the other projects
$db->where('project_ID', $_url[1]);

// My pages in this project
$allMyPages = $db->get('pages p');

//var_dump($allMyPages); die();



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


/*
echo "<pre>";
print_r(array_column($theCategorizedData, 'theData')); exit();
print_r($theCategorizedData); exit();
*/


// SECURITY CHECK
// If project doesn't belong to me and if no page belong to me
if (
	$project['user_ID'] != currentUserID() && // If the project isn't belong to me
	array_search(currentUserID(), array_column($projectShares, 'share_to')) === false && // And, if the project isn't shared to me
	count($allMyPages) == 0 // And, if there is no my page in it
) {

	// Redirect to "Projects" page
	header('Location: '.site_url('projects'));
	die();

}




// ADD NEW DEVICE
if ( is_numeric(get('new_device')) && is_numeric(get('page_ID')) && get('nonce') == $_SESSION["new_device_nonce"] ) {


	// DB Checks !!! (Page exists?, Device exists?, etc.)

	$device_ID = get('new_device');
	$parent_page_ID = get('page_ID');
	$page_name = Page::ID($parent_page_ID)->getPageInfo('page_name');
	$page_url = Page::ID($parent_page_ID)->getPageInfo('page_url');



	// Add the new page with the device
	$page_ID = $db->insert('pages', array(
		"page_name" => $page_name,
		"page_url" => $page_url,
		"project_ID" => $project_ID,
		"device_ID" => $device_ID,
		"parent_page_ID" => $parent_page_ID,
		"user_ID" => currentUserID()
	));

	// Add its initial version
	$version_ID = $db->insert('versions', array(
		"page_ID" => $page_ID,
		"user_ID" => currentUserID()
	));





	// ADD TO QUEUE

	// Remove the existing and wrong files
	if ( file_exists(Page::ID($page_ID)->pageDir) )
		deleteDirectory(Page::ID($page_ID)->pageDir);


	// Re-Create the log folder if not exists
	if ( !file_exists(Page::ID($page_ID)->logDir) )
		mkdir(Page::ID($page_ID)->logDir, 0755, true);
	@chmod(Page::ID($page_ID)->logDir, 0755);


	// Logger
	$logger = new Katzgrau\KLogger\Logger(Page::ID($page_ID)->logDir, Psr\Log\LogLevel::DEBUG, array(
		'filename' => Page::ID($page_ID)->logFileName,
	    'extension' => 'log', // changes the log file extension
	));




	// Add a new job to the queue
	$queue = new Queue();
	$queue_ID = $queue->new_job('internalize', $page_ID, "Waiting other works to be done.");


	// Initiate Internalizator
	$process = new BackgroundProcess('php '.dir.'/app/bgprocess/internalize.php '.$page_ID.' '.session_id().' '.$project_ID.' '.$queue_ID);
	$process->run(Page::ID($page_ID)->logDir."/internalize.log", true);


	// Add the PID to the queue
	$queue->update_status($queue_ID, "waiting", "Waiting other works to be done.", $process->getPid());





	// Redirect to "Revise" page
	header('Location: '.site_url('revise/'.$page_ID));
	die();
}




// ADD NEW PAGE
if ( post('add_new') == "true" && post('add_new_nonce') == $_SESSION["add_new_nonce"] ) {


	// Security check !!!
	if (

		post('page-url') == "" ||
		post('page-name') == "" ||
		!is_numeric( post('project_ID') ) ||
		!is_numeric( post('category') ) ||
		!is_numeric( post('order') )

	) {

		header('Location: '.site_url('projects?addpageerror'));
		die();

	}


	// DB Checks !!!


	// Add the first pages
	if (
		post('project_ID') != "" &&
		post('page-url') != "" &&
		post('page-name') != ""
	) {

		$devices = post('devices');
		$project_ID = post('project_ID');


		// If not device entered, use the default !!! For now - Detect existing device
		if ( !is_array(post('devices')) || count(post('devices')) == 0 ) {
			$devices[] = 4; // Macbook Pro 15 for now !!!
		}


		$parent_page_ID = null;
		$device_count = 0;
		foreach ($devices as $deviceID) {

			$page_ID = $db->insert('pages', array(
				"page_name" => post('page-name'),
				"page_url" => post('page-url'),
				"project_ID" => $project_ID,
				"device_ID" => $deviceID,
				"parent_page_ID" => $parent_page_ID,
				"user_ID" => currentUserID()
			));

			// Add its initial version
			$version_ID = $db->insert('versions', array(
				"page_ID" => $page_ID,
				"user_ID" => currentUserID()
			));

			if ( $device_count == 0 ) $parent_page_ID = $page_ID;
			$device_count++;


			// Add the page share to only parent page
			if ( is_array(post('page_shares')) && count(post('page_shares')) > 0 && $device_count == 1 ) {

				foreach (post('page_shares') as $share_to) {

					$share_ID = $db->insert('shares', array(
						"share_type" => 'page',
						"shared_object_ID" => $page_ID,
						"share_to" => $share_to,
						"sharer_user_ID" => currentUserID()
					));

				}

			}


			// Add the Category
			if (post('category') != "0") {

				$cat_id = $db->insert('page_cat_connect', array(
					"page_cat_page_ID" => $page_ID,
					"page_cat_ID" => post('category'),
					"page_cat_connect_user_ID" => currentUserID()
				));

			}


			// Add the order
			if (post('order') != "0") {

				$cat_id = $db->insert('sorting', array(
					"sort_type" => 'page',
					"sort_object_ID" => $page_ID,
					"sort_number" => post('order'),
					"sorter_user_ID" => currentUserID()
				));

			}






			// ADD TO QUEUE

			// Remove the existing and wrong files
			if ( file_exists(Page::ID($page_ID)->pageDir) )
				deleteDirectory(Page::ID($page_ID)->pageDir);


			// Re-Create the log folder if not exists
			if ( !file_exists(Page::ID($page_ID)->logDir) )
				mkdir(Page::ID($page_ID)->logDir, 0755, true);
			@chmod(Page::ID($page_ID)->logDir, 0755);


			// Logger
			$logger = new Katzgrau\KLogger\Logger(Page::ID($page_ID)->logDir, Psr\Log\LogLevel::DEBUG, array(
				'filename' => Page::ID($page_ID)->logFileName,
			    'extension' => 'log', // changes the log file extension
			));




			// Add a new job to the queue
			$queue = new Queue();
			$queue_ID = $queue->new_job('internalize', $page_ID, "Waiting other works to be done.");


			// Initiate Internalizator
			$process = new BackgroundProcess('php '.dir.'/app/bgprocess/internalize.php '.$page_ID.' '.session_id().' '.$project_ID.' '.$queue_ID);
			$process->run(Page::ID($page_ID)->logDir."/internalize.log", true);


			// Add the PID to the queue
			$queue->update_status($queue_ID, "waiting", "Waiting other works to be done.", $process->getPid());






		}

	}



	if($project_ID) {
		header('Location: '.site_url('revise/'.$parent_page_ID));
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


// Generate new nonce for add new devices
$_SESSION["new_device_nonce"] = uniqid(mt_rand(), true);


$page_title = Project::ID($_url[1])->projectName." Project - Revisionary App";
require view('dynamic/categorized_blocks');