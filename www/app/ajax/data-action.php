<?php

$status = "initiated";



// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["js_nonce"] ) return;



// Global Vars
$type = ucfirst(request('data-type'));
$action = request('action');
$id = intval(request('id'));



// Security Check !!!
if (
	   ( $type != "Category" && $type != "Project" && $type != "Page" && $type != "Device" && $type != "User" )
	|| ( $action != "addNew" && $action != "projectNew" && $action != "pageNew" && $action != "archive" && $action != "delete" && $action != "remove" && $action != "recover" && $action != "rename" && $action != "reorder" )
	|| !is_numeric( $id )
) {
	$status = "fail";
}



// If no problem, DB Update
if ($status != 'fail') {


	// Parameters
	$first_parameter = null;
	if ($type == "User" && $action == "reorder") {
		$first_parameter = $_POST['orderData'];
		$id == currentUserID();
	}
	if ($type == "Category" && $action == "pageNew") $first_parameter = request('project_ID');
	if ($action == "rename") $first_parameter = request('inputText');


	$result = $type::ID($id)->$action($first_parameter);

	if ($result)
		$status = "successful";
	else
		$status = "fail-db";

}



// Redirect if not ajax
if ( request('ajax') != true ) {
	$_SESSION["js_nonce"] = null;

	header('Location: '.$_SERVER['HTTP_REFERER']);
	die();
}



// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'data-type' => $type,
	'action' => $action,
	'id' => $id,
	'parameter' => $first_parameter,
	'nonce' => request('nonce'),
	'S_nonce' => $_SESSION['js_nonce'],

);

echo json_encode(array(
  'data' => $data
));
die();
