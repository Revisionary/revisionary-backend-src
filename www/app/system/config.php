<?php

$config = array();



// Environmental settings
$config['env'] = [
	'name' 		=> "local-dev",
	'domain'	=> "revisionaryapp.com",
	'subdomain' => "dev",
	'insecure_subdomain' => "insecure",
	'db_host' 	=> "db",
	'db_name' 	=> "revisionaryapp",
	'db_user' 	=> "user",
	'db_pass' 	=> "test"
];

// Check for the remote configuration
if (file_exists(realpath('.') . '/app/system/config-remote.php'))
	require 'config-remote.php';



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
define('domain' , $config['env']['domain']);
define('subdomain' , $config['env']['subdomain']);
define('insecure_subdomain' , $config['env']['insecure_subdomain']);
define('dir', realpath('.'));
define('backdir', realpath('..'));
define('logdir', backdir."/logs");
define('bindir', backdir."/bin");
define('sessiondir', backdir."/sessions");
define('session_lifetime', 99999);
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