<?php

// If not logged in, go login page
if (!userloggedIn()) {
	header('Location: '.site_url('login'));
	die();
}


$projectsWithCats = [
	"Shared" => [
		"Twelve12"
	],
	"Mine" => [
		"Bilal Tas",
		"SoundCloud",
		"TWSJ",
		"Youtube",
		"Cuneyt Tas",
		"BBC",
		"Envato"
	]
];


$additionalHeadJS = [
	'vendor/jquery.sortable.min.js',
	'block.js'
];


$page_title = "Projects - Revisionary App";
require view('projects');