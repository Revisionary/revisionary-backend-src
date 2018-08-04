<?php

$config = array();



// Environmental settings
$config['env'] = [
	'name' 		=> "local-dev",
	'subdomain' => "dev",
	'insecure_subdomain' => "dev",
	'db_host' 	=> "localhost",
	'db_name' 	=> "revisionaryapp",
	'db_user' 	=> "root",
	'db_pass' 	=> "root"
];

// Check for the remote configuration
if (file_exists(realpath('.') . '/app/system/config-remote.php'))
	require 'config-remote.php';



/*
$config['env'] = [
	'name' 		=> "remote-dev",
	'subdomain' => "new",
	'insecure_subdomain' => "new",
	'db_host' 	=> "127.0.0.1",
	'db_name' 	=> "",
	'db_user' 	=> "",
	'db_pass' 	=> ""
];
*/
/*
$config['env'] = [
	'name' 		=> "production",
	'subdomain' => "www",
	'insecure_subdomain' => "www",
	'db_host' 	=> "127.0.0.1",
	'db_name' 	=> "",
	'db_user' 	=> "",
	'db_pass' 	=> ""
];
*/



// SSL Check
if ($config['env']['name'] == 'local-dev') {
	$_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? true : false;
} elseif ($config['env']['name'] == 'remote-dev') {
	$_https = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https" ? true : false;
}



// Database
$config['db'] = [
  'host' => $config['env']['db_host'],
  'name' => $config['env']['db_name'],
  'user' => $config['env']['db_user'],
  'pass' => $config['env']['db_pass']
];



// Default Language
$config['default_language'] = 'en';



// Definitions
define('domain' , 'revisionaryapp.com');
define('subdomain' , $config['env']['subdomain']);
define('insecure_subdomain' , $config['env']['insecure_subdomain']);
define('dir', realpath('.'));
define('backdir', realpath('..'));
define('logdir', backdir."/logs");
define('bindir', backdir."/bin");
define('sessiondir', backdir."/sessions");
define('session_lifetime', 9999);
define('session_name', 'revisionary_session');
define('model' , dir . '/app/model');
define('view' , dir . '/app/view');
define('controller', dir . '/app/controller');
define('cache' , dir . '/assets/cache');
define('port' , (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : "") );
define('ssl' , $_https);
define('secure_url', "https://".subdomain."." . domain);
define('insecure_url', "http://".insecure_subdomain."." . domain);
define('url', ssl ? secure_url : insecure_url);

// TEMP - Image Names !!!
define('project_image_name', 'project.jpg');
define('page_image_name', 'page.jpg');