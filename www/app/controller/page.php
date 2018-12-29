<?php


// SECURITY CHECKS

// If not logged in, go login page !!! Change when public revising available
if (!userloggedIn()) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


// If no page specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects?invalidpage'));
	die();
}



// THE PAGE INFO

// Get the page ID
$page_ID = $_url[1];



// All my pages
$allMyPages = User::ID()->getMy('pages');
$allMyPages = categorize($allMyPages, 'page', true);
//die_to_print($allMyPages);



// Find the current page
$page = array_filter($allMyPages, function($pageFound) use ($page_ID) {
    return ($pageFound['page_ID'] == $page_ID);
});
$page = end($page);
//die_to_print($page);

// Check if page not exists, redirect to the projects page
if ( !$page ) {
	header('Location: '.site_url('projects?pagedoesntexist'));
	die();
}



// THE DEVICE INFO
$devices = Device::ID()->getDevices([$page_ID]);
$first_device = reset($devices);
//die_to_print($first_device);

// If the specified device doesn't exist, go projects page
if ( !isset($first_device) ) {
	header('Location: '.site_url('projects?devicedoesntexist'));
	die();
}


// If nothing goes wrong, open the first device
header('Location: '.site_url('revise/'.$first_device['device_ID']));
die();