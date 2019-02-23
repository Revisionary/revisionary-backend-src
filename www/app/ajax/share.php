<?php

// Defaults
$data = array();
$data['status'] = "initiated";
$shareTo = "";


// Data received
$type = post('data-type');
$object_ID = post("object_ID");
$email = post("email");
$add_type = post("add-type");



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



// Valid type?
if ($type != "project" && $type != "page") {

	$data['status'] = "invalid-process";


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}



// Valid e-mail?
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

	$data['status'] = "invalid-email";
	$data['email'] = $email;


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}


// Valid ID?
if ( !is_numeric($object_ID) ) {

	$data['status'] = "invalid-id";


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}


// Is this object exist?
$object = $type::ID($object_ID);
if (!$object) {

	$data['status'] = "invalid-obj";


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}
$objectInfo = $object->getInfo();
$iamowner = $objectInfo['user_ID'] == currentUserID();

// Do I access this object?
if ( !$iamowner ) {

	// Is this object shared to me? !!!
	// ...

}



// DB Check for existing user
$db->where('user_email', $email);
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
	$shareTo = $email;


}



// If different type selected
if ( $type == "page" && $add_type == "project" ) {


	// Find the project ID
	$object_ID = $objectInfo['project_ID'];
	$type = "project";

}



// Check if already shared
$db->where( 'share_to', $shareTo );
$db->where( 'share_type', $type );
$db->where( 'shared_object_ID', $object_ID );
$shares = $db->get('shares');

// Check the project shares if the current type is page
if ($type == "page") {

	$project_ID = Page::ID( $object_ID )->getInfo('project_ID');


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
	"share_type" => $type,
	"shared_object_ID" => $object_ID,
	"sharer_user_ID" => currentUserID()

));

if ($share_ID) { // If successful

	$data['status'] = "added";


	// Site log
	$log->info(ucfirst($type)." #$object_ID shared to: User #$shareTo | Sharer User #".currentUserID());


	// Notify the user
	if ( $type == "page" || $type == "project" ) {


		$object_link = site_url($type.'/'.$object_ID);
		$objectData = ucfirst($type)::ID($object_ID);
		$objectName = $objectData->getInfo($type."_name");

		$projectName = "";
		if ($type == "page") {
			$projectName = " (in ".Project::ID( $objectData->getInfo("project_ID") )->getInfo('project_name').")";
		}


		// Web notification
		if ( is_integer($shareTo) )
			Notify::ID($shareTo)->web("shared the \"$objectName".$projectName."\" $type with you.", $type, $object_ID);


		// Email notification
		Notify::ID($shareTo)->mail(
			getUserInfo()['fullName']." shared the \"$objectName".$projectName."\" $type with you.",

			"Hello, ".getUserInfo()['fullName']."(".getUserInfo()['userName'].") shared the \"$objectName".$projectName."\" $type with you from Revisionary App. Here is the link to access this $type: <br>

<a href='$object_link' target='_blank'>$object_link</a>"
		);


	}


} else $data['status'] = "not-added";



// CREATE THE RESPONSE
die(json_encode(array(
	'info' => array(
		'type' => $type,
		'object_ID' => $object_ID,
		'email' => $email,
		'add_type' => $add_type
	),
	'data' => $data
)));