<?php

use Cocur\BackgroundProcess\BackgroundProcess;

// Force re-internalize on each refresh for debugging !!!
$forceReInternalize = get('redownload') === "" ? true : false;



// Get the page ID
$device_ID = $_url[1];



// SECURITY CHECKS

// If not logged in, go login page !!! Change when public revising available
if (!userloggedIn()) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


// If no page specified or not numeric, go projects page
if ( !isset($device_ID) || !is_numeric($device_ID) ) {
	header('Location: '.site_url('projects?invaliddevice'));
	die();
}


// If the specified device doesn't exist, go projects page
$deviceData = Device::ID($device_ID);
$device = $deviceData->getInfo("*", true);
//die_to_print($device);
if ( !$device ) {
	header('Location: '.site_url('projects?devicedoesntexist'));
	die();
}



// THE PAGE INFO

// All my pages
$allMyPages = User::ID()->getMy('pages');
//die_to_print($allMyPages);


// Find the current page
$page = array_filter($allMyPages, function($pageFound) use ($device) {
    return ($pageFound['page_ID'] == $device['page_ID']);
});
$page = end($page);
//die_to_print($page);

// Check if page not exists, redirect to the projects page
if ( !$page ) {
	header('Location: '.site_url('projects?pagedoesntexist'));
	die();
}


// Get project ID
$project_ID = $page['project_ID'];
$page_ID = $device['page_ID'];



// SCREEN INFO

// Get screen ID
$screenID = $device['screen_ID'];

// Get the screen sizes
$width = $device['device_width'] ? $device['device_width'] : $device['screen_width'];
$height = $device['device_height'] ? $device['device_height'] : $device['screen_height'];

// Get screen name
$screen_name = $device['screen_name'];

// Get the screen icon
$screenCatID = $device['screen_cat_ID'];
$screenIcon = $device['screen_cat_icon'];



// Screenshots !!!
$pageData = Page::ID($page_ID);
$device_image = $deviceData->getImage();
$project_image = cache."/projects/project-$project_ID/project.jpg";
//die("$device_image -> $project_image");


// PROTOCOL REDIRECTIONS:

// Http to Https Redirection
if ( substr($pageData->remoteUrl, 0, 8) == "https://" && !ssl) {

	header( 'Location: '.site_url('revise/'.$device_ID, true) ); // Force HTTPS
	die();

}

// Https to Http Redirection
if ( substr($pageData->remoteUrl, 0, 7) == "http://" && ssl) {

	header( 'Location: '.site_url('revise/'.$device_ID, false, true) ); // Force HTTP
	die();

}



// Create the log folder if not exists
if ( !file_exists($pageData->logDir) )
	mkdir($pageData->logDir, 0755, true);
@chmod($pageData->logDir, 0755);



// Check if queue is already working
$db->where('queue_type', 'internalize');
$db->where('queue_object_ID', $device_ID);

$db->where("(queue_status = 'working' OR queue_status = 'waiting')");
$existing_queue = $db->get('queues');


$queue_ID = "";
$process_ID = "";
$process_status = "";


// var_dump($existing_queue); die();


// If already working queue exists
if (
	!$forceReInternalize &&
	$existing_queue != null
) {


	// Error catch
	if ( !is_array($existing_queue) || count($existing_queue) > 1  || count($existing_queue) == 0 )
		die('Error #Q21. Please try again...');


	// Set the queue
	$existing_queue = $existing_queue[0];


	$queue_PID = $existing_queue['queue_PID'];
	$queue_ID = $existing_queue['queue_ID'];
	$queue_status = $existing_queue['queue_status'];


	$process_status = "DB: ".ucfirst($queue_status)." Queue Found. Page: #$device_ID User: #".currentUserID()." Process ID: #$queue_PID Queue ID: #$queue_ID";


	// Site log
	$log->info($process_status);


	// Set the process ID to check
	$process_ID = $queue_PID;
	$process = BackgroundProcess::createFromPID( $process_ID );


	$process_status .= " BackgroundProcess::getPid() -> ". $process->getPid();


// If no need to re-internalize
} elseif (

	!$forceReInternalize &&

	file_exists( $pageData->pageDir ) && // Folder is exist
	file_exists( $pageData->pageFile ) && // HTML is downloaded
	file_exists( $device_image ) && // Page image ready
	file_exists( $project_image ) && // // Project image ready
	file_exists( $pageData->logDir."/browser.log" ) && // No error on Browser
	file_exists( $pageData->logDir."/html-filter.log" ) && // No error on HTML filtering
	file_exists( $pageData->logDir."/css-filter.log" ) // No error on CSS filtering

) {


	$process_status = "Already downloaded page #$device_ID is opening for user #".currentUserID().".";


	// Site log
	$log->info($process_status);


// Needs to be completely internalized
} else {


	$process_status = "Page #$device_ID needs to be internalized for user #".currentUserID().".";


	// Site log
	$log->error($process_status);


	// Remove the existing and wrong files
	if ( file_exists($pageData->pageDir) )
		deleteDirectory($pageData->pageDir);


	// Re-Create the log folder if not exists
	if ( !file_exists($pageData->logDir) )
		mkdir($pageData->logDir, 0755, true);
	@chmod($pageData->logDir, 0755);


	// Logger
	$logger = new Katzgrau\KLogger\Logger($pageData->logDir, Psr\Log\LogLevel::DEBUG, array(
		'filename' => $pageData->logFileName,
	    'extension' => 'log', // changes the log file extension
	));


	// NEW QUEUE
	// Add a new job to the queue
	$queue = new Queue();
	$queue_results = $queue->new_job('internalize', $device_ID, "Waiting other works to be done.", session_id());
	$process_ID = $queue_results['process_ID'];
	$queue_ID = $queue_results['queue_ID'];


	// Site log
	$log->info("Page #$device_ID added to the queue #$queue_ID. Process ID: #".$process_ID." User: #".currentUserID().".");


}



// PROJECT SHARES QUERY

// Exlude other types
$db->where('share_type', 'project');

// Is this project?
$db->where('shared_object_ID', $project_ID);

// Project shares data
$projectShares = $db->get('shares', null, "share_to, sharer_user_ID");
//echo "<pre>"; print_r($projectShares); echo "</pre>"; die();



// PAGE SHARES QUERY

// Exlude other types
$db->where('share_type', 'page');

// Is this project?
$db->where('shared_object_ID', $device_ID);

// Project shares data
$pageShares = $db->get('shares', null, "share_to, sharer_user_ID");
//echo "<pre>"; print_r($projectShares); echo "</pre>"; die();



// SCREEN INFO
$screen_data = User::ID()->getScreenData();
//echo "<pre>"; print_r($screen_data); exit();


// PROJECT INFO
$projectInfo = Project::ID($project_ID)->getInfo(null, true);
//echo "<pre>"; print_r($projectInfo); exit();


// All my projects
$allMyProjects = User::ID()->getMy('projects');
//echo "<pre>"; print_r($allMyProjects); echo "</pre>"; die();



// FILTERS
$pin_filter = "all";
if (
	get('filter') == "complete" ||
	get('filter') == "incomplete" ||
	get('filter') == "hide" ||
	get('filter') == "public" ||
	get('filter') == "private"
) $pin_filter = get('filter');



// PIN MODE
$pin_mode = "live";
if (
	get('pinmode') == "live" ||
	get('pinmode') == "standard" ||
	get('pinmode') == "off"
) $pin_mode = get('pinmode');

$pin_private = "0";
if (
	get('privatepin') == "1"
) $pin_private = get('privatepin');

$pin_text = $pin_mode;
if ($pin_private == "1") $pin_text = "Private ".$pin_text;
if ($pin_mode == "standard") $pin_text = "Only Comment";


/*
echo "<pre>"; print_r($existing_queue); echo "</pre>";

echo $process_status."<br>";
echo $process_ID;
die();
*/



$additionalCSS = [
	'vendor/jquery.mCustomScrollbar.css',
	'vendor/popline-theme/default.css',
	'revise.css'
];

$additionalHeadJS = [
	'process.js',
	'revise-globals.js',
	'revise-functions.js',
	'vendor/jquery-ui.min.js',
	'vendor/diff.js',
	'vendor/popline/jquery.popline.js',
	'vendor/popline/plugins/jquery.popline.link.js',
	//'vendor/popline/plugins/jquery.popline.blockquote.js',
	'vendor/popline/plugins/jquery.popline.decoration.js',
	'vendor/popline/plugins/jquery.popline.list.js',
	'vendor/popline/plugins/jquery.popline.justify.js',
	'vendor/popline/plugins/jquery.popline.blockformat.js',
	//'vendor/popline/plugins/jquery.popline.social.js',
	'vendor/popline/plugins/jquery.popline.backcolor.js'
];

$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js',
	'common.js',
	'revise-page.js'
];


// Generate new nonce for add new screens
$_SESSION["new_screen_nonce"] = uniqid(mt_rand(), true);


// Generate new nonce for pin actions
$_SESSION["pin_nonce"] = uniqid(mt_rand(), true);
//error_log("SESSION CREATED: ".$_SESSION["pin_nonce"]);


$page_title = "Revision Mode";
require view('revise');