<?php

// If not logged in, go login page
if (!userloggedIn()) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


// Get the order
$order = isset($_GET['order']) ? $_GET['order'] : '';


// Category Filter
$catFilter = isset($_url[1]) ? $_url[1] : '';


// PROJECTS DATA MODEL
require model('projects');
$projectsData = the_data();


//print_r($projectsData); exit();


// Additional JavaScripts
$additionalHeadJS = [
	'vendor/jquery.sortable.min.js',
	'block.js'
];


$page_title = "Projects - Revisionary App";
require view('projects');