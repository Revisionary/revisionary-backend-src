<?php


$pageID = $_url[1];



$additionalCSS = [
	'revise.css'
];

$additionalHeadJS = [
	'revise.js'
];

$page_title = "Revision Mode";
require view('revise');