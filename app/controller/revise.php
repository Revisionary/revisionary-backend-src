<?php


$pageID = $_url[1];

$internalize = new Internalize($pageID, (isset($_GET['revise']) ? $_GET['revise'] : ""));
$pageURL = $internalize->serveTheURL();
$remoteURL = $internalize->serveTheURL(true);


$additionalCSS = [
	'jquery.mCustomScrollbar.css',
	'revise.css'
];

$additionalHeadJS = [
	'revise-page.js'
];

$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js',
	'revise.js'
];

$page_title = "Revision Mode";
require view('revise');