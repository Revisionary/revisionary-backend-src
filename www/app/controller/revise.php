<?php

use Cocur\BackgroundProcess\BackgroundProcess;

// Force re-internalize on each refresh for debugging !!!
$forceReInternalize = get('redownload') === "" ? true : false;


// SECURITY CHECKS

// If not logged in, go login page !!! Change when public revising available
if (!userloggedIn()) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


// If no page specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects'));
	die();
}


// If page doesn't exist
$db->where("page_ID", $_url[1]);
$page = $db->getOne("pages", "page_ID, user_ID");
if ( !$page ) {
	header('Location: '.site_url('projects'));
	die();
}



// Get the page ID
$page_ID = $_url[1]; // Check if page exists !!!

// Get the version number
$version_number = isset($_url[2]) ? $_url[2] : null; // Check if version exists !!!

// Get the latest version
$db->where('page_ID', $page_ID);
if ( isset($version_number) ) $db->where('version_number', $version_number);
$db->orderBy('version_number');
$version = $db->getOne('versions');

// If version found
if ($version) {
	$version_ID = $version['version_ID'];
	$version_number = $version['version_number'];
} else {

	// GIVE AN ERROR !!! Redirect to home

	$version_ID = 0;
	$version_number = "0.1";
}


// Get parent page ID
$parentpage_ID = Page::ID($page_ID)->getInfo('parent_page_ID');

// Get project ID
$project_ID = Page::ID($page_ID)->getInfo('project_ID');

// Get device ID
$deviceID = Page::ID($page_ID)->getInfo('device_ID');

// Get the device sizes
$width = Page::ID($page_ID)->getInfo('page_width') ? Page::ID($page_ID)->getInfo('page_width') : Device::ID($deviceID)->getInfo('device_width');
$height = Page::ID($page_ID)->getInfo('page_height') ? Page::ID($page_ID)->getInfo('page_height') : Device::ID($deviceID)->getInfo('device_height');

// Check if custom size entered

// Get device name
$device_name = Device::ID($deviceID)->getInfo('device_name');

// Get the device icon
$deviceCatID = Device::ID($deviceID)->getInfo('device_cat_ID');
$db->where('device_cat_ID', $deviceCatID);
$deviceCat = $db->getOne('device_categories');
$deviceIcon = $deviceCat['device_cat_icon'];

// Page Category
$db->where('page_cat_page_ID', $page_ID);
$db->orWhere('page_cat_page_ID', $parentpage_ID);
$pageCatID = $db->getValue('page_cat_connect', 'page_cat_ID');

$db->where('cat_ID', $pageCatID);
$pageCat = $db->getOne('categories');


// Screenshots
$page_image = Page::ID($page_ID)->pageDeviceDir."/page.jpg";
$project_image = Page::ID($page_ID)->projectDir."/project.jpg";


//print_r($pageCat); exit();


// If first time downloading - !!! NO NEED FOR NOW
//if (Page::ID($page_ID)->getInfo('page_downloaded') == 0) {


	// INTERNAL REDIRECTIONS:

	// Http to Https Redirection
	if ( substr(Page::ID($page_ID)->remoteUrl, 0, 8) == "https://" && !ssl) {

		header( 'Location: '.site_url('revise/'.$page_ID, true) ); // Force HTTPS
		die();

	}

	// Https to Http Redirection
	if ( substr(Page::ID($page_ID)->remoteUrl, 0, 7) == "http://" && ssl) {

		header( 'Location: '.site_url('revise/'.$page_ID, false, true) ); // Force HTTP
		die();

	}


/*
	// CHECK THE PAGE RESPONSE
	$noProblem = false;

	// Bring the headers
	$OriginalUserAgent = ini_get('user_agent');
	ini_set('user_agent', 'Mozilla/5.0');
	$headers = @get_headers(Page::ID($page_ID)->remoteUrl, 1);
	ini_set('user_agent', $OriginalUserAgent);

	var_dump($headers);
	die();

	$page_response = intval(substr($headers[0], 9, 3));


	// O.K.
	if ( $page_response == 200 ) {


		// Allow doing the jobs!
		$noProblem = true;


	// Redirecting
	} elseif ( $page_response == 301 || $page_response == 302 ) {


		$new_location = $headers['Location'];
		if ( is_array($new_location) ) $new_location = end($new_location);


		// Update the NEW remoteUrl on DB
		$db->where ('page_ID', $page_ID);
		$db->update ('pages', ['page_url' => $new_location]);


		// Refresh the page for preventing redirects
		header( 'Location: ' . site_url('revise/'.$page_ID) );
		die();


	// Other
	} else {

		// Try non-ssl if the url is on SSL?
		if ( substr(Page::ID($page_ID)->remoteUrl, 0, 8) == "https://" ) {

			// Update the nonSSL remoteUrl on DB !!!???
			$db->where ('page_ID', $page_ID);
			$db->update ('pages', ['page_url' => "http://".substr(Page::ID($page_ID)->remoteUrl, 8)]);


			// Refresh the page to try non-ssl
			header( 'Location: ' . site_url('revise/'.$page_ID) );
			die();


		// If nothing works
		} else {

			header( 'Location: ' . site_url('?error='.Page::ID($page_ID)->remoteUrl) );
			die();

		}


	}
*/

//} // If first time adding



// Create the log folder if not exists
if ( !file_exists(Page::ID($page_ID)->logDir) )
	mkdir(Page::ID($page_ID)->logDir, 0755, true);
@chmod(Page::ID($page_ID)->logDir, 0755);





// Check if queue is already working
$db->where('queue_type', 'internalize');
$db->where('queue_object_ID', $page_ID);


$db->where("(queue_status = 'working' OR queue_status = 'waiting')");


$existing_queue = $db->get('queues');
$queue_ID = "";
$process_ID = "";
$process_status = "";

/*
var_dump($existing_queue);
die();
*/


// If already working queue exists
if (

	!$forceReInternalize &&

	$existing_queue !== null &&
	count($existing_queue) > 0
) {

	$existing_queue = $existing_queue[0];


	$process_status = "DB: ".ucfirst($existing_queue['queue_status'])." Queue Found. Process ID: ".$existing_queue['queue_PID']." Queue ID: ".$existing_queue['queue_ID'];


	$queue_PID = $existing_queue['queue_PID'];
	$queue_ID = $existing_queue['queue_ID'];


	// Site log
	$log->info( ucfirst($existing_queue['queue_status'])." Queue found. Page: #$page_ID User: #".currentUserID()." Queue ID: #$queue_ID Queue PID: #$queue_PID");

	// Set the process ID to check
	$process = BackgroundProcess::createFromPID( $queue_PID );
	$process_ID = $queue_PID;


	$process_status .= " BackgroundProcess::getPid() -> ". $process->getPid();


// If no need to re-internalize
} elseif (

	!$forceReInternalize &&

	file_exists(Page::ID($page_ID)->pageDir) && // Folder is exist
	file_exists(Page::ID($page_ID)->pageFile) && // HTML is downloaded
	file_exists( $page_image ) && // Page image ready
	file_exists( $project_image ) && // // Project image ready
	file_exists( Page::ID($page_ID)->logDir."/browser.log" ) && // No error on Browser
	file_exists( Page::ID($page_ID)->logDir."/html-filter.log" ) && // No error on HTML filtering
	file_exists( Page::ID($page_ID)->logDir."/css-filter.log" ) // No error on CSS filtering

) {

	$process_status = "Already downloaded page #$page_ID is opening for user #".currentUserID().".";


	// Site log
	$log->info("Already downloaded page #$page_ID is opening for user #".currentUserID().".");


	// Initiate Internalizator
	$process = new BackgroundProcess('php '.dir.'/app/bgprocess/internalize_v4.php '.$page_ID.' '.session_id());
	$process->run(Page::ID($page_ID)->logDir."/internalize-tasks-php.log", true);
	$process_ID = $process->getPid();


	$process_status .= " BackgroundProcess::getPid() -> ". $process->getPid();


// Needs to be completely internalized
} else {

	$process_status = "Page #$page_ID needs to be re-internalized for user #".currentUserID().".";


	// Site log
	$log->error("Page #$page_ID needs to be re-internalized for user #".currentUserID().".");


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


	// NEW QUEUE
	// Add a new job to the queue
	$queue = new Queue();
	$queue_results = $queue->new_job('internalize', $page_ID, "Waiting other works to be done.", session_id());
	$process_ID = $queue_results['process_ID'];
	$queue_ID = $queue_results['queue_ID'];


	// Site log
	$log->info("Page #$page_ID added to the queue #$queue_ID. Process ID: #".$process_ID." User: #".currentUserID().".");


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
$db->where('shared_object_ID', $page_ID);

// Project shares data
$pageShares = $db->get('shares', null, "share_to, sharer_user_ID");

//echo "<pre>"; print_r($projectShares); echo "</pre>"; die();





// DEVICE INFO

// Bring the device category info
$db->join("device_categories d_cat", "d.device_cat_ID = d_cat.device_cat_ID", "LEFT");

$db->where('d.device_user_ID', 1); // !!! ?

$db->orderBy('d_cat.device_cat_order', 'asc');
$db->orderBy(' d.device_order', 'asc');
$devices = $db->get('devices d');


// Prepare the devices data
$device_data = [];
foreach ($devices as $device) {

	if ( !isset($device_data[$device['device_cat_ID']]['devices']) ) {

		$device_data[$device['device_cat_ID']] = array(
			'device_cat_icon' => $device['device_cat_icon'],
			'device_cat_name' => $device['device_cat_name'],
			'devices' => array(),
		);

	}

	$device_data[$device['device_cat_ID']]['devices'][$device["device_ID"]] = $device;

}

//echo "<pre>"; print_r($device_data);
//echo "<pre>"; print_r($devices); exit();





// Bring the pages that user can access !!! Not deleted / archived ones, put this on a Access::ID() class!

// Bring the shared ones
$db->join("shares s", "p.page_ID = s.shared_object_ID", "LEFT");
$db->joinWhere("shares s", "s.share_type", "page");
$db->joinWhere("shares s", "s.share_to", currentUserID());

// Ony my pages or shared to me
$db->where('(user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');

// Exclude the other projects
//$db->where('project_ID', $_url[1]);
$db->where('parent_page_ID IS NULL');

// My pages in this project
$allMyPages = $db->get('pages p');




//echo "<pre>"; print_r($allMyPages); echo "</pre>"; die();


// Filters
$pin_filter = "all";
if (
	get('filter') == "complete" ||
	get('filter') == "incomplete" ||
	get('filter') == "hide" ||
	get('filter') == "public" ||
	get('filter') == "private"
) $pin_filter = get('filter');



/*
echo "<pre>";
print_r($existing_queue);
echo "</pre>";

echo $process_status."<br>";
echo $process_ID;
die();
*/



$additionalCSS = [
	'jquery.mCustomScrollbar.css',
	'popline-theme/default.css',
	'revise.css'
];

$additionalHeadJS = [
	'process.js',
	'revise-globals.js',
	'revise-functions.js',
	'vendor/jquery-ui.min.js',
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


// Generate new nonce for add new devices
$_SESSION["new_device_nonce"] = uniqid(mt_rand(), true);


// Generate new nonce for pin actions
$_SESSION["pin_nonce"] = uniqid(mt_rand(), true);
//error_log("SESSION CREATED: ".$_SESSION["pin_nonce"]);


$page_title = "Revision Mode";
require view('revise');