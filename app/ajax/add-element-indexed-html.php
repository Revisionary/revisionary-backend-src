<?php

$status = "initiated";


// NONCE CHECK
if ( request("nonce") !== $_SESSION["element_index_nonce"] )
	return;


// DO THE SECURITY CHECKS!



// Get the page ID
$page_ID = request('page_ID');
$version_number = request('version_number');
$page_file = Page::ID($page_ID, $version_number)->pageFile;



// 1. GET THE OLD HTML
$page_HTML = file_get_contents($page_file);

// 2. FIND THE LARGEST "<body ..... </body>" AND REPLACE WITH THE NEW ONE: request('bodyHTML');
$new_HTML = preg_replace('/<body.*\/body>/s', $_POST['bodyHTML'], $page_HTML);

// 3. SAVE THE FILE
$saved = file_put_contents( $page_file, $new_HTML, FILE_TEXT);



// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	'S_nonce' => $_SESSION['element_index_nonce'],
	'TEST' => Page::ID($page_ID, $version_number)->pageFile,
	'HTML' => $page_HTML,
	'saved' => $saved

);

echo json_encode(array(
  'data' => $data
));
