<?php

use Cocur\BackgroundProcess\BackgroundProcess;

// Force re-internalize
$forceReInternalize = get('redownload') === "" ? true : false;
$ssr = $forceReInternalize && get('ssr') === "";



// SECURITY CHECKS

// If not logged in, go login page !!! Change when public revising available
if (!userLoggedIn()) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}

// Current user level ID
$currentUserLevel_ID = getUserInfo()['userLevelID'];


// If no page specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects?invaliddevice'));
	die();
}



// DEVICE:
// Get the device ID
$device_ID = intval($_url[1]);

// If the specified device doesn't exist, go projects page
$deviceData = Device::ID($device_ID);
if ( !$deviceData ) {
	header('Location: '.site_url('projects?devicedoesntexist'));
	die();
}
$device = $deviceData->getInfo();
//die_to_print($device);



// PAGE:
// Get the page ID
$page_ID = $device['page_ID'];

// All my pages
$allMyPages = User::ID()->getMy('pages');
$allMyPages = categorize($allMyPages, 'page', true);
$allMyPageIDs = array_column($allMyPages, 'page_ID');
//die_to_print($allMyPages);


// Find the current page
$page = array_filter($allMyPages, function($pageFound) use ($page_ID) {
    return ($pageFound['page_ID'] == $page_ID);
});
$page = end($page);

// If current user is admin
if ($currentUserLevel_ID == 1) {

	$pageData = Page::ID($page_ID);
	$page = $pageData ? $pageData->getInfo() : false;

}
//die_to_print($page);

// Check if page not exists, redirect to the projects page
if ( !$page ) {
	header('Location: '.site_url('projects?pagedoesntexist'));
	die();
}


// Check the New URL if force reinternalizing
if ($forceReInternalize) {

	// Test the URL and get final URL after redirects
	$final_url = get_redirect_final_target($page['page_url']);
	if ($page['page_url'] != $final_url) {
		Page::ID($page_ID)->edit('page_url', $final_url);
	}

}


// Get the project ID
$project_ID = $page['project_ID'];


// Find the other pages from this project
$other_pages = array_filter($allMyPages, function($pageFound) use ($project_ID) {
	return ($pageFound['project_ID'] == $project_ID);
});
//die_to_print($other_pages);



// VERSION:
// Get the phase ID
$phase_ID = $device['phase_ID'];

// All my phases
$db->where('page_ID', $allMyPageIDs, 'IN');
$allMyPhases = $db->get('phases');
//die_to_print($allMyPhases);

// Find the current phase
$phase = array_filter($allMyPhases, function($phaseFound) use ($phase_ID) {
    return ($phaseFound['phase_ID'] == $phase_ID);
});
$phase = end($phase);
//die_to_print($phase);

// Current phase data
$phaseData = Phase::ID($phase_ID);
//die_to_print($phaseData);

// If the specified phase doesn't exist, go projects page
if ( !$phaseData ) {
	header('Location: '.site_url('projects?phasedoesntexist'));
	die();
}

// Find the other phases from this page
$other_phases = array_filter($allMyPhases, function($phaseFound) use ($page_ID) {
	return ($phaseFound['page_ID'] == $page_ID);
});
$other_phases = array_values($other_phases); // Reset the keys to get phase numbers
//die_to_print($other_phases);



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



// Screenshots
$device_image = $deviceData->getImage();
$project_image = cache."/projects/project-$project_ID/project.jpg"; // !!!
//die("$device_image -> $project_image");



// PROTOCOL REDIRECTIONS:

// Http to Https Redirection
if ( substr($phaseData->remoteUrl, 0, 8) == "https://" && !ssl) {

	header( 'Location: '.current_url("", "", true) ); // Force HTTPS
	die();

}

// Https to Http Redirection
if ( substr($phaseData->remoteUrl, 0, 7) == "http://" && ssl) {

	header( 'Location: '.current_url("", "", false, true) ); // Force HTTP
	die();

}



// LOG:

// Create the log folder if not exists
if ( !file_exists($phaseData->logDir) )
	mkdir($phaseData->logDir, 0755, true);
@chmod($phaseData->logDir, 0755);



// QUEUE CHECK:

// Check if queue is already working
$db->where('queue_type', 'internalize');
$db->where('queue_object_ID', $phase_ID);
$db->where("(queue_status = 'working' OR queue_status = 'waiting')");
$existing_queue = $db->get('queues');
//die_to_print($existing_queue);


$queue_ID = "";
$process_ID = "";
$process_status = "";



/*
die_to_print( $phaseData->phaseDir, false ); // Folder is exist
die_to_print( $phaseData->phaseFile, false ); // HTML is downloaded
die_to_print( $project_image, false ); // // Project image ready !!!
die_to_print( $phaseData->logDir."/browser.log", false ); // No error on Browser
die_to_print( $phaseData->logDir."/html-filter.log", false ); // No error on HTML filtering
die_to_print( $phaseData->logDir."/css-filter.log" ); // No error on CSS filtering
*/



// If already working queue exists !!!
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


	$process_status = "Phase #$phase_ID Queue Found: Queue #$queue_ID '".ucfirst($queue_status)."' | Page #$page_ID | Device #$device_ID | Process #$queue_PID | User #".currentUserID();


	// Site log
	$log->info($process_status);


	// Set the process ID to check
	$process_ID = $queue_PID;
	$process = BackgroundProcess::createFromPID( $process_ID );


	$process_status .= " BackgroundProcess::getPid() -> ". $process->getPid();


// If no need to re-internalize
} elseif (

	!$forceReInternalize &&

	file_exists( $phaseData->phaseDir ) && // Folder is exist
	file_exists( $phaseData->phaseFile ) && // HTML is downloaded
	file_exists( $phaseData->logDir."/browser.log" ) && // No error on Browser
	file_exists( $phaseData->logDir."/html-filter.log" ) && // No error on HTML filtering
	file_exists( $phaseData->logDir."/css-filter.log" ) // No error on CSS filtering

) {


	$process_status = "Device #$device_ID Opening: Phase #$phase_ID (Already Downloaded) | Page #$page_ID | User #".currentUserID();


	// Site log
	$log->info($process_status);



	// Device screenshot check
	if ( !file_exists( $device_image ) ) {


		// Logger
		$logger = new Katzgrau\KLogger\Logger($phaseData->logDir, Psr\Log\LogLevel::DEBUG, array(
			'filename' => 'screenshot',
		    'extension' => $phaseData->logFileExtension, // changes the log file extension
		));


		// NEW QUEUE
		// Add a new job to the queue
		$queue = new Queue();
		$queue_results = $queue->new_job('screenshot', $phase_ID, $page_ID, $device_ID, "Waiting other works to be done.");
		$process_ID = $queue_results['process_ID'];
		$queue_ID = $queue_results['queue_ID'];


		// Site log
		$log->info("Device #$device_ID Screenshot Taking: Phase #$phase_ID | Page #$page_ID | Queue #$queue_ID | Process ID #".$process_ID." | User #".currentUserID());


	}



// Needs to be completely internalized
} else {


	// Remove the existing and wrong files
	if ( file_exists($phaseData->phaseDir) )
		deleteDirectory($phaseData->phaseDir);


	// Re-Create the log folder if not exists
	if ( !file_exists($phaseData->logDir) )
		mkdir($phaseData->logDir, 0755, true);
	@chmod($phaseData->logDir, 0755);


	// Logger
	$logger = new Katzgrau\KLogger\Logger($phaseData->logDir, Psr\Log\LogLevel::DEBUG, array(
		'filename' => $phaseData->logFileName,
	    'extension' => $phaseData->logFileExtension, // changes the log file extension
	));


	// NEW QUEUE
	// Add a new job to the queue
	$queue = new Queue();
	$ssrAnswer = $ssr ? "yes" : "no";
	$queue_results = $queue->new_job('internalize', $phase_ID, $page_ID, $device_ID, "Waiting other works to be done.", $ssrAnswer);
	$process_ID = $queue_results['process_ID'];
	$queue_ID = $queue_results['queue_ID'];


	// Site log
	$log->info("Device #$device_ID Internalizing: Phase #$phase_ID | Page #$page_ID | Queue #$queue_ID | Process ID #".$process_ID." | User #".currentUserID());


}



// PROJECT SHARES QUERY // CHECK IF NEEDED BECAUSE OF AJAX CHECK !!! ???
$db->where('share_type', 'project');
$db->where('shared_object_ID', $project_ID);
$projectShares = $db->get('shares', null, "share_to, sharer_user_ID");
//die_to_print($projectShares);



// PAGE SHARES QUERY // CHECK IF NEEDED BECAUSE OF AJAX CHECK !!! ???
$db->where('share_type', 'page');
$db->where('shared_object_ID', $page_ID);
$pageShares = $db->get('shares', null, "share_to, sharer_user_ID");
//die_to_print($projectShares);


// PROJECT INFO
$projectData = Project::ID($project_ID);
$projectInfo = $projectData->getInfo();
//die_to_print($projectInfo);

$project_image = $projectInfo['project_image_device_ID'];
if ($project_image == null) $projectData->edit('project_image_device_ID', $device_ID);


// MY PROJECTS
$allMyProjects = User::ID()->getMy('projects');
//die_to_print($allMyProjects);


// MY DEVICES IN THIS PROJECT
$allMyDevices = $devices; // Comes globally from 'categorize.php'
//die_to_print($allMyDevices);


if ($forceReInternalize) {

	// Remove all the pins for this page
	$db->where('phase_ID', $phase_ID);
	$db->delete('pins');

}



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
	get('pinmode') == "browse"
) $pin_mode = get('pinmode');

$pin_private = "0";
if (
	get('privatepin') == "1"
) $pin_private = get('privatepin');


/*
echo "<pre>"; print_r($existing_queue); echo "</pre>";

echo $process_status."<br>";
echo $process_ID;
die();
*/



// Notify the admin
if (getUserInfo()['email'] != "bilaltas@me.com" && getUserInfo()['email'] != "bill@twelve12.com") {

	Notify::ID(1)->mail(
		getUserInfo()['fullName']." revising a page now.",
		"
		<b>User Information</b> <br>
		E-Mail: ".getUserInfo()['email']." <br>
		Full Name: ".getUserInfo()['fullName']." <br>
		Username: ".getUserInfo()['userName']." <br><br>


		<b>Project Name:</b> <a href='".site_url('project/'.$project_ID)."'>".$projectInfo['project_name']."</a> <br>
		<b>Page Name:</b> <a href='".site_url('page/'.$page_ID)."'>".$page['page_name']."</a> <br>
		<b>Device:</b> $screen_name: $width x $height <br>
		"
	);

}


$additionalCSS = [
	'vendor/jquery.mCustomScrollbar.css',
	'vendor/popline-theme/default.css',
	'vendor/spectrum.css',
	'revise.css'
];

$additionalHeadJS = [
	'process.js',
	'revise-globals.js',
	'revise-functions.js',
	'vendor/jquery-ui.min.js',
	'vendor/Autolinker.min.js',
	'vendor/autosize.min.js',
	'vendor/diff.js',
	'vendor/spectrum.js',
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
//$_SESSION["new_screen_nonce"] = uniqid(mt_rand(), true);


// Generate new nonce for pin actions
//$_SESSION["pin_nonce"] = uniqid(mt_rand(), true);
//error_log("SESSION CREATED: ".$_SESSION["pin_nonce"]);



$page_title = "Page Opening...";
require view('revise');