<?php

$data = array();
$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["js_nonce"] ) return;


// Valid e-mail?
if (!filter_var(post("email"), FILTER_VALIDATE_EMAIL)) {

	$data['data']['status'] = "invalid-email";


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

		$data['data']['status'] = "invalid-email";


		// CREATE THE ERROR RESPONSE
		echo json_encode(array(
		  'data' => $data
		));

		return;
	}





	$data['data'] = array(
		'status' => 'found',
		'user_ID' => $user['user_ID'],
		'user_link' => site_url('profile/'.getUserInfo($user['user_ID'])['userName']),
		'user_photo' => getUserInfo($user['user_ID'])['printPicture'],
		'user_name' => '<span '.(getUserInfo($user['user_ID'])['userPic'] != "" ? "class='has-pic'" : "").'>'.(getUserInfo($user['user_ID'])['nameAbbr']).'</span>',
	);

} else { // Not found

	$data['data'] = array(
		'status' => 'not-found'
	);

}



// CREATE THE RESPONSE
echo json_encode(array(
  'data' => $data
));
