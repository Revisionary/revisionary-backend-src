<?php

use Cocur\BackgroundProcess\BackgroundProcess;


// Get the page ID
$pageID = $_url[1];

// Get device ID
$deviceID = Page::ID($pageID)->getPageInfo('device_ID');

// Get the device sizes
$width = Device::ID($deviceID)->getDeviceInfo('device_width');
$height = Device::ID($deviceID)->getDeviceInfo('device_height');


// If first time downloading - !!! NO NEED FOR NOW
//if (Page::ID($pageID)->getPageInfo('page_downloaded') == 0) {


	// INTERNAL REDIRECTIONS:

	// Http to Https Redirection
	if ( substr(Page::ID($pageID)->remoteUrl, 0, 8) == "https://" && !ssl) {

		header( 'Location: '.site_url('revise/'.$pageID, true) ); // Force HTTPS
		die();

	}

	// Https to Http Redirection
	if ( substr(Page::ID($pageID)->remoteUrl, 0, 7) == "http://" && ssl) {

		header( 'Location: '.site_url('revise/'.$pageID, false, true) ); // Force HTTP
		die();

	}


/*
	// CHECK THE PAGE RESPONSE
	$noProblem = false;

	// Bring the headers
	$OriginalUserAgent = ini_get('user_agent');
	ini_set('user_agent', 'Mozilla/5.0');
	$headers = get_headers(Page::ID($pageID)->remoteUrl, 1);
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
		$db->where ('page_ID', $pageID);
		$db->update ('pages', ['page_url' => $new_location]);


		// Refresh the page for preventing redirects
		header( 'Location: ' . site_url('revise/'.$pageID) );
		die();


	// Other
	} else {

		// Try non-ssl if the url is on SSL?
		if ( substr(Page::ID($pageID)->remoteUrl, 0, 8) == "https://" ) {

			// Update the nonSSL remoteUrl on DB !!!???
			$db->where ('page_ID', $pageID);
			$db->update ('pages', ['page_url' => "http://".substr(Page::ID($pageID)->remoteUrl, 8)]);


			// Refresh the page to try non-ssl
			header( 'Location: ' . site_url('revise/'.$pageID) );
			die();


		// If nothing works
		} else {

			header( 'Location: ' . site_url('?error='.Page::ID($pageID)->remoteUrl) );
			die();

		}


	}
*/

//} // If first time adding



// Create the log folder if not exists
if ( !file_exists(Page::ID($pageID)->logDir) )
	mkdir(Page::ID($pageID)->logDir, 0755, true);
@chmod(Page::ID($pageID)->logDir, 0755);


// Initiate Internalizator
$process = new BackgroundProcess('php '.dir.'/app/bgprocess/internalize.php '.$pageID.' '.session_id());
$process->run(Page::ID($pageID)->logDir."/internalize.log", true);



$additionalCSS = [
	'jquery.mCustomScrollbar.css',
	'revise.css'
];

$additionalHeadJS = [
	'revise-page.js'
];

$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js',
	'revise.js'
];

$page_title = "Revision Mode";
require view('revise');