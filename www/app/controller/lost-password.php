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
if ( post('lost-password-submit') == "Send Reset Link" ) {


/*
	// Check the nonce
	if ( post("nonce") !== $_SESSION["login_nonce"] )
		$errors[] = "Please try again";
*/


	$userName = post('email');


	// Check if any empty field
	if ( empty($userName) )
		$errors[] = "Please don't leave the field blank";


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
			$errors[] = "We couldn't find your account here. Would you like to <a href='".site_url("signup")."'><b>signup</b></a>?";
		}

		// If no error above
		else {


			// User info
			$userInfo = $user;
			$user_ID = $userInfo["user_ID"];


			// Create tokens
			$selector = bin2hex(random_bytes(8));
			$token = random_bytes(32);


			// Prepare the URL
			$url = site_url('reset-password?email='.$user['user_email'].'&token='.bin2hex($token));


			// Token expiration
			$expires = new DateTime('NOW');
			$expires->add(new DateInterval('PT01H')); // 1 hour


			// Delete any existing tokens for this user
			$db->where('user_ID', $user_ID);
			$db->delete('password_reset');


			// Insert reset token into database
			$insert = $db->insert('password_reset', array(
		        'pass_reset_token'	 => hash('sha256', $token),
		        'pass_reset_expires' => $expires->format('U'),
		        'user_ID'			 => $user_ID,
		        'pass_reset_IP'		 => get_client_ip()
			));



			// If successful
			if ($insert) {


				// Send email
				Notify::ID($user_ID)->mail(
					"Your password reset request",
					"Hi ".getUserInfo($user_ID)['fullName'].", <br><br>

					Here is the link that you can reset your password: <br>
					<a href='$url' target='_blank'>$url</a>
					"
				);


				// Site Log
				$log->info("User #$user_ID Lost Password: ".$userInfo["user_name"]."(".getUserInfo($user_ID)['fullName'].") | Typed: $userName | Email: ".$userInfo["user_email"]." | User Level ID #".$userInfo["user_level_ID"]."");


				// Redirect to message
				header("Location: ".site_url('lost-password?sent'));
				die();

			}


			// If not successful
			header("Location: ".site_url('lost-password?error'));
			die();

		}

	}

}


$page_title = "Lost Password - Revisionary App";
require view('lost-password');