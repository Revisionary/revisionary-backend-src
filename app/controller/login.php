<?php

// If already logged in, go projects page
if (userloggedIn()) {
	header('Location: '.site_url('projects'));
	die();
}

$userName = "";
$errors = [];

// If submitted
if ( isset($_POST['login-submit']) ) {

	$userName = stripslashes($_POST['username']);
	$password = stripslashes($_POST['password']);


	// Check if any empty field
	if (empty($userName) || empty($password)) {
		$errors[] = "Please don't leave fields blank";
	}


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
		$user = $db->getOne("users", "user_ID, user_password");
		if (!$user) {
			$errors[] = "Your username or password is wrong.";
		}

		// Password check
		elseif ($user && !password_verify($password, $user["user_password"])) {
			$errors[] = "Your username or password is wrong";
		}

		// If no error above
		else {

			$_SESSION['user_ID'] = $user["user_ID"];

			header("Location: ".site_url('projects'));
			die();

		}

	}

}

$page_title = "Login - Revisionary App";
require view('login');