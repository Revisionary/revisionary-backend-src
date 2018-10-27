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
		'user_link' => site_url('profile/'.User::ID($user['user_ID'])->userName),
		'user_photo' => User::ID($user['user_ID'])->printPicture(),
		'user_name' => '<span '.(User::ID($user['user_ID'])->userPic != "" ? "class='has-pic'" : "").'>'.(substr(User::ID($user['user_ID'])->firstName, 0, 1).substr(User::ID($user['user_ID'])->lastName, 0, 1)).'</span>',
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
