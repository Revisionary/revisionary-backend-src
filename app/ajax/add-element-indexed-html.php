<?php

$status = "initiated";


// NONCE CHECK
if ( request("nonce") !== $_SESSION["element_index_nonce"] )
	return;


/*
$db->where();
$version = $db->getOne('versions');
*/

// 1. GET THE OLD HTML
// 2. FIND THE LARGEST "<body ..... </body>" AND REPLACE WITH THE NEW ONE: request('bodyHTML');
// 3. SAVE THE FILE



// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	'S_nonce' => $_SESSION['element_index_nonce'],
	'TEST' => Page::ID(187, 44)->pageUri

);

echo json_encode(array(
  'data' => $data
));
