<?php

// If not logged in, go login page
if (!userloggedIn()) {
	header('Location: '.site_url('login'));
	die();
}

// If no project specified, go projects page
if ( !isset($_url[1]) ) {
	header('Location: '.site_url('projects'));
	die();
}




$pagesWithCats = [
	"Main Pages" => [
		"Home",
		"About",
		"Contact"
	],
	"Portfolio Pages" => [
		"GM Properties",
		"128 Online",
		"Vampire Tools",
		"inMotion",
		"The Kitchen"
	],
	"Blog Pages" => [
		"Blog 1",
		"Blog 2"
	]
];


$additionalHeadJS = [
	'vendor/jquery.sortable.min.js',
	'block.js'
];

$page_title = "Pages - Revisionary App";
require view('pages');