<?php

$subpage = isset($_url[1]) ? $_url[1] : false;
$user_ID = getUserInfo()['userLevelID'] == 1 && isset($_GET['user']) && is_numeric($_GET['user']) ? intval($_GET['user']) : currentUserID();
$userInfo = getUserInfo($user_ID);


// If user not exist
if (!$userInfo) die('User Not Found.');


// User info from DB
$userInfoDB = $Users[$user_ID];


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