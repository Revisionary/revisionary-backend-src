<?php

function userLoggedIn() {
	return isset($_SESSION['user_ID']) ? true : false;
}

function currentUserID() {
	return userLoggedIn() ? $_SESSION['user_ID'] : 0;
}

function getUserInfoDB(int $user_ID = null, bool $nocache = false) {
    global $db, $cache;


	$user_ID = $user_ID != null ? $user_ID : currentUserID();


	// Check the cache first
	$cached_user_info = $cache->get('user:'.$user_ID);
	if ( $cached_user_info !== false && !$nocache ) {


		return $cached_user_info;


	} else { // If not exist in the cache, pull data from DB


		// Bring the user level info
		$db->join("user_levels l", "l.user_level_ID = u.user_level_ID", "LEFT");
	    $db->where("u.user_ID", $user_ID);
		$userInfo = $db->getOne("users u");


		// Set the cache
		if ($userInfo) {
			$cache->set('user:'.$user_ID, $userInfo);
			return $userInfo;
		}


	}


	return false;


}

function getUserInfo($user_ID = 0) {


	// Get the User ID
	$user_ID = !$user_ID ? currentUserID() : $user_ID;


	// If email user
	if ( filter_var($user_ID, FILTER_VALIDATE_EMAIL) ) return
		array(
			'userName' => "",
			'firstName' => "",
			'lastName' => "",
			'fullName' => $user_ID,
			'nameAbbr' => '<i class="fa fa-envelope"></i>',
			'email' => 'Not confirmed yet',
			'userPic' => "",
			'userPicUrl' => null,
			'printPicture' => "",
			'emailNotifications' => "",
			'userLevelName' => "",
			'userLevelID' => "",
			'userLevelMaxProject' => "",
			'userLevelMaxPage' => ""
		);


	// If not numeric
	if ( !is_int($user_ID) ) return false;


	// Get user information
	$userInfo = User::ID($user_ID)->getInfo();
	if ( !$userInfo ) return false;


	// The extended user data
	$extendedUserInfo = array(
		'userName' => $userInfo['user_name'],
		'firstName' => $userInfo['user_first_name'],
		'lastName' => $userInfo['user_last_name'],
		'fullName' => $userInfo['user_first_name']." ".$userInfo['user_last_name'],
		'nameAbbr' => mb_substr($userInfo['user_first_name'], 0, 1).mb_substr($userInfo['user_last_name'], 0, 1),
		'email' => $userInfo['user_email'],
		'userPic' => $userInfo['user_picture'],
		'userPicUrl' => $userInfo['user_picture'] != "" ? cache_url("users/user-$user_ID/".$userInfo['user_picture']) : get_gravatar($userInfo['user_email'], 250),
		'emailNotifications' => $userInfo['user_email_notifications'],
		'userLevelName' => $userInfo['user_level_name'],
		'userLevelID' => $userInfo['user_level_ID'],
		'userLevelMaxProject' => $userInfo['user_level_max_project'],
		'userLevelMaxPage' => $userInfo['user_level_max_page'],
	);
	$extendedUserInfo['printPicture'] = 'style="background-image: url('.$extendedUserInfo['userPicUrl'].');"';


	return $extendedUserInfo;
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