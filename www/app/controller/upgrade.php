<?php

// If not logged in, go login page !!! Change when public revising available
if (!$User) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


$user_levels = $db->get('user_levels');
//die_to_print($user_levels);


// Delete the admin one
unset($user_levels[0]);
//die_to_print($user_levels);



$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js',
	'common.js'
];

$page_title = "Upgrade";
require view('upgrade');