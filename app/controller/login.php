<?php

// If already logged in, go projects page
if (userloggedIn()) {
	header('Location: '.site_url('projects'));
	die();
}

// If submitted
if ( isset($_POST['login-submit']) ) {

	$userName = stripslashes($_POST['username']);
	$password = stripslashes($_POST['password']);

/*
	$stmt = $this->dbQuery("SELECT * FROM users WHERE user_name='".$userName."' OR user_email='".$userName."' LIMIT 1");
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
*/


	//if($stmt->rowCount() > 0 && password_verify($password, $row['user_password'])) {
	if(true) {

		//$_SESSION['user_ID'] = $row['user_ID'];
		$_SESSION['user_ID'] = 0;

		header("Location: ".site_url('projects'));
		die();

	} else {

		$error = "Wrong password";

	}

}

$page_title = "Login - Revisionary App";
require view('login');