<?php

$pageID = $_url[1];
$firstTime = true;

if ($firstTime) {


	// INTERNAL REDIRECTIONS:

	// Http to Https Redirection
	if ( substr(Page::ID($pageID)->remoteUrl, 0, 8) == "https://" && !ssl) {

		$appendUrl = "";
		if ( isset($_GET['new_url']) && !empty($_GET['new_url']) )
    		$appendUrl = "?new_url=".urlencode($_GET['new_url']);

		header('Location: '.site_url('revise/'.$pageID, true).$appendUrl); // Force HTTPS
		die();
	}

	// Https to Http Redirection
	if ( substr(Page::ID($pageID)->remoteUrl, 0, 7) == "http://" && ssl) {

		$appendUrl = "";
		if ( isset($_GET['new_url']) && !empty($_GET['new_url']) )
    		$appendUrl = "?new_url=".urlencode($_GET['new_url']);

		header('Location: '.site_url('revise/'.$pageID, false, true).$appendUrl); // Force HTTP
		die();
	}


	// CHECK THE PAGE RESPONSE
	$noProblem = false;
	$headers = @get_headers(Page::ID($pageID)->remoteUrl, 1);
	$page_response = intval(substr($headers[0], 9, 3));

	// O.K.
	if ( $page_response == 200 ) {


		// Allow doing the jobs!
		$noProblem = true;


	// Redirecting
	} elseif ( $page_response == 301 || $page_response == 302 ) {


		$new_location = $headers['Location'];
		if ( is_array($new_location) ) $new_location = end($new_location);


		// Update the NEW remoteUrl on DB !!!
		// ...


		// Refresh the page for preventing redirects
		header( 'Location: ' . site_url('revise/'.$pageID."?new_url=".urlencode($new_location)) );
		die();


	// Other
	} else {

		// Try non-ssl if the url is on SSL?
		if ( substr(Page::ID($pageID)->remoteUrl, 0, 8) == "https://" ) {

			// Update the nonSSL remoteUrl on DB !!!???
			// ...


			// Refresh the page to try non-ssl
			header( 'Location: ' . site_url('revise/'.$pageID."?new_url=".urlencode( "http://".substr(Page::ID($pageID)->remoteUrl, 8) )) );
			die();


		// If nothing works
		} else {

			header( 'Location: ' . site_url('?error='.Page::ID($pageID)->remoteUrl) );
			die();

		}


	}

} // If first time adding

// Get device ID
$deviceID = Page::ID($pageID)->getPageInfo('device_ID');

$width = Device::ID($deviceID)->getDeviceInfo('device_width');
$height = Device::ID($deviceID)->getDeviceInfo('device_height');



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