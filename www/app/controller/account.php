<?php

$subpage = isset($_url[1]) ? $_url[1] : false;
$user_ID = getUserInfo()['userLevelID'] == 1 && isset($_GET['user']) && is_numeric($_GET['user']) ? intval($_GET['user']) : currentUserID();
$userInfo = getUserInfo($user_ID);


// If user not exist
if (!$userInfo) die('User Not Found.');


// User info from DB
$userInfoDB = $Users[$user_ID];


if ( post('update-submit') == "Update" ) {


	// Check the name field
	if ( empty(post('user_full_name')) ) {

		header('Location: '.current_url("error=name", "successful"));
		die();

	}


	// Full name check
	if ( !preg_match("/^[\p{Latin}[A-Za-z ]+$/u", post('user_full_name')) ) {

		header('Location: '.current_url("error=invalidname", "successful"));
		die();

	}


	// Parse the full name
	$user_full_name = post('user_full_name');
	$firstName = $user_full_name;
	$lastName = "";
	$parsedFullName = explode(' ', $user_full_name);
	if (count($parsedFullName) > 1) {
		$firstName = str_replace(' '.end($parsedFullName), '', $user_full_name);
		$lastName = end($parsedFullName);
	}


	// Update on DB
	$db->where('user_ID', $user_ID);
	$updated = $db->update('users', array(
		"user_first_name" => $firstName,
		"user_last_name" => $lastName,
		"user_job_title" => post('user_job_title'),
		"user_department" => post('user_department'),
		"user_company" => post('user_company')
	));

	if (!$updated) {

		header('Location: '.current_url("error=unknown", "successful"));
		die();

	}



	header('Location: '.current_url("successful", "error"));
	die();


} elseif ( post('password-submit') == "Update" ) {


	// Empty fields check
	if ( empty(post('current_password')) || empty(post('new_password')) || empty(post('new_password_confirmation')) ) {

		header('Location: '.current_url("error=fieldsempty", "successful"));
		die();

	}


	// Current password check
	if (!password_verify( post('current_password') , $userInfoDB["user_password"]) ) {

		header('Location: '.current_url("error=password", "successful"));
		die();

	}


	// Password confirmation check
	if ( post('new_password') != post('new_password_confirmation') ) {

		header('Location: '.current_url("error=passconfirm", "successful"));
		die();

	}


	// Update on DB
	$db->where('user_ID', $user_ID);
	$updated = $db->update('users', array(
		'user_password' => password_hash(post('new_password'), PASSWORD_DEFAULT)
	));

	if (!$updated) {

		header('Location: '.current_url("error=unknown", "successful"));
		die();

	}



	header('Location: '.current_url("successful", "error"));
	die();


}



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