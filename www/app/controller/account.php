<?php

$subpage = isset($_url[1]) ? $_url[1] : false;


// Additional Scripts and Styles
$additionalCSS = [
	'vendor/jquery.mCustomScrollbar.css'
];

$additionalHeadJS = [
	'common.js'
];

$additionalBodyJS = [
	'vendor/jquery.mCustomScrollbar.concat.min.js'
];

$page_title = "My Account - Revisionary App";
require view('account');