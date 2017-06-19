<?php

// If already logged in, go projects page
if (userloggedIn()) {
	header('Location: '.site_url('projects'));
	die();
}


$eMail = "";
$fullName = "";
$emptyError = $mailError = $mailExistError = $nameError = $dbError = false;
$errors = [];

// If submitted
if ( isset($_POST['user-submit']) ) {

	$eMail = trim(stripslashes($_POST['email']));
	$fullName = trim(stripslashes($_POST['full_name']));
	$password = trim(stripslashes($_POST['password']));


	// Check if any empty field
	if (empty($eMail) || empty($fullName) || empty($password)) {
		$emptyError = true;
		$errors[] = "Please don't leave fields blank";
	}


	// E-Mail check
	if (!$emptyError && !filter_var($eMail, FILTER_VALIDATE_EMAIL)) {
		$mailError = true;
		$errors[] = "Invalid email format";
	}


	// Check if e-mail already exists
	if (!$emptyError && !$mailError) {
		$db->where("user_email", $eMail);
		$user = $db->getOne("users", "user_ID");
		if ($user) {
			$mailExistError = true;
			$errors[] = "This e-mail address is already registered, please login or reset your password.";
		}
	}


	// Full name check
	if (!$emptyError && !$mailError && !$mailExistError && !preg_match("/^[\p{Latin}[A-Za-z ]+$/u", $fullName)) {
		$nameError = true;
		$errors[] = "Only letters and white space allowed on your name";
	}


	// If no error
	if( !$emptyError && !$mailError && !$mailExistError && !$nameError ) {

		// Parse the full name
		$firstName = $fullName;
		$lastName = "";
		$parsedFullName = explode(' ', $fullName);
		if (count($parsedFullName) > 1) {
			$firstName = str_replace(' '.end($parsedFullName), '', $fullName);
			$lastName = end($parsedFullName);
		}

		$data = Array (
			'user_name' => permalink($fullName),
			'user_email' => $eMail,
			'user_first_name' => $firstName,
			'user_last_name' => $lastName,
			'user_password' => password_hash($password, PASSWORD_DEFAULT),
			'user_level_ID' => 2 // Free one
		);

		$id = $db->insert ('users', $data);
		if ($id) {

			// Create the session
			$_SESSION['user_ID'] = $id;

			// Redirect to the projects page
			header("Location: ".site_url('projects'));
			die();

		}

		// If not inserted
		$dbError = true;
		$errors[] = "Registration failed: ".$db->getLastError();

	}

}

$page_title = "Signup - Revisionary App";
require view('signup');