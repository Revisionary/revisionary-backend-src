<?php

// If already logged in, go projects page
if (userloggedIn()) {
	header('Location: '.site_url('projects'));
	die();
}


$eMail = "";
$fullName = "";
$nonceError = $emptyError = $mailError = $mailExistError = $nameError = $dbError = false;
$errors = [];

// If submitted
if ( post('user-submit') == "Register" ) {

	if ( post("nonce") !== $_SESSION["signup_nonce"] ) {
		$nonceError = true;
		$errors[] = "Please try again";
	}


	$eMail = post('email');
	$fullName = post('full_name');
	$password = post('password');


	// Check if any empty field
	if (!$nonceError && (empty($eMail) || empty($fullName) || empty($password)) ) {
		$emptyError = true;
		$errors[] = "Please don't leave fields blank";
	}


	// E-Mail check
	if (!$nonceError && !$emptyError && !filter_var($eMail, FILTER_VALIDATE_EMAIL)) {
		$mailError = true;
		$errors[] = "Invalid email format";
	}


	// Check if e-mail already exists
	if (!$nonceError && !$emptyError && !$mailError) {
		$db->where("user_email", $eMail);
		$user = $db->getOne("users", "user_ID");
		if ($user) {
			$mailExistError = true;
			$errors[] = "This e-mail address is already registered, please login or <a href='#'>reset</a> your password.";
		}
	}


	// Full name check
	if (!$nonceError && !$emptyError && !$mailError && !$mailExistError && !preg_match("/^[\p{Latin}[A-Za-z ]+$/u", $fullName)) {
		$nameError = true;
		$errors[] = "Only letters and white space allowed on your name";
	}


	// If no error
	if( !$nonceError && !$emptyError && !$mailError && !$mailExistError && !$nameError ) {

		// Parse the full name
		$firstName = $fullName;
		$lastName = "";
		$parsedFullName = explode(' ', $fullName);
		if (count($parsedFullName) > 1) {
			$firstName = str_replace(' '.end($parsedFullName), '', $fullName);
			$lastName = end($parsedFullName);
		}

		$data = array(
			'user_name' => permalink($fullName),
			'user_email' => $eMail,
			'user_first_name' => $firstName,
			'user_last_name' => $lastName,
			'user_password' => password_hash($password, PASSWORD_DEFAULT),
			'user_level_ID' => 2 // Free one
		);

		$id = $db->insert('users', $data);
		if ($id) {

			// Create the session
			$_SESSION['user_ID'] = $id;


			// Update the shares
			$db->where('share_to', $eMail);
			$db->update ('shares', array(
				'share_to' => $id
			));


			if (post('redirect_to') != "") {
				header("Location: ".htmlspecialchars_decode(post('redirect_to'))); // !!! Check security
				die();
			}

			header("Location: ".site_url('projects'));
			die();

		}

		// If not inserted
		$dbError = true;
		$errors[] = "Registration failed: ".$db->getLastError();

	}

}

// Generate new nonce for form
$_SESSION["signup_nonce"] = uniqid(mt_rand(), true);

$page_title = "Signup - Revisionary App";
require view('signup');