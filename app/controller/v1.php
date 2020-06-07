<?php
//sleep(2);


// Headers
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: https://".$config['env']['dashboard_subdomain'].".".$config['env']['dashboard_domain']);
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header("Access-Control-Max-Age: 3600");



$jwt = $_SERVER['HTTP_AUTHORIZATION'] ?? false;
$parameters = json_decode(file_get_contents("php://input"));
// die(json_encode(array(
// 	"status" => "error",
// 	"token" => $jwt,
// 	"parameters" => $parameters
// )));



// Request Method Check
$method = $_SERVER['REQUEST_METHOD'];
if ($method != "GET" && $method != "PUT" && $method != "POST" && $method != "DELETE") {
	http_response_code(401);
	die(json_encode(array(
		"status" => "error",
		"description" => "Not allowed method"
	)));
}


// API Existance
$api = $_url[1] ?? "no-api";
if ($api == "no-api") {
	http_response_code(401);
	die(json_encode(array(
		"status" => "error",
		"description" => "API not found"
	)));
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
	http_response_code(401);
	die(json_encode(array(
		"status" => "error",
		"description" => "Not allowed API"
	)));
}



// ACTIONS:

// GET all the data
if ($method == "GET" && !isset($_url[2])) {


	// JWT existance
	if (!$jwt) {
		http_response_code(401);
		die(json_encode(array(
			"status" => "error",
			"description" => "Access denied"
		)));
	}


	// USER
	if ($api == "session") {
		$result = User::ID([
			"token" => $jwt
		])->get();

		http_response_code($result['status'] == "success" ? 200 : 401);
		die(json_encode($result));
	}


	$output = array();


	// PROJECTS
	if ($api == "project") {
		$output = User::ID()->getProjects();
	}


	// PAGES
	if ($api == "project") {
		$output = User::ID()->getProjects();
	}


	http_response_code(200);
	die(json_encode($output));
}

// POST
if ($method == "POST" && !isset($_url[2])) {


	// LOGIN SESSION
	if ($api == "session") {

		$result = User::ID([])->login(
			$parameters->username,
			$parameters->password
		);

		http_response_code($result['status'] == "success" ? 200 : 401);
		die(json_encode($result));
	}




	// JWT Existance
	if (!$jwt) {
		http_response_code(401);
		die(json_encode(array(
			"status" => "error",
			"description" => "Access denied"
		)));
	}


	// USERS
	if ($api == "users") {
		$result = User::ID([
			"token" => $jwt
		])->getUsers(
			$parameters->IDs
		);

		http_response_code($result['status'] == "success" ? 200 : 401);
		die(json_encode($result));
	}


	// PROJECTS
	if ($api == "project") {
		die(json_encode(
			User::ID()->getProjects()
		));
	}


	// PAGES
	if ($api == "project") {
		die(json_encode(
			User::ID()->getProjects()
		));
	}
}



// DELETE
if ($method == "DELETE" && !isset($_url[2])) {


	$jwt = $_SERVER['HTTP_AUTHORIZATION'] ?? false;
	if (!$jwt) {
		http_response_code(401);
		die(json_encode(array(
			"status" => "error",
			"description" => "Access denied"
		)));
	}


	// LOGOUT SESSION
	if ($api == "session") {
		$result = User::ID([
			"token" => $jwt
		])->logout();

		http_response_code($result['status'] == "success" ? 200 : 401);
		die(json_encode($result));
	}


}


http_response_code(400);
die(json_encode(array(
	"status" => "error",
	"description" => "Internal Error"
)));



// $class  = filter_input(INPUT_GET, 'class',  FILTER_SANITIZE_STRING);
// $method = filter_input(INPUT_GET, 'method', FILTER_SANITIZE_STRING);
//$log->info($_SERVER['HTTP_REFERER']);