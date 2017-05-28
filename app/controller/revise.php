<?php


$pageID = $_url[1];



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