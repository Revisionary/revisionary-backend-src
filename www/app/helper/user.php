<?php

function userloggedIn() {
	return isset($_SESSION['user_ID']) ? true : false;
}

function currentUserID() {
	return userloggedIn() ? $_SESSION['user_ID'] : "guest";
}

function getUserData(int $user_ID = 0) {
	global $Users;

	// Get the User ID
	$user_ID = !$user_ID ? currentUserID() : $user_ID;


	// If not exist in the global, pull data from DB
	if ( !isset($Users[$user_ID]) ) {
		$Users[$user_ID] = User::ID($user_ID)->getData();
	}


	return $Users[$user_ID];
}