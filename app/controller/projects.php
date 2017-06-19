<?php

// If not logged in, go login page
if (!userloggedIn()) {
	header('Location: '.site_url('login'));
	die();
}


// Get the order
$order = isset($_GET['order']) ? $_GET['order'] : 'custom';



$additionalHeadJS = [
	'vendor/jquery.sortable.min.js',
	'block.js'
];


$page_title = "Projects - Revisionary App";
require view('projects');