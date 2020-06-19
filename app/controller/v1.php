<?php
//sleep(2);


// Headers
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: https://".$config['env']['dashboard_subdomain'].".".$config['env']['dashboard_domain']);
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header("Access-Control-Max-Age: 3600");



$jwt = getBearerToken() ?? false;
$parameters = json_decode(file_get_contents("php://input"));
// die(json_encode(array(
// 	"status" => "error",
// 	"token" => $jwt,
// 	"parameters" => $parameters,
// 	"AUTH" => $_SERVER['HTTP_AUTHORIZATION'],
// 	"Request" => $_REQUEST
// )));



// Request Method Check
$method = $_SERVER['REQUEST_METHOD'];
if ($method != "GET" && $method != "PUT" && $method != "POST" && $method != "DELETE") {
	respondJSON(array(
		"status" => "error",
		"description" => "Not allowed method"
	), 401);
}


// API Existance
$api = $_url[1] ?? "no-api";
if ($api == "no-api") {
	respondJSON(array(
		"status" => "error",
		"description" => "API not found"
	), 401);
}


// API Validation
if (
	$api != "session"
	&& $api != "user"
	&& $api != "users"
	&& $api != "authenticate"
	&& $api != "projectcategories"
	&& $api != "projects"
	&& $api != "project"
	&& $api != "pagecategories"
	&& $api != "pages"
	&& $api != "page"
	&& $api != "phase"
	&& $api != "device"
	&& $api != "user"
	&& $api != "pin"
) {
	respondJSON(array(
		"status" => "error",
		"description" => "Not allowed API"
	), 401);
}



// Parameter check if exists
$parameter1 = null;
if ( isset($_url[2]) ) {
	if (!is_numeric($_url[2])) {
		respondJSON(array(
			"status" => "error",
			"description" => "Wrong parameter"
		), 401);
	}

	$parameter1 = $_url[2];
}




// ACTIONS:

// GET all the data
if ($method == "GET") {


	// JWT existance
	if (!$jwt) {
		respondJSON(array(
			"status" => "error",
			"description" => "Access denied"
		), 401);
	}


	// USER
	if ($api == "session") {
		$result = User::ID([
			"token" => $jwt
		])->get();

		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


	// PROJECT CATEGORIES
	if ($api == "projectcategories") {
		$result = User::ID([
			"token" => $jwt
		])->getProjectCategories_v2();

		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


	// PROJECTS
	if ($api == "projects") {
		$result = User::ID([
			"token" => $jwt
		])->getProjects_v2();

		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


	// PROJECT
	if ($api == "project") {
		$result = User::ID([
			"token" => $jwt
		])->getProject($parameter1);

		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


}



// POST
if ($method == "POST" && !isset($_url[2])) {


	// LOGIN SESSION
	if ($api == "session") {

		$result = User::ID([])->login(
			$parameters->username,
			$parameters->password
		);

		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}




	// JWT Existance
	if (!$jwt) {
		respondJSON(array(
			"status" => "error",
			"description" => "Access denied"
		), 401);
	}


	// USERS
	if ($api == "users") {
		$result = User::ID([
			"token" => $jwt
		])->getUsers(
			$parameters->IDs
		);

		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}

}



// DELETE
if ($method == "DELETE" && !isset($_url[2])) {


	if (!$jwt) {
		respondJSON(array(
			"status" => "error",
			"description" => "Access denied"
		), 401);
	}


	// LOGOUT SESSION
	if ($api == "session") {
		$result = User::ID([
			"token" => $jwt
		])->logout();

		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


}



respondJSON(array(
	"status" => "error",
	"description" => "Internal Error"
), 400);



// $class  = filter_input(INPUT_GET, 'class',  FILTER_SANITIZE_STRING);
// $method = filter_input(INPUT_GET, 'method', FILTER_SANITIZE_STRING);
//$log->info($_SERVER['HTTP_REFERER']);