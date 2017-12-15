<?php

require 'app/init.php';

$_url = get('url');
$_url = array_filter(explode('/', $_url));



// Development mode detection
$_parsed_current_url = parseUrl( current_url() );
if (
	$_parsed_current_url['subdomain'] != subdomain ||
	$_parsed_current_url['domain'] != domain
) {
	ob_start();
	$page_title = "Revisionary App";
	require view('coming-soon');
	ob_end_flush();
	die();
}


// Show the correct controller
if(!isset($_url[0])){
  $_url[0] = 'index';
}

if(!file_exists(controller($_url[0]))){
	http_response_code(404);

	// Show index
	$_url[0] = 'index';

	// If 404 page exists, better to show this one
	if(file_exists(controller('404')))
		$_url[0] = '404';
}



// Force HTTPS
if (!ssl && $_url[0] != 'revise' && $_url[0] != 'ajax') {

	header( 'Location: '.current_url(null, null, true) );
	die();

}


ob_start();
require controller($_url[0]);
ob_end_flush();