<?php

// If already logged in, go projects page
if (userloggedIn()) {

	if (get('redirect') != "") {
		header("Location: ".htmlspecialchars_decode(get('redirect'))); // !!! Check security
		die();
	}

	header('Location: '.site_url('projects'));
	die();

}

$userName = "";
$errors = [];

// If submitted
if ( post('lost-password-submit') == "Login" ) {


/*
	// Check the nonce
	if ( post("nonce") !== $_SESSION["login_nonce"] )
		$errors[] = "Please try again";
*/


	$userName = post('username');
	$password = post('password');


	// Check if any empty field
	if ( empty($userName) || empty($password) )
		$errors[] = "Please don't leave fields blank";


	// Username / E-Mail validation
	elseif (!filter_var($userName, FILTER_VALIDATE_EMAIL) ) {

		if (!preg_match('/^[A-Za-z][A-Za-z0-9]*(?:-[A-Za-z0-9\-]+)*$/', $userName))
			$errors[] = "Invalid username or email format";

	}

	// If no error above
	if ($errors == []) {

		// Username check
		$db->where("user_name", $userName);
		$db->orWhere("user_email", $userName);
		$user = $db->getOne("users");

		if ($user === null) {
			$errors[] = "We couldn't find your account here. Would you like to <a href='".site_url("signup")."'>signup</a>?.";
		}

		// If no error above
		else {

			$userInfo = $user;
			$user_ID = $userInfo["user_ID"];


			// Send email !!!
			Notify::ID($user_ID)->mail(
				"Your password reset request",
				"Hi ".getUserInfo($user_ID)['fullName'].", <br><br>

				Here is the link that you can reset your password: <br>
				...
				"
			);


			// Site Log
			$log->info("User #$user_ID Lost Password: ".$userInfo["user_name"]."(".getUserInfo($user_ID)['fullName'].") | Typed: $userName | Email: ".$userInfo["user_email"]." | User Level ID #".$userInfo["user_level_ID"]."");


			header("Location: ".site_url('lost-password?sent')); // !!!
			die();

		}

	}

}


$page_title = "Lost Password - Revisionary App";
require view('lost-password');