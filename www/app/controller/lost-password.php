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

$email = "";
$errors = [];

// If submitted
if ( post('lost-password-submit') == "Login" ) {


/*
	// Check the nonce
	if ( post("nonce") !== $_SESSION["login_nonce"] )
		$errors[] = "Please try again";
*/


/*
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
			$errors[] = "Your username or password is wrong.";
		}

		// Password check
		elseif ($user && !password_verify($password, $user["user_password"])) {
			$errors[] = "Your username or password is wrong";
		}

		// If no error above
		else {

			$_SESSION['user_ID'] = $user["user_ID"];


			// Notify the admin
			if (getUserInfo()['email'] != "bilaltas@me.com" && getUserInfo()['email'] != "bill@twelve12.com") {

				Notify::ID(1)->mail(
					"New login by ".$user['user_first_name']." ".$user['user_last_name'],
					"
					<b>User Information (Typed: $userName)</b> <br>
					E-Mail: ".$user['user_email']." <br>
					Full Name: ".$user['user_first_name']." ".$user['user_last_name']." <br>
					Username: ".$user['user_name']."
					"
				);

			}


			// Site Log
			$log->info("User #".currentUserID()." Logged In: ".getUserInfo()['userName']."(".getUserInfo()['fullName'].") | Typed: $userName | Email: ".getUserInfo()['email']." | User Level ID #".getUserInfo()['userLevelID']."");


			if (post('redirect_to') != "") {
				header("Location: ".htmlspecialchars_decode(post('redirect_to'))); // !!! Check security
				die();
			}

			header("Location: ".site_url('projects'));
			die();

		}

	}
*/

}

// Generate new nonce for form
$_SESSION["login_nonce"] = uniqid(mt_rand(), true);



$page_title = "Lost Password - Revisionary App";
require view('lost-password');