<?php

// If logged in, go to the Upgrade page
if ( userLoggedIn() ) {
	header('Location: '.site_url('upgrade'));
	die();
}


$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js',
	'common.js'
];


$page_title = "Pricing";
require view('upgrade');