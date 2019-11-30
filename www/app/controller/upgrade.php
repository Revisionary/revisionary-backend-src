<?php

// If not logged in, go login page
if (!$User) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js',
	'common.js'
];

$page_title = "Upgrade";
require view('upgrade');