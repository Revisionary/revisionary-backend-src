<?php

// If not logged in, go login page !!! Change when public revising available
if ($User) {
	header('Location: '.site_url('upgrade'));
	die();
}


$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js',
	'common.js'
];


$page_title = "Pricing";
require view('upgrade');