<?php

$status = "initiated";



// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
$object_ID = request('id');
$dataType = request('dataType');


// Is the object ID number?
if ( !is_numeric($object_ID) )
	return;


// Data type options check
if ( $dataType != "project" && $dataType != "page" )
	return;



// DO THE SECURITY CHECKS !!!



$shared_users = array();



// 1. Find the owner
$objectData = $dataType::ID($object_ID);
$owner_ID = $objectData->user_ID;
$ownerInfo = getUserData($owner_ID);


if ($ownerInfo) {

	$status = "Owner added";

	// Add the owner
	$shared_users[] = array(
		'mStatus' => "owner",
		'email' => $ownerInfo['email'],
		'fullName' => $ownerInfo['fullName'],
		'nameAbbr' => $ownerInfo['nameAbbr'],
		'userImageUrl' => $ownerInfo['userPicUrl'],
		'user_ID' => $owner_ID,
		'sharer_user_ID' => 0
	);

}


// 2. Find the project shares (if only the type is page)
if ( $dataType == "page" ) {

	$project_ID = $objectData->project_ID;


	$db->where('share_type', 'project');
	$db->where('shared_object_ID', $project_ID);
	$object_shares = $db->get('shares', null, "share_to, sharer_user_ID");

	if ($object_shares) {

		$status = "Project shares added";

		// Add the shared users
		foreach ($object_shares as $sharedUser) {

			$shredUserInfo = getUserData($sharedUser['share_to']);
			$shared_users[] = array(
				'mStatus' => "project",
				'email' => $shredUserInfo['email'],
				'fullName' => $shredUserInfo['fullName'],
				'nameAbbr' => $shredUserInfo['nameAbbr'],
				'userImageUrl' => $shredUserInfo['userPicUrl'],
				'user_ID' => $sharedUser['share_to'],
				'sharer_user_ID' => $sharedUser['sharer_user_ID']
			);

		}

	}

}


// 3. Find the object shares
$db->where('share_type', $dataType);
$db->where('shared_object_ID', $object_ID);
$object_shares = $db->get('shares', null, "share_to, sharer_user_ID");

if ($object_shares) {

	$status = "Object shares added";

	// Add the shared users
	foreach ($object_shares as $sharedUser) {

		$shredUserInfo = getUserData($sharedUser['share_to']);
		$shared_users[] = array(
			'mStatus' => "shared",
			'email' => $shredUserInfo['email'],
			'fullName' => $shredUserInfo['fullName'],
			'nameAbbr' => $shredUserInfo['nameAbbr'],
			'userImageUrl' => $shredUserInfo['userPicUrl'],
			'user_ID' => $sharedUser['share_to'],
			'sharer_user_ID' => $sharedUser['sharer_user_ID']
		);

	}

}



$status = count($shared_users) > 0 ? "Users received" : "No Users Found";


// CREATE THE RESPONSE
$info = array(

	'status' => $status,
	'nonce' => request('nonce'),
	'S_nonce' => $_SESSION['pin_nonce'],
	'object_ID' => $object_ID

);

echo json_encode(array(
  'info' => $info,
  'users' => $shared_users
));
