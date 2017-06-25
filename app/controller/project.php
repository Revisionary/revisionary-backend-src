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


// Get the project ID
$project_ID = $_url[1];


// Get the order
$order = isset($_GET['order']) ? $_GET['order'] : 'custom';


$additionalHeadJS = [
	'vendor/jquery.sortable.min.js',
	'block.js'
];

$page_title = Project::ID($_url[1])->projectName." Project - Revisionary App";
require view('project');