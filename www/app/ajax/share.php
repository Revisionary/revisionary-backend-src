<?php

$data = array();
$status = "initiated";
$shareType = false;
$shareTo = "";

$data['status'] = $status;


/*
// NONCE CHECK !!! Disabled for now!
if ( post("nonce") !== $_SESSION["js_nonce"] ) {

	$data['status'] = "security-error";
	$data['posted_nonce'] = post("nonce");
	$data['session_nonce'] = $_SESSION["js_nonce"];


	// CREATE THE ERROR RESPONSE
	echo json_encode(array(
	  'data' => $data
	));

	return;
}
*/


// Valid e-mail?
if (!filter_var(post("email"), FILTER_VALIDATE_EMAIL)) {

	$data['status'] = "invalid-email";
	$data['email'] = post("email");
	$data['posted_nonce'] = post("nonce");
	$data['session_nonce'] = $_SESSION["js_nonce"];


	// CREATE THE ERROR RESPONSE
	echo json_encode(array(
	  'data' => $data
	));

	return;
}


// Valid ID?
if ( !is_numeric(post("object_ID")) ) {

	$data['status'] = "invalid-id";


	// CREATE THE ERROR RESPONSE
	echo json_encode(array(
	  'data' => $data
	));

	return;
}



// DB Check for existing user
$db->where('user_email', post("email"));
$user = $db->getOne('users');

// If found
if ( $user !== null ) {


	// If current user
	if ($user['user_ID'] == currentUserID()) {

		$data['status'] = "invalid-email";


		// CREATE THE ERROR RESPONSE
		die(json_encode(array(
		  'data' => $data
		)));
	}



	// Change the share type
	$shareType = 'user';
	$shareTo = $user['user_ID'];



	$data = array(
		'status' => 'found',
		'user_ID' => $user['user_ID'],
		'user_fullname' => getUserInfo($user['user_ID'])['fullName'],
		'user_nameabbr' => getUserInfo($user['user_ID'])['nameAbbr'],
		'user_link' => site_url('profile/'.getUserInfo($user['user_ID'])['userName']),
		'user_photo' => getUserInfo($user['user_ID'])['printPicture'],
		'user_avatar' => getUserInfo($user['user_ID'])['userPicUrl'],
		'user_name' => '<span '.(getUserInfo($user['user_ID'])['userPic'] != "" ? "class='has-pic'" : "").'>'.(getUserInfo($user['user_ID'])['nameAbbr']).'</span>',
	);


} else { // Not found

	$data = array(
		'status' => 'not-found'
	);


	// Change the share type
	$shareType = 'email';
	$shareTo = post("email");


}



// Check if already shared
$db->where( 'share_to', $shareTo );
$db->where( 'share_type', post('data-type') );
$db->where( 'shared_object_ID', post('object_ID') );
$shares = $db->get('shares');

// Check the project shares if the current type is page
if (post('data-type') == "page") {

	$project_ID = Page::ID( post('object_ID') )->project_ID;


	// Check if the project is already shared
	$db->where( 'share_to', $shareTo );
	$db->where( 'share_type', 'project' );
	$db->where( 'shared_object_ID', $project_ID );
	$project_shares = $db->get('shares');
	$shares = array_merge($shares, $project_shares);

}

if ( count($shares) > 0 ) {

	$data['status'] = "already-exist";


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}



// Add the share to DB
$share_ID = $db->insert('shares', array(

	"share_to" => $shareTo,
	"share_type" => post('data-type'),
	"shared_object_ID" => post("object_ID"),
	"sharer_user_ID" => currentUserID()

));

if ($share_ID) { // If successful

	$data['status'] = $shareType."-added";


	// Notify the user
	if ( post('data-type') == "page" || post('data-type') == "project" ) {


		$object_link = site_url(post('data-type').'/'.post("object_ID"));


		Notify::ID($shareTo)->mail(
			getUserInfo()['fullName']." shared a ".post('data-type')." with you.",

			"Hello, ".getUserInfo()['fullName']."(".getUserInfo()['email'].") shared a ".post('data-type')." with you from Revisionary App. Here is the link to access this ".post('data-type').": <br>

<a href='$object_link' target='_blank'>$object_link</a>"
		);


	}


} else $data['status'] = "not-added";



// CREATE THE RESPONSE
die(json_encode(array(
  'data' => $data
)));