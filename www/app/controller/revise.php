<?php

use Cocur\BackgroundProcess\BackgroundProcess;

// Force re-internalize
$forceReInternalize = get('redownload') === "" ? true : false;



// SECURITY CHECKS

// If not logged in, go login page !!! Change when public revising available
if (!userloggedIn()) {
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



// Get the device ID
$device_ID = $_url[1];

// If the specified device doesn't exist, go projects page
$deviceData = Device::ID($device_ID);
$device = $deviceData->getInfo();
//die_to_print($device);
if ( !$device ) {
	header('Location: '.site_url('projects?devicedoesntexist'));
	die();
}



// Get the version ID
$version_ID = $device['version_ID'];

// If the specified device doesn't exist, go projects page
$versionData = Version::ID($version_ID);
$version = $versionData->getInfo();
//die_to_print($version);
if ( !$version ) {
	header('Location: '.site_url('projects?versiondoesntexist'));
	die();
}



// Get the page ID
$page_ID = $version['page_ID'];

// THE PAGE INFO

// All my pages
$allMyPages = User::ID()->getMy('pages');
$allMyPages = categorize($allMyPages, 'page', true);
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



// Get the project ID
$project_ID = $page['project_ID'];


// Find the other pages from this project
$other_pages = array_filter($allMyPages, function($pageFound) use ($project_ID) {
	return ($pageFound['project_ID'] == $project_ID);
});
//die_to_print($other_pages);



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
$pageData = Page::ID($page_ID);
$device_image = $deviceData->getImage();
$project_image = cache."/projects/project-$project_ID/project.jpg"; // !!!
//die("$device_image -> $project_image");



// PROTOCOL REDIRECTIONS:

// Http to Https Redirection
if ( substr($versionData->remoteUrl, 0, 8) == "https://" && !ssl) {

	$url_to_redirect = site_url('revise/'.$device_ID, true);
	if ( get('pinmode') == "standard" || get('pinmode') == "browse" ) $url_to_redirect = queryArg('pinmode='.get('pinmode'), $url_to_redirect);
	if ( get('privatepin') == "1" ) $url_to_redirect = queryArg('privatepin=1', $url_to_redirect);
	if ( get('filter') == "incomplete" || get('filter') == "complete" ) $url_to_redirect = queryArg('filter='.get('filter'), $url_to_redirect);
	if ( get('new') == "page" ) $url_to_redirect = queryArg('new=page', $url_to_redirect);

	header( 'Location: '.$url_to_redirect ); // Force HTTPS
	die();

}

// Https to Http Redirection
if ( substr($versionData->remoteUrl, 0, 7) == "http://" && ssl) {

	$url_to_redirect = site_url('revise/'.$device_ID, false, true);
	if ( get('pinmode') == "standard" || get('pinmode') == "browse" ) $url_to_redirect = queryArg('pinmode='.get('pinmode'), $url_to_redirect);
	if ( get('privatepin') == "1" ) $url_to_redirect = queryArg('privatepin=1', $url_to_redirect);
	if ( get('filter') == "incomplete" || get('filter') == "complete" ) $url_to_redirect = queryArg('filter='.get('filter'), $url_to_redirect);
	if ( get('new') == "page" ) $url_to_redirect = queryArg('new=page', $url_to_redirect);

	header( 'Location: '.$url_to_redirect ); // Force HTTP
	die();

}


// Create the log folder if not exists
if ( !file_exists($versionData->logDir) )
	mkdir($versionData->logDir, 0755, true);
@chmod($versionData->logDir, 0755);



// Check if queue is already working
$db->where('queue_type', 'internalize');
$db->where('queue_object_ID', $version_ID);

$db->where("(queue_status = 'working' OR queue_status = 'waiting')");
$existing_queue = $db->get('queues');
//die_to_print($existing_queue);


$queue_ID = "";
$process_ID = "";
$process_status = "";





/*
die_to_print( $versionData->versionDir, false ); // Folder is exist
die_to_print( $versionData->versionFile, false ); // HTML is downloaded
die_to_print( $project_image, false ); // // Project image ready !!!
die_to_print( $versionData->logDir."/browser.log", false ); // No error on Browser
die_to_print( $versionData->logDir."/html-filter.log", false ); // No error on HTML filtering
die_to_print( $versionData->logDir."/css-filter.log" ); // No error on CSS filtering
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


	$process_status = "Version #$version_ID Queue Found: Queue #$queue_ID '".ucfirst($queue_status)."' | Page #$page_ID | Device #$device_ID | Process #$queue_PID | User #".currentUserID();


	// Site log
	$log->info($process_status);


	// Set the process ID to check
	$process_ID = $queue_PID;
	$process = BackgroundProcess::createFromPID( $process_ID );


	$process_status .= " BackgroundProcess::getPid() -> ". $process->getPid();


// If no need to re-internalize
} elseif (

	!$forceReInternalize &&

	file_exists( $versionData->versionDir ) && // Folder is exist
	file_exists( $versionData->versionFile ) && // HTML is downloaded
	//file_exists( $project_image ) && // // Project image ready !!!
	file_exists( $versionData->logDir."/browser.log" ) && // No error on Browser
	file_exists( $versionData->logDir."/html-filter.log" ) && // No error on HTML filtering
	file_exists( $versionData->logDir."/css-filter.log" ) // No error on CSS filtering

) {


	$process_status = "Device #$device_ID Opening: Version #$version_ID (Already Downloaded) | Page #$page_ID | User #".currentUserID();


	// Site log
	$log->info($process_status);



	// Device screenshot check
	if ( !file_exists( $device_image ) ) {


		// Logger
		$logger = new Katzgrau\KLogger\Logger($versionData->logDir, Psr\Log\LogLevel::DEBUG, array(
			'filename' => 'screenshot',
		    'extension' => $versionData->logFileExtension, // changes the log file extension
		));


		// NEW QUEUE
		// Add a new job to the queue
		$queue = new Queue();
		$queue_results = $queue->new_job('screenshot', $version_ID, $page_ID, $device_ID, "Waiting other works to be done.");
		$process_ID = $queue_results['process_ID'];
		$queue_ID = $queue_results['queue_ID'];


		// Site log
		$log->info("Device #$device_ID Screenshot Taking: Version #$version_ID | Page #$page_ID | Queue #$queue_ID | Process ID #".$process_ID." | User #".currentUserID());


	}



// Needs to be completely internalized
} else {


	// Remove the existing and wrong files
	if ( file_exists($versionData->versionDir) )
		deleteDirectory($versionData->versionDir);


	// Re-Create the log folder if not exists
	if ( !file_exists($versionData->logDir) )
		mkdir($versionData->logDir, 0755, true);
	@chmod($versionData->logDir, 0755);


	// Logger
	$logger = new Katzgrau\KLogger\Logger($versionData->logDir, Psr\Log\LogLevel::DEBUG, array(
		'filename' => $versionData->logFileName,
	    'extension' => $versionData->logFileExtension, // changes the log file extension
	));


	// NEW QUEUE
	// Add a new job to the queue
	$queue = new Queue();
	$queue_results = $queue->new_job('internalize', $version_ID, $page_ID, $device_ID, "Waiting other works to be done.");
	$process_ID = $queue_results['process_ID'];
	$queue_ID = $queue_results['queue_ID'];


	// Site log
	$log->info("Device #$device_ID Internalizing: Version #$version_ID | Page #$page_ID | Queue #$queue_ID | Process ID #".$process_ID." | User #".currentUserID());


}



// PROJECT SHARES QUERY
$db->where('share_type', 'project');
$db->where('shared_object_ID', $project_ID);
$projectShares = $db->get('shares', null, "share_to, sharer_user_ID");
//die_to_print($projectShares);



// PAGE SHARES QUERY
$db->where('share_type', 'page');
$db->where('shared_object_ID', $page_ID);
$pageShares = $db->get('shares', null, "share_to, sharer_user_ID");
//die_to_print($projectShares);



// SCREEN INFO
$screen_data = User::ID()->getScreenData();
//die_to_print($screen_data);


// PROJECT INFO
$projectInfo = Project::ID($project_ID)->getInfo(null, true);
//die_to_print($projectInfo);


// MY PROJECTS
$allMyProjects = User::ID()->getMy('projects');
//die_to_print($allMyProjects);


// MY DEVICES IN THIS PROJECT
$allMyDevices = $devices;
//die_to_print($allMyDevices);


if ($forceReInternalize) {

	// Remove all the pins for this page
	$db->where('version_ID', $version_ID);
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

		<b>Page Link:</b> ".site_url('revise/'.$device_ID)." ($screen_name: $width x $height) <br>
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