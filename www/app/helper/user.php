<?php

function userloggedIn() {
	return isset($_SESSION['user_ID']) ? true : false;
}

function currentUserID() {
	return userloggedIn() ? $_SESSION['user_ID'] : 0;
}

function getUserInfo($user_ID = 0) {
	global $Users;

	// Get the User ID
	$user_ID = !$user_ID ? currentUserID() : $user_ID;


	// If not exist in the global, pull data from DB
	if ( !isset($Users[$user_ID]) ) {
		$Users[$user_ID] = User::ID($user_ID)->getData();
	}


	return $Users[$user_ID];
}

function checkAvailableEmail($user_email) {
	global $db;

	$db->where("user_email", $user_email);
	$user = $db->getOne("users", "user_ID");

	return $user ? false : true;
}

function checkAvailableUserName($user_name) {
	global $db;

	$db->where("user_name", $user_name);
	$user = $db->getOne("users", "user_ID");

	return $user ? false : true;
}



/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar | blank ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source https://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'blank', $r = 'g', $img = false, $atts = array() ) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}