<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// If not logged in, go login page
if (!userloggedIn()) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}




// ADD NEW
if ( post('add_new') == "true" && post('add_new_nonce') == $_SESSION["add_new_nonce"] ) {


	// Security check !!!
	if (

		post('project-name') == "" ||
		!is_numeric( post('category') ) ||
		!is_numeric( post('order') )

	) {

		header('Location: '.site_url('projects?error'));
		die();

	}


	// DB Checks !!!


	// Add the project
	$project_ID = $db->insert('projects', array(
		"project_name" => post('project-name'),
		"user_ID" => currentUserID()
	));


	// Add the Category
	if (post('category') != "0") {

		$cat_id = $db->insert('project_cat_connect', array(
			"project_cat_project_ID" => $project_ID,
			"project_cat_ID" => post('category'),
			"project_cat_connect_user_ID" => currentUserID()
		));

	}


	// Add the order
	if (post('order') != "0") {

		$cat_id = $db->insert('sorting', array(
			"sort_type" => 'project',
			"sort_object_ID" => $project_ID,
			"sort_number" => post('order'),
			"sorter_user_ID" => currentUserID()
		));

	}


	// Add the project shares
	if ( is_array(post('project_shares')) && count(post('project_shares')) > 0 ) {

		foreach (post('project_shares') as $user_ID) {

			$share_ID = $db->insert('shares', array(
				"share_type" => 'project',
				"shared_object_ID" => $project_ID,
				"share_to" => $user_ID,
				"sharer_user_ID" => currentUserID()
			));

		}

	}


	// Add the first pages
	if (post('page-url') != "" && post('page-name') != "" && is_array(post('devices'))) {

		$parent_page_ID = null;
		$device_count = 0;
		foreach (post('devices') as $deviceID) {

			$page_ID = $db->insert('pages', array(
				"page_name" => post('page-name'),
				"page_url" => post('page-url'),
				"project_ID" => $project_ID,
				"device_ID" => $deviceID,
				"parent_page_ID" => $parent_page_ID,
				"user_ID" => currentUserID()
			));

			if ( $device_count == 0 ) $parent_page_ID = $page_ID;
			$device_count++;






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
		header('Location: '.site_url('project/'.$project_ID.'?add-first-page'));
		die();
	}

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